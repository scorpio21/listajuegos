<?php
require_once 'buscar_datos.php';
header('Content-Type: application/json');
$serial = isset($_GET['serial']) ? strtoupper(trim($_GET['serial'])) : '';
if (!$serial) {
    echo json_encode(['success' => false, 'error' => 'Serial no especificado.']);
    exit;
}
try {
    $conn = get_db_connection();
    $sql = "SELECT * FROM juegos WHERE serial = ? LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$serial]);
    $juego = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($juego) {
        echo json_encode(['success' => true, 'juego' => $juego]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Juego no encontrado.']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
