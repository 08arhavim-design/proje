<?php
declare(strict_types=1);

require_once __DIR__ . '/../app/functions.php';
require_login();
require_admin();

$pdo = pdo();

$title = 'Kullanıcılar';
$view  = __DIR__ . '/../views/admin_users.php';

$error = '';
$ok    = '';

/* Kolonları tespit et (telefon adı bazen phone/telefon olabiliyor) */
$cols = [];
foreach ($pdo->query("SHOW COLUMNS FROM users") as $c) {
    $cols[strtolower((string)$c['Field'])] = true;
}
$phoneCol = isset($cols['phone']) ? 'phone' : (isset($cols['telefon']) ? 'telefon' : '');
$statusCol = isset($cols['status']) ? 'status' : '';
$createdCol = isset($cols['created_at']) ? 'created_at' : '';

/* Rol güncelleme sadece superadmin */
$isSuper = (user_role() === 'superadmin');

/* CSRF */
$csrf = csrf_token();

/* izin verilen roller */
$allowedRoles = ['user','admin','superadmin'];

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        csrf_check();

        $action = (string)($_POST['action'] ?? '');

        if ($action === 'update_role') {
            if (!$isSuper) {
                $error = 'Sadece superadmin rol değiştirebilir.';
            } else {
                $uid  = (int)($_POST['user_id'] ?? 0);
                $role = trim((string)($_POST['role'] ?? ''));

                if ($uid <= 0 || !in_array($role, $allowedRoles, true)) {
                    $error = 'Geçersiz kullanıcı veya rol.';
                } else {
                    // Kendini kilitleme koruması: superadmin kendi rolünü düşüremez
                    if ($uid === current_user_id() && $role !== 'superadmin') {
                        $error = 'Kendi rolünüzü superadmin dışına düşüremezsiniz.';
                    } else {
                        // Hedef kullanıcı mevcut mu?
                        $st = $pdo->prepare("SELECT id, role FROM users WHERE id=:id LIMIT 1");
                        $st->execute([':id' => $uid]);
                        $u = $st->fetch();

                        if (!$u) {
                            $error = 'Kullanıcı bulunamadı.';
                        } else {
                            $st = $pdo->prepare("UPDATE users SET role=:r WHERE id=:id LIMIT 1");
                            $st->execute([':r' => $role, ':id' => $uid]);
                            $ok = 'Rol güncellendi.';
                        }
                    }
                }
            }
        }
    }

    /* Liste */
    $selectPhone = $phoneCol !== '' ? "`$phoneCol` AS phone," : "NULL AS phone,";
    $selectStatus = $statusCol !== '' ? "`$statusCol` AS status," : "NULL AS status,";
    $selectCreated = $createdCol !== '' ? "`$createdCol` AS created_at" : "NULL AS created_at";

    $sql = "
        SELECT id, username,
               $selectPhone
               role,
               $selectStatus
               $selectCreated
        FROM users
        ORDER BY id DESC
        LIMIT 500
    ";
    $users = $pdo->query($sql)->fetchAll();

} catch (Throwable $e) {
    $error = 'Kullanıcılar alınırken hata: ' . $e->getMessage();
    $users = [];
}

include __DIR__ . '/../app/layout.php';
