<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class InjectPwaInstallPrompt
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (!$this->shouldInject($request, $response)) {
            return $response;
        }

        $content = $response->getContent();
        if (!is_string($content) || !str_contains($content, '</body>')) {
            return $response;
        }

        if (str_contains($content, 'kajian-pwa-install-middleware')) {
            return $response;
        }

        if (!str_contains($content, 'rel="manifest"')) {
            $content = str_replace('</head>', $this->headHtml() . '</head>', $content);
        }

        $response->setContent(str_replace('</body>', $this->bodyHtml() . '</body>', $content));

        return $response;
    }

    private function shouldInject(Request $request, Response $response): bool
    {
        if (!$response->isSuccessful()) {
            return false;
        }

        if (
            $request->expectsJson() ||
            $request->is('__deploy-version') ||
            $request->is('admin') ||
            $request->is('admin/*') ||
            $request->is('panitia') ||
            $request->is('panitia/*') ||
            $request->is('livewire/*') ||
            $request->headers->has('X-Livewire') ||
            $request->headers->has('X-Livewire-Navigate')
        ) {
            return false;
        }

        $contentType = (string) $response->headers->get('Content-Type');

        return str_contains($contentType, 'text/html') || $contentType === '';
    }

    private function headHtml(): string
    {
        return <<<'HTML'
<link rel="manifest" href="/manifest.json">
<meta name="mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
<meta name="apple-mobile-web-app-title" content="Kajian Walsan">
<link rel="apple-touch-icon" href="/icons/icon-192x192.png">
<script>
window.pwaDeferredPrompt = window.pwaDeferredPrompt || null;
window.addEventListener('beforeinstallprompt', function(event) {
    event.preventDefault();
    window.pwaDeferredPrompt = event;
    window.dispatchEvent(new CustomEvent('kajian-pwa-ready-to-install'));
});
</script>
HTML;
    }

    private function bodyHtml(): string
    {
        return <<<'HTML'
<div id="kajian-pwa-install-middleware" style="position:fixed;left:16px;right:16px;bottom:16px;z-index:2147483647;max-width:430px;margin:0 auto;font-family:Inter,system-ui,-apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif">
    <div style="overflow:hidden;border-radius:24px;background:#fff;border:1px solid rgba(16,185,129,.18);box-shadow:0 25px 60px rgba(0,0,0,.24)">
        <div style="display:flex;align-items:center;gap:14px;padding:16px;background:linear-gradient(135deg,#059669,#047857);color:#fff">
            <img src="/icons/icon-96x96.png" alt="" style="width:46px;height:46px;border-radius:14px;background:rgba(255,255,255,.2);padding:5px">
            <div style="min-width:0;flex:1">
                <div style="font-size:16px;font-weight:850;line-height:1.2">Install di HP</div>
                <div style="margin-top:3px;font-size:12px;font-weight:650;color:rgba(255,255,255,.86);white-space:nowrap;overflow:hidden;text-overflow:ellipsis">Akses cepat dan notifikasi otomatis</div>
            </div>
            <button type="button" data-kajian-pwa-close style="width:34px;height:34px;border:0;border-radius:12px;background:rgba(255,255,255,.1);color:#fff;font-size:22px;line-height:1;cursor:pointer">&times;</button>
        </div>
        <div style="padding:16px">
            <p style="margin:0 0 14px;color:#374151;font-size:14px;font-weight:650;line-height:1.5">Install di HP untuk akses lebih cepat dan menerima notifikasi pengumuman otomatis.</p>
            <div style="display:flex;gap:10px">
                <button type="button" data-kajian-pwa-later style="flex:1;border:0;border-radius:14px;background:#f3f4f6;color:#6b7280;padding:11px 12px;font-size:14px;font-weight:800;cursor:pointer">Nanti Saja</button>
                <button type="button" data-kajian-pwa-install style="flex:1;border:0;border-radius:14px;background:linear-gradient(135deg,#10b981,#059669);color:#fff;padding:11px 12px;font-size:14px;font-weight:850;cursor:pointer">Install Sekarang</button>
            </div>
        </div>
    </div>
</div>
<script>
(function(){
    var key = 'kajian-pwa-install-dismissed-v1';
    var promptEvent = window.pwaDeferredPrompt || null;
    var box = document.getElementById('kajian-pwa-install-middleware');
    var installButton = document.querySelector('[data-kajian-pwa-install]');
    function installed(){ return window.matchMedia('(display-mode: standalone)').matches || window.navigator.standalone === true; }
    function dismissed(){ var value = localStorage.getItem(key); return value && Date.now() - parseInt(value, 10) < 12 * 60 * 60 * 1000; }
    function hide(){ if (box) box.style.display = 'none'; }
    function setReady(){
        promptEvent = window.pwaDeferredPrompt || promptEvent;
        if (installButton && promptEvent) installButton.textContent = 'Install Sekarang';
    }
    if (installed() || dismissed()) hide();
    window.addEventListener('beforeinstallprompt', function(event){ event.preventDefault(); promptEvent = event; window.pwaDeferredPrompt = event; setReady(); });
    window.addEventListener('kajian-pwa-ready-to-install', setReady);
    window.addEventListener('appinstalled', function(){ localStorage.removeItem(key); hide(); });
    document.querySelectorAll('[data-kajian-pwa-close],[data-kajian-pwa-later]').forEach(function(button){
        button.addEventListener('click', function(){ localStorage.setItem(key, Date.now().toString()); hide(); });
    });
    if (installButton) installButton.addEventListener('click', async function(){
        var ios = /iPad|iPhone|iPod/.test(navigator.userAgent) && !window.MSStream;
        promptEvent = window.pwaDeferredPrompt || promptEvent;
        if (promptEvent) {
            promptEvent.prompt();
            await promptEvent.userChoice;
            promptEvent = null;
            window.pwaDeferredPrompt = null;
            return;
        }
        if (ios) alert('Untuk install di iPhone: tekan tombol Share di Safari, lalu pilih Add to Home Screen.');
        else alert('Untuk install aplikasi: buka menu browser titik tiga, lalu pilih Install app atau Add to Home screen.');
    });
    if ('serviceWorker' in navigator) navigator.serviceWorker.register('/sw.js').catch(function(){});
    setReady();
})();
</script>
HTML;
    }
}
