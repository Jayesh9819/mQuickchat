<?php
require '../vendor/autoload.php';

use Google\Client;
use Google\Service\FirebaseCloudMessaging;
use Google\Service\FirebaseCloudMessaging\Message;
use Google\Service\FirebaseCloudMessaging\Notification;
use Google\Service\FirebaseCloudMessaging\SendMessageRequest;
use Google\Service\Exception as GoogleServiceException;

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

    // Initialize the Google Client
    $client = new Client();
    $client->setAuthConfig($serviceAccountKeyFilePath);
    $client->addScope('https://www.googleapis.com/auth/firebase.messaging');

    // Initialize the FCM service
    $fcm = new FirebaseCloudMessaging($client);

    // Create the notification
    $notification = new Notification();
    $notification->setTitle($title);
    $notification->setBody($body);

    // Check for empty token
    if (empty($token)) {
        return 'Missing device token';
    }

    // Create the message
    $message = new Message();
    $message->setToken($token);
    $message->setNotification($notification);

    // Create the send message request
    $sendMessageRequest = new SendMessageRequest();
    $sendMessageRequest->setMessage($message);

    try {
        // Send the message
        $response = $fcm->projects_messages->send("projects/$projectId/messages:send", $sendMessageRequest);
        return json_encode($response, JSON_PRETTY_PRINT);
    } catch (GoogleServiceException $e) {
        // Capture the full exception details
        $responseBody = method_exists($e, 'getResponseBody') ? $e->getResponseBody() : 'No response body available';
        $errorDetails = [
            'message' => $e->getMessage(),
            'responseBody' => $responseBody,
            'stackTrace' => $e->getTraceAsString(),
        ];
        return 'Error sending message: ' . json_encode($errorDetails, JSON_PRETTY_PRINT);
    } catch (Exception $e) {
        // Handle any other exceptions
        return 'Error sending message: ' . $e->getMessage() . "\nStack trace:\n" . $e->getTraceAsString();
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
