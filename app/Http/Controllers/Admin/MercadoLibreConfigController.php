<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MercadoLibreAccount;
use App\Services\MercadoLibre\MercadoLibreAuthService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Exception;

class MercadoLibreConfigController extends Controller
{
    public function index(): View
    {
        $account = MercadoLibreAccount::defaultAccount();
        $appId = config('services.mercadolibre.app_id');
        $redirectUri = config('services.mercadolibre.redirect_uri');
        $siteId = config('services.mercadolibre.site_id', 'MLV');

        return view('admin.mercadolibre.config', compact('account', 'appId', 'redirectUri', 'siteId'));
    }

    public function redirect(MercadoLibreAuthService $authService): RedirectResponse
    {
        try {
            $url = $authService->getAuthUrl();
            return redirect()->away($url);
        } catch (Exception $e) {
            return redirect()
                ->route('admin.mercadolibre.config')
                ->with('error', "No se pudo generar la URL de autorización: " . $e->getMessage());
        }
    }

    public function callback(Request $request, MercadoLibreAuthService $authService): RedirectResponse
    {
        $code = $request->get('code');
        if (!$code) {
            return redirect()
                ->route('admin.mercadolibre.config')
                ->with('error', "No se recibió el código de autorización de Mercado Libre.");
        }

        try {
            $account = $authService->handleCallback($code, Auth::id());
            return redirect()
                ->route('admin.mercadolibre.config')
                ->with('success', "¡Cuenta conectada exitosamente! Bienvenido, {$account->nickname}.");
        } catch (Exception $e) {
            return redirect()
                ->route('admin.mercadolibre.config')
                ->with('error', "Error al conectar con Mercado Libre: " . $e->getMessage());
        }
    }

    public function disconnect(MercadoLibreAccount $account, MercadoLibreAuthService $authService): RedirectResponse
    {
        $authService->disconnect($account);

        return redirect()
            ->route('admin.mercadolibre.config')
            ->with('success', "Cuenta de Mercado Libre desconectada.");
    }
}
