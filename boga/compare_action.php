<?php
declare(strict_types=1);

// File: /boga/compare_action.php
require_once __DIR__ . '/app/functions.php';

csrf_check();

$action = (string)($_POST['action'] ?? 'add');
$id = (int)($_POST['id'] ?? 0);

$ids = array_values(array_unique(array_map('intval', (array)($_SESSION['compare_ids'] ?? []))));

if ($action === 'clear') {
    $ids = [];
} elseif ($id > 0 && $action === 'remove') {
    $ids = array_values(array_filter($ids, static fn($x) => (int)$x !== $id));
} elseif ($id > 0 && $action === 'add') {
    $ids = array_values(array_filter($ids, static fn($x) => (int)$x > 0 && (int)$x !== $id));
    array_unshift($ids, $id);
    $ids = array_slice($ids, 0, 3); // max 3 boğa
}

$_SESSION['compare_ids'] = $ids;

$return = (string)($_POST['return'] ?? '');
if ($return === '' || preg_match('~^https?://~i', $return)) {
    redirect('karsilastir');
}

if ($return[0] === '/') {
    header('Location: ' . $return);
    exit;
}

redirect($return);
