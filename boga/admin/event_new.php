<?php
require_once __DIR__ . '/../app/functions.php';
require_login();
require_admin();

$pdo = pdo();
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
        $stmt = $pdo->prepare("INSERT INTO events(festival,title,city,district,location,event_date,start_time,categories,has_award,notes)
                               VALUES(?,?,?,?,?,?,?,?,?,?)");
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
            $notes ?: null
        ]);
        redirect('admin/events.php');
    }
}

$title = 'Etkinlik Ekle';
$view  = __DIR__ . '/../views/admin_event_form.php';
$event = [
  'festival'=>'','title'=>'','city'=>'','district'=>'','location'=>'',
  'event_date'=>'','start_time'=>'','categories'=>'','has_award'=>0,'notes'=>''
];
include __DIR__ . '/../app/layout.php';
