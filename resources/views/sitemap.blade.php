<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
        xmlns:xhtml="http://www.w3.org/1999/xhtml">

    {{-- ── Pages statiques ─────────────────────────────────────────── --}}
    @foreach($staticUrls as $url)
    <url>
        <loc>{{ $url['loc'] }}</loc>
        <priority>{{ $url['priority'] }}</priority>
        @if(!empty($url['changefreq']))<changefreq>{{ $url['changefreq'] }}</changefreq>@endif
    </url>
    @endforeach

    {{-- ── Conseillers ─────────────────────────────────────────────── --}}
    @foreach($memberUrls as $url)
    <url>
        <loc>{{ $url['loc'] }}</loc>
        <priority>{{ $url['priority'] }}</priority>
        @if(!empty($url['changefreq']))<changefreq>{{ $url['changefreq'] }}</changefreq>@endif
        @if(!empty($url['lastmod']))<lastmod>{{ $url['lastmod'] }}</lastmod>@endif
    </url>
    @endforeach

    {{-- ── Articles de blog ────────────────────────────────────────── --}}
    @foreach($postUrls as $url)
    <url>
        <loc>{{ $url['loc'] }}</loc>
        <priority>{{ $url['priority'] }}</priority>
        @if(!empty($url['changefreq']))<changefreq>{{ $url['changefreq'] }}</changefreq>@endif
        @if(!empty($url['lastmod']))<lastmod>{{ $url['lastmod'] }}</lastmod>@endif
    </url>
    @endforeach

</urlset>
