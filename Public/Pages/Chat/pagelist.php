<!doctype html>
<html lang="en" dir="ltr">

<head>
    <?php
    include("./Public/Pages/Common/header.php");
    include "./Public/Pages/Common/auth_user.php";

    // Function to echo the script for toastr
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
    <style>
        /* custom.css */

        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f4;
            color: #333;
        }

        .main-content {
            padding: 20px;
        }

        .error {
            color: red;
            font-weight: bold;
        }

        .list-group-item {
            border-radius: 0;
            border-left: 3px solid #007bff;
        }

        .list-group-item-action:hover,
        .list-group-item-action:focus {
            background-color: #f8f9fa;
            color: #007bff;
            text-decoration: none;
        }

        .container {
            background-color: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        /* Additional styling can go here */
    </style>

</head>

<body class="  ">
    <!-- loader Start -->
    <?php
    // include("./Public/Pages/Common/loader.php");

    ?>
    <!-- loader END -->

    <!-- sidebar  -->
    <?php
    include("./Public/Pages/Common/sidebar.php");

    ?>

    <main class="main-content">
        <?php
        include("./Public/Pages/Common/main_content.php");
        ?>


        <div class="content-inner container-fluid pb-0" id="page_layout">
            <br>
            <br>
            <br>
            <div class="container my-4">
                <div class="row">
                    <div class="col-md-4">
                        <!-- Dynamic List Group -->
                        <div class="list-group" id="dynamicPageList" role="tablist">
                            <?php
                            include "./App/db/db_connect.php";

                            // Ensure session has started
                            if (session_status() === PHP_SESSION_NONE) {
                                session_start();
                            }

                            // Check if session variables are set
                            $branchName = isset($_SESSION['branch']) ? $conn->real_escape_string($_SESSION['branch']) : '';
                            $userRole = isset($_SESSION['role']) ? $_SESSION['role'] : '';

                            // Initialize pageQuery
                            $pageQuery = "";

                            // Fetch the branch ID by the branch name if not admin
                            if ($userRole !== 'Admin' && !empty($branchName)) {
                                $branchIdQuery = "SELECT bid FROM branch WHERE name = '$branchName'";
                                $branchIdResult = $conn->query($branchIdQuery);

                                if ($branchIdResult->num_rows > 0) {
                                    $branchRow = $branchIdResult->fetch_assoc();
                                    $branchId = $branchRow['bid'];
                                    $pageQuery = "SELECT * FROM page WHERE bid = $branchId";
                                } else {
                                    echo "Branch not found";
                                }
                            } elseif ($userRole === 'Admin') {
                                // If user is Admin, fetch all pages
                                $pageQuery = "SELECT * FROM page";
                            }

                            // Execute pageQuery if it's set
                            if (!empty($pageQuery)) {
                                $pageResult = $conn->query($pageQuery);

                                if ($pageResult && $pageResult->num_rows > 0) {
                                    while ($row = $pageResult->fetch_assoc()) {
                                        echo '<a class="list-group-item list-group-item-action" role="tab" onclick="location.href=\'Bulk_Chat?user=' . $row["name"] . '\'">' . $row["name"] . '</a>';
                                    }
                                } else {
                                    echo "No pages found";
                                }
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>


            <!-- Bootstrap JS and dependencies -->
            <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
            <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>




        </div>






        <?
        include("./Public/Pages/Common/footer.php");
       
        ?>

    </main>
    <!-- Wrapper End-->
    <!-- Live Customizer start -->
    <!-- Setting offcanvas start here -->
    <?php
    include("./Public/Pages/Common/theme_custom.php");

    ?>

    <!-- Settings sidebar end here -->

    <?php
    include("./Public/Pages/Common/settings_link.php");

    ?>
    <!-- Live Customizer end -->

    <!-- Library Bundle Script -->
    <?php
    include("./Public/Pages/Common/scripts.php");

    ?>

</body>

</html>