<?php
require '../vendor/autoload.php';

use Google\Auth\CredentialsLoader;
use Google\Auth\HttpHandler\Guzzle6HttpHandler;
use GuzzleHttp\Client;

function getAccessToken() {
    $jsonKeyFilePath = './key.json'; // Path to your service account key file

    $credentials = CredentialsLoader::makeCredentials(
        ['https://www.googleapis.com/auth/firebase.messaging'],
        json_decode(file_get_contents($jsonKeyFilePath), true)
    );

    $httpHandler = new Guzzle6HttpHandler(new Client());
    $token = $credentials->fetchAuthToken($httpHandler);
    if (isset($token['access_token'])) {
        return $token['access_token'];
    } else {
        throw new Exception('Failed to get access token');
    }
}

function sendFCMNotification($userId, $title, $body)
{
    // Include database configuration file
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
        $accessToken = getAccessToken(); // Get OAuth 2.0 access token
        $projectId = 'YOUR_PROJECT_ID'; // Replace with your Firebase project ID

        $client = new Client();
        $url = "https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send";

        $message = [
            'message' => [
                'token' => $token,
                'notification' => [
                    'title' => $title,
                    'body' => $body,
                    'channel_id' => 'high_importance_channel',  // This should match the channel ID in Flutter
                    'sound' => 'default'  // This is optional and mainly controlled by Flutter
                ],
                'priority' => 'high'
            ]
        ];

        try {
            $response = $client->post($url, [
                'headers' => [
                    'Authorization' => "Bearer {$accessToken}",
                    'Content-Type' => 'application/json',
                ],
                'body' => json_encode($message),
            ]);

            return $response->getBody()->getContents();
        } catch (Exception $e) {
            error_log('Error sending message: ' . $e->getMessage());
            return 'Error sending message: ' . $e->getMessage();
        }
    } else {
        return "No token found.";
    }
}

// Usage example
echo sendFCMNotification(2, "hellooo", "Hiiiiiiiiii");
?>
