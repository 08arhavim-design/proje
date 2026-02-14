<?php
// File: /boga/views/hesabim.php
// Expects: $user, $error

$h = static function ($v): string {
  if (function_exists('e')) return e((string)$v);
  return htmlspecialchars((string)$v, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
};

$role = (string)($user['role'] ?? '');
$roleLabel = match ($role) {
  'superadmin' => 'Süper Admin',
  'admin'      => 'Admin',
  'user'       => 'Üye',
  default      => ($role !== '' ? $role : '-'),
};
?>

<style>
  .box{max-width:720px;margin:0 auto;background:#0d1117;border:1px solid rgba(255,255,255,0.08);border-radius:18px;padding:16px}
  .row{display:flex;gap:12px;flex-wrap:wrap}
  .col{flex:1;min-width:240px}
  label{display:block;font-size:12px;color:#8b949e;margin:10px 0 6px}
  input{width:100%;padding:10px 12px;border-radius:10px;border:1px solid rgba(255,255,255,0.14);background:rgba(255,255,255,0.06);color:#fff}
  .btn{display:inline-block;padding:10px 12px;border-radius:10px;text-decoration:none;font-weight:800;font-size:13px;border:1px solid rgba(255,255,255,0.12);background:rgba(255,255,255,0.06);color:#fff;cursor:pointer}
  .btn.primary{background:rgba(243,156,18,0.18);border-color:rgba(243,156,18,0.35);color:#f39c12}
  .error{background:#da3633;border:1px solid rgba(255,255,255,0.10);border-radius:14px;padding:12px;margin:10px 0}
  .muted{color:#8b949e;font-size:12px}
  .tag{display:inline-flex;align-items:center;gap:6px;padding:6px 10px;border-radius:999px;font-size:12px;font-weight:900;background:rgba(88,166,255,0.12);border:1px solid rgba(88,166,255,0.25);color:#58a6ff}
</style>

<div class="box">
  <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:10px;flex-wrap:wrap;">
    <div>
      <h2 style="margin:0 0 8px 0;">Hesabım</h2>
      <div class="muted">Bilgilerini buradan güncelleyebilirsin.</div>
    </div>
    <span class="tag">Yetki: <?= $h($roleLabel) ?></span>
  </div>

  <?php if (!empty($error)): ?>
    <div class="error"><?= $h($error) ?></div>
  <?php endif; ?>

  <form method="post" action="">
    <input type="hidden" name="csrf" value="<?= $h(csrf_token()) ?>">

    <div class="row">
      <div class="col">
        <label>Ad Soyad</label>
        <input name="full_name" value="<?= $h($user['full_name'] ?? '') ?>" autocomplete="name">
      </div>
      <div class="col">
        <label>Kullanıcı Adı</label>
        <input name="username" value="<?= $h($user['username'] ?? '') ?>" autocomplete="username" required>
      </div>
      <div class="col">
        <label>Telefon</label>
        <input name="phone" value="<?= $h($user['phone'] ?? '') ?>" autocomplete="tel">
      </div>
    </div>

    <hr style="border:0;border-top:1px solid rgba(255,255,255,0.10);margin:14px 0">

    <div class="row">
      <div class="col">
        <label>Yeni Şifre (boş bırak: değişmesin)</label>
        <input type="password" name="password" autocomplete="new-password">
      </div>
      <div class="col">
        <label>Yeni Şifre (tekrar)</label>
        <input type="password" name="password2" autocomplete="new-password">
      </div>
    </div>

    <div style="margin-top:14px;display:flex;gap:10px;flex-wrap:wrap;">
      <button class="btn primary" type="submit">Kaydet</button>
      <a class="btn" href="<?= $h(function_exists('url') ? url('') : 'index.php') ?>">Ana Sayfa</a>
    </div>
  </form>
</div>
