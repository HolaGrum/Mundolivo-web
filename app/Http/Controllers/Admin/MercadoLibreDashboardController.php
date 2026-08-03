<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MercadoLibreAccount;
use App\Models\MercadoLibreProduct;
use App\Models\MercadoLibreOrder;
use App\Services\MercadoLibre\MercadoLibreProductService;
use App\Services\MercadoLibre\MercadoLibreOrderService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Exception;

class MercadoLibreDashboardController extends Controller
{
    public function index(): View
    {
        $account = MercadoLibreAccount::defaultAccount();
        $isConnected = $account && $account->isConnected();

        $stats = [
            'total_products' => MercadoLibreProduct::count(),
            'active_products' => MercadoLibreProduct::where('status', 'active')->count(),
            'paused_products' => MercadoLibreProduct::where('status', 'paused')->count(),
            'total_orders' => MercadoLibreOrder::count(),
        ];

        $recentProducts = MercadoLibreProduct::with('product')
            ->orderByDesc('last_synced_at')
            ->limit(5)
            ->get();

        $recentOrders = MercadoLibreOrder::with('order')
            ->orderByDesc('synced_at')
            ->limit(5)
            ->get();

        return view('admin.mercadolibre.dashboard', compact(
            'account',
            'isConnected',
            'stats',
            'recentProducts',
            'recentOrders'
        ));
    }

    public function syncAll(
        MercadoLibreProductService $productService,
        MercadoLibreOrderService $orderService
    ): RedirectResponse {
        try {
            $importedProducts = $productService->importFromMercadoLibre();
            $syncedOrders = $orderService->syncOrders(30);

            return redirect()
                ->route('admin.mercadolibre.index')
                ->with('success', "Sincronización exitosa: {$importedProducts} publicaciones importadas y {$syncedOrders} pedidos sincronizados.");
        } catch (Exception $e) {
            return redirect()
                ->route('admin.mercadolibre.index')
                ->with('error', "Error en la sincronización: " . $e->getMessage());
        }
    }
}
