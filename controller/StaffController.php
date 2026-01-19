<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../model/Order.php';
require_once __DIR__ . '/../model/Product.php';
require_once __DIR__ . '/../model/User.php';
require_once __DIR__ . '/../model/StaffSalary.php';

class StaffController {
    public static function dashboard() {
        require_login();
        require_role('staff');

        // Staff can search products (view only)
        $q = trim($_GET['q'] ?? '');
        $brand = trim($_GET['brand'] ?? '');
        $sort = trim($_GET['sort'] ?? '');

        $products = Product::getAll($q,$brand,$sort);
        $brands = Product::brands();

        $u = current_user();
        $orders = Order::staffOrders($u['phone'], trim($_GET['oq'] ?? ''));
        $salary = StaffSalary::byStaff($u['phone']);

        include __DIR__ . '/../view/staff_dashboard.php';
    }
}
