<?php declare(strict_types=1); ?>
<div style="background:#1a1d21; padding:20px; border-radius:12px; color:#fff; border:1px solid #333;">
    <h2 style="margin-top:0;">🐂 Boğa Onay ve Kontrol Paneli</h2>
    <table style="width:100%; border-collapse:collapse; margin-top:20px;">
        <thead>
            <tr style="text-align:left; color:#888; border-bottom:1px solid #333;">
                <th style="padding:10px;">Boğa Bilgisi</th>
                <th style="padding:10px;">Mevcut Durum</th>
                <th style="padding:10px; text-align:center;">Hızlı İşlemler</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($bulls as $b): ?>
                <?php
                    $st = strtolower($b['status']);
                    $cl = ($st === 'approved') ? '#2ecc71' : (($st === 'rejected') ? '#e74c3c' : '#f39c12');
                ?>
                <tr style="border-bottom:1px solid #222;">
                    <td style="padding:15px;">
                        <strong><?= e($b['name']) ?></strong><br>
                        <small style="color:#666;">Sahip: <?= e($b['owner_name'] ?? 'Bilinmiyor') ?></small>
                    </td>
                    <td style="padding:15px;">
                        <span style="display:inline-block; padding:5px 12px; border-radius:6px; font-size:11px; font-weight:bold; background:<?= $cl ?>22; color:<?= $cl ?>; border:1px solid <?= $cl ?>44; text-transform:uppercase;">
                            <?= $st ?>
                        </span>
                        <?php if($st === 'rejected' && !empty($b['reject_reason'])): ?>
                            <div style="font-size:11px; color:#ff7675; margin-top:6px; font-style:italic;">Neden: <?= e($b['reject_reason']) ?></div>
                        <?php endif; ?>
                    </td>
                    <td style="padding:15px; text-align:center;">
                        <div style="display:flex; gap:8px; justify-content:center; align-items:center;">
                            <a href="<?= e(url('admin/bull.php?id=' . (int)$b['id'])) ?>" style="background:#1f6feb; border:0; color:#fff; padding:7px 12px; border-radius:6px; cursor:pointer; font-weight:bold; font-size:12px; text-decoration:none;">DETAY</a>
                            <a href="<?= e(url('admin/bull_edit.php?id=' . (int)$b['id'])) ?>" style="background:#f39c12; border:0; color:#000; padding:7px 12px; border-radius:6px; cursor:pointer; font-weight:bold; font-size:12px; text-decoration:none;">DÜZENLE</a>

                            <?php if($st !== 'approved'): ?>
                                <form method="post" style="margin:0;">
                                    <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
                                    <input type="hidden" name="bull_id" value="<?= $b['id'] ?>">
                                    <button name="bull_action" value="approve" type="submit" style="background:#2ecc71; border:0; color:#fff; padding:7px 15px; border-radius:6px; cursor:pointer; font-weight:bold; font-size:12px;">ONAYLA</button>
                                </form>
                            <?php endif; ?>

                            <?php if($st !== 'rejected'): ?>
                                <form method="post" style="display:flex; background:#000; border-radius:6px; border:1px solid #444; overflow:hidden;">
                                    <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
                                    <input type="hidden" name="bull_id" value="<?= $b['id'] ?>">
                                    <input name="reject_reason" placeholder="Neden?" required style="background:transparent; color:#fff; border:0; padding:7px; width:100px; font-size:12px; outline:none;">
                                    <button name="bull_action" value="reject" type="submit" style="background:#e74c3c; border:0; color:#fff; padding:7px 15px; cursor:pointer; font-weight:bold; font-size:12px;">REDDET</button>
                                </form>
                            <?php endif; ?>

                            <?php if($st !== 'pending'): ?>
                                <form method="post" style="margin:0;">
                                    <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
                                    <input type="hidden" name="bull_id" value="<?= $b['id'] ?>">
                                    <button name="bull_action" value="reset" type="submit" title="Sıfırla" style="background:#34495e; border:1px solid #555; color:#fff; padding:7px 12px; border-radius:6px; cursor:pointer;">🔄</button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
