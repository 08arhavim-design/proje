<?php
declare(strict_types=1);
require_once __DIR__ . '/app/functions.php';
require_login();

$pdo = pdo();
$uid = current_user_id();
$role = user_role();

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();

    $name           = trim((string)($_POST['name'] ?? ''));
    $breed          = trim((string)($_POST['breed'] ?? ''));
    $age            = trim((string)($_POST['age'] ?? ''));
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

    $image_name = null;
    if (isset($_FILES['image']) && (int)($_FILES['image']['error'] ?? 0) === 0) {
        $ext = strtolower((string)pathinfo((string)($_FILES['image']['name'] ?? ''), PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'webp'];
        if (in_array($ext, $allowed, true)) {
            $image_name = time() . '_' . uniqid('', true) . '.' . $ext;

            $target_dir = __DIR__ . '/uploads/bulls/';
            if (!is_dir($target_dir)) {
                @mkdir($target_dir, 0755, true);
            }

            if (!@move_uploaded_file((string)$_FILES['image']['tmp_name'], $target_dir . $image_name)) {
                $image_name = null;
            }
        }
    }

    if ($name === '') {
        $error = "Boğa adı alanı zorunludur.";
    } else {
        $status = in_array($role, ['admin', 'superadmin'], true) ? 'approved' : 'pending';

        try {
            $cols = $pdo->query("DESCRIBE bulls")->fetchAll(PDO::FETCH_ASSOC);
            $existing = [];
            foreach ($cols as $c) {
                $existing[(string)$c['Field']] = true;
            }

            $now = date('Y-m-d H:i:s');

            $want = [
                'uid'            => $uid,
                'created_by'     => $uid,
                'owner_user_id'  => $uid,

                'name'           => $name,
                'breed'          => $breed,
                'age'            => $age,
                'weight'         => $weight,
                'city'           => $city,
                'district'       => $district,
                'owner_name'     => $owner_name,
                'original_owner' => $original_owner,
                'mother_name'    => $mother_name,
                'father_name'    => $father_name,
                'arenas'         => $arenas,
                'championships'  => $championships,
                'price'          => $price,
                'image'          => $image_name,
                'photo_path'     => $image_name ? ('/boga/uploads/bulls/' . $image_name) : null,

                'status'         => $status,
                'created_at'     => $now,
            ];

            $insertCols = [];
            $vals = [];

            foreach ($want as $col => $val) {
                if (!isset($existing[$col])) continue;

                // boş stringleri NULL yap (özellikle varchar/nullable alanlar için)
                if (is_string($val)) {
                    $val = trim($val);
                    if ($val === '') $val = null;
                }
                $insertCols[] = $col;
                $vals[] = $val;
            }

            if (!$insertCols) {
                throw new RuntimeException('Bull tablosu kolonları okunamadı.');
            }

            $placeholders = implode(',', array_fill(0, count($insertCols), '?'));
            $sql = "INSERT INTO bulls (" . implode(',', $insertCols) . ") VALUES ($placeholders)";

            $st = $pdo->prepare($sql);
            $ok = $st->execute($vals);

            if ($ok) {
                if ($status === 'pending') {
                    header("Location: user.php?status=pending");
                } else {
                    header("Location: user.php?status=success");
                }
                exit;
            }

            $error = "Kayıt başarısız!";
        } catch (Throwable $e) {
            $error = "Veritabanı Hatası: " . $e->getMessage();
        }
    }
}

$title = 'Yeni Boğa Ekle';
$view = __DIR__ . '/views/user_add_bull.php';
include __DIR__ . '/app/layout.php';
