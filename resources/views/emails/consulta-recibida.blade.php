<x-mail::message>
# ¡Recibimos tu mensaje!

Hola **{{ $consulta->nombre }}**, gracias por escribirnos.

Recibimos tu consulta y te responderemos en un plazo de **24 a 48 horas hábiles**.

<x-mail::panel>
{{ $consulta->mensaje }}
</x-mail::panel>

Si tenés alguna otra consulta, no dudes en escribirnos nuevamente.

<x-mail::button :url="config('app.url') . '/contacto'">
Ir a contacto
</x-mail::button>

El equipo de **{{ config('app.name') }}**
</x-mail::message>
