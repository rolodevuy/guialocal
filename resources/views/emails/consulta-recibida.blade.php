<x-mail::message>
# ¡Recibimos tu mensaje!

Hola **{{ $consulta->nombre }}**, gracias por escribirnos.

Recibimos tu consulta y te responderemos en un plazo de **24 a 48 horas hábiles**.

<x-mail::panel>
{{ $consulta->mensaje }}
</x-mail::panel>

Mientras tanto, podés ver todos los planes disponibles y lo que incluye cada uno.

<x-mail::button :url="config('app.url') . '/precios'">
Ver planes
</x-mail::button>

El equipo de **{{ config('app.name') }}**
</x-mail::message>
