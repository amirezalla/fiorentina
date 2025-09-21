@once
    <style>
        .shortcode-lazy-loading {
            position: relative;
            min-height: 12rem;
        }

        .loading-spinner {
            align-items: center;
            background: hsla(0, 0%, 100%, 0.261);
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
                border-color: var(--primary-color) transparent var(--primary-color) transparent;
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
            if (!window.jQuery) {
                return setTimeout(waitForJQ, 20);
            } // run as soon as jQuery exists
            (function($) {

                const ENDPOINT = "{{ route('public.ajax.render-ui-block') }}";
                const CSRF = "{{ csrf_token() }}";

                // Process one element (and mark it so we don’t re-run)
                function process($el) {
                    if (!$el.length || $el.data('uiLoaded')) return;
                    $el.data('uiLoaded', 1); // prevent double fire

                    const name = $el.data('name');
                    let attrs = $el.data('attributes');

                    // Handle stringified JSON in data-attributes
                    if (attrs && typeof attrs === 'string') {
                        try {
                            attrs = JSON.parse(attrs);
                        } catch {
                            attrs = {};
                        }
                    }

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
                        success: function(resp) {
                            const error = resp && resp.error;
                            const html = resp && resp.data;

                            if (!error && html) {
                                $el.replaceWith(html);
                                if (window.Theme?.lazyLoadInstance?.update) Theme.lazyLoadInstance
                                    .update();
                                document.dispatchEvent(new CustomEvent('shortcode.loaded', {
                                    detail: {
                                        name,
                                        attributes: attrs || {},
                                        html
                                    }
                                }));
                            }
                        }
                    });
                }

                // 1) Process any nodes that already exist NOW (no DOMContentLoaded needed)
                $('.shortcode-lazy-loading').each(function() {
                    process($(this));
                });

                // 2)
                Process nodes that appear LATER(via AJAX, Livewire, etc.)
                const mo = new MutationObserver((mutations) => {
                    for (const m of mutations) {
                        // Newly added elements
                        m.addedNodes && $(m.addedNodes).each(function() {
                            const $n = $(this);
                            if (!$n.length) return;

                            if ($n.is('.shortcode-lazy-loading')) {
                                process($n);
                            }
                            // Also check descendants
                            $n.find && $n.find('.shortcode-lazy-loading').each(function() {
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
