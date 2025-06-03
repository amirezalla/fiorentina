@extends(Theme::getLayout()) {{-- uses layout/default.blade.php --}}

@section('content')
    {{-- everything here replaces {!! Theme::content() !!} --}}

    {{-- Header section mimicking the screenshot --}}
    <div class="text-center py-60">
        {{-- circular logo – swap the file if you have a different one --}}
        <img src="{{ Theme::asset()->url('images/viola-it-round.svg') }}" alt="{{ $user->name }}" width="160"
            class="mb-30">
        <h1 class="display-5 font-weight-bold">{{ $user->name }}</h1>

        @if ($user->description)
            <p class="text-muted mt-3">{{ $user->description }}</p>
        @endif
    </div>

    <h2 class="section-title mb-40">
        {{ __('Notizie di') }} {{ $user->name }}
    </h2>

    {{-- Post loop --}}
    @forelse ($posts as $post)
        {{-- Re-use whatever partial you already employ for cards/tiles --}}
        {{ $post->name }} {{-- fallback for older posts --}}
    @empty
        <p class="text-center text-muted">
            {{ __('Nessun articolo pubblicato al momento.') }}
        </p>
    @endforelse

    {{-- Botble’s pagination partial --}}
    <div class="mt-40">
        {!! $posts->withQueryString()->links(Theme::getThemeNamespace('partials.pagination')) !!}
    </div>
@endsection
