<div class="statistics-container" id="stats-container">

    @foreach ($statics as $stat)
        @if ($stat['stage_name'] == 'Partita')
            <div class="stat-row mb-3">
                <!-- Home Value -->
                <div class="stat-value">{{ $stat['value_home'] }}</div>

                <!-- Stat Bar -->
                <div class="stat-bar">
                    @php
                        // Ensure numeric values and remove %
                        $valueHome = is_numeric(str_replace('%', '', $stat['value_home']))
                            ? (float) str_replace('%', '', $stat['value_home'])
                            : 0;
                        $valueAway = is_numeric(str_replace('%', '', $stat['value_away']))
                            ? (float) str_replace('%', '', $stat['value_away'])
                            : 0;

                        // Always calculate max value for consistent proportions
                        $maxValue = $valueHome + $valueAway;

                        // Calculate widths
                        if ($maxValue != 0) {
                            $homeWidth = ($valueHome / $maxValue) * 100;
                            $awayWidth = ($valueAway / $maxValue) * 100;
                        } else {
                            $homeWidth = $awayWidth = 0;
                        }

                        // Dynamic CSS classes
                        $homeClass = $isHomeFiorentina ? 'fiorentina-fill' : 'away-fill';
                        $awayClass = !$isHomeFiorentina ? 'fiorentina-fill' : 'away-fill';
                    @endphp

                    <!-- Home Team Bar (always left) -->
                    <div class="stat-bar-fill {{ $homeClass }}" style="width: {{ $homeWidth }}%;"></div>

                    <!-- Away Team Bar (always right) -->
                    <div class="stat-bar-fill {{ $awayClass }}" style="width: {{ $awayWidth }}%;"></div>
                </div>

                <!-- Stat Label -->
                <div class="stat-label text-dark">{{ $stat['incident_name'] }}</div>

                <!-- Away Value -->
                <div class="stat-value">{{ $stat['value_away'] }}</div>
            </div>
        @endif
    @endforeach
</div>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const matchId = "{{ $matchId }}";
        const wsUrl = "wss://weboscket-laviola-341264949013.europe-west1.run.app";
        let ws;

        // 1) Function to fetch the partial HTML
        function fetchStatsHtml() {
            // This endpoint returns the partial, e.g. /match/{matchId}/stats-html
            fetch(`/match/${matchId}/stats-html`)
                .then(res => res.text()) // we expect HTML
                .then(html => {
                    // Replace #stats-container content
                    document.getElementById('stats-container').innerHTML = html;
                })
                .catch(console.error);
        }

        // 2) Create WebSocket for stats/stats_{matchId}.json
        function createWebSocket() {
            ws = new WebSocket(wsUrl);

            ws.onopen = () => {
                console.log("WebSocket for stats connected.");
                ws.send(JSON.stringify({
                    filePath: `stats/stats_${matchId}.json`
                }));
            };

            ws.onmessage = (event) => {
                console.log("Stats file changed:", event.data);

            };

            ws.onerror = console.error;
            ws.onclose = () => {
                console.log("WS stats closed. Reconnecting in 5 seconds...");
                setTimeout(createWebSocket, 5000);
            };
        }

        // 3) Kick it off
        // We already have initial Blade HTML, so we only do fetchStatsHtml() if we want to guarantee 
        // the DB matches the displayed data immediately. Otherwise, we rely on real-time updates.
        // fetchStatsHtml();

        createWebSocket();

        // 4) Example: refresh logic every 3 seconds
        setInterval(() => {
            fetch(`/match/${matchId}/refresh-stats`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    }
                })
                .then(res => res.json())
                .then(data => console.log('refresh-stats triggered:', data))
                .catch(console.error);
            setTimeout(fetchStatsHtml, 10000);


        }, 60000);
    });
</script>
