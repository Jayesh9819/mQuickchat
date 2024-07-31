<?php
require '../vendor/autoload.php';

use Google\Auth\CredentialsLoader;
use Google\Auth\HttpHandler\Guzzle6HttpHandler;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

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

function sendFCMNotification($userId, $title, $body) {
    // Include database configuration file
    include '../App/db/db_connect.php';

    $sql = "SELECT fcm_token FROM user_tokens WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        return "Failed to prepare SQL statement.";
    }

    $stmt->bind_param("i", $userId);
    if (!$stmt->execute()) {
        $stmt->close();
        return "Failed to execute SQL statement.";
    }

    $stmt->bind_result($token);
    $stmt->fetch();
    $stmt->close();
    $conn->close(); // Close the database connection

    // Send notification if token exists
    if ($token) {
        $accessToken = getAccessToken(); // Get OAuth 2.0 access token
        $projectId = 'quickchatbiz-a6bc8'; // Replace with your Firebase project ID

        $client = new Client();
        $url = "https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send";

        $message = [
            'message' => [
                'token' => $token,
                'notification' => [
                    'title' => $title,
                    'body' => $body
                ],
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
        } catch (RequestException $e) {
            $responseBody = $e->hasResponse() ? (string) $e->getResponse()->getBody() : 'No response body available';
            error_log('Error sending message: ' . $e->getMessage() . "\nResponse body: $responseBody");
            return 'Error sending message: ' . $e->getMessage();
        } catch (Exception $e) {
            error_log('Error sending message: ' . $e->getMessage());
            return 'Error sending message: ' . $e->getMessage();
        }
    } else {
        return "No token found.";
    }
}

// Usage example
echo sendFCMNotification(34, "hellooo", "Hiiiiiiiiii");
?>
