<?php
require_once __DIR__ . '/app/functions.php';
require_login(); // Sadece giriş yapanlar erişebilir

if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('/user_add_bull.php');
csrf_check();

$pdo = pdo();
$uid = current_user_id(); // Giriş yapan kullanıcının ID'si

// Formdan gelen veriler
$name = trim((string)($_POST['name'] ?? ''));
$age = (int)($_POST['age'] ?? 0);
$owner_name = trim((string)($_POST['owner_name'] ?? ''));
$category_id = ($_POST['category_id'] ?? '') !== '' ? (int)$_POST['category_id'] : null;

// Basit doğrulama
if ($name === '') { 
    flash_set('err', 'Boğa adı zorunlu.'); 
    redirect('/user_add_bull.php'); 
}

// Resim yükleme işlemi
$upload = handle_bull_upload($_FILES['image'] ?? []);
if (!$upload['ok']) { 
    flash_set('err', $upload['error'] ?? 'Yükleme hatası'); 
    redirect('/user_add_bull.php'); 
}

// Veritabanına kaydetme (created_by ve status kritik)
$stmt = $pdo->prepare("
    INSERT INTO bulls (name, age, owner_name, category_id, image, created_by, status, created_at)
    VALUES (?, ?, ?, ?, ?, ?, 'pending', NOW())
");

$stmt->execute([
    $name, 
    $age, 
    $owner_name, 
    $category_id, 
    $upload['filename'], 
    $uid
]);

flash_set('ok', 'Boğa kaydı alındı, yönetici onayından sonra listelenecektir.');
redirect('/user.php');