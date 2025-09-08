<?php
// --- ACTIVAR ERRORES PARA DEPURAR Y CAPTURARLOS EN LOG ---
error_reporting(E_ALL);
ini_set('display_errors', 0);

require_once __DIR__.'/utilidad/utilidades.php';

// Nota: get_db_connection() se define en utilidad/utilidades.php

// --- MANEJO DE ERRORES ---
set_error_handler(function ($errno, $errstr, $errfile, $errline) {
    $msg = "[PHP ERROR] [$errno] $errstr en $errfile:$errline";
    log_custom('php_errors.log', $msg);
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $msg]);
    exit;
});

set_exception_handler(function ($exception) {
    $msg = "[PHP EXCEPTION] " . $exception->getMessage() . " en " . $exception->getFile() . ":" . $exception->getLine();
    log_custom('php_errors.log', $msg . "\n" . $exception->getTraceAsString());
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $msg]);
    exit;
});

register_shutdown_function(function () {
    $error = error_get_last();
    if ($error && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
        $msg = "[PHP SHUTDOWN] [{$error['type']}] {$error['message']} en {$error['file']}:{$error['line']}";
        log_custom('php_errors.log', $msg);
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => $msg]);
        exit;
    }
});

// --- BUSQUEDA EN BD POR TITULO ---
/**
 * Busca un juego por título y plataforma.
 * - Si hay coincidencia exacta (normalizando espacios y mayúsculas), devuelve un objeto del juego y success=true.
 * - Si no hay exacta pero hay coincidencias parciales, devuelve success="parcial" y una lista de hasta 10 matches.
 * - Si no hay resultados, success=false con error.
 * @param string $titulo
 * @param string $plataforma 'psx'|'ps2'
 * @return array
 */
function buscarEnBDPorTitulo(string $titulo, string $plataforma): array {
    $plataforma = strtolower(trim($plataforma));
    if ($plataforma !== 'ps2') { $plataforma = 'psx'; }

    $tituloOriginal = trim($titulo);
    $tituloNormal = limpiar_titulo_bd($tituloOriginal);

    $conn = get_db_connection();

    // Intento 1: coincidencia exacta por título normalizado y plataforma
    // Usamos TRIM y collation insensible a mayúsculas (LOWER por seguridad)
    $sqlExacta = "SELECT serial, titulo, region, genero, publisher, fecha, plataforma
                  FROM juegos
                  WHERE LOWER(TRIM(titulo)) = LOWER(TRIM(:t)) AND LOWER(plataforma) = :p
                  LIMIT 1";
    $stmt = $conn->prepare($sqlExacta);
    $stmt->execute([':t' => $tituloNormal, ':p' => $plataforma]);
    $row = $stmt->fetch();
    if ($row) {
        // Añadir URL de carátula servida por el backend
        $row['caratula_url'] = 'ver_imagen.php?serial=' . urlencode($row['serial']) . '&tipo=caratula';
        return ['success' => true] + $row;
    }

    // Intento 2: coincidencia flexible (LIKE) por plataforma
    // Buscamos por el título original y también por el normalizado, priorizando coincidencias más cortas
    $like1 = '%' . $tituloOriginal . '%';
    $like2 = '%' . $tituloNormal . '%';
    $sqlParcial = "SELECT serial, titulo, region, genero, publisher, fecha, plataforma
                   FROM juegos
                   WHERE LOWER(plataforma) = :p AND (titulo LIKE :l1 OR titulo LIKE :l2)
                   ORDER BY CHAR_LENGTH(titulo) ASC
                   LIMIT 10";
    $stmt = $conn->prepare($sqlParcial);
    $stmt->execute([':p' => $plataforma, ':l1' => $like1, ':l2' => $like2]);
    $matches = $stmt->fetchAll();
    if ($matches && count($matches) > 0) {
        foreach ($matches as &$m) {
            if (isset($m['serial'])) {
                $m['caratula_url'] = 'ver_imagen.php?serial=' . urlencode($m['serial']) . '&tipo=caratula';
            }
        }
        return ['success' => 'parcial', 'matches' => $matches];
    }

    // Intento 3: búsqueda más laxa por palabras separadas
    $palabras = array_filter(preg_split('/\s+/', $tituloNormal));
    if (!empty($palabras)) {
        $where = [];
        $params = [':p' => $plataforma];
        foreach ($palabras as $i => $w) {
            $key = ':w' . $i;
            $where[] = "titulo LIKE $key";
            $params[$key] = '%' . $w . '%';
        }
        $sqlFlex = "SELECT serial, titulo, region, genero, publisher, fecha, plataforma
                    FROM juegos
                    WHERE LOWER(plataforma) = :p AND (" . implode(' AND ', $where) . ")
                    ORDER BY CHAR_LENGTH(titulo) ASC
                    LIMIT 10";
        $stmt = $conn->prepare($sqlFlex);
        $stmt->execute($params);
        $flexMatches = $stmt->fetchAll();
        if ($flexMatches && count($flexMatches) > 0) {
            foreach ($flexMatches as &$m) {
                if (isset($m['serial'])) {
                    $m['caratula_url'] = 'ver_imagen.php?serial=' . urlencode($m['serial']) . '&tipo=caratula';
                }
            }
            return ['success' => 'flexible', 'matches' => $flexMatches];
        }
    }

    return ['success' => false, 'error' => 'No se encontraron coincidencias.'];
}

// --- ENDPOINT DIRECTO (GET) ---
// Permite llamar a este archivo directamente como API: buscar_datos.php?titulo=...&plataforma=psx|ps2
if (php_sapi_name() !== 'cli') {
    if (isset($_GET['titulo'])) {
        header('Content-Type: application/json; charset=utf-8');
        $titulo = (string)($_GET['titulo'] ?? '');
        $plataforma = (string)($_GET['plataforma'] ?? 'psx');
        $resp = buscarEnBDPorTitulo($titulo, $plataforma);
        echo json_encode($resp);
        exit;
    }
}
