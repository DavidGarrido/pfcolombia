<?php 
session_start();
//session_register("SESSION");
include_once('funciones.php');

require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
///use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Style\Border;


if(!isset($_REQUEST["fechaInicial"]) || eliminarInvalidos($_REQUEST["fechaInicial"]) == ""){
    $_REQUEST["fechaInicial"] = date("2021-02-01");
}
if(!isset($_REQUEST["fechaFinal"]) || eliminarInvalidos($_REQUEST["fechaFinal"]) == ""){
    $siguiente_anho = date("Y", strtotime("+1 year"));
    //$_REQUEST["fechaFinal"] = $siguiente_anho."-01-31";
    $_REQUEST["fechaFinal"] = date("Y-m-d");
}
    
if (!empty($_REQUEST['rep_ani'])) {
    $anio = $_REQUEST['rep_ani'];
}else{
    $anio = date('Y');
}
if (!empty($_REQUEST['rep_qua'])) {
    $q = $_REQUEST['rep_qua'];
    $iniQ = $anio.'-'.$q.'-01';
    $iniQ = date("Y-m-d", strtotime($iniQ));
    if ($_REQUEST['rep_qua']==1) {
        $finQ = $anio.'-'.($q+2).'-31';
    }else if ($_REQUEST['rep_qua']==10) {
        $finQ = $anio.'-'.($q+2).'-31';
    }else{
        $finQ = $anio.'-'.($q+2).'-30';
    }
    $finQ = date("Y-m-d", strtotime($finQ));
}else{
   $iniQ = $_REQUEST["fechaInicial"];
   $finQ = $_REQUEST["fechaFinal"];
}
$PSN1 = new DBbase_Sql;
$PSN2 = new DBbase_Sql;


//  YA GENERACION 0 NO CUENTA
//$sqlFiltro .= " AND sat_reportes.generacionNumero != 0";

if($_SESSION["perfil"] == 163){
    $_REQUEST["idUsuario"] = $_SESSION["id"];
}
//    
$empresa_paisid_txt = "Confraternidad Carcelaria de Colombia";
if(isset($_REQUEST["empresa_paisid"]) && soloNumeros($_REQUEST["empresa_paisid"]) != ""){
    $empresa_paisid = soloNumeros($_REQUEST["empresa_paisid"]);
    $sqlFiltro .= " AND usuario_empresa.empresa_paisid = '".$empresa_paisid."'";
    
    /*
    *   TRAEMOS LOS TIPOS DE CLIENTE/EMPRESA (15)
    */
    $sql = "SELECT * ";
    $sql.=" FROM categorias ";
    $sql.=" WHERE idSec = 37 ORDER BY descripcion asc";
    $PSN2->query($sql);
    $numero=$PSN2->num_rows();
    if($numero > 0)
    {
        while($PSN2->next_record())
        {
            $empresa_paisid_txt = "".$PSN2->f('descripcion');
        }
    }        
    
}


if(isset($_REQUEST["idUsuario"]) && soloNumeros($_REQUEST["idUsuario"]) != ""){
        $buscar_idUsuario = soloNumeros($_REQUEST["idUsuario"]);
        $sqlFiltro .= " AND sat_reportes.idUsuario = '".$buscar_idUsuario."'";
    }
    if ($_SESSION["id_zona"]!="" && $_SESSION["id_zona"]!=0) {
        $sqlFiltro .= " AND C.idSec = '".$_SESSION["id_zona"]."'";
        $_REQUEST["empresa_sitio_cor"] = $_SESSION["id_zona"];
        $buscar_zona = $_SESSION["id_zona"];
    }
    if(isset($_REQUEST["empresa_sitio_cor"]) && soloNumeros($_REQUEST["empresa_sitio_cor"]) != ""){
        $buscar_zona = soloNumeros($_REQUEST["empresa_sitio_cor"]);
        $sqlFiltro .= " AND C.idSec = '".$buscar_zona."'";
    }
    
    if(isset($_REQUEST["empresa_pd"]) && soloNumeros($_REQUEST["empresa_pd"]) != ""){
        $buscar_regional = soloNumeros($_REQUEST["empresa_pd"]);
        $sqlFiltro .= " AND UE.empresa_pd = '".$buscar_regional."'";
    }else if ($_SESSION["empresa_pd"]!="" && $_SESSION["empresa_pd"]!=0 && $_SESSION["empresa_sitio_cor"]=="") {
        $sqlFiltro .= " AND UE.empresa_pd = '".$_SESSION["empresa_pd"]."'";
        $_REQUEST["empresa_pd"] = $_SESSION["empresa_pd"];
    }
    if(isset($_REQUEST["sitioReunion"]) && soloNumeros($_REQUEST["sitioReunion"]) != ""){
        $buscar_prision = soloNumeros($_REQUEST["sitioReunion"]);
        $sqlFiltro .= " AND sat_reportes.sitioReunion = ".$buscar_prision."";
    }
    


