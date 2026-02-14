<?php declare(strict_types=1); ?>
<div style="background:#161b22; padding:20px; border-radius:12px; border:1px solid #30363d;">
    <h2 style="color:#f39c12; border-bottom:1px solid #333; padding-bottom:10px;">⏳ Onay Sırasındaki Boğalar</h2>

    <?php if (empty($bulls)): ?>
        <div style="padding:50px; text-align:center; color:#8b949e;">
            <p>Onay bekleyen kayıt bulunamadı.</p>
            <small>Eğer veritabanında kayıt varsa, statüsünün 'pending' olduğundan emin olun.</small>
        </div>
    <?php else: ?>
        <?php foreach ($bulls as $b): ?>
            <div style="background:#0d1117; border:1px solid #30363d; border-radius:10px; padding:15px; margin-bottom:15px; display:flex; gap:20px; align-items:start;">
                
                <div style="width:150px;">
                    <img src="<?= url('uploads/bulls/'.($b['image'] ?: 'none.jpg')) ?>" style="width:100%; border-radius:8px; border:1px solid #333;">
                </div>

                <div style="flex:1; display:grid; grid-template-columns: 1fr 1fr; gap:10px; font-size:13px; color:#c9d1d9;">
                    <div>
                        <b style="color:#fff; font-size:16px;"><?= e($b['name']) ?></b><br>
                        <b>Sahibi:</b> <?= e($b['owner_name']) ?><br>
                        <b>Anne / Baba:</b> <?= e($b['mother_name'] ?: '-') ?> / <?= e($b['father_name'] ?: '-') ?><br>
                        <b>Yaş / Kilo:</b> <?= e($b['age'] ?: '-') ?> / <?= e($b['weight'] ?: '-') ?> kg
                    </div>
                    <div>
                        <b>Konum:</b> <?= e($b['city']) ?> / <?= e($b['district']) ?><br>
                        <b>Başarılar:</b> <?= e($b['championships'] ?: '-') ?><br>
                        <b>Fiyat:</b> <span style="color:#2ecc71; font-weight:bold;"><?= number_format((float)$b['price'], 0, ',', '.') ?> ₺</span>
                    </div>
                </div>

                <div style="width:180px; text-align:right;">
                    <form method="post" style="margin-bottom:10px;">
                        <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
                        <input type="hidden" name="bull_id" value="<?= $b['id'] ?>">
                        <button name="bull_action" value="approve" style="width:100%; background:#238636; color:#fff; border:0; padding:10px; border-radius:6px; cursor:pointer; font-weight:bold;">ONAYLA</button>
                    </form>
                    
                    <form method="post">
                        <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
                        <input type="hidden" name="bull_id" value="<?= $b['id'] ?>">
                        <input name="reject_reason" placeholder="Red Nedeni..." required style="width:100%; background:#000; border:1px solid #333; color:#fff; padding:5px; border-radius:4px; font-size:11px; margin-bottom:5px;">
                        <button name="bull_action" value="reject" style="width:100%; background:transparent; color:#e74c3c; border:1px solid #e74c3c; padding:8px; border-radius:6px; cursor:pointer; font-size:12px;">REDDET</button>
                    </form>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>