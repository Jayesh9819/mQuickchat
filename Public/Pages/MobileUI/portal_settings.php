<?php
session_start();
if (!isset($_SESSION['role'])) {
    header("Location: login.php"); // Redirect to login if no session exists
    exit();
}
?>
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

    // Display error message if available
    if (isset($_SESSION['login_error'])) {
        echo '<p class="error">' . htmlspecialchars($_SESSION['login_error']) . '</p>';
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

            $_SESSION['toast'] = ['type' => 'success', 'message' => 'Password changed successfully.'];
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
        $sharedDir = '/var/www/quickchat/data/www/share/profile/';
    
        // Ensure the shared directory exists
        if (!is_dir($sharedDir)) {
            mkdir($sharedDir, 0777, true);
        }
    
        $profilePicture = null;
        if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
            // Generate a unique file name
            $fileName = time() . '-' . basename($_FILES['profile_picture']['name']);
            $targetFilePath = $sharedDir . $fileName;
    
            // Move the uploaded file to the shared directory
            if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $targetFilePath)) {
                $profilePicture = $fileName;
            } else {
                echo "Error uploading file.";
                exit;
            }
        }
    
        // Update the database with the new profile picture file name
        $sql = "UPDATE user SET p_p = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$profilePicture, $userId]);
    
        // Update the session with the new profile picture
        unset($_SESSION['p_p']);
        $_SESSION['p_p'] = $profilePicture;
    
        // Set a success message and redirect the user
        $_SESSION['toast'] = ['type' => 'success', 'message' => 'Profile picture updated successfully'];
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    }
    
    function updateChatSetting($conn, $userId, $type, $path, $isActive)
    {
        $stmt = $conn->prepare("SELECT id FROM chatSettings WHERE user_id = ? AND type = ?");
        $stmt->bind_param("is", $userId, $type);
        $stmt->execute();
        $result = $stmt->get_result();
        $existing = $result->fetch_assoc();

        if ($existing) {
            $stmt = $conn->prepare("UPDATE chatSettings SET path = ?, status = ?, updated_at = NOW() WHERE id = ?");
            $stmt->bind_param("sii", $path, $isActive, $existing['id']);
            $stmt->execute();
        } else {
            $stmt = $conn->prepare("INSERT INTO chatSettings (user_id, type, path, status, created_at, updated_at) VALUES (?, ?, ?, ?, NOW(), NOW())");
            $stmt->bind_param("issi", $userId, $type, $path, $isActive);
            $stmt->execute();
        }
    }

    // Check if the wallpaper form has been submitted
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['wallpaper'])) {
        $userId = $_SESSION['user_id'];
        $isActive = 1;
        $path = $_POST['wallpaper'];

        if ($path === "custom" && isset($_FILES['custom_wallpaper']['tmp_name'])) {
            $uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/uploads/wallpapers/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            $fileName = time() . '-' . basename($_FILES['custom_wallpaper']['name']);
            $targetFilePath = $uploadDir . $fileName;
            if (move_uploaded_file($_FILES['custom_wallpaper']['tmp_name'], $targetFilePath)) {
                $path = $fileName;
            } else {
                $path = "upload_failed";
            }
        }
        updateChatSetting($conn, $userId, "image", $path, $isActive);
    }

    // Check if the notification tone form has been submitted
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['notification_tone'])) {
        $userId = $_SESSION['user_id'];
        $isActive = 1;
        $path = $_POST['notification_tone'];

        if ($path === "custom" && isset($_FILES['custom_tone']['tmp_name'])) {
            $uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/uploads/tone/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            $fileName = time() . '-' . basename($_FILES['custom_tone']['name']);
            $targetFilePath = $uploadDir . $fileName;
            if (move_uploaded_file($_FILES['custom_tone']['tmp_name'], $targetFilePath)) {
                $path = $fileName;
            } else {
                $path = "upload_failed";
            }
        }
        updateChatSetting($conn, $userId, "sound", $path, $isActive);
    }
    ?>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">

    <style>
        .settings-form {
            margin-bottom: 20px;
        }

        .image-box {
            flex-basis: calc(50% - 20px);
            margin: 10px;
            height: 0;
            padding-top: 25%;
            position: relative;
            background-color: #f0f0f0;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .image-box img {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .centered-content {
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            height: 100vh;
            text-align: center;
        }
    </style>
</head>

<body>
    <?php include("./Public/Pages/Common/header.php"); ?>
    <?php include("./Public/Pages/Common/sidebar.php"); ?>

    <div class="page-content-wrapper centered-content">
        <div class="container">
            <h3 class="mt-4 mb-3">Your Settings</h3>

            <!-- Time Zone Update Form -->
            <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" class="settings-form">
                <div class="form-group">
                    <label for="time_zone">Select your time zone:</label>
                    <select name="time_zone" id="time_zone" class="form-control">
                        <?php
                        $us_timezones = [
                            'America/New_York',
                            'America/Chicago',
                            'America/Denver',
                            'America/Phoenix',
                            'America/Los_Angeles',
                            'America/Anchorage',
                            'America/Honolulu'
                        ];
                        foreach ($us_timezones as $timezone) {
                            $selected = ($_SESSION['timezone'] ?? 'America/New_York') === $timezone ? ' selected' : '';
                            echo "<option value=\"$timezone\"$selected>$timezone</option>";
                        }
                        ?>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary btn-block">Update Time Zone</button>
            </form>

            <!-- Password Change Form -->
            <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" class="settings-form">
                <div class="form-group">
                    <input type="password" name="new_password" placeholder="New Password" required class="form-control">
                </div>
                <div class="form-group">
                    <input type="password" name="confirm_password" placeholder="Confirm New Password" required class="form-control">
                </div>
                <button type="submit" class="btn btn-primary btn-block">Change Password</button>
            </form>

            <!-- Profile Picture Update Form -->
            <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" enctype="multipart/form-data" class="settings-form">
                <div class="form-group">
                    <input type="file" name="profile_picture" required class="form-control-file">
                </div>
                <button type="submit" class="btn btn-primary btn-block">Upload Picture</button>
            </form>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <?php include("./Public/Pages/Common/script.php"); ?>
</body>

</html>
