<?php

namespace App\Services\MercadoLibre;

use App\Models\MercadoLibreProduct;
use App\Models\MercadoLibreAccount;
use Vanilo\Product\Models\Product;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Exception;

class MercadoLibreProductService
{
    protected MercadoLibreClient $client;

    public function __construct(MercadoLibreClient $client)
    {
        $this->client = $client;
    }

    public function publish(Product $product, array $options): MercadoLibreProduct
    {
        $categoryId = $options['category_id'] ?? null;
        if (empty($categoryId)) {
            // Predict category if not provided
            $predictions = $this->predictCategory($product->name);
            $categoryId = !empty($predictions) ? $predictions[0]['id'] : 'MLV1055';
        }

        $price = (float) ($options['price'] ?? $product->price ?? 10.00);
        $quantity = (int) ($options['available_quantity'] ?? 5);
        $condition = $options['condition'] ?? 'new';
        $listingType = $options['listing_type_id'] ?? 'gold_special';
        $currencyId = $options['currency_id'] ?? 'VES';

        // Prepare pictures array
        $pictures = [];
        if (method_exists($product, 'getMedia') && $product->getMedia()->count() > 0) {
            foreach ($product->getMedia() as $media) {
                $pictures[] = ['source' => $media->getUrl()];
            }
        } else {
            // Default placeholder picture for testing if none exist
            $pictures[] = ['source' => 'https://http2.mlstatic.com/D_NQ_NP_604245-MLV70000000000_012024-O.webp'];
        }

        $itemPayload = [
            'title' => substr($product->name, 0, 60),
            'category_id' => $categoryId,
            'price' => $price,
            'currency_id' => $currencyId,
            'available_quantity' => $quantity,
            'buying_mode' => 'buy_it_now',
            'condition' => $condition,
            'listing_type_id' => $listingType,
            'pictures' => $pictures,
            'attributes' => [
                [
                    'id' => 'ITEM_CONDITION',
                    'value_name' => $condition === 'new' ? 'Nuevo' : 'Usado',
                ],
            ],
        ];

        try {
            $response = $this->client->post('items', $itemPayload);

            $mlProduct = MercadoLibreProduct::create([
                'product_id' => $product->id,
                'ml_item_id' => $response['id'],
                'ml_category_id' => $categoryId,
                'title' => $response['title'] ?? $product->name,
                'price' => $price,
                'available_quantity' => $quantity,
                'status' => $response['status'] ?? 'active',
                'permalink' => $response['permalink'] ?? null,
                'listing_type_id' => $listingType,
                'condition' => $condition,
                'last_synced_at' => Carbon::now(),
                'sync_error' => null,
            ]);

            return $mlProduct;
        } catch (Exception $e) {
            Log::error("Error publicando en Mercado Libre (Producto #{$product->id}): " . $e->getMessage());
            throw $e;
        }
    }

    public function updateStockAndPrice(MercadoLibreProduct $mlProduct, ?float $newPrice = null, ?int $newQuantity = null): MercadoLibreProduct
    {
        $payload = [];

        if ($newPrice !== null) {
            $payload['price'] = $newPrice;
        }
        if ($newQuantity !== null) {
            $payload['available_quantity'] = $newQuantity;
        }

        if (empty($payload)) {
            return $mlProduct;
        }

        try {
            $response = $this->client->put("items/{$mlProduct->ml_item_id}", $payload);

            $mlProduct->update([
                'price' => $newPrice ?? $mlProduct->price,
                'available_quantity' => $newQuantity ?? $mlProduct->available_quantity,
                'status' => $response['status'] ?? $mlProduct->status,
                'last_synced_at' => Carbon::now(),
                'sync_error' => null,
            ]);

            return $mlProduct;
        } catch (Exception $e) {
            $mlProduct->update(['sync_error' => $e->getMessage()]);
            throw $e;
        }
    }

    public function changeStatus(MercadoLibreProduct $mlProduct, string $status): MercadoLibreProduct
    {
        // Allowed statuses: 'active', 'paused', 'closed'
        try {
            $response = $this->client->put("items/{$mlProduct->ml_item_id}", [
                'status' => $status,
            ]);

            $mlProduct->update([
                'status' => $response['status'] ?? $status,
                'last_synced_at' => Carbon::now(),
                'sync_error' => null,
            ]);

            return $mlProduct;
        } catch (Exception $e) {
            $mlProduct->update(['sync_error' => $e->getMessage()]);
            throw $e;
        }
    }

    public function predictCategory(string $title): array
    {
        $siteId = config('services.mercadolibre.site_id', 'MLV');
        try {
            $res = $this->client->get("sites/{$siteId}/domain_discovery/search", [
                'limit' => 5,
                'q' => $title,
            ]);

            return $res;
        } catch (Exception $e) {
            Log::warning("No se pudieron predecir categorías para: {$title} ({$e->getMessage()})");
            return [];
        }
    }

    public function importFromMercadoLibre(): int
    {
        $account = $this->client->getAccount();
        if (!$account || !$account->ml_user_id) {
            throw new Exception("Cuenta no conectada o ID de vendedor desconocido.");
        }

        $itemsResponse = $this->client->get("users/{$account->ml_user_id}/items/search", [
            'status' => 'active,paused',
        ]);

        $itemIds = $itemsResponse['results'] ?? [];
        $importedCount = 0;

        foreach ($itemIds as $itemId) {
            if (MercadoLibreProduct::where('ml_item_id', $itemId)->exists()) {
                continue;
            }

            try {
                $itemData = $this->client->get("items/{$itemId}");

                // Create a Vanilo Product locally if not existing
                $product = Product::create([
                    'name' => $itemData['title'] ?? 'Producto ML ' . $itemId,
                    'sku' => 'ML-' . strtoupper(Str::random(6)),
                    'price' => $itemData['price'] ?? 0,
                    'state' => 'active',
                ]);

                MercadoLibreProduct::create([
                    'product_id' => $product->id,
                    'ml_item_id' => $itemId,
                    'ml_category_id' => $itemData['category_id'] ?? null,
                    'title' => $itemData['title'] ?? $product->name,
                    'price' => $itemData['price'] ?? 0,
                    'available_quantity' => $itemData['available_quantity'] ?? 1,
                    'status' => $itemData['status'] ?? 'active',
                    'permalink' => $itemData['permalink'] ?? null,
                    'listing_type_id' => $itemData['listing_type_id'] ?? 'gold_special',
                    'condition' => $itemData['condition'] ?? 'new',
                    'last_synced_at' => Carbon::now(),
                ]);

                $importedCount++;
            } catch (Exception $e) {
                Log::error("Error importando ítem ML {$itemId}: " . $e->getMessage());
            }
        }

        return $importedCount;
    }
}
