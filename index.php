<?php

require_once 'src/config.php';

$requestUri = $_SERVER['REQUEST_URI'];
$path = parse_url($requestUri, PHP_URL_PATH);
$path = trim($path, '/');

$segments = explode('/', $path);

if (!isLoggedIn() || $segments[0] == 'logout') {
    switch ($segments[0]) {
        case'login':
            include 'src/login.php';
            break;
        case'register':
            include 'src/register.php';
            break;
        case 'logout':
            include 'src/logout.php';
            break;
        default:
            header('Location: /login/');
            break;
    }
    exit();
}

include 'views/header.php';

include 'src/router.php';


include 'views/footer.php';
