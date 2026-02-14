<?php
// $b (bull kaydı), $error değişkenleri controller'dan gelmeli.
?>
<div style="max-width:900px; margin:20px auto; background:#161b22; padding:35px; border-radius:20px; border:1px solid #30363d; color:#fff; box-shadow: 0 15px 35px rgba(0,0,0,0.4);">

    <h2 style="margin:0 0 25px 0; border-bottom:2px solid #1f6feb; padding-bottom:15px; color:#f0f6fc; display:flex; align-items:center; gap:10px;">
        <span>🐂</span> Boğa Bilgilerini Güncelle
    </h2>

    <?php if (!empty($error)): ?>
        <div style="background:#da3633; color:#fff; padding:12px 14px; border-radius:10px; margin-bottom:18px;">
            <?= e($error) ?>
        </div>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data" style="display:flex; flex-direction:column; gap:25px;">
        <input type="hidden" name="csrf" value="<?= csrf_token() ?>">

        <fieldset style="border:1px solid #30363d; border-radius:12px; padding:20px; background:rgba(0,0,0,0.2);">
            <legend style="padding:0 10px; color:#58a6ff; font-weight:bold; font-size:14px;">📍 TEMEL BİLGİLER</legend>

            <div style="display:grid; grid-template-columns: 1fr 1fr 1fr; gap:15px;">
                <div>
                    <label style="display:block; font-size:12px; color:#8b949e; margin-bottom:5px;">Boğa Adı</label>
                    <input type="text" name="name" required
                           value="<?= e($b['name'] ?? '') ?>"
                           placeholder="Örn: Karabela"
                           style="width:100%; background:#0d1117; border:1px solid #30363d; padding:10px; border-radius:6px; color:#fff;">
                </div>

                <div>
                    <label style="display:block; font-size:12px; color:#8b949e; margin-bottom:5px;">Irkı</label>
                    <input type="text" name="breed"
                           value="<?= e($b['breed'] ?? '') ?>"
                           placeholder="Örn: Simental"
                           style="width:100%; background:#0d1117; border:1px solid #30363d; padding:10px; border-radius:6px; color:#fff;">
                </div>

                <div>
                    <label style="display:block; font-size:12px; color:#8b949e; margin-bottom:5px;">Yaşı</label>
                    <input type="number" name="age" min="0"
                           value="<?= e((string)($b['age'] ?? 0)) ?>"
                           placeholder="Örn: 4"
                           style="width:100%; background:#0d1117; border:1px solid #30363d; padding:10px; border-radius:6px; color:#fff;">
                </div>
            </div>

            <div style="display:grid; grid-template-columns: 1fr 1fr 1fr; gap:15px; margin-top:15px;">
                <div>
                    <label style="display:block; font-size:12px; color:#8b949e; margin-bottom:5px;">Canlı Kilo (kg)</label>
                    <input type="text" name="weight"
                           value="<?= e($b['weight'] ?? '') ?>"
                           placeholder="950"
                           style="width:100%; background:#0d1117; border:1px solid #30363d; padding:10px; border-radius:6px; color:#fff;">
                </div>

                <div>
                    <label style="display:block; font-size:12px; color:#8b949e; margin-bottom:5px;">İl</label>
                    <input type="text" name="city"
                           value="<?= e($b['city'] ?? '') ?>"
                           placeholder="Artvin"
                           style="width:100%; background:#0d1117; border:1px solid #30363d; padding:10px; border-radius:6px; color:#fff;">
                </div>

                <div>
                    <label style="display:block; font-size:12px; color:#8b949e; margin-bottom:5px;">İlçe</label>
                    <input type="text" name="district"
                           value="<?= e($b['district'] ?? '') ?>"
                           placeholder="Arhavi"
                           style="width:100%; background:#0d1117; border:1px solid #30363d; padding:10px; border-radius:6px; color:#fff;">
                </div>
            </div>
        </fieldset>

        <fieldset style="border:1px solid #30363d; border-radius:12px; padding:20px; background:rgba(0,0,0,0.2);">
            <legend style="padding:0 10px; color:#58a6ff; font-weight:bold; font-size:14px;">🧬 SAHİPLİK VE SOYAĞACI</legend>

            <div style="display:grid; grid-template-columns: 1fr 1fr; gap:15px;">
                <div>
                    <label style="display:block; font-size:12px; color:#8b949e; margin-bottom:5px;">Şu Anki Sahibi</label>
                    <input type="text" name="owner_name"
                           value="<?= e($b['owner_name'] ?? '') ?>"
                           style="width:100%; background:#0d1117; border:1px solid #30363d; padding:10px; border-radius:6px; color:#fff;">
                </div>

                <div>
                    <label style="display:block; font-size:12px; color:#8b949e; margin-bottom:5px;">İlk Sahibi (Varsa)</label>
                    <input type="text" name="original_owner"
                           value="<?= e($b['original_owner'] ?? '') ?>"
                           style="width:100%; background:#0d1117; border:1px solid #30363d; padding:10px; border-radius:6px; color:#fff;">
                </div>
            </div>

            <div style="display:grid; grid-template-columns: 1fr 1fr; gap:15px; margin-top:15px;">
                <div>
                    <label style="display:block; font-size:12px; color:#8b949e; margin-bottom:5px;">Anne Adı</label>
                    <input type="text" name="mother_name"
                           value="<?= e($b['mother_name'] ?? '') ?>"
                           style="width:100%; background:#0d1117; border:1px solid #30363d; padding:10px; border-radius:6px; color:#fff;">
                </div>

                <div>
                    <label style="display:block; font-size:12px; color:#8b949e; margin-bottom:5px;">Baba Adı</label>
                    <input type="text" name="father_name"
                           value="<?= e($b['father_name'] ?? '') ?>"
                           style="width:100%; background:#0d1117; border:1px solid #30363d; padding:10px; border-radius:6px; color:#fff;">
                </div>
            </div>
        </fieldset>

        <fieldset style="border:1px solid #30363d; border-radius:12px; padding:20px; background:rgba(0,0,0,0.2);">
            <legend style="padding:0 10px; color:#58a6ff; font-weight:bold; font-size:14px;">🏆 KARİYER VE SATIŞ</legend>

            <div style="margin-bottom:15px;">
                <label style="display:block; font-size:12px; color:#8b949e; margin-bottom:5px;">Katıldığı Arenalar</label>
                <textarea name="arenas" rows="2"
                          placeholder="Örn: Artvin Kafkasör, Aydın..."
                          style="width:100%; background:#0d1117; border:1px solid #30363d; padding:10px; border-radius:6px; color:#fff; resize:none;"><?= e($b['arenas'] ?? '') ?></textarea>
            </div>

            <div style="margin-bottom:15px;">
                <label style="display:block; font-size:12px; color:#8b949e; margin-bottom:5px;">Aldığı Şampiyonluklar</label>
                <textarea name="championships" rows="2"
                          placeholder="Örn: 2023 Kafkasör Baş Boğa..."
                          style="width:100%; background:#0d1117; border:1px solid #30363d; padding:10px; border-radius:6px; color:#fff; resize:none;"><?= e($b['championships'] ?? '') ?></textarea>
            </div>

            <div style="display:grid; grid-template-columns: 1fr 1fr; gap:15px;">
                <div>
                    <label style="display:block; font-size:12px; color:#8b949e; margin-bottom:5px;">Satış Fiyatı (₺)</label>
                    <input type="text" name="price"
                           value="<?= e($b['price'] ?? '') ?>"
                           placeholder="150.000"
                           style="width:100%; background:#0d1117; border:1px solid #30363d; padding:10px; border-radius:6px; color:#fff;">
                </div>

                <div>
                    <label style="display:block; font-size:12px; color:#8b949e; margin-bottom:5px;">Boğa Fotoğrafı</label>

                    <?php if (!empty($b['image'])): ?>
                        <div style="margin-bottom:8px; color:#8b949e; font-size:12px;">
                            Mevcut fotoğraf:
                            <div style="margin-top:6px;">
                                <img src="<?= e(url('uploads/bulls/' . basename((string)$b['image']))) ?>"
                                     alt="Mevcut Foto"
                                     style="max-width:160px; border-radius:10px; border:1px solid #30363d;">
                            </div>
                        </div>
                    <?php endif; ?>

                    <input type="file" name="image" accept="image/*"
                           style="width:100%; background:#0d1117; border:1px solid #30363d; padding:8px; border-radius:6px; color:#fff;">
                    <div style="color:#8b949e; font-size:12px; margin-top:6px;">
                        Yeni fotoğraf seçersen eskisi değişir.
                    </div>
                </div>
            </div>
        </fieldset>

        <button type="submit"
                style="background:#238636; color:#fff; border:0; padding:18px; border-radius:10px; font-weight:bold; cursor:pointer; font-size:16px; box-shadow: 0 4px 15px rgba(35,134,54,0.3);">
            BOĞA KAYDINI GÜNCELLE VE ONAYA GÖNDER
        </button>
    </form>
</div>
