<x-mail::message>
# Nuevo reclamo de negocio

Se recibió una solicitud de reclamo para **{{ $claim->lugar->nombre }}**.

**Solicitante:** {{ $claim->nombre_completo }}
**Email:** {{ $claim->email }}
**Teléfono:** {{ $claim->telefono }}
**RUT:** {{ $claim->rut_numero }}

@if($claim->mensaje)
**Mensaje:** {{ $claim->mensaje }}
@endif

<x-mail::button :url="config('app.url') . '/admin/claim-requests'">
Ver en el panel
</x-mail::button>

{{ config('app.name') }}
</x-mail::message>
