@php
    use Carbon\Carbon;
    use Illuminate\Support\Facades\DB;
    $date = Carbon::parse($post->published_at);
    $formattedDate = $date->locale('it')->translatedFormat('d F Y - H:i');
    $ua = request()->header('User-Agent', '');

    // very small UA test – good enough for phone / tablet vs desktop
    $isMobile = preg_match('/android|iphone|ipod|ipad|blackberry|bb10|mini|windows\sce|palm/i', $ua);
@endphp



@if ($post->author->name)
    <div class="row">
        <div class="col-lg-7" style="padding-top:6px">


            @if ($post->author->avatar->url)
                @php
                    $img = RvMedia::image($post->author->avatar->url, $post->author->first_name, 'thumbnail', true, [
                        'style' => 'border-radius:50%;width:33px;height:33px;',
                    ]);
                @endphp
                {{ $img }}
            @else
                <span class="post-author " style="color: gray;">{!! BaseHelper::renderIcon('ti ti-user-circle') !!}
            @endif
            <span style="margin-left:5px">Di</span>
            <span class="author-name"><a style="font-size:medium;padding-left: 2px;color:#8424e3;font-weight:700;"
                    href="/author/{{ $post->author->username }}">{{ $post->author->first_name }}
                    {{ $post->author->last_name }}</a></span> <span class="created_inner">{{ $formattedDate }}</span>

        </div>
        <div class="col-lg-5 d-flex justify-content-end pr-30" style="padding-bottom:14px;">
            <div class="social-buttons" style="display: contents;">

                {{-- Facebook – PNG already contains its own blue circle --}}
                <a class="social-btn facebook"
                    href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(request()->fullUrl()) }}"
                    target="_blank" rel="noopener" aria-label="Condividi su Facebook">
                    <img src="{{ asset('storage/Facbook_logo.png') }}" style='width:28%' alt="Facebook">
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
                <a href="#comments" class="social-btn comment-btn" aria-label="Vai ai commenti"
                    style="background: none; border:2px solid #21252963">
                    <i class="fas fa-comment " style="filter: none;color:#21252963"></i>
                </a>

            </div>
        </div>

    </div>



@endif

<div class="row">
    @if (!$isMobile)
        @include('ads.includes.dblog-author')
    @endif



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

            @php

                // 3️⃣ plain-DB lookup in media_files
                $media = DB::table('media_files')->where('url', $post->image)->first();
            @endphp





        </div>
        @if ($media && $media->alt && $media->alt !== $media->name)
            <span class="d-block text-muted fw-light mb-3 p-0"
                style="    background: #dddddd;
    padding: 10px !important;
    color: #393939 !important;
    margin-top: -16px !important;
    border-radius: 0px 0px 10px 10px;">
                <i class="fa fa-info-circle" style="margin-right:5px"></i> {{ $media->alt }}</span>
        @endif
    </div>

    @if (!$isMobile)
        @include('ads.includes.dblog-title')
    @else
        @include('ads.includes.MOBILE_DOPO_FOTO_26')
    @endif


</div>

<script>
    (function() {
        /** -----------------------------------------------------------
         * 1)  The CSS you asked for (with !important flags intact)
         * ----------------------------------------------------------- */
        const css = `
        #google_image_div {
            width: 100vw !important;
        }
    `;

        /** -----------------------------------------------------------
         * 2)  Inject <style> once
         * ----------------------------------------------------------- */
        function injectStyle() {
            if (document.getElementById('amp-fix-style')) return; // avoid duplicates
            const s = document.createElement('style');
            s.id = 'amp-fix-style';
            s.type = 'text/css';
            s.appendChild(document.createTextNode(css));
            document.head.appendChild(s);
        }

        /** -----------------------------------------------------------
         * 3)  Run when DOM is ready …
         * ----------------------------------------------------------- */
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', injectStyle);
        } else {
            injectStyle();
        }


    })();
</script>
