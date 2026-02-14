<?php
// File: /boga/views/bull_detail.php
// Expects: $bull, $matches, $error, $views_count (int), $is_favorite (bool), $in_compare (bool)

$error       = $error ?? null;
$views_count = (int)($views_count ?? 0);
$is_favorite = (bool)($is_favorite ?? false);
$in_compare  = (bool)($in_compare ?? false);

$h = static function ($v): string {
    if (function_exists('e')) return e((string)$v);
    return htmlspecialchars((string)($v ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
};

$fmtDate = static function ($d): string {
    return $d ? date('d.m.Y', strtotime((string)$d)) : '-';
};

$pick = static function (array $keys) use ($bull): string {
    if (empty($bull) || !is_array($bull)) return '';
    foreach ($keys as $k) {
        $v = trim((string)($bull[$k] ?? ''));
        if ($v !== '' && $v !== '0') return $v;
    }
    return '';
};

$id = (int)($bull['id'] ?? 0);

$imgRel = '';
if (!empty($bull) && is_array($bull)) {
    $p = (string)($bull['image'] ?? $bull['photo_path'] ?? '');
    if ($p !== '') $imgRel = 'uploads/bulls/' . rawurlencode(basename($p));
}
$imgUrl = function_exists('url') ? url($imgRel) : $imgRel;

$created = '-';
$ca = (string)($bull['created_at'] ?? '');
if ($ca !== '' && $ca !== '0000-00-00 00:00:00') $created = substr($ca, 0, 10);

$age = (int)($bull['age'] ?? 0);

$info = [
    'Boğa ID'      => $id > 0 ? (string)$id : '',
    'Sahibi'       => $pick(['owner_name']),
    'Irk'          => $pick(['breed']),
    'Yaş'          => $age > 0 ? (string)$age : '',
    'Kilo'         => $pick(['weight']),
    'Kategori'     => $pick(['category_name', 'last_category']),
    'Asıl Sahip'   => $pick(['original_owner']),
    'Ana'          => $pick(['mother_name', 'mother']),
    'Baba'         => $pick(['father_name', 'father']),
    'Son Arena'    => $pick(['last_arena_text', 'last_arena']),
    'Son Rakip'    => $pick(['last_opponent_text', 'last_opponent']),
    'Fiyat'        => $pick(['price']),
    'Görüntülenme' => $views_count > 0 ? (string)$views_count : '',
];

$blocks = [
    'Şampiyonluklar'     => $pick(['championships']),
    'Arenalar'           => $pick(['arenas']),
    'Kazandığı Ünvanlar' => $pick(['won_titles', 'wins_text']),
];

// Match stats
$total = is_array($matches) ? count($matches) : 0;
$wins = 0; $losses = 0; $draws = 0; $unknown = 0;
if (!empty($matches) && is_array($matches)) {
    foreach ($matches as $m) {
        $r = trim(mb_strtolower((string)($m['result_text'] ?? ''), 'UTF-8'));
        if ($r === '') { $unknown++; continue; }
        $isWin  = (mb_strpos($r, 'kaz') !== false) || (mb_strpos($r, 'galib') !== false) || (mb_strpos($r, 'yendi') !== false);
        $isLoss = (mb_strpos($r, 'kay') !== false) || (mb_strpos($r, 'mağlub') !== false) || (mb_strpos($r, 'maglub') !== false) || (mb_strpos($r, 'yenildi') !== false);
        $isDraw = (mb_strpos($r, 'berab') !== false) || (mb_strpos($r, 'draw') !== false);

        if ($isWin) $wins++;
        elseif ($isLoss) $losses++;
        elseif ($isDraw) $draws++;
        else $unknown++;
    }
}

// Share
$scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host = (string)($_SERVER['HTTP_HOST'] ?? '');
$uri  = (string)($_SERVER['REQUEST_URI'] ?? '');
$currentUrl = ($host !== '') ? ($scheme . '://' . $host . $uri) : $uri;
$waUrl = 'https://wa.me/?text=' . rawurlencode($currentUrl);

// Endpoints (SEO uyumlu)
$homeHref    = function_exists('url') ? url('') : 'index.php';
$loginHref   = function_exists('url') ? url('giris') : 'login.php';
$favPost     = function_exists('url') ? url('favori') : 'favorite.php';
$comparePost = function_exists('url') ? url('karsilastir-islem') : 'karsilastir-islem';
$compareHref = function_exists('url') ? url('karsilastir') : 'karsilastir';

$csrf = function_exists('csrf_token') ? (string)csrf_token() : '';
?>

<style>
  .btn{display:inline-block;padding:10px 12px;border-radius:10px;text-decoration:none;font-weight:800;font-size:13px;border:1px solid rgba(255,255,255,0.12);background:rgba(255,255,255,0.06);color:#fff;cursor:pointer}
  .btn.green{background:rgba(35,134,54,0.20);border-color:rgba(35,134,54,0.35);color:#2ecc71}
  .btn.orange{background:rgba(243,156,18,0.18);border-color:rgba(243,156,18,0.35);color:#f39c12}
  .btn.red{background:rgba(218,54,51,0.16);border-color:rgba(218,54,51,0.38);color:#ffb3b0}
  .tag{display:inline-block;padding:4px 8px;border-radius:999px;font-size:11px;font-weight:900;background:rgba(88,166,255,0.12);border:1px solid rgba(88,166,255,0.25);color:#58a6ff;white-space:nowrap}
  .muted{color:#8b949e;font-size:12px}
  .errorbox{background:#da3633;border-radius:14px;padding:12px;margin-top:12px;border:1px solid rgba(255,255,255,0.10)}

  .wrap{max-width:1100px;margin:0 auto}
  .card{background:#0d1117;border:1px solid rgba(255,255,255,0.06);border-radius:18px;padding:16px}
  .grid{display:grid;grid-template-columns:1fr;gap:14px}
  @media (min-width: 900px){.grid{grid-template-columns:420px 1fr}}
  .img{width:100%;aspect-ratio:16/11;object-fit:cover;border-radius:14px;background:#0b0e14}

  .kv{display:grid;grid-template-columns:160px 1fr;gap:8px 12px;margin-top:10px}
  .kv .k{color:#8b949e;font-size:12px}
  .kv .v{font-weight:900}

  .actions{display:flex;gap:10px;flex-wrap:wrap;margin-top:12px}
  .block{background:#161b22;border:1px solid #30363d;border-radius:14px;padding:12px;margin-top:10px}
  .block .t{font-weight:900;margin:0 0 6px 0}
  .block .c{white-space:pre-wrap;font-size:13px;color:#c9d1d9}

  .stats{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:10px;margin:12px 0}
  .sbox{background:#161b22;border:1px solid #30363d;border-radius:14px;padding:12px}
  .sbox .k{color:#8b949e;font-size:12px}
  .sbox .v{font-size:18px;font-weight:900;margin-top:6px}
  @media (max-width: 900px){.stats{grid-template-columns:repeat(2,minmax(0,1fr))}}

  table{width:100%;border-collapse:collapse;margin-top:10px}
  th,td{padding:10px 8px;border-bottom:1px solid rgba(255,255,255,0.10);text-align:left;font-size:13px}
</style>

<div class="wrap">
  <div style="display:flex;justify-content:space-between;align-items:center;margin:10px 0 12px 0;">
    <a class="btn" href="<?= $h($homeHref) ?>">← Ana Sayfa</a>
    <span class="tag">Boğa Detayı</span>
  </div>

  <?php if (!empty($error)): ?>
    <div class="errorbox"><?= $h((string)$error) ?></div>
  <?php endif; ?>

  <?php if (!empty($bull) && is_array($bull)): ?>

    <div class="stats">
      <div class="sbox"><div class="k">Toplam Maç</div><div class="v"><?= (int)$total ?></div></div>
      <div class="sbox"><div class="k">Galibiyet</div><div class="v"><?= (int)$wins ?></div></div>
      <div class="sbox"><div class="k">Mağlubiyet</div><div class="v"><?= (int)$losses ?></div></div>
      <div class="sbox"><div class="k">Berabere</div><div class="v"><?= (int)$draws ?></div></div>
    </div>

    <div class="card">
      <div class="grid">
        <div>
          <?php if ($imgRel !== ''): ?>
            <img class="img" src="<?= $h($imgUrl) ?>" alt="<?= $h((string)($bull['name'] ?? '')) ?>">
          <?php else: ?>
            <div class="img" aria-label="Fotoğraf yok"></div>
          <?php endif; ?>

          <div class="actions">
            <!-- ✅ FAVORİ (AJAX) -->
            <?php if (function_exists('is_logged_in') && is_logged_in()): ?>
              <form method="post" action="<?= $h($favPost) ?>" data-fav-form style="display:inline;">
                <input type="hidden" name="csrf" value="<?= $h($csrf) ?>">
                <input type="hidden" name="bull_id" value="<?= (int)$id ?>">
                <input type="hidden" name="return" value="<?= $h($_SERVER['REQUEST_URI'] ?? '') ?>">
                <button class="btn <?= $is_favorite ? 'red' : 'green' ?>" type="submit" data-fav-btn>
                  <?= $is_favorite ? '★ Favorilerden Çıkar' : '☆ Favorilere Ekle' ?>
                </button>
              </form>
            <?php else: ?>
              <a class="btn green" href="<?= $h($loginHref) ?>">☆ Favori için giriş yap</a>
            <?php endif; ?>

            <!-- ✅ KARŞILAŞTIR -->
            <form method="post" action="<?= $h($comparePost) ?>" style="display:inline;">
              <input type="hidden" name="csrf" value="<?= $h($csrf) ?>">
              <input type="hidden" name="id" value="<?= (int)$id ?>">
              <input type="hidden" name="action" value="<?= $in_compare ? 'remove' : 'add' ?>">
              <input type="hidden" name="return" value="<?= $h($_SERVER['REQUEST_URI'] ?? '') ?>">
              <button class="btn orange" type="submit">
                <?= $in_compare ? '− Karşılaştırmadan Çıkar' : '+ Karşılaştırmaya Ekle' ?>
              </button>
            </form>

            

            <button class="btn" type="button" id="copyLinkBtn">Linki Kopyala</button>
            <a class="btn green" target="_blank" rel="noopener" href="<?= $h($waUrl) ?>">WhatsApp</a>
          </div>
        </div>

        <div>
          <div style="display:flex;gap:10px;align-items:center;flex-wrap:wrap;">
            <h1 style="margin:0;font-size:22px;letter-spacing:.5px;"><?= $h((string)($bull['name'] ?? '')) ?></h1>
            <?php if ($id > 0): ?><span class="tag">ID: <?= (int)$id ?></span><?php endif; ?>
          </div>

          <div class="muted" style="margin-top:6px;">Kayıt: <?= $h($created) ?></div>

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
          <h2 style="margin:0 0 8px 0;font-size:16px;">Son Güreşler</h2>
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
          <?php if ($unknown > 0): ?>
            <div class="muted" style="margin-top:8px;">Not: <?= (int)$unknown ?> maç sonucu sınıflandırılamadı.</div>
          <?php endif; ?>
        </div>
      <?php endif; ?>
    </div>

    <script>
      (function(){
        var btn = document.getElementById('copyLinkBtn');
        if(!btn) return;
        btn.addEventListener('click', async function(){
          try{
            await navigator.clipboard.writeText(<?= json_encode($currentUrl) ?>);
            btn.textContent = 'Kopyalandı ✓';
            setTimeout(()=>btn.textContent='Linki Kopyala', 1200);
          }catch(e){
            prompt('Linki kopyalayın:', <?= json_encode($currentUrl) ?>);
          }
        });
      })();
    </script>

    <script>
      (function(){
        var form = document.querySelector('[data-fav-form]');
        if(!form) return;

        form.addEventListener('submit', async function(e){
          e.preventDefault();

          var btn = form.querySelector('[data-fav-btn]');
          if(btn) btn.disabled = true;

          try{
            var fd = new FormData(form);
            fd.set('ajax', '1');

            var res = await fetch(form.action, {
              method: 'POST',
              body: fd,
              headers: {'X-Requested-With':'XMLHttpRequest','Accept':'application/json'}
            });

            var data = await res.json();
            if(!data || !data.ok){
              alert((data && data.message) ? data.message : 'Hata oluştu.');
              return;
            }

            if(btn){
              btn.textContent = data.is_favorite ? '★ Favorilerden Çıkar' : '☆ Favorilere Ekle';
              btn.classList.toggle('red', !!data.is_favorite);
              btn.classList.toggle('green', !data.is_favorite);
            }

            var badge = document.getElementById('favBadge');
            if(badge && typeof data.favorites_count !== 'undefined'){
              badge.textContent = String(data.favorites_count);
              badge.style.display = (data.favorites_count > 0) ? 'inline-flex' : 'none';
            }
          }catch(err){
            alert('Favori işlemi başarısız.');
          }finally{
            if(btn) btn.disabled = false;
          }
        });
      })();
    </script>

  <?php endif; ?>
</div>
