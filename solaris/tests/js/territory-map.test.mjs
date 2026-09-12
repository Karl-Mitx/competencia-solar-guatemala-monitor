import test from 'node:test';
import assert from 'node:assert/strict';
import fs from 'node:fs';
import { createTerritoryExplorer } from '../../resources/js/territory-map.js';

test('department selector works without Leaflet, handles empty readings and resets', () => {
    const elements = new Map();
    const originalDocument = globalThis.document;
    globalThis.document = { getElementById(id) {
        if (!elements.has(id)) elements.set(id, { textContent: '', value: '', style: {}, handlers: {}, addEventListener(event, handler) { this.handlers[event] = handler; } });
        return elements.get(id);
    } };
    try {
        const totals = { generation_records: 1, generation_kwh: 200, farms: 2, capacity_kw: 30, co2_tonnes: .08 };
        createTerritoryExplorer({ totals, departments: [
            { code: '01', name: 'Guatemala', reading: { ...totals, generation_kwh: 100 } },
            { code: '02', name: 'El Progreso', reading: { ...totals, generation_records: 0, generation_kwh: 0 } },
            { code: '03', name: 'Sacatepéquez', reading: null },
            { code: '04', name: 'Chimaltenango', reading: { ...totals, generation_kwh: 0 } },
        ] }, '/boundaries.json');
        const select = elements.get('territory-select');
        const change = value => { select.value = value; select.handlers.change(); };
        change('01');
        assert.equal(elements.get('territory-share-bar').style.width, '50%');
        change('02');
        assert.equal(elements.get('territory-energy').textContent, '—');
        assert.match(elements.get('territory-note').textContent, /Sin registros/);
        change('03');
        assert.match(elements.get('territory-note').textContent, /Fuera de los filtros/);
        change('04');
        assert.equal(elements.get('territory-energy').textContent, '0');
        elements.get('territory-reset').handlers.click();
        assert.equal(select.value, '');
        assert.equal(elements.get('territory-share-bar').style.width, '100%');
    } finally { globalThis.document = originalDocument; }
});

test('geography has 22 departments with closed rings inside Guatemala reference bounds', () => {
    const geo = JSON.parse(fs.readFileSync(new URL('../../resources/data/guatemala-departments.json', import.meta.url)));
    assert.equal(geo.features.length, 22);
    assert.equal(new Set(geo.features.map(feature => feature.properties.code)).size, 22);
    for (const feature of geo.features) {
        const polygons = feature.geometry.type === 'Polygon' ? [feature.geometry.coordinates] : feature.geometry.coordinates;
        for (const polygon of polygons) for (const ring of polygon) {
            assert.ok(ring.length >= 4);
            assert.deepEqual(ring[0], ring.at(-1));
            for (const [longitude, latitude] of ring) assert.ok(longitude > -93 && longitude < -88 && latitude > 13 && latitude < 19);
        }
    }
});
