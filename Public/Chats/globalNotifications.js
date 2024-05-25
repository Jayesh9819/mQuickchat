function checkForNewMessages() {
    fetch('../Public/Chats/checkNewMessages.php')
        .then(response => response.json())
        .then(data => {
            if (data.newMessages) {
                console.log(data.newMessages);
                // Attempt to play notification sound automatically when new messages are received.
                var audio = new Audio('../Public/Chats/notification.wav');
                audio.play().catch(error => {
                    console.error('Error playing sound:', error);
                    // This catch block is here because browsers may block the sound from playing automatically without user interaction.
                });
            }
        })
        .catch(error => console.error('Error fetching new messages:', error));
}

setInterval(checkForNewMessages, 10000);
