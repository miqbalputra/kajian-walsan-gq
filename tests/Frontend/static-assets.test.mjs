// Run against a local Nginx instance using STATIC_TEST_ORIGIN=http://127.0.0.1:18080.
import test from 'node:test';
import assert from 'node:assert/strict';
import { readFile } from 'node:fs/promises';
import { request } from 'node:http';
import { gunzipSync } from 'node:zlib';

const origin = process.env.STATIC_TEST_ORIGIN;
const manifest = JSON.parse(await readFile(new URL('../../public/build/manifest.json', import.meta.url), 'utf8'));
const options = { skip: !origin };
function get(pathname, headers = {}) {
    return new Promise((resolve, reject) => {
        const req = request(new URL(pathname, origin), { headers }, (response) => {
            const chunks = [];
            response.on('data', (chunk) => chunks.push(chunk));
            response.on('end', () => resolve({ status: response.statusCode, headers: response.headers, body: Buffer.concat(chunks) }));
        });
        req.setTimeout(10000, () => req.destroy(new Error('Asset request timed out.')));
        req.on('error', reject);
        req.end();
    });
}

test('fingerprinted CSS is gzip-compressed behind a proxy, immutable, and retains security headers', options, async () => {
    const url = '/build/' + manifest['resources/css/app.css'].file;
    const plain = await get(url);
    const compressed = await get(url, { 'accept-encoding': 'gzip', via: '1.1 performance-test' });
    assert.equal(compressed.status, 200);
    assert.equal(compressed.headers['content-encoding'], 'gzip');
    assert.match(compressed.headers.vary, /Accept-Encoding/i);
    assert.equal(compressed.headers['cache-control'], 'public, max-age=31536000, immutable');
    assert.equal(compressed.headers['x-frame-options'], 'SAMEORIGIN');
    assert.equal(compressed.headers['x-content-type-options'], 'nosniff');
    assert.deepEqual(gunzipSync(compressed.body), plain.body);
    assert.ok(compressed.body.length < plain.body.length / 3);
    console.log(JSON.stringify({ cssBytes: plain.body.length, cssTransferredBytes: compressed.body.length }));
});

test('local icon font is served as WOFF2 with immutable cache', options, async () => {
    const response = await get('/build/' + manifest['resources/fonts/wali-symbols.woff2'].file);
    assert.equal(response.status, 200);
    assert.equal(response.body.subarray(0, 4).toString(), 'wOF2');
    assert.match(response.headers['content-type'], /woff2/);
    assert.match(response.headers['cache-control'], /immutable/);
});

test('unchanged asset can be revalidated without downloading the body again', options, async () => {
    const url = '/build/' + manifest['resources/css/app.css'].file;
    const first = await get(url);
    const revalidated = await get(url, { 'if-none-match': first.headers.etag });
    assert.equal(revalidated.status, 304);
    assert.equal(revalidated.body.length, 0);
    assert.match(revalidated.headers['cache-control'], /immutable/);
});

test('missing build assets are not stored as immutable successful responses', options, async () => {
    const response = await get('/build/assets/missing-abcdefgh.js');
    assert.equal(response.status, 404);
    assert.doesNotMatch(response.headers['cache-control'] || '', /immutable/);
});

test('mutable push script is compressed with a shorter cache lifetime', options, async () => {
    const response = await get('/js/pwa-push.js', { 'accept-encoding': 'gzip' });
    assert.equal(response.status, 200);
    assert.equal(response.headers['content-encoding'], 'gzip');
    assert.equal(response.headers['cache-control'], 'public, max-age=3600, must-revalidate');
});

test('service worker remains revalidatable rather than immutable', options, async () => {
    const response = await get('/sw.js');
    assert.equal(response.status, 200);
    assert.equal(response.headers['cache-control'], 'no-cache');
});

test('dynamic HTML retains private cache headers and does not receive static gzip policy', options, async () => {
    const response = await get('/login', { 'accept-encoding': 'gzip' });
    assert.equal(response.status, 200);
    assert.match(response.headers['cache-control'], /private/);
    assert.doesNotMatch(response.headers['cache-control'], /immutable|max-age=31536000/);
    assert.equal(response.headers['content-encoding'], undefined);
});
