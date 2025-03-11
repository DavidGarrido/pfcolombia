<?php
//Porcentajes de efectividad
function obtenerPorcentaje($cantidad, $total) {
    $porcentaje = ((float)$cantidad * 100) / $total; // Regla de tres
    $porcentaje = round($porcentaje, 2);  // Quitar los decimales
    return $porcentaje;
}

/*******************************************
En un rango de fechas:
Cuantos prospecto hay desde el día 1 y cuantos el día último.
La diferencia es = nuevos prospectos, 
con TODOS los COMERCIALES.

ID MENU = 1
*******************************************/

$mesesNom = array("No", "Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre");

$PSN = new DBbase_Sql;
$PSN1 = new DBbase_Sql;
$PSN2 = new DBbase_Sql;
$PSN3 = new DBbase_Sql;

$sql= "SELECT idMenu ";
$sql.=" FROM usuarios_menu_graphs ";
$sql.=" WHERE idMenu = 1 
AND idUsuario = '".$_SESSION["id"]."'";
$PSN->query($sql);
if($PSN->num_rows() == 0)
{
    die("NO esta autorizado a ver esta grafica.");
}


/*
*   FILTROS DE FECHAS.
*/
$busquedaFechaIni = date("2000-01-01");
if(isset($_REQUEST["fechaInicial"]) && soloNumeros($_REQUEST["fechaInicial"]) != ""){
	$busquedaFechaIni = eliminarInvalidos($_REQUEST["fechaInicial"]);
}
else{
    $_REQUEST["fechaInicial"] = $busquedaFechaIni;
}
//
$siguiente_anho = date("Y", strtotime("+1 year"));
//$busquedaFechaFin = $siguiente_anho."-01-31";
$busquedaFechaFin = date("Y-m-d");
if(isset($_REQUEST["fechaFinal"]) && soloNumeros($_REQUEST["fechaFinal"]) != ""){
	$busquedaFechaFin = eliminarInvalidos($_REQUEST["fechaFinal"]);
}else{
    $_REQUEST["fechaFinal"] = $busquedaFechaFin;
}

$anho_actual = date("Y", strtotime($_REQUEST["fechaInicial"]));

if($_REQUEST["idUsuario"] == ""){
    $_REQUEST["idUsuario"] = 0;
}

/*****************************************************************************
//  EN ESTA GRAFICA NO CUENTA PERO PODRIA SERVIR PARA OTRAS
//Si es cliente o autorizado
*****************************************************************************/
if($_SESSION["perfil"] == 163){
    //
    $_REQUEST["idUsuario"] = $_SESSION["id"];
    //  
}


$sqlFiltros = "";


if(!isset($_REQUEST["fechaInicial"]) || eliminarInvalidos($_REQUEST["fechaInicial"]) == ""){
    $_REQUEST["fechaInicial"] = date("Y-m-01", strtotime("-2 months"));
}
if(!isset($_REQUEST["fechaFinal"]) || eliminarInvalidos($_REQUEST["fechaFinal"]) == ""){
    $_REQUEST["fechaFinal"] = date("Y-m-d");
}


if(isset($_REQUEST["idUsuario"]) && soloNumeros($_REQUEST["idUsuario"]) != "" && soloNumeros($_REQUEST["idUsuario"]) > 0){
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
}else if ($_SESSION["empresa_pd"]!="" && $_SESSION["empresa_pd"]!=0) {
    $buscar_regional = soloNumeros($_SESSION["empresa_pd"]);
    $sqlFiltro .= " AND UE.empresa_pd = '".$_SESSION["empresa_pd"]."'";
    $_REQUEST["empresa_pd"] = $_SESSION["empresa_pd"];
}

if(isset($_REQUEST["sitioReunion"]) && soloNumeros($_REQUEST["sitioReunion"]) != ""){
    $buscar_prision = soloNumeros($_REQUEST["sitioReunion"]);
    $sqlFiltro .= " AND sat_reportes.sitioReunion = ".$buscar_prision."";
}

