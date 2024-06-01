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

// transaction notification 
if (empty($_SESSION['user_id'])) {
    error_log("Session variable 'user_id' not set");
    exit;
}

$userid = $_SESSION['user_id'];

// Fetch all pending transactions where notified = 0
$sql = "SELECT * FROM transaction WHERE notified = 0";
if ($result = $conn->query($sql)) {
    if ($result->num_rows > 0) {
        while ($transaction = $result->fetch_assoc()) {
            $username = $transaction['username'];

            // Fetch user details using the username
            $userSql = "SELECT id, role, branchname AS branch FROM user WHERE username = ?";
            $stmtUser = $conn->prepare($userSql);
            $stmtUser->bind_param("s", $username);
            $stmtUser->execute();
            $resultUser = $stmtUser->get_result();

            if ($user = $resultUser->fetch_assoc()) {
                $role = $user['role'];
                $branch = $user['branch'];
                $userId = $user['id'];

                // Custom condition based on the role
                if ($role === 'Agent') {
                    $pagelist=$user['pagename'];

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
                }

                $stmtTransaction = $conn->prepare($transactionSql);
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
                }
            } else {
                error_log("User not found for username: $username");
            }
        }
    }
} else {
    error_log("SQL error: " . $conn->error);
}

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
    }
} else {
    error_log("SQL error: " . $conn->error);
}

$userIDs = [];

// Fetch user, agent, and manager/supervisor IDs
$stmtUser = $conn->prepare("SELECT id FROM user WHERE username = ?");
$stmtAgent = $conn->prepare("SELECT id FROM user WHERE username = ?");
$stmtManSup = $conn->prepare("SELECT id FROM user WHERE branchname = ? AND (role = 'Manager' OR role = 'Supervisor')");

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
    }
} else {
    error_log("SQL error: " . $conn->error);
}

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
    }
} else {
    error_log("SQL error: " . $conn->error);
}

$conn->close();


