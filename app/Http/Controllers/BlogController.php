<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use App\Models\Language;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    public function index(Request $request)
    {
        $search   = trim((string) $request->input('search', ''));
        $category = trim((string) $request->input('category', ''));
        $lang     = app()->getLocale();
        $fallback = Language::defaultCode() ?? config('app.fallback_locale', 'fr');

        $query = BlogPost::query();

        if ($category !== '') {
            $query->where(function ($q) use ($category, $lang, $fallback) {
                $q->where("category->{$lang}", 'like', "%{$category}%")
                    ->orWhere("category->{$fallback}", 'like', "%{$category}%");
            });
        }

        if ($search !== '') {
            $query->where(function ($q) use ($search, $lang, $fallback) {
                $q->where("title->{$lang}", 'like', "%{$search}%")
                    ->orWhere("content->{$lang}", 'like', "%{$search}%")
                    ->orWhere("title->{$fallback}", 'like', "%{$search}%")
                    ->orWhere("content->{$fallback}", 'like', "%{$search}%");
            });
        }

        $posts = $query->orderByDesc('created_at')
            ->paginate(6)
            ->withQueryString();

        // Catégories + compteur (bindings pour robustesse)
        $categories = BlogPost::query()
            ->whereNotNull('category')
            ->selectRaw("
                COALESCE(
                    NULLIF(JSON_UNQUOTE(JSON_EXTRACT(category, CONCAT('$.', ?))), ''),
                    NULLIF(JSON_UNQUOTE(JSON_EXTRACT(category, CONCAT('$.', ?))), '')
                ) as name
            ", [$lang, $fallback])
            ->selectRaw("COUNT(*) as total")
            ->groupBy('name')
            ->havingRaw("name is not null and name != ''")
            ->orderBy('name')
            ->get();

        $recents = BlogPost::query()
            ->orderByDesc('created_at')
            ->take(3)
            ->get();

        return view('pages.blog', [
            'posts' => $posts,
            'search' => $search,
            'category' => $category,
            'categories' => $categories,
            'recents' => $recents,
        ]);
    }

    public function show(string $locale, BlogPost $post)
    {
        $lang = app()->getLocale();
        $fallback = Language::defaultCode() ?? config('app.fallback_locale', 'fr');

        // Récupère les 7 posts voisins en une seule requête puis extrait prev/next/recents
        $neighbors = BlogPost::query()
            ->where('id', '!=', $post->id)
            ->where(function ($q) use ($lang, $fallback) {
                $q->whereNotNull("title->{$lang}")
                    ->orWhereNotNull("title->{$fallback}");
            })
            ->where(function ($q) use ($post) {
                $q->where('created_at', '<', $post->created_at)
                    ->orWhere('created_at', '>', $post->created_at);
            })
            ->orderByDesc('created_at')
            ->take(7)
            ->get();

        $recentPosts = $neighbors->take(5);

        $prevPost = $neighbors->first(fn($p) => $p->created_at < $post->created_at);
        $nextPost  = $neighbors->sortBy('created_at')->first(fn($p) => $p->created_at > $post->created_at);

        $header_bg = $post->image_url ?: asset('assets/img/blog/Entete-page-blog1.jpg');

        $title = $post->getTranslation('title', $lang, false)
            ?: $post->getTranslation('title', $fallback, false)
            ?: 'VIP GPI';

        return view('pages.article', [
            'article' => $post,
            'recentPosts' => $recentPosts,
            'prevPost' => $prevPost,
            'nextPost' => $nextPost,
            'header_bg' => $header_bg,
            'title' => $title . ' | VIP GPI',
        ]);
    }
}
