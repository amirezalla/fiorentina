@php
    use Illuminate\Support\Str;
@endphp
<style>
    .edit-box {
        padding: 10px;
        border: 1px solid #ddd;
        background-color: #f9f9f9;
        border-radius: 5px;
    }
</style>
<div class="container mt-3" id="commentary-container"></div>




<script>
    document.addEventListener("DOMContentLoaded", function() {
        var matchId = "{{ $matchId }}";
        var wsUrl = "wss://weboscket-laviola-341264949013.europe-west1.run.app";

        function fetchCommentaries() {
            fetch('/match/' + matchId + '/commentaries')
                .then(response => response.json())
                .then(data => renderAdminCommentaries(data))
                .catch(console.error);
        }

        function renderAdminCommentaries(commentaries) {
            const container = document.getElementById('commentary-container');
            container.innerHTML = '';

            commentaries.forEach(comment => {
                // Insert your admin row structure (edit, delete, time, text, etc.)
                container.innerHTML += buildAdminRow(comment);
            });
        }

        // Build one row of admin commentary (including the edit box HTML)
        function buildAdminRow(comment) {
            // We'll replicate your existing snippet
            let rowHtml = `
            <div class="commentary-row ${comment.comment_class} 
                ${comment.is_important ? 'important' : ''} 
                ${comment.is_bold ? 'comment-is-bold' : ''}"
                data-id="${comment.id}">
                <div class="comment-time" style="flex: 0.5">${comment.comment_time || ''}</div>
                <div style="flex: 0.5">
                    <a style="margin-right: 5px" href="#" onclick="deleteCommentary(${comment.id}, event)">
                        <i class="text-danger fa-solid fa-trash"></i>
                    </a>
                    <a style="margin-right: 5px" href="#" onclick="toggleEditBox(${comment.id}, event)">
                        <i class="text-white fa-solid fa-pen-to-square"></i>
                    </a>
                </div>
                <div class="comment-icon"></div>
                <div class="comment-text ${comment.is_bold ? 'comment-bold' : ''}">
                    ${comment.comment_text || ''}
                </div>
            </div>

            <div id="edit-box-${comment.id}" class="edit-box" style="display: none; margin-top: 10px;">
                <form action="/update-commentary" method="POST" onsubmit="return submitEditForm(event, ${comment.id})">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" name="id" value="${comment.id}">
                    <textarea name="comment_text" class="form-control" rows="3">${comment.comment_text || ''}</textarea>

                    <div class="form-check mt-2">
                        <input type="checkbox" name="is_important" class="form-check-input"
                               id="is_important_${comment.id}" ${comment.is_important ? 'checked' : ''}>
                        <label class="form-check-label" for="is_important_${comment.id}">Important</label>
                    </div>

                    <div class="form-check">
                        <input type="checkbox" name="is_bold" class="form-check-input"
                               id="is_bold_${comment.id}" ${comment.is_bold ? 'checked' : ''}>
                        <label class="form-check-label" for="is_bold_${comment.id}">Bold</label>
                    </div>

                    <button type="submit" class="btn btn-primary mt-2">Save</button>
                    <button type="button" class="btn btn-secondary mt-2" onclick="toggleEditBox(${comment.id})">
                        Cancel
                    </button>
                </form>
            </div>
            `;
            return rowHtml;
        }

        // Delete commentary via AJAX, so we don’t need a full page reload
        window.deleteCommentary = function(commentId, event) {
            event.preventDefault();
            if (!confirm('Are you sure you want to delete this commentary?')) return;

            fetch(`/delete-commentary?id=${commentId}`, {
                    method: 'GET', // or 'POST' if your route is POST
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                })
                .then(res =>
                    if (res.success) {
                        // ── kick the Laravel sync (stores JSON, WebSocket will notice)
                        fetch(`/match/${matchId}/sync-all-commentaries`).catch(() => {});
                        // ── or refresh immediately:
                        if (typeof refreshCommentaries === 'function') refreshCommentaries();
                    })
                .then(() => {
                    // The server will handle rewriting the JSON.
                    // We'll just rely on the WebSocket to refresh, or we can call fetchCommentaries()
                    // fetchCommentaries();
                })
                .catch(console.error);
        };

        // Toggle the edit box
        window.toggleEditBox = function(id, event) {
            if (event) event.preventDefault();
            const editBox = document.getElementById(`edit-box-${id}`);
            if (!editBox) return;

            editBox.style.display = (editBox.style.display === 'none' || !editBox.style.display) ?
                'block' :
                'none';
        };

        // Submit the edit form via AJAX
        window.submitEditForm = function(event, id) {
            event.preventDefault();
            const form = event.target;
            const formData = new FormData(form);

            fetch(form.action, {
                    method: 'POST',
                    body: formData,
                })
                .then(res =>
                    if (res.success) {
                        // ── kick the Laravel sync (stores JSON, WebSocket will notice)
                        fetch(`/match/${matchId}/sync-all-commentaries`).catch(() => {});
                        // ── or refresh immediately:
                        if (typeof refreshCommentaries === 'function') refreshCommentaries();
                    })
                .then(() => {
                    // The server updates DB, rewrites Wasabi JSON
                    // We rely on WebSocket to refresh automatically
                    // Or we can do fetchCommentaries() if you want immediate update
                    // fetchCommentaries();
                })
                .catch(console.error);

            // Hide the edit box
            toggleEditBox(id);
            return false;
        };

        // ---------------------------------------
        // Setup WebSocket with Reconnect
        // ---------------------------------------
        let ws;

        function createWebSocket() {
            ws = new WebSocket(wsUrl);

            ws.onopen = () => {
                console.log("WebSocket connected (admin).");
                // Subscribe to commentary
                ws.send(JSON.stringify({
                    filePath: `commentary/commentary_${matchId}.json`
                }));
            };

            ws.onmessage = (event) => {
                console.log("WS update (admin):", event.data);
                // Re-fetch from the server to see the updated commentary
                fetchCommentaries();
            };

            ws.onerror = (error) => {
                console.error("WebSocket error:", error);
            };

            ws.onclose = () => {
                console.log("WebSocket closed. Reconnecting in 5 seconds...");
                setTimeout(createWebSocket, 5000);
            };
        }

        // Initialize
        fetchCommentaries();
        createWebSocket();
        setInterval(() => {

            fetch(`/match/${matchId}/sync-all-commentaries`)
                .then(res => res.json())
                .then(console.log)
                .catch(console.error);




            const subscriptionMessage1 = JSON.stringify({
                filePath: `chat/messages_${matchId}.json`
            });
            ws.send(subscriptionMessage1);
        }, 60000);
    });
</script>
