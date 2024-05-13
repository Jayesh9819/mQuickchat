<?php 

session_start();
 function linkify($text)
{
	$urlPattern = '/\b(https?|ftp|file):\/\/[-A-Z0-9+&@#\/%?=~_|!:,.;]*[-A-Z0-9+&@#\/%=~_|]/i';
	$text = preg_replace($urlPattern, '<a class="rtext" href="$0" target="_blank">$0</a>', $text);
	return $text;
}

# check if the user is logged in
if (isset($_SESSION['username'])) {

	if (isset($_POST['id_2'])) {
	
	# database connection file
	include '../db.conn.php';

	$id_1  = $_SESSION['user_id'];
	$id_2  = $_POST['id_2'];
	$opend = 0;

	$sql = "SELECT * FROM bmessages
	        WHERE pagename=?
	        AND   from_id= ?
	        ORDER BY id ASC";
	$stmt = $conn->prepare($sql);
	$stmt->execute([$id_1, $id_2]);

	if ($stmt->rowCount() > 0) {
	    $chats = $stmt->fetchAll();

	    # looping through the chats
	    foreach ($chats as $chat) {
	    	if ($chat['opened'] == 0) {
	    		
	    		$opened = 1;
	    		$chat_id = $chat['chat_id'];

	    		$sql2 = "UPDATE bmessages
	    		         SET opened = ?
	    		         WHERE id = ?";
	    		$stmt2 = $conn->prepare($sql2);
	            $stmt2->execute([$opened, $chat_id]); 
				$attachmentHTML = '';
				if (!empty($chat['attachment'])) {
					// Assuming the attachment field contains the filename of the image
					$imageUrl = "../uploads/". $chat['attachment']; // Adjust the path as needed
					$attachmentHTML = "<img src='{$imageUrl}' alt='Attachment' style='max-width: 200px; display: block;'>";
				}


	            ?>
                  <p class="ltext border rounded p-2 mb-1">
				  <?= linkify($chat['message']) ?>
						<?=$attachmentHTML?> 
					    <small class="d-block">
					    	<?=$chat['created_at']?>
					    </small>     	
				  </p>        
	            <?php
	    	}
	    }
	}

 }

}else {
	header("Location: ../../index.php");
	exit;
}