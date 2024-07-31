function checkForNewMessages() {
    fetch('../Public/Chats/newmsg.php')
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok: ' + response.statusText);
            }
            return response.json();
        })
        .then(data => {
            if (data.newMessages) {
                console.log(data.newMessages);
                // Attempt to play notification sound automatically when new messages are received.
                var audio = new Audio('../Public/Chats/user.mp3');
                audio.play().catch(error => {
                    console.error('Error playing sound:', error);
                    // This catch block is here because browsers may block the sound from playing automatically without user interaction.
                });
            }
        })
        .catch(error => {
            console.error('Error fetching new messages:', error);
        });
}

setInterval(checkForNewMessages, 500); // Set to 5000ms (5 seconds) for better performance

// Call the function immediately to check for messages on page load
checkForNewMessages();
