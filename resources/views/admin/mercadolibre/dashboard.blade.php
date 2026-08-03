@extends('appshell::layouts.private')

@section('title')
    {{ __('Mercado Libre - Resumen') }}
@stop

@push('page-actions')
    <form action="{{ route('admin.mercadolibre.sync') }}" method="POST" class="d-inline">
        @csrf
        <button type="submit" class="btn btn-sm btn-warning text-dark fw-bold">
            <i class="fas fa-sync-alt me-1"></i> {{ __('Sincronizar Todo') }}
        </button>
    </form>
    <a href="{{ route('admin.mercadolibre.products.index') }}" class="btn btn-sm btn-outline-primary ms-1">
        {{ __('Ver Publicaciones') }}
    </a>
    <a href="{{ route('admin.mercadolibre.config') }}" class="btn btn-sm btn-outline-secondary ms-1">
        <i class="fas fa-cog"></i>
    </a>
@endpush

@section('content')

    @if(!$isConnected)
        <div class="alert alert-warning border-warning shadow-sm d-flex align-items-center justify-content-between mb-4" role="alert">
            <div>
                <strong class="d-block mb-1"><i class="fas fa-exclamation-triangle me-2"></i> Cuenta no conectada</strong>
                <span>Para publicar productos y sincronizar pedidos con Mercado Libre, conecta tu cuenta de vendedor.</span>
            </div>
            <a href="{{ route('admin.mercadolibre.config') }}" class="btn btn-sm btn-dark">
                Conectar ahora
            </a>
        </div>
    @else
        <div class="alert alert-success border-success shadow-sm d-flex align-items-center justify-content-between mb-4" role="alert">
            <div>
                <strong class="d-block mb-1"><i class="fas fa-check-circle me-2"></i> Conectado como {{ $account->nickname }}</strong>
                <span class="text-muted small">Sitio activo: <strong>{{ $account->site_id }}</strong> | Vence el {{ $account->expires_at?->format('d/m/Y H:i') }}</span>
            </div>
            <span class="badge bg-success">En línea</span>
        </div>
    @endif

    <div class="row g-4 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-3 h-100" style="background: linear-gradient(135deg, #fff3cd 0%, #ffe69c 100%);">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-dark fw-semibold small text-uppercase">Publicaciones Totales</span>
                        <span class="badge bg-dark text-warning">ML</span>
                    </div>
                    <h2 class="display-5 fw-bold mb-0 text-dark">{{ $stats['total_products'] }}</h2>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-3 h-100 bg-white">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-muted fw-semibold small text-uppercase">Activas</span>
                        <span class="badge bg-success">Activo</span>
                    </div>
                    <h2 class="display-5 fw-bold mb-0 text-success">{{ $stats['active_products'] }}</h2>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-3 h-100 bg-white">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-muted fw-semibold small text-uppercase">Pausadas</span>
                        <span class="badge bg-warning text-dark">Pausado</span>
                    </div>
                    <h2 class="display-5 fw-bold mb-0 text-warning">{{ $stats['paused_products'] }}</h2>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-3 h-100" style="background: linear-gradient(135deg, #e2e3e5 0%, #d3d6d8 100%);">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-dark fw-semibold small text-uppercase">Pedidos Importados</span>
                        <span class="badge bg-dark">Ventas</span>
                    </div>
                    <h2 class="display-5 fw-bold mb-0 text-dark">{{ $stats['total_orders'] }}</h2>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-6">
            <x-appshell::card accent="secondary">
                <x-slot:title>Últimas Publicaciones Sincronizadas</x-slot:title>
                <x-slot:actions>
                    <a href="{{ route('admin.mercadolibre.products.index') }}" class="btn btn-xs btn-outline-secondary">Ver todas</a>
                </x-slot:actions>

                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Producto</th>
                                <th>ID ML</th>
                                <th>Precio</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentProducts as $mlProduct)
                                <tr>
                                    <td>
                                        <div class="fw-semibold">{{ $mlProduct->title }}</div>
                                        <small class="text-muted">{{ $mlProduct->product?->sku ?? 'Sin SKU' }}</small>
                                    </td>
                                    <td>
                                        @if($mlProduct->permalink)
                                            <a href="{{ $mlProduct->permalink }}" target="_blank" class="text-decoration-none">
                                                {{ $mlProduct->ml_item_id }} <i class="fas fa-external-link-alt small"></i>
                                            </a>
                                        @else
                                            <code>{{ $mlProduct->ml_item_id }}</code>
                                        @endif
                                    </td>
                                    <td>${{ number_format($mlProduct->price, 2) }}</td>
                                    <td>
                                        <span class="{{ $mlProduct->status_badge_class }}">{{ ucfirst($mlProduct->status) }}</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4">No hay publicaciones vinculadas aún.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </x-appshell::card>
        </div>

        <div class="col-lg-6">
            <x-appshell::card accent="secondary">
                <x-slot:title>Últimos Pedidos de Mercado Libre</x-slot:title>
                <x-slot:actions>
                    <a href="{{ route('admin.mercadolibre.orders.index') }}" class="btn btn-xs btn-outline-secondary">Ver todos</a>
                </x-slot:actions>

                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th># Pedido</th>
                                <th>Comprador</th>
                                <th>Estado</th>
                                <th>Fecha</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentOrders as $mlOrder)
                                <tr>
                                    <td>
                                        <a href="{{ route('admin.mercadolibre.orders.show', $mlOrder) }}" class="fw-bold text-decoration-none">
                                            {{ $mlOrder->ml_order_id }}
                                        </a>
                                    </td>
                                    <td>{{ $mlOrder->ml_buyer_nickname }}</td>
                                    <td>
                                        <span class="badge bg-info">{{ $mlOrder->status_label }}</span>
                                    </td>
                                    <td><small class="text-muted">{{ $mlOrder->synced_at?->diffForHumans() }}</small></td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4">No hay pedidos importados aún.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </x-appshell::card>
        </div>
    </div>
@stop
