<?php
require_once __DIR__ . '/config.php';
function create_admin_if_missing($pdo) {
    $stmt = $pdo->query("SELECT COUNT(*) AS c FROM users WHERE role='admin'"); $row = $stmt->fetch();
    if ($row && (int)$row['c'] === 0) {
        $hash = password_hash('admin123', PASSWORD_DEFAULT);
        $pdo->prepare("INSERT INTO users (fullname, email, password, role, status) VALUES (?, ?, ?, 'admin', 'active')")
            ->execute(['Admin', 'admin@example.com', $hash]);
    }
}
create_admin_if_missing($pdo);
?>