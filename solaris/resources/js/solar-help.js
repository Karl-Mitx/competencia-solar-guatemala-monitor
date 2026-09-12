const normalize = text => text.toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g, '').replace(/[^a-z0-9\s]/g, ' ').replace(/\s+/g, ' ').trim();
const topics = [
    { words: ['correo', 'gmail', 'enviar', 'email', 'adjunto'], text: 'Con gusto. En Reportes, seleccione los filtros y escriba el correo destinatario en “Recibe este reporte por correo”. Presione “Enviar CSV por correo”. El envío requiere que el administrador haya configurado el servicio de correo; también puede descargar el archivo.', route: 'reports', label: 'Abrir Reportes' },
    { words: ['descargar', 'reporte', 'reportes', 'csv', 'excel'], text: 'Para obtener su reporte, abra Reportes, seleccione el departamento y el período que desea consultar y presione “Descargar CSV”. Puede abrir ese archivo en una hoja de cálculo. Los indicadores corresponden a los filtros seleccionados.', route: 'reports', label: 'Consultar reportes' },
    { words: ['mapa', 'marcador', 'territorio', 'departamento', 'departamentos'], text: 'En el mapa puede seleccionar un departamento para consultar su generación, capacidad y CO₂ evitado. Los soles representan granjas: selecciónelos para abrir su ficha. El gris indica ausencia de registros; el dorado, cero kWh registrados. La escala verde compara la generación según sus filtros.', route: 'map', label: 'Explorar el mapa' },
    { words: ['co2', 'carbono', 'emisiones', 'impacto'], text: 'El CO₂ evitado es una estimación: se multiplica la energía generada en kWh por 0.4 kg/kWh. Por ejemplo, 100 kWh equivalen a 40 kg de CO₂ evitado estimado. No es una medición directa de emisiones.', route: 'manual', label: 'Ver los cálculos' },
    { words: ['kwh', 'energia', 'generacion', 'consumo'], text: 'El kWh mide una cantidad de energía. Por ejemplo, un equipo de 1 kW funcionando durante una hora consume 1 kWh. En SOLARIS, Generación permite consultar la energía real y la esperada de cada período.', route: 'generations.index', label: 'Consultar generación' },
    { words: ['kw', 'capacidad', 'panel', 'paneles', 'potencia'], text: 'La capacidad instalada se calcula sumando la potencia en kW de cada tipo de panel multiplicada por su cantidad. Describe el inventario actual; no equivale a la energía generada durante un mes.', route: 'panels.index', label: 'Ver catálogo de paneles' },
    { words: ['alerta', 'alertas', 'rendimiento', 'cumplimiento'], text: 'SOLARIS genera una alerta cuando la energía real es igual o inferior al 80% de la esperada, siempre que exista una expectativa mayor que cero. Puede revisar los registros afectados en Alertas.', route: 'alerts.index', label: 'Consultar alertas' },
    { words: ['proyeccion', 'proyecciones', 'pronostico', 'futuro'], text: 'Las proyecciones usan el promedio diario de los tres meses cerrados consecutivos anteriores y los días del mes que se estima. Son orientativas y no garantizan la generación futura.', route: 'projections.index', label: 'Ver proyecciones' },
    { words: ['oscuro', 'claro', 'tema', 'apariencia'], text: 'Puede cambiar la apariencia con “Usar modo oscuro” o “Usar modo claro”, en el menú lateral. Su navegador recordará la elección.' },
    { words: ['granja', 'granjas', 'registrar', 'editar', 'administrar'], text: 'Puede consultar las granjas desde Granjas solares. Para registrar o modificar información necesita iniciar sesión con una cuenta autorizada.', route: 'farms.index', label: 'Consultar granjas' },
    { words: ['real', 'reales', 'demostracion', 'datos'], text: 'Revise la etiqueta superior de la página: “Datos de demostración” identifica cifras de ejemplo. Esta ayuda no consulta los registros en tiempo real; para conocer cantidades actuales, use el panel y sus filtros.', route: 'dashboard', label: 'Ver panorama nacional' },
];