if(isset($_REQUEST["fechaInicial"]) && eliminarInvalidos($_REQUEST["fechaInicial"]) != ""){
    $fechaInicial = eliminarInvalidos($_REQUEST["fechaInicial"]);
    $sqlFiltro .= " AND sat_reportes.fechaReporte >= '".$fechaInicial."'";
}

//
if(isset($_REQUEST["fechaFinal"]) && eliminarInvalidos($_REQUEST["fechaFinal"]) != ""){
    $fechaFinal = eliminarInvalidos($_REQUEST["fechaFinal"]);
    $sqlFiltro .= " AND sat_reportes.fechaReporte <= '".$fechaFinal."'";
}
                
$sql = "SELECT C.descripcion AS rgal_usuario,CA.descripcion AS zona_usuario, ";
$sql.="  M.municipio AS mnpo_prision_extra, D.departamento AS dpto_prision_extra, sat_reportes.*, "; 
$sql.="     U.nombre as nombreUsuario,
            U.direccion as direccionUsuario,
            U.identificacion as identificacionUsuario,
            UE.empresa_sitio,
            UE.empresa_socio,
            UE.empresa_rm,
            UE.empresa_proceso,
            UE.empresa_paisid ";
$sql .= " FROM sat_reportes ";
$sql .= " LEFT JOIN usuario AS U ON U.id = sat_reportes.idUsuario
    LEFT JOIN usuario_empresa AS UE ON UE.idUsuario = sat_reportes.idUsuario 
    LEFT JOIN categorias AS C ON C.id = UE.empresa_pd
    LEFT JOIN categorias AS CA ON CA.id = C.idSec
    LEFT JOIN dane_municipios AS M ON M.id_municipio = sat_reportes.ciudad
    LEFT JOIN dane_departamentos AS D ON D.id_departamento = M.departamento_id  ";
$sql .= " WHERE 1 ".$sqlFiltro."AND sat_reportes.rep_tip = 318 ORDER BY UE.empresa_paisid ASC, U.nombre ASC";
$reportCSV = $PSN1->query($sql);
$numero=$PSN1->num_rows();
//echo $sql;
//
//
$sql = "SELECT usuario.*, usuario_empresa.* FROM usuario LEFT JOIN usuario_empresa ON usuario_empresa.idUsuario = usuario.id WHERE usuario.id = '".$_SESSION["id"]."'";
$PSN2->query($sql);
if($PSN2->num_rows() > 0)
{
    if($PSN2->next_record())
    {
        $empresa_pais = $PSN2->f('empresa_pais');
        $empresa_sitio_cor = $PSN2->f('empresa_sitio_cor');
        $empresa_socio = $PSN2->f('empresa_socio');   
        $empresa_rm = $PSN2->f('empresa_rm');
    }
}
$ubi_regist = $numero+10;
$mergeacross = 30;
$spreadsheet = new SpreadSheet();
$spreadsheet->getProperties()->setCreator("Andres Torres")->setTitle("Informe de Satura");
$spreadsheet->setActiveSheetIndex(0);
$hojaActiva = $spreadsheet->getActiveSheet();
$spreadsheet->getActiveSheet()->getStyle('A1:AA7')->getFill()
    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
    ->getStartColor()->setARGB('FEA98D');
