<?php
require_once __DIR__.'/utilidad/utilidades.php';
header('Content-Type: application/json');
$data = json_decode(file_get_contents('php://input'), true);
if (!$data || !isset($data['serial'])) {
    echo json_encode(['success' => false, 'error' => 'Datos incompletos.']);
    exit;
}
$serial = strtoupper(trim($data['serial']));
$campos = ['titulo','region','genero','publisher','fecha','plataforma'];
$set = [];
$params = [];
foreach ($campos as $campo) {
    if (isset($data[$campo])) {
        // Guardar en mayúsculas, pero mantener acentos y caracteres especiales
        $valor = mb_strtoupper(trim($data[$campo]), 'UTF-8');
        $set[] = "$campo = ?";
        $params[] = $valor;
    }
}
if (empty($set)) {
    echo json_encode(['success' => false, 'error' => 'No hay campos para actualizar.']);
    exit;
}
$params[] = $serial;
try {
    $conn = get_db_connection();
    $sql = "UPDATE juegos SET ".implode(", ", $set)." WHERE serial = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute($params);
    echo json_encode(['success' => true]);
    exit;
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    exit;
}
