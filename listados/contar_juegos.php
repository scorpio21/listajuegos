<?php
// Script para contar registros por plataforma en la tabla 'juegos'
$host = 'localhost';
$db   = 'listajuegos';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=$charset", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}

$sql = "SELECT plataforma, COUNT(*) as total FROM juegos GROUP BY plataforma ORDER BY plataforma";
$res = $pdo->query($sql);

echo "<h2>Cantidad de juegos por plataforma:</h2>";
echo "<ul>";
foreach ($res as $row) {
    echo "<li><b>" . htmlspecialchars(strtoupper($row['plataforma'])) . ":</b> " . intval($row['total']) . "</li>";
}
echo "</ul>";
?>
