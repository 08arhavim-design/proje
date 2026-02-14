<?php
// Değişkenler: $error, $ok, $events, $page, $pages, $total, $csrf, $editing
?>

<style>
  .box{background:#0d1117;border:1px solid rgba(255,255,255,.08);border-radius:16px;padding:16px}
  .h{display:flex;align-items:center;justify-content:space-between;gap:10px;flex-wrap:wrap}
  .muted{color:#8b949e;font-size:13px}
  .msg{margin-top:10px;padding:10px 12px;border-radius:12px}
  .msg.err{background:rgba(218,54,51,.15);border:1px solid rgba(218,54,51,.35)}
  .msg.ok{background:rgba(35,134,54,.15);border:1px solid rgba(35,134,54,.35)}
  .grid{display:grid;grid-template-columns:repeat(6,minmax(0,1fr));gap:10px;margin-top:12px}
  .grid .col2{grid-column:span 2}
  .grid .col3{grid-column:span 3}
  label{display:block;font-size:12px;color:#8b949e;margin-bottom:6px}
  input,textarea{width:100%;padding:10px 12px;border-radius:12px;border:1px solid rgba(255,255,255,.14);background:rgba(255,255,255,.06);color:#fff}
  textarea{min-height:90px;resize:vertical}
  .btn{display:inline-block;padding:10px 12px;border-radius:12px;border:1px solid rgba(255,255,255,.14);background:rgba(255,255,255,.06);color:#fff;text-decoration:none;font-weight:800;font-size:13px;cursor:pointer}
  .btn.primary{background:rgba(243,156,18,.18);border-color:rgba(243,156,18,.35);color:#f39c12}
  .btn.red{background:rgba(218,54,51,.14);border-color:rgba(218,54,51,.35);color:#ff7b72}
  .btn.gray{background:rgba(255,255,255,.05)}
  .list{margin-top:14px;display:grid;gap:10px}
  .card{background:#161b22;border:1px solid #30363d;border-radius:14px;padding:12px;display:flex;justify-content:space-between;gap:12px;flex-wrap:wrap}
  .title{font-weight:900}
  .meta{margin-top:6px;display:flex;gap:8px;flex-wrap:wrap}
  .tag{display:inline-block;padding:4px 8px;border-radius:999px;font-size:11px;font-weight:900;background:rgba(88,166,255,.12);border:1px solid rgba(88,166,255,.25);color:#58a6ff}
  .pager{margin-top:14px;display:flex;gap:8px;flex-wrap:wrap}
  @media (max-width: 980px){ .grid{grid-template-columns:repeat(2,minmax(0,1fr))} .grid .col2,.grid .col3{grid-column:auto} }
</style>

<div class="box">
  <div class="h">
    <div>
      <div style="font-size:18px;font-weight:900;">📅 Takvim Yönetimi</div>
      <div class="muted">Boğa güreşi etkinliklerini buradan ekleyip düzenleyebilirsiniz. (Sayfa başına 6 kayıt)</div>
    </div>
  </div>

  <?php if (!empty($error)): ?>
    <div class="msg err"><?php echo e($error); ?></div>
  <?php endif; ?>
  <?php if (!empty($ok)): ?>
    <div class="msg ok"><?php echo e($ok); ?></div>
  <?php endif; ?>

  <!-- ETKİNLİK EKLE / DÜZENLE -->
  <div style="margin-top:14px;padding:12px;border:1px solid rgba(255,255,255,.08);border-radius:14px;background:rgba(255,255,255,.03);">
    <div style="font-weight:900;margin-bottom:10px;">
      <?php echo $editing ? 'Etkinlik Düzenle' : 'Yeni Etkinlik Ekle'; ?>
    </div>

    <form method="post">
      <input type="hidden" name="csrf" value="<?php echo e($csrf ?? ''); ?>">
      <input type="hidden" name="action" value="<?php echo $editing ? 'update' : 'add'; ?>">
      <?php if ($editing): ?>
        <input type="hidden" name="id" value="<?php echo (int)$editing['id']; ?>">
      <?php endif; ?>

      <div class="grid">
        <div class="col2">
          <label>Tarih *</label>
          <input type="date" name="event_date" required
                 value="<?php echo e($editing['event_date'] ?? ''); ?>">
        </div>

        <div class="col2">
          <label>Saat</label>
          <input type="time" name="start_time"
                 value="<?php echo e($editing['start_time'] ?? ''); ?>">
        </div>

        <div class="col2">
          <label>Yer / Lokasyon</label>
          <input type="text" name="location" placeholder="Örn: Kafkasör"
                 value="<?php echo e($editing['location'] ?? ''); ?>">
        </div>

        <div class="col3">
          <label>Başlık *</label>
          <input type="text" name="title" required placeholder="Örn: Kafkasör Boğa Güreşleri"
                 value="<?php echo e($editing['title'] ?? ''); ?>">
        </div>

        <div class="col3">
          <label>Açıklama / Not</label>
          <textarea name="notes" placeholder="Kategori, program, detay..."><?php echo e($editing['notes'] ?? ''); ?></textarea>
        </div>
      </div>

      <div style="margin-top:10px;display:flex;gap:10px;flex-wrap:wrap;">
        <button class="btn primary" type="submit"><?php echo $editing ? 'Güncelle' : 'Etkinlik Ekle'; ?></button>
        <?php if ($editing): ?>
          <a class="btn gray" href="/boga/admin/events.php">Vazgeç</a>
        <?php endif; ?>
      </div>
    </form>
  </div>

  <!-- LİSTE -->
  <div style="margin-top:16px;font-weight:900;">Kayıtlar (<?php echo (int)($total ?? 0); ?>)</div>

  <div class="list">
    <?php if (empty($events)): ?>
      <div class="muted">Henüz etkinlik yok veya veri çekilemedi.</div>
    <?php else: ?>
      <?php foreach ($events as $ev): ?>
        <div class="card">
          <div>
            <div class="title"><?php echo e($ev['title'] ?? ''); ?></div>
            <div class="meta">
              <span class="tag">📅 <?php echo e($ev['event_date'] ?? ''); ?></span>
              <?php if (!empty($ev['start_time'])): ?>
                <span class="tag">🕒 <?php echo e(substr((string)$ev['start_time'], 0, 5)); ?></span>
              <?php endif; ?>
              <?php if (!empty($ev['location'])): ?>
                <span class="tag">📍 <?php echo e($ev['location']); ?></span>
              <?php endif; ?>
            </div>
            <?php if (!empty($ev['notes'])): ?>
              <div class="muted" style="margin-top:8px;white-space:pre-wrap;"><?php echo e($ev['notes']); ?></div>
            <?php endif; ?>
          </div>

          <div style="display:flex;gap:10px;flex-wrap:wrap;align-items:flex-start;">
            <a class="btn" href="/boga/admin/events.php?edit=<?php echo (int)$ev['id']; ?>">Düzenle</a>

            <form method="post" onsubmit="return confirm('Bu etkinliği silmek istiyor musunuz?');">
              <input type="hidden" name="csrf" value="<?php echo e($csrf ?? ''); ?>">
              <input type="hidden" name="action" value="delete">
              <input type="hidden" name="id" value="<?php echo (int)$ev['id']; ?>">
              <button class="btn red" type="submit">Sil</button>
            </form>
          </div>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>

  <!-- SAYFALAMA -->
  <?php if (($pages ?? 1) > 1): ?>
    <div class="pager">
      <?php for ($i=1; $i <= (int)$pages; $i++): ?>
        <a class="btn <?php echo ((int)$page === $i) ? 'primary' : ''; ?>"
           href="/boga/admin/events.php?page=<?php echo $i; ?>">
          <?php echo $i; ?>
        </a>
      <?php endfor; ?>
    </div>
  <?php endif; ?>

</div>
