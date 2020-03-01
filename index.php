<?php

session_start();

require __DIR__ . '/includes/utils.php';
require __DIR__ . '/includes/DBConnection.php';
require __DIR__ . '/includes/QueryManager.php';
require __DIR__ . '/includes/UserInfo.php';

define('PRODUCTION_PATH_PREFIX', '/~alfrol/prax4/');
define('PATH_PREFIX', '/');

$redirect = '';

// Handle the case when additional query parameters are passed.
if (strpos($_SERVER['REQUEST_URI'], '?')) {
    $url_parts = explode('?', $_SERVER['REQUEST_URI']);
    $redirect = $url_parts[0];
    QueryManager::handle_query($url_parts[1]);
} else {
    $redirect = $_SERVER['REQUEST_URI'];
}

$db = new DBConnection();
$ui = new UserInfo();

if (isset($_SESSION['loggedin']) && $_SESSION['loggedin']) {
    switch ($redirect) {
        case '':
        case PATH_PREFIX:
        case PATH_PREFIX . 'login':
        case PATH_PREFIX . 'register':
            header('Location: home');
            break;
        case PATH_PREFIX . 'home':
            require __DIR__ . '/pages/home.php';
            break;
        case PATH_PREFIX . 'profile':
            require __DIR__ . '/pages/profile.php';
            break;
        case PATH_PREFIX . 'profile-info':
            require __DIR__ . '/pages/profile_info.php';
            break;
        case PATH_PREFIX . 'new-switt':
            require __DIR__ . '/pages/new_switt.php';
            break;
        case PATH_PREFIX . 'search':
            require __DIR__ . '/pages/search.php';
            break;
        default:
            http_response_code(404);
            require __DIR__ . '/pages/404.php';
            break;
    }
} else {
    switch ($redirect) {
        case '':
        case PATH_PREFIX:
        case PATH_PREFIX . 'home':
        case PATH_PREFIX . 'profile':
        case PATH_PREFIX . 'profile-info':
        case PATH_PREFIX . 'new-switt':
        case PATH_PREFIX . 'search':
            header('Location: login');
            break;
        case PATH_PREFIX . 'login':
            require __DIR__ . '/pages/login.php';
            break;
        case PATH_PREFIX . 'register':
            require __DIR__ . '/pages/register.php';
            break;
        default:
            http_response_code(404);
            require __DIR__ . '/pages/404.php';
            break;
    }
}
