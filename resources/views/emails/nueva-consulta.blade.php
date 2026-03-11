<x-mail::message>
# Nueva consulta recibida

Llegó un mensaje desde el formulario de contacto de **{{ config('app.name') }}**.

<x-mail::table>
| Campo | Detalle |
|:------|:--------|
| **Nombre** | {{ $consulta->nombre }} |
| **Email** | {{ $consulta->email }} |
@if($consulta->asunto)| **Asunto** | {{ $consulta->asunto }} |
@endif| **Fecha** | {{ $consulta->created_at->translatedFormat('j M Y, H:i') }} |
</x-mail::table>

**Mensaje:**

> {{ $consulta->mensaje }}

<x-mail::button :url="config('app.url') . '/admin/consultas'">
Gestionar consultas
</x-mail::button>

{{ config('app.name') }}
</x-mail::message>