$spreadsheet->getActiveSheet()->getColumnDimension('A')->setWidth(12);
$spreadsheet->getActiveSheet()->getColumnDimension("B")->setAutoSize(true);
$spreadsheet->getActiveSheet()->getColumnDimension("C")->setAutoSize(true);
$spreadsheet->getActiveSheet()->getColumnDimension("D")->setAutoSize(true);
$spreadsheet->getActiveSheet()->getColumnDimension('E')->setWidth(12);
$spreadsheet->getActiveSheet()->getColumnDimension('F')->setWidth(40);
$spreadsheet->getActiveSheet()->getColumnDimension('G')->setWidth(12);
$spreadsheet->getActiveSheet()->getColumnDimension('H')->setWidth(12);
$spreadsheet->getActiveSheet()->getColumnDimension('T')->setWidth(12);
$spreadsheet->getActiveSheet()->getColumnDimension('U')->setWidth(12);
$spreadsheet->getActiveSheet()->getColumnDimension('V')->setWidth(12);
$spreadsheet->getActiveSheet()->getColumnDimension('W')->setWidth(12);
$spreadsheet->getActiveSheet()->getColumnDimension('X')->setWidth(50);
$spreadsheet->getActiveSheet()->getStyle('A6:AA6')->getBorders()->getTop()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK);
$spreadsheet->getActiveSheet()->getStyle('A7:AA7')->getBorders()->getBottom()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
$spreadsheet->getActiveSheet()->getStyle('A6:A7')->getBorders()->getRight()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
$spreadsheet->getActiveSheet()->getStyle('B6:B7')->getBorders()->getRight()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
$spreadsheet->getActiveSheet()->getStyle('C6:C7')->getBorders()->getRight()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
$spreadsheet->getActiveSheet()->getStyle('D6:D7')->getBorders()->getRight()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
$spreadsheet->getActiveSheet()->getStyle('E6:E7')->getBorders()->getRight()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
$spreadsheet->getActiveSheet()->getStyle('F6:F7')->getBorders()->getRight()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
$spreadsheet->getActiveSheet()->getStyle('G6:G7')->getBorders()->getRight()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
$spreadsheet->getActiveSheet()->getStyle('H6:H7')->getBorders()->getRight()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
$spreadsheet->getActiveSheet()->getStyle('I6:I7')->getBorders()->getRight()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
$spreadsheet->getActiveSheet()->getStyle('I6:Q6')->getBorders()->getBottom()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
$spreadsheet->getActiveSheet()->getStyle('J6:J7')->getBorders()->getRight()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
$spreadsheet->getActiveSheet()->getStyle('K6:K7')->getBorders()->getRight()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
$spreadsheet->getActiveSheet()->getStyle('L6:L7')->getBorders()->getRight()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
$spreadsheet->getActiveSheet()->getStyle('M6:M7')->getBorders()->getRight()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
$spreadsheet->getActiveSheet()->getStyle('N6:N7')->getBorders()->getRight()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
$spreadsheet->getActiveSheet()->getStyle('O6:O7')->getBorders()->getRight()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
$spreadsheet->getActiveSheet()->getStyle('P6:P7')->getBorders()->getRight()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
$spreadsheet->getActiveSheet()->getStyle('Q6:Q7')->getBorders()->getRight()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
$spreadsheet->getActiveSheet()->getStyle('R6:R7')->getBorders()->getRight()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
$spreadsheet->getActiveSheet()->getStyle('R6')->getAlignment()->setWrapText(true);
$spreadsheet->getActiveSheet()->getStyle('S6:S7')->getBorders()->getRight()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
$spreadsheet->getActiveSheet()->getStyle('S6')->getAlignment()->setWrapText(true);
$spreadsheet->getActiveSheet()->getStyle('T6:T7')->getBorders()->getRight()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
$spreadsheet->getActiveSheet()->getStyle('T6')->getAlignment()->setWrapText(true);
$spreadsheet->getActiveSheet()->getStyle('U6:U7')->getBorders()->getRight()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
$spreadsheet->getActiveSheet()->getStyle('U6')->getAlignment()->setWrapText(true);
$spreadsheet->getActiveSheet()->getStyle('V6:V7')->getBorders()->getRight()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
$spreadsheet->getActiveSheet()->getStyle('V6')->getAlignment()->setWrapText(true);
$spreadsheet->getActiveSheet()->getStyle('W6:W7')->getBorders()->getRight()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
$spreadsheet->getActiveSheet()->getStyle('W6')->getAlignment()->setWrapText(true);
$spreadsheet->getActiveSheet()->getStyle('I6')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
$spreadsheet->getActiveSheet()->getStyle('X6')->getAlignment()->setWrapText(true);
$spreadsheet->getActiveSheet()->getStyle('Y6')->getAlignment()->setWrapText(true);
$spreadsheet->getActiveSheet()->getStyle('Z6')->getAlignment()->setWrapText(true);
$spreadsheet->getActiveSheet()->getStyle('AA6')->getAlignment()->setWrapText(true);
$spreadsheet->getActiveSheet()->getColumnDimension('Y')->setWidth(50);
$spreadsheet->getActiveSheet()->getColumnDimension('Z')->setWidth(50);
$spreadsheet->getActiveSheet()->getColumnDimension('AA')->setWidth(50);
$spreadsheet->getActiveSheet()->getStyle('A6')->getBorders()->getAllBorders();
$spreadsheet->getActiveSheet()->mergeCells('A1:D1');
$spreadsheet->getActiveSheet()->getStyle('A6:AA7')->getFont()->setBold(true);
$hojaActiva->setCellValue('A1','INFORME DE COORDINADOR - REPORTES:'.$numero);
$spreadsheet->getActiveSheet()->mergeCells('A2:B2');
$hojaActiva->setCellValue('A2','INFORMACIÓN DESDE:');
//foreach($spreadsheet->getActiveSheet()->getColumnDimension() as $col) { $col->setAutoSize(true); }

