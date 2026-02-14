<?php
// File: /boga/favorite.php
declare(strict_types=1);

require_once __DIR__ . '/app/functions.php';

$isAjax =
    (($_POST['ajax'] ?? '') === '1') ||
    (strtolower((string)($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '')) === 'xmlhttprequest') ||
    (str_contains(strtolower((string)($_SERVER['HTTP_ACCEPT'] ?? '')), 'application/json'));

$sendJson = static function (int $code, array $payload): void {
    http_response_code($code);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($payload, JSON_UNESCAPED_UNICODE);
    exit;
};

if (!is_logged_in()) {
    if ($isAjax) $sendJson(401, ['ok' => false, 'message' => 'Giriş yapmalısınız.']);
    redirect('login.php');
}

/** CSRF (functions.php içindeki csrf_check() die() yaptığı için burada AJAX uyumlu kontrol) */
$csrf = (string)($_POST['csrf'] ?? '');
if (empty($_SESSION['__csrf']) || !hash_equals((string)$_SESSION['__csrf'], $csrf)) {
    if ($isAjax) $sendJson(400, ['ok' => false, 'message' => 'CSRF Hatası!']);
    die('CSRF Hatası!');
}

$pdo = pdo();

/** Hem bull_id hem id destekle */
$bullId = filter_input(INPUT_POST, 'bull_id', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
if (!$bullId) {
    $bullId = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
}
if (!$bullId) {
    if ($isAjax) $sendJson(422, ['ok' => false, 'message' => 'Geçersiz boğa ID.']);
    flash_set('error', 'Geçersiz boğa ID.');
    redirect('');
}

$userId = current_user_id();

/** Tablo yoksa oluştur */
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

try {
    $stmt = $pdo->prepare("SELECT id FROM bull_favorites WHERE user_id=? AND bull_id=? LIMIT 1");
    $stmt->execute([$userId, $bullId]);
    $favId = (int)($stmt->fetchColumn() ?: 0);

    $isFavoriteNow = false;
    $msg = '';

    if ($favId > 0) {
        $del = $pdo->prepare("DELETE FROM bull_favorites WHERE id=?");
        $del->execute([$favId]);
        $isFavoriteNow = false;
        $msg = 'Favorilerden çıkarıldı.';
        flash_set('success', $msg);
    } else {
        $ins = $pdo->prepare("INSERT IGNORE INTO bull_favorites (user_id, bull_id) VALUES (?, ?)");
        $ins->execute([$userId, $bullId]);
        $isFavoriteNow = true;
        $msg = 'Favorilere eklendi.';
        flash_set('success', $msg);
    }

    $cntStmt = $pdo->prepare("SELECT COUNT(*) FROM bull_favorites WHERE user_id=?");
    $cntStmt->execute([$userId]);
    $favCount = (int)$cntStmt->fetchColumn();

    if ($isAjax) {
        $sendJson(200, [
            'ok' => true,
            'is_favorite' => $isFavoriteNow,
            'favorites_count' => $favCount,
            'message' => $msg,
        ]);
    }
} catch (Throwable $e) {
    error_log('favorite.php error: ' . $e->getMessage());
    if ($isAjax) $sendJson(500, ['ok' => false, 'message' => 'İşlem sırasında hata oluştu.']);
    flash_set('error', 'İşlem sırasında hata oluştu.');
}

/** geri dön */
$return = (string)($_POST['return'] ?? '');
if ($return === '') $return = (string)($_SERVER['HTTP_REFERER'] ?? '');
if ($return !== '') {
    $path = (string)(parse_url($return, PHP_URL_PATH) ?? '');
    if ($path !== '') {
        header('Location: ' . $path, true, 302);
        exit;
    }
}

redirect('');
