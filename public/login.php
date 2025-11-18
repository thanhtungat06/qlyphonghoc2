<?php
require_once __DIR__ . '/../src/config.php';
require_once __DIR__ . '/../src/functions.php';
include __DIR__ . '/header.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email === '' || $password === '') {
        $error = 'Vui lòng nhập email và mật khẩu.';
    } else {
        $stmt = $pdo->prepare('SELECT * FROM users WHERE email = ? LIMIT 1');
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        if (!$user || !password_verify($password, $user['password'])) {
    $error = 'Sai email hoặc mật khẩu.';
} else {
    // Nếu là admin => cho đăng nhập luôn, bỏ qua kiểm tra status
    if ($user['role'] === 'admin') {
        session_regenerate_id(true);
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['fullname'] = $user['fullname'];

        // (tuỳ chọn) tự động kích hoạt nếu vì lý do nào đó admin đang pending
        if ($user['status'] !== 'active') {
            $stmt = $pdo->prepare('UPDATE users SET status = ? WHERE id = ?');
            $stmt->execute(['active', $user['id']]);
        }

        header('Location: dashboard.php');
        exit;
    }

    // Người dùng thường: vẫn phải được phê duyệt
    if ($user['status'] !== 'active') {
        $error = 'Tài khoản chưa được phê duyệt hoặc đã bị từ chối.';
    } else {
        session_regenerate_id(true);
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['fullname'] = $user['fullname'];
        header('Location: dashboard.php');
        exit;
    }
}

    }
}
?>
<section class="flex items-center justify-center min-h-screen">
  <div class="w-full max-w-md bg-white shadow-lg rounded-2xl p-8">
    <h1 class="text-center text-2xl font-bold text-primary mb-2">Quản lý phòng học</h1>
    <p class="text-center text-sm text-slate-500 mb-6">Đăng nhập để tiếp tục</p>

    <?php if ($error): ?>
      <div class="mb-4 rounded-lg bg-red-50 border border-red-200 text-red-700 px-4 py-2 text-sm"><?=e($error)?></div>
    <?php endif; ?>

    <form method="post" class="space-y-4">
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
      <button type="submit"
              class="w-full rounded-lg bg-primary hover:bg-blue-700 transition-colors duration-200 text-white font-medium py-2.5">
        Đăng nhập
      </button>
    </form>

    <p class="mt-4 text-center text-sm">
      Chưa có tài khoản?
      <a class="text-primary hover:underline" href="register.php">Đăng ký</a>
    </p>
  </div>
</section>
<?php include __DIR__ . '/footer.php'; ?>
