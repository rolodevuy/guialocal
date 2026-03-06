<?php

use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\ContactoController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MapaController;
use App\Http\Controllers\NegocioController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\ZonaController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/mapa', [MapaController::class, 'index'])->name('mapa.index');

Route::get('/negocios', [NegocioController::class, 'index'])->name('negocios.index');
Route::get('/negocios/{slug}', [NegocioController::class, 'show'])->name('negocios.show');

Route::get('/categorias', [CategoriaController::class, 'index'])->name('categorias.index');
Route::get('/categorias/{categoria}', [CategoriaController::class, 'show'])->name('categorias.show');

Route::get('/zonas/{zona}', [ZonaController::class, 'show'])->name('zonas.show');

Route::get('/contacto', [ContactoController::class, 'show'])->name('contacto.show');
Route::post('/contacto', [ContactoController::class, 'store'])->name('contacto.store');

Route::get('/quienes-somos', [PageController::class, 'about'])->name('quienes-somos');

Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');
