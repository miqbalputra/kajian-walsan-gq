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

    /**
     * @return array{items:array<int, array<string, mixed>>,nextCursor:int}
     */
    public function changes(?string $cursor = null, ?string $unitId = null): array
    {
        $data = $this->responseData('integrations/changes', array_filter([
            'cursor' => $cursor,
            'unitId' => $unitId ?? config('data-induk.unit_id'),
            'limit' => 200,
        ], static fn (mixed $value): bool => filled($value)));

        $items = $data['items'] ?? null;
        if (! is_array($items) || ! isset($data['nextCursor'])) {
            throw new RuntimeException('Respons perubahan Data Induk tidak valid.');
        }

        return ['items' => $items, 'nextCursor' => (int) $data['nextCursor']];
    }

    /** @return array<int, array<string, mixed>> */
    public function referenceClasses(?string $unitId = null): array
    {
        return $this->collection('integrations/references/classes', array_filter([
            'unitId' => $unitId ?? config('data-induk.unit_id'),
        ], static fn (mixed $value): bool => filled($value)));
    }

    /** @return array<int, array<string, mixed>> */
    public function guardians(?string $unitId = null): array
    {
        return $this->collection('integrations/guardians', array_filter([
            'unitId' => $unitId ?? config('data-induk.unit_id'),
        ], static fn (mixed $value): bool => filled($value)));
    }

    /** @return array<int, array<string, mixed>> */
    public function familyLinks(?string $unitId = null): array
    {
        return $this->collection('integrations/family-links', array_filter([
            'unitId' => $unitId ?? config('data-induk.unit_id'),
        ], static fn (mixed $value): bool => filled($value)));
    }

    /** @return array<int, array<string, mixed>> */
    public function enrollments(?string $unitId = null): array
    {
        return $this->collection('integrations/enrollments', array_filter([
            'unitId' => $unitId ?? config('data-induk.unit_id'),
        ], static fn (mixed $value): bool => filled($value)));
    }

    /** @return array<int, array<string, mixed>> */
    private function collection(string $path, array $query): array
    {
        $data = $this->responseData($path, $query);
        $data = $data['items'] ?? $data;
        if (! is_array($data)) {
            throw new RuntimeException('Respons koleksi Data Induk tidak valid.');
        }
        return $data;
    }

    /** @return array<string, mixed> */
    private function responseData(string $path, array $query): array
    {
        $this->ensureConfigured();
        try {
            $response = $this->request()->retry(2, 200, static fn (Throwable $exception): bool => $exception instanceof ConnectionException)->get($path, $query)->throw();
        } catch (ConnectionException $exception) {
            throw new RuntimeException('Data Induk tidak dapat dijangkau. Periksa jaringan server.', previous: $exception);
        }
        $data = $response->json('data');
        if (! is_array($data)) {
            throw new RuntimeException('Respons Data Induk tidak valid.');
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
