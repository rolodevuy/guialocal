<x-mail::message>
# ¡Tu negocio fue verificado!

Hola {{ $claim->nombre_completo }},

Tu solicitud para **{{ $claim->lugar->nombre }}** fue aprobada. Ya podés gestionar tu ficha desde el panel de autogestión.

**Tus datos de acceso:**
- **Email:** {{ $claim->email }}
- **Contraseña:** {{ $password }}

Te recomendamos cambiar la contraseña una vez que ingreses.

<x-mail::button :url="config('app.url') . '/panel/login'">
Ingresar al panel
</x-mail::button>

{{ config('app.name') }}
</x-mail::message>
