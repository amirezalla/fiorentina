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
            try {
                const ENDPOINT = "{{ route('public.ajax.render-ui-block') }}";
                const CSRF = "{{ csrf_token() }}";
                const MAX_PARALLEL = 4;

                console.log('[ui] endpoint:', ENDPOINT);

                function parseAttrs(v) {
                    if (!v) return {};
                    if (typeof v === 'object') return v;
                    try {
                        return JSON.parse(v);
                    } catch (e) {
                        console.warn('[ui] JSON parse fail', v, e);
                        return {};
                    }
                }

                function run() {
                    const nodes = Array.from(document.querySelectorAll('.shortcode-lazy-loading'));
                    console.log('[ui] found nodes:', nodes.length);
                    if (!nodes.length) return;

                    const jobs = [];
                    const byKey = new Map();

                    nodes.forEach((el, i) => {
                        const name = el.dataset.name;
                        const attrs = parseAttrs(el.dataset.attributes);
                        const key = name + "::" + JSON.stringify(attrs);

                        if (!name) {
                            console.warn('[ui] missing data-name on', el);
                            return;
                        }

                        if (!byKey.has(key)) byKey.set(key, []);
                        byKey.get(key).push(el);
                        if (byKey.get(key).length === 1) jobs.push({
                            key,
                            name,
                            attrs
                        });
                    });

                    console.log('[ui] unique jobs:', jobs.length);

                    function applyHtml(key, html) {
                        (byKey.get(key) || []).forEach(el => el.replaceWith(html));
                        if (window.Theme?.lazyLoadInstance?.update) Theme.lazyLoadInstance.update();
                    }

                    let active = 0,
                        idx = 0;

                    function pump() {
                        while (active < MAX_PARALLEL && idx < jobs.length) {
                            const job = jobs[idx++];
                            active++;
                            console.log('[ui] fetch:', job.name, job.attrs);
                            fetch(ENDPOINT, {
                                    method: "POST",
                                    headers: {
                                        "Content-Type": "application/json",
                                        "Accept": "application/json",
                                        "X-Requested-With": "XMLHttpRequest",
                                        "X-CSRF-TOKEN": CSRF
                                    },
                                    body: JSON.stringify({
                                        name: job.name,
                                        attributes: job.attrs
                                    }),
                                    credentials: "same-origin"
                                })
                                .then(r => {
                                    if (!r.ok) throw new Error('HTTP ' + r.status);
                                    return r.json();
                                })
                                .then(json => {
                                    console.log('[ui] response ok:', json);
                                    if (!json?.error && json?.data) {
                                        applyHtml(job.key, json.data);
                                        document.dispatchEvent(new CustomEvent('shortcode.loaded', {
                                            detail: {
                                                name: job.name,
                                                attributes: job.attrs,
                                                html: json.data
                                            }
                                        }));
                                    }
                                })
                                .catch(err => {
                                    console.error('[ui] request failed:', err);
                                })
                                .finally(() => {
                                    active--;
                                    pump();
                                });
                        }
                    }

                    pump();
                }

                if (document.readyState === "loading") {
                    document.addEventListener("DOMContentLoaded", run, {
                        once: true
                    });
                } else {
                    run();
                }
            } catch (e) {
                console.error('[ui] fatal:', e);
            }
        })
        ();
    </script>
@endonce

<div class="shortcode-lazy-loading" data-name="{{ $name }}" data-attributes="{{ json_encode($attributes) }}">
    <div class="loading-spinner"></div>
</div>
