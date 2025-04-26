<?php
require_once __DIR__.'/utilidad/utilidades.php';
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
// Descargar la imagen
$img_data = @file_get_contents($url);
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
require_once 'buscar_datos.php'; // Para la función get_db_connection
try {
    $pdo = get_db_connection();
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
