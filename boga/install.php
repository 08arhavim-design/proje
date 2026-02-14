<?php
require_once __DIR__ . '/app/functions.php';

$pdo = pdo();
$ok = true;
$msgs = [];

function exec_sql(PDO $pdo, string $sql, array &$msgs) {
  $pdo->exec($sql);
  $msgs[] = "OK: " . preg_replace('/\s+/', ' ', trim(substr($sql,0,120))) . (strlen($sql)>120?'...':'');
}

try {
  exec_sql($pdo, "CREATE TABLE IF NOT EXISTS users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    role ENUM('user','admin','superadmin') NOT NULL DEFAULT 'user',
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at DATETIME NOT NULL
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;", $msgs);

  exec_sql($pdo, "CREATE TABLE IF NOT EXISTS categories (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;", $msgs);

  exec_sql($pdo, "CREATE TABLE IF NOT EXISTS arenas (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    city VARCHAR(100) NULL
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;", $msgs);

  exec_sql($pdo, "CREATE TABLE IF NOT EXISTS bulls (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    age INT NOT NULL DEFAULT 0,
    wins_text TEXT NULL,
    mother VARCHAR(100) NULL,
    father VARCHAR(100) NULL,
    last_arena_text VARCHAR(150) NULL,
    last_opponent_text VARCHAR(150) NULL,
    owner_name VARCHAR(150) NULL,
    category_id INT UNSIGNED NULL,
    image VARCHAR(255) NULL,
    created_by INT UNSIGNED NOT NULL,
    status ENUM('pending','approved') NOT NULL DEFAULT 'pending',
    created_at DATETIME NOT NULL
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;", $msgs);

  exec_sql($pdo, "CREATE TABLE IF NOT EXISTS bull_matches (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    bull_id INT UNSIGNED NOT NULL,
    arena_id INT UNSIGNED NOT NULL,
    match_date DATE NOT NULL,
    opponent_text VARCHAR(150) NULL,
    result_text VARCHAR(80) NULL,
    created_at DATETIME NOT NULL
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;", $msgs);

  exec_sql($pdo, "CREATE TABLE IF NOT EXISTS bull_favorites (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    bull_id INT UNSIGNED NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_user_bull (user_id, bull_id),
    KEY idx_bull (bull_id),
    CONSTRAINT fk_fav_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_fav_bull FOREIGN KEY (bull_id) REFERENCES bulls(id) ON DELETE CASCADE
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;", $msgs);


  exec_sql($pdo, "CREATE TABLE IF NOT EXISTS rate_limits (
    `key` VARCHAR(190) NOT NULL,
    attempts INT NOT NULL DEFAULT 0,
    first_ts INT NOT NULL,
    last_ts INT NOT NULL,
    PRIMARY KEY (`key`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;", $msgs);

  exec_sql($pdo, "CREATE TABLE IF NOT EXISTS otp_codes (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    code_hash VARCHAR(255) NOT NULL,
    expires_at DATETIME NOT NULL,
    attempts INT NOT NULL DEFAULT 0,
    last_sent_at DATETIME NULL,
    verified_at DATETIME NULL,
    created_at DATETIME NOT NULL
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;", $msgs);

  // Seed categories/arenas (optional)
  $pdo->exec("INSERT IGNORE INTO categories(name) VALUES ('Baş'),('Orta'),('Ayak');");
  $pdo->exec("INSERT IGNORE INTO arenas(id,name,city) VALUES (1,'Arhavi Meydanı','Artvin');");

  // Seed superadmin only if empty
  $c = (int)$pdo->query("SELECT COUNT(*) AS c FROM users")->fetch()['c'];
  if ($c === 0) {
    $hash = password_hash('me59@VUhiItj5o_s', PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO users(username,password,phone,role,is_active,created_at) VALUES(?,?,?,?,1,NOW())");
    $stmt->execute(['superadmin',$hash,'905324643708','superadmin']);
    $msgs[] = "OK: superadmin oluşturuldu (905324643708).";
  } else {
    $msgs[] = "Bilgi: users tablosu dolu, seed atlandı.";
  }

} catch (Throwable $e) {
  $ok = false;
  $msgs[] = "HATA: " . $e->getMessage();
}

$title = 'Kurulum';
$view = __DIR__ . '/views/install.php';
include __DIR__ . '/app/layout.php';
