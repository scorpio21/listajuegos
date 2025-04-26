<?php
// Funciones de utilidad generales para el proyecto listajuegos
// Aquí puedes añadir funciones como conexión a BD, limpieza de strings, logging, etc.

function get_db_connection() {
    $host = 'localhost';
    $db   = 'listajuegos';
    $user = 'root'; // Cambia si tu usuario es diferente
    $pass = '';
    $charset = 'utf8mb4';
    $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];
    try {
        return new PDO($dsn, $user, $pass, $options);
    } catch (PDOException $e) {
        log_custom('php_errors.log', "[DB ERROR] " . $e->getMessage());
        echo json_encode(['success' => false, 'error' => 'Error de conexión a la base de datos.']);
        exit;
    }
}

function limpiar($str) {
    if (!is_string($str)) return '';
    $out = @iconv('UTF-8', 'ASCII//TRANSLIT', $str);
    $out = $out === false ? strtolower(trim($str)) : strtolower(trim($out));
    $out = str_replace(['á','é','í','ó','ú','ñ','ü'], ['a','e','i','o','u','n','u'], $out);
    $out = preg_replace('/[^a-z0-9]/','', $out);
    return $out;
}

function limpiar_titulo_bd($titulo) {
    // Elimina sufijos como "- (PAL)", "- (NTSC)" y variantes
    return trim(preg_replace('/\s*-\s*\((PAL|NTSC|NTSC-J|NTSC-U)\)$/i', '', $titulo));
}

function log_custom($filename, $content, $append = true) {
    $dir = __DIR__ . '/../Log';
    if (!file_exists($dir)) mkdir($dir, 0777, true);
    $file = $dir . '/' . $filename;
    file_put_contents($file, $content . "\n", $append ? FILE_APPEND : 0);
}
