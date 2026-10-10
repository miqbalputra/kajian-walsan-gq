<?php

namespace Tests\Feature;

use App\Http\Middleware\InjectPwaInstallPrompt;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class PwaInstallPromptTest extends TestCase
{
    #[DataProvider('entryRoutes')]
    public function test_entry_pages_register_pwa_without_an_install_banner(string $path, string $name): void
    {
        $response = $this->inject($path, $name);
        $this->assertStringContainsString('rel="manifest"', $response->getContent());
        $this->assertStringContainsString("serviceWorker.register('/sw.js')", $response->getContent());
        $this->assertStringContainsString('beforeinstallprompt', $response->getContent());
        $this->assertStringNotContainsString('kajian-pwa-install-middleware', $response->getContent());
    }

    public static function entryRoutes(): array
    {
        return [['/', 'home'], ['/login', 'login']];
    }

    public function test_dashboard_keeps_the_install_prompt(): void
    {
        $response = $this->inject('/wali-santri', 'wali-santri.dashboard');
        $this->assertStringContainsString('kajian-pwa-install-middleware', $response->getContent());
        $this->assertSame(1, substr_count($response->getContent(), "serviceWorker.register('/sw.js')"));
    }

    public function test_bootstrap_and_banner_are_not_injected_twice(): void
    {
        $request = $this->request('/wali-santri', 'wali-santri.dashboard');
        $middleware = new InjectPwaInstallPrompt;
        $first = $middleware->handle($request, fn () => $this->html());
        $second = $middleware->handle($request, fn () => clone $first);
        $this->assertSame($first->getContent(), $second->getContent());
    }

    public function test_json_and_failed_responses_are_left_untouched(): void
    {
        $middleware = new InjectPwaInstallPrompt;
        $json = response()->json(['ok' => true]);
        $this->assertSame($json->getContent(), $middleware->handle(Request::create('/test'), fn () => $json)->getContent());
        $failure = response('Unavailable', 503);
        $this->assertSame('Unavailable', $middleware->handle(Request::create('/test'), fn () => $failure)->getContent());
    }

    private function inject(string $path, string $name): Response
    {
        return (new InjectPwaInstallPrompt)->handle($this->request($path, $name), fn () => $this->html());
    }

    private function request(string $path, string $name): Request
    {
        $request = Request::create($path);
        $route = (new Route('GET', $path, fn () => null))->name($name);
        $request->setRouteResolver(fn () => $route);

        return $request;
    }

    private function html(): Response
    {
        return response('<html><head></head><body><main>Page</main></body></html>');
    }
}
