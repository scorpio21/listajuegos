<?php
require_once __DIR__.'/utilidad/utilidades.php';
// Script para importar datos desde un archivo CSV a la base de datos 'listajuegos', tabla 'juegos'
// Configura estos datos según tu entorno:
$host = 'localhost';
$db   = 'listajuegos';
$user = 'root'; // Cambia por tu usuario si es diferente
$pass = '';
$charset = 'utf8mb4';

// Ruta al archivo CSV (ajusta el nombre del archivo según corresponda)
$csv_file = __DIR__ . '/utilidad/ps2.csv'; // Cambia a 'utilidad/psx.csv' para la otra plataforma

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = get_db_connection();
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}

if (!file_exists($csv_file)) {
    die("No se encontró el archivo CSV: $csv_file\n");
}

$handle = fopen($csv_file, 'r');
if ($handle === false) {
    die("No se pudo abrir el archivo CSV\n");
}

$header = fgetcsv($handle);
if (!$header) {
    die("El archivo CSV está vacío o no tiene cabecera\n");
}

// Prepara la consulta de inserción
$sql = "INSERT INTO juegos (serial, titulo, region, genero, publisher, fecha, url_ficha, plataforma) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
$stmt = $pdo->prepare($sql);

$contador = 0;
while (($data = fgetcsv($handle)) !== false) {
    // Si hay más columnas de las esperadas, recorta el array
    $data = array_slice($data, 0, 8);
    // Si hay menos, rellena con null
    while (count($data) < 8) {
        $data[] = null;
    }
    // Limpiar el título antes de importar
    $data[1] = limpiar_titulo_bd($data[1]);
    $stmt->execute($data);
    $contador++;
}
fclose($handle);
echo "Importación completada. Registros insertados: $contador\n";
?>
