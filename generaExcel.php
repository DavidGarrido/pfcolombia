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
$sqlFiltro .= " AND sat_reportes.generacionNumero != 0";

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
//
if(isset($_REQUEST["idGrupoMadre"]) && soloNumeros($_REQUEST["idGrupoMadre"]) != ""){
    $buscar_idGrupoMadre = soloNumeros($_REQUEST["idGrupoMadre"]);
    $sqlFiltro .= " AND sat_reportes.idGrupoMadre = '".$buscar_idGrupoMadre."'";
}

//
if(isset($_REQUEST["nombre"]) && eliminarInvalidos($_REQUEST["nombre"]) != ""){
    $buscar_nombre = eliminarInvalidos($_REQUEST["nombre"]);
    $sqlFiltro .= " AND sat_reportes.plantador LIKE '%".$buscar_nombre."%'";
}
$tipo = 0;
if(isset($_REQUEST["rep_inex"]) && eliminarInvalidos($_REQUEST["rep_inex"]) != ""){
        $tipo = eliminarInvalidos($_REQUEST["rep_inex"]);
        if ($tipo == 2) {
            $sqlFiltro .= " AND sat_reportes.sitioReunion = 0 ";
        }else{
            $sqlFiltro .= " AND sat_reportes.sitioReunion <> 0 ";
        }    
    }

//
if(isset($_REQUEST["fechaInicial"]) && eliminarInvalidos($_REQUEST["fechaInicial"]) != ""){
    $fechaInicial = eliminarInvalidos($_REQUEST["fechaInicial"]);
    $sqlFiltro .= " AND sat_reportes.fechaReporte >= '".$fechaInicial."'";
}

//
if(isset($_REQUEST["fechaFinal"]) && eliminarInvalidos($_REQUEST["fechaFinal"]) != ""){
    $fechaFinal = eliminarInvalidos($_REQUEST["fechaFinal"]);
    $sqlFiltro .= " AND sat_reportes.fechaReporte <= '".$fechaFinal."'";
}
                
$sql = "SELECT C.descripcion AS rgal_usuario, DD.departamento AS dpto_usuario, DM.municipio AS mnpo_usuario, ";
$sql.=" RUU.reub_nom AS prision, RUU.reub_dir AS dire_prision, CA.descripcion AS rgal_prision, MU.municipio AS mnpo_prision, DE.departamento AS dpto_prision, M.municipio AS mnpo_prision_extra, D.departamento AS dpto_prision_extra, sat_reportes.*, "; 
$sql.=" sat_reportes.*,
    M.municipio, D.departamento,
            usuario.nombre as nombreUsuario,
            usuario.direccion as direccionUsuario,
            usuario.identificacion as identificacionUsuario,
            usuario_empresa.empresa_sitio,
            usuario_empresa.empresa_socio,
            usuario_empresa.empresa_rm,
            usuario_empresa.empresa_proceso,
            usuario_empresa.empresa_paisid ";
$sql .= " FROM sat_reportes ";
$sql .= " LEFT JOIN usuario ON usuario.id = sat_reportes.idUsuario";
$sql .= " LEFT JOIN usuario_empresa ON usuario_empresa.idUsuario = sat_reportes.idUsuario
LEFT JOIN dane_municipios AS DM ON DM.id_municipio = usuario.usua_muni
    LEFT JOIN dane_departamentos AS DD ON DD.id_departamento = DM.departamento_id ";
$sql .= " LEFT JOIN tbl_regional_ubicacion AS RUU ON RUU.reub_id = sat_reportes.sitioReunion
 LEFT JOIN categorias AS CA ON CA.id = RUU.reub_reg_fk
 LEFT JOIN dane_municipios AS MU ON MU.id_municipio = RUU.reub_mun_fk
 LEFT JOIN dane_departamentos AS DE ON DE.id_departamento = MU.departamento_id ";
