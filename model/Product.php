<?php
require_once __DIR__ . '/DB.php';

class Product {
    public static function getAll($q = '', $brand = '', $sort = '') {
        $pdo = DB::conn();
        $sql = 'SELECT * FROM products WHERE 1=1';
        $args = [];
        if ($q !== '') {
            $sql .= ' AND (model LIKE ? OR brand LIKE ?)';
            $args[] = '%' . $q . '%';
            $args[] = '%' . $q . '%';
        }
        if ($brand !== '') {
            $sql .= ' AND brand=?';
            $args[] = $brand;
        }
        if ($sort === 'price_asc') $sql .= ' ORDER BY price ASC';
        elseif ($sort === 'price_desc') $sql .= ' ORDER BY price DESC';
        else $sql .= ' ORDER BY id DESC';

        $st = $pdo->prepare($sql);
        $st->execute($args);
        return $st->fetchAll();
    }

    public static function brands() {
        $pdo = DB::conn();
        $st = $pdo->query('SELECT DISTINCT brand FROM products ORDER BY brand');
        return $st->fetchAll();
    }

    public static function find($id) {
        $pdo = DB::conn();
        $st = $pdo->prepare('SELECT * FROM products WHERE id=?');
        $st->execute([$id]);
        return $st->fetch();
    }

    public static function create($data) {
        $pdo = DB::conn();
        $st = $pdo->prepare('INSERT INTO products(model,brand,ram,rom,price,qty,image,offer_percent) VALUES (?,?,?,?,?,?,?,?)');
        return $st->execute([
            $data['model'], $data['brand'], $data['ram'], $data['rom'],
            $data['price'], $data['qty'], $data['image'], $data['offer_percent'],
        ]);
    }

    public static function update($id, $data) {
        $pdo = DB::conn();
        $st = $pdo->prepare('UPDATE products SET model=?,brand=?,ram=?,rom=?,price=?,qty=?,image=?,offer_percent=? WHERE id=?');
        return $st->execute([
            $data['model'], $data['brand'], $data['ram'], $data['rom'],
            $data['price'], $data['qty'], $data['image'], $data['offer_percent'], $id,
        ]);
    }

    public static function delete($id) {
        $pdo = DB::conn();
        $st = $pdo->prepare('DELETE FROM products WHERE id=?');
        return $st->execute([$id]);
    }

    public static function decreaseQty($id, $qty) {
        $pdo = DB::conn();
        $st = $pdo->prepare('UPDATE products SET qty = qty - ? WHERE id=? AND qty >= ?');
        $st->execute([$qty, $id, $qty]);
        return ($st->rowCount() > 0);
    }

    public static function suggest($q) {
        $q = trim($q);
        if ($q === '') return [];

        $pdo = DB::conn();
        $like = '%' . $q . '%';

        $sql = "SELECT id, model, brand
                FROM products
                WHERE model LIKE ? OR brand LIKE ?
                ORDER BY id DESC
                LIMIT 8";
        $st = $pdo->prepare($sql);
        $st->execute([$like, $like]);

        $out = [];
        while ($r = $st->fetch(\PDO::FETCH_ASSOC)) {
            $out[] = [
                'id' => (int)($r['id'] ?? 0),
                'model' => (string)($r['model'] ?? ''),
                'brand' => (string)($r['brand'] ?? ''),
            ];
        }
        return $out;
    }
}
