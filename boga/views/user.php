<?php declare(strict_types=1); ?>
<div style="background:rgba(255,255,255,0.05); padding:25px; border-radius:15px; color:#fff; border:1px solid rgba(255,255,255,0.1);">
    
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:25px; border-bottom:1px solid rgba(255,255,255,0.1); padding-bottom:15px;">
        <div>
            <h2 style="margin:0; font-size:24px;">🐂 Boğalarım</h2>
            <small style="color:#888;">Eklediğiniz boğaları buradan yönetebilirsiniz.</small>
        </div>
        <a href="<?= url('user_add_bull.php') ?>" style="background:#238636; color:#fff; padding:10px 20px; text-decoration:none; border-radius:8px; font-weight:bold;">+ Yeni Ekle</a>
    </div>

    <?php if(empty($my_bulls)): ?>
        <p style="text-align:center; padding:40px; color:#666;">Henüz bir boğa kaydınız bulunmuyor.</p>
    <?php endif; ?>

    <?php foreach ($my_bulls as $b): ?>
        <?php 
            $st = $b['status'] ?? 'pending';
            $is_rej = ($st === 'rejected');
            $is_app = ($st === 'approved');
        ?>
        <div style="background:#161b22; border:1px solid #30363d; border-radius:12px; padding:20px; margin-bottom:15px;">
            <div style="display:flex; justify-content:space-between; align-items:start;">
                <div style="display:flex; gap:15px;">
                    <img src="<?= url('uploads/bulls/'.basename($b['image'] ?? 'none.jpg')) ?>" style="width:70px; height:70px; object-fit:cover; border-radius:8px; background:#000;">
                    <div>
                        <h3 style="margin:0; color:#f39c12;"><?= e($b['name']) ?></h3>
                        <div style="margin-top:5px;">
                            <?php if($is_rej): ?>
                                <span style="color:#ff4d4d; font-size:12px;">● Reddedildi</span>
                            <?php elseif($is_app): ?>
                                <span style="color:#2ecc71; font-size:12px;">● Yayında</span>
                            <?php else: ?>
                                <span style="color:#f39c12; font-size:12px;">● Onay Bekliyor</span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div style="display:flex; gap:8px;">
                    <a href="<?= url('user_edit_bull.php?id='.$b['id']) ?>" style="background:#1f6feb; color:#fff; padding:8px 15px; border-radius:6px; text-decoration:none; font-size:13px;">Düzenle</a>
                    
                    <a href="<?= url('user_delete_bull.php?id='.$b['id']) ?>" 
                       onclick="return confirm('Bu kaydı silmek istediğinize emin misiniz?')" 
                       style="background:rgba(231,76,60,0.2); color:#e74c3c; border:1px solid #e74c3c; padding:8px 15px; border-radius:6px; text-decoration:none; font-size:13px;">
                       Sil
                    </a>
                </div>
            </div>

            <?php if($is_rej && !empty($b['reject_reason'])): ?>
                <div style="margin-top:15px; padding:10px; background:rgba(231,76,60,0.05); border-radius:6px; border-left:3px solid #e74c3c; font-size:13px;">
                    <strong style="color:#e74c3c;">Red Nedeni:</strong> <?= e($b['reject_reason']) ?>
                </div>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
</div>