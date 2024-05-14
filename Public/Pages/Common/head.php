<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Origin, Content-Type, Accept, Authorization");

include "./App/helper/webset.php";
$base_url='https:quickchat.biz';
?>


<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<meta name="description" content="">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<title><?php echo $settings['name']; ?></title>
<link rel="icon" href="<?php echo $settings['icon']; ?>">
<link rel="stylesheet" href="../assets/style.css">
<link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
