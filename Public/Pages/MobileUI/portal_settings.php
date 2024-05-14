<!doctype html>
<html lang="en" dir="ltr">

<head>
    <?php
    include("./Public/Pages/Common/head.php");
    include "./Public/Pages/Common/auth_user.php";

    if (isset($_SESSION['toast'])) {
        $toast = $_SESSION['toast'];
        echoToastScript($toast['type'], $toast['message']);
        unset($_SESSION['toast']); // Clear the toast message from session
    }

    if (session_status() !== PHP_SESSION_ACTIVE) session_start();

    // Display error message if available
    if (isset($_SESSION['login_error'])) {
        echo '<p class="error">' . $_SESSION['login_error'] . '</p>';
        unset($_SESSION['login_error']); // Clear the error message
    }


    include './App/db/db_connect.php';
    // Check if the form has been submitted
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['time_zone'])) {
        $userId = $_SESSION['user_id'];
        $newTimeZone = $_POST['time_zone'];

        $sql = "UPDATE user SET timezone = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$newTimeZone, $userId]);
        unset($_SESSION['timezone']);
        $_SESSION['timezone'] = $newTimeZone;

        $_SESSION['toast'] = ['type' => 'success', 'message' => 'Time zone updated successfully'];
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    }

    // Handle password change
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['new_password'], $_POST['confirm_password'])) {
        $newPassword = $_POST['new_password'];
        $confirmPassword = $_POST['confirm_password'];

        if ($newPassword === $confirmPassword) {
            $userId = $_SESSION['user_id'];

            $sql = "UPDATE user SET password = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$newPassword, $userId]);

            $_SESSION['toast'] = ['type' => 'success', 'message' => 'Password Chnaged.'];
            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        } else {
            $_SESSION['toast'] = ['type' => 'error', 'message' => 'Passwords do not match'];
            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        }
    }

    // Handle profile picture upload
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['profile_picture'])) {
        $userId = $_SESSION['user_id'];
        // $profilePicture = $_FILES['profile_picture'];
        $uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/uploads/profile/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $profilePicture = null;
        if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
            $fileName = time() . '-' . basename($_FILES['profile_picture']['name']);
            $targetFilePath = $uploadDir . $fileName;
            if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $targetFilePath)) {
                $profilePicture = $fileName;
            } else {
                echo "Error uploading file.";
                exit;
            }
        }


        $sql = "UPDATE user SET p_p = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$profilePicture, $userId]);
        unset($_SESSION['p_p']);
        $_SESSION['p_p'] = $profilePicture;

        $_SESSION['toast'] = ['type' => 'success', 'message' => 'Profile picture updated successfully'];
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    }
    function updateChatSetting($conn, $userId, $type, $path, $isActive)
    {
        // Check if the setting already exists
        $stmt = $conn->prepare("SELECT id FROM chatSettings WHERE user_id = ? AND type = ?");
        $stmt->bind_param("is", $userId, $type);
        $stmt->execute();
        $result = $stmt->get_result();
        $existing = $result->fetch_assoc();

        if ($existing) {
            // Update existing setting
            $stmt = $conn->prepare("UPDATE chatSettings SET path = ?, status = ?, updated_at = NOW() WHERE id = ?");
            $stmt->bind_param("sii", $path, $isActive, $existing['id']);
            $stmt->execute();
        } else {
            // Insert new setting
            $stmt = $conn->prepare("INSERT INTO chatSettings (user_id, type, path, status, created_at, updated_at) VALUES (?, ?, ?, ?, NOW(), NOW())");
            $stmt->bind_param("issi", $userId, $type, $path, $isActive);
            $stmt->execute();
        }
    }

    // Check if the wallpaper form has been submitted
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['wallpaper'])) {
        $userId = $_SESSION['user_id'];
        $isActive = 1;  // Set as active
        $path = $_POST['wallpaper'];  // Default selection

        if ($path === "custom" && isset($_FILES['custom_wallpaper']['tmp_name'])) {
            $uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/uploads/wallpapers/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            $fileName = time() . '-' . basename($_FILES['custom_wallpaper']['name']);
            $targetFilePath = $uploadDir . $fileName;
            if (move_uploaded_file($_FILES['custom_wallpaper']['tmp_name'], $targetFilePath)) {
                $path = $fileName;  // Update path to the uploaded file
            } else {
                $path = "upload_failed";  // Handle upload failure
            }
        }
        updateChatSetting($conn, $userId, "image", $path, $isActive);
    }

    // Check if the notification tone form has been submitted
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['notification_tone'])) {
        $userId = $_SESSION['user_id'];
        $isActive = 1;
        $path = $_POST['notification_tone'];  // Default selection

        if ($path === "custom" && isset($_FILES['custom_tone']['tmp_name'])) {
            $uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/uploads/tone/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            $fileName = time() . '-' . basename($_FILES['custom_tone']['name']);
            $targetFilePath = $uploadDir . $fileName;
            if (move_uploaded_file($_FILES['custom_tone']['tmp_name'], $targetFilePath)) {
                $path = $fileName;  // Update path to the uploaded file
            } else {
                $path = "upload_failed";  // Handle upload failure
            }
        }
        updateChatSetting($conn, $userId, "sound", $path, $isActive);
    }
    ?>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">

    <style>
        .settings-form {
            margin-bottom: 20px;
        }

        /* body,
        html {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        } */

        /* .container {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-around;
            padding: 10px;
        } */

        .image-box {
            flex-basis: calc(50% - 20px);
            /* Assuming 10px margin around each box */
            margin: 10px;
            height: 0;
            padding-top: 25%;
            /* This maintains a 4:2 aspect ratio */
            position: relative;
            background-color: #f0f0f0;
            /* Light grey background for visibility */
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            /* Optional: adds shadow for better visibility */
        }

        .image-box img {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            /* Ensures the image covers the box, might crop */
        }
    </style>


