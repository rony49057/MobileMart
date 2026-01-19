<?php
require_once __DIR__ . '/DB.php';

class User {

    public static function findByPhone($phone) {
        $pdo = DB::conn();
        $st  = $pdo->prepare('SELECT phone, name, gender, dob, address, role, created_at FROM users WHERE phone=?');
        $st->execute([$phone]);
        return $st->fetch();
    }

    public static function auth($phone, $password) {
        $pdo = DB::conn();
        $st  = $pdo->prepare('SELECT phone, name, password_hash, role FROM users WHERE phone=?');
        $st->execute([$phone]);
        $u = $st->fetch();

        if (!$u) return null;

        // verify password
        if (!password_verify($password, $u['password_hash'])) return null;

        return [
            'phone' => $u['phone'],
            'name'  => $u['name'],
            'role'  => $u['role'],
        ];
    }

    public static function create($data) {
        $pdo  = DB::conn();
        $hash = password_hash($data['password'], PASSWORD_DEFAULT);

        $st = $pdo->prepare('INSERT INTO users(phone,name,gender,dob,address,password_hash,role) VALUES (?,?,?,?,?,?,?)');
        return $st->execute([
            $data['phone'],
            $data['name'],
            $data['gender'],
            $data['dob'],
            $data['address'],
            $hash,
            $data['role'],
        ]);
    }

    public static function updateProfile($phone, $data) {
        $pdo = DB::conn();
        $st  = $pdo->prepare('UPDATE users SET name=?, gender=?, dob=?, address=? WHERE phone=?');
        return $st->execute([
            $data['name'],
            $data['gender'],
            $data['dob'],
            $data['address'],
            $phone
        ]);
    }

    public static function changePassword($phone, $newPassword) {
        $pdo  = DB::conn();
        $hash = password_hash($newPassword, PASSWORD_DEFAULT);

        $st = $pdo->prepare('UPDATE users SET password_hash=? WHERE phone=?');
        return $st->execute([$hash, $phone]);
    }

    public static function allByRole($role) {
        $pdo = DB::conn();
        $st  = $pdo->prepare('SELECT phone,name,gender,dob,address,role,created_at FROM users WHERE role=? ORDER BY created_at DESC');
        $st->execute([$role]);
        return $st->fetchAll();
    }

    // Admin can update customer/staff basic info
    public static function updateByAdmin($phone, $data) {
        $pdo = DB::conn();
        $st  = $pdo->prepare("UPDATE users SET name=?, gender=?, dob=?, address=? WHERE phone=?");

        return $st->execute([
            $data['name'],
            $data['gender'],
            $data['dob'],
            $data['address'],
            $phone
        ]);
    }

    public static function delete($phone) {
        $pdo = DB::conn();
        $st  = $pdo->prepare('DELETE FROM users WHERE phone=?');
        return $st->execute([$phone]);
    }

    public static function setRole($phone, $role) {
        $pdo = DB::conn();
        $st  = $pdo->prepare('UPDATE users SET role=? WHERE phone=?');
        return $st->execute([$role, $phone]);
    }

}
