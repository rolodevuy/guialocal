<?php

use App\Http\Controllers\ClaimController;
use App\Http\Controllers\ArticuloController;
use App\Http\Controllers\NewsletterController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\EventoController;
use App\Http\Controllers\FeedController;
use App\Http\Controllers\ContactoController;
use App\Http\Controllers\GuiaController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MapaController;
use App\Http\Controllers\NegocioController;
use App\Http\Controllers\PanelController;
use App\Http\Controllers\ResenaController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\SectorController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\ZonaController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/mapa', [MapaController::class, 'index'])->name('mapa.index');

Route::get('/negocios', [NegocioController::class, 'index'])->name('negocios.index');
Route::get('/negocios/{slug}', [NegocioController::class, 'show'])->name('negocios.show');
Route::post('/negocios/{slug}/resenas', [ResenaController::class, 'store'])->name('negocios.resenas.store')
    ->middleware('throttle:5,1'); // máx 5 reseñas por minuto por IP
Route::get('/negocios/{slug}/reclamar', [ClaimController::class, 'create'])->name('negocios.claim');
Route::post('/negocios/{slug}/reclamar', [ClaimController::class, 'store'])->name('negocios.claim.store')
    ->middleware('throttle:3,10'); // máx 3 claims cada 10 min por IP

Route::get('/categorias', [CategoriaController::class, 'index'])->name('categorias.index');
Route::get('/categorias/{categoria}', [CategoriaController::class, 'show'])->name('categorias.show');

Route::get('/sectores/{sector}', [SectorController::class, 'show'])->name('sectores.show');

Route::get('/zonas/{zona}', [ZonaController::class, 'show'])->name('zonas.show');

Route::get('/articulos', [ArticuloController::class, 'index'])->name('articulos.index');
Route::get('/articulos/{articulo}', [ArticuloController::class, 'show'])->name('articulos.show');

Route::get('/guias', [GuiaController::class, 'index'])->name('guias.index');
Route::get('/guias/{guia}', [GuiaController::class, 'show'])->name('guias.show');

Route::get('/eventos', [EventoController::class, 'index'])->name('eventos.index');
Route::get('/eventos/{evento}', [EventoController::class, 'show'])->name('eventos.show');

Route::get('/contacto', [ContactoController::class, 'show'])->name('contacto.show');
Route::post('/contacto', [ContactoController::class, 'store'])->name('contacto.store');

Route::get('/quienes-somos', [PageController::class, 'about'])->name('quienes-somos');

Route::post('/newsletter/suscribir', [NewsletterController::class, 'subscribe'])->name('newsletter.subscribe')->middleware('throttle:3,1');
Route::get('/newsletter/baja/{token}', [NewsletterController::class, 'baja'])->name('newsletter.baja');

Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');
Route::get('/feed', [FeedController::class, 'index'])->name('feed');

// ── Panel de autogestión para dueños de negocios ─────────────────────────────
Route::prefix('panel')->name('panel.')->group(function () {

    // Login GET: sin middleware guest (el controller maneja el redirect si ya está autenticado)
    // Login POST: sí tiene guest para evitar que alguien autenticado envíe el form
    Route::get('/login',  [PanelController::class, 'showLogin'])->name('login');
    Route::post('/login', [PanelController::class, 'login'])->name('login.post')->middleware('guest');

    Route::post('/logout', [PanelController::class, 'logout'])->name('logout');

    // Panel — requiere auth (middleware propio para redirigir a panel.login y no a 'login')
    Route::middleware(\App\Http\Middleware\PanelAuthenticate::class)->group(function () {
        Route::get('/',        [PanelController::class, 'index'])->name('index');
        Route::get('/editar',  [PanelController::class, 'edit'])->name('edit');
        Route::put('/editar',  [PanelController::class, 'update'])->name('update');
    });
});
