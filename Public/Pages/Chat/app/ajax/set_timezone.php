<?php
// set_timezone.php

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['timezone'])) {
        $timezone = $_POST['timezone'];
        if (in_array($timezone, timezone_identifiers_list())) {
            date_default_timezone_set($timezone);
            echo date_default_timezone_get();
        } else {
            // Invalid timezone received, handle the error
            http_response_code(400);
            echo "Invalid timezone";
        }
    } else {
        // No timezone received, handle the error
        http_response_code(400);
        echo "No timezone set";
    }
} else {
    // Not a POST request, handle accordingly
    http_response_code(405);
    // echo "Method Not Allowed";
}
?>
