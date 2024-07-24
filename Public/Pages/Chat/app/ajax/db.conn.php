<?php
ob_start();

# server name
$servername = "localhost"; // or your server name
$username = "quickchat";
$password = "12345678";
$dbname = "quickchat";

#creating database connection
try {
  $conn = new PDO(
    "mysql:host=$servername;dbname=$dbname",
    $username,
    $password
  );
  $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
  echo "Connection failed : " . $e->getMessage();
}
