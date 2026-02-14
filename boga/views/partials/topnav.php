<?php
// boga/views/partials/topnav.php
?>
<nav class="topnav">
    <div class="nav-left">
        <a href="<?= url('/') ?>">Ana Sayfa</a>
        <?php if (is_logged_in()): ?>
            <a href="<?= url('/user.php') ?>">Hesabım</a> 
            <?php if (user_role() === 'admin' || user_role() === 'superadmin'): ?>
                <a href="<?= url('/admin/') ?>">Admin Panel</a>
            <?php endif; ?>
        <?php endif; ?>
    </div>
    <div class="nav-right">
        <?php if (is_logged_in()): ?>
            <span class="user-badge" style="color:var(--mut); margin-right:10px;">(<?= e(user_role()) ?>)</span>
            <a href="<?= url('/logout.php') ?>">Çıkış</a>
        <?php else: ?>
            <a href="<?= url('/login.php') ?>">Giriş Yap</a>
            <a href="<?= url('/register.php') ?>">Kayıt Ol</a>
        <?php endif; ?>
    </div>
</nav>

<style>
.topnav { display: flex; justify-content: space-between; align-items: center; padding: 12px 20px; background: rgba(0,0,0,.25); backdrop-filter: blur(8px); border-radius: 12px; margin-bottom: 20px; border: 1px solid rgba(255,255,255,0.05); }
.topnav a { color: #fff; text-decoration: none; margin-right: 14px; padding: 8px 12px; border-radius: 8px; transition: background 0.2s; }
.topnav a:hover { background: rgba(255,255,255,0.1); }
</style>