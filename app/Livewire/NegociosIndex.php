<?php

namespace App\Livewire;

use App\Models\Categoria;
use App\Models\Negocio;
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

    public function limpiar(): void
    {
        $this->reset(['q', 'categoria', 'zona']);
        $this->resetPage();
    }

    public function render()
    {
        $categorias = Categoria::activo()->orderBy('nombre')->get();
        $zonas      = Zona::orderBy('nombre')->get();

        $negocios = Negocio::activo()
            ->with(['categoria', 'zona'])
            ->when(trim($this->q), function ($query, $q) {
                $query->where(function ($sub) use ($q) {
                    $sub->where('nombre', 'like', "%{$q}%")
                        ->orWhere('descripcion', 'like', "%{$q}%")
                        ->orWhere('direccion', 'like', "%{$q}%");
                });
            })
            ->when($this->categoria, fn ($q) => $q->whereHas('categoria', fn ($c) => $c->where('slug', $this->categoria)))
            ->when($this->zona, fn ($q) => $q->whereHas('zona', fn ($z) => $z->where('slug', $this->zona)))
            ->orderByDesc('featured')
            ->orderBy('nombre')
            ->paginate(12);

        return view('livewire.negocios-index', compact('negocios', 'categorias', 'zonas'));
    }
}
