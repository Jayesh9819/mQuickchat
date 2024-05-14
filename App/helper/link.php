<?php
if (session_status() !== PHP_SESSION_ACTIVE) session_start();

function storeCurrentData()
{
    $currentUrl = $_SERVER['REQUEST_URI'];
    // Check if we are navigating back or not
    if (!isset($_GET['back'])) {
        if (empty($_SESSION['visited_urls']) || end($_SESSION['visited_urls']) !== $currentUrl) {
            $_SESSION['visited_urls'][] = $currentUrl;
        }
    }
    if (!empty($_POST)) {
        $_SESSION['post_data'] = $_POST;
    }
}

function navigateBack()
{
    if (!empty($_SESSION['visited_urls'])) {
        // Remove the current URL from history
        array_pop($_SESSION['visited_urls']);
        $previousUrl = end($_SESSION['visited_urls']) ?: './Portal'; // Fallback to the homepage
        header('Location: ' . $previousUrl . '?back=1'); // Redirect with the back parameter
        exit;
    }
}

// Handle back navigation
if (isset($_GET['return']) && $_GET['return'] == '1') {
    navigateBack();
} else {
    storeCurrentData();
}
?>
