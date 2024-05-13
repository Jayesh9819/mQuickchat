<?php

session_start();
require '../db.conn.php'; // Ensures the database connection file is included
include '../helpers/chat.php';
function linkify($text)
{
	$urlPattern = '/\b(https?|ftp|file):\/\/[-A-Z0-9+&@#\/%?=~_|!:,.;]*[-A-Z0-9+&@#\/%=~_|]/i';
	return preg_replace($urlPattern, '<a href="$0" target="_blank">$0</a>', $text);
}

if (!isset($_SESSION['username'])) {
	echo json_encode(['status' => 'error', 'message' => 'User not logged in']);
	exit;
}

if (isset($_POST['message'], $_POST['to_id'])) {
	$message = htmlspecialchars($_POST['message']);
	$to_id = $_POST['to_id'];
	$reply_id = $_POST['reply_to_id'] ?? null;

	$from_id = $_SESSION['user_id'];
	$uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/uploads/';

	if (!is_dir($uploadDir) && !mkdir($uploadDir, 0777, true)) {
		echo json_encode(['status' => 'error', 'message' => 'Failed to create upload directory']);
		exit;
	}

	$attachmentPath = null;
	if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] === UPLOAD_ERR_OK) {
		$fileName = time() . '-' . basename($_FILES['attachment']['name']);
		$targetFilePath = $uploadDir . $fileName;
		if (!move_uploaded_file($_FILES['attachment']['tmp_name'], $targetFilePath)) {
			echo json_encode(['status' => 'error', 'message' => 'Error uploading file']);
			exit;
		}
		$attachmentPath = $fileName;
	}

	$sql = "INSERT INTO chats (from_id, to_id, message, attachment, reply_id) VALUES (?, ?, ?, ?, ?)";
	$stmt = $conn->prepare($sql);
	if (!$stmt->execute([$from_id, $to_id, $message, $attachmentPath, $reply_id])) {
		echo json_encode(['status' => 'error', 'message' => 'Failed to insert message']);
		exit;
	}

	$chat_id = $conn->lastInsertId(); // Assuming PDO, getting the last inserted chat ID

	$formattedMessage = [
		'html' => linkify($message),
		'attachment' => $attachmentPath ? "<img src='../uploads/" . htmlspecialchars($attachmentPath) . "' alt='Attachment' style='max-width:100%;display:block;'>" : "",
		'timestamp' => date("h:i:s a")
	];

	// Generate the HTML content for the chat message
	$messageHtml = '<div class="message sent" id="msg_' . $chat_id . '" style="text-align: right; padding-right: 21px;">';
	// $messageHtml .= '<button onclick="setReplyTo(' . $chat_id . ', \'' . addslashes(htmlspecialchars($message)) . '\')">Reply</button>';
	$messageHtml .= '<button style="background: none; border: none; cursor: pointer; position:relative; bottom:10px;" onclick="setReplyTo(' . $chat_id . ', \'' . addslashes(htmlspecialchars($message)) . '\')"><img src="../uploads/reply.png" style="width:50px;" alt="Reply"></button>';


	$messageHtml .= '<div class="message-box" style="display: inline-block; background-color: #dcf8c6; padding: 10px; border-radius: 10px; margin: 5px;">';
	if ($reply_id) {
		$repliedMessage = getMessageById($reply_id, $conn); // Fetching the replied message
		$messageHtml .= '<div class="replied-message" onclick="activateOriginalMessage(\'msg_' . $repliedMessage['chat_id'] . '\');"><em>Replied to: ' . htmlspecialchars($repliedMessage['message']) . '</em></div>';
	}
	if ($attachmentPath) {
		$messageHtml .= '<img src="../uploads/' . htmlspecialchars($attachmentPath) . '" alt="Attachment" style="max-width:100%;display:block;">';
	}

	$messageHtml .= '<p style="margin: 0;">' . linkify($message);
	$messageHtml .= '</p>';
	if (isset($_SESSION['timezone'])) {
		$timezone = new DateTimeZone($_SESSION['timezone']);
	} else {
		$timezone = new DateTimeZone('UTC');
	}
	$currentTime = new DateTime('now', new DateTimeZone('UTC'));
	$currentTime->setTimezone($timezone); // Convert to user's timezone
	$formattedTime = $currentTime->format('h:i:s a');
	$messageHtml .= '<small style="display: block; color: #666; font-size: smaller;">' . $formattedTime . '</small>';
	$messageHtml .= '</div>';
	$messageHtml .= '</div>';

	// Return JSON including the generated HTML
	echo json_encode(['status' => 'success', 'message' => 'Message sent successfully', 'html' => $messageHtml, 'data' => $formattedMessage]);
} else {
	echo json_encode(['status' => 'error', 'message' => 'Required fields are missing']);
	exit;
}
