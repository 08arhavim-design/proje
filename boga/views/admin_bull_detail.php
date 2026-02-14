<?php
// File: /boga/views/admin_bull_detail.php
// Expects: $bull, $matches, $error (opsiyonel)

$error = $error ?? null;

$h = static function (?string $v): string {
  if (function_exists('e')) return e($v ?? '');
  return htmlspecialchars($v ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
};

$fmtDate = static function ($d): string {
  return $d ? date('d.m.Y', strtotime((string)$d)) : '-';
};

$pick = static function (array $keys) use ($bull): string {
  if (empty($bull) || !is_array($bull)) return '';
  foreach ($keys as $k) {
    $v = (string)($bull[$k] ?? '');
    if (trim($v) !== '' && $v !== '0') return trim($v);
  }
  return '';
};

$id = (int)($bull['id'] ?? 0);

$imgRel = '';
$p = (string)($bull['image'] ?? $bull['photo_path'] ?? '');
if ($p !== '') $imgRel = 'uploads/bulls/' . rawurlencode(basename($p));
$imgUrl = function_exists('url') ? url($imgRel) : $imgRel;

$created = '-';
$ca = (string)($bull['created_at'] ?? '');
if ($ca !== '' && $ca !== '0000-00-00 00:00:00') $created = substr($ca, 0, 10);

$age = (int)($bull['age'] ?? 0);
$info = [
  'Sahibi'      => $pick(['owner_name']),
  'Irk'         => $pick(['breed']),
  'Yaş'         => $age > 0 ? (string)$age : '',
  'Kilo'        => $pick(['weight']),
  'Kategori'    => $pick(['category_name', 'last_category']),
  'Asıl Sahip'  => $pick(['original_owner']),
  'Ana'         => $pick(['mother_name', 'mother']),
  'Baba'        => $pick(['father_name', 'father']),
  'Son Arena'   => $pick(['last_arena_text', 'last_arena']),
  'Son Rakip'   => $pick(['last_opponent_text', 'last_opponent']),
  'Fiyat'       => $pick(['price']),
];

$blocks = [
  'Şampiyonluklar'      => $pick(['championships']),
  'Arenalar'            => $pick(['arenas']),
  'Kazandığı Ünvanlar'  => $pick(['won_titles', 'wins_text']),
];
?>

<style>
  .btn{display:inline-block;padding:10px 12px;border-radius:10px;text-decoration:none;font-weight:800;font-size:13px;border:1px solid rgba(255,255,255,0.12);background:rgba(255,255,255,0.06);color:#fff}
  .tag{display:inline-block;padding:4px 8px;border-radius:999px;font-size:11px;font-weight:900;background:rgba(88,166,255,0.12);border:1px solid rgba(88,166,255,0.25);color:#58a6ff;white-space:nowrap}
  .muted{color:#8b949e;font-size:12px}
  .errorbox{background:#da3633;border-radius:14px;padding:12px;margin-top:12px;border:1px solid rgba(255,255,255,0.10)}
  .wrap{max-width:1100px;margin:0 auto}
  .card{background:#0d1117;border:1px solid rgba(255,255,255,0.06);border-radius:18px;padding:16px}
  .grid{display:grid;grid-template-columns:1fr;gap:14px}
  @media (min-width: 900px){.grid{grid-template-columns:420px 1fr}}
  .img{width:100%;aspect-ratio:16/11;object-fit:cover;border-radius:14px;background:#0b0e14}
  .kv{display:grid;grid-template-columns:140px 1fr;gap:8px 12px;margin-top:10px}
  .kv .k{color:#8b949e;font-size:12px}
  .kv .v{font-weight:900}
  .block{background:#161b22;border:1px solid #30363d;border-radius:14px;padding:12px;margin-top:10px}
  .block .t{font-weight:900;margin:0 0 6px 0}
  .block .c{white-space:pre-wrap;font-size:13px;color:#c9d1d9}
  table{width:100%;border-collapse:collapse;margin-top:10px}
  th,td{padding:10px 8px;border-bottom:1px solid rgba(255,255,255,0.10);text-align:left;font-size:13px}
</style>

<div class="wrap">
  <div style="display:flex;justify-content:space-between;align-items:center;margin:10px 0 12px 0;">
    <a class="btn" href="<?= function_exists('url') ? url('admin/bogalar') : '/boga/admin/bulls.php' ?>">← Tüm Boğalar</a>
    <span class="tag">Admin • Boğa Detayı</span>
  </div>

  <?php if (!empty($error)): ?>
    <div class="errorbox"><?= $h((string)$error) ?></div>
  <?php endif; ?>

  <?php if (!empty($bull) && is_array($bull)): ?>
    <div class="card">
      <div class="grid">
        <div>
          <?php if ($imgRel !== ''): ?>
            <img class="img" src="<?= $h($imgUrl) ?>" alt="<?= $h((string)($bull['name'] ?? '')) ?>">
          <?php else: ?>
            <div class="img" aria-label="Fotoğraf yok"></div>
          <?php endif; ?>
        </div>

        <div>
          <h1 style="margin:0 0 6px 0;font-size:22px;"><?= $h((string)($bull['name'] ?? '')) ?></h1>
          <div class="muted">Kayıt: <?= $h($created) ?></div>

          <div class="kv">
            <?php foreach ($info as $k => $v): ?>
              <?php if (trim((string)$v) === '') continue; ?>
              <div class="k"><?= $h($k) ?></div>
              <div class="v"><?= $h((string)$v) ?></div>
            <?php endforeach; ?>
          </div>

          <?php foreach ($blocks as $t => $c): ?>
            <?php if (trim((string)$c) === '' || $c === '-') continue; ?>
            <div class="block">
              <div class="t"><?= $h($t) ?></div>
              <div class="c"><?= $h((string)$c) ?></div>
            </div>
          <?php endforeach; ?>
        </div>
      </div>

      <?php if (!empty($matches) && is_array($matches)): ?>
        <div style="margin-top:14px;">
          <h2 style="margin:0 0 8px 0;font-size:16px;">Güreş Kayıtları</h2>
          <table>
            <thead><tr><th>Tarih</th><th>Arena</th><th>Rakip</th><th>Sonuç</th></tr></thead>
            <tbody>
              <?php foreach ($matches as $m): ?>
                <tr>
                  <td><?= $h($fmtDate($m['match_date'] ?? '')) ?></td>
                  <td><?= $h((string)($m['arena_name'] ?? '-')) ?></td>
                  <td><?= $h((string)($m['opponent_text'] ?? '-')) ?></td>
                  <td><?= $h((string)($m['result_text'] ?? '-')) ?></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php endif; ?>
    </div>
  <?php endif; ?>
</div>
