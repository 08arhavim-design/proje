<?php
/**
 * @var array $peer Karşı kullanıcı bilgileri
 * @var array $msgs Mesaj geçmişi listesi
 */
?>
<style>
  .chat-wrap { max-width: 900px; margin: 0 auto; }
  .chat-head { display: flex; justify-content: space-between; align-items: center; margin: 10px 0 12px; }
  .pill { 
    display: inline-flex; align-items: center; gap: 8px; padding: 8px 14px; 
    border-radius: 999px; background: rgba(255, 255, 255, 0.06); 
    border: 1px solid rgba(255, 255, 255, 0.10); color: #fff;
  }
  .box { background: #0d1117; border: 1px solid rgba(255, 255, 255, 0.06); border-radius: 16px; padding: 14px; }
  .msg { padding: 10px 14px; border-radius: 12px; margin: 8px 0; max-width: 85%; line-height: 1.5; font-size: 14px; }
  .me { 
    margin-left: auto; background: rgba(243, 156, 18, 0.15); 
    border: 1px solid rgba(243, 156, 18, 0.25); color: #fff; 
  }
  .you { 
    margin-right: auto; background: rgba(255, 255, 255, 0.06); 
    border: 1px solid rgba(255, 255, 255, 0.10); color: #fff; 
  }
  .muted { color: #8b949e; font-size: 11px; }
  textarea { 
    width: 100%; min-height: 80px; background: #0b0e14; color: #fff; 
    border: 1px solid rgba(255, 255, 255, 0.12); border-radius: 12px; 
    padding: 12px; outline: none; resize: vertical;
  }
  textarea:focus { border-color: rgba(243, 156, 18, 0.5); }
  .btn { 
    display: inline-flex; align-items: center; justify-content: center; 
    padding: 10px 16px; border-radius: 10px; text-decoration: none; 
    font-weight: 800; font-size: 13px; border: 1px solid rgba(255, 255, 255, 0.12); 
    background: rgba(255, 255, 255, 0.06); color: #fff; cursor: pointer; 
  }
  .btn.primary { background: rgba(243, 156, 18, 0.20); border-color: rgba(243, 156, 18, 0.35); color: #f39c12; }
</style>

<div class="chat-wrap">
  <div class="chat-head">
    <a class="btn" href="<?= url('') ?>">← Geri Dön</a>
    <span class="pill">
      <span style="color: #f39c12;">●</span>
      <?= e($peer['full_name'] ?: ($peer['username'] ?? 'Kullanıcı')) ?>
      <span class="muted" style="margin-left: 5px;">• <?= e($peer['role'] ?? 'Üye') ?></span>
    </span>
  </div>

  <div class="box" id="chatBox" style="max-height: 500px; overflow-y: auto;">
    <?php if (!empty($msgs)): ?>
      <?php foreach ($msgs as $m): ?>
        <?php 
          // Hata almamak için tüm olası sütun isimlerini kontrol ediyoruz
          $content = '';
          if (isset($m['message'])) $content = $m['message'];
          elseif (isset($m['body'])) $content = $m['body'];
          elseif (isset($m['content'])) $content = $m['content'];
        ?>
        <div class="msg <?= ((int)$m['sender_id'] === (int)current_user_id()) ? 'me' : 'you' ?>">
          <div style="word-break: break-word;"><?= nl2br(e($content)) ?></div>
          <div class="muted" style="margin-top: 6px; text-align: right;">
            <?= isset($m['created_at']) ? e(date('H:i', strtotime($m['created_at']))) : '' ?>
          </div>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <div class="muted" style="text-align: center; padding: 20px;">Henüz mesaj yok. İlk mesajı gönderen sen ol!</div>
    <?php endif; ?>
  </div>

  <div class="box" style="margin-top: 12px;">
    <form method="post" action="">
      <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
      <input type="hidden" name="u" value="<?= (int)($peer['id'] ?? 0) ?>">
      
      <textarea name="message" placeholder="Buraya yazın..." required></textarea>
      
      <div style="margin-top: 10px; display: flex; justify-content: flex-end;">
        <button class="btn primary" type="submit">Gönder</button>
      </div>
    </form>
  </div>
</div>

<script>
  document.addEventListener("DOMContentLoaded", function() {
    var chatBox = document.getElementById("chatBox");
    if (chatBox) {
        chatBox.scrollTop = chatBox.scrollHeight;
    }
  });
</script>