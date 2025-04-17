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
        const csrfToken = '{{ csrf_token() }}';
        /* ==============================================================
         * 1)  DELETE commentary (soft‑delete) and refresh immediately
         * ============================================================== */
        window.deleteCommentary = async function(commentId, evt) {
            evt.preventDefault();
            if (!confirm('Are you sure you want to delete this commentary?')) return;

            try {
                // 1. delete row
                await fetch(`/commentary/${commentId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken
                    }
                });

                // 2. hide edit box (if open)
                toggleEditBox(commentId);

                // 3. tell backend to sync JSON
                await fetch(`/match/${matchId}/sync-all-commentaries`);

                // 4. refresh UI list
                if (typeof refreshCommentaries === 'function') refreshCommentaries();

            } catch (e) {
                console.error(e);
                alert('Error deleting commentary.');
            }
        };

        /* ==============================================================
         * 2)  EDIT commentary (AJAX PATCH) and refresh immediately
         * ============================================================== */
        window.submitEditForm = async function(evt, id) {
            evt.preventDefault();
            const form = evt.target;
            const data = new FormData(form);

            try {
                // 1. update row
                await fetch(`/commentary/${id}`, {
                    method: 'PATCH',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: data
                });

                // 2. hide edit box
                toggleEditBox(id);

                // 3. sync JSON on server
                await fetch(`/match/${matchId}/sync-all-commentaries`);

                // 4. refresh UI
                if (typeof refreshCommentaries === 'function') refreshCommentaries();

            } catch (e) {
                console.error(e);
                alert('Error updating commentary.');
            }

            return false;
        };

        /* ==============================================================
         * 3)  Toggle edit box (unchanged)
         * ============================================================== */
        window.toggleEditBox = function(id, evt) {
            if (evt) evt.preventDefault();
            const box = document.getElementById(`edit-box-${id}`);
            if (!box) return;
            box.style.display = (box.style.display === 'none' || !box.style.display) ?
                'block' : 'none';
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
