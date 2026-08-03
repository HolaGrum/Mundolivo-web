<?php

namespace App\Services\MercadoLibre;

use App\Models\MercadoLibreOrder;
use App\Models\MercadoLibreProduct;
use Vanilo\Order\Models\Order;
use Vanilo\Order\Models\OrderStatus;
use Vanilo\Channel\Models\Channel;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Exception;

class MercadoLibreOrderService
{
    protected MercadoLibreClient $client;

    public function __construct(MercadoLibreClient $client)
    {
        $this->client = $client;
    }

    public function syncOrders(int $limit = 30): int
    {
        $account = $this->client->getAccount();
        if (!$account || !$account->ml_user_id) {
            throw new Exception("Cuenta no conectada o ID de vendedor desconocido.");
        }

        $response = $this->client->get("orders/search", [
            'seller' => $account->ml_user_id,
            'sort' => 'date_desc',
            'limit' => $limit,
        ]);

        $ordersData = $response['results'] ?? [];
        $syncedCount = 0;

        foreach ($ordersData as $mlOrderData) {
            try {
                $this->importOrder($mlOrderData);
                $syncedCount++;
            } catch (Exception $e) {
                Log::error("Error sincronizando pedido ML #{$mlOrderData['id']}: " . $e->getMessage());
            }
        }

        return $syncedCount;
    }

    public function importOrder(array $mlOrderData): MercadoLibreOrder
    {
        $mlOrderId = (string) $mlOrderData['id'];

        $existing = MercadoLibreOrder::where('ml_order_id', $mlOrderId)->first();
        if ($existing) {
            // Update status if changed
            $existing->update([
                'ml_status' => $mlOrderData['status'] ?? $existing->ml_status,
                'raw_data' => $mlOrderData,
                'synced_at' => Carbon::now(),
            ]);

            // Update Vanilo order status if needed
            if ($existing->order) {
                $existing->order->update([
                    'status' => $this->mapOrderStatus($mlOrderData['status'] ?? 'paid'),
                ]);
            }

            return $existing;
        }

        return DB::transaction(function () use ($mlOrderData, $mlOrderId) {
            // Get or create channel "Mercado Libre"
            $channel = Channel::firstOrCreate(
                ['slug' => 'mercadolibre'],
                ['name' => 'Mercado Libre']
            );

            $buyer = $mlOrderData['buyer'] ?? [];
            $buyerNickname = $buyer['nickname'] ?? 'Comprador ML';

            // Create Vanilo Order
            $order = Order::create([
                'number' => 'ML-' . $mlOrderId,
                'status' => $this->mapOrderStatus($mlOrderData['status'] ?? 'paid'),
                'channel_id' => $channel->id,
                'notes' => 'Pedido importado desde Mercado Libre. Comprador: ' . $buyerNickname,
            ]);

            // Process order items & deduct local inventory
            $orderItems = $mlOrderData['order_items'] ?? [];
            foreach ($orderItems as $itemData) {
                $itemId = $itemData['item']['id'] ?? null;
                $quantity = (int) ($itemData['quantity'] ?? 1);
                $unitPrice = (float) ($itemData['unit_price'] ?? 0);
                $title = $itemData['item']['title'] ?? 'Producto ML';

                // Check if item exists in mercadolibre_products
                $mlProduct = MercadoLibreProduct::where('ml_item_id', $itemId)->first();

                if ($mlProduct && $mlProduct->product) {
                    $localProduct = $mlProduct->product;
                    // Deduct inventory locally
                    if ($localProduct->stock !== null) {
                        $localProduct->decrement('stock', $quantity);
                    }
                    if ($mlProduct->available_quantity >= $quantity) {
                        $mlProduct->decrement('available_quantity', $quantity);
                    }
                }

                // Add item to Vanilo order
                $order->items()->create([
                    'product_type' => $mlProduct ? get_class($mlProduct->product) : 'Vanilo\Product\Models\Product',
                    'product_id' => $mlProduct ? $mlProduct->product_id : 1,
                    'name' => $title,
                    'price' => $unitPrice,
                    'quantity' => $quantity,
                ]);
            }

            $mlOrder = MercadoLibreOrder::create([
                'order_id' => $order->id,
                'ml_order_id' => $mlOrderId,
                'ml_buyer_id' => (string) ($buyer['id'] ?? ''),
                'ml_buyer_nickname' => $buyerNickname,
                'ml_status' => $mlOrderData['status'] ?? 'paid',
                'ml_shipping_id' => (string) ($mlOrderData['shipping']['id'] ?? ''),
                'ml_payment_id' => (string) ($mlOrderData['payments'][0]['id'] ?? ''),
                'raw_data' => $mlOrderData,
                'synced_at' => Carbon::now(),
            ]);

            return $mlOrder;
        });
    }

    protected function mapOrderStatus(string $mlStatus): string
    {
        return match ($mlStatus) {
            'paid', 'confirmed' => OrderStatus::PENDING,
            'payment_required', 'payment_in_process' => OrderStatus::PENDING,
            'cancelled' => OrderStatus::CANCELLED,
            'completed' => OrderStatus::COMPLETED,
            default => OrderStatus::PENDING,
        };
    }
}
