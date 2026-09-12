export function chartAppearance(theme, reducedMotion = false) {
    return {
        actual: '#62ddb0',
        comparison: theme === 'dark' ? '#c6a5ff' : '#ffbc72',
        fill: 'rgba(98,221,176,.12)',
        text: '#dbe5ff',
        grid: 'rgba(203,217,248,.18)',
        animation: reducedMotion ? false : { duration: 350 },
    };
}

export function applyChartAppearance(chart, theme, reducedMotion) {
    const palette = chartAppearance(theme, reducedMotion);
    chart.data.datasets.forEach((dataset, index) => {
        dataset.borderColor = index === 0 ? palette.actual : palette.comparison;
        dataset.backgroundColor = index === 0 ? palette.fill : 'transparent';
    });
    chart.options.animation = palette.animation;
    for (const axis of ['x', 'y']) {
        chart.options.scales[axis].ticks.color = palette.text;
        chart.options.scales[axis].grid.color = palette.grid;
    }
}
