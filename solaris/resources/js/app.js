import './bootstrap';
import { createTerritoryExplorer } from './territory-map';
import boundaryUrl from '../data/guatemala-departments.json?url';

const loadScript = (src) => new Promise((resolve, reject) => {
    const existing = document.querySelector(`script[src="${src}"]`);
    if (existing) { if (existing.dataset.loaded === 'true') return resolve(); existing.addEventListener('load', resolve, { once: true }); existing.addEventListener('error', reject, { once: true }); return; }
    const script = document.createElement('script'); script.src = src; script.async = true; script.onload = () => { script.dataset.loaded = 'true'; resolve(); }; script.onerror = reject; document.head.appendChild(script);
});
const readJson = (id) => { const el = document.getElementById(id); if (!el) return null; try { return JSON.parse(el.textContent); } catch { return null; } };
const escapeHtml = (value) => String(value ?? '').replace(/[&<>'"]/g, (char) => ({ '&':'&amp;', '<':'&lt;', '>':'&gt;', "'":'&#39;', '"':'&quot;' }[char]));

function initMenu() {
    const themeButton = document.querySelector('[data-theme-toggle]');
    const updateTheme = () => {
        const dark = document.documentElement.dataset.theme === 'dark';
        themeButton?.setAttribute('aria-pressed', String(dark));
        const label = document.querySelector('[data-theme-label]');
        if (label) label.textContent = dark ? 'Usar modo claro' : 'Usar modo oscuro';
    };
    themeButton?.addEventListener('click', () => {
        const theme = document.documentElement.dataset.theme === 'dark' ? 'light' : 'dark';
        document.documentElement.dataset.theme = theme;
        try { localStorage.setItem('solaris-theme', theme); } catch { /* Private browsing can disable storage. */ }
        updateTheme();
    });
    updateTheme();
    const sidebar = document.getElementById('sidebar'); const scrim = document.querySelector('.sidebar-scrim');
    const toggle = document.querySelector('[data-menu-toggle]');
    const setOpen = (open) => { sidebar?.classList.toggle('open', open); scrim?.classList.toggle('open', open); toggle?.setAttribute('aria-expanded', String(open)); };
    toggle?.addEventListener('click', () => setOpen(!sidebar?.classList.contains('open')));
    document.querySelector('[data-menu-close]')?.addEventListener('click', () => setOpen(false));
    document.addEventListener('keydown', (event) => { if (event.key === 'Escape' && sidebar?.classList.contains('open')) { setOpen(false); toggle?.focus(); } });
}

async function initMap() {
    const mapElement = document.getElementById('solar-map'); const farms = readJson('map-data'); if (!mapElement || !farms) return;
    const territoryData = readJson('territory-data');
    const attachTerritories = territoryData ? createTerritoryExplorer(territoryData, boundaryUrl) : null;
    try {
        await loadScript('https://unpkg.com/leaflet@1.9.4/dist/leaflet.js'); if (!window.L) throw new Error('Leaflet no disponible');
        const map = L.map(mapElement, { scrollWheelZoom:false, minZoom:6, maxZoom:16 }).setView([15.1,-90.25],7);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { attribution:'© OpenStreetMap contributors', maxZoom:19 }).addTo(map);
        if (attachTerritories) {
            try { await attachTerritories(map); } catch { document.querySelector('.map-error')?.removeAttribute('hidden'); }
        }
        const points=[];
        farms.forEach((farm) => {
            if (!Number.isFinite(Number(farm.latitude)) || !Number.isFinite(Number(farm.longitude))) return;
            const size = Math.min(34, 20 + Math.sqrt(Math.max(0, Number(farm.capacity_kw))) / 2);
            L.marker([farm.latitude,farm.longitude], { title:farm.name, icon:L.divIcon({ className:'solar-farm-icon', html:'<span aria-hidden="true">✳</span>', iconSize:[size,size], iconAnchor:[size/2,size/2] }) }).bindPopup(`<div class="popup-title">${escapeHtml(farm.name)}</div><div class="popup-meta">${escapeHtml(farm.department)}<br>${Number(farm.capacity_kw).toFixed(1)} kW · ${Number(farm.panels).toLocaleString('es-GT')} paneles<br>${Number(farm.generation_kwh).toLocaleString('es-GT')} kWh registrados</div><a class="popup-link" href="${escapeHtml(farm.url)}">Ver ficha de granja →</a>`).addTo(map); points.push([farm.latitude,farm.longitude]);
        });
        if (!attachTerritories && points.length>1) map.fitBounds(points,{padding:[25,25]}); else if (!attachTerritories && points.length===1) map.setView(points[0],10); setTimeout(()=>map.invalidateSize(),250);
        if (window.ResizeObserver) new ResizeObserver(() => map.invalidateSize()).observe(mapElement);
    } catch { document.querySelector('.map-error')?.removeAttribute('hidden'); }
}

async function initCharts() {
    const canvases=[...document.querySelectorAll('canvas[data-chart]')]; if (!canvases.length) return;
    try {
        await loadScript('https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js'); if (!window.Chart) throw new Error('Chart.js no disponible');
        canvases.forEach((canvas) => {
            const source=readJson(canvas.dataset.source); if (!source) return; const isProjection=canvas.dataset.chart==='projection'; let labels=[]; let datasets=[];
            if (isProjection) { const history=source.history||[], forecast=source.forecast||[]; labels=[...new Set([...history.map(x=>x.period),...forecast.map(x=>x.period)])].sort(); datasets=[{label:'Generación real',data:labels.map(l=>history.find(x=>x.period===l)?.real_kwh??null),borderColor:'#267456',backgroundColor:'rgba(105,129,82,.12)',fill:true,tension:.35,pointRadius:3},{label:'Proyección',data:labels.map(l=>forecast.find(x=>x.period===l)?.projected_kwh??null),borderColor:'#c45a32',backgroundColor:'transparent',borderDash:[6,4],fill:false,tension:.35,pointRadius:3,spanGaps:true}]; }
            else { labels=source.map(x=>x.label||x.period); datasets=[{label:'Generación real',data:source.map(x=>x.real_kwh),borderColor:'#267456',backgroundColor:'rgba(105,129,82,.14)',fill:true,tension:.35,pointRadius:2},{label:'Generación esperada',data:source.map(x=>x.expected_kwh),borderColor:'#998566',backgroundColor:'transparent',borderDash:[5,4],fill:false,tension:.35,pointRadius:0}]; }
            new Chart(canvas,{type:'line',data:{labels,datasets},options:{responsive:true,maintainAspectRatio:false,interaction:{intersect:false,mode:'index'},plugins:{legend:{display:false},tooltip:{callbacks:{label:(ctx)=>`${ctx.dataset.label}: ${Number(ctx.parsed.y??0).toLocaleString('es-GT')} kWh`}}},scales:{x:{grid:{display:false},ticks:{color:'#788986',font:{size:10},maxRotation:0}},y:{beginAtZero:true,grid:{color:'#edf1ef'},ticks:{color:'#788986',font:{size:10},callback:(value)=>Number(value).toLocaleString('es-GT')}}}}});
        });
    } catch { canvases.forEach((canvas)=>{if(canvas.parentElement) canvas.parentElement.innerHTML='<p class="empty-inline">La gráfica no está disponible sin conexión. Consulta los valores en la tabla.</p>';}); }
}

function initForms() {
    const panelRows=document.getElementById('panel-rows'); const template=document.getElementById('panel-template');
    document.getElementById('add-panel')?.addEventListener('click',()=>{if(!panelRows||!template)return;const index=panelRows.querySelectorAll('.panel-row').length;panelRows.insertAdjacentHTML('beforeend',template.innerHTML.replaceAll('__INDEX__',String(index)));});
    panelRows?.addEventListener('click',(event)=>{const button=event.target.closest('.remove-panel');if(!button)return;const rows=panelRows.querySelectorAll('.panel-row');if(rows.length>1)button.closest('.panel-row')?.remove();});
    const capacity=document.getElementById('capacity-preview'); const recompute=()=>{if(!capacity||!panelRows)return;let total=0;panelRows.querySelectorAll('.panel-row').forEach(row=>{const option=row.querySelector('select option:checked');total+=Number(option?.dataset.power||0)*Number(row.querySelector('input')?.value||0);});capacity.innerHTML=`${total.toFixed(2)} <small>kW</small>`;}; panelRows?.addEventListener('input',recompute); panelRows?.addEventListener('change',recompute); recompute();
    const real=document.querySelector('[data-energy-real]'), expected=document.querySelector('[data-energy-expected]'), co2=document.querySelector('[data-co2-preview]'), performance=document.querySelector('[data-performance-preview]'), alert=document.querySelector('[data-alert-preview]'); const preview=()=>{if(!real||!expected)return;const r=Number(real.value||0),e=Number(expected.value||0);if(co2)co2.innerHTML=`${(r*.4).toFixed(2)} <small>kg</small>`;if(performance)performance.textContent=e>0?`${(r/e*100).toFixed(1)}%`:'—';if(alert)alert.textContent=e>0&&r<=e*.8?'Se activará una alerta: el resultado está 20% o más por debajo de lo esperado.':'Una generación igual o inferior al 80% de la esperada activa una alerta.';}; real?.addEventListener('input',preview); expected?.addEventListener('input',preview); preview();
    document.querySelectorAll('[data-copy]').forEach(button=>button.addEventListener('click',async()=>{try{await navigator.clipboard.writeText(button.dataset.copy);const label=button.textContent;button.textContent='Copiado';setTimeout(()=>button.textContent=label,1400);}catch{/* clipboard may be disabled */}}));
    document.querySelectorAll('[data-submit-form]').forEach(form=>form.addEventListener('submit',()=>{const submit=form.querySelector('button[type="submit"],button:not([type])');if(submit){submit.disabled=true;submit.innerHTML='Guardando…';}}));
}
document.addEventListener('DOMContentLoaded',()=>{initMenu();initForms();initMap();initCharts();});
