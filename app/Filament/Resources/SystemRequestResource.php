<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SystemRequestResource\Pages;
use App\Mail\UserLoginDetails;
use App\Models\Message;
use App\Models\Role;
use App\Models\User;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\ComponentContainer;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Filament\Tables\Contracts\HasTable;

class SystemRequestResource extends Resource
{
    protected static ?string $model = Message::class;

    protected static ?string $navigationIcon = 'heroicon-o-inbox-stack';
    protected static ?string $navigationLabel = 'Demandes système';

    public static function shouldRegisterNavigation(): bool
    {
        $user = Auth::user();

        return $user
            && method_exists($user, 'hasAnyRole')
            && $user->hasAnyRole(['admin', 'super_admin']);
    }

    public static function canViewAny(): bool
    {
        $user = Auth::user();

        return $user
            && method_exists($user, 'hasAnyRole')
            && $user->hasAnyRole(['admin', 'super_admin']);
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Gestion Conseillers';
    }

    public static function getNavigationBadge(): ?string
    {
        $count = Message::query()
            ->system()
            ->where('status', 'pending')
            ->count();

        return $count ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->system()
            ->latest();
    }

    public static function form(Form $form): Form
    {
        // Pas de création/édition manuelle
        return $form->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Reçu le')
                    ->dateTime('d M Y à H:i')
                    ->sortable(),

                Tables\Columns\TextColumn::make('subject')
                    ->label('Sujet')
                    ->limit(70)
                    ->searchable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('status')
                    ->label('Statut')
                    ->badge()
                    ->sortable()
                    ->formatStateUsing(fn(?string $state) => $state ?? 'pending')
                    ->color(fn(?string $state) => match ($state ?? 'pending') {
                        'pending'  => 'warning',
                        'approved' => 'success',
                        'rejected' => 'danger',
                        default    => 'gray',
                    }),

                Tables\Columns\TextColumn::make('handled_at')
                    ->label('Traité le')
                    ->dateTime('d M Y à H:i')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('handledBy.first_name')
                    ->label('Traité par')
                    ->formatStateUsing(fn($record) => $record->handledBy
                        ? trim(($record->handledBy->first_name ?? '') . ' ' . ($record->handledBy->last_name ?? ''))
                        : null)
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Statut')
                    ->options([
                        'pending'  => 'En attente',
                        'approved' => 'Approuvée',
                        'rejected' => 'Refusée',
                    ]),
            ])
            ->actions([
                Tables\Actions\Action::make('read')
                    ->label('Ouvrir')
                    ->icon('heroicon-o-eye')
                    ->modalHeading('Demande')
                    ->modalSubmitAction(false)
                    ->modalCancelAction(fn() => \Filament\Actions\StaticAction::make('close')->label('Fermer'))
                    ->form([
                        Forms\Components\TextInput::make('subject')->label('Sujet')->disabled(),
                        Forms\Components\RichEditor::make('body')->label('Détails')->disabled(),
                    ])
                    ->mountUsing(function (Message $record, ComponentContainer $form) {
                        $form->fill([
                            'subject' => $record->subject,
                            'body'    => $record->body,
                        ]);
                    })
                    ->modalFooterActions(function (Message $record, $action): array {
                        return [
                            /**
                             * =========================
                             * WORKFLOW INSCRIPTION
                             * =========================
                             */
                            Tables\Actions\Action::make('accept_registration')
                                ->label('Valider le candidat')
                                ->color('success')
                                ->icon('heroicon-o-check')
                                ->visible(fn() => self::isRegistrationRequest($record) && self::isPending($record))
                                ->requiresConfirmation()
                                ->modalHeading('Confirmer la validation')
                                ->action(function () use ($record, $action) {
                                    abort_unless(auth()->user()?->hasAnyRole(['admin', 'super_admin']), 403);
                                    $record->refresh();

                                    if (! self::ensurePendingOrWarn($record, $action)) {
                                        return;
                                    }

                                    $applicantId = $record->data['applicant_id'] ?? null;
                                    $applicant = $applicantId ? User::find($applicantId) : null;

                                    if (! $applicant) {
                                        Notification::make()->danger()->title('Utilisateur introuvable.')->send();
                                        $action->cancel();
                                        return;
                                    }

                                    try {
                                        DB::transaction(function () use ($record, $applicant) {
                                            // rôle conseiller (fallback id=3 si pas trouvé)
                                            $roleConseiller = Role::where('name', 'conseiller')->first();
                                            $targetRoleId = $roleConseiller ? $roleConseiller->id : 3;

                                            // 1) rendre visible sur "Notre Équipe" (position > 0)
                                            $maxPosition = User::where('position', '>', 0)->max('position') ?? 0;
                                            $newPosition = $maxPosition + 1;

                                            // 2) générer un mot de passe temporaire
                                            $tempPassword = Str::random(12);

                                            // 3) update user (rôle + visibilité + password)
                                            $applicant->update([
                                                'role_id'  => $targetRoleId,
                                                'position' => $newPosition,
                                                'password' => Hash::make($tempPassword),
                                            ]);

                                            // si vous utilisez spatie/permission:
                                            $applicant->syncRoles(['conseiller']);

                                            // 4) marquer la demande traitée
                                            self::markHandled($record, 'approved');

                                            // 5) envoyer courriel (hors DB ok aussi, mais on garde ici avec try/catch global)
                                            Mail::to($applicant->email)->send(new UserLoginDetails($applicant, $tempPassword));
                                        });

                                        Notification::make()
                                            ->success()
                                            ->title('Candidat validé ! Un courriel de connexion a été envoyé.')
                                            ->send();
                                    } catch (\Throwable $e) {
                                        Notification::make()
                                            ->danger()
                                            ->title('Erreur lors de la validation.')
                                            ->body('Détails: ' . $e->getMessage())
                                            ->send();
                                    }

                                    $action->cancel();
                                }),

                            Tables\Actions\DeleteAction::make()
                                ->label('Supprimer')
                                ->icon('heroicon-o-trash')
                                ->color('danger')
                                ->requiresConfirmation()
                                ->visible(fn() => auth()->user()?->hasAnyRole(['super_admin'])) // ou admin aussi si tu veux
                                ->successNotification(
                                    Notification::make()->success()->title('Demande supprimée.')
                                ),

                            Tables\Actions\Action::make('reject_registration')
                                ->label('Refuser')
                                ->color('danger')
                                ->icon('heroicon-o-x-mark')
                                ->visible(fn() => self::isRegistrationRequest($record) && self::isPending($record))
                                ->requiresConfirmation()
                                ->modalHeading('Confirmer le refus')
                                ->action(function () use ($record, $action) {
                                    abort_unless(auth()->user()?->hasAnyRole(['admin', 'super_admin']), 403);
                                    $record->refresh();

                                    if (! self::ensurePendingOrWarn($record, $action)) {
                                        return;
                                    }

                                    $applicantId = $record->data['applicant_id'] ?? null;

                                    try {
                                        DB::transaction(function () use ($record, $applicantId) {
                                            if ($applicantId) {
                                                $applicant = User::find($applicantId);
                                                if ($applicant) {
                                                    $applicant->delete();
                                                }
                                            }

                                            self::markHandled($record, 'rejected');
                                        });

                                        Notification::make()->success()->title('Demande refusée.')->send();
                                    } catch (\Throwable $e) {
                                        Notification::make()
                                            ->danger()
                                            ->title('Erreur lors du refus.')
                                            ->body('Détails: ' . $e->getMessage())
                                            ->send();
                                    }

                                    $action->cancel();
                                }),

                            /**
                             * =========================
                             * WORKFLOW BIO (non répétable)
                             * =========================
                             */
                            Tables\Actions\Action::make('approve_bio')
                                ->label('Approuver modif BIO')
                                ->color('success')
                                ->icon('heroicon-o-check')
                                ->visible(fn() => self::isBioRequest($record) && self::isPending($record))
                                ->requiresConfirmation()
                                ->modalHeading('Approuver la modification de bio ?')
                                ->action(function () use ($record, $action) {
                                    abort_unless(auth()->user()?->hasAnyRole(['admin', 'super_admin']), 403);
                                    $record->refresh();

                                    if (! self::ensurePendingOrWarn($record, $action)) {
                                        return;
                                    }

                                    $payload      = $record->data ?? [];
                                    $targetUserId = $payload['target_user_id'] ?? null;
                                    $proposedBio  = $payload['proposed_bio'] ?? null;
                                    $locale       = $payload['locale'] ?? 'fr';

                                    if (! $targetUserId || $proposedBio === null) {
                                        Notification::make()->danger()->title('Demande invalide (data manquant)')->send();
                                        $action->cancel();
                                        return;
                                    }

                                    $user = User::find($targetUserId);
                                    if (! $user) {
                                        Notification::make()->danger()->title('Utilisateur introuvable')->send();
                                        $action->cancel();
                                        return;
                                    }

                                    try {
                                        DB::transaction(function () use ($record, $user, $proposedBio, $locale) {
                                            $field = $locale === 'en' ? 'bio_en' : 'bio_fr';
                                            $user->update([$field => $proposedBio]);

                                            self::markHandled($record, 'approved');

                                            // réponse interne à l’émetteur (optionnel)
                                            if ($record->sender_id) {
                                                Message::create([
                                                    'sender_id'   => Filament::auth()->id(),
                                                    'receiver_id' => $record->sender_id,
                                                    'subject'     => 'Modification BIO approuvée',
                                                    'body'        => 'Votre demande de modification de biographie a été approuvée.',
                                                    'is_read'     => false,
                                                    'status'      => 'pending',
                                                    'data'        => [
                                                        'type'   => 'bio_change_response',
                                                        'status' => 'approved',
                                                    ],
                                                ]);
                                            }
                                        });

                                        Notification::make()->success()->title('Bio appliquée et demande approuvée')->send();
                                    } catch (\Throwable $e) {
                                        Notification::make()
                                            ->danger()
                                            ->title('Erreur lors de l’approbation.')
                                            ->body('Détails: ' . $e->getMessage())
                                            ->send();
                                    }

                                    $action->cancel();
                                }),

                            Tables\Actions\Action::make('reject_bio')
                                ->label('Refuser modif BIO')
                                ->color('danger')
                                ->icon('heroicon-o-x-mark')
                                ->visible(fn() => self::isBioRequest($record) && self::isPending($record))
                                ->requiresConfirmation()
                                ->modalHeading('Refuser la modification de bio ?')
                                ->form([
                                    Forms\Components\Textarea::make('reason')
                                        ->label('Raison (optionnel)')
                                        ->rows(4),
                                ])
                                ->action(function (array $data) use ($record, $action) {
                                    $record->refresh();

                                    if (! self::ensurePendingOrWarn($record, $action)) {
                                        return;
                                    }

                                    try {
                                        DB::transaction(function () use ($record, $data) {
                                            $record->update([
                                                'status'     => 'rejected',
                                                'handled_at' => now(),
                                                'handled_by' => Filament::auth()->id(),
                                                'is_read'    => true,
                                                'data'       => array_merge($record->data ?? [], [
                                                    'treated'          => true,
                                                    'rejection_reason' => $data['reason'] ?? null,
                                                ]),
                                            ]);

                                            if ($record->sender_id) {
                                                Message::create([
                                                    'sender_id'   => Filament::auth()->id(),
                                                    'receiver_id' => $record->sender_id,
                                                    'subject'     => 'Modification BIO refusée',
                                                    'body'        => 'Votre demande de modification de biographie a été refusée.' .
                                                        (! empty($data['reason'])
                                                            ? '<br><br><strong>Raison :</strong><br>' . $data['reason']
                                                            : ''),
                                                    'is_read' => false,
                                                    'status'  => 'pending',
                                                    'data'    => [
                                                        'type'   => 'bio_change_response',
                                                        'status' => 'rejected',
                                                    ],
                                                ]);
                                            }
                                        });

                                        Notification::make()->success()->title('Demande refusée')->send();
                                    } catch (\Throwable $e) {
                                        Notification::make()
                                            ->danger()
                                            ->title('Erreur lors du refus.')
                                            ->body('Détails: ' . $e->getMessage())
                                            ->send();
                                    }

                                    $action->cancel();
                                }),
                        ];
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Supprimer la sélection')
                        ->requiresConfirmation()
                        ->visible(fn() => auth()->user()?->hasAnyRole(['super_admin'])),
                ]),
                Tables\Actions\BulkAction::make('delete_all_filtered')
                    ->label('Supprimer tout (filtre actuel)')
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Supprimer toutes les demandes affichées ?')
                    ->modalDescription('Cette action supprimera toutes les demandes correspondant au filtre actuel (ex: En attente).')
                    ->visible(fn() => auth()->user()?->hasAnyRole(['super_admin']))
                    ->action(function (HasTable $livewire) {
                        // ✅ La query filtrée vient du composant Livewire (pas de Table)
                        $query = $livewire->getFilteredTableQuery();

                        $count = (clone $query)->count();
                        $query->delete();

                        Notification::make()
                            ->success()
                            ->title("Demandes supprimées ({$count}).")
                            ->send();
                    }),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSystemRequests::route('/'),
        ];
    }

    /**
     * =========================
     * Helpers
     * =========================
     */

    protected static function isPending(Message $record): bool
    {
        return ($record->status ?? 'pending') === 'pending';
    }

    protected static function ensurePendingOrWarn(Message $record, $action): bool
    {
        if (! self::isPending($record)) {
            Notification::make()->warning()->title('Cette demande a déjà été traitée.')->send();
            $action->cancel();
            return false;
        }

        return true;
    }

    protected static function isRegistrationRequest(Message $record): bool
    {
        return ($record->data['action_type'] ?? null) === 'registration_request';
    }

    protected static function isBioRequest(Message $record): bool
    {
        return ($record->data['type'] ?? null) === 'bio_change_request';
    }

    protected static function markHandled(Message $record, string $status): void
    {
        $record->update([
            'status'     => $status,
            'handled_at' => now(),
            'handled_by' => Filament::auth()->id(),
            'is_read'    => true,
            'data'       => array_merge($record->data ?? [], ['treated' => true]),
        ]);
    }
}
