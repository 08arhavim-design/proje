<?php
require_once __DIR__ . '/../app/functions.php';
require_login();
require_admin();
$pdo = pdo();
$title = 'Kategoriler';
$view = __DIR__ . '/../views/admin_categories.php';
include __DIR__ . '/../app/layout.php';