//$spreadsheet->getActiveSheet()->getStyle('A6:AA7')->getBorders()->getAllBorders()   ;
$spreadsheet->getActiveSheet()->getStyle('A1:A5')->getFont()->setBold(true);
$spreadsheet->getActiveSheet()->getStyle('E2')->getFont()->setBold(true);
$hojaActiva->setCellValue('C2',Date::PHPtoExcel(date("d/m/Y",strtotime($fechaInicial))));
    $spreadsheet->getActiveSheet()
        ->getStyle('C2'.$fila)
        ->getNumberFormat()
        ->setFormatCode(NumberFormat::FORMAT_DATE_DDMMYYYY);
$hojaActiva->setCellValue('E2','HASTA:');
$spreadsheet->getActiveSheet()->getStyle('E2')->getFont()->setBold(true);
$hojaActiva->setCellValue('F2',Date::PHPtoExcel(date("d/m/Y",strtotime($fechaFinal))));
$spreadsheet->getActiveSheet()
        ->getStyle('F2'.$fila)
        ->getNumberFormat()
        ->setFormatCode(NumberFormat::FORMAT_DATE_DDMMYYYY);
$spreadsheet->getActiveSheet()->mergeCells('A3:B3');
$hojaActiva->setCellValue('A3','NOMBRE DEL SOCIO:');

