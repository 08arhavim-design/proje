<div class="muted">Telefon: <?= e($_SESSION['otp_phone'] ?? '') ?></div>
<form method="post" action="<?= e(base_url('/otp_verify.php')) ?>">
  <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
  <label>SMS kodu</label>
  <input name="code" inputmode="numeric" maxlength="4" minlength="4" required>
  <button class="btn" type="submit">Doğrula ve giriş yap</button>
</form>
