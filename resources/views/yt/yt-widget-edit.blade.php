@extends(BaseHelper::getAdminMasterLayoutTemplate())

@section('content')
<h1 class="mb-4">YouTube Widget</h1>

@if(session('status'))
  <div class="alert alert-success">{{ session('status') }}</div>
@endif

<form action="{{ route('ytwidget.update') }}" method="POST">
    @csrf

    <div class="mb-3">
        <label class="form-label fw-semibold">Tipo</label>
        <select name="type" class="form-select">
            <option value="live"     {{ old('type', $widget->type) == 'live'     ? 'selected':'' }}>Live</option>
            <option value="playlist" {{ old('type', $widget->type) == 'playlist' ? 'selected':'' }}>Playlist</option>
        </select>
    </div>

    <div id="live-fields"     class="mb-3 {{ old('type', $widget->type) == 'live'     ? '' : 'd-none' }}">
        <label class="form-label">URL dello streaming Live</label>
        <input type="url" name="live_url" class="form-control"
               value="{{ old('live_url', $widget->live_url) }}">
    </div>

    <div id="playlist-fields" class="mb-3 {{ old('type', $widget->type) == 'playlist' ? '' : 'd-none' }}">
        <label class="form-label">URL video (uno per riga)</label>
        <textarea name="playlist_urls[]" rows="5" class="form-control"
                  placeholder="https://youtu.be/XXXXXXX">{{ old('playlist_urls', implode(\"\\n\", $widget->playlist_urls ?? [])) }}</textarea>
        <small class="text-muted">Il sistema estrae automaticamente l’ID.</small>
    </div>

    <button class="btn btn-primary">Salva</button>
</form>

@push('scripts')
<script>
document.querySelector('[name=type]').addEventListener('change', e => {
  document.getElementById('live-fields')    .classList.toggle('d-none', e.target.value !== 'live');
  document.getElementById('playlist-fields').classList.toggle('d-none', e.target.value !== 'playlist');
});
</script>
@endpush
@endsection
