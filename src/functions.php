<?php
function e($str) { return htmlspecialchars($str, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }
function require_admin() {
    if (empty($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'admin') {
        http_response_code(403);
        echo 'Bạn không có quyền truy cập.';
        exit;
    }
}
function is_logged_in() { return !empty($_SESSION['user_id']); }
?>