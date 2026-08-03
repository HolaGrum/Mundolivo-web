@if(!$mlProduct)
    <!-- Modal Publicar en ML -->
    <div class="modal fade" id="modalPublish{{ $product->id }}" tabindex="-1" aria-labelledby="modalPublishLabel{{ $product->id }}" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered text-start">
            <div class="modal-content">
                <form action="{{ route('admin.mercadolibre.products.publish', $product) }}" method="POST">
                    @csrf
                    <div class="modal-header bg-dark text-white">
                        <h5 class="modal-header-title mb-0" id="modalPublishLabel{{ $product->id }}">
                            <i class="fas fa-upload text-warning me-2"></i> Publicar en Mercado Libre
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Título de la publicación</label>
                            <input type="text" class="form-control" value="{{ substr($product->name, 0, 60) }}" disabled>
                            <small class="text-muted">Mercado Libre permite hasta 60 caracteres en el título.</small>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Precio (VES / USD)</label>
                                <input type="number" step="0.01" name="price" class="form-control" value="{{ $product->price }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Cantidad disponible</label>
                                <input type="number" name="available_quantity" class="form-control" value="{{ $product->stock ?? 5 }}" min="1" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Tipo de Publicación</label>
                                <select name="listing_type_id" class="form-select" required>
                                    <option value="gold_special" selected>Clásica (Exposición alta)</option>
                                    <option value="gold_pro">Premium (Exposición máxima)</option>
                                    <option value="free">Gratuita (Exposición baja)</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Condición</label>
                                <select name="condition" class="form-select" required>
                                    <option value="new" selected>Nuevo</option>
                                    <option value="used">Usado</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">ID de Categoría ML <span class="text-muted fw-normal">(Opcional)</span></label>
                            <input type="text" name="category_id" class="form-control" placeholder="Ej: MLV1055 (automático si se deja en blanco)">
                            <small class="text-muted">Si se deja vacío, nuestro sistema intentará detectar la mejor categoría para este título automáticamente.</small>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-sm btn-warning fw-bold text-dark">
                            <i class="fas fa-upload me-1"></i> Publicar Ahora
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@else
    <!-- Modal Actualizar Stock/Precio en ML -->
    <div class="modal fade" id="modalStock{{ $product->id }}" tabindex="-1" aria-labelledby="modalStockLabel{{ $product->id }}" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered text-start">
            <div class="modal-content">
                <form action="{{ route('admin.mercadolibre.products.stock', $mlProduct) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-header bg-dark text-white">
                        <h5 class="modal-header-title mb-0" id="modalStockLabel{{ $product->id }}">
                            <i class="fas fa-edit text-warning me-2"></i> Actualizar Precio / Stock ML
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Precio en ML</label>
                                <input type="number" step="0.01" name="price" class="form-control" value="{{ $mlProduct->price }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Stock en ML</label>
                                <input type="number" name="available_quantity" class="form-control" value="{{ $mlProduct->available_quantity }}" min="0" required>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-sm btn-dark">
                            Guardar Cambios
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endif
