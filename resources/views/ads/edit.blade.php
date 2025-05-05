@extends(BaseHelper::getAdminMasterLayoutTemplate())
@php

    use Illuminate\Support\Facades\Storage;

@endphp
@section('content')
    <form action="{{ route('ads.update', $ad->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="row">
            <div class="col-md-9 gap-3">
                <div class="card mb-3">
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="post_title" class="form-label">Titolo</label>
                            <input type="text" class="form-control" name="post_title" id="post_title"
                                value="{{ old('post_title', $ad->title) }}">
                        </div>

                        <div class="mb-3">
                            <label for="weight" class="form-label">Weight</label>
                            <input type="text" class="form-control" name="weight" id="weight"
                                value="{{ old('weight', $ad->weight) }}">
                        </div>

                        <div class="mb-3">
                            <label for="type" class="form-label">Tipo Annuncio</label>
                            <select class="form-select" name="type" id="advanced-ad-type">
                                @foreach (\App\Models\Ad::TYPES as $key => $title)
                                    <option value="{{ $key }}" @selected($ad->type == $key)> {{ $title }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div id="imageUploadSection" class="mb-3"
                            @if ($ad->type == 2) style="display: none;" @endif>
                            <label for="images[]" class="form-label">Immagini</label>
                            <input type="file" class="form-control" name="images[]" multiple accept="image/*">

                            @php
                                $urls = json_decode($ad->urls, true) ?? [];
                            @endphp

                            @foreach ($ad->images as $index => $image)
                                <div class="border rounded p-2 mt-2">
                                    <img src="{{ Storage::disk('wasabi')->temporaryUrl($image->image_url, now()->addMinutes(15)) }}"
                                        style="max-width: 100%; height: auto" class="mb-2">
                                    <input type="url" name="urls[]" class="form-control"
                                        placeholder="https://example.com" value="{{ $urls[$index] ?? '' }}">
                                </div>
                            @endforeach
                        </div>

                        <div id="googleAdImageNameSection" class="mb-3"
                            @if ($ad->type == 1) style="display: none;" @endif>
                            <label for="amp" class="form-label">AMP Code</label>
                            <textarea name="amp" class="form-control" rows="10" placeholder="Enter your amp code here">{{ $ad->amp }}</textarea>
                        </div>

                        <div class="mb-3">
                            <label for="group" class="form-label">Gruppo annunci</label>
                            <select class="form-select" name="group" id="group">
                                @foreach (\App\Models\Ad::GROUPS as $key => $title)
                                    <option value="{{ $key }}" @selected($ad->group == $key)> {{ $title }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <label for="width" class="form-label">Larghezza (px)</label>
                                <input type="number" class="form-control" name="width" id="width"
                                    value="{{ old('width', $ad->width) }}">
                            </div>
                            <div class="col-md-6">
                                <label for="height" class="form-label">Altezza (px)</label>
                                <input type="number" class="form-control" name="height" id="height"
                                    value="{{ old('height', $ad->height) }}">
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-6">
                                <label for="start_date" class="form-label">Data inizio</label>
                                <input type="date" class="form-control" name="start_date" id="start_date"
                                    value="{{ old('start_date', $ad->start_date ?? date('Y-m-d')) }}">
                            </div>
                            <div class="col-md-6">
                                <label for="expiry_date" class="form-label">Data Scadenza</label>
                                <input type="date" class="form-control" name="expiry_date" id="expiry_date"
                                    value="{{ old('expiry_date', $ad->expiry_date) }}">
                            </div>
                        </div>

                        <div class="mt-3">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" name="status" id="status">
                                <option value="1" @selected($ad->status == 1)>Published</option>
                                <option value="0" @selected($ad->status == 0)>Draft</option>
                            </select>
                        </div>

                        <div class="mt-4">
                            <button class="btn btn-primary" type="submit">Aggiorna</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection

@push('footer')
    <script>
        document.getElementById('advanced-ad-type').addEventListener('change', function(e) {
            const type = parseInt(e.target.value);
            document.getElementById('imageUploadSection').style.display = type === 2 ? 'none' : 'block';
            document.getElementById('googleAdImageNameSection').style.display = type === 2 ? 'block' : 'none';
        });
    </script>
@endpush
