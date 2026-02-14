<?php
declare(strict_types=1);

require_once __DIR__ . '/app/functions.php';
require_login();

$pdo = pdo();
$u = current_user($pdo);
if (!$u) { redirect('/login.php'); }

$errors = [];
$ok = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = (string)($_POST['csrf'] ?? '');
    if (!csrf_verify($token)) {
        $errors[] = 'Güvenlik doğrulaması başarısız.';
    } else {
        $name = trim((string)($_POST['name'] ?? ''));
        $age  = (int)($_POST['age'] ?? 0);

        $won_titles    = trim((string)($_POST['won_titles'] ?? ''));
        $mother_name   = trim((string)($_POST['mother_name'] ?? ''));
        $father_name   = trim((string)($_POST['father_name'] ?? ''));
        $last_arena    = trim((string)($_POST['last_arena'] ?? ''));
        $last_opponent = trim((string)($_POST['last_opponent'] ?? ''));
        $last_category = trim((string)($_POST['last_category'] ?? ''));

        if ($name === '') $errors[] = 'Boğa adı zorunlu.';
        if ($age < 0 || $age > 99) $errors[] = 'Yaş 0-99 olmalı.';

        // Foto yükleme (opsiyonel)
        $photoPath = null;
        if (!empty($_FILES['photo']['name'])) {
            $f = $_FILES['photo'];
            if ($f['error'] !== UPLOAD_ERR_OK) {
                $errors[] = 'Foto yükleme hatası.';
            } else {
                $max = defined('MAX_UPLOAD_BYTES') ? (int)MAX_UPLOAD_BYTES : 15 * 1024 * 1024;
                if ((int)$f['size'] > $max) $errors[] = 'Foto boyutu çok büyük (max 15MB).';

                $ext = strtolower(pathinfo((string)$f['name'], PATHINFO_EXTENSION));
                $allowed = defined('ALLOWED_EXT') ? (array)ALLOWED_EXT : ['jpg','jpeg','png'];
                if (!in_array($ext, $allowed, true)) $errors[] = 'Sadece JPG/JPEG/PNG yüklenebilir.';

                $imgInfo = @getimagesize($f['tmp_name']);
                if ($imgInfo === false) $errors[] = 'Geçersiz görsel dosyası.';

                if (!$errors) {
                    $dir = defined('UPLOAD_DIR') && UPLOAD_DIR !== '' ? UPLOAD_DIR : (__DIR__ . '/uploads/bulls');
                    if (!is_dir($dir)) @mkdir($dir, 0755, true);

                    $safe = bin2hex(random_bytes(8)) . '.' . $ext;
                    $dest = rtrim($dir, '/\\') . DIRECTORY_SEPARATOR . $safe;

                    if (!move_uploaded_file($f['tmp_name'], $dest)) {
                        $errors[] = 'Dosya kaydedilemedi.';
                    } else {
                        $baseUrl = defined('UPLOAD_URL') && UPLOAD_URL !== '' ? UPLOAD_URL : '/boga/uploads/bulls';
                        $photoPath = rtrim($baseUrl,'/') . '/' . $safe; // web yolu
                    }
                }
            }
        }

        if (!$errors) {
            // sahibi otomatik: giriş yapan kullanıcı
            $owner_name = (string)($u['full_name'] ?: $u['username']); // ekranda görünen sahip
            $owner_user_id = (int)$u['id'];

            // Normal kullanıcı ekleyince pending, admin eklerse approved
            $status = in_array((string)$u['role'], ['admin','superadmin'], true) ? 'approved' : 'pending';

            $sql = "INSERT INTO bulls
                (name, age, owner_name, owner_user_id, won_titles, mother_name, father_name, last_arena, last_opponent, last_category, photo_path, status, created_by)
                VALUES
                (:name,:age,:owner_name,:owner_user_id,:won_titles,:mother_name,:father_name,:last_arena,:last_opponent,:last_category,:photo_path,:status,:created_by)";

            $st = $pdo->prepare($sql);
            $st->execute([
                ':name' => $name,
                ':age' => $age,
                ':owner_name' => $owner_name,
                ':owner_user_id' => $owner_user_id,
                ':won_titles' => $won_titles,
                ':mother_name' => $mother_name,
                ':father_name' => $father_name,
                ':last_arena' => $last_arena,
                ':last_opponent' => $last_opponent,
                ':last_category' => $last_category,
                ':photo_path' => $photoPath,
                ':status' => $status,
                ':created_by' => (int)$u['id'],
            ]);

            $ok = ($status === 'pending')
                ? 'Kayıt alındı. Admin onayından sonra yayınlanacak.'
                : 'Boğa kaydı eklendi.';
        }
    }
}

