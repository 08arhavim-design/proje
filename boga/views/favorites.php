<?php
function tr_slug_fav(string $s): string {
  $map = ['Ç'=>'c','ç'=>'c','Ğ'=>'g','ğ'=>'g','İ'=>'i','ı'=>'i','Ö'=>'o','ö'=>'o','Ş'=>'s','ş'=>'s','Ü'=>'u','ü'=>'u'];
  $s = strtr($s, $map);
  $s = strtolower($s);
  $s = preg_replace('~[^a-z0-9]+~', '-', $s) ?? '';
  $s = trim($s, '-');
  return $s !== '' ? $s : 'boga';
}
?>

<style>
  .section{margin-top:18px;background:#0d1117;border:1px solid rgba(255,255,255,0.06);border-radius:18px;padding:16px}
  .cards{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:12px}
  .card{display:block;background:#161b22;border:1px solid #30363d;border-radius:14px;overflow:hidden;text-decoration:none;color:inherit;transition:.2s}
  .card:hover{transform:translateY(-2px);border-color:rgba(243,156,18,0.55)}
  .imgbox{height:140px;background:#0b0e14;display:flex;align-items:center;justify-content:center;overflow:hidden}
  .card img{width:100%;height:100%;object-fit:cover}
  .body{padding:12px}
  .name{font-weight:900;color:#f39c12;margin:0 0 6px 0;font-size:14px}
  .muted{color:#8b949e;font-size:12px}
  @media (max-width:980px){.cards{grid-template-columns:repeat(2,minmax(0,1fr))}}
  @media (max-width:420px){.cards{grid-template-columns:1fr}}
</style>

<div class="section">
  <h2 style="margin:0 0 12px 0;font-size:16px;border-left:4px solid #f39c12;padding-left:12px;">⭐ Favorilerim</h2>

  <?php if (!empty($error)): ?>
    <div style="background:#da3633;border-radius:14px;padding:12px;border:1px solid rgba(255,255,255,0.10);">
      <?= e($error) ?>
    </div>
  <?php endif; ?>

  <div class="cards">
    <?php foreach (($favorites ?? []) as $b): ?>
      <?php
        $id = (int)($b['id'] ?? 0);
        $nm = (string)($b['name'] ?? 'boga');
        $href = function_exists('url') ? url($id . '-' . tr_slug_fav($nm)) : ($id . '-' . tr_slug_fav($nm));
      ?>
      <a class="card" href="<?= e($href) ?>">
        <div class="imgbox">
          <?php if(!empty($b['image'])): ?>
            <img src="<?= url('uploads/bulls/'.basename((string)$b['image'])) ?>" alt="<?= e($nm) ?>">
          <?php else: ?>
            <span class="muted">Fotoğraf yok</span>
          <?php endif; ?>
        </div>
        <div class="body">
          <div class="name"><?= e($nm) ?></div>
          <div class="muted">Sahibi: <?= e($b['owner_name'] ?? '-') ?></div>
          <div class="muted">Konum: <?= e(($b['city'] ?? '-') . ' / ' . ($b['district'] ?? '-')) ?></div>
        </div>
      </a>
    <?php endforeach; ?>

    <?php if (empty($favorites)): ?>
      <div class="muted">Henüz favoriniz yok.</div>
    <?php endif; ?>
  </div>
</div>
