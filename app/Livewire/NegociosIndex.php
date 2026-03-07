<?php

namespace App\Livewire;

use App\Models\Categoria;
use App\Models\Ficha;
use App\Models\Zona;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class NegociosIndex extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public string $q = '';

    #[Url(as: 'categoria')]
    public string $categoria = '';

    #[Url(as: 'zona')]
    public string $zona = '';

    public function mount(): void
    {
        if (empty($this->zona)) {
            $this->zona = request()->cookie('zona_preferida', '');
        }
    }

    public function updatingQ(): void
    {
        $this->resetPage();
    }

    public function updatingCategoria(): void
    {
        $this->resetPage();
    }

    public function updatingZona(): void
    {
        $this->resetPage();
    }

    public function updatedZona(string $value): void
    {
        $this->dispatch('guardar-zona', slug: $value);
    }

    public function limpiar(): void
    {
        $this->reset(['q', 'categoria', 'zona']);
        $this->dispatch('guardar-zona', slug: '');
        $this->resetPage();
    }

    public function render()
    {
        $categorias = Categoria::activo()->orderBy('nombre')->get();
        $zonas      = Zona::orderBy('nombre')->get();

        $fichas = Ficha::activo()
            ->whereHas('lugar', fn ($q) => $q->where('activo', true))
            ->with(['lugar.categoria', 'lugar.zona'])
            ->when(trim($this->q), function ($query, $q) {
                $query->where(function ($sub) use ($q) {
                    $sub->where('descripcion', 'like', "%{$q}%")
                        ->orWhereHas('lugar', fn ($l) => $l
                            ->where('nombre', 'like', "%{$q}%")
                            ->orWhere('direccion', 'like', "%{$q}%")
                        );
                });
            })
            ->when($this->categoria, fn ($q) => $q->whereHas('lugar', fn ($l) => $l
                ->whereHas('categoria', fn ($c) => $c->where('slug', $this->categoria))
            ))
            ->when($this->zona, fn ($q) => $q->whereHas('lugar', fn ($l) => $l
                ->whereHas('zona', fn ($z) => $z->where('slug', $this->zona))
            ))
            ->orderByDesc('featured_score')
            ->paginate(12);

        return view('livewire.negocios-index', compact('fichas', 'categorias', 'zonas'));
    }
}
