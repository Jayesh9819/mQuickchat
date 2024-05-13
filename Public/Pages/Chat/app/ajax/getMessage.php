<?php

session_start();
require '../db.conn.php'; // Ensures the database connection is properly included
include '../helpers/chat.php'; // Includes helper functions like getMessageById

function linkify($text)
{
	$urlPattern = '/\b(https?|ftp|file):\/\/[-A-Z0-9+&@#\/%?=~_|!:,.;]*[-A-Z0-9+&@#\/%=~_|]/i';
	return preg_replace($urlPattern, '<a class="rtext" href="$0" target="_blank">$0</a>', $text);
}

if (!isset($_SESSION['username'])) {
	echo json_encode(['status' => 'error', 'message' => 'User not logged in']);
	exit;
}

if (isset($_POST['id_2'])) {
	$id_1 = $_SESSION['user_id'];
	$id_2 = $_POST['id_2'];

	$sql = "SELECT * FROM chats WHERE to_id=? AND from_id=? ORDER BY chat_id ASC";
	$stmt = $conn->prepare($sql);
	$stmt->execute([$id_1, $id_2]);

	$messageHtml = ''; // Initialize an empty string to build HTML

	if ($stmt->rowCount() > 0) {
		$chats = $stmt->fetchAll();

		foreach ($chats as $chat) {
			if ($chat['opened'] == 0) {
				$opened = 1;
				$chat_id = $chat['chat_id'];
				$sql2 = "UPDATE chats SET opened = ? WHERE chat_id = ?";
				$stmt2 = $conn->prepare($sql2);
				$stmt2->execute([$opened, $chat_id]);

				// $attachmentHTML = '';
				// if (!empty($chat['attachment'])) {
				//     $imageUrl = "../uploads/" . $chat['attachment'];
				//     $attachmentHTML = "<img src='{$imageUrl}' alt='Attachment' style='max-width: 200px; display: block;'>";
				// }

				// $replyButton = '';
				// if (!empty($chat['reply_id'])) {
				// }

				// // Build message block
				// $responseHTML .= "<p class='ltext border rounded p-2 mb-1'>";
				// $responseHTML .= linkify($chat['message']);
				// $responseHTML .= $attachmentHTML;
				// $responseHTML .= "<small class='d-block'>" . $chat['created_at'] . "</small>";
				// $responseHTML .= $replyButton;
				// $responseHTML .= "</p>";
				// $responseHTML .= '<button onclick="setReplyTo(' . $chat_id . ', \'' . addslashes(htmlspecialchars($chat['message'])) . '\')">Reply</button>';
				$messageHtml = '<div class="message received" id="msg_' . $chat['chat_id'] . '" style="text-align: left; padding-right: 21px;">';
				$messageHtml .= '<button onclick="setReplyTo(' . $chat['chat_id'] . ', \'' . addslashes(htmlspecialchars($chat['message'])) . '\')">Reply</button>';
				$messageHtml .= '<div class="message-box" style="display: inline-block; background-color: #e9e9eb; padding: 10px; border-radius: 10px; margin: 5px;">';
				if ($chat['reply_id']) {
					$repliedMessage = getMessageById($chat['reply_id'], $conn); // Fetching the replied message
					$messageHtml .= '<div class="replied-message" onclick="activateOriginalMessage(\'msg_' . $repliedMessage['chat_id'] . '\');"><em>Replied to: ' . htmlspecialchars($repliedMessage['message']) . '</em></div>';
				}
				if ($chat['attachment']) {
					$messageHtml .= '<img src="../uploads/' . htmlspecialchars($chat['attachment']) . '" alt="Attachment" style="max-width:100%;display:block;">';
				}
				$messageHtml .= '<p style="margin: 0;">' . linkify($chat['message']);
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
			}
		}
	}

	// Echo the complete HTML and success status as a JSON object
	echo json_encode(['status' => 'success', 'message' => 'Messages fetched successfully', 'html' => $messageHtml]);
} else {
	echo json_encode(['status' => 'error', 'message' => 'Required parameters are missing']);
	exit;
}
