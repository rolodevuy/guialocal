<?php

namespace App\Livewire;

use App\Models\Categoria;
use App\Models\Ficha;
use App\Models\Lugar;
use App\Models\Zona;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;
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

    #[Url(as: 'abiertos')]
    public bool $soloAbiertos = false;

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

    public function updatingSoloAbiertos(): void
    {
        $this->resetPage();
    }

    public function updatedZona(string $value): void
    {
        $this->dispatch('guardar-zona', slug: $value);
    }

    public function limpiar(): void
    {
        $this->reset(['q', 'categoria', 'zona', 'soloAbiertos']);
        $this->dispatch('guardar-zona', slug: '');
        $this->resetPage();
    }

    public function render()
    {
        // Cache de 1h: categorías y zonas cambian raramente
        $categorias = Cache::remember('negocios_categorias_nav', 3600, fn () =>
            Categoria::activo()
                ->whereNull('parent_id')
                ->with(['children' => fn ($q) => $q->activo()->orderBy('nombre')])
                ->orderBy('nombre')
                ->get()
        );

        $zonas = Cache::remember('negocios_zonas_nav', 3600, fn () =>
            Zona::orderBy('nombre')->get()
        );

        // Conteo de negocios activos por categoría (cache 1h)
        $conteosPorCat = Cache::remember('negocios_conteos_cat', 3600, fn () =>
            Lugar::where('activo', true)
                ->whereHas('fichas', fn ($q) => $q->activo())
                ->selectRaw('categoria_id, COUNT(*) as total')
                ->groupBy('categoria_id')
                ->pluck('total', 'categoria_id')
        );

        $query = Ficha::activo()
            ->whereHas('lugar', fn ($q) => $q->where('activo', true))
            // categoria.parent.parent cubre hasta 3 niveles para el accessor raiz
            ->with(['lugar.categoria.parent.parent', 'lugar.zona'])
            ->when(trim($this->q), function ($query, $q) {
                $query->where(function ($sub) use ($q) {
                    $sub->where('descripcion', 'like', "%{$q}%")
                        ->orWhereHas('lugar', fn ($l) => $l
                            ->where('nombre', 'like', "%{$q}%")
                            ->orWhere('direccion', 'like', "%{$q}%")
                            ->orWhereHas('categoria', fn ($c) => $c->where('nombre', 'like', "%{$q}%"))
                        );
                });
            })
            ->when($this->categoria, fn ($q) => $q->whereHas('lugar', fn ($l) => $l
                ->whereHas('categoria', fn ($c) => $c
                    ->where('slug', $this->categoria)
                    ->orWhereHas('parent', fn ($p) => $p->where('slug', $this->categoria))
                )
            ))
            ->when($this->zona, fn ($q) => $q->whereHas('lugar', fn ($l) => $l
                ->whereHas('zona', fn ($z) => $z->where('slug', $this->zona))
            ))
            ->orderByDesc('featured_score');

        if ($this->soloAbiertos) {
            // Filtramos en PHP porque la lógica de horarios es compleja
            // (franjas, días, horarios especiales). Dataset local = viable.
            $todos    = $query->whereNotNull('horarios')->get();
            $abiertos = $todos->filter(fn ($f) => $f->isAbiertoAhora())->values();

            $perPage  = 12;
            $page     = $this->getPage();
            $fichas   = new LengthAwarePaginator(
                $abiertos->slice(($page - 1) * $perPage, $perPage)->values(),
                $abiertos->count(),
                $perPage,
                $page,
                ['path' => request()->url(), 'query' => request()->query()]
            );
        } else {
            $fichas = $query->paginate(12);
        }

        return view('livewire.negocios-index', compact('fichas', 'categorias', 'zonas', 'conteosPorCat'));
    }
}
