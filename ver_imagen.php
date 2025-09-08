<?php
require_once __DIR__.'/utilidad/utilidades.php';

$serial = isset($_GET['serial']) ? trim($_GET['serial']) : '';
$tipo = isset($_GET['tipo']) ? trim($_GET['tipo']) : 'caratula';

if ($serial === '') {
    http_response_code(400);
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'error' => 'Falta el parámetro serial']);
    exit;
}

try {
    $pdo = get_db_connection();
    $stmt = $pdo->prepare('SELECT imagen_blob, mime_type FROM imagenes WHERE juego_serial = ? AND tipo = ? LIMIT 1');
    $stmt->execute([$serial, $tipo]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$row) {
        http_response_code(404);
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'error' => 'Imagen no encontrada']);
        exit;
    }
    $mime = $row['mime_type'] ?: 'image/jpeg';
    header('Content-Type: ' . $mime);
    header('Cache-Control: public, max-age=86400');
    echo $row['imagen_blob'];
} catch (Exception $e) {
    http_response_code(500);
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
