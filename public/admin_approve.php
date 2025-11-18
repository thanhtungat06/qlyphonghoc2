<?php
require_once __DIR__ . '/../src/config.php';
require_once __DIR__ . '/../src/functions.php';
include __DIR__ . '/header.php';

require_admin();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['approve_id'])) {
    if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'] ?? '')) {
        die('CSRF token không hợp lệ');
    }
    $id = (int)$_POST['approve_id'];
    $final_role = $_POST['final_role'] ?? 'user';
    $allowed = ['admin','gv','phong_dt','phong_tb','user'];
    if (!in_array($final_role, $allowed, true)) { die('Vai trò không hợp lệ'); }
    $stmt = $pdo->prepare('UPDATE users SET status = ?, role = ? WHERE id = ?');
    $stmt->execute(['active', $final_role, $id]);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reject_id'])) {
    if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'] ?? '')) {
        die('CSRF token không hợp lệ');
    }
    $id = (int)$_POST['reject_id'];
    $stmt = $pdo->prepare('UPDATE users SET status = ? WHERE id = ?');
    $stmt->execute(['rejected', $id]);
}

$stmt = $pdo->query("SELECT id, fullname, email, requested_role, created_at FROM users WHERE status = 'pending' ORDER BY created_at ASC");
$users = $stmt->fetchAll();
?>
<section class="max-w-5xl mx-auto py-10">
  <div class="mb-8 text-center">
    <h1 class="text-2xl font-bold text-primary">Quản lý phòng học</h1>
    <p class="text-sm text-slate-500">Phê duyệt tài khoản người dùng</p>
  </div>

  <div class="bg-white rounded-2xl shadow">
    <div class="p-4 border-b border-slate-100 flex items-center justify-between">
      <div class="text-sm text-slate-600">
        Xin chào, <span class="font-medium text-slate-800"><?=e($_SESSION['fullname'] ?? 'Admin')?></span>
      </div>
      <div class="space-x-2">
        <a href="dashboard.php" class="inline-block rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 px-3 py-2 text-sm transition">Quản trị viên</a>
        <a href="logout.php" class="inline-block rounded-lg bg-primary hover:bg-blue-700 text-white px-3 py-2 text-sm transition">Đăng xuất</a>
      </div>
    </div>

    <div class="p-4 overflow-x-auto">
      <?php if (count($users) === 0): ?>
        <div class="p-6 text-center text-slate-500">Không có tài khoản chờ duyệt.</div>
      <?php else: ?>
      <table class="min-w-full text-sm">
        <thead>
          <tr class="bg-slate-50 text-slate-600">
            <th class="px-4 py-3 text-left font-medium">ID</th>
            <th class="px-4 py-3 text-left font-medium">Họ tên</th>
            <th class="px-4 py-3 text-left font-medium">Email</th>
            <th class="px-4 py-3 text-left font-medium">Vai trò đề xuất</th>
            <th class="px-4 py-3 text-left font-medium">Ngày đăng ký</th>
            <th class="px-4 py-3 text-left font-medium">Hành động</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
          <?php foreach ($users as $u): ?>
          <tr class="hover:bg-slate-50">
            <td class="px-4 py-3"><?=e($u['id'])?></td>
            <td class="px-4 py-3"><?=e($u['fullname'])?></td>
            <td class="px-4 py-3"><?=e($u['email'])?></td>
            <td class="px-4 py-3"><?=e($u['requested_role'])?></td>
            <td class="px-4 py-3"><?=e($u['created_at'])?></td>
            <td class="px-4 py-3">
              <form method="post" class="inline-block">
                <input type="hidden" name="csrf_token" value="<?=e($_SESSION['csrf_token'])?>">
                <input type="hidden" name="approve_id" value="<?=e($u['id'])?>">
                <select name="final_role" class="rounded-lg border border-slate-300 px-2 py-1 text-sm bg-white">
                  <option value="gv" <?=($u['requested_role']=='gv')?'selected':''?>>Giáo viên</option>
                  <option value="phong_dt" <?=($u['requested_role']=='phong_dt')?'selected':''?>>Phòng ĐT</option>
                  <option value="phong_tb" <?=($u['requested_role']=='phong_tb')?'selected':''?>>Phòng TB</option>
                  <option value="user" <?=($u['requested_role']=='user')?'selected':''?>>User</option>
                  <option value="admin">Admin</option>
                </select>
                <button class="ml-2 rounded-lg bg-primary hover:bg-blue-700 text-white px-3 py-1.5 text-sm transition" type="submit">Duyệt</button>
              </form>
              <form method="post" class="inline-block ml-2">
                <input type="hidden" name="csrf_token" value="<?=e($_SESSION['csrf_token'])?>">
                <input type="hidden" name="reject_id" value="<?=e($u['id'])?>">
                <button class="rounded-lg bg-red-500 hover:bg-red-600 text-white px-3 py-1.5 text-sm transition" type="submit" onclick="return confirm('Bạn có chắc muốn từ chối?')">Từ chối</button>
              </form>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
      <?php endif; ?>
    </div>
  </div>
</section>
<?php include __DIR__ . '/footer.php'; ?>
