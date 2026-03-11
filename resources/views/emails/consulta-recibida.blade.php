<x-mail::message>
# ¡Recibimos tu mensaje!

Hola **{{ $consulta->nombre }}**, gracias por escribirnos.

Recibimos tu consulta y te responderemos en un plazo de **24 a 48 horas hábiles**.

<x-mail::panel>
{{ $consulta->mensaje }}
</x-mail::panel>

Si tenés alguna otra consulta, podés responder este correo o visitarnos en:

<x-mail::button :url="config('app.url') . '/contacto'">
Volver a contacto
</x-mail::button>

{{ config('app.name') }}
</x-mail::message>
