@php
    $matchId = request()->query('match_id'); // Get match_id from the URL query parameters
    $filePath = "chat/messages_{$matchId}.json";
    $webSocketUrl = 'ws://localhost:8080'; // Update this to your WebSocket server URL
@endphp
<style>
    .chat-container {
        position: sticky;
        top: 120px;
        max-width: 600px;
        margin: 0 auto;
        border-radius: 5px;
        padding: 10px;
    }

    .chat-title {
        background: #441274;
        color: white;
        padding: 10px 20px;
    }

    .chat-messages {
        height: 530px;
        overflow-y: scroll;
        border: 1px solid #ccc;
        padding: 10px;
        margin-bottom: 10px;
        background-color: white;
    }

    .message-bubble {
        display: flex;
        align-items: center;
        margin-bottom: 15px;
    }

    .message-avatar {
        width: 30px;
        height: 30px;
        border-radius: 50%;
        margin-right: 10px;
        background-color: #28a745;
        color: white;
        display: flex;
        justify-content: center;
        align-items: center;
        font-size: 16px;
    }

    .message-content {
        min-width: 30%;
        max-width: 70%;
        padding: 10px 15px;
        color: black;
        font-size: smaller color: black;
        border-radius: 10px;
        background-color: #f1f1f1;
    }

    .message-time {
        margin-top: 5px;
        font-size: 12px;
        color: #888;
    }

    .chat-form {
        display: flex;
        justify-content: space-between;
        padding-top: 10px;
    }

    .chat-form input[type="text"] {
        width: 80%;
        padding: 10px;
        border: 1px solid #ccc;
        border-radius: 50px 0px 0px 50px;
    }

    .chat-form button {
        position: relative;
        left: -10px;
        padding: 0px 10px;
        border: none;
        background-color: #441274;
        color: white;
        border-radius: 0px 50px 50px 0px;
    }
</style>

