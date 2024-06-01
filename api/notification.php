<?php 
if (session_status() !== PHP_SESSION_ACTIVE) session_start();
include "../api/msg.php";
include '../App/db/db_connect.php';

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', 'error.log'); // Make sure this path is writable by the web server

$userid = $_SESSION['userid'] ?? null;

if (!$userid) {
    error_log("User ID not set in session.");
    exit;
}

// Chats notification
$sql = "SELECT chats.*, user.name AS from_name 
        FROM chats 
        JOIN user ON chats.from_id = user.id 
        WHERE chats.opened = 0 
        AND chats.notified = 0;";

if ($result = $conn->query($sql)) {
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $notificationMessage = "You have a new message from " . $row['from_name'];
            echo sendFCMNotification($row['to_id'], "New Message", $notificationMessage);

            // Update the notified status to 1
            $updateSql = "UPDATE chats SET notified = 1 WHERE chat_id = " . $row['chat_id'];
            if (!$conn->query($updateSql)) {
                error_log("SQL update error: " . $conn->error);
            }
        }
    } else {
        error_log("No new chat notifications found.");
    }
} else {
    error_log("SQL error in chat notification query: " . $conn->error);
}

// Transaction notification 
$sql = "SELECT * FROM transaction WHERE notified = 0";
if ($result = $conn->query($sql)) {
    if ($result->num_rows > 0) {
        while ($transaction = $result->fetch_assoc()) {
            $username = $transaction['username'];
            print_r("Processing transaction for username: $username");

            // Fetch user details using the username
            $userSql = "SELECT id, role, branchname AS branch, pagename FROM user WHERE username = ?";
            $stmtUser = $conn->prepare($userSql);
            if (!$stmtUser) {
                error_log("Prepare statement error: " . $conn->error);
                continue;
            }
            $stmtUser->bind_param("s", $username);
            $stmtUser->execute();
            $resultUser = $stmtUser->get_result();

            if ($user = $resultUser->fetch_assoc()) {
                $role = $user['role'];
                $branch = $user['branch'];
                $userId = $user['id'];
                $pagelist = $user['pagename'] ?? '';
                error_log("Fetched user details for username: $username, role: $role");

                // Custom condition based on the role
                if ($role === 'Agent') {
                    if (!empty($pagelist)) {
                        $pagesArray = explode(", ", $pagelist);
                        $quotedPages = array_map(function ($page) use ($conn) {
                            return "'" . mysqli_real_escape_string($conn, $page) . "'";
                        }, $pagesArray);

                        $whereClause = "AND page IN (" . implode(", ", $quotedPages) . ")";
                    } else {
                        $whereClause = '';
                    }

                    $transactionSql = "SELECT * FROM transaction 
                                       WHERE Redeem != 0 
                                       AND Redeem IS NOT NULL $whereClause 
                                       AND approval_status = 0 
                                       AND id = ?";
                } elseif ($role === 'Manager' || $role === 'Supervisor') {
                    $transactionSql = "SELECT * FROM transaction 
                                       WHERE Redeem != 0 
                                       AND Redeem IS NOT NULL 
                                       AND (redeem_status = 0 OR cashout_status = 0) 
                                       AND branch = '$branch' 
                                       AND approval_status = 1 
                                       AND id = ?";
                } elseif ($role === 'Admin') {
                    $transactionSql = "SELECT * FROM transaction 
                                       WHERE Redeem != 0 
                                       AND Redeem IS NOT NULL 
                                       AND (redeem_status = 0 OR cashout_status = 0) 
                                       AND id = ?";
                } else {
                    error_log("Unhandled role: $role for username: $username");
                    continue;
                }

                $stmtTransaction = $conn->prepare($transactionSql);
                if (!$stmtTransaction) {
                    error_log("Prepare statement error: " . $conn->error);
                    continue;
                }
                $stmtTransaction->bind_param("i", $transaction['id']);
                $stmtTransaction->execute();
                $resultTransaction = $stmtTransaction->get_result();

                if ($resultTransaction->num_rows > 0) {
                    $row = $resultTransaction->fetch_assoc();
                    $notificationMessage = "You have a new redeem request from {$row['username']} for amount {$row['redeem']}";
                    echo sendFCMNotification($userId, "Redeem Request", $notificationMessage);

                    // Update the notified status to 1
                    $updateSql = "UPDATE transaction SET notified = 1 WHERE id = " . $row['id'];
                    if (!$conn->query($updateSql)) {
                        error_log("SQL update error: " . $conn->error);
                    }
                } else {
                    error_log("No transaction found matching the criteria for transaction ID: {$transaction['id']}");
                }
            } else {
                error_log("User not found for username: $username");
            }
        }
    } else {
        error_log("No new transactions to notify.");
    }
} else {
    error_log("SQL error in transaction notification query: " . $conn->error);
}

// Additional chat notifications for current user
$sql = "SELECT chats.*, user.name AS from_name, user.id AS to_id 
        FROM chats 
        JOIN user ON chats.from_id = user.id 
        WHERE chats.opened = 0 
        AND chats.to_id = $userid 
        AND chats.notified = 0 
        AND chats.created_at >= NOW() - INTERVAL 2 SECOND";
