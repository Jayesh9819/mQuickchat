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
        throw new Exception('Failed to get access token: ' . (isset($token['error']) ? $token['error'] : 'Unknown error'));
    }
}

function sendFCMNotification($userId, $title, $body) {
    // Include database logic if not using a separate file
    // $conn = connectToDatabase(); // Replace with your connection logic

    $sql = "SELECT fcm_token FROM user_tokens WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        return "Failed to prepare SQL statement: " . $conn->error;
    }

    $stmt->bind_param("i", $userId);
    if (!$stmt->execute()) {
        $stmt->close();
        return "Failed to execute SQL statement: " . $conn->error;
    }

    $stmt->bind_result($token);
    $stmt->fetch();
    $stmt->close();
    // Close the database connection (if using a separate file)
    // $conn->close();

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

            // Check for successful response status code
            if ($response->getStatusCode() === 200) {
                return $response->getBody()->getContents();
            } else {
                $responseBody = (string) $response->getBody();
                error_log("Error sending message: Status code " . $response->getStatusCode() . "\nResponse body: $responseBody");
                return "Error sending message: Status code " . $response->getStatusCode();
            }
        } catch (RequestException $e) {
            $responseBody = $e->hasResponse() ? (string) $e->getResponse()->getBody() : 'No response body available';
            error_log('Error sending message: ' . $e->getMessage() . "\nResponse body: $responseBody");
            return 'Error sending message: ' . $e->getMessage();
        } catch (Exception $e) {
            error_log('Error sending message: ' . $e->getMessage());
            return 'Error sending message: Internal server error'. $e->getMessage(); // Consider a more specific error message

        }
    } else {
        return "No token found.";
    }
}

// Usage example
echo sendFCMNotification(34, "hellooo", "Hiiiiiiiiii");
?>
