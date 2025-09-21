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
            const CSRF = "{{ csrf_token() }}";
            const MAX_PARALLEL = 4; // tune: 4–8 is usually sweet for PHP apps

            // Collect blocks
            const nodes = Array.from(document.querySelectorAll('.shortcode-lazy-loading'));
            if (!nodes.length) return;

            // Build jobs, dedupe by key (name+attributes)
            const jobs = [];
            const byKey = new Map(); // key -> array of nodes that want the same payload

            nodes.forEach((el, i) => {
                const name = el.dataset.name;
                const attrs = parseAttrs(el.dataset.attributes);
                const key = name + "::" + JSON.stringify(attrs);

                if (!byKey.has(key)) byKey.set(key, []);
                byKey.get(key).push(el);

                // Create one job per unique key
                if (byKey.get(key).length === 1) {
                    jobs.push({
                        key,
                        name,
                        attrs
                    });
                }
            });

            // Replace all nodes for a given key with HTML
            function applyHtmlToNodes(key, html) {
                const list = byKey.get(key) || [];
                list.forEach(el => {
                    el.replaceWith(html);
                });
                if (window.Theme?.lazyLoadInstance?.update) Theme.lazyLoadInstance.update();
            }

            // Session cache
            function cacheGet(key) {
                try {
                    return sessionStorage.getItem("ui:" + key);
                } catch {
                    return null;
                }
            }

            function cacheSet(key, v) {
                try {
                    sessionStorage.setItem("ui:" + key, v);
                } catch {}
            }

            // Worker to process jobs with concurrency cap
            let active = 0,
                idx = 0;

            function pump() {
                while (active < MAX_PARALLEL && idx < jobs.length) {
                    const job = jobs[idx++];
                    const cached = cacheGet(job.key);
                    if (cached) {
                        applyHtmlToNodes(job.key, cached);
                        continue;
                    }
                    active++;
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
                        .then(async (res) => {
                            if (!res.ok) throw new Error("HTTP " + res.status);
                            return res.json();
                        })
                        .then((json) => {
                            if (!json?.error && json?.data) {
                                cacheSet(job.key, json.data);
                                applyHtmlToNodes(job.key, json.data);
                                document.dispatchEvent(new CustomEvent('shortcode.loaded', {
                                    detail: {
                                        name: job.name,
                                        attributes: job.attrs,
                                        html: json.data
                                    }
                                }));
                            }
                        })
                        .catch((e) => {
                            console.warn("ui-block request failed", e);
                        })
                        .finally(() => {
                            active--;
                            pump();
                        });
                }
            }

            // Start immediately after DOM is ready (no lazy-loading)
            if (document.readyState === "loading") {
                document.addEventListener("DOMContentLoaded", pump, {
                    once: true
                });
            } else {
                pump();
            }

            function parseAttrs(v) {
                if (!v) return {};
                if (typeof v === 'object') return v;
                try {
                    return JSON.parse(v);
                } catch {
                    return {};
                }
            }
        })
        ();
    </script>
@endonce

<div class="shortcode-lazy-loading" data-name="{{ $name }}" data-attributes="{{ json_encode($attributes) }}">
    <div class="loading-spinner"></div>
</div>
