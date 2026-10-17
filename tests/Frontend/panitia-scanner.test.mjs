import test from 'node:test';
import assert from 'node:assert/strict';
import { readFile } from 'node:fs/promises';
import vm from 'node:vm';

const source = (await readFile(new URL('../../resources/js/panitia-scanner.js', import.meta.url), 'utf8'))
    .replace(/^import .* from 'html5-qrcode';\r?\n/, '');

function scannerHarness() {
    const listeners = new Map();
    const timers = new Map();
    let timerId = 0;
    const classList = () => {
        const values = new Set();
        return {
            toggle(name, enabled) { enabled ? values.add(name) : values.delete(name); },
            contains(name) { return values.has(name); },
        };
    };
    const document = {
        fullscreenElement: null,
        visibilityState: 'visible',
        documentElement: { classList: classList() },
        body: { classList: classList() },
        addEventListener(name, handler) { listeners.set(name, handler); },
        removeEventListener(name) { listeners.delete(name); },
    };
    const window = {
        localStorage: { getItem() { return null; } },
        setTimeout(callback) { timers.set(++timerId, callback); return timerId; },
    };
    const context = vm.createContext({
        window, document, console: { info() {}, warn() {}, error() {} },
        clearTimeout(id) { timers.delete(id); },
        Html5Qrcode: class {}, Html5QrcodeSupportedFormats: {},
    });
    vm.runInContext(source, context);
    const component = window.qrScannerComponent();
    const container = {};
    component.$refs = {
        scannerContainer: container,
        cameraFrame: { style: { setProperty() {} } },
        qrReader: { addEventListener() {}, removeEventListener() {} },
    };
    component.$el = { dataset: { scanEndpoint: '/scan' }, querySelector() { return null; } };
    component.$nextTick = callback => callback();
    component.startScanner = async () => {};
    component.init();
    return {
        component, container, document, listeners,
        async flushTimers() {
            const pending = [...timers.values()];
            timers.clear();
            for (const callback of pending) await callback();
        },
        assertScrollLock(enabled) {
            assert.equal(document.documentElement.classList.contains('panitia-scanner-fullscreen'), enabled);
            assert.equal(document.body.classList.contains('panitia-scanner-fullscreen'), enabled);
        },
    };
}

test('fullscreen fills the viewport when the browser has no Fullscreen API and exits on a second tap', async () => {
    const h = scannerHarness();
    await h.component.toggleFullscreen();
    assert.equal(h.component.isFullscreen, true);
    h.assertScrollLock(true);
    await h.component.toggleFullscreen();
    assert.equal(h.component.isFullscreen, false);
    h.assertScrollLock(false);
});

test('a rejected native request retains viewport fullscreen and an operable exit button', async () => {
    const h = scannerHarness();
    h.container.requestFullscreen = async () => { throw new Error('Not allowed'); };
    await h.component.toggleFullscreen();
    assert.equal(h.component.isFullscreen, true);
    assert.equal(h.component.fullscreenChanging, false);
    h.assertScrollLock(true);
    await h.component.toggleFullscreen();
    h.assertScrollLock(false);
});

test('native fullscreen and browser-driven exit synchronize controls and scrolling', async () => {
    const h = scannerHarness();
    h.container.requestFullscreen = async () => {
        h.document.fullscreenElement = h.container;
        h.listeners.get('fullscreenchange')();
    };
    await h.component.toggleFullscreen();
    assert.equal(h.component.nativeFullscreen, true);
    h.document.fullscreenElement = null;
    h.listeners.get('fullscreenchange')();
    assert.equal(h.component.isFullscreen, false);
    h.assertScrollLock(false);
});

test('Safari-prefixed fullscreen can be entered and exited', async () => {
    const h = scannerHarness();
    h.container.webkitRequestFullscreen = () => { h.document.webkitFullscreenElement = h.container; };
    h.document.webkitExitFullscreen = () => { h.document.webkitFullscreenElement = null; };
    await h.component.toggleFullscreen();
    assert.equal(h.component.nativeFullscreen, true);
    await h.component.toggleFullscreen();
    assert.equal(h.document.webkitFullscreenElement, null);
    h.assertScrollLock(false);
});

