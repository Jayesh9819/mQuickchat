<?php
// Include database configuration file
include_once '../App/db/db_connect.php';

// Function to send FCM notification
function sendFCMNotification($userId, $title, $body)
{
    echo "this is execu";
    echo $title;
    echo $body;
    echo $userId;
    include '../App/db/db_connect.php';
    $sql = "SELECT fcm_token FROM user_tokens WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $stmt->bind_result($token);
    $stmt->fetch();
    $stmt->close();

    // Send notification if token exists
    if ($token) {
        $apiKey = 'AAAAfnk_oyY:APA91bE5TDkyJdwr1dTDtNmYAmeZ3-B6nlC_AwcRD3zgFQ4TcosDdq4JPCHFl_pd_CILt-x5H1Fh4NOgPkrVwgzF08wbkz1wZaCvWrui4qy528UVFVky02PRj6Bur5PnKflPbcdxwd63';
        $url = 'https://fcm.googleapis.com/fcm/send';
        $fields = [
            'to' => $token,
            'notification' => [
                'title' => $title,
                'body' => $body,
                'channel_id' => 'high_importance_channel',  // This should match the channel ID in Flutter
                'sound' => 's'  // This is optional and mainly controlled by Flutter
            ],
            'priority' => 'high'
        ];
        $headers = [
            'Authorization: key=' . $apiKey,
            'Content-Type: application/json'
        ];
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    } else {
        return "No token found.";
    }
}
//  echo sendFCMNotification(2,"hellooo","Hiiiiiiiiii");
