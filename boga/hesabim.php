<?php
// File: /boga/hesabim.php
declare(strict_types=1);

require_once __DIR__ . '/app/functions.php';

require_login();

$pdo = pdo();
$userId = current_user_id();

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();

    $fullName = trim((string)($_POST['full_name'] ?? ''));
    $username = trim((string)($_POST['username'] ?? ''));
    $phone    = trim((string)($_POST['phone'] ?? ''));
    $password = (string)($_POST['password'] ?? '');
    $password2= (string)($_POST['password2'] ?? '');

    if ($username === '') {
        $error = 'Kullanıcı adı boş olamaz.';
    } elseif ($password !== '' && $password !== $password2) {
        $error = 'Şifreler eşleşmiyor.';
    } else {
        try {
            // username benzersiz mi?
            $st = $pdo->prepare("SELECT id FROM users WHERE username=? AND id<>? LIMIT 1");
            $st->execute([$username, $userId]);
            if ($st->fetchColumn()) {
                $error = 'Bu kullanıcı adı zaten kullanılıyor.';
            } else {
                if ($password !== '') {
                    $hash = password_hash($password, PASSWORD_DEFAULT);
                    $up = $pdo->prepare("
                        UPDATE users
                        SET full_name=?, username=?, phone=?, password=?
                        WHERE id=?
                        LIMIT 1
                    ");
                    $up->execute([$fullName, $username, $phone, $hash, $userId]);
                } else {
                    $up = $pdo->prepare("
                        UPDATE users
                        SET full_name=?, username=?, phone=?
                        WHERE id=?
                        LIMIT 1
                    ");
                    $up->execute([$fullName, $username, $phone, $userId]);
                }

                flash_set('success', 'Hesap bilgileri güncellendi.');
                redirect('hesabim');
            }
        } catch (Throwable $e) {
            error_log('hesabim.php update error: ' . $e->getMessage());
            $error = 'Güncelleme sırasında hata oluştu.';
        }
    }
}

$user = null;
try {
    $st = $pdo->prepare("SELECT id, username, full_name, phone, role, created_at FROM users WHERE id=? LIMIT 1");
    $st->execute([$userId]);
    $user = $st->fetch(PDO::FETCH_ASSOC) ?: null;
} catch (Throwable $e) {
    $error = $error ?: 'Veri alınırken hata oluştu.';
}

$title = 'Hesabım';
$view  = __DIR__ . '/views/hesabim.php';
include __DIR__ . '/app/layout.php';
