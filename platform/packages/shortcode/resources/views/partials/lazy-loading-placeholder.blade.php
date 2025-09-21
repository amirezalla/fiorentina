@once
    <style>
        .shortcode-lazy-loading {
            position: relative;
            min-height: 12rem;
        }

        .loading-spinner {
            align-items: center;
            background: hsla(0, 0%, 100%, 0.25);
            display: flex;
            height: 100%;
            inset-inline-start: 0;
            justify-content: center;
            position: absolute;
            top: 0;
            width: 100%;
            z-index: 1;

            &:after {
                animation: loading-spinner-rotation 0.5s linear infinite;
                border-color: white transparent color transparent;
                border-radius: 50%;
                border-style: solid;
                border-width: 1px;
                content: ' ';
                display: block;
                height: 40px;
                position: absolute;
                top: calc(50% - 20px);
                width: 40px;
                z-index: 1;
            }
        }

        @keyframes loading-spinner-rotation {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }
    </style>

    <script>
        (function waitForJQ() {
            if (!window.jQuery) return setTimeout(waitForJQ, 20);
            (function($) {

                const ENDPOINT = "{{ route('public.ajax.render-ui-block') }}";
                const CSRF = "{{ csrf_token() }}";

                function process($el) {
                    if (!$el.length || $el.data('uiLoaded')) return;
                    $el.data('uiLoaded', 1);

                    const name = $el.data('name');
                    let attrs = $el.data('attributes');
                    if (typeof attrs === 'string') {
                        try {
                            attrs = JSON.parse(attrs);
                        } catch {
                            attrs = {};
                        }
                    }

                    $.ajax({
                        url: ENDPOINT,
                        type: 'POST',
                        data: {
                            name,
                            attributes: {
                                ...(attrs || {})
                            }
                        },
                        headers: {
                            'X-CSRF-TOKEN': CSRF,
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        success: function({
                            error,
                            data
                        }) {
                            if (!error && data) {
                                $el.replaceWith(data);
                                if (window.Theme?.lazyLoadInstance?.update) Theme.lazyLoadInstance
                                    .update();
                                document.dispatchEvent(new CustomEvent('shortcode.loaded', {
                                    detail: {
                                        name,
                                        attributes: attrs || {},
                                        html: data
                                    }
                                }));
                            }
                        }
                    });
                }

                // 1) Run immediately on whatever is already in the DOM (no DOMContentLoaded needed)
                $('.shortcode-lazy-loading').each(function() {
                    process($(this));
                });

                // 2)
                const mo = new MutationObserver((mutations) => {
                    for (const m of mutations) {
                        $(m.addedNodes).each(function() {
                            const $n = $(this);
                            if ($n.is && $n.is('.shortcode-lazy-loading')) process($n);
                            if ($n.find) $n.find('.shortcode-lazy-loading').each(function() {
                                process($(this));
                            });
                        });
                    }
                });
                mo.observe(document.documentElement, {
                    childList: true,
                    subtree: true
                });

            })(jQuery);
        })();
    </script>
@endonce

<div class="shortcode-lazy-loading" data-name="{{ $name }}" data-attributes="{{ json_encode($attributes) }}">
    <div class="loading-spinner"></div>
</div>
