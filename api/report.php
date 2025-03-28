<?php

require __DIR__ . '/../vendor/autoload.php';

use setasign\Fpdi\Fpdi;

header("Content-Type: application/json");

include_once('../funciones.php');

$PSN = new DBbase_Sql;
$PSN1 = new DBbase_Sql;
$PSN2 = new DBbase_Sql;
$PSN3 = new DBbase_Sql;
$PSN4 = new DBbase_Sql;
$PSN5 = new DBbase_Sql;


// Procesar entrada
$input = json_decode(file_get_contents("php://input"), true);

$sqlFiltros = "";

// Validar datos
if (!isset($input["fecha_inicio"]) || !isset($input["fecha_fin"])) {
  echo json_encode(["error" => "Fechas no enviadas"]);
  exit;
}

$fechaInicio = $input["fecha_inicio"];
$fechaFin = $input["fecha_fin"];
$generarPdf = $input["pdf"];

// Validar los nuevos campos
if (!isset($input["convocatorias"]) || !isset($input["iglesias"]) || 
    !isset($input["lideres"]) || !isset($input["voluntarios"]) || 
    !isset($input["pospenados"])) {
  echo json_encode(["error" => "Faltan datos del reporte"]);
  exit;
}

$convocatorias = $input["convocatorias"];
$iglesias = $input["iglesias"];
$lideres = $input["lideres"];
$voluntarios_nacionales = $input["voluntarios"];
$pospenados = $input["pospenados"];



$busquedaFechaIni = eliminarInvalidos($fechaInicio);
$busquedaFechaFin = eliminarInvalidos($fechaFin);

$sqlFiltro .= " AND sat_reportes.fechaReporte >= '" . $busquedaFechaIni . "'";
$sqlFiltro .= " AND sat_reportes.fechaReporte <= '" . $busquedaFechaFin . "'";

$sql = "SELECT count(sat_reportes.id) AS total_prisiones FROM sat_reportes";
$sql .= " LEFT JOIN usuario AS U ON U.id = sat_reportes.idUsuario 
LEFT JOIN tbl_regional_ubicacion AS RU ON RU.reub_id = sat_reportes.sitioReunion 
LEFT JOIN categorias AS C ON C.id = RU.reub_reg_fk 
LEFT JOIN usuario_empresa AS UE ON UE.idUsuario = sat_reportes.idUsuario 
LEFT JOIN categorias AS CA ON CA.id = C.idSec ";
$sql .= " WHERE 1 AND sat_reportes.rep_tip = 307 " . $sqlFiltro . " GROUP BY sat_reportes.sitioReunion";
//
// echo $sql;
$PSN1->query($sql);
//echo $sql;
$total_prisiones = $PSN1->num_rows();


$sql = "SELECT SUM(sat_reportes.asistencia_total) AS total_poblacion,SUM(sat_reportes.asistencia_hom) AS prns_invitados, SUM(sat_reportes.asistencia_muj) AS prns_iniciaron, SUM(sat_reportes.asistencia_jov) AS cursos_act, SUM(sat_reportes.asistencia_nin) AS prns_graduados, SUM(sat_reportes.bautizados) AS internos, SUM(sat_reportes.desiciones) AS externos, SUM(sat_reportes.bautizados + sat_reportes.desiciones) AS voluntarios, SUM(sat_reportes.rep_ndis) AS discipulos FROM sat_reportes ";
$sql .= " LEFT JOIN usuario AS U ON U.id = sat_reportes.idUsuario 
LEFT JOIN tbl_regional_ubicacion AS RU ON RU.reub_id = sat_reportes.sitioReunion 
LEFT JOIN categorias AS C ON C.id = RU.reub_reg_fk 
LEFT JOIN usuario_empresa AS UE ON UE.idUsuario = sat_reportes.idUsuario 
LEFT JOIN categorias AS CA ON CA.id = C.idSec ";
$sql .= " WHERE 1 AND sat_reportes.rep_tip = 307 " . $sqlFiltro . "";
//
$PSN->query($sql);
$num = $PSN->num_rows();
if ($num > 0) {
  while ($PSN->next_record()) {
    //$total_poblacion = intval($PSN->f('total_poblacion'));
    $prns_invitados = intval($PSN->f('prns_invitados'));
    $prns_iniciaron = intval($PSN->f('prns_iniciaron'));
    $cursos_act = intval($PSN->f('cursos_act'));
    $prns_graduados = intval($PSN->f('prns_graduados'));
    $invt_internos = intval($PSN->f('internos'));
    $invt_externos = intval($PSN->f('externos'));
    $voluntarios = intval($PSN->f('voluntarios'));
    $discipulos = intval($PSN->f('discipulos'));
  }
} else {
  $varError = 1;
}

