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
    <style>
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.4);
            padding-top: 60px;
        }

        .modal-content {
            background-color: #fefefe;
            margin: 5% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 30%;
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }

        .close:hover,
        .close:focus {
            color: black;
            cursor: pointer;
        }


        .button-82-pushable {
            position: relative;
            border: none;
            background: transparent;
            padding: 0;
            cursor: pointer;
            outline-offset: 4px;
            transition: filter 250ms;
            user-select: none;
            -webkit-user-select: none;
            touch-action: manipulation;
        }

        .button-82-shadow {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            border-radius: 12px;
            background: hsl(120, 100%, 20%, 0.25);
            /* Light green background with 25% opacity */
            will-change: transform;
            transform: translateY(2px);
            transition: transform 600ms cubic-bezier(.3, .7, .4, 1);
        }

        .button-82-edge {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            border-radius: 12px;
            background: linear-gradient(to left, hsl(120, 100%, 25%) 0%, hsl(120, 100%, 40%) 8%, hsl(120, 100%, 40%) 92%, hsl(120, 100%, 25%) 100%);
            /* Light green gradient */
        }

        .button-82-front {
            display: block;
            position: relative;
            padding: 12px 27px;
            border-radius: 12px;
            font-size: 1.1rem;
            color: white;
            background: hsl(120, 100%, 35%);
            /* Light green background */
            will-change: transform;
            transform: translateY(-4px);
            transition: transform 600ms cubic-bezier(.3, .7, .4, 1);
        }

        @media (min-width: 768px) {
            .button-82-front {
                font-size: 1.25rem;
                padding: 12px 42px;
            }
        }

        .button-82-pushable:hover {
            filter: brightness(110%);
            -webkit-filter: brightness(110%);
        }

        .button-82-pushable:hover .button-82-front {
            transform: translateY(-6px);
            transition: transform 250ms cubic-bezier(.3, .7, .4, 1.5);
        }

        .button-82-pushable:active .button-82-front {
            transform: translateY(-2px);
            transition: transform 34ms;
        }

        .button-82-pushable:hover .button-82-shadow {
            transform: translateY(4px);
            transition: transform 250ms cubic-bezier(.3, .7, .4, 1.5);
        }

        .button-82-pushable:active .button-82-shadow {
            transform: translateY(1px);
            transition: transform 34ms;
        }

        .button-82-pushable:focus:not(:focus-visible) {
            outline: none;
        }
    </style>
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

        include './App/db/db_connect.php';
        $ubranch = $_SESSION['branch1'];
        $upage = $_SESSION['page1'];
        $query = "SELECT * FROM offers WHERE (branch='$ubranch' AND (page LIKE '%$upage%' OR page ='ALL')) OR branch ='ALL' AND status=1";
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
                $imagePath = $base_url . "/uploads/" . $image; // Adjust the path as needed

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

        ?>






    </div>
<script>        function expandText(button) {
            var content = button.previousElementSibling;
            if (button.innerText === 'More') {
                content.style.maxHeight = 'none';
                button.innerText = 'Less';
            } else {
                content.style.maxHeight = '4.5em';
                button.innerText = 'More';
            }
        }

        function viewImageFullscreen(img) {
            var fullscreenDiv = document.createElement('div');
            fullscreenDiv.style.position = 'fixed';
            fullscreenDiv.style.top = '0';
            fullscreenDiv.style.left = '0';
            fullscreenDiv.style.width = '100vw';
            fullscreenDiv.style.height = '100vh';
            fullscreenDiv.style.backgroundColor = 'rgba(0,0,0,0.8)';
            fullscreenDiv.style.zIndex = '9999';
            fullscreenDiv.style.display = 'flex';
            fullscreenDiv.style.justifyContent = 'center';
            fullscreenDiv.style.alignItems = 'center';
            fullscreenDiv.style.cursor = 'zoom-out';
            fullscreenDiv.onclick = function() {
                document.body.removeChild(this);
            }

            var cloneImg = img.cloneNode();
            cloneImg.style.maxWidth = '90vw';
            cloneImg.style.maxHeight = '90vh';
            fullscreenDiv.appendChild(cloneImg);
            document.body.appendChild(fullscreenDiv);
        }
</script>
    <!-- Footer Nav -->
    <?php include("./Public/Pages/Common/footer.php"); ?>

    <?php include("./Public/Pages/Common/script.php"); ?>


</body>

</html>