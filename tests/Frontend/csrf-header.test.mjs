import test from 'node:test';
import assert from 'node:assert/strict';
import { readFile } from 'node:fs/promises';
import vm from 'node:vm';
import axios from 'axios';

const source = (await readFile(new URL('../../resources/js/bootstrap.js', import.meta.url), 'utf8'))
    .replace("import axios from 'axios';", '');

async function request(config, token = 'session-meta-token') {
    const client = axios.create();
    vm.runInNewContext(source, {
        axios: client,
        URL,
        window: { location: { href: 'https://kajian.example/admin', origin: 'https://kajian.example' } },
        document: { querySelector: () => token ? { content: token } : null },
    });
    return client.request({
        method: 'POST',
        ...config,
        adapter: async outgoing => ({ data: outgoing.headers.toJSON(), status: 200, statusText: 'OK', headers: {}, config: outgoing }),
    });
}

test('same-origin POST uses the meta CSRF token without reading a cookie', async () => {
    const result = await request({ url: '/save' });
    assert.equal(result.data['X-CSRF-TOKEN'], 'session-meta-token');
});

test('relative API base URL keeps CSRF protection', async () => {
    const result = await request({ url: 'save', baseURL: '/api/' });
    assert.equal(result.data['X-CSRF-TOKEN'], 'session-meta-token');
});

test('external origins do not receive the session CSRF token', async () => {
    for (const url of ['https://outside.example/save', '//outside.example/save']) {
        const result = await request({ url });
        assert.equal(result.data['X-CSRF-TOKEN'], undefined);
    }
});

test('pages without a CSRF meta tag do not send an invalid token', async () => {
    const result = await request({ url: '/save' }, null);
    assert.equal(result.data['X-CSRF-TOKEN'], undefined);
});
