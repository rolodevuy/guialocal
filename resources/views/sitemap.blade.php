<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>

<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">

    {{-- Páginas estáticas --}}
    <url>
        <loc>{{ route('home') }}</loc>
        <changefreq>daily</changefreq>
        <priority>1.0</priority>
    </url>
    <url>
        <loc>{{ route('negocios.index') }}</loc>
        <changefreq>daily</changefreq>
        <priority>0.9</priority>
    </url>
    <url>
        <loc>{{ route('categorias.index') }}</loc>
        <changefreq>weekly</changefreq>
        <priority>0.8</priority>
    </url>
    <url>
        <loc>{{ route('contacto.show') }}</loc>
        <changefreq>monthly</changefreq>
        <priority>0.5</priority>
    </url>
    <url>
        <loc>{{ route('quienes-somos') }}</loc>
        <changefreq>monthly</changefreq>
        <priority>0.4</priority>
    </url>

    {{-- Negocios activos --}}
    @foreach($negocios as $negocio)
    <url>
        <loc>{{ route('negocios.show', $negocio->slug) }}</loc>
        <lastmod>{{ $negocio->updated_at->toAtomString() }}</lastmod>
        <changefreq>weekly</changefreq>
        <priority>0.8</priority>
    </url>
    @endforeach

    {{-- Categorías activas --}}
    @foreach($categorias as $categoria)
    <url>
        <loc>{{ route('categorias.show', $categoria->slug) }}</loc>
        <lastmod>{{ $categoria->updated_at->toAtomString() }}</lastmod>
        <changefreq>weekly</changefreq>
        <priority>0.7</priority>
    </url>
    @endforeach

    {{-- Zonas --}}
    @foreach($zonas as $zona)
    <url>
        <loc>{{ route('zonas.show', $zona->slug) }}</loc>
        <lastmod>{{ $zona->updated_at->toAtomString() }}</lastmod>
        <changefreq>weekly</changefreq>
        <priority>0.6</priority>
    </url>
    @endforeach

</urlset>
