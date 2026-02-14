<?php
require_once __DIR__ . '/../app/functions.php';
require_login();
require_admin();

$pdo = pdo();
$date = (string)($_GET['date'] ?? '');
$date = preg_match('/^\d{4}-\d{2}-\d{2}$/', $date) ? $date : '';

$sql = "SELECT m.*, b.name AS bull_name, a.name AS arena_name
        FROM bull_matches m
        LEFT JOIN bulls b ON b.id=m.bull_id
        LEFT JOIN arenas a ON a.id=m.arena_id";
$params = [];
if ($date) {
  $sql .= " WHERE m.match_date = ?";
  $params[] = $date;
}
$sql .= " ORDER BY m.match_date DESC, m.id DESC LIMIT 500";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$rows = $stmt->fetchAll();

$title = 'Güreş kayıtları';
$view = __DIR__ . '/../views/admin_matches.php';
include __DIR__ . '/../app/layout.php';
