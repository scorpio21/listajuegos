<?php
require_once __DIR__.'/utilidades.php';
header('Content-Type: application/json');
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Método no permitido']);
    exit;
}
$data = json_decode(file_get_contents('php://input'), true);
if (!isset($data['serial']) || !isset($data['url'])) {
    echo json_encode(['success' => false, 'error' => 'Datos incompletos']);
    exit;
}
$serial = $data['serial'];
$url = $data['url'];
// Descargar la imagen (acepta http/https o ruta local relativa)
$img_data = false;
$isRemote = false;
$parts = @parse_url($url);
if ($parts && isset($parts['scheme']) && in_array(strtolower($parts['scheme']), ['http','https'])) {
    $isRemote = true;
    $img_data = @file_get_contents($url);
} else {
    // Resolver como ruta local relativa al raíz del proyecto (listajuegos/)
    $base = realpath(__DIR__ . '/..');
    if ($base !== false) {
        $localPath = $base . DIRECTORY_SEPARATOR . ltrim(str_replace(['../','..\\'], '', $url), '\\/');
        if (is_file($localPath) && is_readable($localPath)) {
            $img_data = @file_get_contents($localPath);
        }
    }
}
if ($img_data === false) {
    echo json_encode(['success' => false, 'error' => 'No se pudo descargar la imagen']);
    exit;
}
// Detectar MIME
$finfo = new finfo(FILEINFO_MIME_TYPE);
$mime = $finfo->buffer($img_data);
if (strpos($mime, 'image/') !== 0) {
    echo json_encode(['success' => false, 'error' => 'El archivo no es una imagen']);
    exit;
}
try {
    $pdo = get_db_connection();
    // Crear tabla si no existe
    $pdo->exec("CREATE TABLE IF NOT EXISTS imagenes (
        id INT AUTO_INCREMENT PRIMARY KEY,
        juego_serial VARCHAR(50) NOT NULL,
        tipo VARCHAR(50) NOT NULL,
        imagen_blob LONGBLOB NOT NULL,
        mime_type VARCHAR(100) NOT NULL,
        creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_juego_tipo (juego_serial, tipo)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    // Eliminar imagen anterior si existe
    $del = $pdo->prepare('DELETE FROM imagenes WHERE juego_serial = ? AND tipo = ?');
    $del->execute([$serial, 'caratula']);
    // Insertar nueva imagen
    $stmt = $pdo->prepare('INSERT INTO imagenes (juego_serial, tipo, imagen_blob, mime_type) VALUES (?, ?, ?, ?)');
    $stmt->execute([$serial, 'caratula', $img_data, $mime]);
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
