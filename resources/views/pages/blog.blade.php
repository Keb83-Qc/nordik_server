@include('partials.header')

<section class="section-padding bg-light py-5">
    <div class="container">
        <div class="row">

            {{-- COLONNE GAUCHE : ARTICLES (8 colonnes) --}}
            <div class="col-lg-8">

                {{-- Affichage des filtres actifs --}}
                @if(request('category') || request('search'))
                <div class="alert alert-white shadow-sm border-start border-4 border-warning d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <strong><i class="fas fa-filter text-warning me-2"></i>{{ __('blog.active_filters') }}</strong>
                        @if(request('search')) "<em>{{ request('search') }}</em>" @endif
                        @if(request('search') && request('category')) & @endif
                        @if(request('category')) {{ __('blog.category_label') }} <em>{{ request('category') }}</em> @endif
                    </div>
                    <a href="{{ route('blog') }}" class="btn btn-sm btn-outline-danger rounded-pill">
                        <i class="fas fa-times"></i> {{ __('blog.clear_filters') }}
                    </a>
                </div>
                @endif

                <div class="row g-4">
                    @if($posts->isEmpty())
                    <div class="col-12 text-center py-5 bg-white rounded shadow-sm">
                        <div class="mb-3"><i class="far fa-newspaper fa-3x text-muted opacity-50"></i></div>
                        <h4 class="text-muted">{{ __('blog.no_posts_found') }}</h4>
                        <a href="{{ route('blog') }}" class="btn btn-primary mt-3 rounded-pill px-4">
                            {{ __('blog.view_all') }}
                        </a>
                    </div>
                    @else
                    @foreach($posts as $post)
                    @php
                    // Image
                    $imgSrc = $post->image_url ?: asset('assets/img/default.jpg');

                    // Lien : on passe le slug de la locale courante (resolveRouteBinding le gère)
                    $link = route('blog.show', ['post' => $post->slug]);

                    // Titre/catégorie : HasTranslations => renvoie la bonne langue automatiquement
                    $displayTitle = $post->title;
                    $cat = !empty($post->category) ? $post->category : __('blog.default_category');

                    // Description
                    $desc = \Illuminate\Support\Str::limit(strip_tags(html_entity_decode((string) $post->content)), 110, '...');
                    @endphp

                    <div class="col-md-6">
                        <div class="blog-card h-100 d-flex flex-column shadow-sm hover-lift bg-white rounded overflow-hidden border-0">

                            {{-- Image --}}
                            <div class="blog-thumb position-relative" style="max-height: 240px; overflow: hidden; border-bottom: 3px solid #f8f9fa;">
                                <a href="{{ $link }}">
                                    <img
                                        src="{{ $imgSrc }}"
                                        alt="{{ $displayTitle }}"
                                        class="w-100 h-100 object-fit-cover transition-scale"
                                        style="object-position: {{ $post->image_position ?? 'center' }};">
                                </a>

                                {{-- Badge Catégorie (lien localisé) --}}
                                <a
                                    href="{{ route('blog', ['category' => $cat]) }}"
                                    class="badge bg-warning text-dark position-absolute top-0 end-0 m-3 text-decoration-none shadow-sm fw-bold px-3 py-2 rounded-pill"
                                    style="font-size: 0.75rem; letter-spacing: 0.5px;">
                                    {{ $cat }}
                                </a>
                            </div>

                            {{-- Contenu --}}
                            <div class="blog-content d-flex flex-column flex-grow-1 p-4">
                                <div class="blog-meta small text-muted mb-2 text-uppercase d-flex align-items-center">
                                    <i class="far fa-clock me-2 text-warning"></i>
                                    {{ $post->created_at->locale(app()->getLocale())->isoFormat('D MMM YYYY') }}
                                </div>

                                <h4 class="blog-title mb-3 fw-bold fs-5 lh-sm">
                                    <a href="{{ $link }}" class="text-dark text-decoration-none hover-gold">
                                        {{ $displayTitle }}
                                    </a>
                                </h4>

                                <p class="text-muted small mb-4 flex-grow-1">{{ $desc }}</p>

                                <div class="mt-auto pt-3 border-top d-flex justify-content-between align-items-center">
                                    <a href="{{ $link }}" class="text-uppercase small fw-bold text-dark text-decoration-none hover-gold">
                                        {{ __('blog.read_more') }}
                                    </a>
                                    <i class="fas fa-arrow-right text-warning opacity-75"></i>
                                </div>
                            </div>

                        </div>
                    </div>
                    @endforeach
                    @endif
                </div>

                {{-- PAGINATION --}}
                <div class="row mt-5">
                    <div class="col-12 d-flex justify-content-center">
                        {{ $posts->withQueryString()->links() }}
                    </div>
                </div>
            </div>

            {{-- COLONNE DROITE : SIDEBAR (4 colonnes) --}}
            <div class="col-lg-4 mt-5 mt-lg-0">
                <div class="sidebar sticky-top" style="top: 120px; z-index: 10;">

                    {{-- Widget 1: Recherche --}}
                    <div class="widget bg-white p-4 rounded shadow-sm mb-4 border-0">
                        <h5 class="fw-bold mb-3 border-bottom pb-2">{{ __('blog.search_title') }}</h5>

                        <form action="{{ route('blog') }}" method="GET" class="d-flex">
                            {{-- conserve la catégorie si déjà sélectionnée --}}
                            @if(request('category'))
                            <input type="hidden" name="category" value="{{ request('category') }}">
                            @endif

                            <input type="text" name="search" class="form-control me-2"
                                placeholder="{{ __('blog.search_placeholder') }}" value="{{ request('search') }}">
                            <button type="submit" class="btn btn-warning text-dark fw-bold">
                                <i class="fas fa-search"></i>
                            </button>
                        </form>
                    </div>

                    {{-- Widget 2: Catégories (préparées dans le controller) --}}
                    <div class="widget bg-white p-4 rounded shadow-sm mb-4 border-0">
                        <h5 class="fw-bold mb-3 border-bottom pb-2">{{ __('blog.categories_title') }}</h5>
                        <ul class="list-unstyled mb-0">

                            @forelse($categories as $catItem)
                            @php
                            $catName = (string) $catItem->name;
                            $count = (int) $catItem->total;
                            @endphp

                            @if($count > 0)
                            <li class="mb-2">
                                <a
                                    href="{{ route('blog', ['category' => $catName]) }}"
                                    class="d-flex justify-content-between align-items-center text-decoration-none text-secondary hover-gold py-1 border-bottom border-light">
                                    <span><i class="fas fa-angle-right me-2 text-warning"></i> {{ $catName }}</span>
                                    <span class="badge bg-light text-dark border">{{ $count }}</span>
                                </a>
                            </li>
                            @endif
                            @empty
                            <li class="text-muted small">{{ __('blog.no_categories') }}</li>
                            @endforelse
                        </ul>
                    </div>

                    {{-- Widget 3: Articles Récents (préparés dans le controller) --}}
                    <div class="widget bg-white p-4 rounded shadow-sm border-0">
                        <h5 class="fw-bold mb-3 border-bottom pb-2">{{ __('blog.recent_posts_title') }}</h5>
                        <ul class="list-unstyled mb-0">

                            @foreach($recents as $recent)
                            @php
                            $recentLink = route('blog.show', ['post' => $recent->slug]);
                            $recentImg = $recent->image_url ?: asset('assets/img/default.jpg');
                            $recentTitle = (string) $recent->title;
                            @endphp

                            <li class="mb-3 d-flex align-items-center">
                                <a href="{{ $recentLink }}" class="flex-shrink-0 me-3">
                                    <img
                                        src="{{ $recentImg }}"
                                        class="rounded object-fit-cover shadow-sm"
                                        width="65"
                                        height="65"
                                        alt="Thumb"
                                        style="border: 1px solid #eee;">
                                </a>
                                <div>
                                    <h6 class="mb-1" style="font-size: 0.95rem; line-height: 1.3;">
                                        <a href="{{ $recentLink }}" class="text-dark text-decoration-none hover-gold">
                                            {{ \Illuminate\Support\Str::limit($recentTitle, 40) }}
                                        </a>
                                    </h6>
                                    <small class="text-muted" style="font-size: 0.75rem;">
                                        <i class="far fa-calendar-alt me-1"></i>
                                        {{ $recent->created_at->locale(app()->getLocale())->isoFormat('D MMM YYYY') }}
                                    </small>
                                </div>
                            </li>
                            @endforeach

                        </ul>
                    </div>

                </div>
            </div>

        </div>
    </div>
</section>

<style>
    .transition-scale {
        transition: transform 0.5s ease;
    }

    .hover-lift:hover .transition-scale {
        transform: scale(1.05);
    }

    .hover-lift {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .hover-lift:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1) !important;
    }

    .hover-gold:hover {
        color: #b88b4a !important;
    }

    .page-link {
        color: #0E1030;
    }

    .page-item.active .page-link {
        background-color: #0E1030;
        border-color: #0E1030;
    }

    .blog-title {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        min-height: 2.8rem;
    }
</style>

@include('partials.footer')