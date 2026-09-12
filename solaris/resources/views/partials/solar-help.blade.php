<button type="button" id="solar-help-toggle" class="solar-help-toggle" aria-controls="solar-help" aria-expanded="false" hidden><x-icon name="sun"/><span>Ayuda SOLARIS</span></button>
<section id="solar-help" class="solar-help" aria-labelledby="solar-help-title" hidden>
    <header><div><strong id="solar-help-title">Asistente SOLARIS</strong><small>Ayuda automática · respuestas revisadas</small></div><button type="button" id="solar-help-close" aria-label="Cerrar ayuda"><x-icon name="close"/></button></header>
    <p class="solar-help-intro">Bienvenido. Con gusto le orientaré sobre SOLARIS. Esta ayuda local no usa IA generativa ni consulta sus datos personales.</p>
    <div class="solar-help-suggestions"><button type="button" data-help-question="mapa">Usar el mapa</button><button type="button" data-help-question="enviar correo">Enviar un reporte</button><button type="button" data-help-question="co2">Entender el CO₂</button></div>
    <div id="solar-help-messages" class="solar-help-messages" role="log" aria-live="polite" aria-relevant="additions" aria-label="Respuestas del asistente"></div>
    <form id="solar-help-form"><label for="solar-help-input">¿En qué puedo ayudarle?</label><div><input id="solar-help-input" type="text" maxlength="400" autocomplete="off" placeholder="Escriba su pregunta" required><button type="submit" aria-label="Consultar al asistente"><x-icon name="arrow"/></button></div><small>Sin envíos externos. La conversación se borra al recargar.</small></form>
</section>
<script type="application/json" id="solar-help-routes">{!! json_encode(collect(['dashboard','map','reports','manual','generations.index','panels.index','alerts.index','projections.index','farms.index'])->mapWithKeys(fn ($name) => [$name => route($name)]), JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS|JSON_HEX_QUOT) !!}</script>
<meta name="solar-help-endpoint" content="{{ route('solar-help') }}">