//
if(isset($_REQUEST["rep_inex"]) && eliminarInvalidos($_REQUEST["rep_inex"]) != ""){
    $tipo = eliminarInvalidos($_REQUEST["rep_inex"]);
    if ($tipo == 2) {
        $sqlFiltro .= " AND sat_reportes.sitioReunion = 0 ";
    }else{
        $sqlFiltro .= " AND sat_reportes.sitioReunion <> 0 ";
    }    
}else{
    $_REQUEST["rep_inex"] = "";
}
//
if(isset($_REQUEST["fechaInicial"]) && eliminarInvalidos($_REQUEST["fechaInicial"]) != ""){
    $fechaInicial = eliminarInvalidos($_REQUEST["fechaInicial"]);
    $sqlFiltro .= " AND sat_reportes.fechaInicio >= '".$fechaInicial."'";
}
//
if(isset($_REQUEST["fechaFinal"]) && eliminarInvalidos($_REQUEST["fechaFinal"]) != ""){
    $fechaFinal = eliminarInvalidos($_REQUEST["fechaFinal"]);
    $sqlFiltro .= " AND sat_reportes.fechaInicio <= '".$fechaFinal."'";
}

//
//Porcentajes de efectividad
//
if(isset($_REQUEST["empresa_paisid"]) && soloNumeros($_REQUEST["empresa_paisid"]) != ""){
    $empresa_paisid = soloNumeros($_REQUEST["empresa_paisid"]);
    $sqlFiltro .= " AND usuario_empresa.empresa_paisid = '".$empresa_paisid."'";
}


if(!isset($_REQUEST["meta1"]) || eliminarInvalidos($_REQUEST["meta1"]) == ""){
    $meta1 = 0;
}else{
    $meta1 = intval($_REQUEST["meta1"]);
}

if(!isset($_REQUEST["meta2"]) || eliminarInvalidos($_REQUEST["meta2"]) == ""){
    $meta2 = 0;
}else{
    $meta2 = intval($_REQUEST["meta2"]);
}


$sqlFiltro_limpio = $sqlFiltro;
$sqlFiltro .= " AND sat_reportes.generacionNumero != 0 ";

/*
*	PIE - Grafica de PIE mostrando cantidad de nuevos prospectos X comercial
*/
$nombreGrafica ="PORCENTAJES DE EFECTIVIDAD DE ECC";
if($_REQUEST["idUsuario"] == 0){
    $nombreGrafica .= " Confraternidad Carcelaria";    
}
$datos = array();
//
//
$sql = "SELECT 
    SUM(asistencia_total) as evangelismo,
    SUM(discipulado) as discipulado,
    SUM(bautizadosPeriodo) as bautizos,
    COUNT(sat_reportes.id) as iglesias
            ";
$sql.=" FROM sat_reportes ";
    $sql .= " LEFT JOIN usuario_empresa AS UE ON UE.idUsuario = sat_reportes.idUsuario
LEFT JOIN tbl_regional_ubicacion AS RU ON RU.reub_id = sat_reportes.sitioReunion 
LEFT JOIN categorias AS C ON C.id = RU.reub_reg_fk
LEFT JOIN categorias AS CA ON CA.id = C.idSec ";
$sql.=" WHERE sat_reportes.rep_tip = 308 ".$sqlFiltro."";
$PSN->query($sql);
//echo $sql;
$num=$PSN->num_rows();
if($num > 0)
{
	while($PSN->next_record())
	{
        //
        $satura_evangelismo = $PSN->f('evangelismo');
        $satura_discipulado = $PSN->f('discipulado');
        $satura_bautizos = $PSN->f('bautizos');
        //
	}
}else{
    $varError = 1;
}


$sql = "SELECT 
    SUM(asistencia_total) as evangelismo,
    SUM(discipulado) as discipulado,
    SUM(bautizadosPeriodo) as bautizos,
    COUNT(sat_reportes.id) as iglesias
            ";
$sql.=" FROM sat_reportes ";
    $sql .= " LEFT JOIN usuario_empresa AS UE ON UE.idUsuario = sat_reportes.idUsuario
LEFT JOIN tbl_regional_ubicacion AS RU ON RU.reub_id = sat_reportes.sitioReunion 
LEFT JOIN categorias AS C ON C.id = RU.reub_reg_fk
LEFT JOIN categorias AS CA ON CA.id = C.idSec ";
$sql.=" WHERE sat_reportes.rep_tip = 308 ".$sqlFiltro."";
//echo $sql;
$PSN->query($sql." AND generacionNumero = 1");

$num=$PSN->num_rows();
if($num > 0)
{
	while($PSN->next_record())
	{
        //
        $satura_iglesias = $PSN->f('iglesias');
        //
	}
}else{
    $varError = 1;
}

