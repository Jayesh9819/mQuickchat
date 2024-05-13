<!doctype html>
<html lang="en" dir="ltr">

<head>
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<?php
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
		$chatWith = getPage($_GET['user'], $conn);
		if (isset($_GET['user'])) {
			echo "User parameter is: " . htmlspecialchars($_GET['user']);
		} else {
			echo "User parameter is missing.";
		}
		
		if (empty($chatWith)) {
			// header("Location: ./Chat_l");
			exit;
		}

		$chats = getChatPage($_SESSION['user_id'], $chatWith['name'], $conn);

		// opened($chatWith['name'], $conn, $chats);
	}



	?>

	<style>
		.vh-100 {
			min-height: 100vh;
		}

		.w-400 {
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
			max-height: 50vh;
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
			max-width: 750px;
			max-height: 300px;
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


		.emoji-picker {
			position: absolute;
			bottom: 60px;
			/* Adjust based on your layout */
			border: 1px solid #ddd;
			padding: 5px;
			background-color: white;
			width: 400px;
			/* Adjust as necessary */
			display: grid;
			grid-template-columns: repeat(8, 1fr);
			/* Adjust column count based on preference */
			gap: 5px;
			overflow-y: auto;
			max-height: 400px;
		}

		.emoji-picker button {
			font-size: 2rem;
			/* Increase font size for larger emojis */
		}

		/* Base styles */
		.chat-box {
			max-width: 750px;
			overflow-y: auto;
		}

		/* Medium devices (tablets, 768px and up) */
		@media (max-width: 768px) {
			.w-400 {
				width: 100%;
				/* Full width */
			}

			.chat-box {
				max-height: 40vh;
			}

			.fs-sm,
			.display-4 {
				font-size: 1rem;
				/* Adjust font size */
			}
		}

		/* Small devices (phones, 600px and down) */
		@media (max-width: 600px) {

			/* .w-400,
			.w-10{
				width: 100%;
			}
			.w-15 {
				width: 50%;
				/* Full width 
			} */

			.chat-box {
				max-height: 40vh;
			}

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
	<!-- loader Start -->
	<?php
	// include("./Public/Pages/Common/loader.php");

	?>
	<!-- loader END -->

	<!-- sidebar  -->
	<?php
	include("./Public/Pages/Common/sidebar.php");

	?>

	<main class="main-content">
		<?php
		include("./Public/Pages/Common/main_content.php");
		?>


		<div class="content-inner container-fluid pb-0" id="page_layout">
			<br><br><br>
			<div class="w-400 shadow p-4 rounded">

				<a href="./Page_Message" class="btn btn-dark">Back</a>

				<div class="d-flex align-items-center">
					<img src="../uploads/profile/<?= !empty($chatWith['p_p']) ? $chatWith['p_p'] : '07.png' ?>" class="w-15 rounded-circle">

					<h3 class="display-4">
						<?= $chatWith['name'] ?> <br>
						<div class="d-flex
               	              align-items-center" title="online">
							<!-- <?php
									if (last_seen($chatWith['last_seen']) == "Active") {
									?>
								<div class="online"></div>
								<small class="d-block p-1">Online</small>
							<?php } else { ?>
								<small style="max-width: small;" class="d-block p-1">
									Last seen:
									<?= last_seen($chatWith['last_seen']) ?>
								</small>
							<?php } ?> -->
						</div>
					</h3>
				</div>
				<?php function linkify($text)
				{
					$urlPattern = '/\b(https?|ftp|file):\/\/[-A-Z0-9+&@#\/%?=~_|!:,.;]*[-A-Z0-9+&@#\/%=~_|]/i';
					$text = preg_replace($urlPattern, '<a class="rtext" href="$0" target="_blank">$0</a>', $text);
					return $text;
				}
				?>
				<div class="shadow p-4 rounded
    	               d-flex flex-column
    	               mt-2 chat-box" id="chatBox">
					<?php
					if (!empty($chats)) {
						foreach ($chats as $chat) {
							if ($chat['from_id'] == $_SESSION['user_id']) { ?>
								<p class="rtext align-self-end
						        border rounded p-2 mb-1">
									<?= linkify($chat['message']) ?>
									<?php
									$attachmentHTML = '';
									if (!empty($chat['attachment'])) {
										$file = "../uploads/" . $chat['attachment']; // Adjust the path as needed
										$fileInfo = pathinfo($file);
										$fileExtension = strtolower($fileInfo['extension']);

										// Assuming the attachment field contains the filename of the image
										switch ($fileExtension) {
											case 'jpg':
											case 'jpeg':
											case 'png':
											case 'gif':
												// Link that opens the image in a new tab
												$attachmentHTML = "<a href='{$file}' target='_blank' class='w-15 image-view-link'><img src='{$file}' alt='Image' style='max-width: 200px; display: block; cursor: pointer;'></a>";
												// Add a download button
												$attachmentHTML .= "<a href='{$file}' download='{$fileInfo['basename']}' class='btn btn-primary btn-sm'>Download</a>";
												break;
											case 'mp4':
												// Video with a download button
												$attachmentHTML = "<video controls style='max-width: 200px; display: block;'>
																		<source src='{$file}' type='video/mp4'>
																		Your browser does not support the video tag.
																	</video>
																	<a href='{$file}' download='{$fileInfo['basename']}' class='btn btn-primary btn-sm'>Download</a>";
												break;
											case 'pdf':
												// PDF link with a download button
												$attachmentHTML = "<a href='{$file}' target='_blank'>Open PDF</a>
																   <a href='{$file}' download='{$fileInfo['basename']}' class='btn btn-primary btn-sm'>Download</a>";
												break;
											default:
												$attachmentHTML = "Unsupported file format";
												break;
										}
									}
									// echo $attachmentHTML;

									?>
									<?= $attachmentHTML ?>



									<small class="d-block">
										<?= $chat['created_at'] ?>
									</small>
								</p>
							<?php } else { ?>
								<p class="ltext border 
					         rounded p-2 mb-1">
									<?= linkify($chat['message']) ?>
									<?php
									$attachmentHTML = '';
									if (!empty($chat['attachment'])) {
										$file = "../uploads/" . $chat['attachment']; // Adjust the path as needed
										$fileInfo = pathinfo($file);
										$fileExtension = strtolower($fileInfo['extension']);

										// Assuming the attachment field contains the filename of the image
										switch ($fileExtension) {
											case 'jpg':
											case 'jpeg':
											case 'png':
											case 'gif':
												// Link that opens the image in a new tab
												$attachmentHTML = "<a href='{$file}' target='_blank' class='w-15 image-view-link'><img src='{$file}' alt='Image' style='max-width: 200px; display: block; cursor: pointer;'></a>";
												// Add a download button
												$attachmentHTML .= "<a href='{$file}' download='{$fileInfo['basename']}' class='btn btn-primary btn-sm'>Download</a>";
												break;
											case 'mp4':
												// Video with a download button
												$attachmentHTML = "<video controls style='max-width: 200px; display: block;'>
																		<source src='{$file}' type='video/mp4'>
																		Your browser does not support the video tag.
																	</video>
																	<a href='{$file}' download='{$fileInfo['basename']}' class='btn btn-primary btn-sm'>Download</a>";
												break;
											case 'pdf':
												// PDF link with a download button
												$attachmentHTML = "<a href='{$file}' target='_blank'>Open PDF</a>
																   <a href='{$file}' download='{$fileInfo['basename']}' class='btn btn-primary btn-sm'>Download</a>";
												break;
											default:
												$attachmentHTML = "Unsupported file format";
												break;
										}
									}
									// echo $attachmentHTML;

									?>
									<?= $attachmentHTML ?>

									<small style="font-size: x-small;" class="d-block">
										<?= $chat['created_at'] ?>
									</small>
								</p>
						<?php }
						}
					} else { ?>
						<div class="alert alert-info 
    				            text-center">
							<i class="fa fa-comments d-block fs-big"></i>
						</div>
					<?php } ?>
				</div>
				<!-- Remove the previous emoji-picker element -->
				<div class="input-group mb-3">
					<button class="btn btn-outline-secondary" style="max-width: 5%; padding: 0;" type="button" id="attachmentBtn">
						<img src="../uploads/pin.png" alt="Attachment" style="max-width: 90%; height: 90%; border-radius: none;">
					</button>
					<input type="file" id="fileInput" style="display: none;">

					<button class="btn btn-outline-secondary emoji-picker-button" type="button">😊</button>
					<textarea cols="3" id="message" class="form-control"></textarea>
					<button class="btn btn-primary" id="sendBtn">
						<i class="fa fa-paper-plane">Send</i>
					</button>
				</div>
				<div id="emojiPicker" class="emoji-picker" style="display: none;"></div>
				<audio id="chatNotificationSound" src="../uploads/notification.wav" preload="auto"></audio>

			</div>
			<script src="timezone_detect.js"></script>

			<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

			<script>

				$(document).ready(function() {
					if ("Notification" in window) {
						Notification.requestPermission();
					}
				});

				function onNewMessageReceived() {
					var chatSound = document.getElementById('chatNotificationSound');
					chatSound.play();

					// Show a notification if the tab is not active
					if (document.hidden && Notification.permission === "granted") {
						new Notification("New message", {
							body: "You have received a new message.",
							// icon: "/path/to/an/icon.png", // Optional: Add an icon
						});
					}
				}

				// Modify your fetchMessages function or its success callback to call onNewMessageReceived appropriately


				document.getElementById('attachmentBtn').addEventListener('click', function() {
					document.getElementById('fileInput').click(); // Simulate click on the file input when attachment button is clicked
				});

				document.getElementById('fileInput').addEventListener('change', function() {
					sendMessage(); // Trigger message send when a file is selected
				});

				document.addEventListener('DOMContentLoaded', function() {
					const textarea = document.getElementById('message');
					const sendBtn = document.getElementById('sendBtn'); // Reference to the send button

					function sendMessage() {
						const message = document.getElementById('message').value.trim();
						console.log('Message:', message); // Debug: Log the message content

						const fileInput = document.getElementById('fileInput');
						const formData = new FormData();

						formData.append('message', message);
						if (fileInput.files[0]) {
							formData.append('attachment', fileInput.files[0]);
							console.log('File:', fileInput.files[0].name); // Debug: Log the file name
						}

						const chatWith = <?= json_encode($chatWith['name']) ?>;
						console.log('Chat with:', chatWith); // Debug: Log the chat partner's name
						formData.append('to_id', chatWith);

						// Make the AJAX call using formData
						$.ajax({
							url: "../Public/Pages/Chat/app/ajax/binsert.php",
							type: "POST",
							data: formData,
							processData: false, // Prevent jQuery from automatically transforming the data into a query string
							contentType: false,
							success: function(data) {
								console.log('Success:', data); // Debug: Log successful data response
								document.getElementById('message').value = ""; // Clear the message input field
								document.getElementById('fileInput').value = ""; // Reset the file input
								$("#chatBox").append(data); // Assuming you want to append the message to the chat box
								scrollDown(); // Ensure the chat box scrolls to the latest message
							},
							error: function(jqXHR, textStatus, errorThrown) {
								console.error('AJAX error:', textStatus, errorThrown); // Debug: Log AJAX error
							}
						});
					}


					// Event listener for the send button
					sendBtn.addEventListener('click', function() {
						sendMessage();
					});

					// Event listener for the Enter key in the textarea
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

				$(document).ready(function() {

					let lastSeenUpdate = function() {
						$.get("../Public/Pages/Chat/app/ajax/update_last_seen.php");
					}
					lastSeenUpdate();
					setInterval(lastSeenUpdate, 10000);



					// auto refresh / reload
					let fechData = function() {
						$.post("../Public/Pages/Chat/app/ajax/bgetMessage.php", {
								id_2: '<?= $chatWith['name'] ?>'
							},
							function(data, status) {
								$("#chatBox").append(data);
								if (data != "") scrollDown();
								if (data != "") onNewMessageReceived();

							});
					}

					fechData();
					/** 
					auto update last seen 
					every 0.5 sec
					**/
					setInterval(fechData, 500);

				});
				document.addEventListener('DOMContentLoaded', function() {
					const emojiPicker = document.getElementById('emojiPicker');
					const toggleButton = document.querySelector('.emoji-picker-button');
					const textarea = document.getElementById('message');

					// Emoji list example, add more as needed
					const emojis = ['👍', '👎', '😀', '😁', '😂', '🤣', '😃', '😄', '😅', '😆', '😉', '😊', '😋', '😎', '😍', '😘', '🥰', '😗', '😙', '😚', '🙂', '🤗', '🤩', '😇', '🥳', '😏', '😌', '😒', '😞', '😔', '😟', '😕', '🙃', '🤔', '🤨', '😳', '😬', '🥺', '😠', '😡', '🤯', '😭', '😱', '😤', '😪', '😷', '🤒', '🤕', '🤢', '🤮', '🤧', '😴', '😈', '👿', '👹', '👺', '💀', '👻', '👽', '🤖', '💩', '😺', '😸', '😹', '😻', '😼', '😽', '🙀', '😿', '😾', '🙈', '🙉', '🙊', '💋', '💌', '💘', '💝', '💖', '💗', '💓', '💞', '💕', '💟', '❣️', '💔', '❤️', '🧡', '💛', '💚', '💙', '💜', '🤎', '🖤', '🤍'];

					// Populate the emoji picker
					emojis.forEach(emoji => {
						const button = document.createElement('button');
						button.textContent = emoji;
						button.style.border = 'none';
						button.style.background = 'transparent';
						button.style.cursor = 'pointer';
						button.onclick = function() {
							textarea.value += emoji;
							// emojiPicker.style.display = 'none'; // Hide picker after selection
						};
						emojiPicker.appendChild(button);
					});

					// Toggle emoji picker display
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

					// Send message on Enter key press
					textarea.addEventListener('keypress', function(event) {
						if (event.key === "Enter" && !event.shiftKey) {
							event.preventDefault(); // Prevent new line in textarea
							sendMessage();
						}
					});

					// Function to send the message
					function sendMessage() {
						const message = textarea.value.trim();
						if (message !== '') {
							console.log('Message sent:', message);
							textarea.value = ''; // Clear the textarea after sending
						}
					}
				});
			</script>




		</div>






		<?
		include("./Public/Pages/Common/footer.php");

		?>

	</main>
	<!-- Wrapper End-->
	<!-- Live Customizer start -->
	<!-- Setting offcanvas start here -->
	<?php
	include("./Public/Pages/Common/theme_custom.php");

	?>

	<!-- Settings sidebar end here -->

	<?php
	include("./Public/Pages/Common/settings_link.php");

	?>
	<!-- Live Customizer end -->

	<!-- Library Bundle Script -->
	<?php
	include("./Public/Pages/Common/scripts.php");

	?>

	<script>
	</script>

</body>

</html>