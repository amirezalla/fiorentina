@php
    use App\Http\Controllers\MatchCommentaryController;
@endphp

<!-- resources/views/match-commentary.blade.php or similar -->
<div class="container mt-3" id="commentary-container"></div>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        var matchId = "{{ $matchId }}"; // from Blade
        var wsUrl = "wss://weboscket-laviola-341264949013.europe-west1.run.app";

        // ---------------------------------------
        // 1) Function to fetch commentary via HTTP
        // ---------------------------------------
        function fetchCommentaries() {
            fetch('/match/' + matchId + '/commentaries')
                .then(response => response.json())
                .then(data => updateCommentaries(data))
                .catch(console.error);
        }

        function updateCommentaries(commentaries) {
            var container = document.getElementById('commentary-container');
            container.innerHTML = ''; // Clear existing commentaries

            commentaries.forEach(function(comment) {
                var commentRow = `
                    <div class="commentary-row ${comment.comment_class}
                         ${comment.is_important ? 'important' : ''}
                         ${comment.is_bold ? 'comment-is-bold' : ''}">
                        <div class="comment-time">${comment.comment_time || ''}</div>
                        <div class="comment-icon"></div>
                        <div class="comment-text ${comment.is_bold ? 'comment-bold' : ''}">
                            ${comment.comment_text || ''}
                        </div>
                    </div>
                `;
                container.innerHTML += commentRow;
            });
        }

        // ---------------------------------------
        // 2) Create WebSocket (with reconnect)
        // ---------------------------------------
        let ws; // we'll store the websocket instance here
        function createWebSocket() {
            ws = new WebSocket(wsUrl);

            ws.onopen = () => {
                console.log("WebSocket connected for commentary.");

                // Tell server which file to watch
                const subscriptionMessage = JSON.stringify({
                    filePath: `commentary/commentary_${matchId}.json`
                });
                ws.send(subscriptionMessage);
            };

            ws.onmessage = (event) => {
                console.log("WebSocket commentary update received:", event.data);
                // Reload commentary from Laravel
                fetchCommentaries();
            };

            ws.onerror = (error) => {
                console.error("WebSocket error (commentary):", error);
            };

            ws.onclose = () => {
                console.log("WebSocket closed (commentary). Reconnecting in 5 seconds...");
                // Attempt to reconnect after 5 seconds
                setTimeout(createWebSocket, 5000);
            };
        }

        // ---------------------------------------
        // 3) Kick things off
        // ---------------------------------------
        fetchCommentaries(); // Initial data load
        createWebSocket(); // Create WebSocket and auto-reconnect on close

        // Optional: fallback polling (every 15s) if you want extra safety
        setInterval(() => {

            fetch(`/match/${matchId}/sync-all-commentaries`)
                .then(res => res.json())
                .then(console.log)
                .catch(console.error);




            const subscriptionMessage1 = JSON.stringify({
                filePath: `chat/messages_${matchId}.json`
            });
            ws.send(subscriptionMessage1);
        }, 3000);
    });
</script>
