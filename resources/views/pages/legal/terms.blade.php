<x-page-layout :withBlog="false">

<section class="section-padding bg-white">
    <div class="container" style="max-width:820px">

        <h2 class="fw-bold mb-4" style="color:var(--navy,#0e1030)">{{ __('legal.terms_title') }}</h2>
        <p class="text-muted mb-4">{{ __('legal.last_updated') }} : {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</p>

        <div class="legal-content" style="line-height:1.8;color:#374151">
            @includeFirst([
                'pages.legal.partials.terms-' . app()->getLocale(),
                'pages.legal.partials.terms-fr',
            ])
        </div>

    </div>
</section>

</x-page-layout>
