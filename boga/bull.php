<?php
declare(strict_types=1);

require_once __DIR__ . '/app/functions.php';
require_once __DIR__ . '/app/bulls_extra.php';

/**
 * layout.php url() ve e() kullanıyor. Bazı ortamlarda tanımlı değilse fatal olmasın diye fallback.
 */
if (!function_exists('url')) {
    function url(string $path): string { return $path; }
}
if (!function_exists('e')) {
    function e(string $v): string {
        return htmlspecialchars($v, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}

function tr_slug_local(string $s): string
{
    $map = [
        'Ç'=>'c','ç'=>'c','Ğ'=>'g','ğ'=>'g','İ'=>'i','ı'=>'i','Ö'=>'o','ö'=>'o','Ş'=>'s','ş'=>'s','Ü'=>'u','ü'=>'u',
    ];
    $s = strtr($s, $map);
    $s = strtolower($s);
    $s = preg_replace('~[^a-z0-9]+~', '-', $s) ?? '';
    $s = trim($s, '-');
    return $s !== '' ? $s : 'boga';
}

function canonical_bull_path(int $id, string $name): string
{
    $base = rtrim((string)dirname($_SERVER['SCRIPT_NAME'] ?? ''), '/\\'); // /boga
    $slug = tr_slug_local($name);
    return ($base !== '' ? $base : '') . '/' . $id . '-' . $slug;          // /boga/11-karabas
}

$debug = isset($_GET['debug']);
if ($debug) {
    ini_set('display_errors', '1');
    ini_set('display_startup_errors', '1');
    error_reporting(E_ALL);
}

$title = 'Boğa Detayı';
$bull = null;
$matches = [];
$error = null;

$views_count = 0;
$head_meta = '';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
if (!$id) {
    http_response_code(404);
    $error = 'Geçersiz boğa ID.';
    $compare_ids = array_values(array_unique(array_map('intval', (array)($_SESSION['compare_ids'] ?? []))));
    $in_compare = false;

    $view = __DIR__ . '/views/bull_detail.php';
    include __DIR__ . '/app/layout.php';
    exit;
}

try {
    $pdo = pdo();

    // ✅ SADECE tıklanan boğa
    $stmt = $pdo->prepare("SELECT * FROM bulls WHERE id = :id AND status = 'approved' LIMIT 1");
    $stmt->execute([':id' => $id]);
    $bull = $stmt->fetch(PDO::FETCH_ASSOC) ?: null;

    if (!$bull) {
        http_response_code(404);
        $error = 'Boğa bulunamadı veya yayında değil.';
    } else {
        $title = (string)($bull['name'] ?? 'Boğa Detayı');

        // ✅ Canonical redirect: bull.php?id=.. veya /boga/11 -> /boga/11-slug
        $reqPath = (string)(parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH) ?? '');
        $base = rtrim((string)dirname($_SERVER['SCRIPT_NAME'] ?? ''), '/\\');
        $numericPath = ($base !== '' ? $base : '') . '/' . $id;
        $target = canonical_bull_path($id, (string)($bull['name'] ?? 'boga'));

        $needsRedirect =
            (strpos($reqPath, 'bull.php') !== false) ||                 // bull.php?id=...
            (rtrim($reqPath, '/') === rtrim($numericPath, '/'));        // /boga/11

        if (!$debug && $needsRedirect && rtrim($reqPath, '/') !== rtrim($target, '/')) {
            header('Location: ' . $target, true, 301);
            exit;
        }

        // ✅ Görüntülenme sayacı (popüler boğalar için)
        increment_bull_view($pdo, (int)$id);
        $views_count = get_bull_views($pdo, (int)$id);

        // ✅ OG / Social meta (WhatsApp kartı)
        $canonicalAbs = absolute_url($target);

        $imgAbs = '';
        if (!empty($bull['image'])) {
            $imgAbs = absolute_url(url('uploads/bulls/' . basename((string)$bull['image'])));
        }

        $ogTitle = (string)($bull['name'] ?? 'Boğa Detayı');
        $ogDescParts = [];
        if (!empty($bull['owner_name'])) $ogDescParts[] = 'Sahibi: ' . (string)$bull['owner_name'];
        $loc = trim((string)($bull['city'] ?? '') . ' / ' . (string)($bull['district'] ?? ''));
        if ($loc !== '/') { $loc = trim($loc, ' /'); }
        if ($loc !== '') $ogDescParts[] = 'Konum: ' . $loc;
        $ogDesc = implode(' — ', $ogDescParts);

        $head_meta = "\n" .
            '<meta name="description" content="' . e($ogDesc) . '">' . "\n" .
            '<meta property="og:type" content="article">' . "\n" .
            '<meta property="og:locale" content="tr_TR">' . "\n" .
            '<meta property="og:title" content="' . e($ogTitle) . '">' . "\n" .
            '<meta property="og:description" content="' . e($ogDesc) . '">' . "\n" .
            '<meta property="og:url" content="' . e($canonicalAbs) . '">' . "\n";

        if ($imgAbs !== '') {
            $head_meta .= '<meta property="og:image" content="' . e($imgAbs) . '">' . "\n" .
                         '<meta property="og:image:alt" content="' . e($ogTitle) . '">' . "\n" .
                         '<meta name="twitter:card" content="summary_large_image">' . "\n" .
                         '<meta name="twitter:image" content="' . e($imgAbs) . '">' . "\n";
        } else {
            $head_meta .= '<meta name="twitter:card" content="summary">' . "\n";
        }

        // ✅ SADECE bu boğanın maçları
        $m = $pdo->prepare("
            SELECT bm.match_date, bm.opponent_text, bm.result_text, a.name AS arena_name
            FROM bull_matches bm
            LEFT JOIN arenas a ON a.id = bm.arena_id
            WHERE bm.bull_id = :id
            ORDER BY bm.match_date DESC, bm.id DESC
            LIMIT 20
        ");
        $m->execute([':id' => $id]);
        $matches = $m->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }
} catch (Throwable $e) {
    error_log('bull.php error: ' . $e->getMessage());
    http_response_code(500);
    $error = $debug ? ('HATA: ' . $e->getMessage()) : 'Veri alınırken hata oluştu.';
}

$compare_ids = array_values(array_unique(array_map('intval', (array)($_SESSION['compare_ids'] ?? []))));
$in_compare = in_array((int)$id, $compare_ids, true);

// --- FAVORİ DURUMU (sayfa açılışında buton doğru gelsin) ---
$is_favorite = false;

if (is_logged_in() && $id > 0) {
    try {
        // tablo yoksa hata vermesin
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS bull_favorites (
                id INT UNSIGNED NOT NULL AUTO_INCREMENT,
                user_id INT UNSIGNED NOT NULL,
                bull_id INT UNSIGNED NOT NULL,
                created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (id),
                UNIQUE KEY uniq_user_bull (user_id, bull_id),
                KEY idx_user (user_id),
                KEY idx_bull (bull_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ");

        $st = $pdo->prepare("SELECT 1 FROM bull_favorites WHERE user_id=? AND bull_id=? LIMIT 1");
        $st->execute([current_user_id(), $id]);
        $is_favorite = (bool)$st->fetchColumn();
    } catch (Throwable $e) {
        $is_favorite = false;
    }
}


$view = __DIR__ . '/views/bull_detail.php';
include __DIR__ . '/app/layout.php';
