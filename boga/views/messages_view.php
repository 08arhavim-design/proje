<style>
    .msg-list { display: flex; flex-direction: column; gap: 10px; }
    .msg-item { 
        background: #161b22; border: 1px solid #30363d; border-radius: 12px; 
        padding: 15px; display: flex; justify-content: space-between; align-items: center;
        text-decoration: none; color: inherit; transition: 0.2s;
    }
    .msg-item:hover { border-color: #f39c12; background: #1c2128; }
    .msg-item.unread { border-left: 4px solid #f39c12; background: rgba(243,156,18,0.05); }
    .msg-info { display: flex; flex-direction: column; gap: 4px; }
    .msg-name { font-weight: 800; color: #fff; font-size: 15px; }
    .msg-preview { color: #8b949e; font-size: 13px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 450px; }
    .msg-meta { text-align: right; }
    .unread-dot { width: 8px; height: 8px; background: #f39c12; border-radius: 50%; display: inline-block; margin-left: 5px; }
</style>

<div class="section">
    <h2>📩 Mesajlarım</h2>
    <div class="msg-list">
        <?php if (!empty($chat_list)): ?>
            <?php foreach ($chat_list as $chat): ?>
                <?php 
                    // Okunmamış mesaj kontrolü
                    $isUnread = ((int)$chat['is_read'] === 0 && (int)$chat['sender_id'] !== (int)current_user_id());
                ?>
                <a href="<?= url('mesaj/'.$chat['id']) ?>" class="msg-item <?= $isUnread ? 'unread' : '' ?>">
                    <div class="msg-info">
                        <div class="msg-name">
                            <?= e($chat['full_name'] ?: $chat['username']) ?>
                            <?php if ($isUnread): ?><span class="unread-dot"></span><?php endif; ?>
                        </div>
                        <div class="msg-preview"><?= e($chat['last_message'] ?? 'Mesaj içeriği bulunamadı') ?></div>
                    </div>
                    <div class="msg-meta">
                        <div class="muted" style="font-size: 12px;">
                            <?= date('d.m.Y H:i', strtotime($chat['created_at'])) ?>
                        </div>
                    </div>
                </a>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="muted" style="padding:40px; text-align:center; background:#161b22; border-radius:12px; border:1px dashed #30363d;">
                Henüz hiç mesajlaşmanız bulunmuyor.
            </div>
        <?php endif; ?>
    </div>
</div>