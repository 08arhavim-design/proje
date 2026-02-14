<?php
// Plesk / PHP 8.1.x - MariaDB 10.3.x uyumlu yapılandırma
return [
  'db' => [
    'host' => 'localhost',
    'name' => 'sporcu',
    'user' => 'sporcu',
    'pass' => 'Osman1983.',
    'charset' => 'utf8mb4',
  ],
  'app' => [
    'base_path' => '/boga', // domain.com/boga
    'upload_dir' => __DIR__ . '/../uploads/bulls',
    'upload_url' => '/boga/uploads/bulls',
    'otp_digits' => 4,
    'otp_ttl_seconds' => 180,     // 3 dakika
    'otp_max_attempts' => 5,      // 5 deneme
    'rate_limit_window_seconds' => 180, // 3 dakika
    'rate_limit_max' => 5,
    'max_upload_bytes' => 15 * 1024 * 1024, // 15 MB
    'allowed_ext' => ['jpg','jpeg','png'],
  ],
  'sms' => [
    // Verimor v2
    'endpoint' => 'https://sms.verimor.com.tr/v2/send.json',
    'username' => '908502426253',
    'password' => 'KYV/224uns',
    'sender'   => '08502426253',
  ],
];
