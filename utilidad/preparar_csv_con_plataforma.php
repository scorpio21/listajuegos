<?php
// Script para añadir la columna 'plataforma' a cada fila de una hoja de Excel y exportar como CSV
// Solo requiere PhpSpreadsheet para el procesamiento del Excel a CSV. No depende de ningún otro archivo del proyecto.
require 'vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Csv;

$archivo_excel = __DIR__ . '/playstation_pal_list.xlsx';
$hojas = [
    ['nombre' => 'PlayStation', 'plataforma' => 'psx', 'csv' => 'psx.csv'],
    ['nombre' => 'PlayStation 2', 'plataforma' => 'ps2', 'csv' => 'ps2.csv']
];

foreach ($hojas as $info) {
    $spreadsheet = IOFactory::load($archivo_excel);
    $hoja = $spreadsheet->getSheetByName($info['nombre']);
    if (!$hoja) {
        echo "No se encontró la hoja: {$info['nombre']}\n";
        continue;
    }
    $data = $hoja->toArray();
    if (empty($data)) {
        echo "Hoja vacía: {$info['nombre']}\n";
        continue;
    }
    // Añadir cabecera 'plataforma' si no existe
    if (strtolower(trim($data[0][count($data[0])-1])) !== 'plataforma') {
        $data[0][] = 'plataforma';
    }
    // Añadir valor de plataforma a cada fila de datos
    for ($i = 1; $i < count($data); $i++) {
        $data[$i][] = $info['plataforma'];
    }
    // Crear nuevo Spreadsheet solo con esos datos
    $nuevo = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
    $nuevo_hoja = $nuevo->getActiveSheet();
    $nuevo_hoja->fromArray($data);
    // Guardar como CSV
    $writer = new Csv($nuevo);
    $writer->setDelimiter(',');
    $writer->setEnclosure('"');
    $writer->setLineEnding("\n");
    $writer->setUseBOM(true);
    $writer->save(__DIR__ . '/' . $info['csv']);
    echo "Hoja '{$info['nombre']}' exportada como {$info['csv']} con columna plataforma.\n";
}
?>
