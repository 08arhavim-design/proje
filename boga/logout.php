<?php
declare(strict_types=1);
require_once __DIR__ . '/app/functions.php';
session_destroy(); session_start();
flash_set('ok','Çıkış yapıldı.');
redirect('/');
