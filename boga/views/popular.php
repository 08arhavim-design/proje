<?php
// File: /boga/views/popular.php
// Expects: $bulls (array), $error

$h = static function ($v): string {
    if (function_exists('e')) return e($v);
    return htmlspecialchars((string)($v ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
};

function tr_slug_pop(string $s): string {
    $map = ['Ç'=>'c','ç'=>'c','Ğ'=>'g','ğ'=>'g','İ'=>'i','ı'=>'i','Ö'=>'o','ö'=>'o','Ş'=>'s','ş'=>'s','Ü'=>'u','ü'=>'u'];
    $s = strtr($s, $map);
    $s = strtolower($s);
    $s = preg_replace('~[^a-z0-9]+~', '-', $s) ?? '';
    $s = trim($s, '-');
    return $s !== '' ? $s : 'boga';
}

$comparePost = function_exists('url') ? url('karsilastir-islem') : 'karsilastir-islem';
?>

<style>
  .section{margin-top:18px;background:#0d1117;border:1px solid rgba(255,255,255,0.06);border-radius:18px;padding:16px}
  .section h2{margin:0 0 12px 0;font-size:16px;display:flex;align-items:center;gap:10px;border-left:4px solid #f39c12;padding-left:12px;color:#fff}
  .btn{display:inline-block;padding:10px 12px;border-radius:10px;text-decoration:none;font-weight:800;font-size:13px;border:1px solid rgba(255,255,255,0.12);background:rgba(255,255,255,0.06);color:#fff;cursor:pointer}
  .btn.orange{background:rgba(243,156,18,0.18);border-color:rgba(243,156,18,0.35);color:#f39c12}
  .muted{color:#8b949e;font-size:12px}
  .errorbox{background:#da3633;border-radius:14px;padding:12px;margin-top:12px;border:1px solid rgba(255,255,255,0.10)}
  .cards{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:12px}
  .card{background:#161b22;border:1px solid #30363d;border-radius:14px;overflow:hidden}
  .imgbox{height:140px;background:#0b0e14;display:flex;align-items:center;justify-content:center;overflow:hidden}
  .card img{width:100%;height:100%;object-fit:cover}
  .body{padding:12px}
  .name{font-weight:900;color:#f39c12;margin:0 0 6px 0;font-size:14px}
  .row{display:flex;justify-content:space-between;gap:10px;align-items:center;margin-top:10px}
  @media (max-width: 980px){.cards{grid-template-columns:repeat(2,minmax(0,1fr))}}
  @media (max-width: 520px){.cards{grid-template-columns:1fr}}
</style>

<div class="section">
  <h2>⭐ Popüler Boğalar</h2>

  <?php if (!empty($error)): ?>
    <div class="errorbox"><?= $h((string)$error) ?></div>
  <?php endif; ?>

  <div class="cards">
    <?php foreach (($bulls ?? []) as $b): ?>
      <?php
        $id = (int)($b['id'] ?? 0);
        $nm = (string)($b['name'] ?? 'boga');
        $href = $id . '-' . tr_slug_pop($nm);
        $img = !empty($b['image']) ? url('uploads/bulls/' . basename((string)$b['image'])) : '';
      ?>
      <div class="card">
        <a href="<?= $h($href) ?>" style="text-decoration:none;color:inherit;">
          <div class="imgbox">
            <?php if ($img !== ''): ?><img src="<?= $h($img) ?>" alt="<?= $h($nm) ?>"><?php else: ?><span class="muted">Fotoğraf yok</span><?php endif; ?>
          </div>
          <div class="body">
            <div class="name"><?= $h($nm) ?></div>
            <div class="muted"><?= $h((string)($b['owner_name'] ?? '-')) ?></div>
            <div class="muted"><?= $h((string)($b['city'] ?? '-')) ?> / <?= $h((string)($b['district'] ?? '-')) ?></div>
            <div class="row">
              <span class="muted">👁 <?= (int)($b['views'] ?? 0) ?></span>
              <form method="post" action="<?= $h($comparePost) ?>" style="display:inline;">
                <input type="hidden" name="csrf" value="<?= $h(csrf_token()) ?>">
                <input type="hidden" name="action" value="add">
                <input type="hidden" name="id" value="<?= (int)$id ?>">
                <input type="hidden" name="return" value="<?= $h(url('karsilastir')) ?>">
                <button class="btn orange" type="submit">+ Karşılaştır</button>
              </form>
            </div>
          </div>
        </a>
      </div>
    <?php endforeach; ?>

    <?php if (empty($bulls)): ?>
      <div class="muted">Henüz popüler boğa verisi yok.</div>
    <?php endif; ?>
  </div>
</div>
