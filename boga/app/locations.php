<?php
declare(strict_types=1);

/**
 * Location provider:
 * - If DB tables exist: iller/ilceler/mahalleler => full TR data.
 * - Else: 81 city fallback + districts from cached JSON.
 */

function trloc_table_exists(PDO $pdo, string $table): bool
{
    $stmt = $pdo->prepare("SHOW TABLES LIKE ?");
    $stmt->execute([$table]);
    return (bool)$stmt->fetchColumn();
}

function trloc_mode(PDO $pdo): string
{
    if (trloc_table_exists($pdo, 'iller') && trloc_table_exists($pdo, 'ilceler')) {
        return 'db';
    }
    return 'json';
}

function trloc_storage_dir(): string
{
    $root = dirname(__DIR__); // /boga
    $dir = $root . '/storage';
    if (!is_dir($dir)) @mkdir($dir, 0755, true);
    return $dir;
}

function trloc_fetch_url(string $url): ?string
{
    if (function_exists('curl_init')) {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_CONNECTTIMEOUT => 6,
            CURLOPT_TIMEOUT => 12,
            CURLOPT_USERAGENT => 'BogaSpor/1.0',
        ]);
        $out = curl_exec($ch);
        curl_close($ch);
        if (is_string($out) && $out !== '') return $out;
    }

    $ctx = stream_context_create([
        'http' => [
            'timeout' => 12,
            'header'  => "User-Agent: BogaSpor/1.0\r\n",
        ],
    ]);

    $out = @file_get_contents($url, false, $ctx);
    return (is_string($out) && $out !== '') ? $out : null;
}

/**
 * Small JSON source (cities+districts). Cached locally.
 */
function trloc_load_city_districts_json(): array
{
    $cache = trloc_storage_dir() . '/turkey_cities_districts.json';
    $url = 'https://furkandlkdr.github.io/mernis-turkiye-disctricts/turkey_cities_districts.json';

    $maxAge = 60 * 60 * 24 * 90; // 90 days
    $needsRefresh = !is_file($cache) || (time() - (int)@filemtime($cache) > $maxAge);

    if ($needsRefresh) {
        $raw = trloc_fetch_url($url);
        if ($raw) @file_put_contents($cache, $raw);
    }

    $raw = @file_get_contents($cache);
    $data = is_string($raw) ? json_decode($raw, true) : null;

    if (!is_array($data)) return ['cities' => [], 'districts' => []];

    $cities = [];
    $districts = [];

    $rows = $data['cities'] ?? $data; // some sources may be direct array
    if (!is_array($rows)) return ['cities' => [], 'districts' => []];

    foreach ($rows as $c) {
        $name = (string)($c['name'] ?? '');
        if ($name === '') continue;
        $cities[] = $name;

        $dlist = [];
        foreach (($c['districts'] ?? []) as $d) {
            $dn = (string)($d['name'] ?? $d);
            if ($dn !== '') $dlist[] = $dn;
        }
        $districts[$name] = $dlist;
    }

    return ['cities' => $cities, 'districts' => $districts];
}

/**
 * Guaranteed 81 provinces fallback (if remote JSON is blocked)
 */
function trloc_builtin_cities(): array
{
    return [
        'Adana','Adıyaman','Afyonkarahisar','Ağrı','Aksaray','Amasya','Ankara','Antalya','Ardahan','Artvin',
        'Aydın','Balıkesir','Bartın','Batman','Bayburt','Bilecik','Bingöl','Bitlis','Bolu','Burdur','Bursa',
        'Çanakkale','Çankırı','Çorum','Denizli','Diyarbakır','Düzce','Edirne','Elazığ','Erzincan','Erzurum',
        'Eskişehir','Gaziantep','Giresun','Gümüşhane','Hakkari','Hatay','Iğdır','Isparta','İstanbul','İzmir',
        'Kahramanmaraş','Karabük','Karaman','Kars','Kastamonu','Kayseri','Kilis','Kırıkkale','Kırklareli','Kırşehir',
        'Kocaeli','Konya','Kütahya','Malatya','Manisa','Mardin','Mersin','Muğla','Muş','Nevşehir','Niğde','Ordu',
        'Osmaniye','Rize','Sakarya','Samsun','Şanlıurfa','Siirt','Sinop','Şırnak','Sivas','Tekirdağ','Tokat',
        'Trabzon','Tunceli','Uşak','Van','Yalova','Yozgat','Zonguldak'
    ];
}

/**
 * Returns cities options: [ ['value'=>..., 'label'=>...], ... ]
 */
