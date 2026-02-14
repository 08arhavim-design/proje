<?php
/**
 * Değişkenler index.php'den gelir:
 * $stats, $bulls_latest, $bulls_popular, $users_latest, $matches_latest, $events_upcoming, $online_users
 */

// Yardımcı fonksiyonlar
if (!function_exists('fmt_date_tr_home')) {
    function fmt_date_tr_home($d){ return $d ? date('d.m.Y', strtotime($d)) : '-'; }
}

if (!function_exists('tr_slug_home')) {
    function tr_slug_home($s){ 
        $map=['Ç'=>'c','ç'=>'c','Ğ'=>'g','ğ'=>'g','İ'=>'i','ı'=>'i','Ö'=>'o','ö'=>'o','Ş'=>'s','ş'=>'s','Ü'=>'u','ü'=>'u'];
        $s = strtr($s, $map);
        $s = strtolower($s);
        $s = preg_replace('~[^a-z0-9]+~', '-', $s) ?? '';
        return trim($s, '-'); 
    }
}
?>

<style>
    .hero { background: linear-gradient(135deg, rgba(243,156,18,0.15), rgba(88,166,255,0.1)); border: 1px solid #30363d; border-radius: 18px; padding: 20px; margin-bottom: 20px; }
    .section { background:#0d1117; border:1px solid #30363d; border-radius: 18px; padding: 18px; margin-bottom: 20px; }
    .section h2 { font-size: 16px; color:#fff; border-left: 4px solid #f39c12; padding-left: 12px; margin-bottom: 15px; }
    .grid-4 { display:grid; grid-template-columns: repeat(4, 1fr); gap: 12px; }
    .card { background:#161b22; border:1px solid #30363d; border-radius: 14px; overflow:hidden; text-decoration:none; color:inherit; transition:0.2s; }
    .card:hover { border-color:#f39c12; transform:translateY(-2px); }
    .card img { width:100%; height:130px; object-fit:cover; }
    .row { background:#161b22; border:1px solid #30363d; border-radius: 12px; padding: 12px; display:flex; justify-content:space-between; align-items:center; margin-bottom: 8px; }
    .stat-box { background:#161b22; border:1px solid #30363d; border-radius: 12px; padding: 12px; text-align:center; }
    .muted { color:#8b949e; font-size: 12px; }
    .tag { padding: 4px 8px; border-radius: 20px; font-size: 11px; background:rgba(88,166,255,0.1); color:#58a6ff; border:1px solid rgba(88,166,255,0.2); }
    @media (max-width: 980px) { .grid-4 { grid-template-columns: 1fr 1fr; } }
    @media (max-width: 480px) { .grid-4 { grid-template-columns: 1fr; } .row { flex-direction: column; align-items: flex-start; gap: 10px; } }
</style>

<div class="hero">
    <h1 style="margin:0; font-size:22px; color:#fff;">Boğa Spor Kayıt Sistemi</h1>
    <p class="muted">Güncel boğa kayıtları ve güreş takvimine hoş geldiniz.</p>
</div>



<div class="section">
    <h2>⭐ Popüler Boğalar</h2>
    <div class="grid-4">
        <?php if(!empty($bulls_popular)): foreach($bulls_popular as $b): ?>
            <a href="<?= url('bull.php?id='.$b['id'].'&slug='.tr_slug_home($b['name'])) ?>" class="card">
                <div style="height:130px; background:#000; overflow:hidden;">
                    <?php if(!empty($b['image'])): ?>
                        <img src="<?= url('uploads/bulls/'.basename($b['image'])) ?>" alt="<?= e($b['name']) ?>">
                    <?php else: ?>
                        <div style="height:100%; display:flex; align-items:center; justify-content:center;" class="muted">Görsel Yok</div>
                    <?php endif; ?>
                </div>
                <div style="padding:10px;">
                    <div style="font-weight:700; color:#fff; font-size:14px;"><?= e($b['name']) ?></div>
                    <div class="muted"><?= e($b['city'] ?? '-') ?> / <?= e($b['district'] ?? '-') ?></div>
                </div>
            </a>
        <?php endforeach; else: ?>
            <div class="muted">Henüz popüler boğa kaydı yok.</div>
        <?php endif; ?>
    </div>
</div>

<div class="section">
  <h2>📅 Yaklaşan Boğa Güreşleri</h2>
  <?php if(!empty($events_upcoming)): foreach($events_upcoming as $e): ?>
    <div class="row">
      <div>
        <div style="font-weight:700; color:#fff;"><?= e($e['title']) ?></div>
        <div class="muted"><?= e($e['city']) ?> / <?= e($e['district'] ?? '') ?></div>
      </div>
      <span class="tag"><?= fmt_date_tr_home($e['event_date']) ?></span>
    </div>
  <?php endforeach; else: ?>
    <div class="muted">Takvimde yaklaşan güreş kaydı bulunmuyor.</div>
  <?php endif; ?>
</div>

<div class="section">
  <h2>🤼 Son Güreş Kayıtları</h2>
  <?php if(!empty($matches_latest)): foreach($matches_latest as $m): ?>
    <div class="row">
      <div>
        <div style="font-weight:700; color:#f39c12;"><?= e($m['bull_name'] ?? '-') ?></div>
        <div class="muted">Arena: <?= e($m['arena_name'] ?? '-') ?></div>
      </div>
      <div style="text-align:right;">
        <div class="tag">Güreş Tarihi: <?= e($m['match_date']) ?></div>
        <div class="muted" style="margin-top:4px;"><?= e($m['result_text'] ?? ($m['opponent_text'] ?? 'Sonuç girilmedi')) ?></div>
      </div>
    </div>
  <?php endforeach; else: ?>
    <div class="muted">Henüz güreş kaydı bulunmuyor.</div>
  <?php endif; ?>
</div>

<div class="section">
    <h2>📊 Genel İstatistikler</h2>
    <div class="grid-4">
        <div class="stat-box"><div class="muted">Boğa</div><div style="font-size:18px; font-weight:800; color:#f39c12;"><?= (int)($stats['bulls'] ?? 0) ?></div></div>
        <div class="stat-box"><div class="muted">Üye</div><div style="font-size:18px; font-weight:800; color:#f39c12;"><?= (int)($stats['users'] ?? 0) ?></div></div>
        <div class="stat-box"><div class="muted">Güreş</div><div style="font-size:18px; font-weight:800; color:#f39c12;"><?= (int)($stats['matches'] ?? 0) ?></div></div>
        <div class="stat-box"><div class="muted">Arena</div><div style="font-size:18px; font-weight:800; color:#f39c12;"><?= (int)($stats['arenas'] ?? 0) ?></div></div>
    </div>
</div>

<div class="section">
    <h2>👥 Son Kayıtlı Üyeler</h2>
    <?php if(!empty($users_latest)): foreach($users_latest as $u): ?>
        <div class="row">
            <div>
                <div style="font-weight:700; color:#fff;"><?= e($u['full_name'] ?: $u['username']) ?></div>
                <div class="muted">Kullanıcı: <?= e($u['username']) ?></div>
            </div>
            <div class="muted">Kayıt: <?= substr($u['created_at'], 0, 10) ?></div>
        </div>
    <?php endforeach; else: ?>
        <div class="muted">Üye kaydı bulunmuyor.</div>
    <?php endif; ?>
</div>

<script>
    // Kullanıcı aktifliğini bildirmek için ping gönder
    async function ping(){ try{ await fetch("<?= url('app/online.php') ?>"); }catch(e){} }
    setInterval(ping, 30000);
    ping();
</script>

<div class="section">
    <h2>🟢 Çevrimiçi Üyeler</h2>
    <div style="display:flex; flex-wrap:wrap; gap:10px;">
        <?php if(!empty($online_users)): foreach($online_users as $ou): ?>
            <div class="row" style="margin:0; flex:1 1 200px;">
                <div>
                    <div style="font-weight:700; color:#f39c12; font-size:14px;"><?= e($ou['full_name'] ?: $ou['username']) ?></div>
                    <div class="muted"><?= e($ou['role'] ?: 'Üye') ?></div>
                </div>
<a href="<?= url('mesaj/'.$ou['id']) ?>" class="tag" style="text-decoration:none; font-weight:bold;">Mesaj Gönder</a>            </div>
        <?php endforeach; else: ?>
            <div class="muted">Şu an aktif üye bulunmuyor.</div>
        <?php endif; ?>
    </div>
</div>