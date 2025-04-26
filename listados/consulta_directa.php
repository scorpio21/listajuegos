<?php
// Script temporal para consultar juegos que contengan 'tekken', 'tag' o 'tournament' en el título
// Uso: acceder vía navegador

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

$sql = "SELECT * FROM juegos WHERE LOWER(titulo) LIKE ? OR LOWER(titulo) LIKE ? OR LOWER(titulo) LIKE ? ORDER BY titulo";
$stmt = $pdo->prepare($sql);
$stmt->execute(['%tekken%', '%tag%', '%tournament%']);
$resultados = $stmt->fetchAll();

if (!$resultados) {
    echo '<b>No se encontraron juegos con "tekken", "tag" o "tournament" en el título.</b>';
    exit;
}

echo '<h2>Resultados encontrados:</h2>';
echo '<table border="1" cellpadding="6" style="border-collapse:collapse;">';
echo '<tr><th>ID</th><th>Serial</th><th>Título</th><th>Región</th><th>Género</th><th>Publisher</th><th>Fecha</th><th>Plataforma</th></tr>';
foreach ($resultados as $fila) {
    echo '<tr>';
    echo '<td>' . htmlspecialchars($fila['id']) . '</td>';
    echo '<td>' . htmlspecialchars($fila['serial']) . '</td>';
    echo '<td>' . htmlspecialchars($fila['titulo']) . '</td>';
    echo '<td>' . htmlspecialchars($fila['region']) . '</td>';
    echo '<td>' . htmlspecialchars($fila['genero']) . '</td>';
    echo '<td>' . htmlspecialchars($fila['publisher']) . '</td>';
    echo '<td>' . htmlspecialchars($fila['fecha']) . '</td>';
    echo '<td>' . htmlspecialchars($fila['plataforma']) . '</td>';
    echo '</tr>';
}
echo '</table>';
?>
