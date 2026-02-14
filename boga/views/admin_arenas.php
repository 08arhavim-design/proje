<h2>Meydanlar</h2>
<form method="post">
    <input name="name" placeholder="Meydan adı" required>
    <input name="city" placeholder="Şehir" required>
    <button>Ekle</button>
</form>
<?php
if ($_POST) {
    $stmt = $pdo->prepare("INSERT INTO arenas (name, city) VALUES (?, ?)");
    $stmt->execute([$_POST['name'], $_POST['city']]);
    header("Location: arenas.php");
    exit;
}
$res = $pdo->query("SELECT * FROM arenas ORDER BY id DESC");
foreach ($res as $row) echo "<div>{$row['name']} - {$row['city']}</div>";
