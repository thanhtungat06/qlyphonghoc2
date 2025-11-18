<?php
require_once __DIR__ . '/../src/config.php';
require_once __DIR__ . '/../src/functions.php';

if (!is_logged_in()) { header('Location: login.php'); exit; }
include __DIR__ . '/header.php';

$role = $_SESSION['role'] ?? 'guest';
$fullname = $_SESSION['fullname'] ?? 'User';
?>
<header class="bg-white shadow-sm">
  <div class="max-w-6xl mx-auto px-4 py-4 flex items-center justify-between">
    <h1 class="text-lg font-semibold text-primary">Quản lý phòng học</h1>
    <div class="flex items-center gap-2">
    
      <a href="logout.php" class="rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 px-3 py-2 text-sm transition">Đăng xuất</a>
    </div>
  </div>
</header>

<main class="max-w-6xl mx-auto px-4">
  <div class="mt-8 grid md:grid-cols-2 gap-6">

    <!-- Card chào mừng -->
    <div class="bg-white rounded-2xl shadow p-6">
      <h2 class="text-base font-semibold text-slate-800 mb-2">Xin chào</h2>
      <p class="text-slate-600">Người dùng: <span class="font-medium text-slate-800"><?= e($fullname) ?></span></p>
      <p class="text-slate-600">Vai trò: <span class="font-medium text-slate-800"><?= e($role) ?></span></p>
    </div>

    <?php if ($role === 'admin'): ?>
      <!-- Màn chào mừng dành riêng cho Admin -->
      <div class="bg-white rounded-2xl shadow p-6">
        <h2 class="text-base font-semibold text-slate-800 mb-2">Chào mừng Quản trị viên</h2>
        <p class="text-slate-600 text-sm leading-6">
          Bạn đang đăng nhập với quyền <strong>Admin</strong>. Đây là bảng điều khiển quản trị hệ thống.
          Từ đây, bạn có thể truy cập các chức năng quản trị (Quản lý người dùng trong hệ thống, phê duyệt tài khoản, chỉnh sửa thông tin...).
        </p>
        
        <div class="mt-3 text-sm">
          <a href="admin_approve.php" class="text-primary hover:underline">Đi tới trang quản trị</a>
        </div>
      </div>
    <?php else: ?>
      <!-- Màn cho người dùng thường -->
      <div class="bg-white rounded-2xl shadow p-6">
        <h2 class="text-base font-semibold text-slate-800 mb-2">Trạng thái hệ thống</h2>
        <p class="text-slate-600 text-sm">Tài khoản của bạn đã được phê duyệt và hoạt động bình thường.</p>
      </div>
    <?php endif; ?>

  </div>
</main>

<?php include __DIR__ . '/footer.php'; ?>
