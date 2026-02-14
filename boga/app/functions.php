<?php
declare(strict_types=1);

if (session_status() !== PHP_SESSION_ACTIVE) { session_start(); }

/* CONFIG VE VERİTABANI */
$__cfg = __DIR__ . '/config.php';
$__config = is_file($__cfg) ? require $__cfg : null;

if (is_array($__config)) {
    $db = $__config['db'] ?? [];
    if (!defined('DB_HOST')) define('DB_HOST', (string)($db['host'] ?? 'localhost'));
    if (!defined('DB_NAME')) define('DB_NAME', (string)($db['name'] ?? ''));
    if (!defined('DB_USER')) define('DB_USER', (string)($db['user'] ?? ''));
    if (!defined('DB_PASS')) define('DB_PASS', (string)($db['pass'] ?? ''));
    if (!defined('DB_CHARSET')) define('DB_CHARSET', (string)($db['charset'] ?? 'utf8mb4'));
} else {
    if (!defined('DB_HOST')) define('DB_HOST', 'localhost');
    if (!defined('DB_NAME')) define('DB_NAME', '');
    if (!defined('DB_USER')) define('DB_USER', '');
    if (!defined('DB_PASS')) define('DB_PASS', '');
    if (!defined('DB_CHARSET')) define('DB_CHARSET', 'utf8mb4');
}

/* URL VE KLASÖR YÖNETİMİ */
if (!defined('SITE_SUBDIR')) define('SITE_SUBDIR', '/boga');

/** base_url alias */
if (!function_exists('base_url')) {
    function base_url(string $path = ''): string {
        return SITE_SUBDIR . '/' . ltrim($path, '/');
    }
}

if (!function_exists('url')) {
    function url(string $path = ''): string {
        return SITE_SUBDIR . '/' . ltrim($path, '/');
    }
}

/** * dm.php hatasını gideren fonksiyon 
 */
if (!function_exists('current_full_url')) {
    function current_full_url(): string {
        $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? '';
        $uri = $_SERVER['REQUEST_URI'] ?? '';
        return $scheme . '://' . $host . $uri;
    }
}

if (!function_exists('redirect')) {
    function redirect(string $path): void {
        header('Location: ' . url($path));
        exit;
    }
}

/* GÜVENLİK VE KAÇIŞ */
if (!function_exists('e')) {
    function e($s): string {
        if ($s === null) $s = '';
        return htmlspecialchars((string)$s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}

if (!function_exists('esc')) {
    function esc($s): string { return e($s); }
}

/* PDO BAĞLANTISI */
if (!function_exists('pdo')) {
    function pdo(): PDO {
        static $pdo = null;
        if ($pdo instanceof PDO) return $pdo;

        $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;
        $pdo = new PDO($dsn, DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
        return $pdo;
    }
}

/* YETKİ VE OTURUM */
if (!function_exists('is_logged_in')) {
    function is_logged_in(): bool { return !empty($_SESSION['user_id']); }
}
if (!function_exists('user_role')) {
    function user_role(): string { return (string)($_SESSION['role'] ?? ''); }
}
if (!function_exists('current_user_id')) {
    function current_user_id(): int { return (int)($_SESSION['user_id'] ?? 0); }
}
if (!function_exists('require_login')) {
    function require_login(): void { if (!is_logged_in()) redirect('login.php'); }
}
if (!function_exists('require_admin')) {
    function require_admin(): void {
        $r = user_role();
        if ($r !== 'admin' && $r !== 'superadmin') redirect('');
    }
}

/* CSRF İŞLEMLERİ */
if (!function_exists('csrf_token')) {
    function csrf_token(): string {
        if (empty($_SESSION['__csrf'])) $_SESSION['__csrf'] = bin2hex(random_bytes(16));
        return (string)$_SESSION['__csrf'];
    }
}

if (!function_exists('validate_csrf')) {
    function validate_csrf(?string $token): bool {
        if (!$token || empty($_SESSION['__csrf'])) return false;
        return hash_equals((string)$_SESSION['__csrf'], (string)$token);
    }
}

if (!function_exists('csrf_check')) {
    function csrf_check(): void {
        $t = (string)($_POST['csrf'] ?? '');
        if (empty($_SESSION['__csrf']) || !hash_equals((string)$_SESSION['__csrf'], $t)) {
            die('CSRF Hatası!');
        }
    }
}


/* SEO SLUG */
if (!function_exists('tr_slug')) {
    function tr_slug(string $s): string {
        $map = ['Ç'=>'c','ç'=>'c','Ğ'=>'g','ğ'=>'g','İ'=>'i','ı'=>'i','Ö'=>'o','ö'=>'o','Ş'=>'s','ş'=>'s','Ü'=>'u','ü'=>'u'];
        $s = strtr($s, $map);
        $s = strtolower($s);
        $s = preg_replace('~[^a-z0-9]+~', '-', $s) ?? '';
        $s = trim($s, '-');
        return $s !== '' ? $s : 'boga';
    }
}

/* FAVORİLER */
if (!function_exists('is_bull_favorite')) {
    function is_bull_favorite(PDO $pdo, int $userId, int $bullId): bool {
        try {
            $st = $pdo->prepare("SELECT 1 FROM bull_favorites WHERE user_id=? AND bull_id=? LIMIT 1");
            $st->execute([$userId, $bullId]);
            return (bool)$st->fetchColumn();
        } catch (Throwable $e) { return false; }
    }
}

/* MESAJ BİLDİRİMLERİ */
if (!function_exists('get_unread_message_count')) {
    function get_unread_message_count(): int {
        if (!is_logged_in()) return 0;
        try {
            $db_temp = pdo();
            $stmt = $db_temp->prepare("SELECT COUNT(*) FROM messages WHERE receiver_id = ? AND is_read = 0");
            $stmt->execute([current_user_id()]);
            return (int)$stmt->fetchColumn();
        } catch (Exception $e) { return 0; }
    }
}

/* ÇEVRİMİÇİ TAKİP */
if (is_logged_in() && !isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
    try {
        $db_online = pdo();
        $st_online = $db_online->prepare("REPLACE INTO user_presence (user_id, last_seen, ip) VALUES (?, NOW(), ?)");
        $st_online->execute([current_user_id(), $_SERVER['REMOTE_ADDR'] ?? '']);
    } catch (Throwable $e) {}
}