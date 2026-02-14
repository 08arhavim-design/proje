<style>
  .box{max-width:820px;margin:0 auto;background:#0d1117;border:1px solid rgba(255,255,255,0.08);border-radius:16px;padding:16px;}
  .h{margin:0 0 12px 0;font-size:16px;border-left:4px solid #f39c12;padding-left:12px}
  .grid{display:grid;grid-template-columns:1fr 1fr;gap:12px;}
  .lbl{display:block;font-size:12px;color:#8b949e;margin-bottom:6px}
  .in{width:100%;background:#161b22;border:1px solid #30363d;border-radius:10px;color:#fff;padding:10px}
  .ta{width:100%;background:#161b22;border:1px solid #30363d;border-radius:10px;color:#fff;padding:10px;min-height:110px;resize:vertical}
  .btn{display:inline-block;padding:12px 14px;border-radius:10px;text-decoration:none;font-weight:900;font-size:13px;border:1px solid rgba(255,255,255,0.12);background:rgba(255,255,255,0.06);color:#fff;}
  .btn.primary{background:rgba(35,134,54,0.20);border-color:rgba(35,134,54,0.35);color:#2ecc71;}
  .err{background:rgba(218,54,51,0.18);border:1px solid rgba(218,54,51,0.35);color:#ffb4b4;border-radius:12px;padding:10px;margin-bottom:12px;}
  .chk{display:flex;align-items:center;gap:10px;background:#161b22;border:1px solid #30363d;border-radius:10px;padding:10px;}
  @media(max-width:640px){.grid{grid-template-columns:1fr;}}
</style>

<div class="box">
  <h2 class="h"><?= e($title ?? 'Etkinlik') ?></h2>

  <?php if(!empty($error)): ?><div class="err"><?= e($error) ?></div><?php endif; ?>

  <form method="post" style="display:flex;flex-direction:column;gap:12px;">
    <input type="hidden" name="csrf" value="<?= csrf_token() ?>">

    <div class="grid">
      <div>
        <label class="lbl">Festival / Organizasyon (opsiyonel)</label>
        <input class="in" name="festival" value="<?= e($event['festival'] ?? '') ?>" placeholder="Örn: Kafkasör">
      </div>
      <div>
        <label class="lbl">Başlık</label>
        <input class="in" name="title" required value="<?= e($event['title'] ?? '') ?>" placeholder="Örn: Kafkasör Boğa Güreşleri">
      </div>
    </div>

    <div class="grid">
      <div>
        <label class="lbl">İl</label>
        <input class="in" name="city" value="<?= e($event['city'] ?? '') ?>" placeholder="Artvin">
      </div>
      <div>
        <label class="lbl">İlçe</label>
        <input class="in" name="district" value="<?= e($event['district'] ?? '') ?>" placeholder="Merkez">
      </div>
    </div>

    <div>
      <label class="lbl">Yer / Alan</label>
      <input class="in" name="location" value="<?= e($event['location'] ?? '') ?>" placeholder="Örn: Kafkasör Yaylası / Arena">
    </div>

    <div class="grid">
      <div>
        <label class="lbl">Tarih</label>
        <input class="in" type="date" name="event_date" required value="<?= e($event['event_date'] ?? '') ?>">
      </div>
      <div>
        <label class="lbl">Saat (opsiyonel)</label>
        <input class="in" type="time" name="start_time" value="<?= e($event['start_time'] ?? '') ?>">
      </div>
    </div>

    <div>
      <label class="lbl">Kategoriler (her satıra bir kategori)</label>
      <textarea class="ta" name="categories" placeholder="- Ayak Kategorisi&#10;- Küçük Orta Kategorisi&#10;- ..."><?= e($event['categories'] ?? '') ?></textarea>
    </div>

    <label class="chk">
      <input type="checkbox" name="has_award" value="1" <?= !empty($event['has_award']) ? 'checked' : '' ?>>
      <span style="font-weight:900;">Ödül Töreni Var</span>
    </label>

    <div>
      <label class="lbl">Not / Açıklama (opsiyonel)</label>
      <textarea class="ta" name="notes" placeholder="Program, duyuru, açıklama..."><?= e($event['notes'] ?? '') ?></textarea>
    </div>

    <div style="display:flex;gap:10px;flex-wrap:wrap;">
      <button class="btn primary" type="submit">Kaydet</button>
      <a class="btn" href="<?= url('admin/events.php') ?>">Geri</a>
      <a class="btn" href="<?= url('takvim.php') ?>" target="_blank">Misafir Takvim</a>
    </div>
  </form>
</div>
