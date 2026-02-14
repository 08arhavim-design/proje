<?php
declare(strict_types=1);

require_once __DIR__ . '/app/functions.php';
require_once __DIR__ . '/app/locations.php';

$title = 'Boğalar';
$error = null;

$bulls = [];
$cities = [];
$districts = [];
$neighborhoods = [];

$mode = 'json';

$q = trim((string)($_GET['q'] ?? ''));
$city = trim((string)($_GET['city'] ?? ''));
$district = trim((string)($_GET['district'] ?? ''));
$neighborhood = trim((string)($_GET['neighborhood'] ?? ''));

$selectedCityName = '';
$selectedDistrictName = '';
$selectedNeighborhoodName = '';

try {
    $pdo = pdo();

    $mode = trloc_mode($pdo);

    $cities = trloc_get_cities($pdo, $mode);

    $selectedCityName = trloc_resolve_city_name($pdo, $mode, $city);
    $districts = $city !== '' ? trloc_get_districts($pdo, $mode, $city) : [];

    $selectedDistrictName = trloc_resolve_district_name($pdo, $mode, $district);
    $neighborhoods = $district !== '' ? trloc_get_neighborhoods($pdo, $mode, $district) : [];

    $selectedNeighborhoodName = trloc_resolve_neighborhood_name($pdo, $mode, $neighborhood);

    $where = ["status='approved'"];
    $params = [];

    if ($q !== '') {
        $where[] = "(name LIKE :q OR owner_name LIKE :q)";
        $params[':q'] = '%' . $q . '%';
    }
    if ($selectedCityName !== '') {
        $where[] = "city = :city";
        $params[':city'] = $selectedCityName;
    }
    if ($selectedDistrictName !== '') {
        $where[] = "district = :district";
        $params[':district'] = $selectedDistrictName;
    }

    $hasVillageCol = trloc_column_exists($pdo, 'bulls', 'village');
    if ($hasVillageCol && $selectedNeighborhoodName !== '') {
        $where[] = "village = :village";
        $params[':village'] = $selectedNeighborhoodName;
    }

    $sql = "SELECT id, name, owner_name, city, district"
         . ($hasVillageCol ? ", village" : "")
         . ", image, created_at
            FROM bulls
            WHERE " . implode(' AND ', $where) . "
            ORDER BY id DESC
            LIMIT 60";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $bulls = $stmt->fetchAll();

} catch (Throwable $e) {
    $error = 'Veri alınırken hata oluştu.';
}

$view = __DIR__ . '/views/bulls.php';
include __DIR__ . '/app/layout.php';
