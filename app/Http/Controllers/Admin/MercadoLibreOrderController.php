<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MercadoLibreOrder;
use App\Services\MercadoLibre\MercadoLibreOrderService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Exception;

class MercadoLibreOrderController extends Controller
{
    public function index(): View
    {
        $orders = MercadoLibreOrder::with('order')
            ->orderByDesc('synced_at')
            ->paginate(15);

        return view('admin.mercadolibre.orders.index', compact('orders'));
    }

    public function show(MercadoLibreOrder $order): View
    {
        $order->load('order.items');
        return view('admin.mercadolibre.orders.show', compact('order'));
    }

    public function sync(MercadoLibreOrderService $service): RedirectResponse
    {
        try {
            $count = $service->syncOrders(50);

            return redirect()
                ->route('admin.mercadolibre.orders.index')
                ->with('success', "Sincronización finalizada: Se procesaron {$count} pedidos recientes de Mercado Libre.");
        } catch (Exception $e) {
            return redirect()
                ->route('admin.mercadolibre.orders.index')
                ->with('error', "Error sincronizando pedidos: " . $e->getMessage());
        }
    }
}
