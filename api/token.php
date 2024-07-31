<?php
// Include database configuration file
include_once '../App/db/db_connect.php';
print_r($_GET);
// Get the user_id and fcm_token from the query parameters
$userId = isset($_GET['user_id']) ? $_GET['user_id'] : null;
$token = isset($_GET['fcm_token']) ? $_GET['fcm_token'] : null;

// Check if the required parameters are provided
if ($userId && $token) {
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
} else {
    echo "Required parameters (user_id, fcm_token) are missing.";
}

$conn->close();
?>
