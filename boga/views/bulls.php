<?php
$h = static function (?string $v): string {
    if (function_exists('e')) return e($v ?? '');
    return htmlspecialchars($v ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
};

function tr_slug_bulls(string $s): string
{
    $map = ['Ç'=>'c','ç'=>'c','Ğ'=>'g','ğ'=>'g','İ'=>'i','ı'=>'i','Ö'=>'o','ö'=>'o','Ş'=>'s','ş'=>'s','Ü'=>'u','ü'=>'u'];
    $s = strtr($s, $map);
    $s = strtolower($s);
    $s = preg_replace('~[^a-z0-9]+~', '-', $s) ?? '';
    $s = trim($s, '-');
    return $s !== '' ? $s : 'boga';
}

$q = trim((string)($_GET['q'] ?? ''));
$city = trim((string)($_GET['city'] ?? ''));
$district = trim((string)($_GET['district'] ?? ''));
$neighborhood = trim((string)($_GET['neighborhood'] ?? ''));

$hasNeighborhoodSelect = !empty($neighborhoods);
?>

<style>
  .section{margin-top:18px;background:#0d1117;border:1px solid rgba(255,255,255,0.06);border-radius:18px;padding:16px}
  .section h2{margin:0 0 12px 0;font-size:16px;display:flex;align-items:center;gap:10px;border-left:4px solid #f39c12;padding-left:12px;color:#fff}
  .btn{display:inline-block;padding:10px 12px;border-radius:10px;text-decoration:none;font-weight:800;font-size:13px;border:1px solid rgba(255,255,255,0.12);background:rgba(255,255,255,0.06);color:#fff;cursor:pointer}
  .btn.primary{background:rgba(243,156,18,0.20);border-color:rgba(243,156,18,0.35);color:#f39c12}
  .muted{color:#8b949e;font-size:12px}
  .errorbox{background:#da3633;border-radius:14px;padding:12px;margin-top:12px;border:1px solid rgba(255,255,255,0.10)}
  .filters{display:grid;grid-template-columns:1.2fr 1fr 1fr 1fr auto;gap:10px;align-items:end}
  @media (max-width: 980px){.filters{grid-template-columns:1fr 1fr;}}
  label{display:block;font-size:12px;color:#8b949e;margin-bottom:6px}
  input,select{width:100%;padding:10px 12px;border-radius:12px;border:1px solid #30363d;background:#161b22;color:#fff;outline:none}
  .cards{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:12px;margin-top:12px}
  @media (max-width: 980px){.cards{grid-template-columns:repeat(2,minmax(0,1fr));}}
  @media (max-width: 420px){.cards{grid-template-columns:1fr;}}
  .card{background:#161b22;border:1px solid #30363d;border-radius:14px;overflow:hidden;text-decoration:none;color:inherit;display:block;transition:.2s}
  .card:hover{transform:translateY(-2px);border-color:rgba(243,156,18,0.55)}
  .imgbox{height:140px;background:#0b0e14;display:flex;align-items:center;justify-content:center;overflow:hidden}
  .card img{width:100%;height:100%;object-fit:cover}
  .body{padding:12px}
  .name{font-weight:900;color:#f39c12;margin:0 0 6px 0;font-size:14px}
</style>

<div class="section">
  <h2>🐂 Boğalar</h2>

  <?php if (!empty($error)): ?>
    <div class="errorbox"><?= $h((string)$error) ?></div>
  <?php endif; ?>

  <form method="get" action="">
    <div class="filters">
      <div>
        <label>Arama</label>
        <input type="text" name="q" value="<?= $h($q) ?>" placeholder="Boğa adı / sahibi">
      </div>

      <div>
        <label>İl</label>
        <select name="city" onchange="this.form.submit()">
          <option value="">Tümü</option>
          <?php foreach (($cities ?? []) as $c): ?>
            <?php $val = (string)($c['value'] ?? ''); $lab = (string)($c['label'] ?? ''); ?>
            <option value="<?= $h($val) ?>" <?= ($val !== '' && $val === $city) ? 'selected' : '' ?>><?= $h($lab) ?></option>
          <?php endforeach; ?>
        </select>
      </div>

      <div>
        <label>İlçe</label>
        <select name="district" <?= $city === '' ? 'disabled' : '' ?> onchange="this.form.submit()">
          <option value="">Tümü</option>
          <?php foreach (($districts ?? []) as $d): ?>
            <?php $val = (string)($d['value'] ?? ''); $lab = (string)($d['label'] ?? ''); ?>
            <option value="<?= $h($val) ?>" <?= ($val !== '' && $val === $district) ? 'selected' : '' ?>><?= $h($lab) ?></option>
          <?php endforeach; ?>
        </select>
      </div>

      <div>
        <label>Köy / Mahalle</label>
        <?php if ($hasNeighborhoodSelect): ?>
          <select name="neighborhood" <?= $district === '' ? 'disabled' : '' ?>>
            <option value="">Tümü</option>
            <?php foreach (($neighborhoods ?? []) as $n): ?>
              <?php $val = (string)($n['value'] ?? ''); $lab = (string)($n['label'] ?? ''); ?>
              <option value="<?= $h($val) ?>" <?= ($val !== '' && $val === $neighborhood) ? 'selected' : '' ?>><?= $h($lab) ?></option>
            <?php endforeach; ?>
          </select>
        <?php else: ?>
          <input type="text" name="neighborhood" value="<?= $h($neighborhood) ?>" placeholder="(Opsiyonel) Köy/Mahalle yaz">
        <?php endif; ?>
      </div>

      <div style="display:flex;gap:10px;flex-wrap:wrap">
        <button class="btn primary" type="submit">Filtrele</button>
        <a class="btn" href="bogalar">Temizle</a>
      </div>
    </div>
  </form>

  <div class="cards">
    <?php foreach (($bulls ?? []) as $b): ?>
      <?php
        $id = (int)($b['id'] ?? 0);
        $nm = (string)($b['name'] ?? 'boga');
        $href = function_exists('canonical_bull_path')
          ? canonical_bull_path($id, $nm)
          : ($id . '-' . tr_slug_bulls($nm));

        $img = '';
        if (!empty($b['image'])) {
          $img = function_exists('url')
            ? url('uploads/bulls/' . basename((string)$b['image']))
            : ('uploads/bulls/' . basename((string)$b['image']));
        }
      ?>
      <a class="card" href="<?= $h($href) ?>" aria-label="<?= $h($nm . ' detay') ?>">
        <div class="imgbox">
          <?php if ($img !== ''): ?>
            <img src="<?= $h($img) ?>" alt="<?= $h($nm) ?>">
          <?php else: ?>
            <span class="muted">Fotoğraf yok</span>
          <?php endif; ?>
        </div>
        <div class="body">
          <div class="name"><?= $h($nm) ?></div>
          <div class="muted">Sahibi: <?= $h((string)($b['owner_name'] ?? '-')) ?></div>
          <div class="muted">Konum: <?= $h((string)($b['city'] ?? '-')) ?> / <?= $h((string)($b['district'] ?? '-')) ?></div>
        </div>
      </a>
    <?php endforeach; ?>

    <?php if (empty($bulls)): ?>
      <div class="muted">Kayıt bulunamadı.</div>
    <?php endif; ?>
  </div>
</div>