if ($result = $conn->query($sql)) {
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $notificationMessage = "You have a new message from " . $row['from_name'];
            echo sendFCMNotification($row['to_id'], "New Message", $notificationMessage);

            // Update the notified status to 1
            $updateSql = "UPDATE chats SET notified = 1 WHERE id = " . $row['id'];
            if (!$conn->query($updateSql)) {
                error_log("SQL update error: " . $conn->error);
            }
        }
    } else {
        error_log("No new chat notifications found for user ID: $userid.");
    }
} else {
    error_log("SQL error in additional chat notification query: " . $conn->error);
}

// Notification for successfully done transactions
$sql = "SELECT * FROM transaction WHERE approval_status = 1 
        AND cashout_status = 1 AND redeem_status = 1 
        AND branch = '$branch' 
        AND transaction.notified = 0 
        AND updated_at >= NOW() - INTERVAL 2 SECOND";
if ($result = $conn->query($sql)) {
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $notificationMessage = "Transaction successfully done by {$row['username']} for amount {$row['redeem']}.";
            $approvedBy = $row['approved_by'];
            $user = $row['username'];

            // User who requested
            $stmtUser->bind_param("s", $user);
            $stmtUser->execute();
            $resultUser = $stmtUser->get_result();
            if ($userRow = $resultUser->fetch_assoc()) {
                $userIDs[] = $userRow['id'];
            }

            // Agent who approved
            $stmtAgent->bind_param("s", $approvedBy);
            $stmtAgent->execute();
            $resultAgent = $stmtAgent->get_result();
            if ($agentRow = $resultAgent->fetch_assoc()) {
                $userIDs[] = $agentRow['id'];
            }

            // Managers and Supervisors
            $stmtManSup->bind_param("s", $branch);
            $stmtManSup->execute();
            $resultManSup = $stmtManSup->get_result();
            while ($manSupRow = $resultManSup->fetch_assoc()) {
                $userIDs[] = $manSupRow['id'];
            }

            foreach ($userIDs as $id) {
                $insertStmt = $conn->prepare("INSERT INTO notification (content, by_id, for_id, created_at) VALUES (?, ?, ?, NOW())");
                $insertStmt->bind_param("sii", $notificationMessage, $userid, $id);
                $insertStmt->execute();
                $insertStmt->close();
                echo sendFCMNotification($id, "Transaction Notification", $notificationMessage);
            }

            // Update the notified status to 1
            $updateSql = "UPDATE transaction SET notified = 1 WHERE id = " . $row['id'];
            if (!$conn->query($updateSql)) {
                error_log("SQL update error: " . $conn->error);
            }
        }
    } else {
        error_log("No transactions found for successful notification.");
    }
} else {
    error_log("SQL error in successful transaction notification query: " . $conn->error);
}

// Notification for failed transactions
$sql = "SELECT * FROM transaction WHERE approval_status = 2 
        AND cashout_status = 0 AND redeem_status = 0 
        AND branch = '$branch' 
        AND transaction.notified = 0 
        AND updated_at >= NOW()";
if ($result = $conn->query($sql)) {
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $notificationMessage = "Redeem not done successfully for amount {$row['redeem']} because of {$row['Reject_msg']}";
            $approvedBy = $row['approved_by'];
            $user = $row['username'];

            // User who requested
            $stmtUser->bind_param("s", $user);
            $stmtUser->execute();
            $resultUser = $stmtUser->get_result();
            if ($userRow = $resultUser->fetch_assoc()) {
                $userIDs[] = $userRow['id'];
            }

            // Agent who approved
            $stmtAgent->bind_param("s", $approvedBy);
            $stmtAgent->execute();
            $resultAgent = $stmtAgent->get_result();
            if ($agentRow = $resultAgent->fetch_assoc()) {
                $userIDs[] = $agentRow['id'];
            }

            // Managers and Supervisors
            $stmtManSup->bind_param("s", $branch);
            $stmtManSup->execute();
            $resultManSup = $stmtManSup->get_result();
            while ($manSupRow = $resultManSup->fetch_assoc()) {
                $userIDs[] = $manSupRow['id'];
            }

            foreach ($userIDs as $id) {
                $insertStmt = $conn->prepare("INSERT INTO notification (content, by_id, for_id, created_at) VALUES (?, ?, ?, NOW())");
                $insertStmt->bind_param("sii", $notificationMessage, $userid, $id);
                $insertStmt->execute();
                $insertStmt->close();
                echo sendFCMNotification($id, "Redeem Not Done", $notificationMessage);
            }

            // Update the notified status to 1
            $updateSql = "UPDATE transaction SET notified = 1 WHERE id = " . $row['id'];
            if (!$conn->query($updateSql)) {
                error_log("SQL update error: " . $conn->error);
            }
        }
    } else {
        error_log("No failed transactions found for notification.");
    }
} else {
    error_log("SQL error in failed transaction notification query: " . $conn->error);
}

$conn->close();
?>
