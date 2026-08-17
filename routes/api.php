<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\StorefrontApiController;
use App\Http\Controllers\MercadoLibreWebhookController;

Route::prefix('storefront')->group(function () {
    Route::get('/products', [StorefrontApiController::class, 'index'])->name('api.storefront.products');
    Route::get('/products/{slug}', [StorefrontApiController::class, 'show'])->name('api.storefront.product');
    Route::post('/orders', [StorefrontApiController::class, 'store'])
        ->middleware('throttle:20,1')
        ->name('api.storefront.orders');
});

Route::post('/mercadolibre/webhook', [MercadoLibreWebhookController::class, 'handle'])
    ->name('api.mercadolibre.webhook');
