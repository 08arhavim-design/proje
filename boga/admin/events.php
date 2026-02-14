<?php
declare(strict_types=1);

require_once __DIR__ . '/../app/functions.php';
require_admin();

$pdo = pdo();

$title = 'Takvim Yönetimi';
$view  = __DIR__ . '/../views/admin_events.php';

$error = '';
$ok = '';

/**
 * EVENTS TABLOSU: bazı kurulumlarda adı farklı olabiliyor.
 * Önce mevcut tablolara bakıp varsa onu kullanıyoruz; yoksa "events" oluşturuyoruz.
 */
function find_events_table(PDO $pdo): string {
    $candidates = ['events', 'calendar_events', 'bull_events', 'takvim', 'takvim_events'];
    foreach ($candidates as $t) {
        $st = $pdo->prepare("SHOW TABLES LIKE :t");
        $st->execute([':t' => $t]);
        if ($st->fetchColumn()) return $t;
    }
    return 'events';
}

$eventsTable = find_events_table($pdo);

// tablo yoksa oluştur
try {
    $st = $pdo->prepare("SHOW TABLES LIKE :t");
    $st->execute([':t' => $eventsTable]);
    $exists = (bool)$st->fetchColumn();

    if (!$exists && $eventsTable === 'events') {
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS `events` (
              `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
              `event_date` DATE NOT NULL,
              `start_time` TIME NULL,
              `title` VARCHAR(255) NOT NULL,
              `location` VARCHAR(255) NULL,
              `notes` TEXT NULL,
              `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
              `updated_at` DATETIME NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
              PRIMARY KEY (`id`),
              KEY `idx_event_date` (`event_date`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ");
        $eventsTable = 'events';
    }
} catch (Throwable $e) {
    $error = 'Takvim tablosu kontrol edilirken hata oluştu.';
}

/**
 * Kolon uyumluluğu: bazı DB’lerde kolon isimleri farklı olabiliyor.
 * Elimizdeki tabloyu okuyup eşleştirme yapıyoruz.
 */
$cols = [];
try {
    foreach ($pdo->query("SHOW COLUMNS FROM `$eventsTable`") as $c) {
        $cols[strtolower((string)$c['Field'])] = true;
    }
} catch (Throwable $e) {
    if (!$error) $error = 'Takvim tablosu okunamadı.';
}

function pick_col(array $cols, array $cands, string $fallback): string {
    foreach ($cands as $c) {
        if (isset($cols[strtolower($c)])) return $c;
    }
    return $fallback;
}

$idCol    = pick_col($cols, ['id'], 'id');
$dateCol  = pick_col($cols, ['event_date','date','tarih'], 'event_date');
$timeCol  = pick_col($cols, ['start_time','time','saat'], 'start_time');
$titleCol = pick_col($cols, ['title','name','baslik'], 'title');
$locCol   = pick_col($cols, ['location','place','yer','alan'], 'location');
$noteCol  = pick_col($cols, ['notes','description','desc','aciklama','detay'], 'notes');

$edit_id = isset($_GET['edit']) ? (int)$_GET['edit'] : 0;
$editing = null;

// CSRF (formlarda name="csrf" bekleniyor)
$csrf = csrf_token();

try {
    // Ekle / Güncelle / Sil
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && empty($error)) {
        csrf_check();

        $action = (string)($_POST['action'] ?? '');

        if ($action === 'add') {
            $event_date = trim((string)($_POST['event_date'] ?? ''));
            $start_time = trim((string)($_POST['start_time'] ?? ''));
            $titleText  = trim((string)($_POST['title'] ?? ''));
            $location   = trim((string)($_POST['location'] ?? ''));
            $notes      = trim((string)($_POST['notes'] ?? ''));

            if ($event_date === '' || $titleText === '') {
                $error = 'Tarih ve başlık zorunlu.';
            } else {
                $sql = "INSERT INTO `$eventsTable` (`$dateCol`, `$timeCol`, `$titleCol`, `$locCol`, `$noteCol`)
                        VALUES (:d, :t, :ti, :l, :n)";
                $st = $pdo->prepare($sql);
                $st->execute([
                    ':d'  => $event_date,
                    ':t'  => ($start_time !== '' ? $start_time : null),
                    ':ti' => $titleText,
                    ':l'  => ($location !== '' ? $location : null),
                    ':n'  => ($notes !== '' ? $notes : null),
                ]);
                $ok = 'Etkinlik eklendi.';
            }
        }

        if ($action === 'update') {
            $id = (int)($_POST['id'] ?? 0);

            $event_date = trim((string)($_POST['event_date'] ?? ''));
            $start_time = trim((string)($_POST['start_time'] ?? ''));
            $titleText  = trim((string)($_POST['title'] ?? ''));
            $location   = trim((string)($_POST['location'] ?? ''));
            $notes      = trim((string)($_POST['notes'] ?? ''));

            if ($id <= 0 || $event_date === '' || $titleText === '') {
                $error = 'Güncelleme için ID, tarih ve başlık zorunlu.';
            } else {
                $sql = "UPDATE `$eventsTable`
                        SET `$dateCol`=:d, `$timeCol`=:t, `$titleCol`=:ti, `$locCol`=:l, `$noteCol`=:n
                        WHERE `$idCol`=:id
                        LIMIT 1";
                $st = $pdo->prepare($sql);
                $st->execute([
                    ':d'  => $event_date,
                    ':t'  => ($start_time !== '' ? $start_time : null),
                    ':ti' => $titleText,
                    ':l'  => ($location !== '' ? $location : null),
                    ':n'  => ($notes !== '' ? $notes : null),
                    ':id' => $id,
                ]);
                $ok = 'Etkinlik güncellendi.';
                $edit_id = 0;
            }
        }

        if ($action === 'delete') {
            $id = (int)($_POST['id'] ?? 0);
            if ($id > 0) {
                $st = $pdo->prepare("DELETE FROM `$eventsTable` WHERE `$idCol`=:id LIMIT 1");
                $st->execute([':id' => $id]);
                $ok = 'Etkinlik silindi.';
            }
        }
    }

    // Edit modu: kayıt çek
    if ($edit_id > 0 && empty($error)) {
        $st = $pdo->prepare("SELECT * FROM `$eventsTable` WHERE `$idCol`=:id LIMIT 1");
        $st->execute([':id' => $edit_id]);
        $editing = $st->fetch() ?: null;
    }

    // Pagination
    $perPage = 6;
    $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
    $offset = ($page - 1) * $perPage;

    $total = (int)$pdo->query("SELECT COUNT(*) FROM `$eventsTable`")->fetchColumn();
    $pages = max(1, (int)ceil($total / $perPage));

    // Liste çek
    // Tarihe göre sıralama: en yakınlar üstte (ASC)
    $sql = "SELECT `$idCol` AS id,
                   `$dateCol` AS event_date,
                   `$timeCol` AS start_time,
                   `$titleCol` AS title,
                   `$locCol` AS location,
                   `$noteCol` AS notes
            FROM `$eventsTable`
            ORDER BY `$dateCol` ASC, `$timeCol` ASC, `$idCol` ASC
            LIMIT $perPage OFFSET $offset";
    $events = $pdo->query($sql)->fetchAll();

} catch (Throwable $e) {
    $error = 'Takvim verileri alınırken hata oluştu.';
    $events = [];
    $pages = 1;
    $page = 1;
    $total = 0;
}

// view'e gidecek değişkenler
$V = [
  'error'   => $error,
  'ok'      => $ok,
  'events'  => $events ?? [],
  'page'    => $page ?? 1,
  'pages'   => $pages ?? 1,
  'total'   => $total ?? 0,
  'csrf'    => $csrf,
  'editing' => $editing,
];

extract($V, EXTR_SKIP);

include __DIR__ . '/../app/layout.php';
