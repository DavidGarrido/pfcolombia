<?php
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
$busquedaFechaIni = date("2021-02-01");
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
$nombreGrafica ="RED DE IGLESIAS EN CASA";
$datos = array();
//
//
$sql = "SELECT
            sat_reportes.generacionNumero,
            COUNT(sat_reportes.id) as conteo,

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
            SUM(sat_reportes.iglesias_reconocidas) as iglesias_reconocidas
            ";
$sql.=" FROM sat_reportes ";
$sql .= " LEFT JOIN sat_grupos ON sat_grupos.id = sat_reportes.idGrupoMadre";
//$sql .= " LEFT JOIN usuario_empresa ON usuario_empresa.idUsuario = sat_reportes.idUsuario";
//
$sql.=" WHERE sat_grupos.satura = 1 ".$sqlFiltro." GROUP BY sat_reportes.generacionNumero";


//
$PSN->query($sql);
$num=$PSN->num_rows();
if($num > 0)
{
	while($PSN->next_record())
	{
        //
        $texto .=  "| GENERACIÓN ".$PSN->f('generacionNumero').": ".$PSN->f('conteo')." ";
		$datos[] = "['GENERACIÓN ".$PSN->f('generacionNumero')."', ".$PSN->f('conteo')."]";
		$totalProspectos += $PSN->f('conteo');
        //
	}
}else{
    $varError = 1;
}


if($varError != 1){
    ?><script type="text/javascript">
      google.charts.load("current", {packages:["corechart", "treemap"]});
      google.charts.setOnLoadCallback(drawChart);
      function drawChart() {
            //GRAFICA DE PIE DE NUEVOS PROSPECTOS- //['Clase', 'Cantidad', { role: 'style' }], //
              var data = google.visualization.arrayToDataTable([
                ['Clase', 'Cantidad'], 
                <?=implode(",", $datos); ?>
            ]);

            var options = {
                is3D: true,
                sliceVisibilityThreshold: 0,
                chartArea: {
                  // leave room for y-axis labels
                  width: '94%'
                },
                legend: { position: 'bottom' },
                width: '100%'
                //colors: ['crimson', 'limegreen']
            };
            //
            var chart = new google.visualization.PieChart(document.getElementById('donutchart'));
            chart.draw(data, options);	//PIE
      }
    </script><?
}
//
?>
<div class="container">
<form action="index.php" method="get" name="form1" class="form-horizontal">
<input type="hidden" name="doc" value="graphs_008" />

<div class="form-group">
    <center><h2 class="text-center well col-sm-12">.FILTROS DE BUSQUEDA - RED SATURA IGLESIAS EN CASA.</h2></center>
</div>
    <div class="form-group">
            <h2 class="text-center well">.FILTROS DE BUSQUEDA - REPORTES.</h2>
        </div>

        <div class="form-group">
            <label class="control-label col-sm-2" for="idUsuario"><strong>Coordinador de prisión:</strong></label>
            <div class="col-sm-4"><?
            ?><select name="idUsuario" onchange="this.form.submit()" class="form-control"><?
            //
            //
            if($_SESSION["perfil"] != 163){
                ?><option value="">Ver todos</option><?
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
                    ?><option value="<?=$PSN2->f('id'); ?>" <?
                    if($buscar_idUsuario == $PSN2->f('id'))
                    {
                        ?>selected="selected"<?
                    }
                    ?>><?=$PSN2->f('nombre'); ?></option><?
                }
            }
            ?></select>
            </div>
            
            <!--<label class="control-label col-sm-2" for="idUsuario"><strong>Excluir Red iglesias en casa de satura:</strong></label>
            <div class="col-sm-4"><input type="checkbox" name="excluir" value="1" class="form-control" <?
                                         if($buscar_excluir == 1){
                                             ?>checked="checked"<?
                                         }
                                         ?> onClick="this.form.submit()"></div>//-->
            
            
            
            
            <!--<label class="control-label col-sm-2" for="idGrupoMadre"><strong>Grupo Madre:</strong></label>
            <div class="col-sm-4"><select name="idGrupoMadre" onchange="this.form.submit()" class="form-control">
                <option value="">Ver todos</option><?
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
                        ?><option value="<?=$PSN2->f('id'); ?>" <?
                        if($buscar_idGrupoMadre == $PSN2->f('id')){
                            ?>selected="selected"<?
                        }
                        ?></optio><?=$PSN2->f('nombre'); ?></option><?
                    }
                }
                ?></select></div>//-->
        
        </div>

        <div class="form-group">
            <label class="control-label col-sm-2" for="fechaInicial"><strong>Fecha Inicial:</strong></label>
            <div class="col-sm-4"><input type="date" name="fechaInicial" id="fechaInicial" value="<?=$fechaInicial; ?>" class="form-control" /></div>

            <label class="control-label col-sm-2" for="fechaFinal"><strong>Fecha Final:</strong></label>
            <div class="col-sm-4"><input type="date" name="fechaFinal" id="fechaFinal" value="<?=$fechaFinal; ?>" class="form-control" /></div>
        </div>
        
    <div class="row text-center">
        <div class="col-sm-12"><center><input type="submit" value="Buscar" class="btn btn-success" style="float:center" /></center></div>
    </div>
</form>

<?
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
         <h4><?=$texto; ?><br/ >Total: <?=$totalProspectos; ?></h4>
          <div id="donutchart" class="chart"></div>        
      </div>
    </div>
    <?
}
?>
</div>