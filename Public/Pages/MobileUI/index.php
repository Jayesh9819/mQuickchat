<!DOCTYPE html>
<html lang="en">

<head>
    <?php include("./Public/Pages/Common/head.php");?>
</head>

<body>

    <?php include("./Public/Pages/Common/loader.php");?>

    <!-- Header Area-->
    <?php include("./Public/Pages/Common/header.php");?>


    <!-- Sidenav Black Overlay-->
    <?php include("./Public/Pages/Common/sidebar.php");?>

    <div class="page-content-wrapper">
        <!-- News Today Wrapper-->
        <div class="news-today-wrapper">
            <div class="container">
                <div class="d-flex align-items-center justify-content-between">
                    <h5 class="mb-3 pl-1 newsten-title">News Today</h5>
                    <p class="mb-3 line-height-1" id="dashboardDate2"></p>
                </div>

                <!-- Hero Slides-->
                <div class="hero-slides owl-carousel">

                    <!-- Single Hero Slide-->
                    <div class="single-hero-slide" style="background-image: url('img/bg-img/4.jpg')">
                        <!-- Background Shape-->
                        <div class="background-shape">
                            <div class="circle2"></div>
                            <div class="circle3"></div>
                        </div>
                        <div class="slide-content h-100 d-flex align-items-end">
                            <div class="container-fluid mb-3">
                                <div class="video-icon">
                                    <i class="lni lni-play"></i>
                                </div>
                                <a class="bookmark-post" href="#"><i class="lni lni-bookmark"></i></a>
                                <a class="post-catagory" href="catagory.html">Politics</a>
                                <a class="post-title d-block" href="single.html">Massive riots in the city to establish rule of law</a>
                                <div class="post-meta d-flex align-items-center">
                                    <a href="#"><i class="mr-1 lni lni-user"></i>Mayaj</a>
                                    <a href="#"><i class="mr-1 lni lni-calendar"></i>26 March</a>
                                    <span><i class="mr-1 lni lni-bar-chart"></i>4 min read</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Single Hero Slide-->
                    <div class="single-hero-slide" style="background-image: url('img/bg-img/2.jpg')">
                        <!-- Background Shape-->
                        <div class="background-shape">
                            <div class="circle2"></div>
                            <div class="circle3"></div>
                        </div>
                        <div class="slide-content h-100 d-flex align-items-end">
                            <div class="container-fluid mb-3">
                                <a class="bookmark-post" href="#"><i class="lni lni-bookmark"></i></a>
                                <a class="post-catagory" href="catagory.html">Fashion</a>
                                <a class="post-title d-block" href="single.html">Fashion 2020: How to get the golden skin on the
                                    outside</a>
                                <div class="post-meta d-flex align-items-center">
                                    <a href="#"><i class="mr-1 lni lni-user"></i>Lim</a>
                                    <a href="#"><i class="mr-1 lni lni-calendar"></i>23 March</a>
                                    <span><i class="mr-1 lni lni-bar-chart"></i>9 min read</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Single Hero Slide-->
                    <div class="single-hero-slide" style="background-image: url('img/bg-img/5.jpg')">
                        <!-- Background Shape-->
                        <div class="background-shape">
                            <div class="circle2"></div>
                            <div class="circle3"></div>
                        </div>
                        <div class="slide-content h-100 d-flex align-items-end">
                            <div class="container-fluid mb-3">
                                <a class="bookmark-post" href="#"><i class="lni lni-bookmark"></i></a>
                                <a class="post-catagory" href="catagory.html">Health</a>
                                <a class="post-title d-block" href="single.html">Loses over 30kg on keto diet and one meal a day</a>
                                <div class="post-meta d-flex align-items-center">
                                    <a href="#"><i class="mr-1 lni lni-user"></i>Nazrul</a>
                                    <a href="#"><i class="mr-1 lni lni-calendar"></i>21 March</a>
                                    <span><i class="mr-1 lni lni-bar-chart"></i>3 min read</span>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>


    </div>

    <!-- Footer Nav -->
    <?php include("./Public/Pages/Common/footer.php");?>

    <?php include("./Public/Pages/Common/script.php");?>


</body>

</html>