$PSN->query($sql." AND generacionNumero = 2");
$num=$PSN->num_rows();
if($num > 0)
{
	while($PSN->next_record())
	{
        //
        $satura_iglesias2 = $PSN->f('iglesias');
        //
	}
}else{
    $varError = 1;
}
$PSN->query($sql." AND generacionNumero = 3");
$num=$PSN->num_rows();
if($num > 0)
{
	while($PSN->next_record())
	{
        //
        $satura_iglesias3 = $PSN->f('iglesias');
        //
	}
}else{
    $varError = 1;
}

$satura_evangelismo_real = 0;
//
//  evangelismo rEAL
$sql = "SELECT 
    SUM(asistencia_total) as evangelismo,
    COUNT(sat_reportes.id) as iglesias
            ";
$sql.=" FROM sat_reportes ";
    $sql .= " LEFT JOIN usuario_empresa AS UE ON UE.idUsuario = sat_reportes.idUsuario
LEFT JOIN tbl_regional_ubicacion AS RU ON RU.reub_id = sat_reportes.sitioReunion 
LEFT JOIN categorias AS C ON C.id = RU.reub_reg_fk
LEFT JOIN categorias AS CA ON CA.id = C.idSec ";
$sql.=" WHERE sat_reportes.rep_tip = 308 ".$sqlFiltro_limpio."";
$PSN->query($sql);
//echo $sql;
$num=$PSN->num_rows();
if($num > 0)
{
	while($PSN->next_record())
	{
        //
        $satura_evangelismo_real = $PSN->f('evangelismo');
        //
	}
}else{
    $varError = 1;
}


$sql = "SELECT
            SUM(sat_reportes.desiciones) as desiciones
            ";
$sql.=" FROM sat_reportes ";
    $sql .= " LEFT JOIN usuario_empresa AS UE ON UE.idUsuario = sat_reportes.idUsuario
LEFT JOIN tbl_regional_ubicacion AS RU ON RU.reub_id = sat_reportes.sitioReunion 
LEFT JOIN categorias AS C ON C.id = RU.reub_reg_fk
LEFT JOIN categorias AS CA ON CA.id = C.idSec";
$sql.=" WHERE sat_reportes.rep_tip = 308 ".$sqlFiltro."";
//
//echo $sql;
$PSN->query($sql);
$num=$PSN->num_rows();
if($num > 0)
{
	while($PSN->next_record())
	{
        //
		$desiciones .= $PSN->f('desiciones');
        //
	}
}else{
    $varError = 1;
}


//
//
$sql = "SELECT * ";
$sql.=" FROM usuario_metas ";
$sql.=" WHERE anho = '".$anho_actual."'";
$sql.=" AND idUsuario = '".soloNumeros($_REQUEST["idUsuario"])."'";
    
$PSN->query($sql);
$num=$PSN->num_rows();
if($num > 0)
{
	while($PSN->next_record())
	{
        //
        $meta_evangelismo = $PSN->f('evangelismo');
        $meta_discipulado = $PSN->f('discipulado');
        $meta_bautizos = $PSN->f('bautizos');
        $meta_iglesias = $PSN->f('iglesias');
        $meta_iglesias2 = $PSN->f('iglesias2');
        $meta_iglesias3 = $PSN->f('iglesias3');
        //
	}
}


$meta_evangelismo = $PSN->f('evangelismo');
$meta_discipulado = $PSN->f('discipulado');
$meta_bautizos = $PSN->f('bautizos');
$meta_iglesias = $PSN->f('iglesias');
$meta_iglesias2 = $PSN->f('iglesias2');
$meta_iglesias3 = $PSN->f('iglesias3');

//

$datos = array();

