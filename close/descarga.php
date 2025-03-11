<?php
// Establecer encabezados para indicar que es un archivo descargable
header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
header("Content-Disposition: attachment; filename='Final.xlsx'");

// Ruta al archivo .xlsx
$ruta_al_archivo_xlsx = '/home/pfcoiied/public_html/close/archivo.xlsx';

// Leer y enviar el contenido del archivo
readfile($ruta_al_archivo_xlsx);
?>