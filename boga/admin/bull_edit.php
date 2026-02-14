<?php
declare(strict_types=1);

require_once __DIR__ . '/../app/functions.php';
require_login();
require_admin();

$pdo = pdo();

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) {
    redirect('admin/bulls.php');
}

$stmt = $pdo->prepare('SELECT * FROM bulls WHERE id = ? LIMIT 1');
$stmt->execute([$id]);
$b = $stmt->fetch();

if (!$b) {
    http_response_code(404);
    $title = 'Boğa Bulunamadı';
    $view  = __DIR__ . '/../views/user_edit_bull_notfound.php';
    include __DIR__ . '/../app/layout.php';
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();

    $name           = trim((string)($_POST['name'] ?? ''));
    $breed          = trim((string)($_POST['breed'] ?? ''));
    $age_raw        = trim((string)($_POST['age'] ?? ''));
    $age            = (int)preg_replace('/[^0-9]/', '', $age_raw);
    $weight         = trim((string)($_POST['weight'] ?? ''));
    $city           = trim((string)($_POST['city'] ?? ''));
    $district       = trim((string)($_POST['district'] ?? ''));
    $owner_name     = trim((string)($_POST['owner_name'] ?? ''));
    $original_owner = trim((string)($_POST['original_owner'] ?? ''));
    $mother_name    = trim((string)($_POST['mother_name'] ?? ''));
    $father_name    = trim((string)($_POST['father_name'] ?? ''));
    $arenas         = trim((string)($_POST['arenas'] ?? ''));
    $championships  = trim((string)($_POST['championships'] ?? ''));
    $price          = trim((string)($_POST['price'] ?? ''));

    $status = strtolower(trim((string)($_POST['status'] ?? (string)($b['status'] ?? 'pending'))));
    $status = in_array($status, ['pending', 'approved'], true) ? $status : 'pending';

    if ($name === '') {
        $error = 'Boğa adı zorunludur.';
    }

    // Resim (opsiyonel)
    $image_name = $b['image'] ?? null;
    if (isset($_FILES['image']) && ($_FILES['image']['error'] ?? 0) === 0) {
        $ext = strtolower(pathinfo((string)$_FILES['image']['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'webp'];
        if (!in_array($ext, $allowed, true)) {
            $error = 'Sadece JPG / PNG / WEBP yükleyebilirsiniz.';
        } else {
            $new_name = time() . '_' . uniqid('', true) . '.' . $ext;
            $target_dir = dirname(__DIR__) . '/uploads/bulls/';
            if (!is_dir($target_dir)) {
                mkdir($target_dir, 0755, true);
            }
            if (!move_uploaded_file((string)$_FILES['image']['tmp_name'], $target_dir . $new_name)) {
                $error = 'Fotoğraf yüklenemedi. Tekrar deneyin.';
            } else {
                // Eski resmi sil (varsa)
                if (!empty($image_name)) {
                    $old_path = $target_dir . basename((string)$image_name);
                    if (is_file($old_path)) {
                        @unlink($old_path);
                    }
                }
                $image_name = $new_name;
            }
        }
    }

    if ($error === '') {
        // Admin güncellemesi: uid filtreleme yok, status formdan gelir.
        $sql = "UPDATE bulls
                SET name=?, breed=?, age=?, weight=?, city=?, district=?,
                    owner_name=?, original_owner=?,
                    mother_name=?, father_name=?,
                    arenas=?, championships=?, price=?, image=?,
                    status=?
                WHERE id=?";
        $pdo->prepare($sql)->execute([
            $name, $breed, $age, $weight, $city, $district,
            $owner_name, $original_owner,
            $mother_name, $father_name,
            $arenas, $championships, $price, $image_name,
            $status,
            $id
        ]);

        redirect('admin/bulls.php');
    }

    // Hata varsa formda kullanıcı girdisini kaybetmemek için $b'yi güncelle
    $b = array_merge($b, [
        'name' => $name,
        'breed' => $breed,
        'age' => $age_raw,
        'weight' => $weight,
        'city' => $city,
        'district' => $district,
        'owner_name' => $owner_name,
        'original_owner' => $original_owner,
        'mother_name' => $mother_name,
        'father_name' => $father_name,
        'arenas' => $arenas,
        'championships' => $championships,
        'price' => $price,
        'image' => $image_name,
        'status' => $status,
    ]);
}

$title = 'Boğa Düzenle (Admin)';
$view  = __DIR__ . '/../views/admin_bull_edit.php';
include __DIR__ . '/../app/layout.php';
