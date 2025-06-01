<?php

switch ($segments[0]) {
    case 'create';
        include 'views/form.php';
        break;

    case 'update';
        if (isset($segments[1]) && is_numeric($segments[1])) {
            $id = (int)$segments[1];
            include 'views/form.php';
        }
        break;

    case 'vehicle':
        if (isset($segments[1]) && is_numeric($segments[1])) {
            $id = (int)$segments[1];
            include 'views/vehicle.php';
        }
        break;

    case '':
        // Home page
        include 'views/home.php';
        break;

    default:
        // Page 404
        http_response_code(404);
        include 'views/404.php';
        break;
}