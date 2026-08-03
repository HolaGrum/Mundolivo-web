<?php

namespace App\Services\MercadoLibre;

use App\Models\MercadoLibreAccount;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Exception;

class MercadoLibreAuthService
{
    public function getAuthUrl(): string
    {
        $appId = config('services.mercadolibre.app_id');
        $redirectUri = config('services.mercadolibre.redirect_uri');
        $siteId = config('services.mercadolibre.site_id', 'MLV');

        if (empty($appId)) {
            throw new Exception("El App ID de Mercado Libre no está configurado (MERCADOLIBRE_APP_ID).");
        }

        // Determinar URL de auth según el país (Venezuela por defecto)
        $authDomain = match ($siteId) {
            'MLV' => 'auth.mercadolibre.com.ve',
            'MLA' => 'auth.mercadolibre.com.ar',
            'MLM' => 'auth.mercadolibre.com.mx',
            'MCO' => 'auth.mercadolibre.com.co',
            default => 'auth.mercadolibre.com.ve',
        };

        $params = http_build_query([
            'response_type' => 'code',
            'client_id' => $appId,
            'redirect_uri' => $redirectUri,
        ]);

        return "https://{$authDomain}/authorization?{$params}";
    }

    public function handleCallback(string $code, ?int $userId = null): MercadoLibreAccount
    {
        $appId = config('services.mercadolibre.app_id');
        $clientSecret = config('services.mercadolibre.client_secret');
        $redirectUri = config('services.mercadolibre.redirect_uri');

        $response = Http::asForm()->post('https://api.mercadolibre.com/oauth/token', [
            'grant_type' => 'authorization_code',
            'client_id' => $appId,
            'client_secret' => $clientSecret,
            'code' => $code,
            'redirect_uri' => $redirectUri,
        ]);

        if ($response->failed()) {
            Log::error("Error al obtener token de Mercado Libre:", [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            throw new Exception("Error al autenticar con Mercado Libre: " . $response->body());
        }

        $data = $response->json();

        // Obtener información del usuario en ML para guardar el nickname
        $userResponse = Http::withToken($data['access_token'])
            ->get("https://api.mercadolibre.com/users/" . $data['user_id']);

        $nickname = $userResponse->successful() ? ($userResponse->json('nickname') ?? 'Vendedor ML') : 'Vendedor ML';
        $siteId = $userResponse->successful() ? ($userResponse->json('site_id') ?? 'MLV') : config('services.mercadolibre.site_id', 'MLV');

        $account = MercadoLibreAccount::updateOrCreate(
            ['ml_user_id' => (string) $data['user_id']],
            [
                'user_id' => $userId,
                'nickname' => $nickname,
                'access_token' => $data['access_token'],
                'refresh_token' => $data['refresh_token'],
                'expires_at' => Carbon::now()->addSeconds($data['expires_in'] ?? 21600),
                'site_id' => $siteId,
                'status' => 'connected',
            ]
        );

        return $account;
    }

    public function refreshToken(MercadoLibreAccount $account): MercadoLibreAccount
    {
        $appId = config('services.mercadolibre.app_id');
        $clientSecret = config('services.mercadolibre.client_secret');

        $response = Http::asForm()->post('https://api.mercadolibre.com/oauth/token', [
            'grant_type' => 'refresh_token',
            'client_id' => $appId,
            'client_secret' => $clientSecret,
            'refresh_token' => $account->refresh_token,
        ]);

        if ($response->failed()) {
            Log::error("Error al refrescar token de Mercado Libre para cuenta {$account->id}:", [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            $account->update(['status' => 'disconnected']);
            throw new Exception("Error al refrescar sesión con Mercado Libre. Por favor conecta tu cuenta nuevamente.");
        }

        $data = $response->json();

        $account->update([
            'access_token' => $data['access_token'],
            'refresh_token' => $data['refresh_token'] ?? $account->refresh_token,
            'expires_at' => Carbon::now()->addSeconds($data['expires_in'] ?? 21600),
            'status' => 'connected',
        ]);

        return $account;
    }

    public function disconnect(MercadoLibreAccount $account): void
    {
        $account->update([
            'access_token' => null,
            'refresh_token' => null,
            'expires_at' => null,
            'status' => 'disconnected',
        ]);
    }
}
