<x-mail::message>
# Recibimos tu solicitud

Hola **{{ $claim->nombre_completo }}**,

Recibimos tu solicitud para gestionar **{{ $claim->lugar->nombre }}**. La revisaremos en las próximas 24-48 horas hábiles.

<x-mail::panel>
**Resumen de tu solicitud:**

**Negocio:** {{ $claim->lugar->nombre }}
**RUT:** {{ $claim->rut_numero }}
**Fecha:** {{ $claim->created_at->format('d/m/Y H:i') }}
</x-mail::panel>

Una vez aprobada, recibirás otro email con tus datos de acceso al panel de gestión.

Si tenés alguna duda, respondé a este correo y te ayudamos.

{{ config('app.name') }}
</x-mail::message>
