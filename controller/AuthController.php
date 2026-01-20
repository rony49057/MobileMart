<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../model/User.php';
require_once __DIR__ . '/../model/Cart.php';

class AuthController {
    public static function login() 
	{
        if (is_post()) {
            $phone = trim($_POST['phone'] ?? '');
            $pass  = $_POST['password'] ?? '';

            if ($phone === '' || $pass === '') {
                set_flash('error', 'Phone and password required.');
                redirect_to(base_url('/index.php?page=login'));
            }

            try {
                $u = User::auth($phone, $pass);
                if (!$u) {
                    set_flash('error', 'Invalid login.');
                    redirect_to(base_url('/index.php?page=login'));
                }

                $_SESSION['user'] = $u;

                Cart::linkToUser($u['phone']);

                if ($u['role'] === 'admin') redirect_to(base_url('/index.php?page=admin_dashboard'));
                if ($u['role'] === 'staff') redirect_to(base_url('/index.php?page=staff_dashboard'));
                redirect_to(base_url('/index.php?page=customer_dashboard'));
            } catch (Exception $e) {
                set_flash('error', 'Login error: ' . $e->getMessage());
                redirect_to(base_url('/index.php?page=login'));
            }
        }
        include __DIR__ . '/../view/login.php';
    }

    public static function logout() 
	{
        $_SESSION = [];
        session_destroy();
        setcookie('MMSESSID', '', time() - 3600, '/');
        redirect_to(base_url('/index.php'));
    }

    public static function register() {
        if (is_post()) {
            $data = [
                'name' => trim($_POST['name'] ?? ''),
                'phone' => trim($_POST['phone'] ?? ''),
                'gender' => trim($_POST['gender'] ?? ''),
                'dob' => trim($_POST['dob'] ?? ''),
                'address' => trim($_POST['address'] ?? ''),
                'role' => trim($_POST['role'] ?? 'customer'),
                'password' => $_POST['password'] ?? '',
                'confirm' => $_POST['confirm'] ?? '',
            ];

            if ($data['name']==='' || $data['phone']==='' || $data['gender']==='' || $data['dob']==='' || $data['password']==='') {
                set_flash('error', 'Please fill all required fields.');
                redirect_to(base_url('/index.php?page=register'));
            }

            if (strlen($data['password']) < 4) {
                set_flash('error', 'Minimum 4 digit');
                redirect_to(base_url('/index.php?page=register'));
            }

            if ($data['password'] !== $data['confirm']) {
                set_flash('error', 'Password and confirm password not match.');
                redirect_to(base_url('/index.php?page=register'));
            }

            if (!in_array($data['role'], ['customer','admin','staff'])) {
                $data['role'] = 'customer';
            }

            try {
                User::create($data);
                set_flash('success', 'Registration successful. Now login.');
                redirect_to(base_url('/index.php?page=login'));
            } catch (Exception $e) {
                set_flash('error', 'Registration failed: ' . $e->getMessage());
                redirect_to(base_url('/index.php?page=register'));
            }
        }
        include __DIR__ . '/../view/register.php';
    }

    public static function forgotPassword() {
        if (is_post()) {
            $phone = trim($_POST['phone'] ?? '');
            $new1 = $_POST['new_password'] ?? '';
            $new2 = $_POST['confirm_password'] ?? '';

            if ($phone==='' || $new1==='' || $new2==='') {
                set_flash('error', 'All fields required.');
                redirect_to(base_url('/index.php?page=forgot'));
            }

            if (strlen($new1) < 4) {
                set_flash('error', 'Minimum 4 digit');
                redirect_to(base_url('/index.php?page=forgot'));
            }

            if ($new1 !== $new2) {
                set_flash('error', 'Password not match.');
                redirect_to(base_url('/index.php?page=forgot'));
            }

            try {
                $u = User::findByPhone($phone);
                if (!$u) {
                    set_flash('error', 'User not found.');
                    redirect_to(base_url('/index.php?page=forgot'));
                }
                User::changePassword($phone, $new1);
                set_flash('success', 'Password updated. Now login.');
                redirect_to(base_url('/index.php?page=login'));
            } catch (Exception $e) {
                set_flash('error', 'Error: ' . $e->getMessage());
                redirect_to(base_url('/index.php?page=forgot'));
            }
        }
        include __DIR__ . '/../view/forgot.php';
    }

    public static function profile() {
        require_login();
        $u = current_user();
        $user = User::findByPhone($u['phone']);

        if (is_post()) {
            $data = [
                'name' => trim($_POST['name'] ?? ''),
                'gender' => trim($_POST['gender'] ?? ''),
                'dob' => trim($_POST['dob'] ?? ''),
                'address' => trim($_POST['address'] ?? ''),
            ];

            if ($data['name']==='' || $data['gender']==='' || $data['dob']==='') {
                set_flash('error', 'Name/Gender/DOB required.');
                redirect_to(base_url('/index.php?page=profile'));
            }

            try {
                User::updateProfile($u['phone'], $data);
                set_flash('success', 'Profile updated.');
                $_SESSION['user']['name'] = $data['name'];
                redirect_to(base_url('/index.php?page=profile'));
            } catch (Exception $e) {
                set_flash('error', 'Update failed: ' . $e->getMessage());
                redirect_to(base_url('/index.php?page=profile'));
            }
        }

        include __DIR__ . '/../view/profile.php';
    }

    public static function changePassword() {
        require_login();
        if (is_post()) {
            $old = $_POST['old_password'] ?? '';
            $new1 = $_POST['new_password'] ?? '';
            $new2 = $_POST['confirm_password'] ?? '';

            if ($old==='' || $new1==='' || $new2==='') {
                set_flash('error', 'All fields required.');
                redirect_to(base_url('/index.php?page=change_password'));
            }

            if (strlen($new1) < 4) {
                set_flash('error', 'Minimum 4 digit');
                redirect_to(base_url('/index.php?page=change_password'));
            }

            if ($new1 !== $new2) {
                set_flash('error', 'New password not match.');
                redirect_to(base_url('/index.php?page=change_password'));
            }

            try {
                $u = current_user();
                $auth = User::auth($u['phone'], $old);
                if (!$auth) {
                    set_flash('error', 'Old password wrong.');
                    redirect_to(base_url('/index.php?page=change_password'));
                }
                User::changePassword($u['phone'], $new1);
                set_flash('success', 'Password changed.');
                redirect_to(base_url('/index.php?page=profile'));
            } catch (Exception $e) {
                set_flash('error', 'Error: '.$e->getMessage());
                redirect_to(base_url('/index.php?page=change_password'));
            }
        }
        include __DIR__ . '/../view/change_password.php';
    }
}