$sql = "SELECT 
SUM(asistencia_total) as asistencia_total,
SUM(discipulado) as discipulado,
SUM(graduadosPeriodo) as graduados,
SUM(desiciones) as decisiones,
SUM(bautizadosPeriodo) as bautizos,
SUM(number_person_without_freedom) as familias_privadas,
SUM(number_person_post_penalties) as familias_pospenados ,
COUNT(sat_reportes.id) as total_grupos";
$sql.=" FROM sat_reportes ";
    $sql .= " LEFT JOIN usuario AS U ON U.id = sat_reportes.idUsuario 
LEFT JOIN tbl_regional_ubicacion AS RU ON RU.reub_id = sat_reportes.sitioReunion 
LEFT JOIN categorias AS C ON C.id = RU.reub_reg_fk 
LEFT JOIN usuario_empresa AS UE ON UE.idUsuario = sat_reportes.idUsuario 
LEFT JOIN categorias AS CA ON CA.id = C.idSec ";
$sql.=" WHERE ".$sqlUser." sat_reportes.rep_tip = 308".$sqlFiltro."";
//echo $sql." ".$sqlFiltro;
$PSN->query($sql);
$num=$PSN->num_rows();
if($num > 0){
	while($PSN->next_record()){
        $satura_asistencia_total = $PSN->f('asistencia_total');
        $satura_discipulado = $PSN->f('discipulado');
        $satura_graduados = $PSN->f('graduados');
        $satura_decisiones = $PSN->f('decisiones');
        $satura_bautizos = $PSN->f('bautizos');
        $satura_total_grupos = $PSN->f('total_grupos');
        $familias_privadas = $PSN->f('familias_privadas');
        $familias_pospenados = $PSN->f('familias_pospenados');
	}
}else{
    $varError = 1;
}

$sql = "SELECT SUM(sat_reportes.asistencia_total) AS total_poblacion,SUM(sat_reportes.asistencia_hom) AS prns_invitados, SUM(sat_reportes.asistencia_muj) AS prns_iniciaron, SUM(sat_reportes.asistencia_jov) AS cursos_act, SUM(sat_reportes.asistencia_nin) AS prns_graduados, SUM(sat_reportes.bautizados) AS internos, SUM(sat_reportes.desiciones) AS externos, SUM(sat_reportes.bautizados + sat_reportes.desiciones) AS voluntarios, SUM(sat_reportes.rep_ndis) AS discipulos FROM sat_reportes";
$sql .= " LEFT JOIN usuario AS U ON U.id = sat_reportes.idUsuario 
LEFT JOIN tbl_regional_ubicacion AS RU ON RU.reub_id = sat_reportes.sitioReunion 
LEFT JOIN categorias AS C ON C.id = RU.reub_reg_fk 
LEFT JOIN usuario_empresa AS UE ON UE.idUsuario = sat_reportes.idUsuario 
LEFT JOIN categorias AS CA ON CA.id = C.idSec ";
$sql.=" WHERE 1 AND sat_reportes.rep_tip = 319 ".$sqlFiltro."";
//

$PSN->query($sql);
//echo $sql;
$num=$PSN->num_rows();
if($num > 0){
	while($PSN->next_record()){
        $pf_total_poblacion = intval($PSN->f('total_poblacion'));
        $pf_prns_invitados = intval($PSN->f('prns_invitados'));
        $pf_prns_iniciaron = intval($PSN->f('prns_iniciaron'));
        $pf_cursos_act = intval($PSN->f('cursos_act'));
        $pf_prns_graduados = intval($PSN->f('prns_graduados'));
        $pf_invt_internos = intval($PSN->f('internos'));
        $pf_invt_externos = intval($PSN->f('externos'));
        $pf_voluntarios = intval($PSN->f('voluntarios'));
        $pf_discipulos = intval($PSN->f('discipulos'));
	}
}else{
    $varError = 1;
}

