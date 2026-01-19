<?php
require_once __DIR__ . '/DB.php';
require_once __DIR__ . '/Product.php';

class Order
{
    public static function createFromCart($paymentMethod)
    {
        $userPhone = $_SESSION['user']['phone'] ?? '';
        if ($userPhone === '') {
            return ['ok' => false, 'msg' => 'Please login first'];
        }

        $pdo = DB::conn();
        $sid = $_COOKIE['mm_cart_sid'] ?? '';
        if ($sid === '') return ['ok'=>false,'msg'=>'Cart session missing'];

        $st = $pdo->prepare('SELECT ci.product_id, ci.qty, p.price, p.offer_percent, p.qty as stock_qty
                             FROM cart_items ci
                             JOIN products p ON p.id=ci.product_id
                             WHERE ci.session_id=?');
        $st->execute([$sid]);
        $items = $st->fetchAll();
        if (!$items) return ['ok'=>false,'msg'=>'Cart is empty'];

        foreach ($items as $it) {
            if ((int)$it['qty'] > (int)$it['stock_qty']) {
                return ['ok'=>false,'msg'=>'Stock not enough for a product'];
            }
        }

        $total = 0;
        foreach ($items as $it) {
            $price = (float)$it['price'];
            $offer = (int)$it['offer_percent'];
            if ($offer > 0) $price = $price - ($price * ($offer/100));
            $total += $price * (int)$it['qty'];
        }

        try {
            $pdo->beginTransaction();

            $ins = $pdo->prepare('INSERT INTO orders(user_phone,total,payment_method,status) VALUES (?,?,?,?)');
            $ins->execute([$userPhone, $total, $paymentMethod, 'Pending']);
            $orderId = (int)$pdo->lastInsertId();

            $oi = $pdo->prepare('INSERT INTO order_items(order_id,product_id,price,qty) VALUES (?,?,?,?)');
            foreach ($items as $it) {
                $price = (float)$it['price'];
                $offer = (int)$it['offer_percent'];
                if ($offer > 0) $price = $price - ($price * ($offer/100));

                $ok = Product::decreaseQty((int)$it['product_id'], (int)$it['qty']);
                if (!$ok) {
                    $pdo->rollBack();
                    return ['ok'=>false,'msg'=>'Stock changed. Try again.'];
                }

                $oi->execute([$orderId, (int)$it['product_id'], $price, (int)$it['qty']]);
            }

            $clr = $pdo->prepare('DELETE FROM cart_items WHERE session_id=?');
            $clr->execute([$sid]);

            $pdo->commit();
            return ['ok'=>true,'msg'=>'Order confirmed','order_id'=>$orderId];
        } catch (Exception $e) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            return ['ok'=>false,'msg'=>'Order failed: '.$e->getMessage()];
        }
    }

    public static function byUser($phone)
    {
        $pdo = DB::conn();
        $st = $pdo->prepare('SELECT * FROM orders WHERE user_phone=? ORDER BY id DESC');
        $st->execute([$phone]);
        return $st->fetchAll();
    }

    public static function items($orderId)
    {
        $pdo = DB::conn();
        $st = $pdo->prepare('SELECT oi.*, p.model, p.brand, p.image
                             FROM order_items oi
                             JOIN products p ON p.id=oi.product_id
                             WHERE oi.order_id=?');
        $st->execute([$orderId]);
        return $st->fetchAll();
    }

    public static function all($q='')
    {
        $pdo = DB::conn();

        $base = "SELECT
                    o.*,
                    cu.name AS user_name,
                    cu.address AS user_address,
                    st.name AS staff_name
                 FROM orders o
                 LEFT JOIN users cu ON cu.phone = o.user_phone
                 LEFT JOIN users st ON st.phone = o.assigned_staff_phone";

        $q = trim($q);

        if ($q === '') {
            $st = $pdo->query($base . " ORDER BY o.id DESC");
            return $st->fetchAll();
        }

        $like = '%' . $q . '%';
        $st = $pdo->prepare($base . " WHERE CAST(o.id AS CHAR) LIKE ? OR o.user_phone LIKE ? OR cu.name LIKE ? ORDER BY o.id DESC");
        $st->execute([$like, $like, $like]);
        return $st->fetchAll();
    }

    public static function assignStaff($orderId, $staffPhone)
    {
        $pdo = DB::conn();
        $st = $pdo->prepare('UPDATE orders SET assigned_staff_phone=? WHERE id=?');
        return $st->execute([$staffPhone, $orderId]);
    }

    public static function updateStatus($orderId, $status)
    {
        $pdo = DB::conn();

        $st = $pdo->prepare('SELECT status FROM orders WHERE id=?');
        $st->execute([(int)$orderId]);
        $row = $st->fetch();

        if (!$row) return ['ok'=>false,'msg'=>'Order not found'];

        $cur = trim($row['status'] ?? '');
        if (strcasecmp($cur, 'Delivered') === 0) {
            return ['ok'=>false,'msg'=>'Delivered order status can not change'];
        }

        $up = $pdo->prepare('UPDATE orders SET status=? WHERE id=?');
        $ok = $up->execute([$status, (int)$orderId]);

        if (!$ok) return ['ok'=>false,'msg'=>'Status update failed'];
        return ['ok'=>true,'msg'=>'Status updated'];
    }

    public static function staffOrders($staffPhone, $q='')
    {
        $pdo = DB::conn();
        $q = trim($q);

        $base = "SELECT o.*, cu.name AS user_name, cu.address AS user_address
                 FROM orders o
                 LEFT JOIN users cu ON cu.phone = o.user_phone";

        if ($q === '') {
            $st = $pdo->prepare($base . ' WHERE o.assigned_staff_phone=? ORDER BY o.id DESC');
            $st->execute([$staffPhone]);
            return $st->fetchAll();
        }

        $like = '%' . $q . '%';
        $st = $pdo->prepare($base . ' WHERE o.assigned_staff_phone=? AND (CAST(o.id AS CHAR) LIKE ? OR o.user_phone LIKE ? OR cu.name LIKE ?) ORDER BY o.id DESC');
        $st->execute([$staffPhone, $like, $like, $like]);
        return $st->fetchAll();
    }

    public static function totalSell()
    {
        $pdo = DB::conn();
        $st = $pdo->prepare('SELECT COALESCE(SUM(total),0) as t FROM orders WHERE status=?');
        $st->execute(['Delivered']);
        $row = $st->fetch();
        return (float)($row['t'] ?? 0);
    }
}
