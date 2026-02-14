<?php
// File: /boga/app/layout.php
?>
<!DOCTYPE html>
<html lang="tr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= e($title ?? 'Boğa Spor') ?></title>

  <style>
    *{ box-sizing:border-box; }
    html,body{ width:100%; overflow-x:hidden; }
    body{ margin:0; background:#0b0e14; font-family:sans-serif; color:#fff; }

    .wrapper{ max-width:1200px; margin:0 auto; padding:15px; }

    .nav{
      background:rgba(255,255,255,0.05);
      border:1px solid rgba(255,255,255,0.10);
      border-radius:12px;
      padding:12px;
      margin-bottom:20px;
    }

    .nav-top{
      display:flex;
      gap:10px;
      align-items:center;
      justify-content:space-between;
    }

    .brand{
      display:flex;
      gap:10px;
      align-items:center;
      font-weight:900;
      letter-spacing:.3px;
    }

    .menu-toggle{
      display:none;
      border:1px solid rgba(255,255,255,0.12);
      background:rgba(255,255,255,0.10);
      color:#c9d1d9;
      padding:10px 12px;
      border-radius:10px;
      font-weight:900;
      cursor:pointer;
    }

    .links{
      margin-top:12px;
      display:flex;
      flex-wrap:wrap;
      gap:8px;
    }

    .menu-btn{
      background:rgba(255,255,255,0.10);
      color:#8b949e;
      padding:10px 14px;
      border-radius:10px;
      text-decoration:none;
      font-size:13px;
      font-weight:800;
      white-space:nowrap;
      display:inline-flex;
      align-items:center;
      justify-content:center;
      gap:6px;
      max-width:100%;
      position: relative; /* Bildirim için gerekli */
    }
    .active{
      border:1px solid #f39c12;
      background:rgba(243,156,18,0.10);
      color:#f39c12;
    }

    .badge{
      display:inline-flex;
      align-items:center;
      justify-content:center;
      min-width:18px;
      height:18px;
      padding:0 6px;
      border-radius:999px;
      font-size:11px;
      font-weight:900;
      background:rgba(243,156,18,0.20);
      border:1px solid rgba(243,156,18,0.35);
      color:#f39c12;
    }

    /* 🔴 Yeni Mesaj Bildirim Rozeti */
    .msg-badge {
      background: #ff4757;
      color: white;
      font-size: 10px;
      font-weight: bold;
      padding: 2px 6px;
      border-radius: 50%;
      position: absolute;
      top: -5px;
      right: -5px;
      border: 2px solid #0b0e14;
      animation: pulse-red 2s infinite;
      z-index: 5;
    }

    @keyframes pulse-red {
      0% { box-shadow: 0 0 0 0 rgba(255, 71, 87, 0.7); }
      70% { box-shadow: 0 0 0 6px rgba(255, 71, 87, 0); }
      100% { box-shadow: 0 0 0 0 rgba(255, 71, 87, 0); }
    }

    /* Hesabım mini menü (masaüstü) */
    .account-box{ margin-left:auto; display:flex; gap:8px; align-items:center; }
    .account-caret{
      padding:10px 12px;
      border-radius:10px;
      border:1px solid rgba(255,255,255,0.12);
      background:rgba(255,255,255,0.10);
      color:#8b949e;
      cursor:pointer;
      font-weight:900;
      line-height:1;
    }
    .account-menu{
      position:fixed;
      right:8px;
      min-width:200px;
      max-width:calc(100vw - 16px);
      background:#0d1117;
      border:1px solid rgba(255,255,255,0.10);
      border-radius:12px;
      padding:8px;
      display:none;
      z-index:99999;
      box-shadow:0 10px 30px rgba(0,0,0,0.35);
    }
    .account-menu.open{ display:block; }
    .account-menu a{
      display:block;
      padding:10px;
      border-radius:10px;
      text-decoration:none;
      color:#c9d1d9;
      font-weight:800;
      font-size:13px;
    }
    .account-menu a:hover{ background:rgba(255,255,255,0.06); }

    /* ✅ MOBİL: hamburger */
    @media (max-width: 780px){
      .wrapper{ padding:12px; }
      .menu-toggle{ display:inline-flex; align-items:center; gap:8px; }
      .links{
        display:none;
        flex-direction:column;
        gap:10px;
      }
      .links.open{ display:flex; }
      .menu-btn{
        width:100%;
        white-space:normal;
        overflow-wrap:anywhere;
      }
      .account-box{ width:100%; justify-content:space-between; }
    }
  </style>
</head>

<body>
<div class="wrapper">

<?php
  $favCount = 0;
  $unreadCount = 0;
  if (is_logged_in()) {
    try {
      $pdo = pdo();
      $role = user_role();
      
      // Favori Sayısı
      if ($role !== 'admin' && $role !== 'superadmin') {
        $st = $pdo->prepare("SELECT COUNT(*) FROM bull_favorites WHERE user_id=?");
        $st->execute([current_user_id()]);
        $favCount = (int)$st->fetchColumn();
      }

      // Okunmamış Mesaj Sayısı (Yeni Eklenen)
      $stMsg = $pdo->prepare("SELECT COUNT(*) FROM messages WHERE receiver_id = ? AND is_read = 0");
      $stMsg->execute([current_user_id()]);
      $unreadCount = (int)$stMsg->fetchColumn();

    } catch (Throwable $e) { 
        $favCount = 0; 
        $unreadCount = 0;
    }
  }
?>

<div class="nav">
  <div class="nav-top">
    <div class="brand">🐂 Boğa Spor</div>

    <button type="button" class="menu-toggle" id="menuToggle" aria-label="Menüyü aç/kapat">
      ☰ Menü
      <?php if ($unreadCount > 0): ?>
        <span style="background:#ff4757; width:8px; height:8px; border-radius:50%; display:inline-block;"></span>
      <?php endif; ?>
    </button>
  </div>

  <div class="links" id="navLinks">
    <a href="<?= url('') ?>" class="menu-btn">Ana Sayfa</a>
    <a href="<?= url('bogalar') ?>" class="menu-btn">Boğalar</a>

    <?php if (is_logged_in()): ?>
      <?php $role = user_role(); ?>

      <a href="<?= url('mesajlarim') ?>" class="menu-btn">
        📩 Mesajlar
        <?php if ($unreadCount > 0): ?>
          <span class="msg-badge"><?= $unreadCount ?></span>
        <?php endif; ?>
      </a>

      <?php if ($role === 'admin' || $role === 'superadmin'): ?>
        <a href="<?= url('admin/onay-bekleyen') ?>" class="menu-btn <?= ($title=='Onay Bekleyen Boğalar'?'active':'') ?>">Onay Bekleyenler</a>
        <a href="<?= url('admin/bogalar') ?>" class="menu-btn">Tüm Boğalar</a>
        <a href="<?= url('admin/guresler') ?>" class="menu-btn">Güreşler</a>
        <a href="<?= url('admin/kullanicilar') ?>" class="menu-btn">Kullanıcılar</a>
        <a href="<?= url('admin/takvim') ?>" class="menu-btn">Takvim</a>
        <a href="<?= url('cevrimici') ?>" class="menu-btn">Çevrimiçi Üyeler</a>
      <?php else: ?>
        <a href="<?= url('bogalarim') ?>" class="menu-btn">Boğalarım</a>
        <a href="<?= url('boga-ekle') ?>" class="menu-btn">+ Yeni Boğa</a>
        <a href="<?= url('takvim') ?>" class="menu-btn">Takvim</a>
        <a href="<?= url('karsilastir') ?>" class="menu-btn">Karşılaştırma</a>

        <a href="<?= url('favorilerim') ?>" class="menu-btn">
          Favorilerim
          <?php if ($favCount > 0): ?>
            <span class="badge"><?= (int)$favCount ?></span>
          <?php endif; ?>
        </a>
      <?php endif; ?>

      <div class="account-box" id="accountWrap">
        <a href="<?= url('hesabim') ?>" class="menu-btn">Hesabım</a>
        <button type="button" class="account-caret" id="accountCaret" aria-label="Hesabım menüsü">▾</button>
      </div>

    <?php else: ?>
      <a href="<?= url('takvim') ?>" class="menu-btn">Takvim</a>
      <a href="<?= url('giris') ?>" class="menu-btn">Giriş Yap</a>
      <a href="<?= url('kayit') ?>" class="menu-btn">Kayıt Ol</a>
    <?php endif; ?>
  </div>
</div>

<?php if (is_logged_in()): ?>
  <div class="account-menu" id="accountMenu">
    <a href="<?= url('hesabim') ?>">Hesap Bilgilerim</a>
    <div style="padding:8px 10px;opacity:.75;font-weight:800;font-size:12px;">
      Rol: <?= e(user_role()) ?>
    </div>
    <a href="<?= url('cikis') ?>" style="color:#ff6b6b;">Çıkış</a>
  </div>
<?php endif; ?>

<script>
(function(){
  var toggle = document.getElementById('menuToggle');
  var links  = document.getElementById('navLinks');

  function closeMobileMenu(){
    if(links) links.classList.remove('open');
  }

  if(toggle && links){
    toggle.addEventListener('click', function(){
      links.classList.toggle('open');
    });

    links.addEventListener('click', function(e){
      var a = e.target && e.target.closest ? e.target.closest('a') : null;
      if(!a) return;
      if(window.matchMedia && window.matchMedia('(max-width: 780px)').matches){
        closeMobileMenu();
      }
    });
  }

  var caret = document.getElementById('accountCaret');
  var menu  = document.getElementById('accountMenu');
  if(caret && menu){
    function place(){
      var r = caret.getBoundingClientRect();
      menu.style.top = (r.bottom + 8) + 'px';
    }
    caret.addEventListener('click', function(e){
      e.preventDefault();
      e.stopPropagation();
      if(menu.classList.contains('open')){
        menu.classList.remove('open');
      }else{
        place();
        menu.classList.add('open');
      }
    });
    document.addEventListener('click', function(){ menu.classList.remove('open'); });
    window.addEventListener('resize', function(){ if(menu.classList.contains('open')) place(); });
  }

  // Online ping & Bildirim Kontrolü
  var loggedIn = <?= is_logged_in() ? 'true' : 'false' ?>;
  if(loggedIn){
    function ping(){
      try { fetch("<?= url('online-ping') ?>", { credentials: "same-origin" }); } catch(e){}
    }
    ping();
    setInterval(ping, 30000);
  }
})();
</script>

<div class="main-content">
  <?php if(isset($view) && file_exists($view)) include $view; ?>
</div>

</div>
</body>
</html>