$spreadsheet->getActiveSheet()->mergeCells('C3:F3');
$hojaActiva->setCellValue('C3','Confraternidad Carcelaria de Colombia');
$hojaActiva->setCellValue('A4','USUARIO:');
$spreadsheet->getActiveSheet()->mergeCells('B4:C4');
$hojaActiva->setCellValue('B4',$_SESSION["nombre"]);
$hojaActiva->setCellValue('A5','PROCESO:');
$spreadsheet->getActiveSheet()->mergeCells('B5:C5');
$hojaActiva->setCellValue('B5',"EVANGELISTAS");
$spreadsheet->getActiveSheet()->mergeCells('A6:A7');
$hojaActiva->setCellValue('A6',"Fecha del reporte");
$spreadsheet->getActiveSheet()->getStyle('A6')->getAlignment()->setWrapText(true);
$spreadsheet->getActiveSheet()->mergeCells('B6:B7');
$hojaActiva->setCellValue('B6',"Zona");
$spreadsheet->getActiveSheet()->mergeCells('C6:C7');
$hojaActiva->setCellValue('C6',"Regional");
$spreadsheet->getActiveSheet()->mergeCells('D6:D7');
$hojaActiva->setCellValue('D6',"Nombre de coordinador");
$spreadsheet->getActiveSheet()->mergeCells('E6:E7');
$hojaActiva->setCellValue('E6',"Numero de prisiones atendidas");
$spreadsheet->getActiveSheet()->getStyle('E6')->getAlignment()->setWrapText(true);
$spreadsheet->getActiveSheet()->mergeCells('F6:F7');
$hojaActiva->setCellValue('F6',"Nombre de prisiones atendidas");
$spreadsheet->getActiveSheet()->getStyle('F6')->getAlignment()->setWrapText(true);
$spreadsheet->getActiveSheet()->mergeCells('G6:G7');
$hojaActiva->setCellValue('G6',"Grupos intramueros atendidos");
$spreadsheet->getActiveSheet()->getStyle('G6')->getAlignment()->setWrapText(true);
$spreadsheet->getActiveSheet()->mergeCells('H6:H7');
$hojaActiva->setCellValue('H6',"Grupos extramueros atendidos");
$spreadsheet->getActiveSheet()->getStyle('H6')->getAlignment()->setWrapText(true);
$spreadsheet->getActiveSheet()->mergeCells('I6:Q6');
$hojaActiva->setCellValue('I6',"Actividades realizadas");
$hojaActiva->setCellValue('I7','Orar');
$hojaActiva->setCellValue('J7','Compañerismo');
$hojaActiva->setCellValue('K7','Adorar');
$hojaActiva->setCellValue('L7','Aplicar la biblia');
$hojaActiva->setCellValue('M7','Evangelizar');
$hojaActiva->setCellValue('N7','Cena del señor');
$hojaActiva->setCellValue('O7','Dar');
$hojaActiva->setCellValue('P7','Bautizar');
$hojaActiva->setCellValue('Q7','Entrenar nuevos lideres');
$spreadsheet->getActiveSheet()->mergeCells('R6:R7');
$hojaActiva->setCellValue('R6',"Total de creyentes que asistieron");
$spreadsheet->getActiveSheet()->mergeCells('S6:S7');
$hojaActiva->setCellValue('S6',"Total de discípulos");
$spreadsheet->getActiveSheet()->mergeCells('T6:T7');
$hojaActiva->setCellValue('T6',"Número de bautizados");
$spreadsheet->getActiveSheet()->mergeCells('U6:U7');
$hojaActiva->setCellValue('U6',"Número de voluntarios internos activos");
$spreadsheet->getActiveSheet()->mergeCells('V6:V7');
$hojaActiva->setCellValue('V6',"Número de voluntarios externos activos");
$spreadsheet->getActiveSheet()->mergeCells('W6:W7');
$hojaActiva->setCellValue('W6',"Número de pospenados que está acompañando");
$spreadsheet->getActiveSheet()->mergeCells('X6:X7');
$hojaActiva->setCellValue('X6','Descripción de un testimonio de Impacto positivo en la vida de un PPL como resultado de su proceso de Evangelismo y Discipulado');
$spreadsheet->getActiveSheet()->mergeCells('Y6:Y7');
$hojaActiva->setCellValue('Y6','Describir una experiencia de superación personal de un participante del Programa Pospenado en su Regional');
$spreadsheet->getActiveSheet()->mergeCells('Z6:Z7');
$hojaActiva->setCellValue('Z6','Describir un testimonio de la autoridad Carcelaria acerca del impacto positivo que ha generado la implementación de los programas de la CCC en la vida de los internos');
$spreadsheet->getActiveSheet()->mergeCells('AA6:AA7');
$hojaActiva->setCellValue('AA6','Describir las observaciones o comentarios sobre los obstáculos y dificultades durante este período en el desarrollo de las actividades');

