<x-mail::message>
# Solicitud de reclamo

Hola **{{ $claim->nombre_completo }}**,

Revisamos tu solicitud para **{{ $claim->lugar->nombre }}** y lamentablemente no pudimos verificarla en esta oportunidad.

<x-mail::panel>
**Motivo:** {{ $claim->motivo_rechazo }}
</x-mail::panel>

Si creés que fue un error, podés enviar una nueva solicitud con la documentación correcta. Asegurate de que la constancia de RUT esté vigente y sea legible.

<x-mail::button :url="config('app.url') . '/negocios/' . $claim->lugar->slug . '/reclamar'">
Reintentar solicitud
</x-mail::button>

Si tenés dudas, respondé a este correo y te ayudamos.

{{ config('app.name') }}
</x-mail::message>
