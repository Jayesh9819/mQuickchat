<!DOCTYPE html>
<html lang="en">

<head>
    <?php include("./Public/Pages/Common/head.php"); ?>
    <?php
    include "./App/db/db_connect.php";
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

    <!-- Header Area-->
    <?php include("./Public/Pages/Common/header.php"); ?>


    <!-- Sidenav Black Overlay-->
    <?php include("./Public/Pages/Common/sidebar.php"); ?>

    <div class="page-content-wrapper">
        <?php
        $role = $_SESSION['role'];
        if ($role == 'User') {

            include './App/db/db_connect.php';
            $ubranch = $_SESSION['branch1'];
            $upage = $_SESSION['page1'];
            $query = "SELECT * FROM offers WHERE (branch='$ubranch' OR branch ='ALL') AND (page LIKE '%$upage%' OR page ='ALL') AND status=1";
            $result = mysqli_query($conn, $query);

            if (mysqli_num_rows($result) > 0) {
                echo '<div class="row">'; // Start the Bootstrap row
                while ($row = mysqli_fetch_assoc($result)) {
                    $title = htmlspecialchars($row["name"]); // Escape special characters to prevent XSS
                    $content = htmlspecialchars($row["content"]);
                    $image = htmlspecialchars($row["image"]);
                    $id = htmlspecialchars($row["id"]);
                    $branch = htmlspecialchars($row["branch"]);
                    $page = htmlspecialchars($row["page"]);
                    $imagePath = "../uploads/" . $image; // Adjust the path as needed

                    // Check if essential elements are not null
                    echo "
                <div class='col-md-4'>
                    <div class='card'>
                        <img src='{$imagePath}' class='card-img-top' alt='{$title}'>
                        <div class='card-body'>
                            <h5 class='card-title'>{$title}</h5>
                            <div class='content-collapse'>
                                <p class='card-text'>{$content}</p>
                            </div>
                            <button class='btn btn-primary' onclick='expandText(this)'>More</button>
                        </div>
                    </div>
                </div>
                ";
                }
                echo '</div>'; // End the Bootstrap row
                echo "
        <script>
        function expandText(button) {
            var content = button.previousElementSibling;
            if (button.innerText === 'More') {
                content.style.maxHeight = 'none';
                button.innerText = 'Less';
            } else {
                content.style.maxHeight = '4.5em';
                button.innerText = 'More';
            }
        }
        </script>
        ";
            } else {
                echo "No results found.";
            }
        }
        ?>






    </div>

    <!-- Footer Nav -->
    <?php include("./Public/Pages/Common/footer.php"); ?>

    <?php include("./Public/Pages/Common/script.php"); ?>


</body>

</html>