<?php

session_start();

function linkify($text)
{
    $urlPattern = '/\b(https?|ftp|file):\/\/[-A-Z0-9+&@#\/%?=~_|!:,.;]*[-A-Z0-9+&@#\/%=~_|]/i';
    $text = preg_replace($urlPattern, '<a class="rtext" href="$0" target="_blank">$0</a>', $text);
    return $text;
}

# Check if the user is logged in
if (isset($_SESSION['username'])) {
    if (
        isset($_POST['message']) &&
        isset($_POST['to_id'])
    ) {
        # Database connection file
        include '../db.conn.php';
        $message = $_POST['message'];
        $platform = $_POST['to_id'];
        $from_id = $_SESSION['user_id'];

        $uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $attachmentPath = null;
        if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] === UPLOAD_ERR_OK) {
            $fileName = time() . '-' . basename($_FILES['attachment']['name']);
            $targetFilePath = $uploadDir . $fileName;
            if (move_uploaded_file($_FILES['attachment']['tmp_name'], $targetFilePath)) {
                $attachmentPath = $fileName;
            } else {
                echo "Error uploading file.";
                exit;
            }
        }

        # Fetch all user_ids associated with the platform
        $sqlUsers = "SELECT * FROM user WHERE pagename = ? AND role='User'";
        $stmtUsers = $conn->prepare($sqlUsers);
        $stmtUsers->execute([$platform]);

        $users = $stmtUsers->fetchAll(PDO::FETCH_ASSOC);

        foreach ($users as $user) {
            $to_id = $user['id'];

            $sql = "INSERT INTO chats (from_id, to_id, message, attachment) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $res = $stmt->execute([$from_id, $to_id, $message, $attachmentPath ?? null]);
            if ($res) {
                $sql2 = "SELECT * FROM conversations
                   WHERE (user_1=? AND user_2=?)
                   OR    (user_2=? AND user_1=?)";
                $stmt2 = $conn->prepare($sql2);
                $stmt2->execute([$from_id, $to_id, $from_id, $to_id]);
    
                date_default_timezone_set($_SESSION['timezone']);
    
                $time = date("h:i:s a");
    
                if ($stmt2->rowCount() == 0) {
                    # insert them into conversations table 
                    $sql3 = "INSERT INTO 
                         conversations(user_1, user_2)
                         VALUES (?,?)";
                    $stmt3 = $conn->prepare($sql3);
                    $stmt3->execute([$from_id, $to_id]);
                }
            }
    
        }
            $sql2 = "SELECT * FROM bconversation
               WHERE (user_1=? AND user_2=?)
               OR    (user_2=? AND user_1=?)";
            $stmt2 = $conn->prepare($sql2);
            $stmt2->execute([$from_id, $platform, $from_id, $platform]);

			date_default_timezone_set(str_replace(' ', '', $_SESSION['timezone']));

            $time = date("h:i:s a");

            if ($stmt2->rowCount() == 0) {
                # insert them into conversations table 
                $sql3 = "INSERT INTO 
                     bconversation(user_1, user_2)
                     VALUES (?,?)";
                $stmt3 = $conn->prepare($sql3);
                $stmt3->execute([$from_id, $platform]);
            }
        


        # Log the bulk message operation
        $sqlBulkMessageLog = "INSERT INTO bmessages (from_id, pagename, message,attachment) VALUES (?, ?, ?,?)";
        $stmtBulkMessageLog = $conn->prepare($sqlBulkMessageLog);
        $resultBulkMessageLog = $stmtBulkMessageLog->execute([$from_id, $platform, $message, $attachmentPath ?? null]);

        echo '<p class="rtext align-self-end border rounded p-2 mb-1">';
        echo linkify($message);
        if ($attachmentPath) {
            echo '<img src="../uploads/' . htmlspecialchars($attachmentPath) . '" alt="Attachment" style="max-width:100%;display:block;">';
        }
        echo '<small class="d-block">' . date("h:i:s a") . '</small>';
        echo '</p>';
    }
} else {
    header("Location: ../../index.php");
    exit;
}
