<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Start output buffering
ob_start();

// Assuming session_start() is required if you're using $_SESSION
session_start();
$role=$_SESSION['role'];


// Set the content type to application/json
header('Content-Type: application/json');

$response = ['newMessages' => false]; // Default response

if (isset($_SESSION['user_id'])) {
    $id_1 = $_SESSION['user_id'];

    // Database connection
    include "../Pages/Chat/app/db.conn.php"; // Ensure this path is correct

    $sql = "SELECT COUNT(chat_id) AS newMessages FROM chats WHERE to_id=? AND opened=0";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$id_1]);
    $result = $stmt->fetch();

    // Check if there are new messages and update the response accordingly
    if ($result && $result['newMessages'] > 0) {
        $response = ['newMessages' => true];
    }
}

// Clean (erase) the output buffer and turn off output buffering
ob_end_clean();

// Now, output the JSON response
echo json_encode($response);

