<x-mail::message>
# Nueva consulta en Guía Local

Llegó un mensaje nuevo desde el formulario de contacto.

**Nombre:** {{ $consulta->nombre }}
**Email:** {{ $consulta->email }}

**Mensaje:**
{{ $consulta->mensaje }}

<x-mail::button :url="config('app.url') . '/admin/consultas'">
Ver en el panel
</x-mail::button>

{{ config('app.name') }}
</x-mail::message>
