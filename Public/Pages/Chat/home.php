<!doctype html>
<html lang="en" dir="ltr">

<head>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

	<?php
	ob_start();

	include("./Public/Pages/Common/header.php");
	include "./Public/Pages/Common/auth_user.php";

	// Function to echo the script for toastr
	function echoToastScript($type, $message)
	{
		echo "<script type='text/javascript'>document.addEventListener('DOMContentLoaded', function() { toastr['$type']('$message'); });</script>";
	}


	if (isset($_SESSION['toast'])) {
		$toast = $_SESSION['toast'];
		echoToastScript($toast['type'], $toast['message']);
		unset($_SESSION['toast']); // Clear the toast message from session
	}

	if (session_status() !== PHP_SESSION_ACTIVE) session_start();

	// Display error message if available
	if (isset($_SESSION['login_error'])) {
		echo '<p class="error">' . $_SESSION['login_error'] . '</p>';
		unset($_SESSION['login_error']); // Clear the error message
	}
	if (isset($_SESSION['username'])) {
		# database connection file
		include 'app/db.conn.php';

		include 'app/helpers/user.php';
		include 'app/helpers/chat.php';
		include 'app/helpers/opened.php';

		include 'app/helpers/timeAgo.php';

		if (!isset($_GET['user'])) {
			header("Location: ./Chat_l");
			exit;
		}

		# Getting User data data
		$chatWith = getUser($_GET['user'], $conn);
		if (empty($chatWith)) {
			header("Location: ./Chat_l");
			exit;
		}

		$chats = getChats($_SESSION['user_id'], $chatWith['id'], $conn);
		opened($chatWith['id'], $conn, $chats);
	}
	?>

	<style>
		.chat-header {
			background-color: #8c44d9;
		}

		.vh-100 {
			min-height: 100vh;
		}

		.w-400 {
			width: 800px;
		}

		.w-300 {
			width: 800px;
		}

		.fs-xs {
			font-size: 1rem;
		}

		.w-10 {
			width: 10%;
		}


		.fs-big {
			font-size: 5rem !important;
		}

		.online {
			width: 10px;
			height: 10px;
			background: green;
			border-radius: 50%;
		}

		.w-15 {
			width: 10%;
		}

		.fs-sm {
			font-size: 2rem;
		}

		.display-4 {
			font-size: 1.5rem !important;
		}

		small {
			color: #444;
			font-size: 0.5rem;
			text-align: right;
		}

		.chat-box {
			overflow-y: auto;
			overflow-x: hidden;

		}

		.rtext {
			width: 65%;
			background: #f8f9fa;
			color: #444;
		}

		.ltext {
			width: 65%;
			background: #3289c8;
			color: #fff;
		}

		/* width */
		*::-webkit-scrollbar {
			width: 3px;
		}

		/* Track */
		*::-webkit-scrollbar-track {
			background: #f1f1f1;
		}

		/* Handle */
		*::-webkit-scrollbar-thumb {
			background: #aaa;
		}

		/* Handle on hover */
		*::-webkit-scrollbar-thumb:hover {
			background: #3289c8;
		}

		textarea {
			resize: none;
		}

		/*message_status*/
		/* Custom CSS styles */
		.chat-box {
			background-image: url("../uploads/chat-5.avif");
			height: 87%;
			width: 100%;

			/* Limit the height of the chat box */
			overflow-y: auto;
			/* Enable vertical scrolling */
		}

		.chat-box p {
			margin: 5px 0;
			/* Add spacing between chat messages */
		}

		.chat-input-group {
			position: relative;
			/* Set position to relative for proper alignment */
		}

		#message {
			border-radius: 20px;
			/* Adjust border radius for message input */
			resize: none;
			/* Disable resizing of textarea */
		}

		#sendBtn {
			position: absolute;
			/* Position the send button */
			right: 10px;
			bottom: 10px;
		}

		.ltext {
			word-break: break-all;
			font-family: serif;
			background-color: white;
			color: black;
			max-width: 50%;
			font-size: larger;
		}


		.rtext {
			font-family: serif;
			word-break: break-all;
			background-color: #bbb;
			color: #444;
			max-width: 50%;
			font-size: larger;


		}

		.active-message {
			background-color: #059e59;
			/* Example: a yellow background for active message */
			border-color: #ffecb3;
		}



		.emoji-picker {
			position: relative;
			top: -265px;
			left: 20px;
			border: 1px solid #ddd;
			padding: 5px;
			background-color: white;
			width: 300px;
			/* Adjust width based on your design needs, might reduce for mobile */
			display: grid;
			grid-template-columns: repeat(8, 1fr);
			gap: 5px;
			overflow-y: auto;
			max-height: 200px;
			/* Adjusted for a reasonable height */
			z-index: 1000;
			/* Ensure it sits on top of other elements */
			border-radius: 20px;
		}

		.emoji-picker button {
			font-size: 2rem;
			cursor: pointer;
			background: none;
			border: none;
			padding: 5px;
		}

		.pageNameheader {
			position: relative;
			right: 16px;
		}

		.redeemChatButton {
			position: absolute;
			right: 40px;
			top: 50px;
			display: none;
		}

		/* Base styles */
		.chat-box {
			overflow-y: auto;
		}

		@media (min-width: 768px) {
			.redeemChatButton {
				display: block;
			}
		}

		/* Medium devices (tablets, 768px and up) */
		@media (max-width: 768px) {
			.w-400 {
				width: 100%;
				/* Full width */
			}


			.fs-sm,
			.display-4 {
				font-size: 1rem;
				/* Adjust font size */
			}

			.emoji-picker {
				grid-template-columns: repeat(4, 1fr);
			}

			.shprofile {
				display: none;
			}

			.replyButtonsss {
				position: relative;
				top: -10px;
			}

		}

		/* Small devices (phones, 600px and down) */
		@media (max-width: 600px) {

			.fs-big,
			.fs-xs,
			.fs-sm,
			.display-4 {
				font-size: 0.8rem;
				/* Adjust font size */
			}

			.emoji-picker {
				width: 100%;
				/* Full width */
				grid-template-columns: repeat(4, 1fr);
				/* Less columns */
			}

		}
	</style>



