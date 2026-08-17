<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Vanilo\Category\Models\Taxon;
use Vanilo\Category\Models\Taxonomy;
use Vanilo\Foundation\Models\Product;

class StorefrontCatalogSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Catálogo de prueba. La información vive en la base de datos;
     * el storefront la consume a través de la API (Vanilo).
     */
    protected array $categories = [
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

    protected array $products = [
        [
            'id' => 0,
            'title' => 'Pintura Interior Blanco',
            'sku' => '10000001',
            'description' => 'Pintura acrílica para interiores, acabado mate, excelente cubrimiento.',
            'price' => 12.9,
            'taxons' => ['Interior', 'Madera', 'Hormigón', 'Azulejo'],
        ],
        [
            'id' => 1,
            'title' => 'Pintura Exterior',
            'sku' => '10000002',
            'description' => 'Resistente a la intemperie y a la decoloración, ideal para fachadas.',
            'price' => 39.5,
            'taxons' => ['Exterior', 'Metal', 'Hormigón'],
        ],
        [
            'id' => 2,
            'title' => 'Imprimación Selladora 1L',
            'sku' => '10000003',
            'description' => 'Base primer para mejorar adherencia y durabilidad de la pintura.',
            'price' => 15.0,
            'taxons' => ['Imprimantes/Primers', 'Selladores', 'Madera', 'Metal'],
        ],
        [
            'id' => 3,
            'title' => 'Set Brochas Profesionales',
            'sku' => '10000004',
            'description' => 'Juego de brochas de distintas medidas, mango ergonómico.',
            'price' => 18.75,
            'taxons' => ['Brochas'],
        ],
        [
            'id' => 4,
            'title' => 'Pintura Automotriz',
            'sku' => '10000005',
            'description' => 'Pintura especializada para vehículos, resistente a la intemperie y con acabado duradero.',
            'price' => 9.9,
            'taxons' => ['Automotriz', 'Automoción', 'Pintado automotriz'],
        ],
        [
            'id' => 5,
            'title' => 'Bandeja y Rejilla para Rodillo',
            'sku' => '10000006',
            'description' => 'Bandeja plástica resistente con rejilla metálica incluidos.',
            'price' => 7.5,
            'taxons' => ['Rodillos', 'Aplicación profesional'],
        ],
        [
            'id' => 6,
            'title' => 'Cinta Masking 24mm x 50m',
            'sku' => '10000007',
            'description' => 'Cinta de enmascarar para protección en pintura y acabados.',
            'price' => 4.2,
            'taxons' => ['Cintas y plásticos de enmascarar'],
        ],
        [
            'id' => 8,
            'title' => 'Lija Agua/Seco Pack 10',
            'sku' => '10000008',
            'description' => 'Pack de 10 hojas de lija para acabado fino y lijado entre manos.',
            'price' => 6.0,
            'taxons' => ['Lijas y esponjas', 'Reparación y masillado', 'Madera', 'Metal'],
        ],
    ];

    public function run(): void
    {
        $this->seedTaxonomies();
        $this->seedProducts();
    }

    protected function seedTaxonomies(): void
    {
        foreach ($this->categories as $category) {
            $taxonomy = Taxonomy::query()->firstOrCreate(
                ['name' => $category['name']],
                ['name' => $category['name']]
            );

            foreach ($category['subcategories'] as $sub) {
                $taxonomy->taxons()->firstOrCreate(['name' => $sub]);
            }
        }
    }

    protected function seedProducts(): void
    {
        foreach ($this->products as $item) {
            $sku = (string) $item['sku'];

            $product = Product::query()->updateOrCreate(
                ['sku' => $sku],
                [
                    'name' => trim($item['title']),
                    'description' => $item['description'],
                    'excerpt' => $item['description'],
                    'price' => (float) $item['price'],
                    'stock' => 100,
                    'state' => 'active',
                ]
            );

            $this->syncTaxons($product, $item['taxons']);
            $this->syncImage($product, (string) $item['id']);
        }
    }

    protected function syncTaxons(Product $product, array $taxons): void
    {
        $ids = Taxon::query()->whereIn('name', $taxons)->pluck('id');
        $product->taxons()->sync($ids);
    }

    protected function syncImage(Product $product, string $imageKey): void
    {
        $path = public_path("imgs-api/{$imageKey}.webp");

        if (!File::exists($path)) {
            return;
        }

        $product->clearMediaCollection();
        $product->addMediaFromString(File::get($path))
            ->usingFileName(basename($path))
            ->usingName($imageKey)
            ->toMediaCollection();
    }
}
