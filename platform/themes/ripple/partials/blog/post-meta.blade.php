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
        <div class="col-lg-5 d-flex justify-content-end pr-30" style="padding-bottom: 14px">
            <div class="social-buttons">
                {{-- Facebook --}}
                <a class="social-btn facebook"
                    href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(request()->fullUrl()) }}"
                    target="_blank" rel="noopener" aria-label="Condividi su Facebook">
                    <img src="{{ asset('storage/Facbook_logo.png') }}" alt="Facebook" width="22" height="22">
                </a>

                {{-- X (Twitter) --}}
                <a class="social-btn x"
                    href="https://twitter.com/intent/tweet?url={{ urlencode(request()->fullUrl()) }}&text={{ urlencode($post->name) }}"
                    target="_blank" rel="noopener" aria-label="Condividi su X">
                    <img src="{{ asset('storage/X_logo.png') }}" alt="X" width="22" height="22">
                </a>

                {{-- WhatsApp --}}
                <a class="social-btn whatsapp"
                    href="https://api.whatsapp.com/send?text={{ urlencode($post->name . ' ' . request()->fullUrl()) }}"
                    target="_blank" rel="noopener" aria-label="Condividi su WhatsApp">
                    <img src="{{ asset('storage/WA_logo.png') }}" alt="WhatsApp" width="22" height="22">
                </a>

                {{-- Threads --}}
                <a class="social-btn threads"
                    href="https://www.threads.net/intent/post?text={{ urlencode($post->name . ' ' . request()->fullUrl()) }}"
                    target="_blank" rel="noopener" aria-label="Condividi su Threads">
                    <img src="{{ asset('storage/Threads_logo.png') }}" alt="Threads" width="22" height="22">
                </a>

                <!-- Messenger -->
                <a class="social-btn messenger" href=`mailto:?subject={{ rawurlencode($post->name) }}
                    &body={{ rawurlencode("Ho trovato questo articolo interessante e ho pensato di condividerlo con voi. Dategli un'occhiata: " . request()->fullUrl()) }}`
                    aria-label="Invia via e-mail">
                    <i class="fas fa-envelope">
                        <img src="" alt="">
                </a>

                <!-- Comments -->
                <a href="#comments" class="social-btn comment-btn" aria-label="Vai ai commenti">
                    <i class="fas fa-comment"></i>
                </a>

            </div>
        </div>

    </div>



@endif

<div class="row">

    @include('ads.includes.adsrecentp1')



    <div class="col-lg-12 d-flex justify-content-center img-in-post mb-3 mt-3">
        <div>


            {{ RvMedia::image($post->image, $post->name, 'featured', attributes: ['loading' => 'lazy', 'style' => 'width:775px;height:475px;']) }}
        </div>
    </div>

    @include('ads.includes.adsrecentp2')
    <div class="d-block d-md-none col-12 text-center">
        @include('ads.includes.MOBILE_DOPO_FOTO_26')
    </div>

</div>