</head>

<body class="  ">

	<?php
	include("./Public/Pages/Common/sidebar.php");

	?>

	<main class="main-content">
		<?php
		// include("./Public/Pages/Common/main_content.php");
		?>
		<div class="content-inner container-fluid pb-0" id="page_layout">
			<div class=" rounded" style="height:92vh;width:100%;">

				<div class="chat-header" style=" width: 100%; padding: 10px; display: flex; align-items: center;">

					<a href="./Chat_l" class="" style="color: white; margin-right: 10px; font-size: 30px; text-decoration: none;">←</a>

					<img src="../uploads/profile/<?= !empty($chatWith['p_p']) ? $chatWith['p_p'] : '07.png' ?>" class="rounded-circle" style="width: 50px; height: 50px; margin-right: 10px;">

					<div style="flex-grow: 1;">
						<h1 style="margin-bottom: 0; font-size: 16px; color: white; font-weight: bold;">
							<?= $chatWith['username'] ?>
						</h1>
						<?php
						if ($chatWith['role'] == 'User') {
							echo '<h1 class="" style="margin-bottom: 0; font-size: 16px; color: white; font-weight: bold;">
            				Page Name:- ' . $chatWith['pagename'] . '
       								 </h1>';
						} elseif ($chatWith['role'] == 'Agent' && $_SESSION['role'] != 'User') {
							echo '<h1 class="" style="margin-bottom: 0; font-size: 16px; color: white; font-weight: bold;">
						Page Name:- ' . $chatWith['pagename'] . '
									</h1>';
						} elseif ($chatWith['role'] == 'Manager' || $chatWith['role'] == 'Supervisor') {
							echo '<h1 class="" style="margin-bottom: 0; font-size: 16px; color: white; font-weight: bold;">
						Branch Name:- ' . $chatWith['branchname'] . '
									</h1>';
						}
						?>

						<div title="online">
							<?php if (last_seen($chatWith['last_seen']) == "Active") { ?>
								<div style="width: 10px; height: 10px; background-color: lime; border-radius: 50%; margin-right: 5px;"></div>
								<p style="color: white;">Online</p>
							<?php } else { ?>
								<p style="color: white;">
									Last seen: <?= last_seen($chatWith['last_seen']) ?>
								</p>
							<?php } ?>
						</div>
					</div>
					<?php
					if ($_SESSION['role'] == 'User') {
						echo '<a name="" id="" class="btn btn-secondary shprofile" href="./Redeem_Request" role="button">Redeem </a>';
					}

					if ($chatWith['role'] == 'User') {
						echo '<a name="" id="" class="btn btn-primary shprofile" href="./Show_Profile?u=' . $chatWith['id'] . '" role="button">Show Profile</a>';
						echo '				<a name="" id="" class="btn btn-primary shprofile" href="./cash_out?u=' . $chatWith['username'] . '" role="button">Redeem </a>
					<a name="" id="" class="btn btn-danger shprofile" href="./deposit?u=' . $chatWith['username'] . '" role="button">Recharge </a>';
					}
					?>


				</div>
				<?php function linkify($text)
				{
					$urlPattern = '/\b(https?|ftp|file):\/\/[-A-Z0-9+&@#\/%?=~_|!:,.;]*[-A-Z0-9+&@#\/%=~_|]/i';
					$text = preg_replace($urlPattern, '<a class="rtext" href="$0" target="_blank">$0</a>', $text);
					return $text;
				}
				?>


				<div class="shadow p-4  d-flex flex-column  chat-box" id="chatBox">


					<div class="chat-messages" style="padding: 20px;">

						<?php if (!empty($chats)) : foreach ($chats as $chat) : ?>

								<div class="message <?= ($chat['from_id'] == $_SESSION['user_id']) ? 'sent' : 'received' ?>" id="msg_<?= $chat['chat_id'] ?>" style="text-align: <?= ($chat['from_id'] == $_SESSION['user_id']) ? 'right' : 'left'; ?>">


									<button class="replyButtonsss" style="background: none; border: none; cursor: pointer; position:relative; bottom:30px;" onclick="setReplyTo(<?= $chat['chat_id'] ?>, '<?= addslashes(htmlspecialchars($chat['message'])) ?>')">
										<img src="../uploads/reply.png" style="width:50px;" alt="Reply" style="display: block;">
									</button>


									<div class="message-box <?= !empty($chat['reply_id']) ? 'replied-message-box' : '' ?>" style="background-color: <?= ($chat['from_id'] == $_SESSION['user_id']) ? '#dcf8c6' : '#e9e9eb'; ?>; padding: 10px; display: inline-block; border-radius: 10px; margin: 5px;">
										<?php if (isset($chat['sender_username']) && !empty($chat['sender_username'])) : ?>
											<h3 style="display: block; color: #666; font-size: smaller;"><?= htmlspecialchars($chat['sender_username']) ?></h3>
										<?php endif; ?>

										<?php if (!empty($chat['reply_id'])) : ?>
											<?php $repliedMessage = getMessageById($chat['reply_id'], $conn); ?>
											<div class="replied-message" onclick="activateOriginalMessage('msg_<?= $repliedMessage['chat_id'] ?>');">
												<em>Replied to: <?= htmlspecialchars($repliedMessage['message']) ?></em>
											</div>

										<?php endif; ?>
										<?php
										$attachmentHTML = '';
										if (!empty($chat['attachment'])) {
											$file = "../uploads/" . $chat['attachment'];
											$fileInfo = pathinfo($file);
											$fileExtension = strtolower($fileInfo['extension']);

											switch ($fileExtension) {
												case 'jpg':
												case 'jpeg':
												case 'png':
												case 'gif':
													$attachmentHTML = "<div><a href='{$file}' target='_blank'><img src='{$file}' alt='Image' style='max-width: 100%; max-height: 200px; display: block;'></a></div>";
													$attachmentHTML .= "<a href='{$file}' download class='btn btn-link' style='text-decoration: none; color: white; background-color: #4CAF50; padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: bold; transition: background-color 0.3s;'>Download</a>";
													$attachmentHTML .= "<style>.btn:hover { background-color: #45a049; }</style>";
													break;
												case 'mp4':
													$attachmentHTML = "<div><video controls style='max-width: 100%; max-height: 200px;'><source src='{$file}' type='video/mp4'>Your browser does not support the video tag.</video></div>";
													$attachmentHTML .= "<a href='{$file}' download class='btn btn-link' style='text-decoration: none; color: white; background-color: #4CAF50; padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: bold; transition: background-color 0.3s;'>Download</a>";
													$attachmentHTML .= "<style>.btn:hover { background-color: #45a049; }</style>";
													break;
												case 'pdf':
													$attachmentHTML = "<a href='{$file}' target='_blank' class='btn btn-link' style='text-decoration: none; color: #333;'>Open PDF</a>";
													$attachmentHTML .= "<a href='{$file}' download class='btn btn-link' style='text-decoration: none; color: white; background-color: #4CAF50; padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: bold; transition: background-color 0.3s;'>Download</a>";
													$attachmentHTML .= "<style>.btn:hover { background-color: #45a049; }</style>";
													break;
												default:
													$attachmentHTML .= "<a href='{$file}' download class='btn btn-link' style='text-decoration: none; color: white; background-color: #4CAF50; padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: bold; transition: background-color 0.3s;'>Download</a>";
													$attachmentHTML .= "<style>.btn:hover { background-color: #45a049; }</style>";
													break;
											}
										}
										echo $attachmentHTML;
										?>

										<p style="margin: 0;"><?= linkify($chat['message']) ?></p>
										<?php
										if (isset($_SESSION['timezone'])) {
											$timezone = new DateTimeZone($_SESSION['timezone']);
										} else {
											$timezone = new DateTimeZone('UTC');
										}
										$dateTime = new DateTime($chat['created_at'], new DateTimeZone('UTC')); // Assuming stored time is in UTC
										$dateTime->setTimezone($timezone); // Convert to user's timezone
										$displayTime = $dateTime->format('M d, Y h:i A');
										?>
										<small style="display: block; color: #666; font-size: smaller;"><?= $displayTime ?></small>
										<?php if (isset($chat['sender_username']) && !empty($chat['sender_username'])) : ?>
											<small style="display: block; color: #666; font-size: smaller;">By <?= htmlspecialchars($chat['sender_username']) ?></small>
										<?php endif; ?>
									</div>

								</div>
							<?php endforeach;
						else : ?>
							<div class="alert alert-info text-center">
								<i class="fa fa-comments d-block fs-big"></i>
								No messages yet.
							</div>
						<?php endif; ?>
					</div>

				</div>

				<div id="replyIndicator" style="display: none; background-color: #f0f0f0; padding: 5px; border-radius: 5px; margin-bottom: 5px;">
					<button onclick="clearReply()" style="float: right;">&times;</button>
				</div>

				<div class="input-group mb-3" style="display: flex; align-items: center; width: 100%; height: 50px; background-color: #f8f9fa; border-radius: 25px; padding: 4px; box-shadow: 0 2px 4px rgba(0,0,0,0.15);">
					<button class="btn btn-outline-secondary" type="button" id="attachmentBtn" style="flex: 0 0 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-right: 5px; background-color: white;">
						<img src="../uploads/pin.png" alt="Attachment" style="width: 20px; height: 20px;">
					</button>
					<input type="file" id="fileInput" style="display: none;">

					<button class="btn btn-outline-secondary emoji-picker-button" type="button" style="flex: 0 0 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-right: 5px; background-color: white;">😊</button>
					<textarea id="message" class="form-control" style="flex-grow: 1; height: 100%; resize: none; padding: 8px; border-radius: 25px; margin-right: 5px; background-color: white; border: 1px solid #ced4da;" rows="1"></textarea>
					<button class="btn btn-primary" id="sendBtn" style="border: none; background: none; padding: 0; outline: none; margin-left: 10px; position:relative;top:2px ;flex: 0 0 40px; height: 40px;  display: flex; align-items: center; justify-content: center;">
						<!-- <i class="fas fa-paper-plane" style="width: 20px; height: 20px;"></i> -->
						<img src="../uploads/Qbutton.png" style="width: 60px; height: 60px;" alt="">
					</button>
				</div>
				<div id="emojiPicker" class="emoji-picker" style="display: none;"></div>
				<audio id="chatNotificationSound" src="../uploads/notification.wav" preload="auto"></audio>

			</div>


		</div>


		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

		<script>
			document.addEventListener("visibilitychange", function() {
				if (!document.hidden) {
					// The user has switched back to the tab, fetch new messages immediately
					fetchMessages();
				}
			});

			$(document).ready(function() {
				if ("Notification" in window) {
					Notification.requestPermission();
				}
			});

			function activateMessage(msgId) {
				// Remove active class from all messages
				document.querySelectorAll('.message-box').forEach(box => {
					box.classList.remove('active-message');
				});

				// Add active class to the clicked message
				const messageElement = document.getElementById(msgId);
				if (messageElement) {
					messageElement.classList.add('active-message');
					messageElement.scrollIntoView({
						behavior: 'smooth',
						block: 'center'
					});

					// Optional: Clear active state after a few seconds or on another action
					setTimeout(() => {
						messageElement.classList.remove('active-message');
					}, 3000); // Removes the active class after 3 seconds
				}
			}

			function activateOriginalMessage(msgId) {
				// Remove active class from all messages
				document.querySelectorAll('.message-box').forEach(box => {
					box.classList.remove('active-message');
				});

				// Add active class to the original message
				const originalMessage = document.getElementById(msgId);
				if (originalMessage) {
					originalMessage.classList.add('active-message');
					originalMessage.scrollIntoView({
						behavior: 'smooth',
						block: 'center'
					});
					setTimeout(() => {
						originalMessage.classList.remove('active-message');
					}, 3000);
				}


			}

			function scrollToMessage(msgId) {
				const messageElement = document.getElementById(msgId);
				if (messageElement) {
					messageElement.scrollIntoView({
						behavior: 'smooth',
						block: 'center'
					});
				}
			}


			function onNewMessageReceived() {
				var chatSound = document.getElementById('chatNotificationSound');
				chatSound.play();
				if (document.hidden && Notification.permission === "granted") {
					new Notification("New message", {
						body: "You have received a new message.",

					});
				}
			}

			document.getElementById('attachmentBtn').addEventListener('click', function() {
				document.getElementById('fileInput').click(); // Simulate click on the file input when attachment button is clicked
			});

			document.getElementById('fileInput').addEventListener('change', function() {
				sendMessage();
			});
			var replyToId = null;

			function setReplyTo(messageId, messageText) {
				replyToId = messageId; // Set the reply ID
				console.log("Replying to Message ID:", replyToId); // Debug: Output the reply ID

				const replyIndicator = document.getElementById('replyIndicator');
				replyIndicator.innerHTML = `Replying to: "${messageText}"`; // Show reply reference
				console.log("Message text set for reply:", messageText); // Debug: Output the message text being replied to

				replyIndicator.style.display = 'block'; // Make the reply indicator visible
				document.getElementById('message').focus(); // Focus the text area
			}

			function clearReply() {
				console.log("Clearing reply from Message ID:", replyToId); // Debug: Output the ID being cleared
				replyToId = null; // Clear the reply ID

				const replyIndicator = document.getElementById('replyIndicator');
				replyIndicator.style.display = 'none'; // Hide the reply indicator
				replyIndicator.innerHTML = ""; // Also clear the inner HTML

				const messageInput = document.getElementById('message');
				messageInput.value = ""; // Clear the text area
				messageInput.focus(); // Optional: Focus the text area again after clearing
				console.log("Reply cleared and message input reset."); // Debug: Confirmation of reset
			}
			// Assuming you receive `originalMessage` as part of the AJAX response for replies



			document.addEventListener('DOMContentLoaded', function() {

				const textarea = document.getElementById('message');
				const sendBtn = document.getElementById('sendBtn'); // Reference to the send button
				// Ensure sendMessage incorporates replyToId
				function sendMessage() {
					console.log("this is exexcuting");
					const message = document.getElementById('message').value.trim();
					if (message === '') {
						console.error('Cannot send an empty message.');
						return; // Exit the function if message is empty
					}

					const fileInput = document.getElementById('fileInput');
					const formData = new FormData();

					formData.append('message', message);
					if (fileInput.files.length > 0) {
						formData.append('attachment', fileInput.files[0]);
					}

					formData.append('to_id', <?= json_encode($chatWith['id']) ?>);

					if (replyToId !== null) {
						formData.append('reply_to_id', replyToId);
					}

					$.ajax({
						url: "../Public/Pages/Chat/app/ajax/insert.php",
						type: "POST",
						data: formData,
						processData: false,
						contentType: false,
						success: function(response) {
							const data = JSON.parse(response);
							console.log(data);
							if (data.status === "success") {
								console.log("Message sent successfully:", data.message, data.html, data.data);
								$("#chatBox").append(data.html); // Assuming the server responds with HTML to append

								document.getElementById('message').value = "";
								document.getElementById('fileInput').value = "";
								replyToId = null;
								clearReply(); // Call clearReply to reset the reply reference
								$("#chatBox").append(data); // Append the message to the chat box

								scrollDown();
							} else {
								console.error("Error in response:", data.message);
							}
						},
						error: function(xhr, status, error) {
							console.error("Error sending message:", xhr.responseText);
						}
					});
				}

				sendBtn.addEventListener('click', function() {
					sendMessage();
				});
				textarea.addEventListener('keydown', function(event) {
					if (event.key === "Enter" && !event.shiftKey) {
						event.preventDefault(); // Prevent new line
						sendMessage(); // Send the message
					}
				});
			});

			var scrollDown = function() {
				let chatBox = document.getElementById('chatBox');
				chatBox.scrollTop = chatBox.scrollHeight;
			}

			scrollDown();

			document.addEventListener('DOMContentLoaded', function() {
				// Function to update last seen status
				let lastSeenUpdate = function() {
					$.get("../Public/Pages/Chat/app/ajax/update_last_seen.php");
				};
				lastSeenUpdate(); // Call immediately when the document is ready

				// Interval for updating last seen status every 10 seconds
				setInterval(lastSeenUpdate, 10000);

				// Function to fetch new messages
				let fetchData = function() {
					let formData = new FormData(); // Initialize FormData object
					formData.append('id_2', <?= $chatWith['id'] ?>); // Append the user ID with whom the chat is happening

					$.ajax({
						url: "../Public/Pages/Chat/app/ajax/getMessage.php",
						type: "POST",
						data: formData,
						processData: false, // Prevent jQuery from automatically transforming the data into a query string
						contentType: false, // Set the content type of the request to false to let the browser set it
						success: function(response) {
							const data = JSON.parse(response);

							if (data.status === "success") {
								if (data.html.trim().length > 0) {
									$("#chatBox").append(data.html)

									scrollDown();
								}
							} else {
								console.error("Error in response:", data.message);
							}
						},
						error: function(xhr, status, error) {
							console.error("Error sending message:", xhr.responseText);
						}
					});
				};

				// Set interval to fetch new data every 500 milliseconds
				setInterval(fetchData, 500);
			});

			document.addEventListener('DOMContentLoaded', function() {
				const emojiPicker = document.getElementById('emojiPicker');
				const toggleButton = document.querySelector('.emoji-picker-button');
				const textarea = document.getElementById('message');
				const emojis = ['👍', '👎', '😀', '😁', '😂', '🤣', '😃', '😄', '😅', '😆', '😉', '😊', '😋', '😎', '😍', '😘', '🥰', '😗', '😙', '😚', '🙂', '🤗', '🤩', '😇', '🥳', '😏', '😌', '😒', '😞', '😔', '😟', '😕', '🙃', '🤔', '🤨', '😳', '😬', '🥺', '😠', '😡', '🤯', '😭', '😱', '😤', '😪', '😷', '🤒', '🤕', '🤢', '🤮', '🤧', '😴', '😈', '👿', '👹', '👺', '💀', '👻', '👽', '🤖', '💩', '😺', '😸', '😹', '😻', '😼', '😽', '🙀', '😿', '😾', '🙈', '🙉', '🙊', '💋', '💌', '💘', '💝', '💖', '💗', '💓', '💞', '💕', '💟', '❣️', '💔', '❤️', '🧡', '💛', '💚', '💙', '💜', '🤎', '🖤', '🤍'];
				emojis.forEach(emoji => {
					const button = document.createElement('button');
					button.textContent = emoji;
					button.style.border = 'none';
					button.style.background = 'transparent';
					button.style.cursor = 'pointer';
					button.onclick = function() {
						textarea.value += emoji;
					};
					emojiPicker.appendChild(button);
				});
				toggleButton.addEventListener('click', function() {
					const isDisplayed = window.getComputedStyle(emojiPicker).display !== 'none';
					emojiPicker.style.display = isDisplayed ? 'none' : 'block';
				});

				// Hide emoji picker when clicking outside
				document.addEventListener('click', function(event) {
					if (!emojiPicker.contains(event.target) && event.target !== toggleButton) {
						emojiPicker.style.display = 'none';
					}
				});
				textarea.addEventListener('keypress', function(event) {
					if (event.key === "Enter" && !event.shiftKey) {
						event.preventDefault(); // Prevent new line in textarea
						sendMessage();
					}
				});

				function sendMessage() {
					const message = textarea.value.trim();
					if (message !== '') {
						console.log('Message sent:', message);
						textarea.value = ''; // Clear the textarea after sending
					}
				}
			});
		</script>
		<?
		?>

	</main>
	<?php
	include("./Public/Pages/Common/theme_custom.php");

	?>
	<?php
	include("./Public/Pages/Common/settings_link.php");

	?>
	<?php
	include("./Public/Pages/Common/scripts.php");

	?>


</body>

</html>