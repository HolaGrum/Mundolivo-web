<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MercadoLibreProduct;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Vanilo\Category\Models\Taxonomy;
use Vanilo\Foundation\Models\Product;
use Vanilo\Order\Contracts\OrderFactory as OrderFactoryContract;

class StorefrontApiController extends Controller
{
    /**
     * Categorías de respaldo usadas cuando aún no existen taxonomías configuradas
     * en el backoffice de Vanilo.
     */
    protected array $fallbackCategories = [
        [
            'name' => 'Tipo de pintura',
            'subcategories' => ['Interior', 'Exterior', 'Industrial', 'Automotriz'],
        ],
        [
            'name' => 'Acabados y consumibles',
            'subcategories' => ['Barnices', 'Lacas', 'Esmaltes', 'Selladores', 'Imprimantes/Primers', 'Pigmentos', 'Diluyentes', 'Aditivos'],
        ],
        [
            'name' => 'Accesorios y ferretería',
            'subcategories' => ['Brochas', 'Rodillos', 'Pistolas y boquillas', 'Compresores', 'Lijas y esponjas', 'Espátulas y masillas', 'Cintas y plásticos de enmascarar', 'Equipos de protección (EPP)'],
        ],
        [
            'name' => 'Servicios especializados',
            'subcategories' => ['Aplicación profesional', 'Reparación y masillado', 'Tratamiento anticorrosivo', 'Decapado y preparación de superficies', 'Pintado automotriz', 'Asesoría técnica y selección de color'],
        ],
        [
            'name' => 'Por uso',
            'subcategories' => ['Madera', 'Metal', 'Hormigón', 'Azulejo', 'Automoción', 'Decorativo (texturas y efectos)'],
        ],
    ];

