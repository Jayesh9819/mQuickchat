<?php
if (session_status() !== PHP_SESSION_ACTIVE) session_start();
// Include code to check user role from your database or another source
// For example, assuming you have a $userRole variable set somewhere based on user data
$userRole =$_SESSION['role'];
$allowedRoles = ['User', 'Manager', 'Agent', 'Admin', 'Supervisor']; // Define allowed roles

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] != 1 || !isset($userRole) || !in_array($userRole, $allowedRoles)) {
    // The user is not logged in or does not have an allowed role
    header('Location: ../index.php/Login_to_CustCount'); // Redirect to the login page
    exit; // Prevent further script execution after redirect
}

// Proceed with page content here
