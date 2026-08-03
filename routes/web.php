<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\StorefrontController;
use App\Http\Controllers\Admin\MercadoLibreDashboardController;
use App\Http\Controllers\Admin\MercadoLibreProductController;
use App\Http\Controllers\Admin\MercadoLibreOrderController;
use App\Http\Controllers\Admin\MercadoLibreConfigController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Tienda Web (Storefront)
|--------------------------------------------------------------------------
*/
Route::get('/', [StorefrontController::class, 'index'])->name('storefront.index');
Route::get('/catalog', [StorefrontController::class, 'index'])->name('storefront.catalog');
Route::get('/product/{slug}', [StorefrontController::class, 'show'])->name('storefront.show');

/*
|--------------------------------------------------------------------------
| Administración (Dashboard & Profile)
|--------------------------------------------------------------------------
*/
Route::get('/dashboard', DashboardController::class)
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

/*
|--------------------------------------------------------------------------
| Backoffice Mercado Libre (Admin)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])->prefix('admin/mercadolibre')->name('admin.mercadolibre.')->group(function () {
    Route::get('/', [MercadoLibreDashboardController::class, 'index'])->name('index');
    Route::post('/sync', [MercadoLibreDashboardController::class, 'syncAll'])->name('sync');

    // Publicaciones e Inventario
    Route::get('/products', [MercadoLibreProductController::class, 'index'])->name('products.index');
    Route::post('/products/import', [MercadoLibreProductController::class, 'import'])->name('products.import');
    Route::get('/products/template', [MercadoLibreProductController::class, 'downloadTemplate'])->name('products.template');
    Route::post('/products/bulk-upload', [MercadoLibreProductController::class, 'bulkUpload'])->name('products.bulk-upload');
    Route::post('/products/{product}/publish', [MercadoLibreProductController::class, 'publish'])->name('products.publish');
    Route::put('/products/{mlProduct}/status', [MercadoLibreProductController::class, 'updateStatus'])->name('products.status');
    Route::put('/products/{mlProduct}/stock', [MercadoLibreProductController::class, 'updateStock'])->name('products.stock');

    // Pedidos
    Route::get('/orders', [MercadoLibreOrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [MercadoLibreOrderController::class, 'show'])->name('orders.show');
    Route::post('/orders/sync', [MercadoLibreOrderController::class, 'sync'])->name('orders.sync');

    // Configuración y OAuth 2.0
    Route::get('/config', [MercadoLibreConfigController::class, 'index'])->name('config');
    Route::get('/redirect', [MercadoLibreConfigController::class, 'redirect'])->name('redirect');
    Route::get('/callback', [MercadoLibreConfigController::class, 'callback'])->name('callback');
    Route::delete('/disconnect/{account}', [MercadoLibreConfigController::class, 'disconnect'])->name('disconnect');
});

require __DIR__.'/auth.php';
