<?php
/*******************************************
GRAFICA DE CAPACITACION
*******************************************/

$mesesNom = array("No", "Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre");

$PSN = new DBbase_Sql;
$PSN1 = new DBbase_Sql;
$PSN2 = new DBbase_Sql;


$sql= "SELECT idMenu ";
$sql.=" FROM usuarios_menu_graphs ";
$sql.=" WHERE idMenu = 11 
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

if(isset($_REQUEST["empresa_paisid"]) && soloNumeros($_REQUEST["empresa_paisid"]) != ""){
    $empresa_paisid = soloNumeros($_REQUEST["empresa_paisid"]);
    $sqlFiltro .= " AND usuario_empresa.empresa_paisid = '".$empresa_paisid."'";
}


/*
*   PIE - Grafica de PIE mostrando cantidad de nuevos prospectos X comercial
*/
$nombreGrafica = "GRAN CELEBRACIÓN";
$datos = array();
//
$sql = "SELECT 
            COUNT(sat_reportes.id) as conteo, 
            SUM(sat_reportes.asistencia_total) as asistencia_total,
                SUM(sat_reportes.asistencia_hom) as asistencia_hom,
                SUM(sat_reportes.asistencia_muj) as asistencia_muj,
                SUM(sat_reportes.asistencia_jov) as asistencia_jov,
                SUM(sat_reportes.asistencia_nin) as asistencia_nin
            
            ";
$sql.=" FROM sat_reportes ";
$sql .= " LEFT JOIN usuario_empresa ON usuario_empresa.idUsuario = sat_reportes.idUsuario";
$sql.=" WHERE sat_reportes.generacionNumero = 8 ".$sqlFiltro."";
//
$datosArr[] = '["Tipo", "Cantidad"]';
$datosArr2[] = '["Tipo", "Cantidad"]';
//
$PSN->query($sql);
$num=$PSN->num_rows();
if($num > 0)
{
    while($PSN->next_record())
    {
        //
        $asistencia = intval($PSN->f('asistencia_total'));
        $conteo = intval($PSN->f('conteo'));
        $datosArr[] = '["Personas alcanzadas '.$asistencia.'", '.$asistencia.']';
        $datosArr[] = '["Actividades de gran celebración '.$conteo.'", '.$conteo.']';
        //
        
        $datosArr2[] = '["Hombres '.intval($PSN->f('asistencia_hom')).'", '.intval($PSN->f('asistencia_hom')).']';
        $datosArr2[] = '["Mujeres '.intval($PSN->f('asistencia_muj')).'", '.intval($PSN->f('asistencia_muj')).']';
        $datosArr2[] = '["Jóvenes '.intval($PSN->f('asistencia_jov')).'", '.intval($PSN->f('asistencia_jov')).']';
        $datosArr2[] = '["Niños '.intval($PSN->f('asistencia_nin')).'", '.intval($PSN->f('asistencia_nin')).']';
        
    }
}else{
    $varError = 1;
}
//
//
if($varError != 1){
    ?><script type="text/javascript">
      google.charts.load("current", {packages:["corechart", "treemap"]});
      google.charts.setOnLoadCallback(drawChart);
      function drawChart() {
          var data = google.visualization.arrayToDataTable([
                <?=implode(",", $datosArr); ?>
            ]);
          
          
          

            var options = {
                animation:{
                    "startup": true,
                    duration: 2000,
                    easing: 'out'
                },
                colors: ['#016601', '#00FBFF'],
                legend: { position: 'none' }
            };

            var chart = new google.visualization.ColumnChart(document.getElementById('donutchart'));
            chart.draw(data, options);  //ColumnChart
          
          
          
            var data = google.visualization.arrayToDataTable([
                <?=implode(",", $datosArr2); ?>
            ]);

            var options = {
                is3D: true,
                sliceVisibilityThreshold: 0,
                chartArea: {
                    width: '94%'
                },
                pieHole: 0.4,
                width: '100%',
                colors: ['#3D38B8', '#FC0BFB', '#8778ED', '#8C448C']
            };
            //
            var chart = new google.visualization.PieChart(document.getElementById('donutchart2'));
            chart.draw(data, options);  //PIE
          
          
      }
    </script><?php
}
//
?>
<div class="container">
<form action="index.php" method="get" name="form1" class="form-horizontal">
    <input type="hidden" name="doc" value="graphs_014" />
    <div>
        <h2 class="alert alert-info text-center">GRÁFICA DE GRAN CELEBRACIÓN</h2>
    </div>
    <div class="cont-tit">
        <div class="hr"><hr></div>
        <div class="tit-cen">
            <h3>FILTRO DE BUSQUEDA</h3>
            <h5>de la gráfica</h5>
        </div>
        <div class="hr"><hr></div>
    </div>
    <div class="form-group ">
        <div class="col-sm-3">
            <strong>Miembro de la regional:</strong>
            <select name="idUsuario" onchange="this.form.submit()" class="form-control">
                <?php
                if($_SESSION["perfil"] != 163){
                    ?><option value="">Ver todos</option><?php
                }
        
                /*
                *   TRAEMOS LOS USUARIOS
                */
                $sql = "SELECT * ";
                $sql.=" FROM usuario ";
                $sql.=" WHERE tipo IN (162, 163, 167) ";
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
                }?>
            </select>
        </div>
        <?php
        if($_SESSION["perfil"] != 163){
            ?>
            <div class="col-sm-3">
                <strong>Nombre del pais:</strong>
                <select name="empresa_paisid" class="form-control">
                    <option value="">Sin especificar</option>
                    <?php
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
                            ?><option value="<?=$PSN2->f('id'); ?>" <?php
                            if($empresa_paisid == $PSN2->f('id'))
                            {
                                ?>selected="selected"<?php
                            }
                            ?>><?=$PSN2->f('descripcion'); ?></option><?php
                        }
                    }
                    ?>
                </select>
            </div>
        <?php }?>
        <div class="col-sm-2">
            <strong>Fecha Inicial:</strong>
            <input type="date" name="fechaInicial" id="fechaInicial" value="<?=$busquedaFechaIni; ?>" class="form-control" />
        </div>
        <div class="col-sm-2">
            <strong>Fecha Final:</strong>
            <input type="date" name="fechaFinal" id="fechaFinal" value="<?=$busquedaFechaFin; ?>" class="form-control" />
        </div>
        <div class="col-sm-1"><br>
            <input type="submit" value="Buscar" class="btn btn-success" />
        </div>
    </div>
</form>
<div class="cont-tit">
            <div class="hr"><hr></div>
            <div class="tit-cen">
                <h3 class="text-center">RESULTADOS DEL FILTRO</h3>
                <h5>Gráficas de gran celebración</h5>
            </div>
            <div class="hr"><hr></div>
        </div>
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
      <div class="col-md-6 text-center">
        <h4 class="alert alert-warning"><?=$nombreGrafica; ?></h4>
        <div id="donutchart" class="chart"></div>
      </div>
    <!-- GRAFICA //-->
      <div class="col-md-6 text-center">
        <h4 class="alert alert-warning"><?=$nombreGrafica; ?> - DETALLADO</h4>
        <div id="donutchart2" class="chart"></div>
      </div>

    </div>
    
    <?php
}
?>
</div>