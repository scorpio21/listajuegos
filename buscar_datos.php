<?php
// --- ACTIVAR ERRORES PARA DEPURAR Y CAPTURARLOS EN LOG ---
error_reporting(E_ALL);
ini_set('display_errors', 0);

require_once __DIR__.'/utilidad/utilidades.php';

// --- CONEXIÓN BASE DE DATOS ---
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
