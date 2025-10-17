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
        <div class="col-lg-4" style="padding-top:6px">

            {{-- Author --}}
            @if ($post->author->avatar->url ?? null)
                @php
                    $img = RvMedia::image($post->author->avatar->url, $post->author->first_name, 'thumbnail', true, [
                        'style' => 'border-radius:50%;width:33px;height:33px;',
                    ]);
                @endphp
                {!! $img !!}
            @else
                <span class="post-author" style="color: gray;">{!! BaseHelper::renderIcon('ti ti-user-circle') !!}</span>
            @endif

            <span style="margin-left:5px">Di</span>
            <span class="author-name">
                <a style="font-size:medium;padding-left:2px;color:#8424e3;font-weight:700;"
                    href="/author/{{ $post->author->username }}">
                    {{ $post->author->first_name }} {{ $post->author->last_name }}
                </a>
            </span>


        </div>

        {{-- Styles (can be moved to your CSS) --}}
        <style>
            .collab-avatars {
                display: inline-flex;
                align-items: center;
                gap: 4px
            }

            .collab-avatar {
                width: 24px;
                height: 24px;
                border-radius: 50%;
                object-fit: cover;
                border: 1px solid #fff;
                box-shadow: 0 0 0 1px #e7e7ec
            }

            .collab-link {
                display: inline-flex;
                align-items: center;
                text-decoration: none
            }

            .collab-link:hover .collab-avatar {
                box-shadow: 0 0 0 1px #8424e3
            }

            .collab-initial {
                width: 24px;
                height: 24px;
                border-radius: 50%;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                background: #8424e3;
                color: #fff !important;
                font-weight: 800;
                font-size: 12px;
                border: 2px solid #fff;
                box-shadow: 0 0 0 1px #e7e7ec
            }

            .collab-wrap {
                left: 0px;
                padding: 1px 18px;
            }

            .collab-link img {
                width: 24px !important;
                height: 24px !important;
            }
        </style>

        {{-- Tooltip init (Bootstrap). Safe no-op if Bootstrap tooltip isn’t present. --}}
        <script>
            (function() {
                if (window.jQuery && typeof jQuery.fn.tooltip === 'function') {
                    jQuery(function($) {
                        $('[data-toggle="tooltip"]').tooltip();
                    });
                } else if (window.bootstrap && bootstrap.Tooltip) {
                    document.querySelectorAll('[data-toggle="tooltip"]').forEach(function(el) {
                        new bootstrap.Tooltip(el);
                    });
                }
            })();
        </script>

        <div class="col-lg-3" style="padding-top: 10px">
            <span class="created_inner">{{ $formattedDate }}</span>
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
        <div class="col-12 p-0 d-flex justify-content-center img-in-post mb-3 mt-3" style="position: relative">
            {{-- Collaborators --}}
            @php
                $collabs = $post->collaborators ?? collect();
            @endphp


            {{ RvMedia::image(
                $post->image,
                $post->name,
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
    <div class="collab-wrap">
        @if ($collabs->isNotEmpty())

            <small class="text-muted  mr-1" style="line-height:1; display:inline-flex;font-weight:600">CON LA
                COLLABORAZIONE DI:</small>
            <div class="collab-avatars mt-1">
                @foreach ($collabs as $c)
                    @php
                        $avatar = $c->avatar->url ?? null;
                        $initial = mb_strtoupper(mb_substr($c->last_name ?: $c->first_name ?: $c->username, 0, 1));
                        $label = e(trim($c->first_name . ' ' . $c->last_name ?: $c->username));
                    @endphp

                    <a href="/author/{{ $c->username }}" class="text-dark collab-link mr-2" data-toggle="tooltip"
                        data-placement="top" title="{{ $label }}">
                        {{ $c->first_name }} {{ $c->last_name }}
                    </a>
                @endforeach
            </div>


        @endif
        {{-- Inviati Speciali --}}
        @if (!empty($post->inviati))
            <div style="margin-top:2px;">
                @php
                    $raw = $post->inviati;
                    $decoded = [];

                    if (is_string($raw)) {
                        // Decode first layer
                        $step1 = json_decode($raw, true);

                        if (is_array($step1)) {
                            foreach ($step1 as $item) {
                                $val = $item['value'] ?? '';

                                // Try decoding inner JSON
                                $inner = json_decode($val, true);

                                if (is_array($inner)) {
                                    foreach ($inner as $sub) {
                                        if (isset($sub['value'])) {
                                            $decoded[] = ['value' => $sub['value']];
                                        }
                                    }
                                } else {
                                    // fallback — just store raw text
                                    $decoded[] = ['value' => $val];
                                }
                            }
                        }
                    } elseif (is_array($raw)) {
                        $decoded = $raw;
                    }

                    // Normalize and clean up any extra "value":" fragments
$inviati = collect($decoded)
    ->map(function ($i) {
        $val = is_array($i) ? $i['value'] ?? '' : (string) $i;

        // 🔧 cleanup: remove unwanted characters and prefixes
        $val = preg_replace('/"?value"?\s*:\s*"?/i', '', $val); // remove value":
        $val = str_replace(['{', '}', '"', '\\'], '', $val); // remove quotes/braces/backslashes
                            $val = trim($val);

                            return $val;
                        })
                        ->filter()
                        ->values();
                @endphp

                @if ($inviati->isNotEmpty())
                    <div class="inviati mt-2">
                        <small class="text-muted d-block mb-1">Inviati speciali:</small>
                        @foreach ($inviati as $name)
                            <span class="collab-link mr-2" data-toggle="tooltip" data-placement="top">
                                {{ $name }}
                            </span>
                        @endforeach
                    </div>
                @endif



            </div>
    </div>
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
