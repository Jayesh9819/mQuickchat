<?php
ob_start();

$servername = "66.45.252.210"; // or your server name
$username = "QC";
$password = "12345678";
$dbname = "quickchat";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} else {
    return $conn;
    // echo "success";
}