function trloc_get_cities(PDO $pdo, string $mode): array
{
    if ($mode === 'db') {
        $rows = $pdo->query("SELECT id, il_adi FROM iller ORDER BY il_adi ASC")->fetchAll(PDO::FETCH_ASSOC);
        $out = [];
        foreach ($rows as $r) {
            $out[] = ['value' => (string)$r['id'], 'label' => (string)$r['il_adi']];
        }
        return $out;
    }

    $json = trloc_load_city_districts_json();
    $cities = $json['cities'] ?? [];
    if (!is_array($cities) || !$cities) $cities = trloc_builtin_cities();

    $out = [];
    foreach ($cities as $c) {
        $c = (string)$c;
        if ($c === '') continue;
        $out[] = ['value' => $c, 'label' => $c];
    }
    return $out;
}

function trloc_resolve_city_name(PDO $pdo, string $mode, string $cityValue): string
{
    $cityValue = trim($cityValue);
    if ($cityValue === '') return '';

    if ($mode === 'db' && ctype_digit($cityValue)) {
        $stmt = $pdo->prepare("SELECT il_adi FROM iller WHERE id=? LIMIT 1");
        $stmt->execute([(int)$cityValue]);
        $name = (string)($stmt->fetchColumn() ?: '');
        return $name;
    }

    return $cityValue;
}

function trloc_get_districts(PDO $pdo, string $mode, string $cityValue): array
{
    $cityValue = trim($cityValue);
    if ($cityValue === '') return [];

    if ($mode === 'db') {
        if (!ctype_digit($cityValue)) return [];
        $stmt = $pdo->prepare("SELECT id, ilce_adi FROM ilceler WHERE il_id=? ORDER BY ilce_adi ASC");
        $stmt->execute([(int)$cityValue]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $out = [];
        foreach ($rows as $r) {
            $out[] = ['value' => (string)$r['id'], 'label' => (string)$r['ilce_adi']];
        }
        return $out;
    }

    $json = trloc_load_city_districts_json();
    $byCity = $json['districts'] ?? [];
    $list = is_array($byCity) ? ($byCity[$cityValue] ?? []) : [];
    $out = [];
    if (is_array($list)) {
        foreach ($list as $d) {
            $d = (string)$d;
            if ($d === '') continue;
            $out[] = ['value' => $d, 'label' => $d];
        }
    }
    return $out;
}

function trloc_resolve_district_name(PDO $pdo, string $mode, string $districtValue): string
{
    $districtValue = trim($districtValue);
    if ($districtValue === '') return '';

    if ($mode === 'db' && ctype_digit($districtValue)) {
        $stmt = $pdo->prepare("SELECT ilce_adi FROM ilceler WHERE id=? LIMIT 1");
        $stmt->execute([(int)$districtValue]);
        return (string)($stmt->fetchColumn() ?: '');
    }

    return $districtValue;
}

function trloc_has_neighborhoods(PDO $pdo, string $mode): bool
{
    return $mode === 'db' && trloc_table_exists($pdo, 'mahalleler');
}

function trloc_get_neighborhoods(PDO $pdo, string $mode, string $districtValue): array
{
    $districtValue = trim($districtValue);
    if ($districtValue === '') return [];
    if (!trloc_has_neighborhoods($pdo, $mode)) return [];
    if (!ctype_digit($districtValue)) return [];

    $stmt = $pdo->prepare("SELECT id, mahalle_adi FROM mahalleler WHERE ilce_id=? ORDER BY mahalle_adi ASC");
    $stmt->execute([(int)$districtValue]);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $out = [];
    foreach ($rows as $r) {
        $out[] = ['value' => (string)$r['id'], 'label' => (string)$r['mahalle_adi']];
    }
    return $out;
}

function trloc_resolve_neighborhood_name(PDO $pdo, string $mode, string $neighborhoodValue): string
{
    $neighborhoodValue = trim($neighborhoodValue);
    if ($neighborhoodValue === '') return '';

    if ($mode === 'db' && trloc_has_neighborhoods($pdo, $mode) && ctype_digit($neighborhoodValue)) {
        $stmt = $pdo->prepare("SELECT mahalle_adi FROM mahalleler WHERE id=? LIMIT 1");
        $stmt->execute([(int)$neighborhoodValue]);
        return (string)($stmt->fetchColumn() ?: '');
    }

    return $neighborhoodValue;
}

function trloc_column_exists(PDO $pdo, string $table, string $column): bool
{
    $stmt = $pdo->prepare("SHOW COLUMNS FROM `$table` LIKE ?");
    $stmt->execute([$column]);
    return (bool)$stmt->fetchColumn();
}
