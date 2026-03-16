<?php

return [
    'groups' => [

        // ─── ESPACE CONSEILLER ───────────────────────────────────
        'Espace Conseiller' => [
            'label'     => 'Espace Conseiller',
            'icon'      => 'heroicon-o-user-circle',
            'sort'      => 1,
            'collapsed' => true,
        ],

        // ─── CLIENTS ─────────────────────────────────────────────
        'Gestion Clients' => [
            'label'     => 'Clients & Soumissions',
            'icon'      => 'heroicon-o-inbox-arrow-down',
            'sort'      => 2,
            'collapsed' => true,
        ],

        // ─── SITE WEB ─────────────────────────────────────────────
        'Site Web' => [
            'label'     => 'Site Web',
            'icon'      => 'heroicon-o-globe-alt',
            'sort'      => 3,
            'collapsed' => true,
        ],

        // ─── MARKETING ────────────────────────────────────────────
        'Marketing' => [
            'label'     => 'Marketing & Blog',
            'icon'      => 'heroicon-o-megaphone',
            'sort'      => 4,
            'collapsed' => true,
        ],

        // ─── CONSEILLERS (RH / admin) ─────────────────────────────
        'Gestion Conseillers' => [
            'label'     => 'Équipe & Conseillers',
            'icon'      => 'heroicon-o-users',
            'sort'      => 5,
            'collapsed' => true,
        ],

        // ─── ADMIN SYSTÈME ────────────────────────────────────────
        'Configuration' => [
            'label'     => 'Administration Système',
            'icon'      => 'heroicon-o-cog-6-tooth',
            'sort'      => 6,
            'collapsed' => true,
        ],

        // ─── TRADUCTIONS ──────────────────────────────────────────
        'GestionLangues' => [
            'label'     => 'Traductions',
            'icon'      => 'heroicon-o-language',
            'sort'      => 7,
            'collapsed' => true,
        ],
    ],

    'sort' => [
        // Espace Conseiller (+ admin: Gestion Conseillers)
        \App\Filament\Resources\MessageResource::class          => 1,
        \App\Filament\Pages\CommissionCalculator::class         => 2,
        \App\Filament\Resources\WikiArticleResource::class      => 3,
        \App\Filament\Resources\BugReportResource::class        => 5,

        // Clients & Soumissions
        \App\Filament\Resources\SubmissionResource::class       => 1,

        // Site Web
        \App\Filament\Resources\PublicServiceCategoryResource::class => 1,
        \App\Filament\Resources\PublicServiceResource::class         => 2,

        // Marketing & Blog
        \App\Filament\Resources\BlogPostResource::class         => 1,
        \App\Filament\Resources\SlideResource::class            => 2,
        \App\Filament\Resources\HomepageStatResource::class     => 3,
        \App\Filament\Resources\PartnerResource::class          => 4,
        \App\Filament\Resources\ServiceResource::class          => 5,

        // Équipe & Conseillers
        \App\Filament\Resources\UserResource::class             => 1,
        \App\Filament\Resources\EmployeeResource::class         => 2,
        \App\Filament\Resources\TeamTitleResource::class        => 3,

        // Administration Système
        \App\Filament\Resources\TauxCommissionResource::class   => 1,
        \App\Filament\Resources\CompagnieInfoResource::class    => 2,
        \App\Filament\Resources\VehicleBrandResource::class     => 3,
        \App\Filament\Resources\CareerPageResource::class       => 4,
        \App\Filament\Resources\ToolResource::class             => 5,
        \App\Filament\Pages\AdvisorLinks::class                 => 6,
        \App\Filament\Pages\ManageGoogleReviews::class          => 7,
        \App\Filament\Pages\MailSettingsPage::class             => 8,
        \App\Filament\Resources\SystemLogResource::class        => 9,
    ],

    // Liens externes / panels séparés
    'links' => [
        [
            'label'   => 'Analyse des Besoins Financiers',
            'icon'    => 'heroicon-o-arrow-top-right-on-square',
            'url'     => '/abf',
            'group'   => 'Espace Conseiller',
            'sort'    => 4,
            'new_tab' => true,
        ],
    ],
];
