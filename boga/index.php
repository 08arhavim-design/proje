<?php
require_once __DIR__ . '/app/functions.php';
require_once __DIR__ . '/app/bulls_extra.php';

$title = 'Ana Sayfa';
$stats = ['bulls'=>0,'users'=>0,'matches'=>0,'arenas'=>0];

$bulls_latest = [];
$bulls_popular = [];
$users_latest = [];
$matches_latest = [];
$events_upcoming = [];
$online_users = [];

$error = null;

try {
    $pdo = pdo();

    // İstatistikler
    $stats['bulls'] = (int)$pdo->query("SELECT COUNT(*) FROM bulls WHERE status='approved'")->fetchColumn();
    $stats['users'] = (int)$pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
    $stats['arenas'] = (int)$pdo->query("SELECT COUNT(*) FROM arenas")->fetchColumn();
    $stats['matches'] = (int)$pdo->query("SELECT COUNT(*) FROM bull_matches")->fetchColumn();

    // Çevrimiçi Üyeleri Getir (Son 5 dakika)
    if ($pdo->query("SHOW TABLES LIKE 'user_presence'")->fetchColumn()) {
        $stmt_online = $pdo->query("
            SELECT u.id, u.username, u.full_name, u.role 
            FROM user_presence up 
            JOIN users u ON u.id = up.user_id 
            WHERE up.last_seen > NOW() - INTERVAL 5 MINUTE 
            ORDER BY up.last_seen DESC
        ");
        $online_users = $stmt_online->fetchAll();
    }

    // Popüler boğalar (Önce bunu çekiyoruz)
    $bulls_popular = get_popular_bulls($pdo, 8);

    // Son eklenen boğalar
    $stmt = $pdo->query("SELECT id,name,owner_name,city,district,image,created_at FROM bulls WHERE status='approved' ORDER BY id DESC LIMIT 4");
    $bulls_latest = $stmt->fetchAll();

    // Son üyeler
    $stmt = $pdo->query("SELECT id,username,full_name,created_at FROM users ORDER BY id DESC LIMIT 6");
    $users_latest = $stmt->fetchAll();

    // Son güreşler
    $stmt = $pdo->query("
      SELECT bm.match_date, bm.result_text, bm.opponent_text, a.name AS arena_name, b.name AS bull_name
      FROM bull_matches bm
      LEFT JOIN arenas a ON a.id=bm.arena_id
      LEFT JOIN bulls b ON b.id=bm.bull_id
      ORDER BY bm.match_date DESC, bm.id DESC
      LIMIT 5
    ");
    $matches_latest = $stmt->fetchAll();

    // Yaklaşan takvim
    if ($pdo->query("SHOW TABLES LIKE 'events'")->fetchColumn()) {
        $stmt = $pdo->query("SELECT * FROM events WHERE event_date >= CURDATE() ORDER BY event_date ASC LIMIT 5");
        $events_upcoming = $stmt->fetchAll();
    }

} catch (Throwable $e) {
    error_log('index.php error: ' . $e->getMessage());
    $error = 'Hata: ' . $e->getMessage();
}

$view = __DIR__ . '/views/home.php';
include __DIR__ . '/app/layout.php';