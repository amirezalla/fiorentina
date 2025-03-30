@php
    use App\Http\Controllers\MatchCommentaryController;
@endphp

<!-- resources/views/match-commentary.blade.php or similar -->
<div class="container mt-3" id="commentary-container"></div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        var matchId = "{{ $matchId }}"; // from Blade
        var interval = 15000; // 15s, if you still want fallback polling (optional)

        // Function to fetch latest commentaries via Laravel
        function fetchCommentaries() {
            fetch('/match/' + matchId + '/commentaries')
                .then(response => response.json())
                .then(data => {
                    updateCommentaries(data);
                })
                .catch(error => {
                    console.error('Error fetching commentaries:', error);
                });
        }

        // Function to update the commentary section with new data
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

        // -----------------------
        // 1) Initial fetch
        // -----------------------
        fetchCommentaries();

        // -----------------------
        // 2) OPTIONAL Polling
        // -----------------------
        // If you rely on WebSocket, you can remove or reduce this
        setInterval(() => {
            const subscriptionMessage1 = JSON.stringify({
                filePath: `commentary/commentary_${matchId}.json`
            });
            ws.send(subscriptionMessage1);
        }, 2000);
        // -----------------------
        // 3) Setup WebSocket
        // -----------------------
        const wsUrl = "wss://weboscket-laviola-341264949013.europe-west1.run.app";
        const ws = new WebSocket(wsUrl);

        ws.onopen = function() {
            console.log("WebSocket connection for commentary established.");
            // Instruct server to watch the Wasabi file for commentary
            // e.g. "commentary/commentary_12345.json"
            const subscriptionMessage = JSON.stringify({
                filePath: `commentary/commentary_${matchId}.json`
            });
            ws.send(subscriptionMessage);
        };

        ws.onmessage = function(event) {
            console.log("WebSocket commentary update received:", event.data);

            // Since the file changed in Wasabi, let's fetch from our Laravel endpoint
            fetchCommentaries();
        };

        ws.onerror = function(error) {
            console.error("WebSocket error (commentary):", error);
        };

        ws.onclose = function() {
            console.log("WebSocket closed (commentary).");
        };
    });
</script>
