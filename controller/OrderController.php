<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../model/Order.php';

class OrderController
{
    public static function confirm()
    {
        require_login();
        require_role('customer');

        if (!is_post()) {
            redirect_to(base_url('/index.php?page=cart'));
        }

        $u = current_user();
        $userPhone = $u['phone'] ?? '';

        if ($userPhone === '') {
            set_flash('error', 'User phone missing.');
            redirect_to(base_url('/index.php?page=cart'));
        }

        $payment = trim($_POST['payment_method'] ?? '');
        if ($payment === '') {
            $payment = 'cash';
        }

        $res = Order::createFromCart($userPhone, $payment);

        if (!($res['ok'] ?? false)) {
            set_flash('error', $res['msg'] ?? 'Order failed');
            redirect_to(base_url('/index.php?page=cart'));
        }

        set_flash('success', 'Order confirmed.');
        redirect_to(base_url('/index.php?page=my_orders'));
    }

    public static function myOrders()
    {
        require_login();
        require_role('customer');

        $u = current_user();
        $phone = $u['phone'] ?? '';

        try {
            $orders = Order::byUser($phone);
        } catch (Exception $e) {
            $orders = [];
            set_flash('error', 'Order load error: ' . $e->getMessage());
        }

        include __DIR__ . '/../view/orders.php';
    }
}
