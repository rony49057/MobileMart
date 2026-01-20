<?php
require_once __DIR__ . '/DB.php';

class StaffSalary
{
    public static function pay($staffPhone, $amount, $month, $note = '')
    {
        $pdo = DB::conn();
        $st = $pdo->prepare("INSERT INTO staff_salary(staff_phone, amount, month, note, created_at) VALUES(?,?,?,?,NOW())");
        return $st->execute([$staffPhone, $amount, $month, $note]);
    }

    public static function add($staffPhone, $amount, $month, $note = '')
    {
        return self::pay($staffPhone, $amount, $month, $note);
    }

    public static function byStaff($staffPhone)
    {
        $pdo = DB::conn();
        $st = $pdo->prepare("SELECT * FROM staff_salary WHERE staff_phone=? ORDER BY id DESC");
        $st->execute([$staffPhone]);
        return $st->fetchAll();
    }

    public static function totalPaid()
    {
        $pdo = DB::conn();
        $st = $pdo->query("SELECT COALESCE(SUM(amount),0) AS t FROM staff_salary");
        $row = $st->fetch();
        return (float)($row['t'] ?? 0);
    }

    public static function all($q = '')
    {
        $pdo = DB::conn();
        $q = trim($q);

        $base = "SELECT ss.*, u.name as staff_name
                 FROM staff_salary ss
                 LEFT JOIN users u ON u.phone = ss.staff_phone";

        if ($q === '') {
            $st = $pdo->query($base . " ORDER BY ss.id DESC");
            return $st->fetchAll();
        }

        $like = '%' . $q . '%';
        $st = $pdo->prepare($base . " WHERE ss.staff_phone LIKE ? OR u.name LIKE ? OR ss.month LIKE ? ORDER BY ss.id DESC");
        $st->execute([$like, $like, $like]);
        return $st->fetchAll();
    }

    public static function delete($id)
    {
        $pdo = DB::conn();
        $st = $pdo->prepare("DELETE FROM staff_salary WHERE id=?");
        return $st->execute([(int)$id]);
    }
}
