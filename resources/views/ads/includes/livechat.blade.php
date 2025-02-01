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
<script src="https://cdnjs.cloudflare.com/ajax/libs/pusher/7.0.3/pusher.min.js"></script>
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

    document.addEventListener('DOMContentLoaded', function() {
    const matchId = {{ $matchId }}; // Assuming you have the matchId available in your Blade template

    // Initialize Pusher for real-time updates
    const pusher = new Pusher('{{ env('PUSHER_APP_KEY') }}', {
        cluster: '{{ env('PUSHER_APP_CLUSTER') }}',
        encrypted: false
    });

    // Subscribe to the specific match channel
    const channel = pusher.subscribe(`match.${matchId}`);
    channel.bind('App\\Events\\MessageSent', function(data) {
        console.log('Received message from Pusher at:', new Date().toISOString());
        appendMessage(data.message, data.member); // Append both message and member data
    });

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

        // Prepend the new message to the top of the messages list
        messagesList.insertBefore(newMessage, messagesList.firstChild);

        // Scroll to the top of the chat to show the latest message at the top
        const chatMessages = document.getElementById('chat-messages');
        chatMessages.scrollTop = 0;
    }

    // Send message function
    function sendMessage() {
        const messageInput = document.getElementById('message-input');
        const message = messageInput.value.trim();

        if (message === '') {
            return;
        }

        console.log('Sending message at:', new Date().toISOString());

        // Send message to the server
        axios.post(`/chat/${matchId}`, {
                message: message
            })
            .then(response => {
                console.log('Message sent successfully at:', new Date().toISOString());
                // Show the censored message returned from the server
                const censoredMessage = response.data.censored_message;
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

    // Fetch existing messages when the page loads
    window.onload = function() {
        axios.get(`/chat/${matchId}`)
            .then(response => {
                const messages = response.data.messages;
                messages.forEach(function(message) {
                    appendMessage(message, message.member);
                });
            })
            .catch(error => {
                console.error('Error fetching messages:', error);
            });
    };

    // Add event listener to the send message button
    const sendMessageButton = document.getElementById('send-message-btn');
    sendMessageButton.addEventListener('click', function(event) {
        sendMessage();
    });

    // Add event listener for the Enter key
    const messageInput = document.getElementById('message-input');
    messageInput.addEventListener('keyup', function(event) {
        if (event.key === 'Enter') {
            sendMessage();
        }
    });
});
</script>