test('unrelated fullscreen events do not close viewport fullscreen', async () => {
    const h = scannerHarness();
    await h.component.toggleFullscreen();
    h.listeners.get('fullscreenchange')();
    assert.equal(h.component.isFullscreen, true);
});

test('Escape exits viewport fullscreen and restores scrolling', async () => {
    const h = scannerHarness();
    await h.component.toggleFullscreen();
    h.listeners.get('keydown')({ key: 'Escape' });
    assert.equal(h.component.isFullscreen, false);
    h.assertScrollLock(false);
});

test('manual entry leaves native fullscreen before showing the dialog and keeps decoding paused', async () => {
    const h = scannerHarness();
    h.document.fullscreenElement = h.container;
    h.component.setFullscreen(true);
    h.document.exitFullscreen = async () => { h.document.fullscreenElement = null; };
    let opened = false;
    h.component.$refs.manualDialog = { showModal() {
        assert.equal(h.document.fullscreenElement, null);
        assert.equal(h.component.pauseReasons.manual, true);
        opened = true;
    } };
    await h.component.openManualDialog();
    assert.equal(opened, true);
    h.assertScrollLock(false);
});

test('layout changes rebuild the decode region once and preserve feedback, zoom and manual pause', async () => {
    const h = scannerHarness();
    h.component.cameraActive = true;
    h.component.zoomValue = 2;
    h.component.pauseReasons.manual = true;
    h.component.feedback = { type: 'success', title: 'Presensi berhasil' };
    let restarts = 0;
    h.component.stopScanner = async () => {};
    h.component.startScanner = async resetFeedback => {
        assert.equal(resetFeedback, false);
        restarts++;
        h.component.zoomValue = 1;
    };
    h.component.refreshScannerLayout();
    h.component.refreshScannerLayout();
    await h.flushTimers();
    assert.equal(restarts, 1);
    assert.equal(h.component.zoomValue, 2);
    assert.equal(h.component.feedback.type, 'success');
    assert.equal(h.component.pauseReasons.manual, true);
    assert.equal(h.component.cameraRestarting, false);
});

test('portrait video covers the camera frame without distorting the QR code for decoding', () => {
    const h = scannerHarness();
    const dimensions = {};
    h.component.$refs.cameraFrame = {
        clientWidth: 390, clientHeight: 780,
        style: { setProperty(name, value) { dimensions[name] = Number.parseFloat(value); } },
    };
    h.component.sizeCameraVideo({ videoWidth: 640, videoHeight: 480 });
    assert.equal(dimensions['--camera-video-height'], 780);
    assert.equal(dimensions['--camera-video-width'] / dimensions['--camera-video-height'], 640 / 480);
    assert.ok(dimensions['--camera-video-width'] >= 390);
});

test('destroy cancels a queued camera restart and unlocks the page', async () => {
    const h = scannerHarness();
    let restarts = 0;
    h.component.startScanner = async () => { restarts++; };
    h.component.refreshScannerLayout();
    await h.component.toggleFullscreen();
    h.component.destroy();
    await h.flushTimers();
    assert.equal(restarts, 0);
    assert.equal(h.listeners.size, 0);
    h.assertScrollLock(false);
});

test('leaving during a camera restart does not try to stop the same camera twice', () => {
    const h = scannerHarness();
    h.component.cameraActive = true;
    h.component.cameraRestarting = true;
    h.component.scanner = { stop() { throw new Error('Camera is already stopping'); } };
    assert.doesNotThrow(() => h.component.destroy());
    h.assertScrollLock(false);
});

test('leaving the page during a pending native request does not leave fullscreen or scrolling locked', async () => {
    const h = scannerHarness();
    let finishRequest;
    h.container.requestFullscreen = () => new Promise(resolve => { finishRequest = resolve; });
    h.document.exitFullscreen = async () => { h.document.fullscreenElement = null; };
    const pending = h.component.toggleFullscreen();
    h.component.destroy();
    h.document.fullscreenElement = h.container;
    finishRequest();
    await pending;
    assert.equal(h.document.fullscreenElement, null);
    h.assertScrollLock(false);
});
