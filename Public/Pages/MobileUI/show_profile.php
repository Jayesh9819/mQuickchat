<!doctype html>
<html lang="en" dir="ltr">

<head>
    <?php
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


    ?>
    <style type="text/css">
        body {
            margin-top: 20px;
            color: #1a202c;
            text-align: left;
            background-color: #e2e8f0;
        }

        .main-body {
            padding: 15px;
        }

        .card {
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, .1), 0 1px 2px 0 rgba(0, 0, 0, .06);
        }

        .card {
            position: relative;
            display: flex;
            flex-direction: column;
            min-width: 0;
            word-wrap: break-word;
            background-color: #fff;
            background-clip: border-box;
            border: 0 solid rgba(0, 0, 0, .125);
            border-radius: .25rem;
        }

        .card-body {
            flex: 1 1 auto;
            min-height: 1px;
            padding: 1rem;
        }

        .gutters-sm {
            margin-right: -8px;
            margin-left: -8px;
        }

        .gutters-sm>.col,
        .gutters-sm>[class*=col-] {
            padding-right: 8px;
            padding-left: 8px;
        }

        .mb-3,
        .my-3 {
            margin-bottom: 1rem !important;
        }

        .bg-gray-300 {
            background-color: #e2e8f0;
        }

        .h-100 {
            height: 100% !important;
        }

        .shadow-none {
            box-shadow: none !important;
        }
    </style>


</head>

