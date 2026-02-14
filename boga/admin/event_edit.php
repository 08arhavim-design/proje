<?php
require_once __DIR__ . '/../app/functions.php';
require_login();
require_admin();

$pdo = pdo();
$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) redirect('admin/events.php');

$stmt = $pdo->prepare("SELECT * FROM events WHERE id=? LIMIT 1");
$stmt->execute([$id]);
$event = $stmt->fetch();
if (!$event) redirect('admin/events.php');

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();

    $festival    = trim((string)($_POST['festival'] ?? ''));
    $title       = trim((string)($_POST['title'] ?? ''));
    $city        = trim((string)($_POST['city'] ?? ''));
    $district    = trim((string)($_POST['district'] ?? ''));
    $location    = trim((string)($_POST['location'] ?? ''));
    $event_date  = trim((string)($_POST['event_date'] ?? ''));
    $start_time  = trim((string)($_POST['start_time'] ?? ''));
    $categories  = trim((string)($_POST['categories'] ?? ''));
    $has_award   = !empty($_POST['has_award']) ? 1 : 0;
    $notes       = trim((string)($_POST['notes'] ?? ''));

    if ($title === '' || $event_date === '') {
        $error = 'Başlık ve tarih zorunludur.';
    } else {
        $stmt = $pdo->prepare("UPDATE events
                               SET festival=?, title=?, city=?, district=?, location=?, event_date=?, start_time=?, categories=?, has_award=?, notes=?
                               WHERE id=?");
        $stmt->execute([
            $festival ?: null,
            $title,
            $city ?: null,
            $district ?: null,
            $location ?: null,
            $event_date,
            $start_time ?: null,
            $categories ?: null,
            $has_award,
            $notes ?: null,
            $id
        ]);
        redirect('admin/events.php');
    }
}

$title = 'Etkinlik Düzenle';
$view  = __DIR__ . '/../views/admin_event_form.php';
include __DIR__ . '/../app/layout.php';
