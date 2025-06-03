    {{-- everything here replaces {!! Theme::content() !!} --}}
    {{-- Header section mimicking the screenshot --}}
    @php
        use Botble\Media\Models\MediaFile;

    @endphp
    @php Theme::set('section-name', $user->first_name) @endphp

    <div class="text-center py-60">
        {{-- circular logo – swap the file if you have a different one --}}

        <div class="container">
            <div class="col-3">
                @dd($user)
            </div>
            <div class="col-9">

            </div>
        </div>
        @php
            $img = RvMedia::getImageUrl($user->avatar, $user->first_name, 'thumbnail');
        @endphp
        <img src=" {{ $img }} " alt="{{ $user->first_name }}" width="160" class="mb-30">
        <h1 class="display-5 font-weight-bold">{{ $user->first_name }} {{ $user->last_name }}</h1>

    </div>

    <h2 class="section-title mb-40">
        {{ __('Notizie di') }} {{ $user->first_name }} {{ $user->last_name }}
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