$sql .= "LEFT JOIN categorias AS C ON C.id = usuario_empresa.empresa_pd
    LEFT JOIN dane_municipios AS M ON M.id_municipio = sat_reportes.ciudad
    LEFT JOIN dane_departamentos AS D ON D.id_departamento = M.departamento_id ";
$sql .= " WHERE 1 ".$sqlFiltro." ORDER BY usuario_empresa.empresa_paisid ASC, usuario.nombre ASC";
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
/*$spreadsheet->getActiveSheet()->getStyle('C')
        ->getNumberFormat()
        ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_DATE_DDMMYYYY);*/
$hojaActiva = $spreadsheet->getActiveSheet();
$spreadsheet->getActiveSheet()->getStyle('A1:AI10')->getFill()
    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
    ->getStartColor()->setARGB('C8FFFF');
$spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(12);
$spreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(12);
$spreadsheet->getActiveSheet()->getColumnDimension('D')->setWidth(12);
$spreadsheet->getActiveSheet()->getColumnDimension('AF')->setWidth(12);
$spreadsheet->getActiveSheet()->getColumnDimension('AG')->setWidth(12);
$spreadsheet->getActiveSheet()->getColumnDimension('AH')->setWidth(12);
$spreadsheet->getActiveSheet()->mergeCells('A2:D2');
$spreadsheet->getActiveSheet()->getStyle('A9:AI10')->getFont()->setBold(true);
$hojaActiva->setCellValue('A2','INFORME DE COORDINADOR - REPORTES:'.$numero);
$hojaActiva->setCellValue('A3','INFORMACIÓN DESDE:');
$spreadsheet->getActiveSheet()->getStyle('A2:A8')->getFont()->setBold(true);
$spreadsheet->getActiveSheet()->getStyle('C3:C4')->getFont()->setBold(true);
$spreadsheet->getActiveSheet()->getStyle('E4')->getFont()->setBold(true);
$hojaActiva->setCellValue('B3',Date::PHPtoExcel(date("d/m/Y",strtotime($fechaInicial))));
    $spreadsheet->getActiveSheet()
        ->getStyle('B3'.$fila)
        ->getNumberFormat()
        ->setFormatCode(NumberFormat::FORMAT_DATE_DDMMYYYY);
$hojaActiva->setCellValue('C3','HASTA:');
$hojaActiva->setCellValue('D3',Date::PHPtoExcel(date("d/m/Y",strtotime($fechaFinal))));
$spreadsheet->getActiveSheet()
        ->getStyle('D3'.$fila)
        ->getNumberFormat()
        ->setFormatCode(NumberFormat::FORMAT_DATE_DDMMYYYY);
$hojaActiva->setCellValue('A4','PERIODO Q DESDE:');
$hojaActiva->setCellValue('B4', Date::PHPtoExcel(date("d/m/Y", strtotime($iniQ))));
$spreadsheet->getActiveSheet()
        ->getStyle('B4'.$fila)
        ->getNumberFormat()
        ->setFormatCode(NumberFormat::FORMAT_DATE_DDMMYYYY);
$hojaActiva->setCellValue('C4','HASTA:');
$hojaActiva->setCellValue('D4', Date::PHPtoExcel(date("d/m/Y", strtotime($finQ))));
$spreadsheet->getActiveSheet()
        ->getStyle('D4'.$fila)
        ->getNumberFormat()
        ->setFormatCode(NumberFormat::FORMAT_DATE_DDMMYYYY);
$q = "";
switch ($_REQUEST['rep_qua']) {
    case '1':
        $q = "1";
        break;
    case '4':
        $q = "2";
        break;
    case '7':
        $q = "3";
        break;
    case '10':
        $q = "4";
        break;
    default:
        // code...
        break;
}
$hojaActiva->setCellValue('E4','Q:'.$q);
$spreadsheet->getActiveSheet()->mergeCells('A5:B5');
$hojaActiva->setCellValue('A5','NOMBRE DEL SOCIO:');

