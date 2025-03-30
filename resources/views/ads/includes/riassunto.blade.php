<div class="container" id="summary-container">
    {{-- 
      1) Blade-rendered summary on initial load 
         (grouping by stage_name, showing incidents).
      2) If you want to refresh it dynamically, you
         can either keep this or render it purely via JS.
    --}}
    @foreach ($summaries->groupBy('stage_name') as $stageName => $items)
        <div class="stage mb-4">
            <h4 class="stage-tempo">{{ $stageName }}</h4>

            @foreach ($items as $item)
                @php
                    $participants = json_decode($item->incident_participants, true);
                    $isHomeTeam = $item->incident_team == 1; // Team 1 for home, Team 2 for away
                @endphp

                <div
                    class="incident d-flex align-items-center mb-2 {{ $isHomeTeam ? 'justify-content-start' : 'justify-content-end' }}">
                    @if ($isHomeTeam)
                        <!-- Home Team Incident -->
                        <div class="incident-content d-flex align-items-center">
                            <div class="incident-time">
                                {{ $item->incident_time }}
                            </div>
                            <div class="incident-icon m-2">
                                @if ($participants[0]['incident_type'] === 'GOAL')
                                    <i class="fa fa-futbol"></i>
                                @elseif ($participants[0]['incident_type'] === 'YELLOW_CARD')
                                    <i class="fa fa-square text-warning"></i>
                                    @if (isset($participants[1]) && $participants[1]['incident_type'] === 'RED_CARD')
                                        <i class="fa fa-square text-danger"></i>
                                    @endif
                                @elseif ($participants[0]['incident_type'] === 'RED_CARD')
                                    <i class="fa fa-square text-danger"></i>
                                @elseif ($participants[0]['incident_type'] === 'SUBSTITUTION_OUT')
                                    <i class="fa fa-exchange-alt"></i>
                                @elseif ($participants[0]['incident_type'] === 'PENALTY_KICK')
                                    @if (isset($participants[1]) && $participants[1]['incident_type'] === 'PENALTY_MISSED')
                                        <i class="fa fa-xmark text-danger"></i>
                                    @elseif (isset($participants[1]) && $participants[1]['incident_type'] === 'PENALTY_SCORED')
                                        <i class="fa fa-futbol"></i>
                                    @endif
                                @endif
                            </div>
                            <div class="incident-detail">
                                @foreach ($participants as $p)
                                    @if ($p['incident_type'] == 'ASSISTANCE')
                                        <span>{{ $p['participant_name'] }}</span>
                                    @else
                                        <strong>{{ $p['participant_name'] }}</strong>
                                    @endif
                                    @if (!$loop->last)
                                        ,
                                    @endif
                                @endforeach

                                {{-- If it's a goal, show partial score if available --}}
                                @if ($item->incident_type === 'GOAL' && isset($participants[0]['home_score']) && isset($participants[0]['away_score']))
                                    <span> ({{ $participants[0]['home_score'] }} -
                                        {{ $participants[0]['away_score'] }})</span>
                                @endif
                            </div>
                        </div>
                    @else
                        <!-- Away Team Incident -->
                        <div class="incident-content d-flex align-items-center">
                            <div class="incident-detail text-right mr-2">
                                @foreach ($participants as $p)
                                    <strong>{{ $p['participant_name'] }}</strong>
                                    @if (!$loop->last)
                                        ,
                                    @endif
                                @endforeach
                                @if ($item->incident_type === 'GOAL' && isset($participants[0]['home_score']) && isset($participants[0]['away_score']))
                                    <span> ({{ $participants[0]['home_score'] }} -
                                        {{ $participants[0]['away_score'] }})</span>
                                @endif
                            </div>
                            <div class="incident-icon m-2">
                                @if ($participants[0]['incident_type'] === 'GOAL')
                                    <i class="fa fa-futbol"></i>
                                @elseif ($participants[0]['incident_type'] === 'YELLOW_CARD')
                                    <i class="fa fa-square text-warning"></i>
                                    @if (isset($participants[1]) && $participants[1]['incident_type'] === 'RED_CARD')
                                        <i class="fa fa-square text-danger"></i>
                                    @endif
                                @elseif ($participants[0]['incident_type'] === 'RED_CARD')
                                    <i class="fa fa-square text-danger"></i>
                                @elseif ($participants[0]['incident_type'] === 'SUBSTITUTION_OUT')
                                    <i class="fa fa-exchange-alt"></i>
                                @elseif ($participants[0]['incident_type'] === 'PENALTY_KICK')
                                    @if (isset($participants[1]) && $participants[1]['incident_type'] === 'PENALTY_MISSED')
                                        <i class="fa fa-xmark text-danger"></i>
                                    @elseif (isset($participants[1]) && $participants[1]['incident_type'] === 'PENALTY_SCORED')
                                        <i class="fa fa-futbol"></i>
                                    @endif
                                @endif
                            </div>
                            <div class="incident-time">
                                {{ $item->incident_time }}
                            </div>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    @endforeach
</div>




<script>
    document.addEventListener("DOMContentLoaded", function() {
        const matchId = "{{ $matchId }}";
        const wsUrl = "wss://weboscket-laviola-341264949013.europe-west1.run.app";
        let ws;

        // ----------------------
        // 1) Function to fetch summary (JSON) from /match/{matchId}/summary
        //    Then we re-render the #summary-container. For demonstration,
        //    we just dump JSON text. If you want to replicate the exact
        //    incident layout, you'd parse the JSON and build HTML similarly
        //    to how the Blade is doing it above.
        // ----------------------
        function fetchSummaryHtml() {
            fetch(`/match/${matchId}/summary-html`)
                .then(res => res.text()) // We expect an HTML partial
                .then(html => {
                    document.getElementById('summary-container').innerHTML = html;
                })
                .catch(console.error);
        }

        // ----------------------
        // 2) Create WebSocket for summary_{matchId}.json
        // ----------------------
        function createWebSocket() {
            ws = new WebSocket(wsUrl);

            ws.onopen = () => {
                console.log("WebSocket for summary connected.");
                // Subscribe to summary_{matchId}.json
                ws.send(JSON.stringify({
                    filePath: `summary/summary_${matchId}.json`
                }));
            };

            ws.onmessage = (event) => {
                console.log("Summary file changed:", event.data);
            };

            ws.onerror = (error) => {
                console.error("WebSocket error:", error);
            };

            ws.onclose = () => {
                console.log("WS summary closed. Reconnecting in 5 seconds...");
                setTimeout(createWebSocket, 5000);
            };
        }

        // ----------------------
        // 3) Kick things off
        // ----------------------
        // Optionally call fetchSummary() once if you want to see the JSON rendering
        // right away. But if you want to preserve the Blade HTML above, you can skip it.
        // fetchSummary();

        createWebSocket();

        // ----------------------
        // 4) Additional refresh and time interval
        //    e.g. calling store-commentaries every 3 seconds,
        //    plus sending subscription again for chat/messages_{matchId}.json
        // ----------------------
        setInterval(() => {

            fetch(`/match/${matchId}/refresh-summary`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    console.log('refresh-summary triggered:', data);
                })
                .catch(error => {
                    console.error('Error calling refresh-summary:', error);
                });
            setTimeout(fetchSummaryHtml, 10000);
            // 4B) Re-subscribe to chat/messages_{matchId}.json (if you want)
            const subscriptionMessage1 = JSON.stringify({
                filePath: `summary/summary_${matchId}.json`
            });
            ws.send(subscriptionMessage1);

        }, 60000); // every 60 seconds
    });
</script>
