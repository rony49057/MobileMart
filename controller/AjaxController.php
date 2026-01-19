<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../model/Product.php';
require_once __DIR__ . '/../model/Cart.php';
require_once __DIR__ . '/../model/Order.php';

class AjaxController {
    private static function json($arr) {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($arr);
        exit;
    }

    public static function searchProducts() {
        $q = trim($_GET['q'] ?? '');
        $brand = trim($_GET['brand'] ?? '');
        $sort = trim($_GET['sort'] ?? '');

        try {
            $products = Product::getAll($q, $brand, $sort);
            if (!$products) $products = [];
            self::json(['ok' => true, 'products' => $products]);
        } catch (Exception $e) {
            self::json(['ok' => false, 'msg' => $e->getMessage(), 'products' => []]);
        }
    }

    public static function addToCart() {
        $u = current_user();
        $role = $u['role'] ?? 'guest';

        if ($role === 'admin' || $role === 'staff') {
            self::json(['ok' => false, 'msg' => 'Admin and Staff can not add cart']);
        }

        $productId = (int)($_POST['product_id'] ?? 0);
        $qty = (int)($_POST['qty'] ?? 1);
        if ($qty < 1) $qty = 1;

        try {
            if ($productId <= 0) throw new Exception('Invalid product');

            $r = Cart::add($productId, $qty);

            if (!$r || empty($r['ok'])) {
                self::json(['ok' => false, 'msg' => ($r['msg'] ?? 'Please check quantity')]);
            }

            $count = Cart::countItems();
            self::json(['ok' => true, 'msg' => ($r['msg'] ?? 'Added to cart'), 'cart_count' => $count]);
        } catch (Exception $e) {
            self::json(['ok' => false, 'msg' => $e->getMessage()]);
        }
    }

    public static function removeFromCart() {
        $cartId = (int)($_POST['cart_id'] ?? 0);

        try {
            if ($cartId <= 0) throw new Exception('Invalid cart item');
            $r = Cart::remove($cartId);
            self::json(['ok' => true, 'msg' => ($r['msg'] ?? 'Removed')]);
        } catch (Exception $e) {
            self::json(['ok' => false, 'msg' => $e->getMessage()]);
        }
    }

    public static function updateCartQty() {
        $cartId = (int)($_POST['cart_id'] ?? 0);
        $qty = (int)($_POST['qty'] ?? 1);
        if ($qty < 1) $qty = 1;

        try {
            if ($cartId <= 0) throw new Exception('Invalid cart item');

            $r = Cart::updateQty($cartId, $qty);
            if ($r && isset($r['ok']) && !$r['ok']) {
                self::json(['ok' => false, 'msg' => ($r['msg'] ?? 'Please check quantity')]);
            }

            self::json(['ok' => true, 'msg' => 'Updated']);
        } catch (Exception $e) {
            self::json(['ok' => false, 'msg' => $e->getMessage()]);
        }
    }

    public static function assignOrderStaff() {
        $orderId = (int)($_POST['order_id'] ?? 0);
        $staffPhone = trim($_POST['staff_phone'] ?? '');

        try {
            if ($orderId <= 0) throw new Exception('Invalid order');
            if ($staffPhone === '') throw new Exception('Select staff first');

            $ok = Order::assignStaff($orderId, $staffPhone);
            if (!$ok) {
                self::json(['ok' => false, 'msg' => 'Assign failed']);
            }

            self::json(['ok' => true, 'msg' => 'Assigned']);
        } catch (Exception $e) {
            self::json(['ok' => false, 'msg' => $e->getMessage()]);
        }
    }

    public static function updateOrderStatus() {
        $orderId = (int)($_POST['order_id'] ?? 0);
        $status = trim($_POST['status'] ?? '');

        try {
            if ($orderId <= 0) throw new Exception('Invalid order');
            if ($status === '') throw new Exception('Invalid status');

            $r = Order::updateStatus($orderId, $status);

            if (!$r || empty($r['ok'])) {
                self::json(['ok' => false, 'msg' => ($r['msg'] ?? 'Status update failed')]);
            }

            self::json(['ok' => true, 'msg' => ($r['msg'] ?? 'Status updated')]);
        } catch (Exception $e) {
            self::json(['ok' => false, 'msg' => $e->getMessage()]);
        }
    }
}
