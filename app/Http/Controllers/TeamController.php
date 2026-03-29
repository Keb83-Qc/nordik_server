<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App\Models\User;
use App\Models\TeamTitle;
use App\Models\SystemLog;

class TeamController extends Controller
{
    public function index(Request $request)
    {
        $ville   = $request->get('ville', '');
        $langue  = $request->get('langue', '');

        // Données de base mises en cache 30 min (liste des villes + membres complets)
        // La clé varie par filtre pour que chaque combinaison ait son propre cache
        $cacheKey = 'team_index_' . md5($ville . '|' . $langue);

        $teamData = Cache::remember($cacheKey, 1800, function () use ($ville, $langue) {
            // Villes disponibles
            $cities = User::query()
                ->whereNotNull('city')
                ->where('city', '!=', '')
                ->where('position', '!=', 0)
                ->whereHas('role', fn($q) => $q->where('name', 'not like', '%Candidat%'))
                ->distinct()
                ->orderBy('city')
                ->pluck('city');

            // Membres
            $query = User::with(['title', 'role'])
                ->where('position', '!=', 0)
                ->whereHas('role', fn($q) => $q->where('name', 'not like', '%Candidat%'));

            if ($ville !== '') {
                $query->where('city', $ville);
            }

            if ($langue !== '') {
                $query->whereJsonContains('languages', $langue);
            }

            $members = $query->orderBy('position')->orderBy('last_name')->get();

            return compact('cities', 'members');
        });

        return view('pages.equipe', array_merge($teamData, [
            'selected_city'       => $ville,
            'selected_lang'       => $langue,
            'available_languages' => User::getAvailableLanguages(),
            'header_title'        => __('TeamController.header_title'),
            'header_subtitle'     => __('TeamController.header_subtitle'),
            'header_bg'           => asset('assets/img/equipe/canvas.png'),
            'title'               => __('TeamController.meta_title'),
        ]));
    }

    public function show(string $slug)
    {
        // On essaie d'abord le cache, mais on attrape le ModelNotFoundException
        // pour logger proprement avant de renvoyer une 404
        try {
            $member = Cache::remember("team_member_{$slug}", 86400, function () use ($slug) {
                return User::with(['title', 'role'])
                    ->where('slug', $slug)
                    ->firstOrFail();
            });
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException) {
            // Invalide le cache au cas où l'entrée corrompue serait mise en cache
            Cache::forget("team_member_{$slug}");

            SystemLog::record('warning', "[404] Profil conseiller introuvable : {$slug}", [
                'slug'       => $slug,
                'url'        => request()->fullUrl(),
                'referer'    => request()->header('referer', ''),
                'user_agent' => mb_substr(request()->userAgent() ?? '', 0, 200),
            ], SystemLog::SOURCE_PUBLIC);

            abort(404, "Le profil conseiller « {$slug} » est introuvable.");
        }

        $display_role = $member->title
            ? $member->title->title_name
            : ($member->role->name ?? 'Conseiller');

        $full_name = $member->first_name . ' ' . $member->last_name;

        return view('pages.team_detail', [
            'member'          => $member,
            'display_role'    => $display_role,
            'full_name'       => $full_name,
            'header_title'    => $full_name,
            'header_subtitle' => $display_role,
            'header_bg'       => asset('assets/img/equipe/canvas.png'),
            'title'           => $full_name . ' - ' . $display_role,
        ]);
    }
}