//$spreadsheet->getActiveSheet()->mergeCells('B7:C7');
if($numero > 0){
    $fila = 8;
    while($PSN1->next_record()){
        $spreadsheet->getActiveSheet()
        ->getStyle('A'.$fila)
        ->getNumberFormat()
        ->setFormatCode(NumberFormat::FORMAT_DATE_DDMMYYYY);
        $hojaActiva->setCellValue('A'.$fila,Date::PHPtoExcel(date("d/m/Y", strtotime($PSN1->f('fechaReporte')))));
        $hojaActiva->setCellValue('B'.$fila,$PSN1->f('zona_usuario'));
        $hojaActiva->setCellValue('C'.$fila,$PSN1->f('rgal_usuario'));
        $hojaActiva->setCellValue('D'.$fila,$PSN1->f('nombreUsuario'));
        $hojaActiva->setCellValue('E'.$fila,$PSN1->f('asistencia_total'));
        $sql = "SELECT P.*,U.* ";
        $sql .= " FROM tbl_adjuntos AS P
            LEFT JOIN tbl_regional_ubicacion AS U ON U.reub_id = P.adj_nom
            WHERE adj_rep_fk = ".$PSN1->f('id')." AND adj_tip = 4";
        $PSN2->query($sql);
        $prisiones_resul = "";
        if($PSN2->num_rows() > 0){
            while($PSN2->next_record()){
                $prisiones_resul .= $PSN2->f('reub_nom').", ";
            }
        }
        $prisiones_resul = substr($prisiones_resul, 0, -2);
        $hojaActiva->setCellValue('F'.$fila,$prisiones_resul);
        $hojaActiva->setCellValue('G'.$fila,$PSN1->f('asistencia_hom'));
        $hojaActiva->setCellValue('H'.$fila,$PSN1->f('asistencia_muj'));
        $mapeo_oracion = "No";
        if($PSN1->f('mapeo_oracion')==1){
            $mapeo_oracion = "Sí";
        }
        $hojaActiva->setCellValue('I'.$fila,$mapeo_oracion);
        $mapeo_companerismo = "No";
        if($PSN1->f('mapeo_companerismo')==1){
            $mapeo_companerismo = "Sí";
        }
        $hojaActiva->setCellValue('J'.$fila,$mapeo_companerismo);
        $mapeo_adoracion = "No";
        if($PSN1->f('mapeo_adoracion')==1){
            $mapeo_adoracion = "Sí";
        }
        $hojaActiva->setCellValue('K'.$fila,$mapeo_adoracion);
        $mapeo_biblia = "No";
        if($PSN1->f('mapeo_biblia')==1){
            $mapeo_biblia = "Sí";
        }
        $hojaActiva->setCellValue('L'.$fila,$mapeo_biblia);
        $mapeo_evangelizar = "No";
        if($PSN1->f('mapeo_evangelizar')==1){
            $mapeo_evangelizar = "Sí";
        }
        $hojaActiva->setCellValue('M'.$fila,$mapeo_evangelizar);
        $mapeo_cena = "No";
        if($PSN1->f('mapeo_cena')==1){
            $mapeo_cena = "Sí";
        }
        $hojaActiva->setCellValue('N'.$fila,$mapeo_cena);
        $mapeo_dar = "No";
        if($PSN1->f('mapeo_dar')==1){
            $mapeo_dar = "Sí";
        }
        $hojaActiva->setCellValue('O'.$fila,$mapeo_dar);
        $mapeo_bautizar = "No";
        if($PSN1->f('mapeo_bautizar')==1){
            $mapeo_bautizar = "Sí";
        }
        $hojaActiva->setCellValue('P'.$fila,$mapeo_bautizar);
        $mapeo_trabajadores = "No";
        if($PSN1->f('mapeo_trabajadores')==1){
            $mapeo_trabajadores = "Sí";
        }
        $hojaActiva->setCellValue('Q'.$fila,$mapeo_trabajadores);
        
        $hojaActiva->setCellValue('R'.$fila,intval($PSN1->f('asistencia_jov')));
        $hojaActiva->setCellValue('S'.$fila,intval($PSN1->f('asistencia_nin')));
        $hojaActiva->setCellValue('T'.$fila,intval($PSN1->f('bautizados')));
        $hojaActiva->setCellValue('U'.$fila,intval($PSN1->f('discipulado')));
        $hojaActiva->setCellValue('V'.$fila,intval($PSN1->f('desiciones')));
        $hojaActiva->setCellValue('W'.$fila,intval($PSN1->f('preparandose')));
        $hojaActiva->setCellValue('X'.$fila,$PSN1->f('comentario'));
        $spreadsheet->getActiveSheet()->getStyle('X'.$fila)->getAlignment()->setWrapText(true);
        $hojaActiva->setCellValue('Y'.$fila,$PSN1->f('rep_text2'));
        $spreadsheet->getActiveSheet()->getStyle('Y'.$fila)->getAlignment()->setWrapText(true);
        $hojaActiva->setCellValue('Z'.$fila,$PSN1->f('rep_text3'));
        $spreadsheet->getActiveSheet()->getStyle('Z'.$fila)->getAlignment()->setWrapText(true);
        $hojaActiva->setCellValue('AA'.$fila,$PSN1->f('rep_text4'));
        $spreadsheet->getActiveSheet()->getStyle('AA'.$fila)->getAlignment()->setWrapText(true);
        $fecRep = date("Y-m-d", strtotime($PSN1->f('fechaReporte')));
        if ($iniQ<=$fecRep && $fecRep<=$finQ) {
            $desici = intval($PSN1->f('desiciones'));
        }else{
            $desici = "0";
        }

        if ($iniQ<=$fecRep && $fecRep<=$finQ) {
            $bautiP = intval($PSN1->f('bautizadosPeriodo'));
        }else{
            $bautiP = "0";
        }
        if($suma > 0){
           $promedio = $suma/9;
           $promedio = floatval($promedio);
           $prom = round($promedio, 2);
        }else{
            $prom =0;
        }
        
        $fila++;

    }
}
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="Archivo_'.date("Ymd_His").'.xlsx"');
header('Cache-Control: max-age=0');
$spreadsheet->setActiveSheetIndex(0);
$hojaActiva = $spreadsheet->getActiveSheet();

$writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
$writer->save('php://output');
?>