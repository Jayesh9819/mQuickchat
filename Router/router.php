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