    public function index(Request $request): JsonResponse
    {
        $search = trim((string) $request->get('q'));

        $query = Product::query()
            ->actives()
            ->with(['media', 'propertyValues.property', 'taxons']);

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('excerpt', 'like', "%{$search}%");
            });
        }

        $products = $query->orderBy('priority')->get();
        $mlProducts = MercadoLibreProduct::where('status', 'active')->get()->keyBy('product_id');

        return response()->json([
            'products' => $products->map(fn (Product $product) => $this->serializeProduct($product, $mlProducts->get($product->id))),
            'categories' => $this->categories(),
        ]);
    }

    public function show(string $slug): JsonResponse
    {
        $product = Product::query()
            ->with(['media', 'propertyValues.property', 'taxons'])
            ->where('slug', $slug)
            ->orWhere('sku', $slug)
            ->orWhere('id', $slug)
            ->first();

        if (!$product) {
            return response()->json(['message' => 'Producto no encontrado'], 404);
        }

        $mlProduct = MercadoLibreProduct::where('product_id', $product->id)
            ->where('status', 'active')
            ->first();

        return response()->json(['product' => $this->serializeProduct($product, $mlProduct)]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'items' => ['required', 'array', 'min:1'],
            'items.*.id' => ['required', 'integer'],
            'items.*.quantity' => ['required', 'integer', 'min:1', 'max:99'],
            'customer.name' => ['nullable', 'string', 'max:255'],
            'customer.phone' => ['nullable', 'string', 'max:30'],
            'customer.email' => ['nullable', 'email', 'max:255'],
            'customer.message' => ['nullable', 'string', 'max:2000'],
        ]);

        $items = [];
        foreach ($validated['items'] as $line) {
            $product = Product::query()->actives()->find($line['id']);
            if (!$product) {
                continue;
            }

            $items[] = [
                'product' => $product,
                'quantity' => (int) $line['quantity'],
            ];
        }

        if (empty($items)) {
            return response()->json(['message' => 'El carrito no contiene productos válidos.'], 422);
        }

        $customer = $validated['customer'] ?? [];
        $fullName = trim($customer['name'] ?? '') ?: 'Cliente tienda web';
        $phone = trim($customer['phone'] ?? '') ?: '—';
        $message = trim($customer['message'] ?? '') ?: '—';

        $data = [
            'notes' => "Pedido enviado desde la tienda web\nCliente: {$fullName}\nTeléfono: {$phone}\nMensaje: {$message}",
            'language' => app()->getLocale(),
            'currency' => 'USD',
            'billpayer' => [
                'firstname' => $fullName,
                'lastname' => 'Tienda Web',
                'email' => $customer['email'] ?? null,
                'phone' => $customer['phone'] ?? null,
                'address' => [
                    'name' => $fullName,
                    'country_id' => 'VE',
                    'address' => 'Pedido vía WhatsApp',
                ],
            ],
        ];

        try {
            $order = app(OrderFactoryContract::class)->createFromDataArray($data, $items);
        } catch (\Throwable $e) {
            report($e);

            return response()->json(['message' => 'No se pudo registrar el pedido. Inténtalo de nuevo.'], 500);
        }

        return response()->json([
            'message' => 'Pedido registrado correctamente',
            'order' => [
                'id' => $order->id,
                'number' => $order->getNumber(),
                'total' => round($order->total(), 2),
            ],
        ], 201);
    }

    /**
     * Construye las categorías a partir de las taxonomías de Vanilo.
     * Si no hay taxonomías, devuelve un listado de respaldo.
     */
    protected function categories(): array
    {
        $taxonomies = Taxonomy::query()->with('taxons')->get();

        if ($taxonomies->isEmpty()) {
            return $this->fallbackCategories;
        }

        return $taxonomies->map(function (Taxonomy $taxonomy) {
            return [
                'name' => $taxonomy->name,
                'subcategories' => $taxonomy->taxons->pluck('name')->all(),
            ];
        })->values()->all();
    }

    protected function serializeProduct(Product $product, ?MercadoLibreProduct $mlProduct = null): array
    {
        $properties = [];
        foreach ($product->propertyValues as $propertyValue) {
            $slug = $propertyValue->property?->slug ?? strtolower($propertyValue->property?->name ?? '');
            if ($slug === null || $slug === '') {
                continue;
            }

            $properties[$slug] = $propertyValue->value ?? $propertyValue->title;
        }

        return [
            'id' => $product->id,
            'title' => $product->title(),
            'name' => $product->name,
            'slug' => $product->slug,
            'sku' => $product->sku,
            'description' => $product->description,
            'excerpt' => $product->excerpt,
            'price' => (float) $product->price,
            'originalPrice' => $product->original_price !== null ? (float) $product->original_price : null,
            'currencyFormat' => '$',
            'currencyId' => 'USD',
            'stock' => (float) $product->stock,
            'inStock' => $product->isOnStock(),
            'image' => $this->mediaUrl($product->getImageUrl()),
            'images' => $product->getImageUrls()->map(fn ($url) => $this->mediaUrl($url))->values()->all(),
            'availableSizes' => $this->extractSizes($properties),
            'style' => $properties['style'] ?? $properties['estilo'] ?? $properties['presentacion'] ?? null,
            'categories' => $product->taxons->pluck('name')->values()->all(),
            'mlPermalink' => $mlProduct?->permalink,
            'mlStatus' => $mlProduct?->status,
        ];
    }

    /**
     * Devuelve la ruta relativa de la imagen para que funcione en cualquier
     * dominio/puerto sin depender de APP_URL.
     */
    protected function mediaUrl(?string $url): ?string
    {
        if ($url === null || $url === '') {
            return null;
        }

        $path = parse_url($url, PHP_URL_PATH);

        return is_string($path) ? $path : $url;
    }

    protected function extractSizes(array $properties): array
    {
        $value = $properties['size'] ?? $properties['talla'] ?? $properties['sizes'] ?? null;
        if ($value === null || $value === '') {
            return [];
        }

        return collect(explode(',', $value))
            ->map(fn (string $size) => trim($size))
            ->filter(fn (string $size) => $size !== '')
            ->values()
            ->all();
    }
}