switch($meta1){
    case 1:
        $meta1_valor = intval($satura_evangelismo_real);
        $datos[] = "['Evangelismo', ".intval($satura_evangelismo_real).", '#F39C12' ]";
        break;
    case 2:
        $meta1_valor = intval($satura_discipulado);
        $datos[] = "['Discipulado', ".intval($satura_discipulado).", '#F39C12' ]";
        break;
    case 3:
        $meta1_valor = intval($desiciones);
        $datos[] = "['Desiciones para Cristo', ".intval($desiciones).", '#F39C12' ]";
        break;
        break;
    case 4:
        $meta1_valor = intval($satura_bautizos);
        $datos[] = "['Bautizos', ".intval($satura_bautizos).", '#F39C12' ]";
        break;
    case 5:
        $meta1_valor = intval($satura_iglesias);
        $datos[] = "['IPG Gen 1', ".intval($satura_iglesias).", '#F39C12' ]";
        break;
    case 6:
        $meta1_valor = intval($satura_iglesias2);
        $datos[] = "['IPG Gen 2', ".intval($satura_iglesias2).", '#F39C12' ]";
        break;
    case 7:
        $meta1_valor = intval($satura_iglesias3);
        $datos[] = "['IPG Gen 3', ".intval($satura_iglesias3).", '#F39C12' ]";
        break;
    case 8:
        $meta1_valor = intval($satura_evangelismo);
        $datos[] = "['Asistencia', ".intval($satura_evangelismo).", '#F39C12' ]";
        break;
    default:
        $datos[] = "['0', 0, '#F39C12' ]";
        break;
}


//
//
//
switch($meta2){
    case 1:
        $datos[] = "['".obtenerPorcentaje($satura_evangelismo_real, $meta1_valor)."% Evangelismo', ".intval($satura_evangelismo_real).", '#2E86C1' ]";
        break;
    case 2:
        $datos[] = "['".obtenerPorcentaje($satura_discipulado, $meta1_valor)."% Discipulado', ".intval($satura_discipulado).", '#2E86C1' ]";
        break;
    case 3:
        $datos[] = "['".obtenerPorcentaje($desiciones, $meta1_valor)."% Desiciones para Cristo', ".intval($desiciones).", '#2E86C1' ]";
        break;
    case 4:
        $datos[] = "['".obtenerPorcentaje($satura_bautizos, $meta1_valor)."% Bautizos', ".intval($satura_bautizos).", '#2E86C1' ]";
        break;
    case 5:
        $datos[] = "['".obtenerPorcentaje($satura_iglesias, $meta1_valor)."% IPG Gen 1', ".intval($satura_iglesias).", '#2E86C1' ]";
        break;
    case 6:
        $datos[] = "['".obtenerPorcentaje($satura_iglesias2, $meta1_valor)."% IPG Gen 2', ".intval($satura_iglesias2).", '#2E86C1' ]";
        break;
    case 7:
        $datos[] = "['".obtenerPorcentaje($satura_iglesias3, $meta1_valor)."% IPG Gen 3', ".intval($satura_iglesias3).", '#2E86C1' ]";
        break;
    case 8:
        $datos[] = "['".obtenerPorcentaje($satura_evangelismo, $meta1_valor)."% Asistencia', ".intval($satura_evangelismo).", '#2E86C1' ]";
        break;
    default:
        $datos[] = "['0', 0, '#2E86C1' ]";
        break;
}



/*
$datos = array();
$datos[] = "['Evangelismo', ".intval($meta_evangelismo).", ".intval($satura_evangelismo_real)." ]";
$datos[] = "['Discipulado', ".intval($meta_discipulado).", ".intval($satura_discipulado)." ]";
$datos[] = "['Bautizos', ".intval($meta_bautizos).", ".intval($satura_bautizos)." ]";
$datos[] = "['IPG Gen 1', ".intval($meta_iglesias).", ".intval($satura_iglesias)." ]";
$datos[] = "['IPG Gen 2', ".intval($meta_iglesias2).", ".intval($satura_iglesias2)." ]";
$datos[] = "['IPG Gen 3', ".intval($meta_iglesias3).", ".intval($satura_iglesias3)." ]";
$datos[] = "['Asistencia', ".intval($meta_iglesias+$meta_iglesias2+$meta_iglesias3).", ".intval($satura_evangelismo)." ]";
*/



