<?php
/*******************************************
En un rango de fechas.
Cuantos prospecto hay desde
el día 1 y cuantos el día
último. De cada
COMERCIAL
La diferencia es = nuevos
prospectos, en un lapso de
(3– 6) cortes mostrando el
avance. (éste gráfica es
animado)
https://developers.google.co
m/chart/interactive/docs/animation

ID MENU = 2
*******************************************/

$mesesNom = array("No", "Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre");

$PSN = new DBbase_Sql;
$PSN1 = new DBbase_Sql;
$PSN2 = new DBbase_Sql;


$sql= "SELECT idMenu ";
$sql.=" FROM usuarios_menu_graphs ";
$sql.=" WHERE idMenu = 2 
AND idUsuario = '".$_SESSION["id"]."'";
$PSN->query($sql);
if($PSN->num_rows() == 0)
{
    die("NO esta autorizado a ver esta grafica.");
}

/*
*   FILTROS DE FECHAS.
*/
$busquedaFechaIni = date("2021-02-01");
if(isset($_REQUEST["fechaInicial"]) && soloNumeros($_REQUEST["fechaInicial"]) != ""){
	$busquedaFechaIni = eliminarInvalidos($_REQUEST["fechaInicial"]);
}else{
    $_REQUEST["fechaInicial"] = $busquedaFechaIni;
}
//
//
//$busquedaFechaFin = $siguiente_anho."-01-31";
$busquedaFechaFin = date("Y-m-d");
if(isset($_REQUEST["fechaFinal"]) && soloNumeros($_REQUEST["fechaFinal"]) != ""){
	$busquedaFechaFin = eliminarInvalidos($_REQUEST["fechaFinal"]);
}else{
    $_REQUEST["fechaFinal"] = $busquedaFechaFin;
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
/*
*   FILTROS DE FECHAS.
*/
if(isset($_REQUEST["fechaInicial"]) && soloNumeros($_REQUEST["fechaInicial"]) != ""){
    $busquedaFechaIni = eliminarInvalidos($_REQUEST["fechaInicial"]);
}
//
if(isset($_REQUEST["fechaFinal"]) && soloNumeros($_REQUEST["fechaFinal"]) != ""){
    $busquedaFechaFin = eliminarInvalidos($_REQUEST["fechaFinal"]);
}

if(isset($_REQUEST["idUsuario"]) && soloNumeros($_REQUEST["idUsuario"]) != ""){
    $buscar_idUsuario = soloNumeros($_REQUEST["idUsuario"]);
    $sqlFiltro .= " AND sat_reportes.idUsuario = '".$buscar_idUsuario."'";
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


$sqlFiltro .= " AND sat_reportes.generacionNumero != 0";
$sqlFiltro .= " AND sat_reportes.generacionNumero != 77";
$sqlFiltro .= " AND sat_reportes.generacionNumero != 8";

/*
*	PIE - Grafica de PIE mostrando cantidad de nuevos prospectos X comercial
*/
$nombreGrafica = "ASISTENCIA MENSUAL";
$datos = array();
//
//
$sql = "SELECT
            COUNT(sat_reportes.idUsuario) as conteoUsuarios,

            SUM(CASE WHEN sat_reportes.generacionNumero > 0 THEN 1 ELSE 0 END) AS gruposConteo,

            SUM(sat_reportes.asistencia_total) as asistencia_total,
            SUM(sat_reportes.asistencia_hom) as asistencia_hom,
            SUM(sat_reportes.asistencia_muj) as asistencia_muj,
            SUM(sat_reportes.asistencia_jov) as asistencia_jov,
            SUM(sat_reportes.asistencia_nin) as asistencia_nin,

            SUM(sat_reportes.bautizados) as bautizados,
            SUM(sat_reportes.bautizadosPeriodo) as bautizadosPeriodo,
            SUM(sat_reportes.discipulado) as discipulado,
            SUM(sat_reportes.desiciones) as desiciones,
            SUM(sat_reportes.preparandose) as preparandose,
            SUM(sat_reportes.iglesias_reconocidas) as iglesias_reconocidas,
            SUM(sat_reportes.bautizados) as bautizados,

            MONTH(sat_reportes.fechaReporte) as mes,
            YEAR(sat_reportes.fechaReporte) as anho,
            usuario.nombre
            ";
$sql.=" FROM sat_reportes ";
    $sql .= " LEFT JOIN usuario ON usuario.id = sat_reportes.idUsuario";
$sql.=" WHERE 1 ".$sqlFiltro." GROUP BY anho, mes";
//
//echo $sql;
$PSN->query($sql);
$num=$PSN->num_rows();
if($num > 0)
{
    while($PSN->next_record())
    {
        //
        $nombresComercial[$PSN->f('anho')][$PSN->f('mes')] = $PSN->f('asistencia_total');
        $totalProspectos += $PSN->f('asistencia_total');
        //        
    }
}else{
    $varError = 1;
}
//
//
//
$mesInicial = date("n", strtotime($busquedaFechaIni));
$anhoInicial = date("Y", strtotime($busquedaFechaIni));
//
$anhoFinal = date("Y", strtotime($busquedaFechaFin));
$mesFinal = date("n", strtotime($busquedaFechaFin));
//
$datosArr[] = '["Meses", "Asistencia"]';
//
for($tempAnho=$anhoInicial;$tempAnho<=$anhoFinal;$tempAnho++){
    //
    if($tempAnho == $anhoFinal){
        $mesFinalTemp = $mesFinal;
    }else{
        $mesFinalTemp = 12;
    }
    //
    for($tempMes=$mesInicial;$tempMes<=$mesFinalTemp;$tempMes++){
        $mes_actual = $mesesNom[$tempMes];
        //
        //
        $valor = array();
            //
            $tempValor = $nombresComercial[$tempAnho][$tempMes];

            if($tempValor > 0){
                $valor[] = $tempValor;
            }else{
                $valor[] = 0;
            }
            //            
            $contador++;
        //
        $stringValores = implode(",", $valor);
        $datosArr[] = '["'.$mes_actual.'", '.$stringValores.']';
    }
}
//
if($varError != 1){
    ?><script type="text/javascript">
      google.charts.load("current", {packages:["corechart", "treemap"]});
      google.charts.setOnLoadCallback(drawChart);
      function drawChart() {
            //GRAFICA DE LINE CHART DE NUEVOS PROSPECTOS- //['Clase', 'Cantidad', { role: 'style' }], //
              var data = google.visualization.arrayToDataTable([
                <?=implode(",", $datosArr); ?>
            ]);

            var options = {
                animation:{
                    "startup": true,
                    duration: 2000,
                    easing: 'out'
                },
                legend: { position: 'bottom' }
            };

            var chart = new google.visualization.ColumnChart(document.getElementById('donutchart'));
            chart.draw(data, options);	//ColumnChart
      }
    </script><?php
}
//
?>
<div class="container">
<form action="index.php" method="get" name="form1" class="form-horizontal">
<input type="hidden" name="doc" value="graphs_003" />

<div class="form-group">
    <center><h2 class="text-center well col-sm-12">.FILTROS DE BUSQUEDA - ASISTENCIA MENSUAL.</h2></center>
</div>
    <div class="form-group">
    <div class="form-group">
            <label class="control-label col-sm-2" for="idUsuario"><strong>Coordinador de prisión:</strong></label>
            <div class="col-sm-4"><?php
            ?><select name="idUsuario" onchange="this.form.submit()" class="form-control">
            <?php
            if($_SESSION["perfil"] != 163){
                ?><option value="">Ver todos</option><?php
            }
    
            /*
            *	TRAEMOS LOS USUARIOS
            */
            $sql = "SELECT * ";
            $sql.=" FROM usuario ";
            $sql.=" WHERE tipo IN (162, 163) ";
            if($_SESSION["perfil"] == 163){
                $sql.=" AND id = '".$_SESSION["id"]."'";
            }
            $sql.=" ORDER BY nombre asc";

            $PSN2->query($sql);
            $numero=$PSN2->num_rows();
            if($numero > 0)
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
            
            <!--<label class="control-label col-sm-2" for="idGrupoMadre"><strong>Grupo Madre:</strong></label>
            <div class="col-sm-4"><select name="idGrupoMadre" onchange="this.form.submit()" class="form-control">
                <option value="">Ver todos</option><?php
                //
                //	TRAEMOS LOS GRUPOS MADRES
                //
                $sql = "SELECT id, nombre ";
                $sql.=" FROM sat_grupos ";
                if($_SESSION["perfil"] == 163){
                    $sql.=" WHERE idUsuario = '".$_SESSION["id"]."'";
                }
                $sql.=" ORDER BY nombre asc";
                //
                $PSN2->query($sql);
                $numero=$PSN2->num_rows();
                if($numero > 0)
                {
                    while($PSN2->next_record())
                    {
                        ?><option value="<?=$PSN2->f('id'); ?>" <?php
                        if($buscar_idGrupoMadre == $PSN2->f('id')){
                            ?>selected="selected"<?php
                        }
                        ?></optio><?=$PSN2->f('nombre'); ?></option><?php
                    }
                }
                ?></select></div>//-->
        
        </div>

        <div class="form-group">
            <label class="control-label col-sm-2" for="fechaInicial"><strong>Fecha Inicial:</strong></label>
            <div class="col-sm-4"><input type="date" name="fechaInicial" id="fechaInicial" value="<?=$busquedaFechaIni; ?>" class="form-control" /></div>

            <label class="control-label col-sm-2" for="fechaFinal"><strong>Fecha Final:</strong></label>
            <div class="col-sm-4"><input type="date" name="fechaFinal" id="fechaFinal" value="<?=$busquedaFechaFin; ?>" class="form-control" /></div>
        </div>

    <div class="row text-center">
        <div class="col-sm-12"><center><input type="submit" value="Buscar" class="btn btn-success" style="float:center" /></center></div>
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
    </div><?  
}


if($varError != 1){
  ?>
    <div class="row">
    <!-- GRAFICA //-->
      <div class="col-md-12 text-center">
        <h2><?=$nombreGrafica; ?></h2>
         <p>Total: <?=$totalProspectos; ?></p>
        <div id="donutchart" class="chart"></div>
      </div>

    </div>
    <?php
}
?>
</div>