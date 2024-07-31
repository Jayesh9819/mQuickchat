<?php
require '../vendor/autoload.php';

use Google\Client;
use Google\Service\FirebaseCloudMessaging;

function sendFCMNotification($token, $title, $body) {
    // Path to your service account key file
    $serviceAccountKeyFilePath = './key.json';

    // Check if the key file exists
    if (!file_exists($serviceAccountKeyFilePath)) {
        return 'Service account key file not found';
    }

    $client = new Client();
    $client->setAuthConfig($serviceAccountKeyFilePath);
    $client->addScope('https://www.googleapis.com/auth/firebase.messaging');

    $fcm = new FirebaseCloudMessaging($client);

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
        $response = $fcm->projects_messages->send('projects/' . $client->getProjectId() . '/messages:send', $message);
        return $response;
    } catch (Exception $e) {
        return 'Error sending message: ' . $e->getMessage();
    }
}

// Fetch the token from your database
$token = "dG5FnLz7QaeokWnN5j0T78:APA91bGs5GdCR1hHBZ-keEqdTzMuVubuTj6Y0kSOvJSoP8tVFwy3MHWDm8clalP4XbtXNaYEVR4rcn1yDzEbu0jqmSFG3viuFDd3POu5c7o55PjlnjcZeqDb3_2yKl9psh4Bc2_9v69R"; // Replace with the user token you fetched from the database
$title = "Test Notification";
$body = "This is a test notification";

$response = sendFCMNotification($token, $title, $body);

// Ensure some output to verify script execution
echo "Script executed \n";
echo "Response: $response\n";
?>
