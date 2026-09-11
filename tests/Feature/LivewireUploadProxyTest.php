<?php

namespace Tests\Feature;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class LivewireUploadProxyTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Route::get('/_test/livewire-upload-url', function (Request $request) {
            return response()->json([
                'secure' => $request->isSecure(),
                'url' => URL::temporarySignedRoute(
                    'livewire.upload-file',
                    now()->addMinute()
                ),
            ]);
        });
    }

    public function test_https_forwarded_by_private_proxy_is_used_for_livewire_signed_upload_url(): void
    {
        $response = $this->withServerVariables([
            'REMOTE_ADDR' => '172.20.0.2',
            'SERVER_PORT' => '80',
            'HTTPS' => false,
        ])->withHeader('X-Forwarded-Proto', 'https')
            ->get('http://kajian.griyaquran.web.id/_test/livewire-upload-url');

        $response->assertOk()
            ->assertJsonPath('secure', true);

        $this->assertSame('https', parse_url($response->json('url'), PHP_URL_SCHEME));
    }

    public function test_public_client_cannot_spoof_forwarded_https_scheme(): void
    {
        $response = $this->withServerVariables([
            'REMOTE_ADDR' => '203.0.113.10',
            'SERVER_PORT' => '80',
            'HTTPS' => false,
        ])->withHeader('X-Forwarded-Proto', 'https')
            ->get('http://kajian.griyaquran.web.id/_test/livewire-upload-url');

        $response->assertOk()
            ->assertJsonPath('secure', false);

        $this->assertSame('http', parse_url($response->json('url'), PHP_URL_SCHEME));
    }
}
