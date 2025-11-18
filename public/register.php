<?php
require_once __DIR__ . '/../src/config.php';
require_once __DIR__ . '/../src/functions.php';
include __DIR__ . '/header.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $csrf = $_POST['csrf_token'] ?? '';
    if (!hash_equals($_SESSION['csrf_token'], $csrf)) {
        die('CSRF token không hợp lệ');
    }

    $fullname = trim($_POST['fullname'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $requested_role = $_POST['requested_role'] ?? 'user';

    $allowed_roles = ['gv','phong_dt','phong_tb','user'];

    if ($fullname === '' || $email === '' || $password === '') {
        $error = 'Vui lòng điền đầy đủ thông tin.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Email không hợp lệ.';
    } elseif (strlen($password) < 6) {
        $error = 'Mật khẩu phải ít nhất 6 ký tự.';
    } elseif (!in_array($requested_role, $allowed_roles, true)) {
        $error = 'Vai trò đề xuất không hợp lệ.';
    } else {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        try {
            $stmt = $pdo->prepare("INSERT INTO users (fullname, email, password, requested_role) VALUES (?, ?, ?, ?)");
            $stmt->execute([$fullname, $email, $hash, $requested_role]);
            $success = 'Đăng ký thành công. Tài khoản đang chờ phê duyệt.';
        } catch (PDOException $e) {
            $error = 'Email đã tồn tại hoặc lỗi hệ thống.';
        }
    }
}
?>
<section class="flex items-center justify-center min-h-screen">
  <div class="w-full max-w-md bg-white shadow-lg rounded-2xl p-8">
    <h1 class="text-center text-2xl font-bold text-primary mb-2">Quản lý phòng học</h1>
    <p class="text-center text-sm text-slate-500 mb-6">Tạo tài khoản mới</p>

    <?php if ($error): ?>
      <div class="mb-4 rounded-lg bg-red-50 border border-red-200 text-red-700 px-4 py-2 text-sm"><?=e($error)?></div>
    <?php endif; ?>
    <?php if ($success): ?>
      <div class="mb-4 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-2 text-sm"><?=e($success)?></div>
    <?php endif; ?>

    <form method="post" class="space-y-4">
      <input type="hidden" name="csrf_token" value="<?=e($_SESSION['csrf_token'])?>">
      <div>
        <label class="block text-sm font-medium mb-1">Họ và tên</label>
        <input type="text" name="fullname" required
               class="w-full rounded-lg border border-slate-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary">
      </div>
      <div>
        <label class="block text-sm font-medium mb-1">Email</label>
        <input type="email" name="email" required
               class="w-full rounded-lg border border-slate-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary">
      </div>
      <div>
        <label class="block text-sm font-medium mb-1">Mật khẩu</label>
        <input type="password" name="password" required
               class="w-full rounded-lg border border-slate-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary">
      </div>
      <div>
        <label class="block text-sm font-medium mb-1">Vai trò</label>
        <select name="requested_role" required
                class="w-full rounded-lg border border-slate-300 px-3 py-2 bg-white focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary">
          <option value="gv">Giáo viên</option>
          <option value="phong_dt">Phòng Đào tạo</option>
          <option value="phong_tb">Phòng Trang bị</option>
          <option value="user">Khác (user)</option>
        </select>
      </div>
      <button type="submit"
              class="w-full rounded-lg bg-primary hover:bg-blue-700 transition-colors duration-200 text-white font-medium py-2.5">
        Đăng ký
      </button>
    </form>

    <p class="mt-4 text-center text-sm">
      Đã có tài khoản?
      <a class="text-primary hover:underline" href="login.php">Đăng nhập</a>
    </p>
  </div>
</section>
<?php include __DIR__ . '/footer.php'; ?>
