<?php
require_once __DIR__ . '/../app/functions.php';
require_login();
require_superadmin();
$title = 'Yeni kullanıcı';
$view = __DIR__ . '/../views/admin_user_new.php';
include __DIR__ . '/../app/layout.php';
