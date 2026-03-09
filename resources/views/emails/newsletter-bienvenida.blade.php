<x-mail::message>
# ¡Bienvenido al newsletter de Guía Local!

Hola, ya estás suscrito a las novedades de **{{ $suscriptor->zona?->nombre ?? 'tu ciudad' }}**.

Vas a recibir periódicamente:

- 🏪 **Nuevos negocios** que se suman a la guía
- 🏷️ **Promociones vigentes** en tu zona
- 📰 **Artículos y guías** del barrio

---

Nada más por ahora. Te escribimos cuando haya novedades.

<x-mail::button :url="route('home')">
Ver la guía
</x-mail::button>

---

*¿No querés recibir más mails?*
[Darme de baja]({{ $urlBaja }})
</x-mail::message>
