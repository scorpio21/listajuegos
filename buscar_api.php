<?php
require_once 'buscar_datos.php';
header('Content-Type: application/json');

if (isset($_GET['titulo']) && !empty($_GET['titulo'])) {
    $titulo = filter_input(INPUT_GET, 'titulo');
    $plataforma = filter_input(INPUT_GET, 'plataforma');
    $plataforma = isset($plataforma) ? strtolower(trim($plataforma)) : 'psx';
    if ($plataforma !== 'ps2') $plataforma = 'psx';
    $datosBD = buscarEnBDPorTitulo($titulo, $plataforma);
    echo json_encode($datosBD);
    exit;
}
// Si no hay parámetro título, responde con error JSON
http_response_code(400);
echo json_encode(['success' => false, 'error' => 'Falta el parámetro "titulo".']);
exit;
