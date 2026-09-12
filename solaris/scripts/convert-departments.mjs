// Source: https://github.com/minfin-bi/Mapas-TopoJSON-Guatemala/blob/main/deptos.json
// Usage: node scripts/convert-departments.mjs path/to/deptos.json
import fs from 'node:fs';
const topology = JSON.parse(fs.readFileSync(process.argv[2], 'utf8'));
const arcs = topology.arcs.map(arc => {
    let x = 0, y = 0;
    return arc.map(point => {
        x += point[0]; y += point[1];
        return [x * topology.transform.scale[0] + topology.transform.translate[0], y * topology.transform.scale[1] + topology.transform.translate[1]].map(value => Number(value.toFixed(5)));
    });
});
const ring = indexes => indexes.flatMap((index, position) => {
    const points = index < 0 ? [...arcs[~index]].reverse() : arcs[index];
    return position ? points.slice(1) : points;
});
const features = topology.objects.departamentos_gtm.geometries.map(geometry => ({
    type: 'Feature', properties: { code: String(geometry.properties.id / 100).padStart(2, '0'), name: geometry.properties.Departamento },
    geometry: { type: geometry.type, coordinates: geometry.type === 'Polygon' ? geometry.arcs.map(ring) : geometry.arcs.map(polygon => polygon.map(ring)) },
}));
if (features.length !== 22 || new Set(features.map(f => f.properties.code)).size !== 22) throw new Error('Expected 22 distinct departments');
fs.mkdirSync('resources/data', { recursive: true });
fs.writeFileSync('resources/data/guatemala-departments.json', JSON.stringify({ type: 'FeatureCollection', features }));