export function answerQuestion(input) {
    const text = normalize(String(input).slice(0, 400));
    if (!text) return { text: 'Por favor, escriba una pregunta sobre SOLARIS. Con gusto le orientaré.' };
    if (/\b(insulta|insultar|groseria|groserias|idiota|estupido|mierda|puta|puto|joder|imbecil|pendejo|fuck|shit)\b/.test(text)) return { text: 'Estoy aquí para atenderle con respeto. Con gusto puedo ayudarle con el mapa, los reportes o los conceptos de energía solar.' };
    if (/\b(ignora|instrucciones|prompt|sistema interno|finge)\b/.test(text)) return { text: 'Mi función es ofrecer orientación sobre SOLARIS mediante respuestas de ayuda revisadas. Puede preguntarme por el mapa, los reportes o los cálculos.' };
    const words = new Set(text.split(' '));
    const ranked = topics.map(topic => ({ topic, score: topic.words.filter(word => words.has(word)).length })).sort((a, b) => b.score - a.score);
    if (ranked[0].score) return ranked[0].topic;
    if (/\b(hola|buenos|buenas|saludos)\b/.test(text)) return { text: 'Bienvenido a SOLARIS. Es un gusto atenderle. ¿Desea ayuda con el mapa, los reportes o algún concepto de energía solar?' };
    if (/\b(gracias|agradezco)\b/.test(text)) return { text: 'Con mucho gusto. Quedo a su disposición para orientarle sobre SOLARIS.' };
    return { text: 'no tengo una respuesta revisada para esa consulta. Permítame consultar la ayuda avanzada.', fallback: true };
}

export function initSolarHelp() {
    const panel = document.getElementById('solar-help');
    if (!panel) return;
    const toggle = document.getElementById('solar-help-toggle');
    const input = document.getElementById('solar-help-input');
    const messages = document.getElementById('solar-help-messages');
    const routes = JSON.parse(document.getElementById('solar-help-routes').textContent);
    const setOpen = open => { panel.hidden = !open; toggle.setAttribute('aria-expanded', String(open)); if (open) input.focus(); else toggle.focus(); };
    toggle.addEventListener('click', () => setOpen(panel.hidden));
    document.getElementById('solar-help-close').addEventListener('click', () => setOpen(false));
    panel.addEventListener('keydown', event => { if (event.key === 'Escape') { event.stopPropagation(); setOpen(false); } });
    const endpoint = document.querySelector('meta[name="solar-help-endpoint"]')?.content;
    const ask = async question => {
        if (!question.trim()) return;
        const reply = answerQuestion(question);
        if (reply.fallback && endpoint) {
            const token = document.querySelector('meta[name="csrf-token"]')?.content;
            try {
                const response = await fetch(endpoint, { method:'POST', headers:{'Content-Type':'application/json','Accept':'application/json','X-CSRF-TOKEN':token}, body:JSON.stringify({question}) });
                const remote = await response.json();
                reply.text = remote.text || 'No pude obtener una respuesta en este momento.';
                reply.fallback = false;
            } catch { reply.text = 'La ayuda avanzada no está disponible sin conexión. Puedo orientarle sobre SOLARIS, el mapa, reportes, capacidad, CO₂, alertas y proyecciones.'; }
        }
        const message = document.createElement('div');
        message.className = 'solar-help-answer';
        const label = document.createElement('strong'); label.textContent = 'Asistente SOLARIS';
        const text = document.createElement('p'); text.textContent = reply.text;
        message.append(label, text);
        // Free text is deliberately not echoed: offensive input never becomes chat content.
        if (reply.route && routes[reply.route]) { const link = document.createElement('a'); link.href = routes[reply.route]; link.textContent = reply.label; message.append(link); }
        messages.append(message);
        while (messages.children.length > 12) messages.firstElementChild.remove();
        input.value = '';
        messages.scrollTop = messages.scrollHeight;
    };
    document.getElementById('solar-help-form').addEventListener('submit', event => { event.preventDefault(); ask(input.value); });
    document.querySelectorAll('[data-help-question]').forEach(button => button.addEventListener('click', () => ask(button.dataset.helpQuestion)));
    toggle.hidden = false;
}
