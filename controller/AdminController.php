<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../model/Product.php';
require_once __DIR__ . '/../model/User.php';
require_once __DIR__ . '/../model/Order.php';
require_once __DIR__ . '/../model/StaffSalary.php';

class AdminController {

    public static function dashboard() {
        require_login();
        require_role('admin');

        $q = trim($_GET['q'] ?? '');
        $brand = trim($_GET['brand'] ?? '');
        $sort = trim($_GET['sort'] ?? '');

        try {
            $products = Product::getAll($q, $brand, $sort);
            $brands = Product::brands();

            $totalSell = (float)Order::totalSell();
            $salaryPaid = (float)StaffSalary::totalPaid();

            $netSell = $totalSell - $salaryPaid;
            if ($netSell < 0) $netSell = 0;
        } catch (Exception $e) {
            $products = [];
            $brands = [];
            $totalSell = 0;
            $salaryPaid = 0;
            $netSell = 0;
            set_flash('error', 'Admin dashboard error: ' . $e->getMessage());
        }

        include __DIR__ . '/../view/admin_dashboard.php';
    }

    public static function products() {
        require_login();
        require_role('admin');

        if (is_post()) {
            $action = $_POST['action'] ?? '';

            try {
                if ($action === 'add') {
                    $data = self::readProductForm();
                    Product::create($data);
                    set_flash('success', 'Product added.');
                } elseif ($action === 'update') {
                    $id = (int)($_POST['id'] ?? 0);
                    $data = self::readProductForm();
                    if ($id > 0) {
                        Product::update($id, $data);
                        set_flash('success', 'Product updated.');
                    }
                } elseif ($action === 'delete') {
                    $id = (int)($_POST['id'] ?? 0);
                    if ($id > 0) {
                        Product::delete($id);
                        set_flash('success', 'Product deleted.');
                    }
                }
            } catch (Exception $e) {
                set_flash('error', 'Product action failed: ' . $e->getMessage());
            }

            redirect_to(base_url('/index.php?page=admin_products'));
        }

        $q = trim($_GET['q'] ?? '');
        $brand = trim($_GET['brand'] ?? '');
        $sort = trim($_GET['sort'] ?? '');

        $products = Product::getAll($q, $brand, $sort);
        $brands = Product::brands();

        $edit = null;
        $editId = (int)($_GET['edit_id'] ?? 0);
        if ($editId > 0) $edit = Product::find($editId);

        include __DIR__ . '/../view/admin_products.php';
    }