</head>

<body class="  ">
    <!-- loader Start -->
    <?php
    // include("./Public/Pages/Common/loader.php");

    ?>
    <!-- loader END -->

    <!-- sidebar  -->
    <?php //include("./Public/Pages/Common/loader.php"); 
    ?>
    <?php include("./Public/Pages/Common/header.php"); ?>
    <?php include("./Public/Pages/Common/sidebar.php"); ?>


    <div class="page-content-wrapper">

        <div class="row">
            <div class="col-md-8 col-lg-6 mx-auto">
                <h3 class="mt-4 mb-3">Your Settings</h3>

                <!-- Time Zone Update Form -->
                <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" class="settings-form">
                    <div class="form-group">
                        <label for="time_zone">Select your time zone:</label>
                        <select name="time_zone" id="time_zone" class="form-control">
                            <?php
                            $us_timezones = [
                                'America/New_York',    // Eastern Time
                                'America/Chicago',     // Central Time
                                'America/Denver',      // Mountain Time
                                'America/Phoenix',     // Arizona Time
                                'America/Los_Angeles', // Pacific Time
                                'America/Anchorage',   // Alaska Time
                                'America/Honolulu'     // Hawaii Time
                            ];
                            foreach ($us_timezones as $timezone) {
                                $selected = ($_SESSION['timezone'] ?? 'America/New_York') === $timezone ? ' selected' : '';
                                echo "<option value=\"$timezone\"$selected>$timezone</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Update Time Zone</button>
                </form>

                <!-- Password Change Form -->
                <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" class="settings-form">
                    <div class="form-group">
                        <input type="password" name="new_password" placeholder="New Password" required class="form-control">
                    </div>
                    <div class="form-group">
                        <input type="password" name="confirm_password" placeholder="Confirm New Password" required class="form-control">
                    </div>
                    <button type="submit" class="btn btn-primary">Change Password</button>
                </form>

                <!-- Profile Picture Update Form -->
                <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" class="settings-form">
                    <div class="form-group">
                        <input type="file" name="profile_picture" required class="form-control-file">
                    </div>
                    <button type="submit" class="btn btn-primary">Upload Picture</button>
                </form>
                <!-- Enhanced Wallpaper Selector -->
                <!-- <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" class="settings-form">
                    <div class="container">
                        <div class="image-box">
                            <img src="../assets/images/wallpape/1.jpeg" alt="Image 1">
                        </div>
                        <div class="image-box">
                            <img src="../assets/images/wallpape/2.jpg" alt="Image 2">
                        </div>
                        <div class="image-box">
                            <img src="../assets/images/wallpape/3.jpeg" alt="Image 3">
                        </div>
                        <div class="image-box">
                            <img src="../assets/images/wallpape/4.jpeg" alt="Image 4">
                        </div>
                    </div> -->

                    <!-- Notification Tone Selection and Custom Upload -->
                    <!-- <div class="form-group">
                        <label for="notification_tone">Select notification tone:</label>
                        <ul class="list-group">
                            <li class="list-group-item" onclick="selectTone('ding')">Ding <audio src="path/to/ding.mp3" controls></audio></li>
                            <li class="list-group-item" onclick="selectTone('chime')">Chime <audio src="path/to/chime.mp3" controls></audio></li>
                            <li class="list-group-item" onclick="selectTone('alert')">Alert <audio src="path/to/alert.mp3" controls></audio></li>
                            <li class="list-group-item">
                                Custom <input type="file" name="custom_tone" id="customToneInput" style="display:none;">
                                <button type="button" onclick="document.getElementById('customToneInput').click()">Upload</button>
                            </li>
                        </ul>
                        <input type="hidden" name="notification_tone" id="notification_tone">
                    </div> -->

                    <!-- Submit Button -->
                    <!-- <button type="submit" class="btn btn-primary">Save Settings</button> -->
                <!-- </form> -->



            </div>
        </div>
    </div>


    <?php
    function echoToastScript($type, $message)
    {
        echo "<script type='text/javascript'>document.addEventListener('DOMContentLoaded', function() { toastr['$type']('$message'); });</script>";
    }

    ?>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <!-- Live Customizer end -->


    <?php
    include("./Public/Pages/Common/script.php");

    ?>

</body>

</html>