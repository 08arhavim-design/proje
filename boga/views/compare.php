<?php
// File: /boga/views/compare.php
// Expects: $bulls (array), $popular (array), $error (string|null)

$h = static function ($v): string {
    if (function_exists('e')) return e($v);
    return htmlspecialchars((string)($v ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
};

function tr_slug_cmp(string $s): string {
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
  .cards{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:12px}
  .card{background:#161b22;border:1px solid #30363d;border-radius:14px;overflow:hidden}
  .imgbox{height:130px;background:#0b0e14;display:flex;align-items:center;justify-content:center;overflow:hidden}
  .card img{width:100%;height:100%;object-fit:cover}
  .body{padding:12px}
  .name{font-weight:900;color:#f39c12;margin:0 0 6px 0;font-size:14px}
  .row{display:flex;justify-content:space-between;gap:10px;align-items:center;margin-top:10px}
  table{width:100%;border-collapse:collapse;margin-top:10px}
  th,td{padding:10px 8px;border-bottom:1px solid rgba(255,255,255,0.10);text-align:left;font-size:13px;vertical-align:top}
  .addbar{display:flex;gap:10px;flex-wrap:wrap;align-items:center}
  input[type="number"]{padding:10px 12px;border-radius:10px;border:1px solid rgba(255,255,255,0.12);background:rgba(255,255,255,0.06);color:#fff;min-width:140px}
  @media (max-width: 980px){.cards{grid-template-columns:repeat(2,minmax(0,1fr))}}
  @media (max-width: 520px){.cards{grid-template-columns:1fr}}
</style>

<div class="section">
  <h2>⚖️ Karşılaştır</h2>

  <?php if (!empty($error)): ?>
    <div class="errorbox"><?= $h((string)$error) ?></div>
  <?php endif; ?>

  <div class="addbar">
    <form method="post" action="<?= $h($comparePost) ?>" style="display:flex;gap:10px;align-items:center;flex-wrap:wrap;">
      <input type="hidden" name="csrf" value="<?= $h(csrf_token()) ?>">
      <input type="hidden" name="action" value="add">
      <input type="hidden" name="return" value="<?= $h($_SERVER['REQUEST_URI'] ?? '') ?>">
      <input type="number" name="id" min="1" placeholder="Boğa ID" required>
      <button class="btn orange" type="submit">+ Ekle</button>
    </form>

    <form method="post" action="<?= $h($comparePost) ?>" style="display:inline;">
      <input type="hidden" name="csrf" value="<?= $h(csrf_token()) ?>">
      <input type="hidden" name="action" value="clear">
      <input type="hidden" name="return" value="<?= $h($_SERVER['REQUEST_URI'] ?? '') ?>">
      <button class="btn" type="submit">Temizle</button>
    </form>

    <div class="muted">En fazla 3 boğa seçebilirsiniz.</div>
  </div>

  <?php if (empty($bulls)): ?>
    <div class="muted" style="margin-top:10px;">Henüz boğa seçmediniz. Aşağıdan popüler boğalardan ekleyebilirsiniz.</div>
  <?php endif; ?>

  <?php if (!empty($bulls)): ?>
    <div class="cards" style="margin-top:12px;">
      <?php foreach ($bulls as $b): ?>
        <?php
          $id = (int)($b['id'] ?? 0);
          $nm = (string)($b['name'] ?? 'boga');
          $href = $id . '-' . tr_slug_cmp($nm);
          $img = !empty($b['image']) ? url('uploads/bulls/' . basename((string)$b['image'])) : '';
        ?>
        <div class="card">
          <a href="<?= $h($href) ?>" style="text-decoration:none;color:inherit;">
            <div class="imgbox">
              <?php if ($img !== ''): ?><img src="<?= $h($img) ?>" alt="<?= $h($nm) ?>"><?php else: ?><span class="muted">Fotoğraf yok</span><?php endif; ?>
            </div>
            <div class="body">
              <div class="name"><?= $h($nm) ?></div>
              <div class="muted"><?= $h((string)($b['owner_name'] ?? '-')) ?> • <?= $h((string)($b['city'] ?? '-')) ?> / <?= $h((string)($b['district'] ?? '-')) ?></div>
              <div class="muted">👁 <?= (int)($b['views'] ?? 0) ?></div>
            </div>
          </a>

          <div class="body" style="padding-top:0;">
            <form method="post" action="<?= $h($comparePost) ?>">
              <input type="hidden" name="csrf" value="<?= $h(csrf_token()) ?>">
              <input type="hidden" name="action" value="remove">
              <input type="hidden" name="id" value="<?= (int)$id ?>">
              <input type="hidden" name="return" value="<?= $h($_SERVER['REQUEST_URI'] ?? '') ?>">
              <button class="btn" type="submit">− Çıkar</button>
            </form>
          </div>
        </div>
      <?php endforeach; ?>
    </div>

    <?php
      $fields = [
        'Sahibi' => static fn($b) => (string)($b['owner_name'] ?? ''),
        'Konum' => static fn($b) => trim((string)($b['city'] ?? '') . ' / ' . (string)($b['district'] ?? '')),
        'Irk' => static fn($b) => (string)($b['breed'] ?? ''),
        'Yaş' => static fn($b) => (string)($b['age'] ?? ''),
        'Kilo' => static fn($b) => (string)($b['weight'] ?? ''),
        'Kategori' => static fn($b) => (string)($b['category_name'] ?? ''),
        'Görüntülenme' => static fn($b) => (string)($b['views'] ?? ''),
        'Kayıt' => static fn($b) => (string)($b['created_at'] ?? ''),
      ];

      // hide empty rows
      $rows = [];
      foreach ($fields as $label => $fn) {
        $has = false;
        foreach ($bulls as $b) { if (trim((string)$fn($b)) !== '' && trim((string)$fn($b)) !== '0') { $has = true; break; } }
        if ($has) $rows[$label] = $fn;
      }
    ?>

    <div style="margin-top:16px;">
      <h2 style="margin:0 0 10px 0;font-size:16px;">📊 Karşılaştırma Tablosu</h2>
      <table>
        <thead>
          <tr>
            <th>Özellik</th>
            <?php foreach ($bulls as $b): ?>
              <th><?= $h((string)($b['name'] ?? '-')) ?></th>
            <?php endforeach; ?>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($rows as $label => $fn): ?>
            <tr>
              <td style="color:#8b949e;font-weight:900;"><?= $h($label) ?></td>
              <?php foreach ($bulls as $b): ?>
                <td><?= $h((string)$fn($b)) ?></td>
              <?php endforeach; ?>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>
</div>

<div class="section">
  <h2>⭐ Popüler Boğalardan Ekle</h2>
  <div class="cards">
    <?php foreach (($popular ?? []) as $b): ?>
      <?php
        $id = (int)($b['id'] ?? 0);
        $nm = (string)($b['name'] ?? 'boga');
        $img = !empty($b['image']) ? url('uploads/bulls/' . basename((string)$b['image'])) : '';
      ?>
      <div class="card">
        <div class="imgbox">
          <?php if ($img !== ''): ?><img src="<?= $h($img) ?>" alt="<?= $h($nm) ?>"><?php else: ?><span class="muted">Fotoğraf yok</span><?php endif; ?>
        </div>
        <div class="body">
          <div class="name"><?= $h($nm) ?></div>
          <div class="muted"><?= $h((string)($b['owner_name'] ?? '-')) ?> • <?= $h((string)($b['city'] ?? '-')) ?> / <?= $h((string)($b['district'] ?? '-')) ?></div>
          <div class="row">
            <span class="muted">👁 <?= (int)($b['views'] ?? 0) ?></span>
            <form method="post" action="<?= $h($comparePost) ?>" style="display:inline;">
              <input type="hidden" name="csrf" value="<?= $h(csrf_token()) ?>">
              <input type="hidden" name="action" value="add">
              <input type="hidden" name="id" value="<?= (int)$id ?>">
              <input type="hidden" name="return" value="<?= $h($_SERVER['REQUEST_URI'] ?? '') ?>">
              <button class="btn orange" type="submit">+ Ekle</button>
            </form>
          </div>
        </div>
      </div>
    <?php endforeach; ?>

    <?php if (empty($popular)): ?>
      <div class="muted">Henüz popüler boğa verisi yok.</div>
    <?php endif; ?>
  </div>
</div>
