@extends('appshell::layouts.private')

@section('title')
    {{ __('Mercado Libre - Configuración OAuth 2.0') }}
@stop

@section('content')

    <div class="row g-4">
        <div class="col-lg-6">
            <x-appshell::card accent="secondary">
                <x-slot:title>Estado de Conexión y Autorización</x-slot:title>

                <div class="text-center py-4">
                    @if($account && $account->isConnected())
                        <div class="mb-3">
                            <span class="badge bg-success px-3 py-2 fs-6">
                                <i class="fas fa-check-circle me-1"></i> Cuenta Conectada
                            </span>
                        </div>
                        <h4 class="fw-bold mb-1">{{ $account->nickname }}</h4>
                        <p class="text-muted small mb-4">ID de Vendedor: <code>{{ $account->ml_user_id }}</code> | Sitio: <strong>{{ $account->site_id }}</strong></p>

                        <div class="alert alert-light border text-start mb-4">
                            <ul class="list-unstyled mb-0 small">
                                <li class="mb-1"><i class="fas fa-clock text-muted me-2"></i> <strong>Expiración del Token:</strong> {{ $account->expires_at?->format('d/m/Y H:i:s') }}</li>
                                <li><i class="fas fa-sync text-success me-2"></i> <strong>Autorefresco:</strong> Activo (se renueva automáticamente)</li>
                            </ul>
                        </div>

                        <form action="{{ route('admin.mercadolibre.disconnect', $account) }}" method="POST" onsubmit="return confirm('¿Deseas desconectar tu cuenta de Mercado Libre?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger">
                                <i class="fas fa-unlink me-1"></i> Desconectar Cuenta
                            </button>
                        </form>
                    @else
                        <div class="mb-3">
                            <span class="badge bg-secondary px-3 py-2 fs-6">
                                <i class="fas fa-times-circle me-1"></i> Sin Conexión
                            </span>
                        </div>
                        <h4 class="fw-bold mb-2">Conecta tu Tienda con Mercado Libre</h4>
                        <p class="text-muted small mb-4">
                            Al conectar tu cuenta podrás publicar productos del catálogo en 1 clic, mantener stock y precios sincronizados, y recibir pedidos directamente en tu panel de administración.
                        </p>

                        <a href="{{ route('admin.mercadolibre.redirect') }}" class="btn btn-lg btn-warning text-dark fw-bold px-4 shadow-sm">
                            <i class="fas fa-plug me-2"></i> Autorizar / Conectar con Mercado Libre
                        </a>
                    @endif
                </div>
            </x-appshell::card>
        </div>

        <div class="col-lg-6">
            <x-appshell::card accent="secondary">
                <x-slot:title>Parámetros de la Aplicación en Mercado Libre</x-slot:title>

                <p class="text-muted small">
                    Para conectar tu cuenta, primero debes registrar tu aplicación en el portal de desarrolladores de <a href="https://developers.mercadolibre.com.ve/" target="_blank">Mercado Libre Venezuela</a> y colocar estas credenciales en el archivo <code>.env</code> de tu servidor:
                </p>

                <div class="mb-3">
                    <label class="form-label fw-bold small text-uppercase text-muted">MERCADOLIBRE_APP_ID (Client ID)</label>
                    <input type="text" class="form-control font-monospace" value="{{ $appId ?: 'No configurado en .env' }}" readonly>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold small text-uppercase text-muted">MERCADOLIBRE_CLIENT_SECRET</label>
                    <input type="text" class="form-control font-monospace" value="{{ config('services.mercadolibre.client_secret') ? '••••••••••••••••••••••••••••' : 'No configurado en .env' }}" readonly>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold small text-uppercase text-muted">MERCADOLIBRE_REDIRECT_URI (URI de retorno)</label>
                    <div class="input-group">
                        <input type="text" class="form-control font-monospace" value="{{ $redirectUri }}" id="redirectUriInput" readonly>
                        <button class="btn btn-outline-secondary" type="button" onclick="navigator.clipboard.writeText(document.getElementById('redirectUriInput').value); alert('URL copiada');">
                            <i class="fas fa-copy"></i>
                        </button>
                    </div>
                    <small class="text-muted">Copia esta dirección y agrégala a las "URI de redireccionamiento permitidas" en la configuración de tu App de Mercado Libre.</small>
                </div>

                <div class="alert alert-info border-info mb-0 small">
                    <i class="fas fa-info-circle me-1"></i> Si necesitas cambiar tus credenciales, edita el archivo <code>.env</code> de tu servidor y recarga la configuración con <code>php artisan config:clear</code>.
                </div>
            </x-appshell::card>
        </div>
    </div>

@stop
