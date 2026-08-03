@extends('appshell::layouts.private')

@section('title')
    {{ __('Mercado Libre - Publicaciones e Inventario') }}
@stop

@push('page-actions')
    <button type="button" class="btn btn-sm btn-success me-1" data-bs-toggle="modal" data-bs-target="#modalBulkUpload">
        <i class="fas fa-file-upload me-1"></i> {{ __('Carga Masiva (CSV + Imágenes)') }}
    </button>

    <form action="{{ route('admin.mercadolibre.products.import') }}" method="POST" class="d-inline">
        @csrf
        <button type="submit" class="btn btn-sm btn-outline-dark">
            <i class="fas fa-cloud-download-alt me-1"></i> {{ __('Importar de ML') }}
        </button>
    </form>
@endpush

@section('content')

    <div class="mb-3 d-flex gap-2">
        <a href="{{ route('admin.mercadolibre.products.index') }}" class="btn btn-sm {{ !request('status') ? 'btn-dark' : 'btn-outline-dark' }}">
            Todos
        </a>
        <a href="{{ route('admin.mercadolibre.products.index', ['status' => 'published']) }}" class="btn btn-sm {{ request('status') === 'published' ? 'btn-dark' : 'btn-outline-dark' }}">
            Publicados en ML
        </a>
        <a href="{{ route('admin.mercadolibre.products.index', ['status' => 'unpublished']) }}" class="btn btn-sm {{ request('status') === 'unpublished' ? 'btn-dark' : 'btn-outline-dark' }}">
            Sin publicar en ML
        </a>
    </div>

    <x-appshell::card accent="secondary">
        <x-slot:title>Catálogo de Productos e Integración</x-slot:title>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Producto</th>
                        <th>SKU</th>
                        <th>Precio Vanilo</th>
                        <th>Estado ML</th>
                        <th>ID Publicación ML</th>
                        <th class="text-end">Acciones ML</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $product)
                        @php
                            $mlProduct = $mlProducts->get($product->id);
                        @endphp
                        <tr>
                            <td>
                                <div class="fw-bold">{{ $product->name }}</div>
                            </td>
                            <td><code>{{ $product->sku }}</code></td>
                            <td>${{ number_format($product->price, 2) }}</td>
                            <td>
                                @if($mlProduct)
                                    <span class="{{ $mlProduct->status_badge_class }}">{{ ucfirst($mlProduct->status) }}</span>
                                    @if($mlProduct->sync_error)
                                        <span class="badge bg-danger ms-1" title="{{ $mlProduct->sync_error }}"><i class="fas fa-exclamation-circle"></i> Error</span>
                                    @endif
                                @else
                                    <span class="badge bg-secondary">No publicado</span>
                                @endif
                            </td>
                            <td>
                                @if($mlProduct)
                                    @if($mlProduct->permalink)
                                        <a href="{{ $mlProduct->permalink }}" target="_blank" class="fw-bold text-decoration-none">
                                            {{ $mlProduct->ml_item_id }} <i class="fas fa-external-link-alt small"></i>
                                        </a>
                                    @else
                                        <code>{{ $mlProduct->ml_item_id }}</code>
                                    @endif
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td class="text-end">
                                @if(!$mlProduct)
                                    <button type="button" class="btn btn-sm btn-warning fw-bold text-dark" data-bs-toggle="modal" data-bs-target="#modalPublish{{ $product->id }}">
                                        <i class="fas fa-upload me-1"></i> Publicar en ML
                                    </button>
                                @else
                                    <div class="btn-group">
                                        @if($mlProduct->status === 'active')
                                            <form action="{{ route('admin.mercadolibre.products.status', $mlProduct) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" name="status" value="paused">
                                                <button type="submit" class="btn btn-sm btn-outline-warning" title="Pausar publicación">
                                                    <i class="fas fa-pause"></i>
                                                </button>
                                            </form>
                                        @else
                                            <form action="{{ route('admin.mercadolibre.products.status', $mlProduct) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" name="status" value="active">
                                                <button type="submit" class="btn btn-sm btn-outline-success" title="Reactivar publicación">
                                                    <i class="fas fa-play"></i>
                                                </button>
                                            </form>
                                        @endif

                                        <button type="button" class="btn btn-sm btn-outline-dark" data-bs-toggle="modal" data-bs-target="#modalStock{{ $product->id }}" title="Actualizar Precio / Stock">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                    </div>
                                @endif

                                @include('admin.mercadolibre.products.modal_publish', ['product' => $product, 'mlProduct' => $mlProduct])
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-5">
                                No se encontraron productos en el inventario.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-appshell::card>

    @if($products->hasPages())
        <hr>
        <nav>
            {{ $products->withQueryString()->links() }}
        </nav>
    @endif

    <!-- Modal para Carga Masiva de Productos con Todo e Imágenes -->
    <div class="modal fade" id="modalBulkUpload" tabindex="-1" aria-labelledby="modalBulkUploadLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <form action="{{ route('admin.mercadolibre.products.bulk-upload') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-content">
                    <div class="modal-header bg-dark text-white">
                        <h5 class="modal-title" id="modalBulkUploadLabel">
                            <i class="fas fa-file-upload me-2"></i> Carga Masiva de Productos con Todo e Imágenes
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-info d-flex align-items-center mb-4">
                            <i class="fas fa-info-circle fa-2x me-3"></i>
                            <div>
                                <strong>Instrucciones:</strong> Sube tu inventario en archivo <strong>CSV</strong>. Puedes asociar imágenes indicando una <strong>URL web externa</strong> en la columna <em>imagen</em>, o indicando el nombre del archivo local (ej. <code>zapato.jpg</code>) y adjuntando las fotos en el selector inferior.
                                <div class="mt-2">
                                    <a href="{{ route('admin.mercadolibre.products.template') }}" class="btn btn-sm btn-outline-dark fw-bold">
                                        <i class="fas fa-download me-1"></i> Descargar Plantilla CSV en Español
                                    </a>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="csv_file" class="form-label fw-bold">1. Archivo CSV con lista de productos (*)</label>
                            <input type="file" class="form-control" id="csv_file" name="csv_file" accept=".csv,.txt" required>
                            <div class="form-text">Columnas requeridas: <code>sku,nombre,precio,stock,descripcion,imagen,publicar_ml</code></div>
                        </div>

                        <div class="mb-3">
                            <label for="images" class="form-label fw-bold">2. Archivos de Imágenes Locales (Opcional)</label>
                            <input type="file" class="form-control" id="images" name="images[]" multiple accept="image/*">
                            <div class="form-text">Si usaste nombres de archivos en la columna <em>imagen</em> (ej. <code>zapato.jpg</code>), selecciónalos aquí para vincularlos automáticamente.</div>
                        </div>

                        <div class="form-check form-switch mt-4">
                            <input class="form-check-input" type="checkbox" role="switch" id="auto_publish" name="auto_publish" value="1">
                            <label class="form-check-label fw-bold" for="auto_publish">
                                Publicar automáticamente en Mercado Libre al finalizar la carga
                            </label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-success fw-bold">
                            <i class="fas fa-upload me-1"></i> Iniciar Carga Masiva
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

@stop
