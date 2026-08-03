@extends('storefront.layout')

@section('title', $product->name . ' | Vanilo Store & Mercado Libre')

@section('content')

    <!-- Breadcrumbs -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('storefront.index') }}" class="text-decoration-none text-muted">Catálogo</a></li>
            <li class="breadcrumb-item active fw-bold text-dark" aria-current="page">{{ substr($product->name, 0, 40) }}...</li>
        </ol>
    </nav>

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white mb-5">
        <div class="row g-0">
            <!-- Left: Product Gallery / Image -->
            <div class="col-lg-6 bg-light d-flex align-items-center justify-content-center p-5 position-relative" style="min-height: 450px;">
                @php
                    $imageUrl = $product->hasMedia('default') ? $product->getFirstMediaUrl('default') : null;
                @endphp

                @if($imageUrl)
                    <img src="{{ $imageUrl }}" class="img-fluid rounded-3" style="max-height: 400px; object-fit: contain;" alt="{{ $product->name }}">
                @else
                    <div class="text-center">
                        <i class="fas fa-box-open text-secondary opacity-25" style="font-size: 8rem;"></i>
                        <p class="text-muted mt-2">Imagen no disponible</p>
                    </div>
                @endif

                @if($mlProduct && $mlProduct->status === 'active')
                    <span class="position-absolute top-0 start-0 m-4 badge badge-ml rounded-pill px-3 py-2 shadow-sm fs-6">
                        <i class="fas fa-check-circle me-1"></i> Verificado en Mercado Libre
                    </span>
                @endif
            </div>

            <!-- Right: Product Info & Actions -->
            <div class="col-lg-6 p-4 p-lg-5 d-flex flex-column justify-content-between">
                <div>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="badge bg-secondary-subtle text-dark border font-monospace px-2 py-1">
                            SKU: {{ $product->sku }}
                        </span>
                        @if(($product->stock ?? 1) > 0)
                            <span class="badge bg-success px-3 py-2">
                                <i class="fas fa-check me-1"></i> En Stock ({{ $product->stock ?? 'Disponible' }})
                            </span>
                        @else
                            <span class="badge bg-danger px-3 py-2">Agotado temporalmente</span>
                        @endif
                    </div>

                    <h1 class="fw-extrabold text-dark mb-3">{{ $product->name }}</h1>

                    <div class="mb-4">
                        <span class="display-4 fw-bold text-dark">${{ number_format($product->price, 2) }}</span>
                        <span class="text-muted ms-1">USD / Precio Oficial</span>
                    </div>

                    <hr class="my-4 border-secondary opacity-25">

                    <h6 class="fw-bold text-uppercase text-muted small mb-2">Descripción del Producto</h6>
                    <div class="text-secondary mb-4" style="line-height: 1.7;">
                        {!! nl2br(e($product->description ?: 'Producto original y garantizado por nuestra tienda oficial. Todo el stock se encuentra sincronizado con nuestra red de distribución en Venezuela.')) !!}
                    </div>
                </div>

                <!-- Purchase Options Card -->
                <div class="mt-4">
                    <div class="d-grid gap-3">
                        <button type="button" class="btn btn-dark btn-lg py-3 fw-bold shadow-sm" onclick="alert('Funcionalidad de carrito directo disponible en caja.');">
                            <i class="fas fa-shopping-cart me-2"></i> Comprar en Tienda Web Oficial
                        </button>

                        @if($mlProduct && $mlProduct->status === 'active' && $mlProduct->permalink)
                            <a href="{{ $mlProduct->permalink }}" target="_blank" class="btn btn-ml btn-lg py-3 fw-bold text-decoration-none shadow-sm d-flex align-items-center justify-content-center gap-2">
                                <i class="fas fa-external-link-alt"></i>
                                <span>Comprar a través de <strong>Mercado Libre Venezuela</strong></span>
                            </a>
                        @elseif($mlProduct)
                            <div class="alert alert-warning mb-0 small">
                                <i class="fas fa-info-circle me-1"></i> Publicado en Mercado Libre (ID: <code>{{ $mlProduct->ml_item_id }}</code>) - Estado: <strong>{{ ucfirst($mlProduct->status) }}</strong>
                            </div>
                        @endif
                    </div>

                    <!-- Trust / Sync Badges -->
                    <div class="row g-2 mt-4 text-center small text-muted">
                        <div class="col-4">
                            <div class="p-2 rounded bg-light border">
                                <i class="fas fa-shield-alt text-dark d-block mb-1 fs-5"></i>
                                <strong>Garantía</strong><br>Directa
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="p-2 rounded bg-light border">
                                <i class="fas fa-sync text-dark d-block mb-1 fs-5"></i>
                                <strong>Inventario</strong><br>Sincronizado
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="p-2 rounded bg-light border">
                                <i class="fas fa-truck text-dark d-block mb-1 fs-5"></i>
                                <strong>Envíos</strong><br>A todo el país
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@stop
