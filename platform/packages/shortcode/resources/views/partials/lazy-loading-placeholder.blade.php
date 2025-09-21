@once
    <style>
        .shortcode-lazy-loading {
            position: relative;
            min-height: 12rem;
        }

        .loading-spinner {
            align-items: center;
            background: hsla(0, 0%, 100%, 0.5);
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
        (function() {
            const ENDPOINT = "{{ route('public.ajax.render-ui-block') }}";
            const CSRF = "{{ csrf_token() }}"; // remove if you move to routes/api.php

            const els = Array.from(document.querySelectorAll('.shortcode-lazy-loading'));
            if (!('IntersectionObserver' in window) || els.length === 0) {
                // Fallback: load all at once
                return loadAll(els);
            }

            const io = new IntersectionObserver((entries) => {
                entries.forEach((entry) => {
                    if (!entry.isIntersecting) return;
                    io.unobserve(entry.target);
                    loadOne(entry.target);
                });
            }, {
                rootMargin: '300px 0px'
            }); // start a bit before it enters

            els.forEach(el => io.observe(el));

            function loadAll(nodes) {
                nodes.forEach(loadOne);
            }

            async function loadOne(el) {
                const name = el.dataset.name;
                let attributes = safeParse(el.dataset.attributes) || {}; // handles stringified JSON or object

                // --- client-side cache (per tab) ---
                const cacheKey = 'ui:' + name + ':' + JSON.stringify(attributes);
                const cached = sessionStorage.getItem(cacheKey);
                if (cached) {
                    el.replaceWith(cached);
                    dispatchLoaded(name, attributes, cached);
                    maybeUpdateLazy();
                    return;
                }

                try {
                    const res = await fetch(ENDPOINT, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': CSRF, // remove if on API stack
                        },
                        body: JSON.stringify({
                            name,
                            attributes
                        }),
                        credentials: 'same-origin',
                    });

                    if (!res.ok) throw new Error('HTTP ' + res.status);
                    const payload = await res.json();

                    if (payload && !payload.error && payload.data) {
                        el.replaceWith(payload.data);
                        sessionStorage.setItem(cacheKey, payload.data);
                        dispatchLoaded(name, attributes, payload.data);
                        maybeUpdateLazy();
                    }
                } catch (e) {
                    // optional: add a tiny retry/backoff or show a placeholder
                    console.warn('ui-block load failed', e);
                }
            }

            function safeParse(v) {
                if (!v) return null;
                if (typeof v === 'object') return v;
                try {
                    return JSON.parse(v);
                } catch {
                    return null;
                }
            }

            function maybeUpdateLazy() {
                if (window.Theme && Theme.lazyLoadInstance && typeof Theme.lazyLoadInstance.update === 'function') {
                    Theme.lazyLoadInstance.update();
                }
            }

            function dispatchLoaded(name, attributes, html) {
                document.dispatchEvent(new CustomEvent('shortcode.loaded', {
                    detail: {
                        name,
                        attributes,
                        html
                    }
                }));
            }
        })
        ();
    </script>
@endonce

<div class="shortcode-lazy-loading" data-name="{{ $name }}" data-attributes="{{ json_encode($attributes) }}">
    <div class="loading-spinner"></div>
</div>
