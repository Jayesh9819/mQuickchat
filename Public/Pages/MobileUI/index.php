<!DOCTYPE html>
<html lang="en">

<head>
    <?php include("./Public/Pages/Common/head.php"); ?>
    <?php
    include "./Public/Pages/Common/auth_user.php";
    function echoToastScript($type, $message)
    {
        echo "<script type='text/javascript'>document.addEventListener('DOMContentLoaded', function() { toastr['$type']('$message'); });</script>";
    }


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
    ?>

</head>

<body>

    <?php include("./Public/Pages/Common/loader.php"); ?>
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
                            $imagePath = $base_url."/uploads/" . $image; // Adjust the path as needed

                            // Check if essential elements are not null
                            if (!empty($title) && !empty($content) && !empty($image)) {
                                echo "
                <div class='single-hero-slide' style='background-image: url("{$imagePath}")'>
                    <!-- Background Shape-->
                    <div class='background-shape'>
                        <div class='circle2'></div>
                        <div class='circle3'></div>
                    </div>
                    <div class='slide-content h-100 d-flex align-items-end'>
                        <div class='container-fluid mb-3'>
                            <a class='post-catagory' href='#'>{$upage}</a>
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


    </div>

    <!-- Footer Nav -->
    <?php include("./Public/Pages/Common/footer.php"); ?>

    <?php include("./Public/Pages/Common/script.php"); ?>


</body>

</html>