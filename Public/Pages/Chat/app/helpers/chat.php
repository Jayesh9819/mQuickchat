<?php

function getChats($id_1, $id_2, $conn)
{
    // Get the role of the second user
    $data = getUserDataByUsername($id_2, $conn);
    $role = $data['role'];
    $data2 = getUserDataByUsername($id_1, $conn);
    // Check if 'role' is set in the array and is not empty, otherwise set to 'temp'
    $roleu = !empty($data2['role']) ? $data2['role'] : 'temp';

    $r = ['Agent', 'Supervisor', 'Manager', 'Admin'];
    // Define the initial SQL query and parameters based on the user role
    if ($role == 'User'  && $roleu != 'User') {
        $sql = "SELECT chats.*, 
                sender.username AS sender_username, 
                receiver.username AS receiver_username
                FROM chats
                LEFT JOIN user AS sender ON chats.from_id = sender.id
                LEFT JOIN user AS receiver ON chats.to_id = receiver.id
                WHERE (chats.from_id = ? OR chats.to_id = ?)
                ORDER BY chats.chat_id ASC";
        $params = [$id_2, $id_2];
    } elseif ($roleu == 'query') {
        $sql = "SELECT chats.*, 
        sender.username AS sender_username, 
        receiver.username AS receiver_username
        FROM chats
        LEFT JOIN unknown_users AS sender ON chats.from_id = sender.id
        LEFT JOIN user AS receiver ON chats.to_id = receiver.id
        WHERE (chats.from_id = ? OR chats.to_id = ?)
        ORDER BY chats.chat_id ASC";
        $params = [$id_1, $id_1];
    } elseif ($role == 'query') {
        $sql = "SELECT chats.*, 
        sender.username AS sender_username, 
        receiver.username AS receiver_username
        FROM chats
        LEFT JOIN unknown_users AS sender ON chats.from_id = sender.id
        LEFT JOIN user AS receiver ON chats.to_id = receiver.id
        WHERE (chats.from_id = ? OR chats.to_id = ?)
        ORDER BY chats.chat_id ASC";
        $params = [$id_2, $id_2];
    } elseif (in_array($role, $r) && in_array($roleu, $r)) {
        $sql = "SELECT chats.*, 
        sender.username AS sender_username, 
        receiver.username AS receiver_username
        FROM chats
        LEFT JOIN user AS sender ON chats.from_id = sender.id
        LEFT JOIN user AS receiver ON chats.to_id = receiver.id
        WHERE (chats.from_id = ? AND chats.to_id = ?) OR (chats.from_id = ? AND chats.to_id = ?)
        ORDER BY chats.chat_id ASC";
        $params = [$id_1, $id_2, $id_2, $id_1];
    } else {
        $sql = "SELECT chats.*, 
        sender.username AS sender_username, 
        receiver.username AS receiver_username
        FROM chats
        LEFT JOIN user AS sender ON chats.from_id = sender.id
        LEFT JOIN user AS receiver ON chats.to_id = receiver.id
        WHERE (chats.from_id = ? OR chats.to_id = ?)
        ORDER BY chats.chat_id ASC";
        $params = [$id_1, $id_1];
    }
    $stmt = $conn->prepare($sql);
    $stmt->execute($params);

    // Fetch all chats if available
    if ($stmt->rowCount() > 0) {
        $chats = $stmt->fetchAll(PDO::FETCH_ASSOC);
        // If role is 'User', append participants to the chats array
        return $chats;
    } else {
        // Return an empty array if no chats found
        return [];
    }
}
function getChatPage($id_1, $id_2, $conn)
{

    $sql = "SELECT * FROM bmessages
            WHERE (from_id=? AND pagename=?)
            OR    (pagename=? AND from_id=?)
            ORDER BY id DESC";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$id_1, $id_2, $id_1, $id_2]);

    if ($stmt->rowCount() > 0) {
        $chats = $stmt->fetchAll();
        return $chats;
    } else {
        $chats = [];
        return $chats;
    }
}
function getUserDataByUsername($username, $conn)
{
    // Sanitize input to prevent SQL injection
    $username = ($username);

    // SQL query to retrieve user data by username
    // $query = "SELECT * FROM user WHERE id = ?"; // Corrected the query with a placeholder
    if (substr($username, 0, 2) === 'UT') {
        $query = "SELECT * FROM unknown_users WHERE id = ?";
    } else {
        $query = "SELECT * FROM user WHERE id = ?";
    }

    // Execute the query with bound parameter
    $stmt = $conn->prepare($query);
    $stmt->execute([$username]);

    // Fetch the user data
    $userData = $stmt->fetch(PDO::FETCH_ASSOC);

    // Return the user data
    return $userData;
}
/**
 * Fetch a message by its ID.
 *
 * @param int $messageId The ID of the message to fetch.
 * @param PDO $conn Database connection object.
 * @return array|null Returns the message data as an associative array or null if not found.
 */
function getMessageById($messageId, $conn)
{
    try {
        $stmt = $conn->prepare("SELECT * FROM chats WHERE chat_id = ?");
        $stmt->execute([$messageId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);  // Fetch the message as an associative array.
    } catch (PDOException $e) {
        error_log("Error fetching message by ID: " . $e->getMessage());
        return null;  // Return null in case of an error.
    }
}
