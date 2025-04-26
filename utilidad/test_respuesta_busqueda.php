<?php
// Script para probar la respuesta JSON del backend buscar_datos.php
function probar_busqueda($titulo, $plataforma) {
    $url = 'http://localhost:8080/buscar_datos.php';
    $params = http_build_query([
        'titulo' => $titulo,
        'plataforma' => $plataforma
    ]);
    $context = stream_context_create([
        'http' => [
            'method' => 'POST',
            'header' => 'Content-type: application/x-www-form-urlencoded',
            'content' => $params
        ]
    ]);
    $result = file_get_contents($url, false, $context);
    echo '<h3>Prueba de búsqueda: <b>'.htmlspecialchars($titulo).'</b> ('.strtoupper($plataforma).')</h3>';
    echo '<pre>';
    echo htmlspecialchars($result);
    echo '</pre>';
}

probar_busqueda('AGE OF EMPIRES II - THE AGE OF KINGS', 'ps2');
probar_busqueda('DIE HARD TRILOGY', 'psx');
?>
