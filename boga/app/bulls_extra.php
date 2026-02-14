<?php
declare(strict_types=1);

/**
 * File: /boga/app/bulls_extra.php
 * Popularity (view counts) + small helpers used by bull.php / index.php / compare.php.
 */

function ensure_bull_views_table(PDO $pdo): void
{
    try {
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS bull_views (
                bull_id INT NOT NULL PRIMARY KEY,
                views INT NOT NULL DEFAULT 0,
                updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");
    } catch (Throwable $e) {
        // ignore if no permission
    }
}

function increment_bull_view(PDO $pdo, int $bullId): void
{
    if ($bullId <= 0) return;
    ensure_bull_views_table($pdo);

    try {
        $stmt = $pdo->prepare("
            INSERT INTO bull_views (bull_id, views) VALUES (:id, 1)
            ON DUPLICATE KEY UPDATE views = views + 1
        ");
        $stmt->execute([':id' => $bullId]);
    } catch (Throwable $e) {
        // ignore
    }
}

function get_bull_views(PDO $pdo, int $bullId): int
{
    if ($bullId <= 0) return 0;
    ensure_bull_views_table($pdo);

    try {
        $stmt = $pdo->prepare("SELECT views FROM bull_views WHERE bull_id=:id LIMIT 1");
        $stmt->execute([':id' => $bullId]);
        return (int)($stmt->fetchColumn() ?: 0);
    } catch (Throwable $e) {
        return 0;
    }
}

function get_popular_bulls(PDO $pdo, int $limit = 8): array
{
    ensure_bull_views_table($pdo);

    $limit = max(1, min(50, $limit));

    try {
        $stmt = $pdo->query("
            SELECT b.id, b.name, b.owner_name, b.city, b.district, b.image, COALESCE(v.views,0) AS views
            FROM bulls b
            LEFT JOIN bull_views v ON v.bull_id = b.id
            WHERE b.status='approved'
            ORDER BY COALESCE(v.views,0) DESC, b.id DESC
            LIMIT {$limit}
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    } catch (Throwable $e) {
        return [];
    }
}

function absolute_url(string $pathOrUrl): string
{
    if ($pathOrUrl === '') return '';

    if (preg_match('~^https?://~i', $pathOrUrl)) return $pathOrUrl;

    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = (string)($_SERVER['HTTP_HOST'] ?? '');

    if ($host === '') return $pathOrUrl;
    if ($pathOrUrl[0] !== '/') $pathOrUrl = '/' . $pathOrUrl;

    return $scheme . '://' . $host . $pathOrUrl;
}
