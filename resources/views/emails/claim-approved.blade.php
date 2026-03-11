<x-mail::message>
# ¡Tu negocio fue verificado!

Hola **{{ $claim->nombre_completo }}**,

Tu solicitud para **{{ $claim->lugar->nombre }}** fue aprobada. Ya podés gestionar tu ficha desde el panel de autogestión.

@if($isNewUser)
<x-mail::panel>
**Tus datos de acceso:**

**Email:** {{ $claim->email }}
**Contraseña:** {{ $password }}
</x-mail::panel>

Te recomendamos cambiar la contraseña una vez que ingreses.
@else
<x-mail::panel>
Ya tenés una cuenta con **{{ $claim->email }}**. Usá tu contraseña actual para ingresar. Si no la recordás, podés resetearla desde el enlace de abajo.
</x-mail::panel>
@endif

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
