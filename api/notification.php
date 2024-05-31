<?php 
if (session_status() !== PHP_SESSION_ACTIVE) session_start();
include "../api/msg.php";
include '../App/db/db_connect.php';
$userid = $_SESSION['userid'];

// Chats notification
$sql = "SELECT chats.*, user.name AS from_name 
        FROM chats 
        JOIN user ON chats.from_id = user.id 
        WHERE chats.opened = 0 
        AND chats.notified = 0 
        ;";

if ($result = $conn->query($sql)) {
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $notificationMessage = "You have a new message. From " . $row['from_name'];
            $url = "./Portal_Chats"; // Assuming there's a generic inbox URL
            $color = "medium"; 
            echo sendFCMNotification($row['to_id'], $row['from_name'], $row['message']);

            // Update the notified status to 1
            $updateSql = "UPDATE chats SET notified = 1 WHERE chat_id = " . $row['chat_id'];
            if (!$conn->query($updateSql)) {
                error_log("SQL update error: " . $conn->error);
            }
        }
    }
} else {
    error_log("SQL error: " . $conn->error);
}
?>
