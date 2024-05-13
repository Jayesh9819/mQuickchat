<?php

function getConversation($user_id, $conn)
{
  // SQL query to get all conversations for the current user, including the unread message count
  $sql = "SELECT user_1, user_2, MAX(created_at) as last_message_time, 
                 SUM(CASE WHEN to_id = ? AND opened = 0 THEN 1 ELSE 0 END) as unread_messages
          FROM (
              SELECT CASE WHEN from_id = ? THEN to_id ELSE from_id END AS user_1,
                     CASE WHEN to_id = ? THEN from_id ELSE to_id END AS user_2,
                     created_at, to_id, opened
              FROM chats
              WHERE from_id = ? OR to_id = ?
          ) AS derived_table
          GROUP BY user_1, user_2
          ORDER BY last_message_time DESC";

  $stmt = $conn->prepare($sql);
  $stmt->execute([$user_id, $user_id, $user_id, $user_id, $user_id]);

  if ($stmt->rowCount() > 0) {
    $conversations = $stmt->fetchAll();
    $user_data = []; // Array to store user conversations including unread messages count

    foreach ($conversations as $conversation) {
      // Determine the other user's ID in the conversation
      $other_user_id = ($conversation['user_1'] == $user_id) ? $conversation['user_2'] : $conversation['user_1'];

      // Fetch the other user's details
      if (substr($other_user_id, 0, 2) === 'UT') {
        $sql2 = "SELECT * FROM unknown_users WHERE id = ?";
      } else {
        $sql2 = "SELECT * FROM user WHERE id = ?";
      }
      $stmt2 = $conn->prepare($sql2);
      $stmt2->execute([$other_user_id]);

      if ($stmt2->rowCount() > 0) {
        $otherUserDetails = $stmt2->fetch(); // Assuming you need just one row per user
        $otherUserDetails['unread_messages'] = $conversation['unread_messages'];
        array_push($user_data, $otherUserDetails);
      }
    }
    return $user_data;
  } else {
    return []; // No conversations found
  }
}


function getAllUnreadMessages($conn)
{
  // SQL query to fetch all unread messages along with user details
  $sql = "SELECT
  COALESCE(from_user.id, from_unknown.id) AS from_user_id,
  COALESCE(from_user.name, from_unknown.username) AS from_user_name,
  COALESCE(from_user.pagename, from_unknown.pagename) AS from_pagename,
  COALESCE(to_user.id, to_unknown.id) AS to_user_id,
  COALESCE(to_user.name, to_unknown.username) AS to_user_name,
  COALESCE(to_user.pagename, to_unknown.pagename) AS to_pagename,
  COUNT(*) AS unread_count
FROM
  chats m
LEFT JOIN user AS from_user ON m.from_id = from_user.id AND from_user.role = 'User'
LEFT JOIN user AS to_user ON m.to_id = to_user.id
LEFT JOIN unknown_users AS from_unknown ON m.from_id = from_unknown.id
LEFT JOIN unknown_users AS to_unknown ON m.to_id = to_unknown.id
WHERE
  m.opened = 0
GROUP BY
  LEAST(m.from_id, m.to_id), GREATEST(m.from_id, m.to_id)
ORDER BY
  unread_count DESC;
";

  $stmt = $conn->prepare($sql);
  $stmt->execute();

  if ($stmt->rowCount() > 0) {
    $unreadMessages = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $unreadMessages;
  } else {
    return []; // No unread messages found
  }
}
