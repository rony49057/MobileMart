<?php
require_once __DIR__ . '/DB.php';

class StaffSalary {

    public static function pay($staffPhone, $amount, $month, $note='') {
        $pdo = DB::conn();
        $st = $pdo->prepare('INSERT INTO staff_salary(staff_phone,amount,month,note) VALUES (?,?,?,?)');
        return $st->execute([$staffPhone, $amount, $month, $note]);
    }

    public static function totalPaid() {
        $pdo = DB::conn();
        $st = $pdo->query('SELECT COALESCE(SUM(amount),0) AS total FROM staff_salary');
        $row = $st->fetch();
        return (float)($row['total'] ?? 0);
    }

    public static function all() {
        $pdo = DB::conn();
        $sql = "SELECT ss.*, u.name AS staff_name
                FROM staff_salary ss
                LEFT JOIN users u ON u.phone = ss.staff_phone
                ORDER BY ss.id DESC";
        $st = $pdo->query($sql);
        return $st->fetchAll();
    }
}
