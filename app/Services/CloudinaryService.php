<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class CloudinaryService
{
    protected string $cloudName;
    protected string $apiKey;
    protected string $apiSecret;
    protected string $folder;
    protected bool $enabled;

    public function __construct()
    {
        $this->cloudName = config('cloudinary.cloud_name');
        $this->apiKey = config('cloudinary.api_key');
        $this->apiSecret = config('cloudinary.api_secret');
        $this->folder = config('cloudinary.folder');
        $this->enabled = config('cloudinary.enabled', false);
    }

    /**
     * Upload file - ke Cloudinary jika enabled, atau ke local storage.
     *
     * @param UploadedFile $file File yang diupload
     * @param string $subfolder Subfolder tujuan (e.g., 'attendance-proofs')
     * @return array{url: string, is_cloud: bool}
     */
    public function upload(UploadedFile $file, string $subfolder = 'uploads'): array
    {
        if (!$this->enabled || empty($this->cloudName)) {
            // Fallback ke local storage
            $path = $file->store($subfolder, 'public');
            return [
                'url' => $path,
                'is_cloud' => false,
            ];
        }

        return $this->uploadToCloudinary($file, $subfolder);
    }

    /**
     * Upload file ke Cloudinary via REST API.
     */
    protected function uploadToCloudinary(UploadedFile $file, string $subfolder): array
    {
        $timestamp = time();
        $folder = $this->folder . '/' . $subfolder;

        // Determine resource type — saat ini hanya image (PDF dinonaktifkan)
        $extension = strtolower($file->getClientOriginalExtension());
        $resourceType = in_array($extension, ['pdf', 'doc', 'docx']) ? 'raw' : 'image';

        // Transformation only applies to images, NOT raw files
        $quality = config('cloudinary.quality', 'auto:eco');
        $maxWidth = config('cloudinary.max_width', 1200);
        $maxHeight = config('cloudinary.max_height', 1200);
        $transformation = ($resourceType === 'image')
            ? "q_{$quality},w_{$maxWidth},h_{$maxHeight},c_limit"
            : null;

        // Generate signature - only include params that will be sent
        $params = [
            'folder'    => $folder,
            'timestamp' => $timestamp,
        ];
        if ($transformation) {
            $params['transformation'] = $transformation;
        }

        ksort($params);
        $signatureString = collect($params)
            ->map(fn($value, $key) => "{$key}={$value}")
            ->implode('&');
        $signatureString .= $this->apiSecret;
        $signature = sha1($signatureString);

        // Build POST payload
        $payload = [
            'api_key'   => $this->apiKey,
            'timestamp' => $timestamp,
            'signature' => $signature,
            'folder'    => $folder,
        ];
        if ($transformation) {
            $payload['transformation'] = $transformation;
        }

        try {
            $response = Http::timeout(30)
                ->attach('file', file_get_contents($file->getRealPath()), $file->getClientOriginalName())
                ->post("https://api.cloudinary.com/v1_1/{$this->cloudName}/{$resourceType}/upload", $payload);

            if ($response->successful()) {
                $data = $response->json();
                $secureUrl = $data['secure_url'] ?? $data['url'] ?? null;

                if ($secureUrl) {
                    Log::info('[Cloudinary] Upload success', [
                        'public_id'     => $data['public_id'] ?? '',
                        'resource_type' => $resourceType,
                        'bytes'         => $data['bytes'] ?? 0,
                        'format'        => $data['format'] ?? '',
                        'url'           => $secureUrl,
                    ]);

                    return [
                        'url'      => $secureUrl,
                        'is_cloud' => true,
                    ];
                }
            }

            // Log full Cloudinary error response
            Log::error('[Cloudinary] Upload failed', [
                'resource_type' => $resourceType,
                'extension'     => $extension,
                'status'        => $response->status(),
                'body'          => $response->body(),
            ]);

        } catch (\Exception $e) {
            Log::error('[Cloudinary] Exception during upload', [
                'message' => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
            ]);
        }

        // Fallback to local storage on failure
        Log::warning('[Cloudinary] Falling back to local storage', [
            'subfolder' => $subfolder,
        ]);
        $path = $file->store($subfolder, 'public');
        return [
            'url'      => $path,
            'is_cloud' => false,
        ];
    }

    /**
     * Delete file dari Cloudinary.
     *
     * @param string $publicId Cloudinary Public ID
     * @return bool
     */
    public function delete(string $publicId): bool
    {
        if (!$this->enabled || empty($this->cloudName)) {
            return false;
        }

        $timestamp = time();
        $signatureString = "public_id={$publicId}&timestamp={$timestamp}" . $this->apiSecret;
        $signature = sha1($signatureString);

        try {
            $response = Http::post("https://api.cloudinary.com/v1_1/{$this->cloudName}/image/destroy", [
                'public_id' => $publicId,
                'api_key' => $this->apiKey,
                'timestamp' => $timestamp,
                'signature' => $signature,
            ]);

            return $response->successful() && ($response->json('result') === 'ok');
        } catch (\Exception $e) {
            Log::error('[Cloudinary] Delete failed', ['message' => $e->getMessage()]);
            return false;
        }
    }

    /**
     * Check apakah URL adalah Cloudinary URL.
     */
    public static function isCloudinaryUrl(?string $url): bool
    {
        if (empty($url))
            return false;
        return str_contains($url, 'res.cloudinary.com') || str_starts_with($url, 'https://res.cloudinary.com');
    }

    /**
     * Get displayable URL (works for both local and Cloudinary files).
     */
    public static function getDisplayUrl(?string $path): string
    {
        if (empty($path))
            return '';

        // If it's already a full URL (Cloudinary), return as-is
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        // Local file - use storage URL
        return asset('storage/' . $path);
    }
}
