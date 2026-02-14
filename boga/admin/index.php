<?php
declare(strict_types=1);
require_once __DIR__ . '/../app/functions.php';
require_login(); require_admin();
$title='Admin Panel'; $view=__DIR__.'/../views/admin_home.php'; include __DIR__.'/../app/layout.php';
