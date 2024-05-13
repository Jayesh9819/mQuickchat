<?php

function getUser($username, $conn)
{
    // Check if the user exists in the 'user' table
    $sql = "SELECT * FROM user WHERE username=?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$username]);

    if ($stmt->rowCount() === 1) {
        $user = $stmt->fetch();
        return $user;
    } else {
        // If user not found in 'user' table, fetch from 'unknown_users'
        $sql = "SELECT * FROM unknown_users WHERE username=?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$username]);

        if ($stmt->rowCount() >= 1) {
            $user = $stmt->fetch();
            return $user;
        } else {
            // If user not found in either table, return an empty array
            return [];
        }
    }
}
function getPage($username, $conn)
{
        $sql = "SELECT * FROM page
                WHERE name=?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$username]);

        if ($stmt->rowCount() === 1) {
                $user = $stmt->fetch();
                return $user;
        } else {
                $user = [];
                return $user;
        }
}
