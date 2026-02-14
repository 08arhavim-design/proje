<?php
declare(strict_types=1);
require_once __DIR__ . '/../app/functions.php';
require_login();
require_admin();
$pdo = pdo();
if (session_status() !== PHP_SESSION_ACTIVE) session_start();
if (!hash_equals((string)($_SESSION['__csrf'] ?? ''),(string)($_GET['csrf'] ?? ''))) exit('CSRF hatası');
$id = (int)($_GET['id'] ?? 0);
if ($id>0) { $pdo->prepare("UPDATE bulls SET status='approved' WHERE id=:id")->execute([':id'=>$id]); }
header('Location: /boga/admin/pending.php'); exit;