if($varError != 1){
    ?>

    <script type="text/javascript">
        google.charts.load("current", {packages:["corechart"]});
        google.charts.setOnLoadCallback(drawChart);
        function drawChart() {
          var data = google.visualization.arrayToDataTable([
            ['Nombre', 'Meta',{ role: "style" }], 
                <?=implode(",", $datos); ?>
          ]);

          var view = new google.visualization.DataView(data);
          view.setColumns([0, 1,
                           { calc: "stringify",
                             sourceColumn: 1,
                             type: "string",
                             role: "annotation" },
                           2]);

          var options = {

            bar: {groupWidth: "95%"},
            legend: { position: "none" },
          };
          var chart = new google.visualization.BarChart(document.getElementById("barchart_values"));
          chart.draw(view, options);
        }
    </script>
<?php } ?>
<div class="container">
    <form action="index.php" method="get" name="form1" class="form-horizontal">
        <input type="hidden" name="doc" value="graphs_013" />
        <div class="row">
            <h3 class="alert alert-info text-center">GRÁFICA DE PORCENTAJES DE EFECTIVIDAD DE ECC</h3>
        </div>
        <div class="cont-tit">
            <div class="hr"><hr></div>
            <div class="tit-cen">
                <h3>FILTRO DE BUSQUEDA</h3>
                <h5>de reportes</h5>
            </div>
            <div class="hr"><hr></div>
        </div>
        <div class="form-group">
            <div class="col-sm-2">
                <strong>Zona:</strong>
                <select name="empresa_sitio_cor" id="zona" class="form-control" onchange="this.form.submit()">
                    <option value="" <?php if($empresa_pd == ""){?>
                                selected="selected" <?php
                            } ?>>Todas la zonas</option>
                    <?php
                    /*
                    *   TRAEMOS LOS TIPOS DE CLIENTE/EMPRESA (15)
                    */
                    $sql = "SELECT * ";
                    $sql.=" FROM categorias ";
                    $sql.=" WHERE idSec = 39 ";
                    if ($_SESSION["id_zona"]!="" && $_SESSION["id_zona"]!=0) {
                        $sql.=" AND id = ".$_SESSION["id_zona"];
                    }
                    $sql .= " ORDER BY descripcion ASC ";

                    $PSN3->query($sql);
                    $numero=$PSN3->num_rows();
                    if($numero > 0){
                        while($PSN3->next_record()){
                            ?><option value="<?=$PSN3->f('id'); ?>" <?php
                            if($buscar_zona == $PSN3->f('id')){?>
                                selected="selected" <?php
                            }
                            ?>><?=$PSN3->f('descripcion'); ?></option><?php
                        }
                    }?>
                </select>
            </div>
            <div class="col-sm-2">
                <strong>Regional:</strong>
                <select  name="empresa_pd" id="regional" class="form-control" onchange="this.form.submit()">
                    <?php echo($zona == ""  && $_SESSION["perfil"]!=163 && $_SESSION["perfil"]!=162 )?'<option value="" selected >Todas la regionales</option>':"";
                    $sql = "SELECT C.id, C.descripcion AS regional, CA.descripcion AS zona FROM categorias AS C";
                    $sql.=" LEFT JOIN categorias AS CA ON CA.id = C.idSec
                    WHERE CA.idSec = 39 ";
                    if (!empty($buscar_zona)) {
                        $sql.=" AND CA.id = ".$buscar_zona;
                    }
                    if ($_SESSION["perfil"]!=167) {
                        if ($_SESSION["empresa_pd"]!="" && $_SESSION["empresa_pd"]!=0) {
                            $sql.=" AND C.id = ".$_SESSION["empresa_pd"];
                        }
                    }
                    
                    $PSN2->query($sql); 
                    echo $sql;
                    $numero=$PSN2->num_rows();
                    if($numero > 0){
                        while($PSN2->next_record()){?>
                            <option value="<?=$PSN2->f('id'); ?>" <?php
                            if($buscar_regional == $PSN2->f('id')){
                                ?>selected="selected"<?php
                            }
                            ?> ><?=$PSN2->f('regional'); ?></option><?php
                        }
                    }
                    ?>
                </select>                    
            </div>
            <div class="col-sm-2">
                <strong>Miembro de la regional:</strong><?php
            ?><select name="idUsuario" onchange="this.form.submit()" class="form-control">
            <?php
            if($_SESSION["perfil"] != 163){
                ?><option value="">Ver todos</option><?php
            }

            /*
            *   TRAEMOS LOS USUARIOS
            */
            $sql = "SELECT * ";
            $sql.=" FROM usuario AS U
            LEFT JOIN usuario_empresa AS UE ON UE.idUsuario = U.id";
            $sql.=" WHERE U.tipo IN (162, 163, 167) ";
            if($_SESSION["perfil"] == 163){
                $sql.=" AND U.id = '".$_SESSION["id"]."'";
            }
            if (!empty($buscar_regional)) {
                $sql.=" AND UE.empresa_pd = ".$buscar_regional." ";
            }
            $sql.=" ORDER BY U.nombre asc";

            $PSN2->query($sql);
            $numero_coo=$PSN2->num_rows();
            if($numero_coo > 0)
            {
                while($PSN2->next_record())
                {
                    ?><option value="<?=$PSN2->f('id'); ?>" <?php
                    if($buscar_idUsuario == $PSN2->f('id'))
                    {
                        ?>selected="selected"<?php
                    }
                    ?>><?=$PSN2->f('nombre'); ?></option><?php
                }
            }
            ?></select>
            </div>
            <div class="col-sm-3">
                <strong>Prisión:</strong>
                <select name="sitioReunion" id="rep_carcel" class="form-control" onchange="this.form.submit()">
                    <?php
                    /*
                    *   TRAEMOS LOS TIPOS DE SERVICIOS QUE PRESTA (25)
                    */
                    if ($_SESSION['empresa_pd'] != "") {
                        echo '<option value="">Sin especificar</option>';
                        $sql = "SELECT * ";
                        $sql.=" FROM tbl_regional_ubicacion ";
                        if(!empty($buscar_regional)){
                            $sql.=" WHERE reub_reg_fk = ".$buscar_regional;
                        }
                        $sql.=" ORDER BY reub_reg_fk asc";

                        $PSN2->query($sql);
                        $numero_pri=$PSN2->num_rows();
                        if($numero_pri > 0){
                            while($PSN2->next_record()){
                                ?><option value="<?=$PSN2->f('reub_id'); ?>" <?php
                                if($buscar_prision == $PSN2->f('reub_id'))
                                {
                                    ?>selected="selected"<?php
                                }
                                ?>><?=$PSN2->f('reub_nom'); ?></option><?php
                            }
                        }
                    }else{
                        echo '<option value="">Sin regional asignada</option>';
                    }
                    ?>
                </select>
            </div>
            <div class="col-sm-2">
                <strong>Tipo:</strong>
                <select name="rep_inex" class="form-control" onchange="this.form.submit()">
                    <option value="">Intramuros / Extramuros</option>
                    <option value="1" <?php echo($_REQUEST["rep_inex"] == 1)?'selected="selected"':""; ?>>Intramuros</option>
                    <option value="2" <?php echo($_REQUEST["rep_inex"] == 2)?'selected="selected"':""; ?>>Extramuros</option>
                </select>
            </div>
            <div class="col-sm-2">
                <strong>Fecha Inicial:</strong>
                <input type="date" name="fechaInicial" id="fechaInicial" value="<?=$fechaInicial; ?>" class="form-control" />
            </div>
            <div class="col-sm-2">
                <strong>Fecha Final:</strong>
                <input type="date" name="fechaFinal" id="fechaFinal" value="<?=$fechaFinal; ?>" class="form-control" />
            </div>
            <div class="col-sm-2">
                <strong>Dato 1:</strong>
                <select name="meta1" id="meta1" class="form-control">
                    <option value="0" <?php if($meta1 == 0){ ?>selected<?php } ?>>Sin especificar</option>
                    <option value="8" <?php if($meta1 == 8){ ?>selected<?php } ?>>Asistencia</option>
                    <option value="2" <?php if($meta1 == 2){ ?>selected<?php } ?>>Discipulado</option>
                    <option value="3" <?php if($meta1 == 3){ ?>selected<?php } ?>>Decisiones para Cristo</option>
                    <option value="4" <?php if($meta1 == 4){ ?>selected<?php } ?>>Bautizos</option>
                </select>
            </div>
            <div class="col-sm-2">
                <strong>Dato 2:</strong>
                <select name="meta2" id="meta2" class="form-control">
                    <option value="0" <?php if($meta2 == 0){ ?>selected<?php } ?>>Sin especificar</option>
                    <option value="8" <?php if($meta2 == 8){ ?>selected<?php } ?>>Asistencia</option>
                    <option value="2" <?php if($meta2 == 2){ ?>selected<?php } ?>>Discipulado</option>
                    <option value="3" <?php if($meta2 == 3){ ?>selected<?php } ?>>Decisiones para Cristo</option>
                    <option value="4" <?php if($meta2 == 4){ ?>selected<?php } ?>>Bautizos</option>   
                </select>
            </div>
            <div class="col-sm-2"><br>
                <input type="submit" value="Buscar" class="btn btn-success" style="float:center" />
            </div>
        </div>
    </form>
