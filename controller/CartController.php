<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../model/Cart.php';

class CartController {
    public static function cart() {
        try {
            $items = Cart::items();
        } catch (Exception $e) {
            $items = [];
            set_flash('error', 'Cart error: ' . $e->getMessage());
        }
        include __DIR__ . '/../view/cart.php';
    }
}
