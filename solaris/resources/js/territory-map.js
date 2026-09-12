const format = (value, digits = 1) => Number(value || 0).toLocaleString('es-GT', { maximumFractionDigits: digits });
const code = value => String(value).padStart(2, '0');

export function createTerritoryExplorer(data, boundaryUrl) {
    const select = document.getElementById('territory-select');
    const departments = new Map(data.departments.map(department => [code(department.code), department]));
    const max = Math.max(0, ...data.departments.map(d => d.reading?.generation_kwh || 0));
    let map, borders;
    const layers = new Map();
    const colors = ['#d5dfaf', '#9aba86', '#5e966d', '#306b50', '#183c34'];
    const text = (id, value) => { document.getElementById(id).textContent = value; };
    const style = feature => {
        const departmentCode = code(feature.properties.code);
        const reading = departments.get(departmentCode)?.reading;
        const selected = select.value === departmentCode;
        const fillColor = !reading || !reading.generation_records ? '#e5e7df' : reading.generation_kwh === 0 ? '#e7cf86' : colors[Math.min(4, Math.ceil(reading.generation_kwh / max * 5) - 1)];
        return { fillColor, fillOpacity: !reading ? .2 : .86, color: selected ? '#c45a32' : '#faf6e9', weight: selected ? 3 : 1.2, opacity: 1 };
    };
    const show = (value, zoom = true) => {
        select.value = value;
        const department = departments.get(value);
        const reading = value ? department?.reading : data.totals;
        text('territory-code', value ? `TERRITORIO / ${value}` : 'LECTURA NACIONAL');
        text('territory-name', department?.name || 'Guatemala');
        text('territory-energy', reading?.generation_records ? format(reading.generation_kwh) : '—');
        text('territory-note', !reading ? 'Fuera de los filtros seleccionados.' : !reading.generation_records ? 'Sin registros de generación para estos filtros.' : 'Lectura según los filtros seleccionados.');
        text('territory-farms', reading ? format(reading.farms, 0) : '—');
        text('territory-capacity', reading ? format(reading.capacity_kw) : '—');
        text('territory-co2', reading?.generation_records ? format(reading.co2_tonnes, 3) : '—');
        const share = reading?.generation_records && data.totals.generation_kwh > 0 ? reading.generation_kwh / data.totals.generation_kwh * 100 : null;
        text('territory-share-value', share === null ? 'Sin base de comparación' : `${format(share)}% del total filtrado`);
        document.getElementById('territory-share-bar').style.width = `${Math.min(100, share || 0)}%`;
        if (borders) {
            borders.setStyle(style);
            const layer = layers.get(value);
            layer?.bringToFront();
            if (zoom) map.fitBounds(layer ? layer.getBounds() : borders.getBounds(), { padding: [24, 24], maxZoom: 10, animate: false });
        }
    };
    select.addEventListener('change', () => show(select.value));
    document.getElementById('territory-reset').addEventListener('click', () => show(''));
    text('territory-scale-note', max > 0 ? `Cinco intervalos iguales: de 0 a ${format(max)} kWh. Escala relativa a los filtros; fuera del filtro, territorio atenuado.` : 'Sin generación positiva. Dorado: 0 kWh registrados. Gris: sin registros. Fuera del filtro: atenuado.');
    show('', false);
    return async leafletMap => {
        map = leafletMap;
        const response = await fetch(boundaryUrl, { signal: AbortSignal.timeout(10000) });
        if (!response.ok) throw new Error('Límites no disponibles');
        const geometry = await response.json();
        borders = window.L.geoJSON(geometry, {
            style,
            onEachFeature: (feature, layer) => {
                const departmentCode = code(feature.properties.code);
                const label = document.createElement('span');
                label.textContent = departments.get(departmentCode)?.name || feature.properties.name;
                layer.bindTooltip(label, { sticky: true, className: 'territory-tooltip' });
                layer.on('click', () => show(departmentCode));
                layer.on('mouseover', () => layer.setStyle({ weight: 3, color: '#c45a32' }));
                layer.on('mouseout', () => layer.setStyle(style(feature)));
                layers.set(departmentCode, layer);
            },
            attribution: '<a href="https://github.com/minfin-bi/Mapas-TopoJSON-Guatemala">Límites: MINFIN</a>',
        }).addTo(map);
        show(select.value);
    };
}
