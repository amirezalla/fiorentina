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
        (function() {
            function run($) {
                const ENDPOINT = "{{ route('public.ajax.render-ui-block') }}";
                const CSRF = "{{ csrf_token() }}";

                $('.shortcode-lazy-loading').each(function(_, el) {
                    const $el = $(el);
                    const name = $el.data('name');
                    let attrs = $el.data('attributes');
                    if (typeof attrs === 'string') {
                        try {
                            attrs = JSON.parse(attrs);
                        } catch {
                            attrs = {};
                        }
                    }

                    function onOk(res) {
                        const {
                            error,
                            data
                        } = res || {};
                        if (!error && data) {
                            $el.replaceWith(data);
                            if (window.Theme?.lazyLoadInstance?.update) Theme.lazyLoadInstance.update();
                            document.dispatchEvent(new CustomEvent('shortcode.loaded', {
                                detail: {
                                    name,
                                    attributes: attrs || {},
                                    html: data
                                }
                            }));
                        }
                    }

                    if ($ && $.ajax) {
                        // jQuery path
                        $.ajax({
                            url: ENDPOINT,
                            type: 'POST',
                            data: JSON.stringify({
                                name,
                                attributes: {
                                    ...(attrs || {})
                                }
                            }),
                            contentType: 'application/json; charset=utf-8',
                            dataType: 'json',
                            headers: {
                                'X-CSRF-TOKEN': CSRF,
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            success: onOk
                        });
                    } else {
                        // Fallback (no jQuery AJAX, e.g. slim build)
                        fetch(ENDPOINT, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'Accept': 'application/json',
                                    'X-Requested-With': 'XMLHttpRequest',
                                    'X-CSRF-TOKEN': CSRF
                                },
                                credentials: 'same-origin',
                                body: JSON.stringify({
                                    name,
                                    attributes: {
                                        ...(attrs || {})
                                    }
                                })
                            })
                            .then(r => r.json())
                            .then(onOk)
                            .catch(console.warn);
                    }
                });
            }

            // Run as soon as jQuery is available; otherwise fallback still runs
            if (window.jQuery) run(window.jQuery);
            else if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', () => run(window.jQuery || null));
            } else {
                run(window.jQuery || null);
            }
        })
        ();
    </script>
@endonce

<div class="shortcode-lazy-loading" data-name="{{ $name }}" data-attributes="{{ json_encode($attributes) }}">
    <div class="loading-spinner"></div>
</div>