<?php
/*
*    
*/
if($varError == 1){
  ?><div class="container">
        <div class="row">
            <h5 class="alert alert-warning text-center">No se ha encontrado ningun registro para el rango de fechas seleccionado.</h5>
        </div>
    </div><?php  
}

if($varError != 1){?>
    <div class="row">
        <div class="cont-tit">
            <div class="hr"><hr></div>
            <div class="tit-cen">
                <h3 class="text-center">RESULTADOS DE BUSQUEDA</h3>
                <h5><?=$totalProspectos; ?> Registros encontrados</h5>
            </div>
            <div class="hr"><hr></div>
        </div>
         <div class="contenedor-flex content-grafic fl-sard">
            <div class="cont-resu fl-sard" id="pde">
                <div class="resu-item bck-col-3">
                    <div class="item-ico">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="item-con">
                        <div class="item-text">
                            <h2><?php 
                                switch($meta1){
                                    case 2:
                                        $meta1_valor = intval($satura_discipulado);
                                        echo intval($satura_discipulado);
                                        break;
                                    case 3:
                                        $meta1_valor = intval($desiciones);
                                        echo intval($desiciones);
                                        break;
                                        break;
                                    case 4:
                                        $meta1_valor = intval($satura_bautizos);
                                        echo intval($satura_bautizos);
                                        break;

                                    case 8:
                                        $meta1_valor = intval($satura_evangelismo);
                                        echo intval($satura_evangelismo);
                                        break;
                                    default:
                                        echo  "0.0";
                                        break;
                                }
                                 ?></h2>
                            <p><?php 
                                switch($meta1){
                                    case 2:
                                        echo "Discipulado";
                                        break;
                                    case 3:
                                        echo "Desiciones para Cristo";
                                        break;
                                    case 4:
                                        echo "Bautizos";
                                        break;
                                    case 8:
                                        echo "Asistencia";
                                        break;
                                    default:
                                        echo "Seleccione";
                                        break;
                                }
                                 ?></p>
                        </div>
                        
                    </div>
                </div>
                <div class="resu-item bck-col-2">
                    <div class="item-ico">
                        <i class="fas fa-percent"></i>
                    </div>
                    <div class="item-con">
                        <div class="item-text">
                            <h2><?php 
                                switch($meta2){
                                    
                                    case 2:
                                        echo obtenerPorcentaje($satura_discipulado, $meta1_valor);
                                        break;
                                    case 3:
                                        echo obtenerPorcentaje($desiciones, $meta1_valor);
                                        break;
                                    case 4:
                                        echo obtenerPorcentaje($satura_bautizos, $meta1_valor);
                                        break;
                                    case 8:
                                        echo obtenerPorcentaje($satura_evangelismo, $meta1_valor);
                                        break;
                                    default:
                                        echo "0.0";
                                        break;
                                }
                                 ?> % </h2>
                            <p><?php 
                                switch($meta2){
                                    
                                    case 2:
                                        echo "Discipulado";
                                        break;
                                    case 3:
                                        echo "Desiciones para Cristo";
                                        break;
                                    case 4:
                                        echo "Bautizos";
                                        break;
                                    case 8:
                                        echo "Asistencia";
                                        break;
                                    default:
                                        echo "00.0";
                                        break;
                                }
                                 ?></p>
                        </div>
                    </div>
                </div>
                <div class="resu-item bck-col-1">
                    <div class="item-ico">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="item-con">
                        <div class="item-text">
                            <h2><?php 
                                switch($meta2){
                                    
                                    case 2:
                                        echo intval($satura_discipulado);
                                        break;
                                    case 3:
                                        echo intval($desiciones);
                                        break;
                                    case 4:
                                        echo intval($satura_bautizos);
                                        break;
                                    case 8:
                                        echo intval($satura_evangelismo);
                                        break;
                                    default:
                                        echo "0.0";
                                        break;
                                }
                                 ?></h2>
                            <p><?php 
                                switch($meta2){
                                    case 2:
                                        echo "Discipulado";
                                        break;
                                    case 3:
                                        echo "Desiciones para Cristo";
                                        break;
                                    case 4:
                                        echo "Bautizos";
                                        break;
                                    case 8:
                                        echo "Asistencia";
                                        break;
                                    default:
                                        echo "Seleccione";
                                        break;
                                }
                                 ?></p>
                        </div>
                    </div> 
                </div>
            </div>
        </div>
        <div id="barchart_values" style="width: 100%; height: 500px;"></div>
    </div>
<?php } ?>
</div>