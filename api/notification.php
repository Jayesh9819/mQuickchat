<?php 
if (session_status() !== PHP_SESSION_ACTIVE) session_start();
include "../api/msg.php";
include '../App/db/db_connect.php';
$userid=$_SESSION['userid'];
$sql = "SELECT chats.*, user.name AS from_name 
FROM chats 
JOIN user ON chats.from_id = user.id 
WHERE chats.opened = 0 
AND chats.from_id = $userid 
AND chats.created_at >= NOW();";
if ($result = $conn->query($sql)) {
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $notificationMessage = "You have a new message. From ".$row['from_name'];
            $url = "./Portal_Chats"; // Assuming there's a generic inbox URL
            $color = "medium"; 
            echo sendFCMNotification($row['to_id'],$row['from_name'],$row['message']);
            // sendSSEData($notificationMessage, $url, $color);
        }
    }
} else {
    error_log("SQL error: " . $conn->error);
}