$csrf = csrf_token();
?>
<!doctype html>
<html lang="tr">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Boğa Ekle</title>
<style>
body{margin:0;background:#0b1220;color:#e8eefc;font-family:system-ui,-apple-system,Segoe UI,Roboto}
.wrap{max-width:1200px;margin:auto;padding:20px}
.card{background:#111a2e;border-radius:12px;padding:18px}
input,textarea{width:100%;padding:12px;border-radius:10px;border:1px solid rgba(255,255,255,.12);background:rgba(0,0,0,.25);color:#fff;margin:8px 0}
button{padding:12px 16px;border-radius:10px;border:0;background:#1f6feb;color:#fff;font-weight:700;width:100%}
.msg{padding:10px 12px;border-radius:10px;margin:10px 0}
.err{background:rgba(255,80,80,.12);border:1px solid rgba(255,80,80,.35)}
.ok{background:rgba(80,255,140,.10);border:1px solid rgba(80,255,140,.30)}
.grid{display:grid;grid-template-columns:1fr 1fr;gap:12px}
@media(max-width:800px){.grid{grid-template-columns:1fr}}
.small{opacity:.8;font-size:13px}
</style>
</head>
<body>
<div class="wrap">
  <div class="card">
    <h2>Boğa Ekle</h2>
    <div class="small">Boğa sahibi otomatik: <?php echo esc((string)($u['full_name'] ?: $u['username'])); ?></div>

    <?php foreach($errors as $e): ?>
      <div class="msg err"><?php echo esc($e); ?></div>
    <?php endforeach; ?>
    <?php if($ok): ?>
      <div class="msg ok"><?php echo esc($ok); ?></div>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data">
      <input type="hidden" name="csrf" value="<?php echo esc($csrf); ?>">

      <div class="grid">
        <div>
          <input name="name" placeholder="Boğa adı" value="<?php echo esc((string)($_POST['name'] ?? '')); ?>">
          <input name="age" type="number" min="0" max="99" placeholder="Yaş" value="<?php echo esc((string)($_POST['age'] ?? '')); ?>">
          <input name="last_category" placeholder="Kategori" value="<?php echo esc((string)($_POST['last_category'] ?? '')); ?>">
          <input name="last_arena" placeholder="En son güreştiği yer (meydan)" value="<?php echo esc((string)($_POST['last_arena'] ?? '')); ?>">
          <input name="last_opponent" placeholder="En son güreştiği boğa" value="<?php echo esc((string)($_POST['last_opponent'] ?? '')); ?>">
        </div>
        <div>
          <input name="mother_name" placeholder="Anne adı" value="<?php echo esc((string)($_POST['mother_name'] ?? '')); ?>">
          <input name="father_name" placeholder="Baba adı" value="<?php echo esc((string)($_POST['father_name'] ?? '')); ?>">
          <textarea name="won_titles" rows="6" placeholder="Kazandığı yarışlar"><?php echo esc((string)($_POST['won_titles'] ?? '')); ?></textarea>
          <input type="file" name="photo" accept=".jpg,.jpeg,.png">
          <div class="small">JPG/JPEG/PNG, max 15MB</div>
        </div>
      </div>

      <button type="submit">Kaydet</button>
    </form>
  </div>
</div>
</body>
</html>
