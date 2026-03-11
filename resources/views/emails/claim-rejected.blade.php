<x-mail::message>
# Solicitud de reclamo

Hola {{ $claim->nombre_completo }},

Lamentablemente no pudimos verificar tu solicitud para **{{ $claim->lugar->nombre }}**.

**Motivo:** {{ $claim->motivo_rechazo }}

Si creés que fue un error, podés enviar una nueva solicitud con la documentación correcta.

<x-mail::button :url="config('app.url') . '/negocios/' . $claim->lugar->slug . '/reclamar'">
Reintentar solicitud
</x-mail::button>

{{ config('app.name') }}
</x-mail::message>