<div class="col-lg-4 mt-50">
    <div class="mb-4">@include('ads.includes.SIZE_468X60_TOP_SX')</div>

    <div class="chat-container">
        <h4 class="chat-title"><i class="far fa-comments"></i> Chat dei tifosi</h4>
        <!-- Messages Display Section -->
        <div class="chat-messages" id="chat-messages">
            <ul id="messages-list">
                <!-- Messages will be appended here -->
            </ul>
        </div>

        <!-- Form to Submit a New Message -->
        <div class="chat-form">
            @if (auth('member')->check())
                <input type="text" id="message-input" placeholder="Invia il tuo messaggio" />
                <button id="send-message-btn"><i class="fas fa-paper-plane"></i> Invia</button>
            @else
                <div class="row">
                    <p class="col-12 alert alert-warning">Per inviare un messaggio devi effettuare il login.</p>
                    <a style="background: #441274;color:white" href="/login" class="btn col-12 btn-purple"><i
                            class="fas fa-right-to-bracket"></i> Login</a>
                </div>
            @endif

        </div>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    document.addEventListener('DOMContentLoaded', () => {

        // ------------------------------------------------------------------
        // CONFIG
        // ------------------------------------------------------------------
        const matchId = '{{ $matchId }}'; // injected from Blade
        const wsUrl = "wss://websocket-1030861031399.europe-west1.run.app";
        const csrfToken = '{{ csrf_token() }}';

        const messagesUl = document.getElementById('messages-list');

        // ------------------------------------------------------------------
        // 1)  HELPER: build an <li> from a message object
        // ------------------------------------------------------------------
        const colorCache = {};

        function getAvatarColor(letter) {
            if (colorCache[letter]) return colorCache[letter];
            const colors = {
                A: '#3498db',
                B: '#2ecc71',
                C: '#e74c3c',
                D: '#f39c12',
                E: '#8e44ad',
                F: '#9b59b6',
                G: '#16a085',
                H: '#e67e22',
                I: '#f1c40f',
                J: '#e84393',
                K: '#34495e'
            };
            return colorCache[letter] = colors[letter] || '#95a5a6';
        }

        function renderMessage(msg) {
            const li = document.createElement('li');
            li.className = 'message-bubble';

            const avatar = document.createElement('div');
            avatar.className = 'message-avatar';
            avatar.textContent = (msg.member?.first_name ?? 'A').charAt(0).toUpperCase();
            avatar.style.backgroundColor = getAvatarColor(avatar.textContent);

            const wrap = document.createElement('div');
            wrap.className = 'message-content';
            wrap.innerHTML = `
             <strong style="font-size:small">
                 ${(msg.member?.first_name ?? '')} ${(msg.member?.last_name ?? '')}
             </strong><br>
             ${msg.message}
             <div class="message-time">
                 ${new Date(msg.created_at).toLocaleTimeString()}
             </div>`;

            li.append(avatar, wrap);
            return li;
        }

        // ------------------------------------------------------------------
        // 2)  HTTP fetch helpers
        // ------------------------------------------------------------------
        function refreshMessages() {
            fetch(`/chat/${matchId}`)
                .then(r => r.json())
                .then(({
                    messages
                }) => {
                    messagesUl.innerHTML = '';
                    messages.reverse().forEach(msg => { // newest first
                        // fallback member if missing
                        if (!msg.member && msg.user_id == 1) {
                            msg.member = {
                                first_name: 'Redazione',
                                last_name: 'LaViola',
                                avatar: null
                            };
                        }
                        messagesUl.append(renderMessage(msg));
                    });
                })
                .catch(console.error);
        }

        // initial load
        refreshMessages();

        // ------------------------------------------------------------------
        // 3)  WebSocket with reconnect & keep‑alive ping
        // ------------------------------------------------------------------
        let ws, pingInterval;

        function connectWs() {
            ws = new WebSocket(wsUrl);

            ws.onopen = () => {
                console.log('[chat] WS connected');
                // subscribe to the correct file
                ws.send(JSON.stringify({
                    filePath: `chat/messages_${matchId}.json`
                }));
                // keep‑alive ping every 30 s
                pingInterval = setInterval(() => {
                    if (ws.readyState === WebSocket.OPEN) ws.send('{"type":"ping"}');
                }, 30000);
            };

            ws.onmessage = e => {
                console.log('[chat] change notice', e.data);
                refreshMessages();
            };

            ws.onerror = err => console.error('[chat] WS error', err);

            ws.onclose = () => {
                console.log('[chat] WS closed – reconnect in 5 s');
                clearInterval(pingInterval);
                setTimeout(connectWs, 5000);
            };
        }
        connectWs();

        // ------------------------------------------------------------------
        // 4)  Fallback polling every 70 s (optional)
        // ------------------------------------------------------------------
        setInterval(() => {
            // triggers backend sync (if you have it) then refresh list
            // fetch(`/match/${matchId}/sync-all-commentaries`).catch(() => {});
            refreshMessages();
        }, 70000);

        // ------------------------------------------------------------------
        // 5)  SEND, EDIT, DELETE hooks (reuse existing code)
        //     call refreshMessages() on success so UI updates instantly
        // ------------------------------------------------------------------
        @if (auth('member')->check())
            const sendBtn = document.getElementById('send-message-btn');
            const input = document.getElementById('message-input');

            function postMessage() {
                const txt = input.value.trim();
                if (!txt) return;
                fetch(`/chat/${matchId}`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            message: txt
                        })
                    })
                    .then(r => r.json())
                    .then(res => {
                        input.value = '';
                        refreshMessages(); // immediate refresh
                    })
                    .catch(console.error);
            }

            sendBtn.addEventListener('click', postMessage);
            input.addEventListener('keyup', e => (e.key === 'Enter') && postMessage());
        @endif

        // Your existing edit / delete JS remains; after success call refreshMessages()
    });



    // Remove polling, since WebSocket now handles updates
    // setInterval(fetchMessages, 2500);
</script>
