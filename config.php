<?php


define('DB_HOST', '127.0.0.1');   
define('DB_PORT', '3306');        
define('DB_NAME', 'mobile_mart_db');
define('DB_USER', 'root');
define('DB_PASS', '');           


define('BASE_URL', '/MobileMart');


error_reporting(E_ALL);
ini_set('display_errors', '1');


if (session_status() === PHP_SESSION_NONE) {
    session_name('MMSESSID');
    session_start();
}


if (!isset($_COOKIE['mm_cart_sid']) || $_COOKIE['mm_cart_sid'] === '') {

 
    try {
        $sid = bin2hex(random_bytes(8));
    } catch (Exception $e) {
        $sid = bin2hex(openssl_random_pseudo_bytes(8));
    }


    setcookie('mm_cart_sid', $sid, time() + (30 * 24 * 3600), '/');
    $_COOKIE['mm_cart_sid'] = $sid;
}


function base_url($path = '') {
    if ($path === '') return BASE_URL;
    if ($path[0] !== '/') $path = '/' . $path;
    return BASE_URL . $path;
}

function e($str) {
    return htmlspecialchars((string)$str, ENT_QUOTES, 'UTF-8');
}

function redirect_to($url) {
    header('Location: ' . $url);
    exit;
}

function is_post() {
    return ($_SERVER['REQUEST_METHOD'] ?? '') === 'POST';
}

function is_get() {
    return ($_SERVER['REQUEST_METHOD'] ?? '') === 'GET';
}

function require_post() {
    if (!is_post()) {
        http_response_code(405);
        echo 'Method Not Allowed';
        exit;
    }
}

function set_flash($key, $msg) {
    $_SESSION['flash'][$key] = $msg;
}

function get_flash($key) {
    if (isset($_SESSION['flash'][$key])) {
        $msg = $_SESSION['flash'][$key];
        unset($_SESSION['flash'][$key]);
        return $msg;
    }
    return '';
}

function current_user() {
    return $_SESSION['user'] ?? null;
}

function require_login() {
    if (!current_user()) {
        set_flash('error', 'Please login first.');
        redirect_to(base_url('/index.php?page=login'));
    }
}

function require_role($role) {
    $u = current_user();
    if (!$u || ($u['role'] ?? '') !== $role) {
        http_response_code(403);
        echo 'Forbidden';
        exit;
    }
}
