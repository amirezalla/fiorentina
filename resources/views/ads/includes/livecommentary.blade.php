<div class="container mt-3" id="commentary-container"></div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const matchId = "{{ $matchId }}";
        const wsUrl = "wss://weboscket-laviola-341264949013.europe-west1.run.app";

        // --- 1. Load from Laravel (reading Wasabi JSON file only) ---
        function fetchCommentaries() {
            fetch(`/match/${matchId}/commentaries`)
                .then(response => response.json())
                .then(updateCommentaries)
                .catch(console.error);
        }
        fetch(`/match/${matchId}/sync-all-commentaries`)

        function updateCommentaries(commentaries) {
            const container = document.getElementById('commentary-container');
            container.innerHTML = ''; // Clear previous

            commentaries.forEach(comment => {
                const row = `
                    <div class="commentary-row ${comment.comment_class || ''}
                         ${comment.is_important ? 'important' : ''}
                         ${comment.is_bold ? 'comment-is-bold' : ''}">
                        <div class="comment-time">${comment.comment_time || ''}</div>
                        <div class="comment-icon"></div>
                        <div class="comment-text ${comment.is_bold ? 'comment-bold' : ''}">
                            ${comment.comment_text || ''}
                        </div>
                    </div>
                `;
                container.innerHTML += row;
            });
        }

        // --- 2. WebSocket live listener for file updates ---
        let ws;

        function createWebSocket() {
            ws = new WebSocket(wsUrl);

            ws.onopen = () => {
                console.log("WebSocket connected.");
                ws.send(JSON.stringify({
                    filePath: `commentary/commentary_${matchId}.json`
                }));
            };

            ws.onmessage = () => {
                console.log("Update received. Reloading commentary.");
                fetchCommentaries();
            };

            ws.onerror = error => {
                console.error("WebSocket error:", error);
            };

            ws.onclose = () => {
                console.warn("WebSocket closed. Reconnecting...");
                setTimeout(createWebSocket, 5000);
            };
        }

        // --- 3. Kick things off ---
        fetchCommentaries();
        createWebSocket();

        // --- Optional polling fallback if WebSocket fails silently ---
        setInterval(fetchCommentaries, 30000);
    });
</script>
