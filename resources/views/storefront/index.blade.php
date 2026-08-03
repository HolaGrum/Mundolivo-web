@extends('storefront.layout')

@section('title', 'Catálogo de Productos | Tienda Oficial')

@section('content')

    <!-- Hero Section -->
    <div class="p-5 mb-5 rounded-4 shadow-sm text-white position-relative overflow-hidden" style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <span class="badge bg-warning text-dark px-3 py-2 rounded-pill fw-bold mb-3">
                    <i class="fas fa-check-circle me-1"></i> Stock Real & Sincronizado
                </span>
                <h1 class="display-5 fw-extrabold mb-3">Catálogo Oficial de Productos</h1>
                <p class="lead mb-4 text-light opacity-75">
                    Adquiere nuestros productos con garantía directa o encuéntralos en Mercado Libre. Todo administrado desde un único centro de inventario.
                </p>
                <!-- Search Form -->
                <form action="{{ route('storefront.index') }}" method="GET" class="d-flex gap-2 max-w-md">
                    <div class="input-group input-group-lg shadow-sm">
                        <span class="input-group-text bg-white border-0 text-muted">
                            <i class="fas fa-search"></i>
                        </span>
                        <input type="text" name="q" class="form-control border-0" placeholder="Buscar por nombre o SKU..." value="{{ $search }}">
                        @if($search)
                            <a href="{{ route('storefront.index') }}" class="btn btn-light border-0 d-flex align-items-center">
                                <i class="fas fa-times text-muted"></i>
                            </a>
                        @endif
                        <button class="btn btn-warning fw-bold text-dark px-4" type="submit">Buscar</button>
                    </div>
                </form>
            </div>
            <div class="col-lg-4 d-none d-lg-flex justify-content-end">
                <div class="text-center p-4 rounded-4 bg-white bg-opacity-10 backdrop-blur border border-light border-opacity-10">
                    <i class="fas fa-boxes-stacked display-1 text-warning mb-3"></i>
                    <h5 class="fw-bold mb-0">Integración Total</h5>
                    <small class="text-light opacity-75">100% Sincronizado con ML</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Products Grid -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold mb-0">
            @if($search)
                Resultados para: "<span class="text-primary">{{ $search }}</span>"
            @else
                Todos los Productos
            @endif
        </h3>
        <span class="text-muted">{{ $products->total() }} artículos encontrados</span>
    </div>

    <div class="row g-4">
        @forelse($products as $product)
            @php
                $mlProduct = $mlProducts->get($product->id);
                $imageUrl = $product->hasMedia('default') ? $product->getFirstMediaUrl('default', 'thumbnail') : null;
            @endphp
            <div class="col-sm-6 col-md-4 col-lg-3">
                <div class="card product-card h-100 bg-white d-flex flex-column">
                    <!-- Image Wrapper -->
                    <div class="position-relative bg-light text-center py-4" style="height: 220px; display: flex; align-items: center; justify-content: center;">
                        @if($imageUrl)
                            <img src="{{ $imageUrl }}" class="img-fluid" style="max-height: 180px; object-fit: contain;" alt="{{ $product->name }}">
                        @else
                            <i class="fas fa-box-open text-secondary opacity-25" style="font-size: 5rem;"></i>
                        @endif

                        @if($mlProduct && $mlProduct->status === 'active')
                            <span class="position-absolute top-0 end-0 m-3 badge badge-ml rounded-pill px-3 py-2 shadow-sm">
                                <i class="fas fa-bolt me-1"></i> En Mercado Libre
                            </span>
                        @endif
                    </div>

                    <!-- Card Body -->
                    <div class="card-body d-flex flex-column p-4">
                        <small class="text-muted font-monospace mb-1">{{ $product->sku }}</small>
                        <h5 class="card-title fw-bold text-dark mb-2 line-clamp-2">
                            {{ $product->name }}
                        </h5>

                        <div class="mt-auto pt-3">
                            <div class="d-flex justify-content-between align-items-baseline mb-3">
                                <span class="fs-4 fw-extrabold text-dark">${{ number_format($product->price, 2) }}</span>
                                @if(($product->stock ?? 1) > 0)
                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">
                                        Disponible
                                    </span>
                                @else
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1">
                                        Agotado
                                    </span>
                                @endif
                            </div>

                            <a href="{{ route('storefront.show', $product->slug ?: $product->id) }}" class="btn btn-dark w-100 fw-semibold py-2 rounded-3">
                                Ver Detalles <i class="fas fa-arrow-right ms-1"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="text-center py-5 bg-white rounded-4 shadow-sm border">
                    <i class="fas fa-search display-4 text-muted mb-3"></i>
                    <h4 class="fw-bold">No se encontraron productos</h4>
                    <p class="text-muted">Intenta buscar con otros términos o consulta nuevamente más tarde.</p>
                    <a href="{{ route('storefront.index') }}" class="btn btn-outline-dark mt-2">
                        Ver Todos los Productos
                    </a>
                </div>
            </div>
        @endforelse
    </div>

    @if($products->hasPages())
        <div class="mt-5 d-flex justify-content-center">
            {{ $products->withQueryString()->links() }}
        </div>
    @endif

@stop
