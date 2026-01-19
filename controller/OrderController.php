<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../model/Order.php';
require_once __DIR__ . '/../model/Cart.php';

class OrderController {

    public static function confirm() {
        require_login();
        require_role('customer');

        if (!is_post()) {
            redirect_to(base_url('/index.php?page=cart'));
        }

        try {
            $payment = trim($_POST['payment_method'] ?? 'cash');
            if ($payment === '') $payment = 'cash';

            Order::createFromCart($payment);

            set_flash('success', 'Order confirmed.');
            redirect_to(base_url('/index.php?page=my_orders'));
        } catch (Exception $e) {
            set_flash('error', 'Order error: ' . $e->getMessage());
            redirect_to(base_url('/index.php?page=cart'));
        }
    }

    public static function myOrders() {
        require_login();
        require_role('customer');

        $u = current_user();
        $phone = $u['phone'] ?? '';

        try {
            $orders = Order::byCustomer($phone);
        } catch (Exception $e) {
            $orders = [];
            set_flash('error', 'Order load error: ' . $e->getMessage());
        }

        include __DIR__ . '/../view/my_orders.php';
    }
}
