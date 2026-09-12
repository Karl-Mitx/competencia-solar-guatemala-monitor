<div class="farm-photo-field">
    <label class="field" for="farm-photo">Fotografía de la instalación
        <input id="farm-photo" type="file" name="photo" accept="image/jpeg,image/png,image/webp">
        <small>Opcional · JPG, PNG o WebP · máximo 5 MB.</small>
    </label>
    @if($farm->photo_path)<p class="muted">Hay una fotografía guardada. Si seleccionas otra, la reemplazará.</p>@endif
    <img id="farm-photo-preview" class="farm-photo-preview" @if($farm->photo_path) src="{{ asset('storage/'.$farm->photo_path) }}" @endif alt="Vista previa de la fotografía" @if(!$farm->photo_path) hidden @endif>
</div>
@push('scripts')
<script>
document.getElementById('farm-photo')?.addEventListener('change', event => { const file = event.target.files?.[0]; const preview = document.getElementById('farm-photo-preview'); if (!file || !preview) return; preview.src = URL.createObjectURL(file); preview.hidden = false; });
</script>
@endpush
