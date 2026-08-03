@extends('appshell::layouts.private')

@section('title')
    {{ __('Detalle de Pedido Mercado Libre #') . $order->ml_order_id }}
@stop

@push('page-actions')
    <a href="{{ route('admin.mercadolibre.orders.index') }}" class="btn btn-sm btn-outline-secondary">
        {{ __('Volver al listado') }}
    </a>
    @if($order->order)
        <a href="{{ route('vanilo.admin.order.show', $order->order) }}" class="btn btn-sm btn-dark ms-2">
            <i class="fas fa-shopping-bag me-1"></i> {{ __('Ver Pedido en Vanilo #') . $order->order->number }}
        </a>
    @endif
@endpush

@section('content')

    <div class="row g-4">
        <div class="col-lg-8">
            <x-appshell::card accent="secondary">
                <x-slot:title>Productos e Ítems del Pedido</x-slot:title>

                @if($order->order && $order->order->items->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Producto</th>
                                    <th>Cantidad</th>
                                    <th>Precio Unitario</th>
                                    <th class="text-end">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($order->order->items as $item)
                                    <tr>
                                        <td>
                                            <div class="fw-bold">{{ $item->name }}</div>
                                        </td>
                                        <td>{{ $item->quantity }}</td>
                                        <td>${{ number_format($item->price, 2) }}</td>
                                        <td class="text-end fw-bold">${{ number_format($item->total, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <td colspan="3" class="text-end fw-bold">Total</td>
                                    <td class="text-end fw-bold fs-5">${{ number_format($order->order->total(), 2) }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                @else
                    <div class="p-4 text-center text-muted">
                        No hay ítems detallados o el pedido en Vanilo no está vinculado.
                    </div>
                @endif
            </x-appshell::card>
        </div>

        <div class="col-lg-4">
            <x-appshell::card accent="secondary">
                <x-slot:title>Información del Comprador</x-slot:title>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between">
                        <span class="text-muted">Apodo ML</span>
                        <strong class="text-dark">{{ $order->ml_buyer_nickname }}</strong>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        <span class="text-muted">ID Comprador</span>
                        <code>{{ $order->ml_buyer_id }}</code>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        <span class="text-muted">Estado del Pedido</span>
                        <span class="badge bg-success">{{ $order->status_label }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        <span class="text-muted">ID Envío ML</span>
                        <code>{{ $order->ml_shipping_id ?: 'No disponible' }}</code>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        <span class="text-muted">ID Pago ML</span>
                        <code>{{ $order->ml_payment_id ?: 'No disponible' }}</code>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        <span class="text-muted">Sincronizado</span>
                        <span>{{ $order->synced_at?->format('d/m/Y H:i') }}</span>
                    </li>
                </ul>
            </x-appshell::card>
        </div>
    </div>

@stop
