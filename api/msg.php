<?php
require '../vendor/autoload.php';

use Google\Auth\CredentialsLoader;
use Google\Auth\OAuth2;
use GuzzleHttp\Client;

function getAccessToken() {
    $jsonKeyFilePath = './key.json'; // Path to your service account key file

    $credentials = CredentialsLoader::makeCredentials(
        ['https://www.googleapis.com/auth/firebase.messaging'],
        json_decode(file_get_contents($jsonKeyFilePath), true)
    );

    $httpClient = new Client([
        'timeout' => 10.0,
        'verify' => false,
    ]);

    $token = $credentials->fetchAuthToken($httpClient);
    if (isset($token['access_token'])) {
        return $token['access_token'];
    } else {
        throw new Exception('Failed to get access token');
    }
}

function sendFCMNotification($token, $title, $body) {
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
            ]
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
}

// Fetch the token from your database
$token = "dG5FnLz7QaeokWnN5j0T78:APA91bGs5GdCR1hHBZ-keEqdTzMuVubuTj6Y0kSOvJSoP8tVFwy3MHWDm8clalP4XbtXNaYEVR4rcn1yDzEbu0jqmSFG3viuFDd3POu5c7o55PjlnjcZeqDb3_2yKl9psh4Bc2_9v69R"; // Replace with the user token you fetched from the database
$title = "Test Notification";
$body = "This is a test notification";

$response = sendFCMNotification($token, $title, $body);

// Ensure some output to verify script execution
echo "Script executed\n";
echo "Response: $response\n";
