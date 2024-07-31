<?php
require '../vendor/autoload.php';

use Google\Client;
use Google\Service\FirebaseCloudMessaging;
use Google\Service\FirebaseCloudMessaging\Message;
use Google\Service\FirebaseCloudMessaging\Notification;
use Google\Service\FirebaseCloudMessaging\SendMessageRequest;

function sendFCMNotification($token, $title, $body) {
    // Path to your service account key file
    $serviceAccountKeyFilePath = './key.json';

    // Check if the key file exists
    if (!file_exists($serviceAccountKeyFilePath)) {
        return 'Service account key file not found';
    }

    // Get the project ID from the service account key file
    $serviceAccount = json_decode(file_get_contents($serviceAccountKeyFilePath), true);
    if (isset($serviceAccount['project_id'])) {
        $projectId = $serviceAccount['project_id'];
    } else {
        return 'Project ID not found in service account key file';
    }

    $client = new Client();
    $client->setAuthConfig($serviceAccountKeyFilePath);
    $client->addScope('https://www.googleapis.com/auth/firebase.messaging');

    $fcm = new FirebaseCloudMessaging($client);

    $notification = new Notification();
    $notification->setTitle($title);
    $notification->setBody($body);

    $message = new Message();
    $message->setToken($token);
    $message->setNotification($notification);

    $sendMessageRequest = new SendMessageRequest();
    $sendMessageRequest->setMessage($message);

    try {
        $response = $fcm->projects_messages->send("projects/$projectId/messages:send", $sendMessageRequest);
        return json_encode($response, JSON_PRETTY_PRINT);
    } catch (Exception $e) {
        // Decode the HTTP response to get more details
        $response = $e->getResponse();
        if ($response) {
            $body = (string) $response->getBody();
            return 'Error sending message: ' . $e->getMessage() . "\nHTTP Response Body:\n" . $body;
        } else {
            return 'Error sending message: ' . $e->getMessage() . "\nStack trace:\n" . $e->getTraceAsString();
        }
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
?>
