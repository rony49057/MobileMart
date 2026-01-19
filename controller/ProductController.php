<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../model/Product.php';
require_once __DIR__ . '/../model/Cart.php';

class ProductController {
    public static function home() {
        // Public home: show products + login/cart buttons
        $q = trim($_GET['q'] ?? '');
        $brand = trim($_GET['brand'] ?? '');
        $sort = trim($_GET['sort'] ?? '');

        try {
            $products = Product::getAll($q, $brand, $sort);
            $brands = Product::brands();
        } catch (Exception $e) {
            $products = [];
            $brands = [];
            set_flash('error', 'Product load error: ' . $e->getMessage());
        }

        include __DIR__ . '/../view/home.php';
    }

    public static function customerDashboard() {
        // Same as home, but if logged in customer then show logout/profile
        $q = trim($_GET['q'] ?? '');
        $brand = trim($_GET['brand'] ?? '');
        $sort = trim($_GET['sort'] ?? '');

        try {
            $products = Product::getAll($q, $brand, $sort);
            $brands = Product::brands();
        } catch (Exception $e) {
            $products = [];
            $brands = [];
            set_flash('error', 'Product load error: ' . $e->getMessage());
        }

        include __DIR__ . '/../view/customer_dashboard.php';
    }

    public static function details() {
        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 0) {
            redirect_to(base_url('/index.php'));
        }
        $product = Product::find($id);
        if (!$product) {
            set_flash('error', 'Product not found.');
            redirect_to(base_url('/index.php'));
        }
        include __DIR__ . '/../view/product_details.php';
    }
}