<body>
    <?php include("./Public/Pages/Common/loader.php"); 
    ?>
    <?php include("./Public/Pages/Common/header.php"); ?>
    <?php include("./Public/Pages/Common/sidebar.php"); ?>
    <?php
    include './App/db/db_connect.php';

    $id = $_SESSION['userid'];
    $sql = "Select * from user Where id=$id";
    $result = $conn->query($sql);
    $profile = $result->fetch_assoc();
    ?>
    <div class="page-content-wrapper">

        <main class="main-content">
            <div class="content-inner container-fluid pb-0" id="page_layout">
                <div class="row gutters-sm">
                    <div class="col-md-4 mb-3">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex flex-column align-items-center text-center">
                                    <img src="../uploads/profile/<?= !empty($chatWith['p_p']) ? $chatWith['p_p'] : '07.png' ?>" alt="User" class="rounded-circle" width="150">
                                    <div class="mt-3">
                                        <h4><?= $profile['username'] ?></h4>
                                        <p class="text-muted font-size-sm">Page:- <?= $profile['pagename'] ?></p>
                                        <a name="" id="" class="btn btn-primary" href="./Redeem" role="button">Reedem</a>

                                        <!-- <button class="btn btn-outline-primary" href>Message</button> -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="card mb-3">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-sm-3">
                                        <h6 class="mb-0">Full Name</h6>
                                    </div>
                                    <div class="col-sm-9 text-secondary">
                                        <?= $profile['name'] ?>
                                    </div>
                                </div>
                                <hr>
                                <div class="row">
                                    <div class="col-sm-3">
                                        <h6 class="mb-0">FaceBook</h6>
                                    </div>
                                    <div class="col-sm-9 text-secondary">
                                        <link rel="stylesheet" href="<?= $profile['Fb-link'] ?>">
                                    </div>
                                </div>
                                <hr>
                            </div>
                        </div>
                        <?php
                        $username = $profile['username'];
                        $sql = "SELECT * FROM transaction WHERE username='$username'";
                        $sumSql = "SELECT SUM(recharge) AS total_recharge, SUM(redeem) AS total_redeem, SUM(excess) AS total_excess, SUM(bonus) AS total_bonus, SUM(freepik) AS total_freepik FROM transaction WHERE username='$username'";
                        $result = $conn->query($sql);
                        $results = $result->fetch_all(MYSQLI_ASSOC);
                        $rest = $conn->query($sumSql);
                        $sumRes = $rest->fetch_assoc();

                        ?>
                        <div class="row gutters-sm">
                            <div class="col-sm-6 mb-3">
                                <div class="card mt-3">
                                    <ul class="list-group list-group-flush">
                                        <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                                            <h6 class="mb-0">Total Recharge</h6>
                                            <span class="text-secondary"><?= !empty($sumRes['total_recharge']) ? $sumRes['total_recharge'] : 'N/A' ?></span>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                                            <h6 class="mb-0">Total Redeem</h6>
                                            <span class="text-secondary"><?= !empty($sumRes['total_redeem']) ? $sumRes['total_redeem'] : 'N/A' ?></span>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                                            <h6 class="mb-0">Total Bonus</h6>
                                            <span class="text-secondary"><?= !empty($sumRes['total_bonus']) ? $sumRes['total_bonus'] : 'N/A' ?></span>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                                            <h6 class="mb-0">Total Free Play</h6>
                                            <span class="text-secondary"><?= !empty($sumRes['total_freepik']) ? $sumRes['total_freepik'] : 'N/A' ?></span>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <div class="col-sm-6 mb-3">
                                <div class="card mt-3">
                                    <ul class="list-group list-group-flush">
                                        <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                                            <h6 class="mb-0">Page</h6>
                                            <span class="text-secondary"><?= $profile['pagename'] ?></span>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                                            <h6 class="mb-0">Branch</h6>
                                            <span class="text-secondary"><?= $profile['branchname'] ?></span>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                                            <h6 class="mb-0">Created By</h6>
                                            <span class="text-secondary"><?= $profile['by'] ?></span>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                                            <h6 class="mb-0">Last Seen</h6>
                                            <span class="text-secondary"><?= $profile['last_seen'] ?></span>
                                        </li>
                                    </ul>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
                <!-- <div class="table-responsive">
                    <?php
                    if (empty($results)) {
                        echo "No records found";
                    } else {
                    ?>

                        <table id="example" class="table table-bordered table-hover display nowrap margin-top-10 w-p100">
                            <thead>
                                <tr>
                                    <th>Transaction Type</th>
                                    <th>Recharge</th>
                                    <th>Redeem</th>
                                    <th>Excess Amount</th>
                                    <th>Bonus Amount</th>
                                    <th>Free Play</th>
                                    <th>Platform Name</th>
                                    <th>Page Name</th>
                                    <th>CashApp Name</th>
                                    <th>Timestamp</th>
                                    <th>Username</th>
                                    <th>By</th>
                                    <th>Remark</th>

                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($results as $row) :

                                ?>

                                    <tr>
                                        <td class="<?= ($row['type'] === 'Debit') ? 'Debit' : 'Credit' ?>">
                                            <?= $row['type'] ?>
                                        </td>
                                        <td><?= $row['recharge'] ?></td>
                                        <td><?= $row['redeem'] ?></td>
                                        <td><?= $row['excess'] ?></td>
                                        <td><?= $row['bonus'] ?></td>
                                        <td><?= $row['freepik'] ?></td>

                                        <td><?= $row['platform'] ?></td>
                                        <td><?= $row['page'] ?></td>
                                        <td><?= $row['cashapp'] ?></td>

                                        <td><?= $row['created_at'] ?></td>
                                        <td><?= $row['username'] ?></td>
                                        <td><?= $row['by_u'] ?></td>
                                        <td><?= $row['remark'] ?></td>

                                    </tr>
                                <?php endforeach; ?>
                                <?php
                                if ($sumRes) {
                                    echo "<tfoot>";
                                    echo "<tr>";
                                    echo "<th colspan=''>Total:</th>";
                                    echo "<th>{$sumRes['total_recharge']}</th>";
                                    echo "<th>{$sumRes['total_redeem']}</th>";
                                    echo "<th>{$sumRes['total_excess']}</th>";
                                    echo "<th>{$sumRes['total_bonus']}</th>";
                                    echo "<th>{$sumRes['total_freepik']}</th>";
                                    echo "</tr>";
                                    echo "</tfoot>";
                                }
                                ?>

                            </tbody>
                        </table>
                    <?php } ?>
                </div> -->

            </div>
    </div>
    <script data-cfasync="false" src="/cdn-cgi/scripts/5c5dd728/cloudflare-static/email-decode.min.js"></script>
    <script src="https://code.jquery.com/jquery-1.10.2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/js/bootstrap.bundle.min.js"></script>
    <script type="text/javascript">

    </script>

    </div>
    </main>
    </div>

    <?php
    include("./Public/Pages/Common/footer.php");

    ?>
    <?php
    include("./Public/Pages/Common/script.php");
    ?>

</body>

</html>