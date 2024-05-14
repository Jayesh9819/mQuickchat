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
    
    $depositID = $_SESSION['username'];

    $platformOptions = "<option value=''>Select Platform</option>";
    $result = $conn->query("SELECT name FROM platform where status =1");
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $platformOptions .= "<option value='" . htmlspecialchars($row['name']) . "'>" . htmlspecialchars($row['name']) . "</option>";
        }
    }
    $platformOptions .= "<option value='other'>Other</option>";


    ?>
</head>

<body>

    <?php include("./Public/Pages/Common/loader.php"); ?>

    <!-- Header Area-->
    <?php include("./Public/Pages/Common/header.php"); ?>


    <!-- Sidenav Black Overlay-->
    <?php include("./Public/Pages/Common/sidebar.php"); ?>

    <div class="page-content-wrapper">

        <div class="container mt-5">
            <h2>Enter the Details Correctly</h2>
            <form action="../App/Logic/creation.php?action=CashOut" method="post">
                <div class="form-group">
                    <label for="username">Enter the User Name</label>
                    <input type="text" class="form-control" id="username" name="username" value="<?php echo htmlspecialchars($depositID); ?>" readonly>
                </div>
                <div class="form-group">
                    <label for="reedemamount">Redeem Amount</label>
                    <input type="number" class="form-control" id="reedemamount" name="reedemamount" placeholder="Enter the Redeem Amount">
                </div>
                <div class="form-group">
                    <label for="platformname">Platform Name</label>
                    <select class="form-control" id="platformname" name="platformname" onchange="showOtherField(this, 'platformname-other')">
                        <?php echo $platformOptions; ?>
                    </select>
                    <input type="text" class="form-control mt-2" id="platformname-other" name="platformname_other" style="display:none;" placeholder="Enter Platform Name">
                </div>
                <div class="form-group">
                    <label for="ctag">Cash Tag</label>
                    <input type="text" class="form-control" id="ctag" name="ctag" placeholder="Enter the Cash Tag">
                </div>
                <div class="form-group">
                    <label for="ttype">Tip Type</label>
                    <select class="form-control" id="ttype" name="ttype">
                        <option value="">Select Type</option>
                        <option value="no_tip">No Tip</option>
                        <option value="deduct_redeem">Deduct From Redeem Amount</option>
                        <option value="deduct_platform">Deduct From Platform</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="tip">Tip</label>
                    <input type="number" class="form-control" id="tip" name="tip" placeholder="Enter the Tip Amount">
                </div>
                <div class="form-group">
                    <label for="remark">Remark</label>
                    <input type="text" class="form-control" id="remark" name="remark" placeholder="Enter the Remark">
                </div>
                <button type="submit" class="btn btn-primary">Submit</button>
                <button type="button" class="btn btn-secondary">Cancel</button>
            </form>
        </div>

    </div>

    <!-- Footer Nav -->
    <?php include("./Public/Pages/Common/footer.php"); ?>

    <?php include("./Public/Pages/Common/script.php"); ?>


</body>

</html>