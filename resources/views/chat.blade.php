<!DOCTYPE html>
<html>
<head>
    <title>Chat with LLM</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <div id="chat-container">
        <div id="chat-messages"></div>
        <input type="text" id="message-input" placeholder="Type your message...">
        <button onclick="sendMessage()">Send</button>
        <button onclick="clearChat()">Clear Chat</button>
    </div>

    <script>
        function sendMessage() {
            const message = $('#message-input').val();
            if (!message) return;

            $('#message-input').val('');
            $('#chat-messages').append(`<p><strong>You:</strong> ${message}</p>`);

            $.ajax({
                url: '/api/chat',
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: { message: message },
                success: function(response) {
                    if (response.success) {
                        $('#chat-messages').append(`<p><strong>Assistant:</strong> ${response.message}</p>`);
                    } else {
                        $('#chat-messages').append(`<p class="error">Error: ${response.message}</p>`);
                    }
                },
                error: function(xhr) {
                    const error = xhr.responseJSON || { message: 'An unknown error occurred' };
                    $('#chat-messages').append(`<p class="error">Error: ${error.message}</p>`);
                }
            });
        }

        function clearChat() {
            $.ajax({
                url: '/api/clear-chat',
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function() {
                    $('#chat-messages').empty();
                }
            });
        }
    </script>
</body>
</html>