$spreadsheet->getActiveSheet()->mergeCells('C5:F5');
$hojaActiva->setCellValue('C5','Confraternidad Carcelaria de Colombia');

$hojaActiva->setCellValue('A6','USUARIO:');
$spreadsheet->getActiveSheet()->mergeCells('B6:C6');
$hojaActiva->setCellValue('B6',$_SESSION["nombre"]);
$hojaActiva->setCellValue('A7','PROCESO:');
$spreadsheet->getActiveSheet()->mergeCells('B7:C7');
$hojaActiva->setCellValue('B7',"CAPACITAR Y MULTPLICAR (C&M)");

$spreadsheet->getActiveSheet()->mergeCells('A9:A10');
$hojaActiva->setCellValue('A9',"Nombre del lider");
$spreadsheet->getActiveSheet()->mergeCells('B9:B10');
$hojaActiva->setCellValue('B9',"Nombre del grupo / iglesia");
$spreadsheet->getActiveSheet()->mergeCells('C9:C10');
$hojaActiva->setCellValue('C9',"Fecha de Inicio");
$spreadsheet->getActiveSheet()->mergeCells('D9:D10');
$hojaActiva->setCellValue('D9',"Generación");
$spreadsheet->getActiveSheet()->mergeCells('E9:E10');
$hojaActiva->setCellValue('E9',"Ubicación");
$spreadsheet->getActiveSheet()->mergeCells('F9:F10');
$hojaActiva->setCellValue('F9',"Grupo Madre / Iglesia");
$hojaActiva->setCellValue('G9',"Asistencia del grupo");
$hojaActiva->setCellValue('G10','=SUM(G11:G'.$ubi_regist.')');
$hojaActiva->setCellValue('H9',"Total de creyentes en el grupo");
$hojaActiva->setCellValue('H10','=SUM(H11:H'.$ubi_regist.')');
$hojaActiva->setCellValue('I9',"Nuevos creyentes en el grupo en este periodo");
$hojaActiva->setCellValue('I10','=SUM(I11:I'.$ubi_regist.')');
$hojaActiva->setCellValue('J9',"Total de bautizados en el grupo");
$hojaActiva->setCellValue('J10','=SUM(J11:J'.$ubi_regist.')');
$hojaActiva->setCellValue('K9',"Nuevos bautizados en el grupo en este periodo");
$hojaActiva->setCellValue('K10','=SUM(K11:K'.$ubi_regist.')');
$hojaActiva->setCellValue('L9',"Orar");
$hojaActiva->setCellValue('L10','=SUM(L11:L'.$ubi_regist.')');
$hojaActiva->setCellValue('M9',"Companerismo");
$hojaActiva->setCellValue('M10','=SUM(M11:M'.$ubi_regist.')');
$hojaActiva->setCellValue('N9',"Adorar");
$hojaActiva->setCellValue('N10','=SUM(N11:N'.$ubi_regist.')');
$hojaActiva->setCellValue('O9',"Aplicar la biblia");
$hojaActiva->setCellValue('O10','=SUM(O11:O'.$ubi_regist.')');
$hojaActiva->setCellValue('P9',"Evangelizar");
$hojaActiva->setCellValue('P10','=SUM(P11:P'.$ubi_regist.')');
$hojaActiva->setCellValue('Q9',"Cena del Señor");
$hojaActiva->setCellValue('Q10','=SUM(Q11:Q'.$ubi_regist.')');
$hojaActiva->setCellValue('R9',"Dar");
$hojaActiva->setCellValue('R10','=SUM(R11:R'.$ubi_regist.')');
$hojaActiva->setCellValue('S9',"Bautizar");
$hojaActiva->setCellValue('S10','=SUM(S11:S'.$ubi_regist.')');
$hojaActiva->setCellValue('T9',"Entrenar nuevos lideres");
$hojaActiva->setCellValue('T10','=SUM(T11:T'.$ubi_regist.')');
$hojaActiva->setCellValue('U9',"1");
$hojaActiva->setCellValue('U10','=SUM(U11:U'.$ubi_regist.')');
$hojaActiva->setCellValue('V9',"2");
$hojaActiva->setCellValue('V10','=SUM(V11:V'.$ubi_regist.')');
$hojaActiva->setCellValue('W9',"3");
$hojaActiva->setCellValue('W10','=SUM(W11:W'.$ubi_regist.')');
$hojaActiva->setCellValue('X9',"4");
$hojaActiva->setCellValue('X10','=SUM(X11:X'.$ubi_regist.')');
//$hojaActiva->setCellValue('Y9'," ");
$hojaActiva->setCellValue('Z10',"Ubicacion del entrenador");
$hojaActiva->setCellValue('AA10','Entrenador');
$hojaActiva->setCellValue('AB10','Carnet de identidad');
$hojaActiva->setCellValue('AC10','Ch');
$hojaActiva->setCellValue('AD10','Suma');
$hojaActiva->setCellValue('AD10','Suma');
$hojaActiva->setCellValue('AE10','Promedio');
$hojaActiva->setCellValue('AF10','Desde');
$hojaActiva->setCellValue('AG10','Hasta');
$hojaActiva->setCellValue('AH10','Reunido');
$hojaActiva->setCellValue('AI10','Nombre del socio');


