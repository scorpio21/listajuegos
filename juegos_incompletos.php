<?php
require_once __DIR__.'/utilidad/utilidades.php';

// NUEVO: Contar juegos sin título (nombre) para ambas plataformas
if (isset($_GET['contar_vacios']) && $_GET['contar_vacios'] == '1') {
    try {
        $conn = get_db_connection();
        $sql = "SELECT COUNT(*) as total FROM juegos WHERE titulo IS NULL OR titulo = ''";
        $stmt = $conn->query($sql);
        $row = $stmt->fetch();
        echo json_encode(['success' => true, 'total_vacios' => intval($row['total'])]);
        exit;
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        exit;
    }
}

// NUEVO: Contar juegos vacíos por campo y plataforma
if (isset($_GET['estadisticas_vacios']) && $_GET['estadisticas_vacios'] == '1') {
    $campos = ['titulo','region','genero','publisher','fecha'];
    $plataformas = ['psx','ps2'];
    $result = [];
    try {
        $conn = get_db_connection();
        foreach ($plataformas as $plat) {
            $resPlat = [];
            foreach ($campos as $campo) {
                $sql = "SELECT COUNT(*) as total FROM juegos WHERE LOWER(plataforma) = ? AND (".$campo." IS NULL OR ".$campo." = '')";
                $stmt = $conn->prepare($sql);
                $stmt->execute([$plat]);
                $row = $stmt->fetch();
                $resPlat[$campo] = intval($row['total']);
            }
            $result[$plat] = $resPlat;
        }
        echo json_encode(['success' => true, 'estadisticas' => $result]);
        exit;
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        exit;
    }
}

$plataforma = isset($_GET['plataforma']) ? strtolower(trim($_GET['plataforma'])) : '';
if (!$plataforma) {
    echo json_encode(['success' => false, 'error' => 'Falta el parámetro plataforma.']);
    exit;
}
if ($plataforma !== 'psx' && $plataforma !== 'ps2') {
    echo json_encode(['success' => true, 'seriales' => []]);
    exit;
}

try {
    $conn = get_db_connection();
    $sql = "SELECT serial FROM juegos WHERE LOWER(plataforma) = ? AND (titulo IS NULL OR titulo = '' OR region IS NULL OR region = '' OR genero IS NULL OR genero = '' OR publisher IS NULL OR publisher = '' OR fecha IS NULL OR fecha = '') ORDER BY serial";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$plataforma]);
    $seriales = $stmt->fetchAll(PDO::FETCH_COLUMN);
    echo json_encode(['success' => true, 'seriales' => $seriales]);
    exit;
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    exit;
}
// No debe haber nada después de este bloque, ni espacios ni saltos de línea.
