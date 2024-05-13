<?php
include "./App/db/db_connect.php";
$sql = "SELECT * FROM websetting";
$stmt = $conn->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();
$results = $result->fetch_all(MYSQLI_ASSOC);
$settings = array();
foreach ($results as $row) {
    $settings[$row['name']] = $row['value'];
}

?>
