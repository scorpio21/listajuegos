<?php
set_time_limit(0);
$base = 'https://psxdatacenter.com/psx2/plist2.html';
$prefix = 'https://psxdatacenter.com/psx2/games2/';
$output = fopen('ps2_pal_list.csv', 'w');
// Añade BOM para que Excel reconozca UTF-8
fwrite($output, "\xEF\xBB\xBF");
fputcsv($output, ['Serial', 'Título', 'Perspectiva', 'Gráficos', 'Tema', 'Lenguaje', 'Otros seriales/regiones', 'Enlace ficha']);

// 1. Obtener todos los enlaces de ficha (SLES y SCES)
$html = file_get_contents($base);
preg_match_all('~games2/(S[CL]ES-\d+\.html)~', $html, $matches);
$links = array_unique($matches[1]);

$contador = 0;
$total = count($links);

foreach ($links as $file) {
    $contador++;
    $url = $prefix . $file;
    $ficha = @file_get_contents($url);
    if (!$ficha) continue;

    // Título (desde el <title>)
    preg_match('~<title>(.*?)</title>~i', $ficha, $mtit);
    $titulo = isset($mtit[1]) ? trim($mtit[1]) : '';

    // Perspectiva
    preg_match('~([Ff]irst|[Tt]hird) person perspective~', $ficha, $mpers);
    $perspectiva = isset($mpers[0]) ? $mpers[0] : '';

    // Gráficos
    preg_match_all('~([23]D graphics|Cartoon graphics)~', $ficha, $mgraf);
    $graficos = isset($mgraf[0]) ? implode(', ', array_unique($mgraf[0])) : '';

    // Tema
    preg_match('~([A-Za-z &\-]+ theme[s]?)~', $ficha, $mtema);
    $tema = isset($mtema[1]) ? $mtema[1] : '';

    // Lenguaje/Idioma (busca líneas tipo 'Languages: English, Spanish, ...' o 'Language: ...')
    $lenguaje = '';
    if (preg_match('~Languages?:\s*([^<\n]+)~i', $ficha, $mlang)) {
        $lenguaje = trim($mlang[1]);
    }

    // Otros seriales/regiones
    preg_match_all('~\[(S[LCS][A-Z]{2}-\d+)\]~', $ficha, $mreg);
    $otros = isset($mreg[1]) ? implode(', ', array_unique($mreg[1])) : '';

    // Serial principal
    preg_match('~S[CL]ES-\d+~', $file, $mser);
    $serial = isset($mser[0]) ? $mser[0] : '';

    // Convierte todos los datos a UTF-8 para evitar problemas de acentos y caracteres especiales
    $datos = [$serial, $titulo, $perspectiva, $graficos, $tema, $lenguaje, $otros, $url];
    $datos = array_map(function($v) { return mb_convert_encoding($v, 'UTF-8', 'auto'); }, $datos);
    fputcsv($output, $datos);

    // Mensaje de progreso opcional (puedes comentar esta línea)
    echo "Procesando ($contador/$total): $serial<br>";
    flush();
}

fclose($output);
echo "<br>¡Listo! Archivo ps2_pal_list.csv generado con $contador juegos, acentos y campo de lenguaje.";
?>
