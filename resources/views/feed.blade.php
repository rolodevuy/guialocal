<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?><rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">
<channel>
    <title>{{ config('app.name') }} — Artículos</title>
    <link>{{ url('/') }}</link>
    <description>Notas y guías sobre negocios y servicios del barrio.</description>
    <language>es</language>
    <lastBuildDate>{{ now()->toRfc2822String() }}</lastBuildDate>
    <atom:link href="{{ route('feed') }}" rel="self" type="application/rss+xml"/>

    @foreach($articulos as $articulo)
    <item>
        <title><![CDATA[{{ $articulo->titulo }}]]></title>
        <link>{{ route('articulos.show', $articulo) }}</link>
        <guid isPermaLink="true">{{ route('articulos.show', $articulo) }}</guid>
        <description><![CDATA[{{ $articulo->extracto ?? Str::limit(strip_tags($articulo->cuerpo), 300) }}]]></description>
        <pubDate>{{ ($articulo->publicado_en ?? $articulo->created_at)->toRfc2822String() }}</pubDate>
        @if($articulo->categoria)
        <category>{{ $articulo->categoria->nombre }}</category>
        @endif
        @if($articulo->getFirstMediaUrl('portada'))
        <enclosure url="{{ $articulo->getFirstMediaUrl('portada') }}" type="image/jpeg"/>
        @endif
    </item>
    @endforeach

</channel>
</rss>