$sql = "SELECT SUM(sat_reportes.asistencia_total) AS total_poblacion,SUM(sat_reportes.asistencia_hom) AS prns_invitados, SUM(sat_reportes.asistencia_muj) AS prns_iniciaron, SUM(sat_reportes.asistencia_jov) AS cursos_act, SUM(sat_reportes.asistencia_nin) AS prns_graduados, SUM(sat_reportes.bautizados) AS internos, SUM(sat_reportes.desiciones) AS externos, SUM(sat_reportes.bautizados + sat_reportes.desiciones) AS voluntarios, SUM(sat_reportes.rep_ndis) AS discipulos FROM sat_reportes";
$sql .= " LEFT JOIN usuario AS U ON U.id = sat_reportes.idUsuario 
LEFT JOIN tbl_regional_ubicacion AS RU ON RU.reub_id = sat_reportes.sitioReunion 
LEFT JOIN categorias AS C ON C.id = RU.reub_reg_fk 
LEFT JOIN usuario_empresa AS UE ON UE.idUsuario = sat_reportes.idUsuario 
LEFT JOIN categorias AS CA ON CA.id = C.idSec ";
$sql.=" WHERE 1 AND sat_reportes.rep_tip = 317 ".$sqlFiltro."";
//

$PSN->query($sql);
//echo $sql;
$num=$PSN->num_rows();
if($num > 0){
	while($PSN->next_record()){
        $ib_total_poblacion = intval($PSN->f('total_poblacion'));
        $ib_prns_invitados = intval($PSN->f('prns_invitados'));
        $ib_prns_iniciaron = intval($PSN->f('prns_iniciaron'));
        $ib_cursos_act = intval($PSN->f('cursos_act'));
        $ib_prns_graduados = intval($PSN->f('prns_graduados'));
        $ib_invt_internos = intval($PSN->f('internos'));
        $ib_invt_externos = intval($PSN->f('externos'));
        $ib_voluntarios = intval($PSN->f('voluntarios'));
        $ib_discipulos = intval($PSN->f('discipulos'));
	}
}else{
    $varError = 1;
}

$pdf = new Fpdi('P', 'mm', 'A4');
$pdf->SetAutoPageBreak(true, 20);
$pdf->SetMargins(15, 30, 15);

// Función conversión colores (mantener igual)
function hex2rgb($hex)
{
  $hex = str_replace("#", "", $hex);
  if (strlen($hex) == 3) {
    $r = hexdec(substr($hex, 0, 1) . substr($hex, 0, 1));
    $g = hexdec(substr($hex, 1, 1) . substr($hex, 1, 1));
    $b = hexdec(substr($hex, 2, 1) . substr($hex, 2, 1));
  } else {
    $r = hexdec(substr($hex, 0, 2));
    $g = hexdec(substr($hex, 2, 2));
    $b = hexdec(substr($hex, 4, 2));
  }
  return [$r, $g, $b];
}

// Crear página
$pdf->AddPage();

// Fondo azul (mantener igual)
$colorFondo = hex2rgb('#fff');
$pdf->SetFillColor(...$colorFondo);
$pdf->Rect(0, 0, $pdf->GetPageWidth(), $pdf->GetPageHeight(), 'F');

$pdf->Image('../images/logo.png', 15, 10, 40); // X=15, Y=10, Ancho=40mm

// Título principal (mantener igual)
$pdf->SetFont('Arial', 'B', 18);
// $pdf->SetTextColor(255, 255, 255);

$colorTitulo = hex2rgb('#ff0624');
$pdf->SetTextColor($colorTitulo[0], $colorTitulo[1], $colorTitulo[2]);
$titulo = "Reporte desde $fechaInicio hasta $fechaFin";
$anchoTitulo = $pdf->GetStringWidth($titulo);
$pdf->SetXY((210 - $anchoTitulo) / 2, 20);
$pdf->Cell($anchoTitulo, 30, $titulo, 0, 1, 'C');

// Contenedor Datos Generales (ajustar altura)
$pdf->SetDrawColor(255, 255, 255);
$pdf->SetFillColor(255, 255, 255);
$pdf->Rect(20, 40, 170, 150, 'FD'); // Altura aumentada a 100mm

// Encabezado Sección
$pdf->SetFont('Arial', 'B', 16);
$pdf->SetTextColor($colorTitulo[0], $colorTitulo[1], $colorTitulo[2]);
$pdf->SetXY(30, 45);

$titulo = iconv('UTF-8', 'windows-1252', "Estadísticas del Programa");
$pdf->Cell(0, 10, $titulo, 0, 1);

// Línea divisoria
$pdf->SetDrawColor($colorTitulo[0], $colorTitulo[1], $colorTitulo[2]);
$pdf->Line(30, 55, 180, 55);

