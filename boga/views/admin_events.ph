<style>
  .wrap{max-width:1100px;margin:0 auto;}
  .top{display:flex;gap:10px;flex-wrap:wrap;align-items:center;justify-content:space-between;margin-bottom:12px;}
  .btn{display:inline-block;padding:10px 12px;border-radius:10px;text-decoration:none;font-weight:900;font-size:13px;border:1px solid rgba(255,255,255,0.12);background:rgba(255,255,255,0.06);color:#fff;}
  .btn.primary{background:rgba(35,134,54,0.20);border-color:rgba(35,134,54,0.35);color:#2ecc71;}
  .tbl{width:100%;border-collapse:separate;border-spacing:0 10px;}
  .tbl th{color:#8b949e;font-size:12px;text-align:left;padding:0 10px;}
  .row{background:#161b22;border:1px solid #30363d;border-radius:14px;}
  .row td{padding:12px 10px;vertical-align:top;}
  .pill{display:inline-block;padding:6px 10px;border-radius:999px;font-weight:900;font-size:12px;background:rgba(243,156,18,0.12);border:1px solid rgba(243,156,18,0.35);color:#f39c12;}
  .muted{color:#8b949e;font-size:12px;}
  .actions{display:flex;gap:8px;flex-wrap:wrap;}
  .btn.danger{background:rgba(218,54,51,0.14);border-color:rgba(218,54,51,0.35);color:#ff6b6b;}
</style>

<div class="wrap">
  <div class="top">
    <div>
      <div style="font-weight:900;font-size:16px;">📅 Takvim Yönetimi</div>
      <div class="muted">Boğa güreşi etkinliklerini buradan ekleyip düzenleyebilirsiniz.</div>
    </div>
    <a class="btn primary" href="<?= url('admin/event_new.php') ?>">+ Etkinlik Ekle</a>
  </div>

  <table class="tbl">
    <thead>
      <tr>
        <th>Tarih</th>
        <th>Başlık</th>
        <th>Yer</th>
        <th>Kategoriler</th>
        <th>İşlem</th>
      </tr>
    </thead>
    <tbody>
    <?php if(empty($events)): ?>
      <tr><td class="muted">Kayıt yok.</td></tr>
    <?php endif; ?>

    <?php foreach($events as $e): ?>
      <tr class="row">
        <td>
          <span class="pill">
            <?= e(date('d.m.Y', strtotime($e['event_date']))) ?>
            <?= !empty($e['start_time']) ? ' • '.e(substr((string)$e['start_time'],0,5)) : '' ?>
          </span>
        </td>

        <td style="font-weight:900;">
          <?= e($e['festival'] ?? '') ? e($e['festival']).' — ' : '' ?>
          <?= e($e['title'] ?? '') ?>
          <?php if(!empty($e['has_award'])): ?>
            <div class="muted" style="margin-top:6px;">🏆 Ödül Töreni</div>
          <?php endif; ?>
        </td>

        <td class="muted">
          <?= e(trim(($e['city'] ?? '').' '.(($e['district'] ?? '') ? '/ '.$e['district'] : ''))) ?>
          <?= !empty($e['location']) ? '<br>'.e($e['location']) : '' ?>
        </td>

        <td class="muted">
          <?php
            $cat = trim((string)($e['categories'] ?? ''));
            if ($cat === '') echo '-';
            else echo nl2br(e($cat));
          ?>
        </td>

        <td>
          <div class="actions">
            <a class="btn" href="<?= url('admin/event_edit.php?id='.(int)$e['id']) ?>">Düzenle</a>
            <form method="post" action="<?= url('admin/event_delete.php') ?>" onsubmit="return confirm('Silinsin mi?')">
              <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
              <input type="hidden" name="id" value="<?= (int)$e['id'] ?>">
              <button class="btn danger" type="submit">Sil</button>
            </form>
          </div>
        </td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
</div>
