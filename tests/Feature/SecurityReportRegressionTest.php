<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class SecurityReportRegressionTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Route::middleware('web')->get('/_test/security', fn () => response('ok')->header('X-Powered-By', 'PHP/test'));
        Route::middleware('web')->post('/_test/security', fn () => response('accepted'));
        config(['app.url' => 'https://kajian.griyaquran.web.id', 'session.secure' => true]);
    }

    public function test_csp_and_httponly_cookies_are_emitted_without_exposing_php(): void
    {
        $response = $this->get('https://kajian.griyaquran.web.id/_test/security');
        $response->assertOk()->assertHeader('Content-Security-Policy')->assertHeaderMissing('X-Powered-By');
        $this->assertStringContainsString("object-src 'none'", $response->headers->get('Content-Security-Policy'));
        foreach (['XSRF-TOKEN', config('session.cookie')] as $name) {
            $cookie = collect($response->headers->getCookies())->first(fn ($cookie) => $cookie->getName() === $name);
            $this->assertNotNull($cookie, $name);
            $this->assertTrue($cookie->isHttpOnly(), $name);
            $this->assertTrue($cookie->isSecure(), $name);
        }
    }

    public function test_production_redirect_uses_configured_origin_and_preserves_path_query(): void
    {
        $this->app['env'] = 'production';
        $this->get('http://attacker.example/_test/security?next=one%20two')
            ->assertStatus(301)->assertRedirect('https://kajian.griyaquran.web.id/_test/security?next=one%20two');
        $this->post('http://kajian.griyaquran.web.id/_test/security')
            ->assertStatus(308)->assertRedirect('https://kajian.griyaquran.web.id/_test/security');
    }

    public function test_production_trusted_proxy_does_not_cause_redirect_loop(): void
    {
        $this->app['env'] = 'production';
        $this->withServerVariables(['REMOTE_ADDR' => '172.20.0.2', 'HTTPS' => false, 'SERVER_PORT' => '80'])
            ->withHeader('X-Forwarded-Proto', 'https')
            ->get('http://kajian.griyaquran.web.id/_test/security')
            ->assertOk()->assertHeader('Strict-Transport-Security', 'max-age=31536000');
    }

    public function test_untrusted_client_cannot_bypass_https_using_forwarded_header(): void
    {
        $this->app['env'] = 'production';
        $this->withServerVariables(['REMOTE_ADDR' => '203.0.113.10', 'HTTPS' => false, 'SERVER_PORT' => '80'])
            ->withHeader('X-Forwarded-Proto', 'https')
            ->get('http://kajian.griyaquran.web.id/_test/security')->assertStatus(301);
    }

    public function test_csrf_still_rejects_missing_tokens_and_accepts_meta_header(): void
    {
        // Production enables real CSRF validation (Laravel bypasses it in testing).
        $this->app['env'] = 'production';
        $url = 'https://kajian.griyaquran.web.id/_test/security';
        $this->withSession(['_token' => 'existing-token'])->post($url)->assertStatus(419);
        $this->withSession(['_token' => 'existing-token'])->withHeader('X-CSRF-TOKEN', 'existing-token')
            ->post($url)->assertOk()->assertSee('accepted');
        $this->assertSame('existing-token', session()->token());
    }
}
