import test from 'node:test';
import assert from 'node:assert/strict';
import { chartAppearance, applyChartAppearance } from '../../resources/js/chart-appearance.js';

test('reduced motion disables animation in either theme', () => {
    for (const theme of ['light', 'dark']) assert.equal(chartAppearance(theme, true).animation, false);
});

test('changing appearance preserves values and dashed comparison', () => {
    const chart = { data: { datasets: [{data:[12,15]}, {data:[20,25],borderDash:[5,4]}] }, options: {scales:{x:{ticks:{},grid:{}},y:{ticks:{},grid:{}}}} };
    applyChartAppearance(chart, 'dark', true);
    assert.deepEqual(chart.data.datasets[0].data, [12,15]);
    assert.deepEqual(chart.data.datasets[1].borderDash, [5,4]);
    assert.equal(chart.data.datasets[1].borderColor, '#c6a5ff');
    assert.equal(chart.options.scales.y.ticks.color, '#dbe5ff');
    applyChartAppearance(chart, 'light', false);
    assert.equal(chart.data.datasets[1].borderColor, '#ffbc72');
});
