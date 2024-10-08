<?php
// sjd
$uri = parse_url($_SERVER['REQUEST_URI'])['path'];
include './Router/initialize.php';


if ($uri == $firstparam || $uri == $secondparam) {
    echo '<script type="text/JavaScript"> 
    window.location.replace("./index.php/Home");
    </script>';
    die();
} else {

    $prefix = $thirdparam;
    $root = $fourthparam;
    $routes = [
        $prefix . $root . '/Home'                        => './Public/Pages/MobileUI/index.php',
        $prefix . $root . '/Login'                        => './Public/Pages/MobileUI/Login.php',
        $prefix . $root . '/Redeem'                        => './Public/Pages/MobileUI/Redeem.php',
        $prefix . $root . '/LogOut'                        => './Public/Pages/Common/destroy_session.php',
        $prefix . $root . '/Offers'                        => './Public/Pages/MobileUI/offers.php',
        $prefix . $root . '/Profile'                        => './Public/Pages/MobileUI/show_profile.php',
        $prefix . $root . '/Refer'                        => './Public/Pages/MobileUI/referandearn.php',
        $prefix . $root . '/Setting'                        => './Public/Pages/MobileUI/portal_settings.php',
        $prefix . $root . '/Withdraw_Earning'                         => './Public/Pages/MobileUI/withdrawlearning.php',
        $prefix . $root . '/See_Refer'                         => './Public/Pages/MobileUI/see_refer.php',



        //Chat Routes
        $prefix . $root . '/Chat'                        => './Public/Pages/Chat/index.php',
        $prefix . $root . '/Chat_Screen'                        => './Public/Pages/Chat/home.php',


    ];

    function routeToController($uri, $routes)
    {
        if (array_key_exists($uri, $routes)) {
            require $routes[$uri];
        } else {
            abort();
        }
    }

    function abort()
    {

        require  "./Public/Pages/Error/404.php";
        die();
    }
    routeToController($uri, $routes);
}