    private static function readProductForm() {
        $model = trim($_POST['model'] ?? '');
        $brand = trim($_POST['brand'] ?? '');
        $ram = trim($_POST['ram'] ?? '');
        $rom = trim($_POST['rom'] ?? '');
        $price = (float)($_POST['price'] ?? 0);
        $qty = (int)($_POST['qty'] ?? 0);
        $offer = (int)($_POST['offer_percent'] ?? 0);

        $oldImage = trim($_POST['old_image'] ?? 'default-phone.png');
        if ($oldImage === '') $oldImage = 'default-phone.png';

        if ($model === '' || $brand === '' || $price <= 0) {
            throw new Exception('Model/Brand/Price required');
        }

        $image = $oldImage;

        if (isset($_FILES['image_file']) && is_array($_FILES['image_file'])) {
            $f = $_FILES['image_file'];

            if (!empty($f['name']) && (int)($f['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE) {

                if ((int)$f['error'] !== UPLOAD_ERR_OK) {
                    throw new Exception('Image upload error (code ' . (int)$f['error'] . ')');
                }

                $maxBytes = 5 * 1024 * 1024;
                if ((int)$f['size'] > $maxBytes) {
                    throw new Exception('Image too large (max 5MB)');
                }

                $ext = strtolower(pathinfo($f['name'], PATHINFO_EXTENSION));
                $allowedExt = ['png', 'jpg', 'jpeg'];
                if (!in_array($ext, $allowedExt, true)) {
                    throw new Exception('Invalid image type. Allowed: PNG/JPG/JPEG');
                }

                $imgInfo = @getimagesize($f['tmp_name']);
                if ($imgInfo === false) {
                    throw new Exception('Invalid image file');
                }
                $allowedMime = ['image/png', 'image/jpeg'];
                if (!in_array($imgInfo['mime'] ?? '', $allowedMime, true)) {
                    throw new Exception('Invalid image type. Allowed: PNG/JPG/JPEG');
                }

                $original = basename($f['name']);
                $original = preg_replace('/[^a-zA-Z0-9._-]/', '_', $original);
                if ($original === '' || $original === '.' || $original === '..') {
                    throw new Exception('Invalid image file name');
                }

                $finalExt = strtolower(pathinfo($original, PATHINFO_EXTENSION));
                if (!in_array($finalExt, $allowedExt, true)) {
                    throw new Exception('Invalid image file name/extension');
                }

                $destDir = __DIR__ . '/../view/images/';
                if (!is_dir($destDir)) {
                    throw new Exception('Image folder not found: view/images');
                }

                $destPath = $destDir . $original;

                if (!move_uploaded_file($f['tmp_name'], $destPath)) {
                    throw new Exception('Failed to save uploaded image');
                }

                $image = $original;
            }
        }

        if ($qty < 0) $qty = 0;
        if ($offer < 0) $offer = 0;
        if ($offer > 90) $offer = 90;

        return [
            'model' => $model,
            'brand' => $brand,
            'ram' => $ram,
            'rom' => $rom,
            'price' => $price,
            'qty' => $qty,
            'image' => $image,
            'offer_percent' => $offer,
        ];
    }

    public static function users() {
        require_login();
        require_role('admin');

        if (is_post()) {
            $action = $_POST['action'] ?? '';
            try {
                if ($action === 'update_user') {
                    $phone = trim($_POST['phone'] ?? '');
                    $data = [
                        'name' => trim($_POST['name'] ?? ''),
                        'gender' => trim($_POST['gender'] ?? ''),
                        'dob' => trim($_POST['dob'] ?? ''),
                        'address' => trim($_POST['address'] ?? ''),
                    ];
                    if ($phone === '' || $data['name'] === '') {
                        throw new Exception('Phone and name required');
                    }
                    User::updateByAdmin($phone, $data);
                    set_flash('success', 'User updated.');
                    redirect_to(base_url('/index.php?page=admin_users'));
                }

                if ($action === 'delete_user') {
                    $phone = trim($_POST['phone'] ?? '');
                    if ($phone !== '') User::delete($phone);
                    set_flash('success', 'User deleted.');
                    redirect_to(base_url('/index.php?page=admin_users'));
                }

                if ($action === 'reset_password') {
                    $phone = trim($_POST['phone'] ?? '');
                    $newpass = $_POST['new_password'] ?? '';

                    if ($phone === '' || $newpass === '') {
                        throw new Exception('Phone and new password required');
                    }
                    if (strlen($newpass) < 4) {
                        throw new Exception('Minimum 4 digit');
                    }

                    User::changePassword($phone, $newpass);
                    set_flash('success', 'Password reset done.');
                    redirect_to(base_url('/index.php?page=admin_users'));
                }

                if ($action === 'add_staff') {
                    $password = $_POST['password'] ?? '';
                    $confirm  = $_POST['confirm'] ?? '';

                    if (strlen($password) < 4) {
                        throw new Exception('Minimum 4 digit');
                    }
                    if ($password !== $confirm) {
                        throw new Exception('Password not match');
                    }

                    $data = [
                        'name' => trim($_POST['name'] ?? ''),
                        'phone' => trim($_POST['phone'] ?? ''),
                        'gender' => trim($_POST['gender'] ?? ''),
                        'dob' => trim($_POST['dob'] ?? ''),
                        'address' => trim($_POST['address'] ?? ''),
                        'role' => 'staff',
                        'password' => $password,
                        'confirm' => $confirm,
                    ];

                    User::create($data);
                    unset($_SESSION['old_add_staff']);
                    set_flash('success', 'New staff added.');
                    redirect_to(base_url('/index.php?page=admin_users'));
                }

            } catch (Exception $e) {
                if (($action ?? '') === 'add_staff') {
                    $_SESSION['old_add_staff'] = $_POST;
                    unset($_SESSION['old_add_staff']['password'], $_SESSION['old_add_staff']['confirm']);
                }
                set_flash('error', $e->getMessage());
                redirect_to(base_url('/index.php?page=admin_users'));
            }
        }

        $customers = User::allByRole('customer');
        $staffs = User::allByRole('staff');

        include __DIR__ . '/../view/admin_users.php';
    }

    public static function orders() {
        require_login();
        require_role('admin');

        $q = trim($_GET['q'] ?? '');
        $orders = Order::all($q);
        $staffs = User::allByRole('staff');
        include __DIR__ . '/../view/admin_orders.php';
    }

    public static function staffSalary() {
        require_login();
        require_role('admin');

        if (is_post()) {
            try {
                $staffPhone = trim($_POST['staff_phone'] ?? '');
                $amount = (float)($_POST['amount'] ?? 0);
                $month = trim($_POST['month'] ?? '');
                $note = trim($_POST['note'] ?? '');

                if ($staffPhone === '' || $amount <= 0 || $month === '') {
                    throw new Exception('Staff/Amount/Month required');
                }

                StaffSalary::pay($staffPhone, $amount, $month, $note);
                set_flash('success', 'Salary paid.');
            } catch (Exception $e) {
                set_flash('error', 'Salary error: ' . $e->getMessage());
            }
            redirect_to(base_url('/index.php?page=admin_staff_salary'));
        }

        $staffs = User::allByRole('staff');

        $history = [];
        try {
            $history = StaffSalary::all();
        } catch (Exception $e) {
            $history = [];
        }

        include __DIR__ . '/../view/admin_staff_salary.php';
    }
}
