<x-mail::message>
# Nuevo reclamo de negocio

Un usuario quiere reclamar la titularidad de un negocio en **{{ config('app.name') }}**.

<x-mail::table>
| Campo | Detalle |
|:------|:--------|
| **Negocio** | {{ $claim->lugar->nombre }} |
| **Solicitante** | {{ $claim->nombre_completo }} |
| **Email** | {{ $claim->email }} |
| **Teléfono** | {{ $claim->telefono }} |
| **RUT** | {{ $claim->rut_numero }} |
</x-mail::table>

@if($claim->mensaje)
**Mensaje del solicitante:**

> {{ $claim->mensaje }}
@endif

La constancia de RUT fue adjuntada a la solicitud. Revisala desde el panel de administración.

<x-mail::button :url="config('app.url') . '/admin/claim-requests'">
Revisar solicitud
</x-mail::button>

{{ config('app.name') }}
</x-mail::message>
