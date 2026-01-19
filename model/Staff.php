<?php
require_once __DIR__ . '/DB.php';

class StaffSalary {
    public static function pay($staffPhone, $amount, $month, $note='') {
        $pdo = DB::conn();
        $st = $pdo->prepare('INSERT INTO staff_salary(staff_phone,amount,month,note) VALUES (?,?,?,?)');
        return $st->execute([$staffPhone, $amount, $month, $note]);
    }

    public static function byStaff($staffPhone) {
        $pdo = DB::conn();
        $st = $pdo->prepare('SELECT * FROM staff_salary WHERE staff_phone=? ORDER BY id DESC');
        $st->execute([$staffPhone]);
        return $st->fetchAll();
    }
}
?>