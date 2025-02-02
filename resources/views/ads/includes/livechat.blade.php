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
    const colorCache = {};

    function getAvatarColor(firstLetter) {
        const letter = firstLetter.toUpperCase();
        if (colorCache[letter]) return colorCache[letter];

        let color;
        switch (letter) {
            case 'A':
                color = '#3498db';
                break; // Blue
            case 'B':
                color = '#2ecc71';
                break; // Green
            case 'C':
                color = '#e74c3c';
                break; // Red
            case 'D':
                color = '#f39c12';
                break; // Orange
            case 'E':
                color = '#8e44ad';
                break; // Dark Purple
            case 'F':
                color = '#9b59b6';
                break; // Purple
            case 'G':
                color = '#16a085';
                break; // Teal
            case 'H':
                color = '#e67e22';
                break; // Orange
            case 'I':
                color = '#f1c40f';
                break; // Yellow
            case 'J':
                color = '#e84393';
                break; // Pink
            case 'K':
                color = '#34495e';
                break; // Navy Blue
            default:
                color = '#95a5a6'; // Default Gray
        }

        colorCache[letter] = color;
        return color;
    }

    // Setup CSRF token for axios
    axios.defaults.headers.common['X-CSRF-TOKEN'] = '{{ csrf_token() }}';

    // Extract match_id from the URL
    const urlParams = new URLSearchParams(window.location.search);
    const matchId = urlParams.get('match_id'); // Get match_id from the URL

    if (!matchId) {
        console.error('Match ID is missing in the URL.');
    }

    // Function to append a message to the messages list
    function appendMessage(message, member) {
        const messagesList = document.getElementById('messages-list');

        const newMessage = document.createElement('li');
        newMessage.classList.add('message-bubble');

        const avatar = document.createElement('div');
        avatar.classList.add('message-avatar');
        avatar.textContent = member.first_name.charAt(0).toUpperCase() || 'A';
        avatar.style.backgroundColor = getAvatarColor(member.first_name.charAt(0));

        const messageContent = document.createElement('div');
        messageContent.classList.add('message-content');
        messageContent.innerHTML = `
            <strong style='font-size:small'>${member.first_name} ${member.last_name}</strong><br>
            ${message.message}
            <div class="message-time">${new Date(message.created_at).toLocaleTimeString()}</div>
        `;

        newMessage.appendChild(avatar);
        newMessage.appendChild(messageContent);

        // Append the new message to the top of the messages list
        messagesList.insertBefore(newMessage, messagesList.firstChild);
    }

    @if (auth('member')->check())
        // Send message when button is clicked or enter key is pressed
        const sendMessageButton = document.getElementById('send-message-btn');
        const messageInput = document.getElementById('message-input');

        messageInput.addEventListener('keyup', function(event) {
            if (event.key === 'Enter') {
                sendMessage();
            }
        });

        sendMessageButton.addEventListener('click', function(event) {
            sendMessage();
        });
    @endif

    function sendMessage() {
        console.log('Sending message at:', new Date().toISOString());

        const message = messageInput.value.trim();

        if (message === '') {
            return;
        }

        // Send message to the server
        axios.post(`/chat/${matchId}`, {
                message: message
            })
            .then(response => {
                console.log('Message sent successfully at:', new Date().toISOString());
                // Show the censored message returned from the server
                const censoredMessage = response.data.censored_message;
                console.log('Censored message:', censoredMessage);
                messageInput.value = ''; // Clear input field
            })
            .catch(error => {
                if (error.response && error.response.status === 400) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: error.response.data.error, // Show error message
                    });
                } else {
                    console.error('Error sending message:', error);
                }
            });
    }

    // Function to fetch existing messages from the server
    function fetchMessages() {
        axios.get(`/chat/${matchId}`)
            .then(response => {
                const messages = response.data.messages;
                document.getElementById('messages-list').innerHTML = ''; // Clear existing messages
                messages.forEach(function(message) {
                    appendMessage(message, message.member);
                });
            })
            .catch(error => {
                console.error('Error fetching messages:', error);
            });
    }

    window.onload = function() {
        // Fetch messages on initial load
        fetchMessages();

        // Set up the WebSocket connection
        const ws = new WebSocket("wss://weboscket-laviola-341264949013.europe-west1.run.app"); // Replace with your actual WebSocket URL
        const subscriptionMessage1 = JSON.stringify({
            filePath: `chat/messages_${matchId}.json`
        });
        setInterval(ws.send(subscriptionMessage1), 1000);

        ws.onopen = function() {
            console.log("WebSocket connection established.");

            // Send the filePath message to instruct the server which file to check
            const subscriptionMessage = JSON.stringify({
                filePath: `chat/messages_${matchId}.json`
            });
            ws.send(subscriptionMessage);
        };

        ws.onmessage = function(event) {
            console.log("WebSocket message received: ", event.data);
            // When a message is received from the WebSocket server (indicating a change),
            // fetch the latest messages from your server.
            fetchMessages();
        };

        ws.onerror = function(error) {
            console.error("WebSocket error: ", error);
        };

        ws.onclose = function() {
            console.log("WebSocket connection closed.");
        };

        // Remove polling, since WebSocket now handles updates
        // setInterval(fetchMessages, 2500);
    };


</script>
