<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class StorefrontController extends Controller
{
    /**
     * Muestra la tienda (SPA en React). El catálogo y el detalle de producto
     * consumen los datos de Vanilo a través de la API pública del storefront.
     */
    public function index(): View
    {
        return view('storefront.app');
    }

    public function show(string $slug): View
    {
        return view('storefront.app');
    }
}
