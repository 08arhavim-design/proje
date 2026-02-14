<?php
declare(strict_types=1);
/** @var array $bulls */
?>
<div class="card" style="max-width:980px;margin:0 auto;">
  <h2 style="margin:0 0 14px;">Yeni Boğa Ekle</h2>

  <form method="post" action="<?= e(url_path('/admin/bulls.php')) ?>" style="display:block;">
    <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
    <input type="hidden" name="action" value="create">

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
      <div style="grid-column:1/-1;">
        <label style="display:block;margin:0 0 6px;opacity:.9;">Boğa adı</label>
        <input required name="name" maxlength="100" style="width:100%;padding:12px 14px;border-radius:12px;border:1px solid rgba(255,255,255,.18);background:rgba(255,255,255,.06);color:#eef4ff;outline:none;">
      </div>

      <div>
        <label style="display:block;margin:0 0 6px;opacity:.9;">Yaşı</label>
        <input type="number" name="age" min="0" max="99" style="width:100%;padding:12px 14px;border-radius:12px;border:1px solid rgba(255,255,255,.18);background:rgba(255,255,255,.06);color:#eef4ff;outline:none;">
      </div>

      <div>
        <label style="display:block;margin:0 0 6px;opacity:.9;">Boğa sahibi</label>
        <input name="owner_name" maxlength="150" style="width:100%;padding:12px 14px;border-radius:12px;border:1px solid rgba(255,255,255,.18);background:rgba(255,255,255,.06);color:#eef4ff;outline:none;">
      </div>

      <div style="grid-column:1/-1;">
        <label style="display:block;margin:0 0 6px;opacity:.9;">Kazandığı yarışlar</label>
        <textarea name="won_titles" rows="3" style="width:100%;padding:12px 14px;border-radius:12px;border:1px solid rgba(255,255,255,.18);background:rgba(255,255,255,.06);color:#eef4ff;outline:none;resize:vertical;"></textarea>
      </div>

      <div>
        <label style="display:block;margin:0 0 6px;opacity:.9;">Annesi</label>
        <input name="mother_name" maxlength="150" style="width:100%;padding:12px 14px;border-radius:12px;border:1px solid rgba(255,255,255,.18);background:rgba(255,255,255,.06);color:#eef4ff;outline:none;">
      </div>

      <div>
        <label style="display:block;margin:0 0 6px;opacity:.9;">Babası</label>
        <input name="father_name" maxlength="150" style="width:100%;padding:12px 14px;border-radius:12px;border:1px solid rgba(255,255,255,.18);background:rgba(255,255,255,.06);color:#eef4ff;outline:none;">
      </div>

      <div>
        <label style="display:block;margin:0 0 6px;opacity:.9;">En son güreştiği yer</label>
        <input name="last_arena" maxlength="150" style="width:100%;padding:12px 14px;border-radius:12px;border:1px solid rgba(255,255,255,.18);background:rgba(255,255,255,.06);color:#eef4ff;outline:none;">
      </div>

      <div>
        <label style="display:block;margin:0 0 6px;opacity:.9;">Hangi boğayla güreşti</label>
        <input name="last_opponent" maxlength="150" style="width:100%;padding:12px 14px;border-radius:12px;border:1px solid rgba(255,255,255,.18);background:rgba(255,255,255,.06);color:#eef4ff;outline:none;">
      </div>

      <div style="grid-column:1/-1;">
        <label style="display:block;margin:0 0 6px;opacity:.9;">Hangi kategoride yarıştı</label>
        <input name="last_category" maxlength="100" style="width:100%;padding:12px 14px;border-radius:12px;border:1px solid rgba(255,255,255,.18);background:rgba(255,255,255,.06);color:#eef4ff;outline:none;">
      </div>
    </div>

    <div style="margin-top:14px;display:flex;justify-content:flex-end;">
      <button type="submit" class="btn" style="padding:12px 16px;border-radius:12px;">Kaydet</button>
    </div>
  </form>

  <hr style="border:none;border-top:1px solid rgba(255,255,255,.10);margin:18px 0;">

  <h2 style="margin:0 0 12px;">Kayıtlı Boğalar</h2>

  <?php if (!isset($bulls) || !is_array($bulls)) $bulls = []; ?>
  <?php if (!$bulls): ?>
    <div style="opacity:.8;">Henüz kayıt yok.</div>
  <?php else: ?>
    <div style="overflow:auto;">
      <table style="width:100%;border-collapse:collapse;min-width:760px;">
        <thead>
          <tr style="text-align:left;opacity:.9;">
            <th style="padding:10px;border-bottom:1px solid rgba(255,255,255,.10);">ID</th>
            <th style="padding:10px;border-bottom:1px solid rgba(255,255,255,.10);">Boğa</th>
            <th style="padding:10px;border-bottom:1px solid rgba(255,255,255,.10);">Yaş</th>
            <th style="padding:10px;border-bottom:1px solid rgba(255,255,255,.10);">Sahip</th>
            <th style="padding:10px;border-bottom:1px solid rgba(255,255,255,.10);">Durum</th>
            <th style="padding:10px;border-bottom:1px solid rgba(255,255,255,.10);">Tarih</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($bulls as $b): ?>
            <tr>
              <td style="padding:10px;border-bottom:1px solid rgba(255,255,255,.06);"><?= (int)$b['id'] ?></td>
              <td style="padding:10px;border-bottom:1px solid rgba(255,255,255,.06);"><?= e((string)$b['name']) ?></td>
              <td style="padding:10px;border-bottom:1px solid rgba(255,255,255,.06);"><?= (int)$b['age'] ?></td>
              <td style="padding:10px;border-bottom:1px solid rgba(255,255,255,.06);"><?= e((string)($b['owner_name'] ?? '')) ?></td>
              <td style="padding:10px;border-bottom:1px solid rgba(255,255,255,.06);"><?= e((string)$b['status']) ?></td>
              <td style="padding:10px;border-bottom:1px solid rgba(255,255,255,.06);"><?= e((string)$b['created_at']) ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>
</div>
