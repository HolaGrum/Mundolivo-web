<?php

namespace App\Http\Controllers;

use App\Services\MercadoLibre\MercadoLibreClient;
use App\Services\MercadoLibre\MercadoLibreOrderService;
use App\Models\MercadoLibreProduct;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Exception;

class MercadoLibreWebhookController extends Controller
{
    public function handle(Request $request, MercadoLibreClient $client, MercadoLibreOrderService $orderService): JsonResponse
    {
        $topic = $request->input('topic');
        $resource = $request->input('resource');

        Log::info("MercadoLibre Webhook Recibido", [
            'topic' => $topic,
            'resource' => $resource,
            'user_id' => $request->input('user_id'),
        ]);

        try {
            if (in_array($topic, ['orders', 'orders_v2']) && $resource) {
                // $resource format: /orders/12345678
                $orderData = $client->get(ltrim($resource, '/'));
                if (!empty($orderData)) {
                    $orderService->importOrder($orderData);
                }
            } elseif ($topic === 'items' && $resource) {
                // $resource format: /items/MLV123456
                $itemData = $client->get(ltrim($resource, '/'));
                $itemId = $itemData['id'] ?? null;
                if ($itemId) {
                    $mlProduct = MercadoLibreProduct::where('ml_item_id', $itemId)->first();
                    if ($mlProduct) {
                        $mlProduct->update([
                            'price' => $itemData['price'] ?? $mlProduct->price,
                            'available_quantity' => $itemData['available_quantity'] ?? $mlProduct->available_quantity,
                            'status' => $itemData['status'] ?? $mlProduct->status,
                            'last_synced_at' => Carbon::now(),
                        ]);
                    }
                }
            }
        } catch (Exception $e) {
            Log::error("Error procesando webhook ML ({$topic} - {$resource}): " . $e->getMessage());
        }

        return response()->json(['status' => 'ok']);
    }
}
