<?php
require_once __DIR__ . '/DB.php';

class Cart {
    private static function cartSid() {
        return $_COOKIE['mm_cart_sid'] ?? '';
    }

    public static function linkToUser($phone) {
        $pdo = DB::conn();
        $sid = self::cartSid();
        if ($sid === '') return;
        $st = $pdo->prepare('UPDATE cart_items SET user_phone=? WHERE session_id=?');
        $st->execute([$phone, $sid]);
    }

    public static function countItems() {
        $pdo = DB::conn();
        $sid = self::cartSid();
        if ($sid === '') return 0;
        $st = $pdo->prepare('SELECT COALESCE(SUM(qty),0) as c FROM cart_items WHERE session_id=?');
        $st->execute([$sid]);
        $row = $st->fetch();
        return (int)($row['c'] ?? 0);
    }

    public static function items() {
        $pdo = DB::conn();
        $sid = self::cartSid();
        $st = $pdo->prepare('SELECT ci.id as cart_id, ci.qty as cart_qty, p.*
                             FROM cart_items ci
                             JOIN products p ON p.id=ci.product_id
                             WHERE ci.session_id=?
                             ORDER BY ci.id DESC');
        $st->execute([$sid]);
        return $st->fetchAll();
    }

    // FIXED: stock vs requested qty check before add
    public static function add($productId, $qty) {
        $pdo = DB::conn();
        $sid = self::cartSid();
        $user = current_user();
        $phone = $user['phone'] ?? null;

        $qty = (int)$qty;
        if ($qty < 1) $qty = 1;

        // Check product qty
        $p = Product::find($productId);
        if (!$p) return ['ok' => false, 'msg' => 'Product not found'];

        $stock = (int)$p['qty'];
        if ($stock <= 0) return ['ok' => false, 'msg' => 'Out of stock'];

        // If already in cart -> check (existing + new) <= stock
        $st = $pdo->prepare('SELECT id, qty FROM cart_items WHERE session_id=? AND product_id=?');
        $st->execute([$sid, $productId]);
        $row = $st->fetch();

        $existing = $row ? (int)$row['qty'] : 0;

        // যদি stock এর থেকে বেশি হয় → add হবে না
        if (($existing + $qty) > $stock) {
            return ['ok' => false, 'msg' => 'Please check quantity'];
        }

        if ($row) {
            $newQty = $existing + $qty;
            $up = $pdo->prepare('UPDATE cart_items SET qty=?, user_phone=? WHERE id=?');
            $up->execute([$newQty, $phone, $row['id']]);
        } else {
            $ins = $pdo->prepare('INSERT INTO cart_items(session_id,user_phone,product_id,qty) VALUES (?,?,?,?)');
            $ins->execute([$sid, $phone, $productId, $qty]);
        }

        return ['ok' => true, 'msg' => 'Added to cart'];
    }

    public static function updateQty($cartId, $qty) {
        $pdo = DB::conn();
        $sid = self::cartSid();
        $qty = max(1, (int)$qty);

        // get product qty
        $st = $pdo->prepare('SELECT ci.product_id, p.qty as pqty
                             FROM cart_items ci
                             JOIN products p ON p.id=ci.product_id
                             WHERE ci.id=? AND ci.session_id=?');
        $st->execute([$cartId, $sid]);
        $row = $st->fetch();
        if (!$row) return ['ok' => false, 'msg' => 'Cart item not found'];

        $maxQty = (int)$row['pqty'];
        if ($qty > $maxQty) $qty = $maxQty;

        $up = $pdo->prepare('UPDATE cart_items SET qty=? WHERE id=? AND session_id=?');
        $up->execute([$qty, $cartId, $sid]);
        return ['ok' => true, 'msg' => 'Quantity updated'];
    }

    public static function remove($cartId) {
        $pdo = DB::conn();
        $sid = self::cartSid();
        $st = $pdo->prepare('DELETE FROM cart_items WHERE id=? AND session_id=?');
        $st->execute([$cartId, $sid]);
        return ['ok' => true, 'msg' => 'Removed'];
    }

    public static function clear() {
        $pdo = DB::conn();
        $sid = self::cartSid();
        $st = $pdo->prepare('DELETE FROM cart_items WHERE session_id=?');
        $st->execute([$sid]);
    }
}
