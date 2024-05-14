<!DOCTYPE html>
<html lang="en">

<head>
  <?php include("./Public/Pages/Common/head.php"); ?>

</head>

<body>
  <!-- Preloader -->

  <!-- Header Area -->
  <?php include("./Public/Pages/Common/loader.php"); ?>
  <?php include("./Public/Pages/Common/header.php"); ?>
  <?php include("./Public/Pages/Common/sidebar.php"); ?>



  <div class="page-content-wrapper d-flex align-items-center justify-content-center">
    <div class="container">
      <!-- Error Content-->
      <div class="error-content text-center">
        <img class="mb-3" src="img/bg-img/404.png" alt="">
        <h3 class="mb-3">Oops! Page not found</h3>
        <p class="mb-4">We couldn't find any results for your search. <br> Try again.</p>
        <a class="btn btn-primary" href="./Home">Go Home</a>
      </div>
    </div>
  </div>

  <!-- Footer Nav -->
  
  <?php include("./Public/Pages/Common/script.php"); ?>

  <!-- All JavaScript Files-->
</body>

</html>