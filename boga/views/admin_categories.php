<h2>Kategoriler</h2>
<form method="post">
    <input name="name" placeholder="Kategori adı" required>
    <button>Ekle</button>
</form>
<?php
if ($_POST) {
    $stmt = $pdo->prepare("INSERT INTO categories (name) VALUES (?)");
    $stmt->execute([$_POST['name']]);
    header("Location: categories.php");
    exit;
}
$res = $pdo->query("SELECT * FROM categories ORDER BY id DESC");
foreach ($res as $row) echo "<div>{$row['name']}</div>";
