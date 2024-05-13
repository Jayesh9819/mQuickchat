<!doctype html>
<html lang="en" dir="ltr">

<head>
	<meta http-equiv="refresh" content="30">

	<?php
	ob_start();
	include("./Public/Pages/Common/head.php");
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
		// include './Public/Pages/Chat/./Public/Pages/Chat/app/';
		include 'app/helpers/user.php';
		include 'app/helpers/conversations.php';
		include 'app/helpers/timeAgo.php';
		include 'app/helpers/last_chat.php';
		if ($_SESSION['role'] == 'User') {
			// Fetch online agents in the same page
			$pagename = $_SESSION['page'];
			$sql = "SELECT * FROM user WHERE role = 'Agent' AND last_seen(last_seen) COLLATE utf8mb4_unicode_ci  = 'Active' AND pagename LIKE '%$pagename%' ";

			$stmt = $conn->prepare($sql);
			$stmt->execute();
			$agents = $stmt->fetchAll(PDO::FETCH_ASSOC);
			$user = getUser($_SESSION['username'], $conn);

			// Getting User conversations
			$conversations = getConversation($user['id'], $conn);
		} else {
			$user = getUser($_SESSION['username'], $conn);

			$conversations = getConversation($user['id'], $conn);
		}
		$role = $_SESSION['role'];
		if ($role == 'Admin' || $role == 'Manager' || $role == 'Supervisor') {
			$sql = "SELECT * FROM user WHERE role = 'Agent' AND last_seen(last_seen) COLLATE utf8mb4_unicode_ci  = 'Active' ";

			$stmt = $conn->prepare($sql);
			$stmt->execute();
			$onlineAgents = $stmt->fetchAll(PDO::FETCH_ASSOC);
			$pending = getAllUnreadMessages($conn);
		}
		if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
			// This is an AJAX request
			if (isset($_SESSION['user_id'])) {
				$user_id = $_SESSION['user_id'];
				$conversations1 = getConversation($user_id, $conn);
				echo json_encode($conversations1);  // Return data in JSON format
			} else {
				echo json_encode([]); // No user session, return empty array
			}
			exit; // Prevent further execution
		}
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

		a {
			text-decoration: none;
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
			color: #bbb;
			font-size: 0.7rem;
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
			background-color: blueviolet;
			color: black;
			max-width: 50%;
			font-size: large;
		}

		.rtext {
			background-color: blue;
			color: aliceblue;
			max-width: 50%;
			font-size: large;


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

		.unread-messages {
			display: inline-block;
			background-color: green;
			color: white;
			font-size: 0.8em;
			border-radius: 50%;
			padding: 2px 6px;
			margin-left: 5px;
			vertical-align: top;
			float: right;
			/* Add float right to move it to the right side */
			margin-right: 10px;
			/* Adjust as needed for spacing from the right edge */
		}

		.d-flex.align-items-center {
			flex-grow: 1;
			/* Ensure the container takes all available space */
		}
	</style>



</head>

<body>
	<!-- loader Start -->
	<?php
	// include("./Public/Pages/Common/loader.php");

	?>
	<!-- loader END -->

	<!-- sidebar  -->
	<?php
	include("./Public/Pages/Common/sidebar.php");

	?>



	<div class="content-inner container-fluid pb-0" id="page_layout">
		<div class="p-2 w-100
                rounded shadow">
			<?php if ($_SESSION['role'] == 'User') { ?>
				<div>
					<h3>Online Agents Available for Chat</h3>
					<ul>
						<?php foreach ($agents as $agent) { ?>
							<a href="./Chat_Screen?user=<?= $agent['username'] ?>" class="d-flex
	    				          justify-content-between
	    				          align-items-center p-2">
								<div class="d-flex
	    					            align-items-center">
									<img src="../uploads/profile/<?= !empty($chatWith['p_p']) ? $chatWith['p_p'] : '07.png' ?>" class="w-15 rounded-circle">
									<h3 class="fs-xs m-2">
										<?= $agent['username'] ?><br>
									</h3>
								</div>
								</li>
							<?php } ?>
					</ul>
				</div>
				<div>
					<h3>Chat History</h3>
				</div>


			<?php } else { ?>

				<div>
					<div class="d-flex
    		            mb-3 p-3 bg-light
			            justify-content-between
			            align-items-center">
						<div class="d-flex
    			            align-items-center">
							<img src="../uploads/profile/<?= !empty($chatWith['p_p']) ? $chatWith['p_p'] : '07.png' ?>" class="w-15 rounded-circle">
							<h3 class="fs-xs m-2"><?= $user['username'] ?? $_SESSION['username'] ?></h3>
						</div>
					</div>

					<div class="input-group mb-3">
						<input type="text" placeholder="Search..." id="searchText" class="form-control">
						<button class="btn btn-primary" id="serachBtn">
							<i class="fa fa-search">Search</i>
						</button>
					</div>

				<?php
			} ?>

				<ul id="chatList" class="list-group mvh-50 overflow-auto" style="padding: 0; margin: 0; list-style: none; background-color: #121212;">
					<?php
					if (!empty($conversations)) {
						foreach ($conversations as $conversation) {
							$hasUnread = !empty($conversation['unread_messages']) && $conversation['unread_messages'] > 0;
							$bgColor = $hasUnread ? 'limegreen' : 'lightblue';
							$statusDot = last_seen($conversation['last_seen']) == "Active" ? '<span class="status-dot" style="width: 10px; height: 10px; background-color: #0f0; border-radius: 50%; display: inline-block; margin-left: 10px;"></span>' : '';
					?>
							<li class="list-group-item" style="border-bottom: 1px solid #333; display: flex; justify-content: space-between; align-items: center; padding: 12px; background-color: <?= $bgColor; ?>;">
								<a href="./Chat_Screen?user=<?= htmlspecialchars($conversation['username']); ?>" style="display: flex; align-items: center; text-decoration: none; color: #ddd; width: 100%;">
									<div class="chat-avatar" style="flex-shrink: 0;">
										<img src="../uploads/profile/<?= !empty($conversation['p_p']) ? htmlspecialchars($conversation['p_p']) : '07.png'; ?>" style="width: 48px; height: 48px; border-radius: 50%; border: 2px solid #2c2c2c;">
									</div>
									<div class="chat-details" style="flex-grow: 1; margin-left: 15px;">
										<h5 style="margin: 0; font-size: 16px; font-weight: 500; color: darkblue;"><?= htmlspecialchars($conversation['username']); ?></h5>
										<?php
										if ($conversation['role'] == 'User') {
											echo '<h5 style="margin: 0; font-size: 16px; font-weight: 500; color: darkblue;">Page Name:-' . htmlspecialchars($conversation['pagename']) . '</h5>';
										} elseif ($conversation['role'] == 'query') {
											echo '<h5 style="margin: 0; font-size: 16px; font-weight: 500; color: darkblue;">Page Name:-' . htmlspecialchars($conversation['pagename']) . '</h5>';
										}
										?>
										<h6 style="color: #010011; font-size: 14px; display: block;"><?= lastChat($_SESSION['user_id'], $conversation['id'], $conn); ?></h6>
									</div>
									<?php if ($hasUnread) { ?>
										<span class="badge badge-primary unread-badge" data-conversation-id="<?= $conversation['id']; ?>" style="background-color: #007bff; color: white; padding: 6px 12px; border-radius: 20px; font-size: 12px;">
											<?= $conversation['unread_messages']; ?>
										</span>
									<?php } ?>

									<?= $statusDot; ?>
								</a>
							</li>
						<?php
						}
					} else { ?>
						<div class="alert alert-info" style="text-align: center; background-color: #282828; color: #ccc; padding: 20px; margin-top: 20px; border-radius: 4px;">
							<i class="fa fa-comments" style="font-size: 24px; display: block; margin-bottom: 10px;"></i>
							No messages yet, start the conversation
						</div>
					<?php } ?>

				</ul>
				<?php if ($role == 'Admin' || $role == 'Manager' || $role == 'Supervisor') {
				?>
					<h3>Pending Chats</h3>
					<ul id="chatList" class="list-group mvh-50 overflow-auto" style="padding: 0; margin: 0; list-style: none; background-color: #121212;">
						<?php
						if (!empty($pending)) {
							foreach ($pending as $conversation) {
								$hasUnread = !empty($conversation['unread_count']) && $conversation['unread_count'] > 0;
								$bgColor = $hasUnread ? 'limegreen' : 'lightblue';
						?> <?php if ($hasUnread) { ?>

									<li class="list-group-item" style="border-bottom: 1px solid #333; display: flex; justify-content: space-between; align-items: center; padding: 12px; background-color: <?= $bgColor; ?>;">
										<a href="./Chat_Screen?user=<?= htmlspecialchars($conversation['from_user_name'] ?? 'Unknown'); ?>" style="display: flex; align-items: center; text-decoration: none; color: #ddd; width: 100%;">
											<div class="chat-avatar" style="flex-shrink: 0;">
												<img src="../uploads/profile/<?= !empty($conversation['p_p']) ? htmlspecialchars($conversation['p_p']) : '07.png'; ?>" style="width: 48px; height: 48px; border-radius: 50%; border: 2px solid #2c2c2c;">
											</div>
											<div class="chat-details" style="flex-grow: 1; margin-left: 15px;">
												<h5 style="margin: 0; font-size: 16px; font-weight: 500; color: darkblue;"><?= htmlspecialchars($conversation['from_user_name'] ?? 'Unknown'); ?></h5>
												<?php
												echo '<h5 style="margin: 0; font-size: 16px; font-weight: 500; color: darkblue;">Page Name: -' . htmlspecialchars($conversation['from_pagename'] ?? 'N/A') . '</h5>';
												?>
											</div>
											<span class="badge badge-primary unread-badge" data-conversation-id="<?= $conversation['from_user_id']; ?>" style="background-color: #007bff; color: white; padding: 6px 12px; border-radius: 20px; font-size: 12px;">
												<?= $conversation['unread_count']; ?>
											</span>

										<?php } ?>

										</a>
									</li>
								<?php
							}
						} else { ?>
								<div class="alert alert-info" style="text-align: center; background-color: #282828; color: #ccc; padding: 20px; margin-top: 20px; border-radius: 4px;">
									<i class="fa fa-comments" style="font-size: 24px; display: block; margin-bottom: 10px;"></i>
									No messages yet, start the conversation
								</div>
							<?php } ?>

					</ul>
					<h3>Online Agents</h3>
					<ul id="chatList" class="list-group mvh-50 overflow-auto" style="padding: 0; margin: 0; list-style: none; background-color: #121212;">
						<?php
						if (!empty($onlineAgents)) {
							foreach ($onlineAgents as $conversation) {
								$hasUnread = !empty($conversation['unread_messages']) && $conversation['unread_messages'] > 0;
								$bgColor = $hasUnread ? 'limegreen' : 'lightblue';
								$statusDot = last_seen($conversation['last_seen']) == "Active" ? '<span class="status-dot" style="width: 10px; height: 10px; background-color: #0f0; border-radius: 50%; display: inline-block; margin-left: 10px;"></span>' : '';
						?>
								<li class="list-group-item" style="border-bottom: 1px solid #333; display: flex; justify-content: space-between; align-items: center; padding: 12px; background-color: <?= $bgColor; ?>;">
									<a href="./Chat_Screen?user=<?= htmlspecialchars($conversation['username']); ?>" style="display: flex; align-items: center; text-decoration: none; color: #ddd; width: 100%;">
										<div class="chat-avatar" style="flex-shrink: 0;">
											<img src="../uploads/profile/<?= !empty($conversation['p_p']) ? htmlspecialchars($conversation['p_p']) : '07.png'; ?>" style="width: 48px; height: 48px; border-radius: 50%; border: 2px solid #2c2c2c;">
										</div>
										<div class="chat-details" style="flex-grow: 1; margin-left: 15px;">
											<h5 style="margin: 0; font-size: 16px; font-weight: 500; color: darkblue;"><?= htmlspecialchars($conversation['username']); ?></h5>
											<?php
											if ($conversation['role'] == 'User') {
												echo '<h5 style="margin: 0; font-size: 16px; font-weight: 500; color: darkblue;">Page Name:-' . htmlspecialchars($conversation['pagename']) . '</h5>';
											} elseif ($conversation['role'] == 'query') {
												echo '<h5 style="margin: 0; font-size: 16px; font-weight: 500; color: darkblue;">Page Name:-' . htmlspecialchars($conversation['pagename']) . '</h5>';
											}
											?>
											<h6 style="color: #010011; font-size: 14px; display: block;"><?= lastChat($_SESSION['user_id'], $conversation['id'], $conn); ?></h6>
										</div>
										<?php if ($hasUnread) { ?>
											<span class="badge badge-primary unread-badge" data-conversation-id="<?= $conversation['id']; ?>" style="background-color: #007bff; color: white; padding: 6px 12px; border-radius: 20px; font-size: 12px;">
												<?= $conversation['unread_messages']; ?>
											</span>
											<?= $statusDot; ?>

										<?php } ?>

									</a>
								</li>
							<?php
							}
						} else { ?>
							<div class="alert alert-info" style="text-align: center; background-color: #282828; color: #ccc; padding: 20px; margin-top: 20px; border-radius: 4px;">
								<i class="fa fa-comments" style="font-size: 24px; display: block; margin-bottom: 10px;"></i>
								No messages yet, start the conversation
							</div>
						<?php } ?>

					</ul>
				<?php } ?>

				</div>
		</div>


		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
		<script>
			function fetchConversations() {
				$.ajax({
					url: 'index.php', // Making an AJAX request to the same file
					type: 'GET',
					dataType: 'json',
					success: function(data) {
						console.log(data); // Process and display the conversations
						// Update the HTML based on returned data
						var html = '';
						data.forEach(function(conv) {
							html += '<div>' + conv.username + ' - Unread messages: ' + conv.unread_messages + '</div>';
						});
						document.getElementById('conversation-list').innerHTML = html;
					},
					error: function() {
						console.error('Error fetching conversations.');
					}
				});
			}

			// Fetch conversations every 5 seconds
			setInterval(fetchConversations, 1000);
			// Also fetch them immediately on page load
			fetchConversations();
		</script>
		<script>
			$(document).ready(function() {

				// Search
				$("#searchText").on("input", function() {
					var searchText = $(this).val();
					if (searchText == "") return;
					$.post('../Public/Pages/Chat/app/ajax/search.php', {
							key: searchText
						},
						function(data, status) {
							$("#chatList").html(data);
						});
				});

				// Search using the button
				$("#serachBtn").on("click", function() {
					var searchText = $("#searchText").val();
					if (searchText == "") return;
					$.post('../Public/Pages/Chat/app/ajax/search.php', {
							key: searchText
						},
						function(data, status) {
							$("#chatList").html(data);
						});
				});




				/** 
				auto update last seen 
				for logged in user
				**/
				let lastSeenUpdate = function() {
					$.get('../Public/Pages/Chat/app/ajax/update_last_seen.php')
						.done(function(data) {
							console.log('Success:', data); // Successful response handling
						})
						.fail(function(jqXHR, textStatus, errorThrown) {
							console.error('AJAX Error:', textStatus); // Error handling
						});
				};

				lastSeenUpdate(); // Initial call
				setInterval(lastSeenUpdate, 10000); // Set to run every 10 seconds
			});
		</script>




	</div>






	<?
	include("./Public/Pages/Common/footer.php");

	?>

	</main>
	<?php
	include("./Public/Pages/Common/settings_link.php");

	?>
	<?php
	include("./Public/Pages/Common/script.php");

	?>

</body>

</html>