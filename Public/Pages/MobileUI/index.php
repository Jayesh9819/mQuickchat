<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php include("./Public/Pages/Common/head.php"); ?>
    <?php include("./Public/Pages/Common/auth_user.php"); ?>

    <?php
    function echoToastScript($type, $message)
    {
        echo "<script type='text/javascript'>document.addEventListener('DOMContentLoaded', function() { toastr['$type']('$message'); });</script>";
    }

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

    $username = $_SESSION['username'];
    $query = "SELECT * FROM transaction WHERE username = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $results = $stmt->get_result();
    // $transaction = $result->fetch_assoc();
    // print_r($stmt);
    // print_r($transaction);
    ?>
</head>

<body onload="sendSessionDataToFlutter();">
<script type="text/javascript">
        // Embed the user ID in a JavaScript variable
        var userId = "<?php echo $_SESSION['userid']; ?>"; 

        // Function to send the user ID to the SwiftUI app
        function sendUserIdToApp() {
            if (window.webkit && window.webkit.messageHandlers.getUserId) {
                window.webkit.messageHandlers.getUserId.postMessage(userId);
            }
        }

        // Call the function to send the user ID
        sendUserIdToApp();
    </script>


    <?php //include("./Public/Pages/Common/loader.php"); 
    ?>
    <?php include("./Public/Pages/Common/header.php"); ?>
    <?php include("./Public/Pages/Common/sidebar.php"); ?>

    <div class="page-content-wrapper">
        <!-- News Today Wrapper-->
        <div class="news-today-wrapper">
            <div class="container">
                <?php
                $role = $_SESSION['role'];
                if ($role == 'User') {

                    include './App/db/db_connect.php';
                    $ubranch = $_SESSION['branch1'];
                    $upage = $_SESSION['page1'];
                    $query = "SELECT * FROM offers WHERE (branch='$ubranch' AND (page LIKE '%$upage%' OR page ='ALL')) OR branch ='ALL' AND status=1";
                    $result = mysqli_query($conn, $query);

                    if (mysqli_num_rows($result) > 0) {
                        echo '<div class="hero-slides owl-carousel">'; // Start the carousel
                        while ($row = mysqli_fetch_assoc($result)) {
                            $title = htmlspecialchars($row["name"]); // Escape special characters to prevent XSS
                            $content = htmlspecialchars($row["content"]);
                            $image = htmlspecialchars($row["image"]);
                            $id = htmlspecialchars($row["id"]);
                            $branch = htmlspecialchars($row["branch"]);
                            $page = htmlspecialchars($row["page"]);
                            $imagePath = $base_url . '/uploads/' . $image; // Adjust the path as needed

                            // Check if essential elements are not null
                            if (!empty($title) && !empty($content) && !empty($image)) {
                                echo "
                                <div class='single-hero-slide' style='background-image: url(\"{$imagePath}\")'>
                                    <!-- Background Shape-->
                                    <div class='background-shape'>
                                        <div class='circle2'></div>
                                        <div class='circle3'></div>
                                    </div>
                                    <div class='slide-content h-100 d-flex align-items-end'>
                                        <div class='container-fluid mb-3'>
                                            <a class='post-catagory' href='#'>{$branch}</a>
                                            <a class='post-title d-block' href='./Offers'>{$title}</a>
                                            <div class='post-meta d-flex align-items-center'>
                                                <a href='#'><i class='mr-1 lni lni-user'></i>User</a>
                                                <a href='#'><i class='mr-1 lni lni-calendar'></i>Today</a>
                                                <span><i class='mr-1 lni lni-bar-chart'></i>Read More</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                ";
                            }
                        }
                        echo '</div>'; // End the carousel
                    } else {
                        echo "No results found.";
                    }
                }
                ?>
            </div>
        </div>
        <div class="container">
            <?php
            if ($results) {
                while ($transaction = $results->fetch_assoc()) : ?>
                    <div class="card mb-3">
                        <div class="card-body">
                            <h5 class="card-title">Redemption Status</h5>
                            <?php
                            $approvalStatus = $transaction['approval_status'];
                            $cashoutStatus = $transaction['cashout_status'];
                            $redeemStatus = $transaction['redeem_status'];
                            $reason = ($transaction['Reject_msg']);

                            if ($approvalStatus == 0) {
                                echo "<p class='card-text text-warning'>Approval is pending.</p>";
                            } elseif ($approvalStatus == 1) {
                                if ($cashoutStatus == 0 || $redeemStatus == 0) {
                                    echo "<p class='card-text text-warning'>Redemption is pending.</p>";
                                } else {
                                    echo "<p class='card-text text-success'>Redemption is complete.</p>";
                                }
                            } elseif ($approvalStatus == 2) {
                                echo "<p class='card-text text-danger'>Redemption rejected. Reason: $reason</p>";
                            }
                            ?>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php
            } else {
                echo
                '<div class="card">
                <div class="card-body">
                    <h5 class="card-title">No Redemption</h5>
                    <p class="card-text">You have no redemption requests.</p>
                </div>
            </div>';
            }

            ?>
        </div>
    </div>

    </div>

    <!-- Footer Nav -->
    <?php include("./Public/Pages/Common/footer.php"); ?>
    <script>
        function sendSessionDataToFlutter() {
            var userId = "<?php echo $_SESSION['userid']; ?>"; 
            if (window.Flutter) {
                Flutter.postMessage(userId);
            }
        }
        function sendUserIdToApp() {
            if (window.webkit && window.webkit.messageHandlers.getUserId) {
                window.webkit.messageHandlers.getUserId.postMessage(userId);
                console.log("Sende to App");
            }
        }

        // Call the function to send the user ID
        sendUserIdToApp();
    </script>
    <?php include("./Public/Pages/Common/script.php"); ?>

</body>

</html>