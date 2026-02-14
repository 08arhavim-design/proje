<?php declare(strict_types=1); ?>
<h2 style="margin-top:0">Kayıt Ol</h2>
<?php if(!empty($errors)): ?><div class="flash"><?= esc(implode("\n",$errors)) ?></div><?php endif; ?>
<form method="post" style="max-width:520px">
  <label>Kullanıcı adı</label><br>
  <input name="username" required style="width:100%;padding:10px;border-radius:12px;border:1px solid var(--bd);background:rgba(0,0,0,.18);color:var(--txt);margin:6px 0 12px"><br>
  <label>Telefon</label><br>
  <input name="phone" placeholder="0532..." style="width:100%;padding:10px;border-radius:12px;border:1px solid var(--bd);background:rgba(0,0,0,.18);color:var(--txt);margin:6px 0 12px"><br>
  <label>Şifre</label><br>
  <input type="password" name="password" required style="width:100%;padding:10px;border-radius:12px;border:1px solid var(--bd);background:rgba(0,0,0,.18);color:var(--txt);margin:6px 0 12px"><br>
  <label>Şifre tekrar</label><br>
  <input type="password" name="password2" required style="width:100%;padding:10px;border-radius:12px;border:1px solid var(--bd);background:rgba(0,0,0,.18);color:var(--txt);margin:6px 0 14px"><br>
  <button type="submit" style="padding:10px 14px;border-radius:12px;border:1px solid var(--bd);background:var(--btn);color:var(--txt)">Kaydı Tamamla</button>
  <a href="<?= url('login.php') ?>" style="margin-left:10px;color:var(--txt)">Giriş</a>
</form>
