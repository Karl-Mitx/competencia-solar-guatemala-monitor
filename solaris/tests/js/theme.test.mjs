import test from 'node:test';
import assert from 'node:assert/strict';
import vm from 'node:vm';
import { readFileSync } from 'node:fs';

const source = readFileSync(new URL('../../public/theme.js', import.meta.url), 'utf8');
function page({ stored = null, cookie = '', blocked = false, systemDark = false } = {}) {
    const document = { cookie, documentElement: { dataset: {}, style: {} } };
    const context = { document, window: {}, location: { protocol: 'https:' }, matchMedia: () => ({ matches: systemDark }), localStorage: {
        getItem() { if (blocked) throw Error('blocked'); return stored; },
        setItem(key, value) { if (blocked) throw Error('blocked'); stored = value; },
    } };
    vm.runInNewContext(source, context);
    return context;
}
test('saved choice takes precedence over system preference', () => {
    assert.equal(page({ stored: 'light', systemDark: true }).document.documentElement.dataset.theme, 'light');
    assert.equal(page({ stored: 'dark' }).document.documentElement.style.colorScheme, 'dark');
});
test('cookie restores preference when browser storage is blocked', () => {
    const first = page({ blocked: true });
    first.window.solarisTheme.set('dark');
    assert.match(first.document.cookie, /SameSite=Lax; Secure/);
    const reloaded = page({ blocked: true, cookie: first.document.cookie });
    assert.equal(reloaded.document.documentElement.dataset.theme, 'dark');
});
test('invalid stored values fall back safely and invalid updates are ignored', () => {
    const context = page({ stored: 'bogus', systemDark: true });
    assert.equal(context.document.documentElement.dataset.theme, 'dark');
    context.window.solarisTheme.set('bogus');
    assert.equal(context.document.documentElement.dataset.theme, 'dark');
});
