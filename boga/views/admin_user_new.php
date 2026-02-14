<form method="post" action="<?= e(base_url('/admin/user_new_post.php')) ?>">
  <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
  <label>Kullanıcı adı</label><input name="username" required>
  <label>Şifre</label><input name="password" type="password" required>
  <label>Telefon (0 ile başlayabilir)</label><input name="phone" required>
  <label>Rol</label>
  <select name="role">
    <option value="user">user</option>
    <option value="admin">admin</option>
    <option value="superadmin">superadmin</option>
  </select>
  <label>Aktif</label>
  <select name="is_active">
    <option value="1">Evet</option>
    <option value="0">Hayır</option>
  </select>
  <button class="btn" type="submit">Kaydet</button>
  <a class="btn btn2" href="<?= e(base_url('/admin/users.php')) ?>">İptal</a>
</form>