// Nuevos datos en dos columnas

$datos = [
  iconv('UTF-8', 'windows-1252', "Cárceles alcanzadas") => $total_prisiones,
  iconv('UTF-8', 'windows-1252', "Internos Convocados") => $prns_invitados,
  iconv('UTF-8', 'windows-1252', "Convocatorias realizadas") => $convocatorias,
  iconv('UTF-8', 'windows-1252', "Cursos de La Peregrinación realizados") => $cursos_act,
  iconv('UTF-8', 'windows-1252', "PPL evangelizadas") => $prns_graduados + $satura_graduados,
  iconv('UTF-8', 'windows-1252', "PPL discipuladas") => $discipulos + $satura_discipulado + $pf_discipulos + $ib_discipulos,
  iconv('UTF-8', 'windows-1252', "Iglesias en los patios") => $iglesias,
  iconv('UTF-8', 'windows-1252', "PPL bautizadas") => $satura_bautizos,
  iconv('UTF-8', 'windows-1252', "Líderes internos") => $lideres,
  iconv('UTF-8', 'windows-1252', "Voluntarios a nivel nacional en evangelismo y discipulado") => $voluntarios_nacionales,
  iconv('UTF-8', 'windows-1252', "Pospenados atendidos") => $pospenados,
];
// Dividir datos en dos columnas
$columna1 = array_slice($datos, 0, 6);
$columna2 = array_slice($datos, 6);

// Columna Izquierda
$yPos = 65;
foreach ($columna1 as $titulo => $valor) {
  $pdf->SetFont('Arial', 'B', 10);
  $pdf->SetTextColor(50, 50, 50);
  $pdf->SetXY(35, $yPos);
  $pdf->Cell(70, 5, $titulo, 0, 1);

  $pdf->SetFont('Arial', '', 12);
  $pdf->SetTextColor($colorTitulo[0], $colorTitulo[1], $colorTitulo[2]);
  $pdf->SetXY(35, $yPos + 6);
  $pdf->Cell(70, 5, $valor, 0, 1);

  $yPos += 15;
}

// Columna Derecha
$yPos = 65;
foreach ($columna2 as $titulo => $valor) {
  $pdf->SetFont('Arial', 'B', 10);
  $pdf->SetTextColor(50, 50, 50);
  $pdf->SetXY(115, $yPos);
  $pdf->Cell(70, 5, $titulo, 0, 1);

  $pdf->SetFont('Arial', '', 12);
  $pdf->SetTextColor($colorTitulo[0], $colorTitulo[1], $colorTitulo[2]);
  $pdf->SetXY(115, $yPos + 6);
  $pdf->Cell(70, 5, $valor, 0, 1);

  $yPos += 15;
}

// Sección Gráfica (ajustar posición)
$pdf->SetFillColor(255, 255, 255);
// $pdf->Rect(20, 180, 170, 80, 'FD'); // Posición más baja
$pdf->SetFont('Arial', 'B', 16);
$pdf->SetTextColor($colorTitulo[0], $colorTitulo[1], $colorTitulo[2]);
$pdf->SetXY(30, 185);
// $pdf->Cell(0, 10, 'Distribución de Alcances', 0, 1);

// Pie de página (ajustar posición)
$pdf->SetFont('Arial', 'I', 10);
$pdf->SetTextColor($colorTitulo[0], $colorTitulo[1], $colorTitulo[2]);
$pdf->SetY(265); // Posición más baja
$pdf->Cell(0, 10, 'Reporte generado el: ' . date('d/m/Y H:i:s'), 0, 0, 'R');

// Generar salida
$pdfContent = $pdf->Output('S');

if ($generarPdf) {
  header("Content-Type: application/pdf");
  header("Content-Disposition: inline; filename=reporte_{$fechaInicio}_{$fechaFin}.pdf");
  echo $pdfContent;
  exit;
}

// Guardar en servidor
$reportDir = __DIR__ . '/../reportes';
if (!is_dir($reportDir)) mkdir($reportDir, 0775, true);

$nombreArchivo = "reporte_{$fechaInicio}_{$fechaFin}.pdf";
$rutaCompleta = "$reportDir/$nombreArchivo";

file_put_contents($rutaCompleta, $pdfContent);

echo json_encode([
  'mensaje' => 'Reporte generado con éxito',
  'archivo' => $nombreArchivo,
  'ruta' => $rutaCompleta
]);
