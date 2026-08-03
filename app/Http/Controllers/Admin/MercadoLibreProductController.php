<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MercadoLibreProduct;
use App\Services\MercadoLibre\MercadoLibreProductService;
use Vanilo\Product\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Support\Facades\Log;
use Exception;

class MercadoLibreProductController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->get('status');

        $query = Product::query()->with('media');

        if ($status === 'published') {
            $query->whereHas('mercadolibreProduct');
        } elseif ($status === 'unpublished') {
            $query->whereDoesntHave('mercadolibreProduct');
        }

        $products = $query->paginate(15);
        $mlProducts = MercadoLibreProduct::all()->keyBy('product_id');

        return view('admin.mercadolibre.products.index', compact('products', 'mlProducts', 'status'));
    }

    public function publish(Request $request, Product $product, MercadoLibreProductService $service): RedirectResponse
    {
        $validated = $request->validate([
            'category_id' => 'nullable|string',
            'price' => 'nullable|numeric|min:0.01',
            'available_quantity' => 'nullable|integer|min:1',
            'condition' => 'required|in:new,used',
            'listing_type_id' => 'required|in:gold_special,gold_pro,free',
        ]);

        try {
            $service->publish($product, $validated);

            return redirect()
                ->route('admin.mercadolibre.products.index')
                ->with('success', "¡Producto '{$product->name}' publicado en Mercado Libre con éxito!");
        } catch (Exception $e) {
            return redirect()
                ->route('admin.mercadolibre.products.index')
                ->with('error', "Error publicando producto en Mercado Libre: " . $e->getMessage());
        }
    }

    public function updateStatus(Request $request, MercadoLibreProduct $mlProduct, MercadoLibreProductService $service): RedirectResponse
    {
        $validated = $request->validate([
            'status' => 'required|in:active,paused,closed',
        ]);

        try {
            $service->changeStatus($mlProduct, $validated['status']);

            return redirect()
                ->route('admin.mercadolibre.products.index')
                ->with('success', "Estado de publicación modificado a '{$validated['status']}'.");
        } catch (Exception $e) {
            return redirect()
                ->route('admin.mercadolibre.products.index')
                ->with('error', "Error modificando estado: " . $e->getMessage());
        }
    }

    public function updateStock(Request $request, MercadoLibreProduct $mlProduct, MercadoLibreProductService $service): RedirectResponse
    {
        $validated = $request->validate([
            'price' => 'nullable|numeric|min:0.01',
            'available_quantity' => 'nullable|integer|min:0',
        ]);

        try {
            $service->updateStockAndPrice(
                $mlProduct,
                $validated['price'] ?? null,
                $validated['available_quantity'] ?? null
            );

            return redirect()
                ->route('admin.mercadolibre.products.index')
                ->with('success', "Inventario y precio actualizados en Mercado Libre.");
        } catch (Exception $e) {
            return redirect()
                ->route('admin.mercadolibre.products.index')
                ->with('error', "Error al actualizar stock/precio: " . $e->getMessage());
        }
    }

    public function import(MercadoLibreProductService $service): RedirectResponse
    {
        try {
            $count = $service->importFromMercadoLibre();

            return redirect()
                ->route('admin.mercadolibre.products.index')
                ->with('success', "Se importaron o actualizaron {$count} publicaciones desde tu cuenta de Mercado Libre.");
        } catch (Exception $e) {
            return redirect()
                ->route('admin.mercadolibre.products.index')
                ->with('error', "Error importando publicaciones: " . $e->getMessage());
        }
    }

    public function downloadTemplate(): StreamedResponse
    {
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="plantilla_productos_vanilo_ml.csv"',
        ];

        $callback = function () {
            $file = fopen('php://output', 'w');
            // Escribir BOM UTF-8 para que Excel en español lo abra correctamente
            fwrite($file, "\xEF\xBB\xBF");

            fputcsv($file, ['sku', 'nombre', 'precio', 'stock', 'descripcion', 'imagen', 'publicar_ml'], ',', '"', '\\');
            fputcsv($file, [
                'DEMO-001',
                'Zapatos Deportivos Azules',
                '45.00',
                '10',
                'Zapatos cómodos para correr y deporte de alto rendimiento',
                'https://images.unsplash.com/photo-1542291026-7eec264c27ff',
                'no'
            ], ',', '"', '\\');
            fputcsv($file, [
                'DEMO-002',
                'Reloj Inteligente Fit',
                '80.00',
                '5',
                'Reloj inteligente con monitor cardíaco y notificaciones en tiempo real',
                'reloj.jpg',
                'si'
            ], ',', '"', '\\');

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function bulkUpload(Request $request, MercadoLibreProductService $service): RedirectResponse
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt',
            'images.*' => 'nullable|image|max:10240',
            'auto_publish' => 'nullable|boolean',
        ], [
            'csv_file.required' => 'Debes adjuntar el archivo CSV con la lista de productos.',
            'csv_file.mimes' => 'El archivo debe estar en formato CSV.',
            'images.*.image' => 'Los archivos adjuntos en el selector de imágenes deben ser fotos (JPG, PNG, WEBP).',
        ]);

        $file = $request->file('csv_file');
        $uploadedImages = $request->file('images', []);
        $autoPublish = $request->boolean('auto_publish');

        $handle = fopen($file->getRealPath(), 'r');
        if (!$handle) {
            return redirect()
                ->route('admin.mercadolibre.products.index')
                ->with('error', 'No se pudo abrir el archivo CSV adjuntado.');
        }

        // Detectar y quitar BOM UTF-8 si existe
        $bom = fread($handle, 3);
        if ($bom !== "\xEF\xBB\xBF") {
            rewind($handle);
        }

        $header = fputcsv_get_header($handle);
        $header = array_map(function ($col) {
            return strtolower(trim($col));
        }, $header);

        $createdOrUpdated = 0;
        $imagesAttached = 0;
        $publishedCount = 0;

        while (($row = fgetcsv($handle, 1000, ',', '"', '\\')) !== false) {
            if (count($row) < count($header)) {
                $row = array_pad($row, count($header), '');
            }
            $data = array_combine($header, $row);

            $sku = trim($data['sku'] ?? '');
            if (empty($sku)) {
                continue;
            }

            $name = trim($data['nombre'] ?? 'Producto ' . $sku);
            $price = floatval($data['precio'] ?? 0);
            $stock = intval($data['stock'] ?? 0);
            $description = trim($data['descripcion'] ?? '');
            $imageRef = trim($data['imagen'] ?? '');
            $publishMl = strtolower(trim($data['publicar_ml'] ?? 'no'));

            $product = Product::updateOrCreate(
                ['sku' => $sku],
                [
                    'name' => $name,
                    'price' => $price,
                    'stock' => $stock,
                    'state' => 'active',
                    'description' => $description,
                ]
            );

            $createdOrUpdated++;

            // Procesar imagen si se especificó una
            if (!empty($imageRef)) {
                try {
                    if (str_starts_with(strtolower($imageRef), 'http://') || str_starts_with(strtolower($imageRef), 'https://')) {
                        $product->addMediaFromUrl($imageRef)->toMediaCollection('default');
                        $imagesAttached++;
                    } else {
                        // Buscar entre los archivos de imagen adjuntados
                        foreach ($uploadedImages as $uploadedImage) {
                            if (strcasecmp($uploadedImage->getClientOriginalName(), $imageRef) === 0) {
                                $product->addMedia($uploadedImage)->toMediaCollection('default');
                                $imagesAttached++;
                                break;
                            }
                        }
                    }
                } catch (Exception $e) {
                    Log::warning("No se pudo asociar imagen al producto [SKU: {$sku}]: " . $e->getMessage());
                }
            }

            // Publicar en ML si se solicitó en fila o auto_publish
            if ($autoPublish || in_array($publishMl, ['si', 'sí', 'yes', '1', 'true'], true)) {
                try {
                    if (!$product->mercadolibreProduct) {
                        $service->publish($product, [
                            'condition' => 'new',
                            'listing_type_id' => 'gold_special',
                            'price' => $product->price,
                            'available_quantity' => $product->stock,
                        ]);
                        $publishedCount++;
                    }
                } catch (Exception $e) {
                    Log::warning("Error auto-publicando producto [SKU: {$sku}] en ML: " . $e->getMessage());
                }
            }
        }

        fclose($handle);

        $message = "Carga masiva finalizada exitosamente: {$createdOrUpdated} productos importados/actualizados";
        if ($imagesAttached > 0) {
            $message .= ", {$imagesAttached} imágenes asociadas";
        }
        if ($publishedCount > 0) {
            $message .= " y {$publishedCount} publicados en Mercado Libre";
        }
        $message .= ".";

        return redirect()
            ->route('admin.mercadolibre.products.index')
            ->with('success', $message);
    }
}

// Función auxiliar para obtener cabeceras de forma segura
if (!function_exists('fputcsv_get_header')) {
    function fputcsv_get_header($handle) {
        $header = fgetcsv($handle, 1000, ',', '"', '\\');
        return $header ?: [];
    }
}
