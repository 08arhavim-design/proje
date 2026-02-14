<?php
declare(strict_types=1);

// File: /boga/compare.php
require_once __DIR__ . '/app/functions.php';
require_once __DIR__ . '/app/bulls_extra.php';

$title = 'Karşılaştır';
$error = null;

$ids = array_values(array_unique(array_map('intval', (array)($_SESSION['compare_ids'] ?? []))));

$idsParam = (string)($_GET['ids'] ?? '');
if ($idsParam !== '') {
    $tmp = array_filter(array_map('intval', preg_split('~\\s*,\\s*~', $idsParam) ?: []));
    $tmp = array_values(array_unique($tmp));
    if (!empty($tmp)) {
        $ids = array_slice($tmp, 0, 3);
        $_SESSION['compare_ids'] = $ids;
    }
}

$bulls = [];
$popular = [];

try {
    $pdo = pdo();

    if (!empty($ids)) {
        $in = implode(',', array_fill(0, count($ids), '?'));
        $stmt = $pdo->prepare("SELECT * FROM bulls WHERE status='approved' AND id IN ($in)");
        $stmt->execute($ids);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];

        // preserve order
        $map = [];
        foreach ($rows as $r) $map[(int)$r['id']] = $r;

        foreach ($ids as $id) {
            if (isset($map[$id])) {
                $r = $map[$id];
                $r['views'] = get_bull_views($pdo, (int)$r['id']);
                $bulls[] = $r;
            }
        }
    }

    $popular = get_popular_bulls($pdo, 12);
} catch (Throwable $e) {
    error_log('compare.php error: ' . $e->getMessage());
    $error = 'Veri alınırken hata oluştu.';
}

$view = __DIR__ . '/views/compare.php';
include __DIR__ . '/app/layout.php';
