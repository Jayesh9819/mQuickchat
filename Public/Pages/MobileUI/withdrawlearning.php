<!doctype html>
<html lang="en" dir="ltr">

<head>
    
    <?php
        error_reporting(E_ALL);  // Turn on all error reporting
        ini_set('display_errors', 1);  // Display errors to the browser
    
    include("./Public/Pages/Common/head.php");
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
    require_once './App/db/db_connect.php';

    // Initialize variables
    $username = $_SESSION['username'] ?? null;
    $withdrawAmount = $_SESSION['withdrawAmount'] ?? 0;
    $errorMessage = '';
    $successMessage = '';

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Retrieve and sanitize form data
        $amount = filter_input(INPUT_POST, 'amount', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
        $cashtag = filter_input(INPUT_POST, 'cashtag', FILTER_SANITIZE_STRING);

        // Validation
        if ($amount <= 0 || $amount > $withdrawAmount) {
            $errorMessage = 'Invalid withdrawal amount.';
        } else {
            // Prepare the insert statement
            $query = "INSERT INTO referralrecord (username, amount, type, cashtag,) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($query);

            // This assumes 'type' is a column in your table. Adjust if your table structure is different.
            $type = 'Withdrawal';

            if ($stmt) {
                $stmt->bind_param("sdss", $username, $amount, $type, $cashtag);

                if ($stmt->execute()) {
                    $successMessage = 'Withdrawal successful.';
                    echo $successMessage;
                    header("Location: ../../index.php/Refer_And_Earn");

                    // Reset amount in the session if needed or perform additional actions
                } else {
                    $errorMessage = 'Error processing your request.';
                    echo $errorMessage;
                }
                $stmt->close();
            } else {
                $errorMessage = 'Error preparing the database statement.';
                echo $errorMessage;

            }
        }
        $conn->close();
    }


    ?>

</head>

<body>
    <!-- loader Start -->
    <?php
    // include("./Public/Pages/Common/loader.php");

    ?>
    <!-- loader END -->

    <!-- sidebar  -->
    <?php include("./Public/Pages/Common/loader.php"); ?>

    <!-- Header Area-->
    <?php include("./Public/Pages/Common/header.php"); ?>


    <!-- Sidenav Black Overlay-->
    <?php include("./Public/Pages/Common/sidebar.php"); ?>




    <div class="page-content-wrapper">

            <div class="container mt-4">
                <div class="row">
                    <div class="col-md-6 offset-md-3">
                        <div class="card">
                            <div class="card-header">
                                <h4>Withdraw Funds</h4>
                            </div>
                            <div class="card-body">
                                <form action="" method="post">
                                    <div class="mb-3">
                                        <label for="username" class="form-label">Username</label>
                                        <input type="text" class="form-control" id="username" name="username" value="<?php echo htmlspecialchars($username); ?>" readonly>
                                    </div>
                                    <div class="mb-3">
                                        <label for="amount" class="form-label">Amount</label>
                                        <input type="number" class="form-control" id="amount" name="amount" value="<?php echo htmlspecialchars($withdrawAmount); ?>" min="0" step="0.01" required>

                                        <script>
                                            document.getElementById('amount').addEventListener('input', function() {
                                                var maxValue = parseFloat("<?php echo htmlspecialchars($withdrawAmount); ?>");
                                                var enteredValue = parseFloat(this.value);
                                                if (enteredValue > maxValue) {
                                                    this.value = maxValue.toString(); // Set the value to the maximum value without rounding
                                                }
                                                this.setAttribute('max', maxValue.toString()); // Update max attribute
                                            });
                                        </script>
                                    </div>
                                    <div class="mb-3">
                                        <label for="cashtag" class="form-label">Cash Tag</label>
                                        <input type="text" class="form-control" id="cashtag" name="cashtag" placeholder="Enter your cash tag" required>
                                    </div>
                                    <button type="submit" class="btn btn-primary">Submit Withdrawal</button>
                                </form>
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