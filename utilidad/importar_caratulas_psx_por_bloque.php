<?php
require_once __DIR__.'/utilidad/utilidades.php';

// Script interactivo: importar carátulas por bloque (PSX/PS2), solo guarda imágenes de seriales existentes en juegos
// Mejoras: selector de plataforma, barra de progreso animada, exportación CSV, interfaz visual, iconos y aviso sonoro
set_time_limit(0);
ini_set('memory_limit', '512M');
require_once 'buscar_datos.php';

// --- Mejoras para flush en tiempo real ---
if (function_exists('apache_setenv')) @apache_setenv('no-gzip', 1);
@ini_set('zlib.output_compression', 0);
@ini_set('output_buffering', 0);
@ini_set('implicit_flush', 1);
ob_implicit_flush(1);
while (ob_get_level()) ob_end_flush();

function obtener_imagenes_en_carpeta($url_carpeta) {
    $html = @file_get_contents($url_carpeta);
    if ($html === false) return [];
    $imagenes = [];
    if (preg_match_all('/href="([A-Z0-9\-]+\.jpg)"/i', $html, $matches)) {
        $imagenes = $matches[1];
    }
    return $imagenes;
}

// --- Selección de plataforma ---
$plataformas = [
    'psx' => [
        'nombre' => 'PSX',
        'icono' => '',
        'base_url' => 'https://psxdatacenter.com/images/covers/P/',
        'tabla' => 'juegos',
    ],
    'ps2' => [
        'nombre' => 'PS2',
        'icono' => '',
        'base_url' => 'https://psxdatacenter.com/psx2/images2/covers/',
        'tabla' => 'juegos', // Cambia aquí si tu tabla de PS2 es distinta
    ],
];
$plataforma = isset($_GET['plataforma']) && isset($plataformas[$_GET['plataforma']]) ? $_GET['plataforma'] : 'psx';
$carpetas = array_merge(['0-9'], range('A','Z'));
$base_url = $plataformas[$plataforma]['base_url'];
$bloque = isset($_GET['bloque']) ? strtoupper($_GET['bloque']) : '';
$export = isset($_GET['export']) ? true : false;

// --- BLOQUE DE EXPORTACIÓN CSV ANTES DE CUALQUIER SALIDA ---
if ($export && in_array($bloque, $carpetas)) {
    $pdo = get_db_connection();
    $url_carpeta = $base_url . $bloque . '/';
    // Obtener seriales no encontrados (igual que en el procesamiento principal)
    $seriales_validos = [];
    $stmt_seriales = $pdo->query('SELECT serial FROM ' . $plataformas[$plataforma]['tabla']);
    while ($row = $stmt_seriales->fetch(PDO::FETCH_ASSOC)) {
        $seriales_validos[$row['serial']] = true;
    }
    $imagenes = obtener_imagenes_en_carpeta($url_carpeta);
    $seriales_no_encontrados = [];
    foreach ($imagenes as $img_file) {
        $serial = pathinfo($img_file, PATHINFO_FILENAME);
        if (!isset($seriales_validos[$serial])) {
            $seriales_no_encontrados[] = $serial;
        }
    }
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="seriales_no_encontrados_' . $plataforma . '_' . $bloque . '.csv"');
    foreach ($seriales_no_encontrados as $s) {
        echo "$s\n";
    }
    exit;
}

