<div class="muted">Bu sayfa tabloları oluşturur ve ilk superadmin hesabını ekler. Kurulumdan sonra bu dosyayı silin.</div>
<hr style="border:0;border-top:1px solid rgba(255,255,255,.08);margin:12px 0">
<ul>
<?php foreach ($msgs as $m): ?>
  <li><?= e($m) ?></li>
<?php endforeach; ?>
</ul>
<a class="btn" href="<?= e(base_url('/')) ?>">Giriş sayfası</a>
