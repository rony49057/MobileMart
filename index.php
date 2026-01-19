<?php
require_once __DIR__ . '/config.php';

// Controllers
require_once __DIR__ . '/controller/AuthController.php';
require_once __DIR__ . '/controller/ProductController.php';
require_once __DIR__ . '/controller/CartController.php';
require_once __DIR__ . '/controller/OrderController.php';
require_once __DIR__ . '/controller/AdminController.php';
require_once __DIR__ . '/controller/StaffController.php';
require_once __DIR__ . '/controller/AjaxController.php';

$page = $_GET['page'] ?? 'home';

// Very simple routing (GET/POST only)
switch ($page) {
    case 'home':
        ProductController::home();
        break;

    case 'login':
        AuthController::login();
        break;

    case 'register':
        AuthController::register();
        break;

    case 'forgot':
        AuthController::forgotPassword();
        break;

    case 'logout':
        AuthController::logout();
        break;

    case 'profile':
        AuthController::profile();
        break;

    case 'change_password':
        AuthController::changePassword();
        break;

    // Customer
    case 'customer_dashboard':
        ProductController::customerDashboard();
        break;

    case 'product':
        ProductController::details();
        break;

    case 'cart':
        CartController::cart();
        break;

    case 'confirm_order':
        OrderController::confirm();
        break;

    case 'my_orders':
        OrderController::myOrders();
        break;

    // Admin
    case 'admin_dashboard':
        AdminController::dashboard();
        break;

    case 'admin_products':
        AdminController::products();
        break;

    case 'admin_users':
        AdminController::users();
        break;

    case 'admin_orders':
        AdminController::orders();
        break;

    case 'admin_staff_salary':
        AdminController::staffSalary();
        break;

    // Staff
    case 'staff_dashboard':
        StaffController::dashboard();
        break;

    // AJAX JSON (ONLY live grid search)
    case 'ajax_search_products':
        AjaxController::searchProducts();
        break;

    case 'ajax_add_to_cart':
        AjaxController::addToCart();
        break;

    case 'ajax_remove_from_cart':
        AjaxController::removeFromCart();
        break;

    case 'ajax_update_cart_qty':
        AjaxController::updateCartQty();
        break;

    case 'ajax_assign_order_staff':
        AjaxController::assignOrderStaff();
        break;

    case 'ajax_update_order_status':
        AjaxController::updateOrderStatus();
        break;

    default:
        ProductController::home();
        break;
}