function progress_bar_js() {
    ?>
    <style>
    #progress-container { width:100%; background:#eee; border-radius:6px; margin:1em 0; overflow:hidden; height:28px; box-shadow:0 2px 8px #0002; }
    #progress-bar { width:0%; background:linear-gradient(90deg,#198754,#0d6efd,#ffc107); height:28px; line-height:28px; color:#fff; font-weight:bold; text-align:center; font-size:1.1em; transition:width 0.3s; letter-spacing:1px; box-shadow:0 1px 4px #0002; }
    #progreso-texto { font-size:1.07em; color:#444; margin-bottom:1em; font-family:monospace; }
    .icono { font-size:1.4em; vertical-align:middle; margin-right:0.3em; }
    .plataforma-btn {padding:0.5em 1.5em;font-size:1.1em;margin-right:1em;border-radius:6px;border:none;cursor:pointer;font-weight:bold;box-shadow:0 1px 4px #0002;transition:background 0.2s;}
    .plataforma-btn.selected {background:#0d6efd;color:#fff;}
    .plataforma-btn:not(.selected) {background:#eee;color:#222;}
    </style>
    <div id="progress-container"><div id="progress-bar">0%</div></div>
    <script>
    function updateProgressBar(percent, status) {
        var bar = document.getElementById("progress-bar");
        if(bar) {
            bar.style.width = percent+"%";
            bar.textContent = percent+"%";
            if (percent >= 100) {
                bar.style.background = "linear-gradient(90deg,#198754,#0d6efd,#ffc107)";
            } else if (status==="error") {
                bar.style.background = "#dc3545";
            } else {
                bar.style.background = "linear-gradient(90deg,#198754,#0d6efd,#ffc107)";
            }
        }
    }
    function playSound(tipo) {
        var audio = document.createElement("audio");
        audio.src = tipo==="ok" ? "data:audio/wav;base64,UklGRiQAAABXQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YQAAAAA=" : "data:audio/wav;base64,UklGRiQAAABXQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YYAAAAAA";
        audio.play();
    }
    function cambiarPlataforma(p) {
        var url = new URL(window.location.href);
        url.searchParams.set('plataforma', p);
        url.searchParams.delete('bloque');
        url.searchParams.delete('export');
        window.location.href = url.toString();
    }
    </script>
    <?php
}

// --- Paso especial para PS2: importar todas las carátulas de la lista de juegos sin ir letra por letra ---
if ($plataforma === 'ps2' && !$export && (empty($bloque) || !in_array($bloque, $carpetas))) {
    echo '<h2><span class="icono">Importar todas las carátulas de PS2 (sin bloques)</h2>';
    progress_bar_js();
    flush();
    $pdo = get_db_connection();
    $tabla = $plataformas[$plataforma]['tabla'];
    $base_url = $plataformas[$plataforma]['base_url'];
    // Cargar todos los seriales válidos de la tabla juegos (solo PS2)
    $seriales_validos = [];
    $stmt_seriales = $pdo->query("SELECT serial FROM $tabla WHERE serial IS NOT NULL AND serial != '' AND (plataforma = 'ps2' OR LOWER(plataforma) = 'ps2')");
    while ($row = $stmt_seriales->fetch(PDO::FETCH_ASSOC)) {
        $seriales_validos[$row['serial']] = true;
    }
    $total = count($seriales_validos);
    $ok = 0;
    $error = 0;
    $ya_existia = 0;
    $errores_detalle = [];
    $no_encontradas = [];
    $i = 0;
    foreach ($seriales_validos as $serial => $_) {
        $url_img = $base_url . $serial . '.jpg';
        $destino = __DIR__ . '/caratulas/' . $serial . '.jpg';
        // ¿Ya existe en la base de datos?
        $stmt = $pdo->prepare('SELECT id FROM imagenes WHERE juego_serial = ? AND tipo = ?');
        $stmt->execute([$serial, 'caratula']);
        if ($stmt->fetch()) {
            $ya_existia++;
            $i++;
            $percent = intval(($i)*100/$total);
            echo "<script>updateProgressBar($percent, 'ok');</script>\n";
            flush();
            continue;
        }
        // --- MANEJO DE ERRORES PARA SUPRIMIR WARNING DE file_get_contents ---
        set_error_handler(function($errno, $errstr, $errfile, $errline) {
            if (strpos($errstr, 'file_get_contents') !== false) {
                // Suprime el warning específico de file_get_contents
                return true;
            }
            return false;
        });
        $img = @file_get_contents($url_img);
        restore_error_handler();
        if ($img !== false) {
            $finfo = new finfo(FILEINFO_MIME_TYPE);
            $mime = $finfo->buffer($img);
            if (strpos($mime, 'image/') !== 0) {
                $error++;
                $errores_detalle[] = "$url_img (no es imagen)";
                $i++;
                $percent = intval(($i)*100/$total);
                echo "<script>updateProgressBar($percent, 'error');</script>\n";
                flush();
                continue;
            }
            try {
                $stmt2 = $pdo->prepare('INSERT INTO imagenes (juego_serial, tipo, imagen_blob, mime_type) VALUES (?, ?, ?, ?)');
                $stmt2->execute([$serial, 'caratula', $img, $mime]);
                $ok++;
            } catch (Exception $e) {
                $error++;
                $errores_detalle[] = "$serial (error al guardar en BD: " . $e->getMessage() . ")";
            }
        } else {
            $no_encontradas[] = $serial;
            $error++;
            $errores_detalle[] = "$serial (no descargada)";
        }
        $i++;
        $percent = intval(($i)*100/$total);
        echo "<script>updateProgressBar($percent, 'ok');</script>\n";
        flush();
    }
    echo '<div style="margin:2em 0 1em 0;font-size:1.2em;">Guardadas en BD: ' . $ok . ' / Ya existían: ' . $ya_existia . ' / Errores: ' . $error . ' de ' . $total . ' seriales de PS2.</div>';
    if ($errores_detalle) {
        echo '<details><summary style="color:#b00;cursor:pointer;">Errores y seriales no encontrados ('.count($errores_detalle).')</summary><ul>';
        foreach ($errores_detalle as $s) echo '<li>' . htmlspecialchars($s) . '</li>';
        echo '</ul></details>';
    }
    echo "<div style='margin-top:2em;'><a href='" . htmlspecialchars(basename(__FILE__)) . "?plataforma=ps2'>Volver a elegir</a></div>";
    exit;
}

// Paso 1: Selección de plataforma y bloque
if (!in_array($bloque, $carpetas)) {
    progress_bar_js(); // <-- Asegura que el JS esté presente SIEMPRE en la pantalla de selección
    echo '<h2><span class="icono">Importar carátulas por bloque</h2>';
    echo '<div style="margin-bottom:1em;">Selecciona la plataforma:</div>';
    foreach ($plataformas as $key => $plat) {
        $selected = ($plataforma === $key) ? 'selected' : '';
        echo '<button class="plataforma-btn ' . $selected . '" onclick="cambiarPlataforma(\'' . $key . '\')">' . $plat['icono'] . ' ' . $plat['nombre'] . '</button>';
    }
    echo '<div style="margin:1.5em 0 1em 0;">Selecciona una carpeta para importar todas sus carátulas:</div>';
    echo '<div style="display:flex;flex-wrap:wrap;gap:0.5em;">';
    foreach ($carpetas as $c) {
        $label = ($c === '0-9') ? 'Números (0-9)' : $c;
        $color = ($c === '0-9') ? '#0d6efd' : '#198754';
        $textColor = '#fff';
        echo '<a href="?plataforma=' . $plataforma . '&bloque=' . urlencode($c) . '" style="display:inline-block;padding:0.5em 1.2em;font-weight:bold;border-radius:5px;text-decoration:none;background:' . $color . ';color:' . $textColor . ';box-shadow:0 1px 3px #0001;">' . ($c === '0-9' ? '' : '') . ' ' . $label . '</a>';
    }
    echo '</div>';
    exit;
}

$pdo = get_db_connection();
$url_carpeta = $base_url . $bloque . '/';

echo "<h2><span class='icono'>Procesando carpeta $bloque de " . $plataformas[$plataforma]['nombre'] . "</h2>";
flush();

// Pre-carga todos los seriales válidos de la tabla correspondiente
$seriales_validos = [];
$stmt_seriales = $pdo->query('SELECT serial FROM ' . $plataformas[$plataforma]['tabla']);
while ($row = $stmt_seriales->fetch(PDO::FETCH_ASSOC)) {
    $seriales_validos[$row['serial']] = true;
}

$imagenes = obtener_imagenes_en_carpeta($url_carpeta);
$total = count($imagenes);
$ok = 0;
$error = 0;
$ya_existia = 0;
$no_en_juegos = 0;
$errores_detalle = [];
$seriales_no_encontrados = [];

progress_bar_js();
echo '<div id="progreso-texto"></div>';
flush();

foreach ($imagenes as $i => $img_file) {
    $serial = pathinfo($img_file, PATHINFO_FILENAME);
    if (!isset($seriales_validos[$serial])) {
        $no_en_juegos++;
        $errores_detalle[] = "$serial (no existe en la tabla juegos)";
        $seriales_no_encontrados[] = $serial;
        continue;
    }
    $img_url = $url_carpeta . $img_file;
    // ¿Ya existe en la base de datos?
    $stmt = $pdo->prepare('SELECT id FROM imagenes WHERE juego_serial = ? AND tipo = ?');
    $stmt->execute([$serial, 'caratula']);
    if ($stmt->fetch()) {
        $ya_existia++;
        continue;
    }
    // Comprobar si la imagen existe antes de intentar descargarla
    $headers = @get_headers($img_url);
    if ($headers && strpos($headers[0], '200') !== false) {
        // --- MANEJO DE ERRORES PARA SUPRIMIR WARNING DE file_get_contents ---
        set_error_handler(function($errno, $errstr, $errfile, $errline) {
            if (strpos($errstr, 'file_get_contents') !== false) {
                // Suprime el warning específico de file_get_contents
                return true;
            }
            return false;
        });
        $img = @file_get_contents($img_url);
        restore_error_handler();
        if ($img !== false) {
            $finfo = new finfo(FILEINFO_MIME_TYPE);
            $mime = $finfo->buffer($img);
            if (strpos($mime, 'image/') !== 0) {
                $error++;
                $errores_detalle[] = "$img_url (no es imagen)";
                $i++;
                $percent = intval(($i)*100/$total);
                echo "<script>updateProgressBar($percent, 'error');</script>\n";
                flush();
                continue;
            }
            try {
                $stmt2 = $pdo->prepare('INSERT INTO imagenes (juego_serial, tipo, imagen_blob, mime_type) VALUES (?, ?, ?, ?)');
                $stmt2->execute([$serial, 'caratula', $img, $mime]);
                $ok++;
            } catch (Exception $e) {
                $error++;
                $errores_detalle[] = "$serial (error al guardar en BD: " . $e->getMessage() . ")";
            }
        } else {
            $no_encontradas[] = $serial;
            $error++;
            $errores_detalle[] = "$serial (no descargada)";
        }
    } else {
        $no_encontradas[] = $serial;
        $error++;
        $errores_detalle[] = "$serial (no encontrada en el servidor)";
    }
    $i++;
    $percent = intval(($i)*100/$total);
    echo "<script>updateProgressBar($percent, 'ok');</script>\n";
    flush();
}
echo "<script>updateProgressBar(100);document.getElementById('progreso-texto').textContent='¡Completado!';playSound('ok');</script>\n";

progress_bar_js(); // muestra barra final

echo "<h3><span class='icono'>Importación completada para carpeta $bloque de " . $plataformas[$plataforma]['nombre'] . "</h3>";
echo "<ul style='font-size:1.08em;'>";
echo "<li><span class='icono'>Total de imágenes en la carpeta: $total</li>";
echo "<li><span class='icono'>Imágenes guardadas correctamente: $ok</li>";
echo "<li><span class='icono'>Imágenes ya existentes: $ya_existia</li>";
echo "<li><span class='icono'>Imágenes cuyo serial no existe en juegos: $no_en_juegos";
if ($no_en_juegos > 0) {
    $export_url = '?plataforma=' . $plataforma . '&bloque=' . urlencode($bloque) . '&export=1';
    echo ' <a href="' . $export_url . '" style="margin-left:1em;padding:0.2em 0.8em;background:#ffc107;color:#333;border-radius:4px;text-decoration:none;font-size:0.95em;">Exportar seriales a CSV</a>';
}
echo "</li>";
echo "<li><span class='icono'>Errores al descargar/guardar: $error</li>";
echo "</ul>";
if ($error > 0 || $no_en_juegos > 0) {
    echo '<details><summary style="color:#d00;cursor:pointer;">Ver detalles de errores</summary><ul style="color:#d00;">';
    foreach ($errores_detalle as $err) echo '<li>' . htmlspecialchars($err) . '</li>';
    echo '</ul></details>';
}
echo "<div style='margin-top:2em;'><a href='" . htmlspecialchars(basename(__FILE__)) . "?plataforma=" . urlencode($plataforma) . "'>Volver a elegir bloque</a></div>"; 
?>
