<?php  
session_start();

# Function to return JSON response to AJAX
function jsonResponse($status, $message, $data = []) {
    echo json_encode([
        'status' => $status,
        'message' => $message,
        'data' => $data
    ]);
    exit;
}

# Check if the user is logged in
if (isset($_SESSION['username'])) {
    
    # Database connection file
    include 'db.conn.php';  // Ensure this path is correctly specified

    # Get the logged in user's username from SESSION
    $username = $_SESSION['username'];
    $sql = "UPDATE user SET last_seen = NOW() WHERE username = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt === false) {
        jsonResponse('error', 'Failed to prepare SQL statement.');
    } else {
        # Execute the prepared statement with parameter binding
        if ($stmt->execute([$username])) {
            jsonResponse('success', 'User last seen updated successfully.');
        } else {
            jsonResponse('error', 'Failed to execute update.', ['errorInfo' => $stmt->errorInfo()]);
        }
    }

} else {
    jsonResponse('error', 'User not logged in.');
}
