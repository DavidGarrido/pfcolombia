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
//
$siguiente_anho = date("Y", strtotime("+1 year"));
//$busquedaFechaFin = $siguiente_anho."-01-31";
$busquedaFechaFin = date("Y-m-d");
if(isset($_REQUEST["fechaFinal"]) && soloNumeros($_REQUEST["fechaFinal"]) != ""){
	$busquedaFechaFin = eliminarInvalidos($_REQUEST["fechaFinal"]);

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
    $_REQUEST["fechaInicial"] = $busquedaFechaIni;
}
if(!isset($_REQUEST["fechaFinal"]) || eliminarInvalidos($_REQUEST["fechaFinal"]) == ""){
    $_REQUEST["fechaFinal"] = $busquedaFechaFin;
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

if(isset($_REQUEST["empresa_paisid"]) && soloNumeros($_REQUEST["empresa_paisid"]) != ""){
    $empresa_paisid = soloNumeros($_REQUEST["empresa_paisid"]);
    $sqlFiltro .= " AND usuario_empresa.empresa_paisid = '".$empresa_paisid."'";
}

$sqlFiltro .= " AND sat_reportes.generacionNumero != 0";
$sqlFiltro .= " AND sat_reportes.generacionNumero != 77";
$sqlFiltro .= " AND sat_reportes.generacionNumero != 8";

/*
*/
$nombreGrafica ="GRUPOS ACTIVOS VS. INACTIVOS";
$datos = array();
//
//
$sql = "SELECT
            sat_reportes.inactivo,
            COUNT(sat_reportes.id) AS gruposConteo
            ";
$sql.=" FROM sat_reportes ";
    $sql .= " LEFT JOIN usuario_empresa ON usuario_empresa.idUsuario = sat_reportes.idUsuario";
$sql.=" WHERE 1 ".$sqlFiltro."";
$sql.=" GROUP BY sat_reportes.inactivo";
//
//echo $sql;
$PSN->query($sql);
$num=$PSN->num_rows();
if($num > 0)
{
	while($PSN->next_record())
	{
        //
        if($PSN->f('inactivo') == 1){
            $datos[] = "['INACTIVOS', ".$PSN->f('gruposConteo')."]";
        }
        else{
            $datos[] = "['ACTIVOS', ".$PSN->f('gruposConteo')."]";
        }
		$totalProspectos += $PSN->f('gruposConteo');
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
                pieHole: 0.4,
                legend: { position: 'bottom' }
                //colors: ['crimson', 'limegreen']
            };


            var chart = new google.visualization.PieChart(document.getElementById('donutchart'));
            chart.draw(data, options);	//PIE
      }
    </script><?php
}
//
?>
<div class="container">
<form action="index.php" method="get" name="form1" class="form-horizontal">
<input type="hidden" name="doc" value="graphs_012" />

<div class="form-group">
    <center><h2 class="text-center well col-sm-12">.FILTROS DE BUSQUEDA - GRAFICA DE ACTIVOS VS. INACTIVOS.</h2></center>
</div>
    <div class="form-group">
            <h2 class="text-center well">.FILTROS DE BUSQUEDA - REPORTES.</h2>
        </div>

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
            
                   <?php
        if($_SESSION["perfil"] != 163){
            ?> <label class="control-label col-sm-2" for="empresa_paisid"><strong>Nombre del pais:</strong></label>
            <div class="col-sm-4"><select name="empresa_paisid" class="form-control">                    
            <option value="">Sin especificar</option>
            <?php
            /*
            *	TRAEMOS LOS TIPOS DE CLIENTE/EMPRESA (15)
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
                    ?><option value="<?=$PSN2->f('id'); ?>" <?php
                    if($empresa_paisid == $PSN2->f('id'))
                    {
                        ?>selected="selected"<?php
                    }
                    ?>><?=$PSN2->f('descripcion'); ?></option><?php
                }
            }
            ?>
            </select></div><?php
        }
        ?>

        
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