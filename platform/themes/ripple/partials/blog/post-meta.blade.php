@php
    use Carbon\Carbon;
    $date = Carbon::parse($post->published_at);
    $formattedDate = $date->locale('it')->translatedFormat('d F Y - H:i');
@endphp



@if ($post->author->name)
    <div class="row">
        <div class="col-lg-7" style="padding-top:6px">
            <span class="created_at " style="color: gray;">
                {!! BaseHelper::renderIcon('ti ti-clock') !!} {{ $formattedDate }}
            </span>
            @if ($post->author->avatar->url)
                <img class="post-author" src="{{ $post->author->avatar->url }}" alt="$post->author->avatar->url">
            @else
                <span class="post-author " style="color: gray;">{!! BaseHelper::renderIcon('ti ti-user-circle') !!}
            @endif
            <span class="author-name">{{ $post->author->name }}</span>

        </div>
        <div class="col-lg-5 d-flex justify-content-end pr-30" style="padding-bottom:14px">
            <div class="social-buttons">

                {{-- Facebook – PNG already contains its own blue circle --}}
                <a class="social-btn facebook"
                    href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(request()->fullUrl()) }}"
                    target="_blank" rel="noopener" aria-label="Condividi su Facebook">
                    <img src="{{ asset('storage/Facbook_logo.png') }}" alt="Facebook">
                </a>

                {{-- X (Twitter) – black logo, grey background --}}
                <a class="social-btn x"
                    href="https://twitter.com/intent/tweet?url={{ urlencode(request()->fullUrl()) }}&text={{ urlencode($post->name) }}"
                    target="_blank" rel="noopener" aria-label="Condividi su X">
                    <img src="{{ asset('storage/X_logo.png') }}" alt="X">
                </a>

                {{-- WhatsApp – logo turned white via CSS filter, green background --}}
                <a class="social-btn whatsapp"
                    href="https://api.whatsapp.com/send?text={{ urlencode($post->name . ' ' . request()->fullUrl()) }}"
                    target="_blank" rel="noopener" aria-label="Condividi su WhatsApp">
                    <img src="{{ asset('storage/WA_logo.png') }}" alt="WhatsApp">
                </a>

                {{-- Threads – logo turned white via CSS filter, black background --}}
                <a class="social-btn threads"
                    href="https://www.threads.net/intent/post?text={{ urlencode($post->name . ' ' . request()->fullUrl()) }}"
                    target="_blank" rel="noopener" aria-label="Condividi su Threads">
                    <img src="{{ asset('storage/Threads_logo.png') }}" alt="Threads">
                </a>

                {{-- “Messenger” = share by e-mail – white envelope on grey background --}}
                <a class="social-btn messenger"
                    href=`mailto:?subject={{ rawurlencode($post->name) }}&body={{ rawurlencode("Ho trovato questo articolo interessante e ho pensato di condividerlo con voi. Dategli un'occhiata: " . request()->fullUrl()) }}`
                    aria-label="Invia via e-mail">
                    <i class="fas fa-envelope"></i>
                </a>

                {{-- Comments --}}
                <a href="#comments" class="social-btn comment-btn" aria-label="Vai ai commenti">
                    <i class="fas fa-comment"></i>
                </a>

            </div>
        </div>

    </div>



@endif

<div class="row">

    @include('ads.includes.dblog-author')



    {{-- Main featured image --}}
    <div class="row mx-0"> {{-- add a proper .row --}}
        <div class="col-12 p-0 d-flex justify-content-center img-in-post mb-3 mt-3">

            {{ RvMedia::image(
                $post->image,
                $post->name,
                'featured',
                attributes: [
                    'loading' => 'lazy',
                    'class' => 'img-fluid w-100', // responsive & full column width
                    // remove the old width:100vh
                ],
            ) }}

        </div>
    </div>


    @include('ads.includes.dblog-title')
    <div class="d-block d-md-none col-12 text-center">
        @include('ads.includes.MOBILE_DOPO_FOTO_26')
    </div>

</div>
