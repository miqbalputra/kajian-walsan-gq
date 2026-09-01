<?php

namespace App\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use LogicException;
use RuntimeException;
use Throwable;

final class DataIndukClient
{
    /**
     * @return array<int, array<string, mixed>>
     */
    public function activeStudentRoster(?string $unitId = null): array
    {
        $this->ensureConfigured();

        $query = array_filter([
            'unitId' => $unitId ?? config('data-induk.unit_id'),
        ], static fn (mixed $value): bool => filled($value));

        try {
            $response = $this->request()
                ->retry(2, 200, static fn (Throwable $exception): bool => $exception instanceof ConnectionException)
                ->get('integrations/students', $query)
                ->throw();
        } catch (ConnectionException $exception) {
            throw new RuntimeException('Data Induk tidak dapat dijangkau. Periksa DATA_INDUK_BASE_URL dan jaringan server.', previous: $exception);
        }

        $data = $response->json('data');
        if (! is_array($data)) {
            throw new RuntimeException('Respons roster dari Data Induk tidak valid.');
        }

        return $data;
    }

    public function isConfigured(): bool
    {
        return (bool) config('data-induk.enabled')
            && filled(config('data-induk.base_url'))
            && filled(config('data-induk.api_key'));
    }

    private function ensureConfigured(): void
    {
        if (! $this->isConfigured()) {
            throw new LogicException('Integrasi Data Induk belum dikonfigurasi. Isi DATA_INDUK_ENABLED, DATA_INDUK_BASE_URL, dan DATA_INDUK_API_KEY.');
        }
    }

    private function request(): PendingRequest
    {
        return Http::acceptJson()
            ->baseUrl((string) config('data-induk.base_url'))
            ->withHeaders(['X-API-Key' => (string) config('data-induk.api_key')])
            ->timeout((int) config('data-induk.timeout', 10));
    }
}
