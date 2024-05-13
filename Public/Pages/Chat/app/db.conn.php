<?php
ob_start();

# server name
$servername = "66.45.252.210"; // or your server name
$username = "QC";
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
