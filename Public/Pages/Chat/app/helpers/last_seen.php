<?php 
include "../db.conn.php";
function humanReadableLastSeen($last_seen_timestamp) {
    $diff = time() - strtotime($last_seen_timestamp);
    
    if ($diff < 60) {
        return 'Active';
    } elseif ($diff < 3600) {
        return round($diff / 60) . ' minutes ago';
    } elseif ($diff < 86400) {
        return round($diff / 3600) . ' hours ago';
    } elseif ($diff < 2592000) {
        return round($diff / 86400) . ' days ago';
    } elseif ($diff < 31536000) {
        return round($diff / 2592000) . ' months ago';
    } else {
        return round($diff / 31536000) . ' years ago';
    }
}
// Your existing PHP code to fetch and process the last seen times
$query = "SELECT username, last_seen FROM user WHERE role = 'Agent'";
$stmt = $conn->prepare($query);
$stmt->execute();
$agents = $stmt->fetchAll(PDO::FETCH_ASSOC);

$output = '';
foreach ($agents as $agent) {
    $output .= $agent['username'] . ': ' . humanReadableLastSeen($agent['last_seen']) . '<br>';
}

echo $output; 



?>