$spreadsheet->getActiveSheet()->mergeCells('B7:C7');
if($numero > 0){
    $fila = 11;
    while($PSN1->next_record()){
        $hojaActiva->setCellValue('A'.$fila,$PSN1->f('plantador')."/".$PSN1->f('rep_entr'));
        $hojaActiva->setCellValue('B'.$fila,$PSN1->f('nombreGrupo_txt'));
        $hojaActiva->setCellValue('C'.$fila,Date::PHPtoExcel(date("d/m/Y", strtotime($PSN1->f('fechaInicio')))));
        $spreadsheet->getActiveSheet()
        ->getStyle('C'.$fila)
        ->getNumberFormat()
        ->setFormatCode(NumberFormat::FORMAT_DATE_DDMMYYYY);
        $hojaActiva->setCellValue('D'.$fila,$PSN1->f('generacionNumero'));
        if($tipo == 2){
            $hojaActiva->setCellValue('E'.$fila,$PSN1->f('prision')." / ".$PSN1->f('dpto_prision')." / ".$PSN1->f('mnpo_prision')." / ".$PSN1->f('dire_prision'));
        }else{
            $hojaActiva->setCellValue('E'.$fila,$PSN1->f('dpto_prision_extra')." / ".$PSN1->f('mnpo_prision_extra')." / ".$PSN1->f('direccion'));
        }
        $hojaActiva->setCellValue('F'.$fila,$PSN1->f('grupoMadre_txt'));
        $hojaActiva->setCellValue('G'.$fila,intval($PSN1->f('asistencia_total')));
        $hojaActiva->setCellValue('H'.$fila,intval($PSN1->f('discipulado')));
        $fecRep = date("Y-m-d", strtotime($PSN1->f('fechaInicio')));
        if ($iniQ<=$fecRep && $fecRep<=$finQ) {
            $desici = intval($PSN1->f('desiciones'));
        }else{
            $desici = "0";
        }
        $hojaActiva->setCellValue('I'.$fila,intval($desici));
        $hojaActiva->setCellValue('J'.$fila,intval($PSN1->f('bautizados')));
        if ($iniQ<=$fecRep && $fecRep<=$finQ) {
            $bautiP = intval($PSN1->f('bautizadosPeriodo'));
        }else{
            $bautiP = "0";
        }
        $hojaActiva->setCellValue('K'.$fila,intval($bautiP));
        $hojaActiva->setCellValue('L'.$fila,intval($PSN1->f('mapeo_oracion')));
        $hojaActiva->setCellValue('M'.$fila,intval($PSN1->f('mapeo_companerismo')));
        $hojaActiva->setCellValue('N'.$fila,intval($PSN1->f('mapeo_adoracion')));
        $hojaActiva->setCellValue('O'.$fila,intval($PSN1->f('mapeo_biblia')));
        $hojaActiva->setCellValue('P'.$fila,intval($PSN1->f('mapeo_evangelizar')));
        $hojaActiva->setCellValue('Q'.$fila,intval($PSN1->f('mapeo_cena')));
        $hojaActiva->setCellValue('R'.$fila,intval($PSN1->f('mapeo_dar')));
        $hojaActiva->setCellValue('S'.$fila,intval($PSN1->f('mapeo_bautizar')));
        $hojaActiva->setCellValue('T'.$fila,intval($PSN1->f('mapeo_trabajadores')));
        $hojaActiva->setCellValue('U'.$fila,0);
        $hojaActiva->setCellValue('V'.$fila,0);
        $hojaActiva->setCellValue('W'.$fila,0);
        $hojaActiva->setCellValue('X'.$fila,0);
        //$hojaActiva->setCellValue('Y'.$fila,intval($PSN1->f('mapeo_oracion')));
        $hojaActiva->setCellValue('Z'.$fila,$PSN1->f('dpto_usuario')." / ".$PSN1->f('mnpo_usuario')." / ".$PSN1->f('direccionUsuario'));
        $hojaActiva->setCellValue('AA'.$fila,$PSN1->f('nombreUsuario'));
        $hojaActiva->setCellValue('AB'.$fila,$PSN1->f('identificacionUsuario'));
        $hojaActiva->setCellValue('AC'.$fila,$PSN1->f('mapeo_comprometido'));
        $suma = $PSN1->f('mapeo_oracion')+$PSN1->f('mapeo_companerismo')+$PSN1->f('mapeo_adoracion')+$PSN1->f('mapeo_biblia')+$PSN1->f('mapeo_evangelizar')+$PSN1->f('mapeo_cena')+$PSN1->f('mapeo_dar')+$PSN1->f('mapeo_bautizar')+$PSN1->f('mapeo_trabajadores');
        $hojaActiva->setCellValue('AD'.$fila,$suma);
        if($suma > 0){
           $promedio = $suma/9;
           $promedio = floatval($promedio);
           $prom = round($promedio, 2);
        }else{
            $prom =0;
        }
        $hojaActiva->setCellValue('AE'.$fila,$prom);
        $hojaActiva->setCellValue('AF'.$fila,Date::PHPtoExcel(date("d/m/Y", strtotime($iniQ))));
        $spreadsheet->getActiveSheet()
        ->getStyle('AF'.$fila)
        ->getNumberFormat()
        ->setFormatCode(NumberFormat::FORMAT_DATE_DDMMYYYY);
        $hojaActiva->setCellValue('AG'.$fila,Date::PHPtoExcel(date("d/m/Y", strtotime($finQ))));
        $spreadsheet->getActiveSheet()
        ->getStyle('AG'.$fila)
        ->getNumberFormat()
        ->setFormatCode(NumberFormat::FORMAT_DATE_DDMMYYYY);
        $hojaActiva->setCellValue('AH'.$fila,Date::PHPtoExcel(date("d/m/Y", strtotime($PSN1->f('mapeo_fecha')))));
        $spreadsheet->getActiveSheet()
        ->getStyle('AH'.$fila)
        ->getNumberFormat()
        ->setFormatCode(NumberFormat::FORMAT_DATE_DDMMYYYY);
        if($tipo == 2){
        $hojaActiva->setCellValue('AI'.$fila,$PSN1->f('rgal_prision'));
        }else{
          $hojaActiva->setCellValue('AI'.$fila,$PSN1->f('rgal_usuario'));  
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