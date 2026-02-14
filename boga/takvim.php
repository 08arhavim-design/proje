<?php
require_once __DIR__ . '/app/functions.php';

$pdo = pdo();
$today = date('Y-m-d');

// Yaklaşan etkinlikler
$stmt = $pdo->prepare("
  SELECT id, festival, title, city, district, location, event_date, start_time, categories, has_award, notes
  FROM events
  WHERE event_date >= CURDATE()
  ORDER BY event_date ASC, start_time ASC, id ASC
  LIMIT 300
");
$stmt->execute();
$upcoming = $stmt->fetchAll();

// Yaklaşan yoksa son etkinlikleri de gösterelim (boş kalmasın)
if (empty($upcoming)) {
  $stmt = $pdo->prepare("
    SELECT id, festival, title, city, district, location, event_date, start_time, categories, has_award, notes
    FROM events
    ORDER BY event_date DESC, start_time DESC, id DESC
    LIMIT 200
  ");
  $stmt->execute();
  $upcoming = $stmt->fetchAll();
}

$title = 'Boğa Güreşleri Takvimi';
$view  = __DIR__ . '/views/takvim.php';
include __DIR__ . '/app/layout.php';
