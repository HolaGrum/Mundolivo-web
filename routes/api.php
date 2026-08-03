<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MercadoLibreWebhookController;

Route::post('/mercadolibre/webhook', [MercadoLibreWebhookController::class, 'handle'])
    ->name('api.mercadolibre.webhook');
