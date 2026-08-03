<?php

namespace App\Http\Controllers;

use App\Models\MercadoLibreProduct;
use Vanilo\Product\Models\Product;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StorefrontController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->get('q');

        $query = Product::query()->with('media')->where('state', 'active');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $products = $query->paginate(12);
        $mlProducts = MercadoLibreProduct::all()->keyBy('product_id');

        return view('storefront.index', compact('products', 'mlProducts', 'search'));
    }

    public function show(string $slug): View
    {
        $product = Product::with('media')
            ->where('slug', $slug)
            ->orWhere('sku', $slug)
            ->orWhere('id', $slug)
            ->firstOrFail();

        $mlProduct = MercadoLibreProduct::where('product_id', $product->id)->first();

        return view('storefront.show', compact('product', 'mlProduct'));
    }
}
