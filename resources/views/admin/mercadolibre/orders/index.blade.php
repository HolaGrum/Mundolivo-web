@extends('appshell::layouts.private')

@section('title')
    {{ __('Mercado Libre - Pedidos') }}
@stop

@push('page-actions')
    <form action="{{ route('admin.mercadolibre.orders.sync') }}" method="POST" class="d-inline">
        @csrf
        <button type="submit" class="btn btn-sm btn-dark">
            <i class="fas fa-sync me-1"></i> {{ __('Sincronizar Pedidos ML') }}
        </button>
    </form>
@endpush

@section('content')

    <x-appshell::card accent="secondary">
        <x-slot:title>Historial de Pedidos de Mercado Libre</x-slot:title>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>ID Pedido ML</th>
                        <th>Pedido Vanilo</th>
                        <th>Comprador ML</th>
                        <th>Estado Pago</th>
                        <th>ID Envío</th>
                        <th>Fecha de Sincronización</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $mlOrder)
                        <tr>
                            <td>
                                <span class="fw-bold">{{ $mlOrder->ml_order_id }}</span>
                            </td>
                            <td>
                                @if($mlOrder->order)
                                    <a href="{{ route('vanilo.admin.order.show', $mlOrder->order) }}" class="badge bg-dark text-decoration-none">
                                        {{ $mlOrder->order->number }}
                                    </a>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>
                                <div class="fw-semibold">{{ $mlOrder->ml_buyer_nickname }}</div>
                                <small class="text-muted">ID: {{ $mlOrder->ml_buyer_id }}</small>
                            </td>
                            <td>
                                <span class="badge {{ $mlOrder->ml_status === 'paid' ? 'bg-success' : 'bg-warning text-dark' }}">
                                    {{ $mlOrder->status_label }}
                                </span>
                            </td>
                            <td>
                                <code>{{ $mlOrder->ml_shipping_id ?: 'No requerido' }}</code>
                            </td>
                            <td>
                                {{ $mlOrder->synced_at?->format('d/m/Y H:i') }}
                            </td>
                            <td class="text-end">
                                <a href="{{ route('admin.mercadolibre.orders.show', $mlOrder) }}" class="btn btn-xs btn-outline-primary">
                                    {{ __('Ver Detalle ML') }}
                                </a>
                                @if($mlOrder->order)
                                    <a href="{{ route('vanilo.admin.order.show', $mlOrder->order) }}" class="btn btn-xs btn-outline-secondary">
                                        {{ __('Ver en Vanilo') }}
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-5">
                                No se han importado pedidos de Mercado Libre aún. Haz clic en <strong>Sincronizar Pedidos ML</strong>.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-appshell::card>

    @if($orders->hasPages())
        <hr>
        <nav>
            {{ $orders->withQueryString()->links() }}
        </nav>
    @endif

@stop
