<?php
// Consulta títulos similares a los buscados
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

$busquedas = [
    ['titulo' => 'AGE OF EMPIRES', 'plataforma' => 'ps2'],
    ['titulo' => 'DIE HARD TRILOGY', 'plataforma' => 'psx']
];

foreach ($busquedas as $b) {
    $sql = "SELECT serial, titulo, region, plataforma FROM juegos WHERE plataforma = ? AND titulo LIKE ? ORDER BY titulo";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$b['plataforma'], '%' . $b['titulo'] . '%']);
    $res = $stmt->fetchAll();
    echo "<h3>Resultados para <b>".htmlspecialchars($b['titulo'])."</b> en <b>".strtoupper($b['plataforma'])."</b>:</h3>";
    if (!$res) {
        echo '<i>No se encontraron coincidencias.</i>';
    } else {
        echo '<ul>';
        foreach ($res as $row) {
            echo '<li><b>'.htmlspecialchars($row['titulo']).'</b> ['.htmlspecialchars($row['serial']).'] - '.htmlspecialchars($row['region']).' ('.strtoupper($row['plataforma']).')</li>';
        }
        echo '</ul>';
    }
}
?>
