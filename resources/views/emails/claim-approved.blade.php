<x-mail::message>
# ¡Tu negocio fue verificado!

Hola **{{ $claim->nombre_completo }}**,

Tu solicitud para **{{ $claim->lugar->nombre }}** fue aprobada. Ya podés gestionar tu ficha desde el panel de autogestión.

<x-mail::panel>
**Tus datos de acceso:**

**Email:** {{ $claim->email }}
**Contraseña:** {{ $password }}
</x-mail::panel>

Te recomendamos cambiar la contraseña una vez que ingreses.

Desde tu panel vas a poder:
- Editar la información de tu negocio
- Subir fotos y logo
- Publicar promociones
- Responder reseñas de clientes
- Ver estadísticas de visitas

<x-mail::button :url="config('app.url') . '/panel/login'">
Ingresar al panel
</x-mail::button>

¡Gracias por ser parte de {{ config('app.name') }}!

{{ config('app.name') }}
</x-mail::message>
