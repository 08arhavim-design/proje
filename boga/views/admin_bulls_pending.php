<?php declare(strict_types=1); ?>
<div style="background:#1a1d21; padding:25px; border-radius:15px; color:#fff; border:1px solid #333;">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
        <h2 style="margin:0; color:#f39c12; font-size:22px; display:flex; align-items:center; gap:10px;">
            <span>⏳</span> Onay Bekleyen Boğalar
        </h2>
        <span style="background:#f39c1222; color:#f39c12; padding:5px 15px; border-radius:20px; font-size:12px; font-weight:bold; border:1px solid #f39c1244;">
            Toplam <?= count($bulls) ?> Kayıt
        </span>
    </div>

    <?php if (empty($bulls)): ?>
        <div style="text-align:center; padding:50px; color:#666; background:#0d1117; border-radius:10px; border:1px dashed #333;">
            Şu an onay bekleyen herhangi bir boğa kaydı bulunmuyor.
        </div>
    <?php else: ?>
        <div style="overflow-x:auto;">
            <table style="width:100%; border-collapse:collapse; font-size:13px; min-width:800px;">
                <thead>
                    <tr style="text-align:left; color:#888; border-bottom:2px solid #333;">
                        <th style="padding:12px;">Foto</th>
                        <th style="padding:12px;">Boğa & Sahibi</th>
                        <th style="padding:12px;">Fiziksel Özellikler</th>
                        <th style="padding:12px;">Soyağacı & Kariyer</th>
                        <th style="padding:12px;">Konum & Fiyat</th>
                        <th style="padding:12px; text-align:center;">İşlem</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($bulls as $b): ?>
                        <tr style="border-bottom:1px solid #222; transition:0.2s;" onmouseover="this.style.background='#1f2428'" onmouseout="this.style.background='transparent'">
                            <td style="padding:12px;">
                                <?php if(!empty($b['image'])): ?>
                                    <img src="<?= url('uploads/'.$b['image']) ?>" style="width:65px; height:65px; object-fit:cover; border-radius:8px; border:1px solid #444;">
                                <?php else: ?>
                                    <div style="width:65px; height:65px; background:#0d1117; display:flex; align-items:center; justify-content:center; border-radius:8px; border:1px solid #333;">🐂</div>
                                <?php endif; ?>
                            </td>

                            <td style="padding:12px;">
                                <strong style="color:#fff; font-size:15px; display:block;"><?= e((string)($b['name'] ?? 'İsimsiz')) ?></strong>
                                <span style="color:#58a6ff; font-size:12px;">👤 <?= e((string)($b['owner_name'] ?? 'Bilinmiyor')) ?></span>
                                <?php if(!empty($b['original_owner'])): ?>
                                    <br><small style="color:#666;">Eski Sahibi: <?= e((string)$b['original_owner']) ?></small>
                                <?php endif; ?>
                            </td>

                            <td style="padding:12px; color:#c9d1d9; line-height:1.6;">
                                🏁 <b>Irk:</b> <?= e((string)($b['breed'] ?? '-')) ?><br>
                                🎂 <b>Yaş:</b> <?= e((string)($b['age'] ?? '-')) ?><br>
                                ⚖️ <b>Kilo:</b> <?= e((string)($b['weight'] ?? '-')) ?> kg
                            </td>

                            <td style="padding:12px; max-width:220px;">
                                <div style="font-size:11px; color:#8b949e; line-height:1.5;">
                                    🧬 <b>A/B:</b> <?= e((string)($b['mother_name'] ?? '-')) ?> / <?= e((string)($b['father_name'] ?? '-')) ?><br>
                                    🏆 <b>Şampiyonluk:</b> <?= e((string)($b['championships'] ?? 'Yok')) ?><br>
                                    🏟️ <b>Arenalar:</b> <?= e((string)($b['arenas'] ?? 'Yok')) ?>
                                </div>
                            </td>

                            <td style="padding:12px;">
                                <span style="display:block; margin-bottom:5px;">📍 <?= e((string)($b['city'] ?? '-')) ?> / <?= e((string)($b['district'] ?? '-')) ?></span>
                                <strong style="color:#2ecc71; font-size:15px;"><?= e((string)($b['price'] ?? '0')) ?> ₺</strong>
                            </td>

                            <td style="padding:12px; text-align:center;">
                                <a href="<?= url('admin/bulls.php') ?>" style="background:#1f6feb; color:#fff; padding:8px 16px; text-decoration:none; border-radius:6px; font-size:12px; font-weight:bold; display:inline-block; transition:0.2s;" onmouseover="this.style.background='#388bfd'">
                                    Yönet & Onayla
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>