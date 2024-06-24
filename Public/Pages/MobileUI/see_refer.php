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

            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="mb-0">Referal List</h4>
                            <h6 class="box-subtitle"></h6>

                        </div>
                        <?php
                        include './App/db/db_connect.php';
                        $username=$_SESSION['username'];
                        $sql = "SELECT * FROM referrecord where username='$username' ";

                        $result = $conn->query($sql);

                        // Check if there are results

                        if ($result->num_rows > 0) {
                        ?>
                            <div class="card-body">
                                <div class="custom-table-effect table-responsive border rounded">
                                    <table class="table mb-0" id="example">
                                        <thead>
                                            <tr class="bg-white">
                                                <th scope="col">ID</th>
                                                <th scope="col">From User</th>
                                                <th scope="col">Amount</th>
                                                <th scope="col">Earn From </th>

                                                <th scope="col">Time</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php while ($row = $result->fetch_assoc()) : ?>
                                                <tr>
                                                    <td><?= htmlspecialchars($row['id']); ?></td>
                                                    <td><?= htmlspecialchars($row['byname']); ?></td>
                                                    <td><?= htmlspecialchars($row['amount']); ?></td>
                                                    <td><?= htmlspecialchars($row['type']); ?></td>
                                                    <td><?= htmlspecialchars($row['created_at']); ?></td>
                                                </tr>
                                            <?php endwhile; ?>
                                        </tbody>
                                    </table>
                                <?php
                            } else {
                                echo "<p>No referral records found.</p>";
                            }
                            // Close connection
                            $conn->close();
                                ?>
                                </div>
                            </div>
                    </div>
                </div>

            </div>
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