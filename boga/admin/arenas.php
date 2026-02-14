<?php
require_once __DIR__ . '/../app/functions.php';
require_login();
require_admin();
$pdo = pdo();
$title = 'Meydanlar';
$view = __DIR__ . '/../views/admin_arenas.php';
include __DIR__ . '/../app/layout.php';
