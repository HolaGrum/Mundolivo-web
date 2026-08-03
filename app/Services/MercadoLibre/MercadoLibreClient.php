<?php

namespace App\Services\MercadoLibre;

use App\Models\MercadoLibreAccount;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class MercadoLibreClient
{
    protected string $baseUrl = 'https://api.mercadolibre.com';
    protected ?MercadoLibreAccount $account = null;

    public function __construct(?MercadoLibreAccount $account = null)
    {
        $this->account = $account ?: MercadoLibreAccount::defaultAccount();
    }

    public function getAccount(): ?MercadoLibreAccount
    {
        return $this->account;
    }

    protected function ensureAuthenticated(): void
    {
        if (!$this->account || !$this->account->isConnected()) {
            throw new Exception("No existe una cuenta de Mercado Libre conectada.");
        }

        if ($this->account->isTokenExpired()) {
            app(MercadoLibreAuthService::class)->refreshToken($this->account);
        }
    }

    public function get(string $endpoint, array $query = []): array
    {
        $this->ensureAuthenticated();

        $response = Http::withToken($this->account->access_token)
            ->acceptJson()
            ->get($this->baseUrl . '/' . ltrim($endpoint, '/'), $query);

        if ($response->failed()) {
            Log::error("MercadoLibre API GET Error [{$endpoint}]", [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            throw new Exception("MercadoLibre API Error ({$response->status()}): " . $response->body());
        }

        return $response->json() ?: [];
    }

    public function post(string $endpoint, array $data = []): array
    {
        $this->ensureAuthenticated();

        $response = Http::withToken($this->account->access_token)
            ->acceptJson()
            ->post($this->baseUrl . '/' . ltrim($endpoint, '/'), $data);

        if ($response->failed()) {
            Log::error("MercadoLibre API POST Error [{$endpoint}]", [
                'status' => $response->status(),
                'body' => $response->body(),
                'data' => $data,
            ]);
            throw new Exception("MercadoLibre API Error ({$response->status()}): " . $response->body());
        }

        return $response->json() ?: [];
    }

    public function put(string $endpoint, array $data = []): array
    {
        $this->ensureAuthenticated();

        $response = Http::withToken($this->account->access_token)
            ->acceptJson()
            ->put($this->baseUrl . '/' . ltrim($endpoint, '/'), $data);

        if ($response->failed()) {
            Log::error("MercadoLibre API PUT Error [{$endpoint}]", [
                'status' => $response->status(),
                'body' => $response->body(),
                'data' => $data,
            ]);
            throw new Exception("MercadoLibre API Error ({$response->status()}): " . $response->body());
        }

        return $response->json() ?: [];
    }

    public function delete(string $endpoint): array
    {
        $this->ensureAuthenticated();

        $response = Http::withToken($this->account->access_token)
            ->acceptJson()
            ->delete($this->baseUrl . '/' . ltrim($endpoint, '/'));

        if ($response->failed()) {
            Log::error("MercadoLibre API DELETE Error [{$endpoint}]", [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            throw new Exception("MercadoLibre API Error ({$response->status()}): " . $response->body());
        }

        return $response->json() ?: [];
    }
}
