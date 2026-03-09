<x-mail::message>
# Novedades de {{ $suscriptor->zona?->nombre ?? 'tu zona' }}

Hola, estas son las novedades de la semana en **Guía Local**.

---

@if($nuevosNegocios->isNotEmpty())
## 🏪 Nuevos negocios

@foreach($nuevosNegocios as $ficha)
**[{{ $ficha->lugar->nombre }}]({{ route('negocios.show', $ficha->lugar->slug) }})**
{{ $ficha->lugar->categoria->nombre }}{{ $ficha->lugar->zona ? ' · ' . $ficha->lugar->zona->nombre : '' }}

@endforeach
@endif

@if($promociones->isNotEmpty())
## 🏷️ Promociones vigentes

@foreach($promociones as $promo)
**{{ $promo->ficha->lugar->nombre ?? '' }}** — {{ $promo->titulo }}
@if($promo->fecha_fin)
*Válida hasta el {{ $promo->fecha_fin->translatedFormat('j \d\e F') }}*
@endif

@endforeach
@endif

@if($ultimoArticulo)
## 📰 Del barrio

**[{{ $ultimoArticulo->titulo }}]({{ route('articulos.show', $ultimoArticulo->slug) }})**

{{ \Illuminate\Support\Str::limit($ultimoArticulo->extracto, 200) }}

<x-mail::button :url="route('articulos.show', $ultimoArticulo->slug)">
Leer más
</x-mail::button>
@endif

---

Guía Local · Tu barrio en un solo lugar

[Darme de baja]({{ $urlBaja }})
</x-mail::message>
