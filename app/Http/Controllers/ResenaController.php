<?php

namespace App\Http\Controllers;

use App\Models\Ficha;
use App\Models\Lugar;
use App\Models\Resena;
use Illuminate\Http\Request;

class ResenaController extends Controller
{
    public function store(Request $request, string $slug)
    {
        // Verificar feature flag
        abort_unless(config('features.resenas'), 404);

        $lugar = Lugar::where('slug', $slug)->where('activo', true)->firstOrFail();
        $ficha = $lugar->fichas()->activo()->firstOrFail();

        $validated = $request->validate([
            'nombre' => ['required', 'string', 'max:100'],
            'email'  => ['nullable', 'email', 'max:150'],
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'cuerpo' => ['required', 'string', 'min:10', 'max:1000'],
        ], [
            'nombre.required' => 'Tu nombre es obligatorio.',
            'rating.required' => 'Seleccioná una puntuación.',
            'cuerpo.required' => 'Escribí algo en tu reseña.',
            'cuerpo.min'      => 'La reseña debe tener al menos 10 caracteres.',
        ]);

        $ficha->resenas()->create($validated);

        return back()->with('resena_enviada', true);
    }
}
