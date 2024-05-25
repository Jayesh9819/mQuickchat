<?php
// Include database configuration file
include_once '../App/db/db_connect.php';

// Get user_id and token from POST data
$userId = $_POST['user_id'];
$token = $_POST['token'];

// Check if the user ID exists in the table
$sql = "SELECT user_id FROM user_tokens WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userId);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    // If user ID exists, update the token
    $updateSql = "UPDATE user_tokens SET fcm_token = ? WHERE user_id = ?";
    $updateStmt = $conn->prepare($updateSql);
    $updateStmt->bind_param("si", $token, $userId);
    $updateStmt->execute();
    $updateStmt->close();
    echo "Token updated successfully.";
} else {
    // If user ID doesn't exist, insert a new record
    $insertSql = "INSERT INTO user_tokens (user_id, fcm_token) VALUES (?, ?)";
    $insertStmt = $conn->prepare($insertSql);
    $insertStmt->bind_param("is", $userId, $token);
    $insertStmt->execute();
    $insertStmt->close();
    echo "Token stored successfully.";
}

$stmt->close();
$conn->close();
?>
