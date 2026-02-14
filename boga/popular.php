<?php
declare(strict_types=1);

// File: /boga/popular.php
require_once __DIR__ . '/app/functions.php';
require_once __DIR__ . '/app/bulls_extra.php';

$title = 'Popüler Boğalar';
$error = null;
$bulls = [];

try {
    $pdo = pdo();
    $bulls = get_popular_bulls($pdo, 50);
} catch (Throwable $e) {
    error_log('popular.php error: ' . $e->getMessage());
    $error = 'Veri alınırken hata oluştu.';
}

$view = __DIR__ . '/views/popular.php';
include __DIR__ . '/app/layout.php';
