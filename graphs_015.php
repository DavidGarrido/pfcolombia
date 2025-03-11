<?php
/*******************************************
GRAFICA DE PLAN MAESTRO
*******************************************/
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

function obtenerPorcentaje($cantidad, $total) {
    $porcentaje = ((float)$cantidad * 100) / $total; // Regla de tres
    $porcentaje = round($porcentaje, 2);  // Quitar los decimales
    return $porcentaje;
}

function cargarDatosGrafica($anio = null,$iduser = null){
    if ($anio == null) {
        $fechInicio = "2021-02-01";
        $fechFin = date("Y-m-d");
    }else{
        $fechInicio = $anio."-02-01";
        $fechFin = ($anio+1)."-01-31";
    }
    $sqlWhere = "";
    $sqlUser = "";
    $datosView = array();
    //echo $_SESSION["perfil"];
    if(!empty($iduser)){
        $buscar_idUsuario = soloNumeros($iduser);
        $sqlUser .= "sat_reportes.idUsuario = '".$buscar_idUsuario."' AND ";
    }
    $sqlWhere .= " AND sat_reportes.fechaReporte >= '".$fechInicio."'";
    $sqlWhere .= " AND sat_reportes.fechaReporte <= '".$fechFin."'";
    $sqlFiltro_limpio = $sqlWhere;
    $sqlWhere .= " AND sat_reportes.generacionNumero != 0 AND sat_reportes.generacionNumero != 77 AND sat_reportes.generacionNumero != 8";
    //echo $sqlWhere;
    $GRF_DATOS = new DBbase_Sql;

    $sql = "SELECT 
    SUM(asistencia_total) as evangelismo,
    COUNT(id) as iglesias
            ";
    $sql .=" FROM sat_reportes ";
    $sql .= " LEFT JOIN usuario_empresa ON usuario_empresa.idUsuario = sat_reportes.idUsuario";
    $sql.=" WHERE ".$sqlUser."  (sat_reportes.generacionNumero = 77 ".$sqlFiltro_limpio." ) OR (sat_reportes.generacionNumero = 8 ".$sqlFiltro_limpio.") ";
    //echo $sql;
    $GRF_DATOS->query($sql);
    $num=$GRF_DATOS->num_rows();
    if($num > 0){
        while($GRF_DATOS->next_record()){
            $datosView['evangelismo'] = $GRF_DATOS->f('evangelismo');
        }
    }
    
    $sql = "SELECT 
    SUM(asistencia_total) as evangelismo,
    SUM(discipulado) as discipulado,
    SUM(bautizadosPeriodo) as bautizos,
    COUNT(id) as iglesias,
    SUM(desiciones) as desiciones";
    $sql.=" FROM sat_reportes ";
    $sql .= " LEFT JOIN usuario_empresa ON usuario_empresa.idUsuario = sat_reportes.idUsuario";
    $sql.=" WHERE ".$sqlUser." 1 ".$sqlWhere."";
    //echo $sql." ".$sqlWhere;
    //echo $sql;
    $GRF_DATOS->query($sql);
    $num=$GRF_DATOS->num_rows();
    //echo "<h2>".$anio."</h2>";
    if($num > 0){
        while($GRF_DATOS->next_record()){
            $datosView['discipulado'] = $GRF_DATOS->f('discipulado');
            $datosView['bautizos'] = $GRF_DATOS->f('bautizos');
            $datosView['desiciones'] = $GRF_DATOS->f('desiciones');
            $datosView['asistencia'] = $GRF_DATOS->f('evangelismo');
            //echo "Evangelismo: ".$GRF_DATOS->f('evangelismo');
            //echo "<br>Discipulado: ".$GRF_DATOS->f('discipulado');
            //echo "<br>Bautizos: ".$GRF_DATOS->f('bautizos');
        }
    }else{
        $varError = 1;
    }
    $GRF_DATOS->query($sql." AND generacionNumero = 1");
    $num=$GRF_DATOS->num_rows();
    if($num > 0){
        while($GRF_DATOS->next_record()){
            //echo "<br>Generacion 1: ".$GRF_DATOS->f('iglesias');
            $datosView['gen-1'] = $GRF_DATOS->f('iglesias');
        }
    }
    $GRF_DATOS->query($sql." AND generacionNumero = 2");
    $num=$GRF_DATOS->num_rows();
    if($num > 0){
        while($GRF_DATOS->next_record()){
            //echo "<br>Generacion 2: ".$satura_iglesias2 = $GRF_DATOS->f('iglesias');
            $datosView['gen-2'] = $GRF_DATOS->f('iglesias'); 
        }
    }

    $GRF_DATOS->query($sql." AND generacionNumero = 3");
    $num=$GRF_DATOS->num_rows();
    if($num > 0){
        while($GRF_DATOS->next_record()){
            //echo "<br>Generacion 3: ".$GRF_DATOS->f('iglesias');
            $datosView['gen-3'] = $GRF_DATOS->f('iglesias');
        }
    }
    //var_dump($datosView);
    return $datosView;
}
function cargarMetasGrafica($anio = null,$iduser = null){
    $sqlWhere = "";
    $metasView = array();
    $GRF_METAS = new DBbase_Sql;
    if ($anio==null) {
        $sql = "SELECT SUM(evangelismo) AS evangelismo,SUM(discipulado) AS discipulado,SUM(bautizos) AS bautizos,SUM(iglesias) AS iglesias,SUM(iglesias2) AS iglesias2,SUM(iglesias3) AS iglesias3";
        $sql.=" FROM usuario_metas ";
        if($iduser != null){
            $sql.=" WHERE idUsuario = '".$iduser."'";
        }else{
            $sql.=" WHERE idUsuario = 0";
        }
    }else{
        $sql = "SELECT * ";
        $sql.=" FROM usuario_metas ";
        $sql.=" WHERE anho = '".$anio."'";
        if($iduser != null){
            $sql.=" AND idUsuario = '".$iduser."'";
        }else{
            $sql.=" AND idUsuario = 0";
        }
    }
    //echo $sql;
    $GRF_METAS->query($sql);
    $num_GRF=$GRF_METAS->num_rows();
    if($num_GRF > 0){
        while($GRF_METAS->next_record()){
            $metasView['evangelismo'] = $GRF_METAS->f('evangelismo');
            $metasView['discipulado'] = $GRF_METAS->f('discipulado');
            $metasView['bautizos'] = $GRF_METAS->f('bautizos');
            $metasView['desiciones'] = 0;
            $metasView['gen-1'] = $GRF_METAS->f('iglesias');
            $metasView['gen-2'] = $GRF_METAS->f('iglesias2');
            $metasView['gen-3'] = $GRF_METAS->f('iglesias3');
            $metasView['asistencia'] = 0;
        }
    }
    return $metasView;
}

$nombreGrafica ="PLAN MAESTRO";
?>
<div class="container">
<form action="index.php" method="get" name="form1" class="form-horizontal">
    <input type="hidden" name="doc" value="graphs_015" />
    <div class="row">
        <h3 class="alert alert-info text-center">PLAN MAESTRO</h3>
    </div>
    <div class="cont-tit">
        <div class="hr"><hr></div>
        <div class="tit-cen">
            <h3 class="text-center">FILTRO DE BUSQUEDA</h3>
            <h5>de plan maestro</h5>
        </div>
        <div class="hr"><hr></div>
    </div>
    <div class="form-group">
        <div class="col-sm-3"></div>
        <div class="col-sm-3">
            <strong>Miembro de la regional:</strong>
            <select name="idUsuario" onchange="this.form.submit()" class="form-control">
                <?php
                if($_SESSION["perfil"] != 163){
                    ?><option value="">Ver todos</option><?php
                }
        
                /*
                *	TRAEMOS LOS USUARIOS
                */
                $nombre_actual = "";
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
                        if($_REQUEST['idUsuario'] == $PSN2->f('id'))
                        {
                            $nombre_actual = " ".$PSN2->f('nombre');
                            ?>selected="selected"<?php
                        }
                        ?>><?=$PSN2->f('nombre'); ?></option><?php
                    }
                } ?>
            </select>
        </div>
        <?php //if($_SESSION["perfil"] != 163){?>
            <!--<div class="col-sm-3">
                <strong>Nombre del pais:</strong>
                <select name="empresa_paisid" class="form-control">
                    <option value="">Sin especificar</option>
                    <?php
                    /*
                    *	TRAEMOS LOS TIPOS DE CLIENTE/EMPRESA (15)
                    */
                    $pais_seleccionado = "";
                    $sql = "SELECT * ";
                    $sql.=" FROM categorias ";
                    $sql.=" WHERE idSec = 37 ORDER BY descripcion asc";
                    $PSN2->query($sql);
                    $numero=$PSN2->num_rows();
                    if($numero > 0){
                        while($PSN2->next_record()){
                            ?><option value="<?=$PSN2->f('id'); ?>" <?php
                            if($_REQUEST['empresa_paisid'] == $PSN2->f('id')){
                                $pais_seleccionado = " - SELECCIONADO: ".$PSN2->f('descripcion');
                                ?>selected="selected"<?php
                            }
                            ?>><?=$PSN2->f('descripcion'); ?></option><?php
                        }
                    }
                    ?>
                </select>
            </div>
        <?php // } ?>-->
        <div class="col-sm-2">
            <strong>Año:</strong>
            <select name="rep_ani" onchange="this.form.submit()" class="form-control">
                <option value="">Sin especificar</option>
                <?php for ($i=2021; $i <= date('Y'); $i++) { 
                    echo '<option value="'.$i.'"';
                    echo ($i == $_REQUEST['rep_ani'])?'selected':'';
                    echo ' >'.$i.'</option>';
                } ?>
            </select>
        </div>
        <div class="col-sm-1">
            <br><input type="submit" value="Filtrar" class="btn btn-success" />
        </div>
    </div>
</form>

<?php
if($varError == 1){
  ?><div class="container">
        <div class="row">
            <h5 class="alert alert-warning text-center">No se ha encontrado ningun registro para el rango de fechas seleccionado.</h5>
        </div>
    </div><?php  
}

if($varError != 1){
    ?>
    <div class="cont-tit" >
        <div class="hr"><hr></div>
        <div class="tit-cen">
            <h3>RESULTADOS DE BUSQUEDA</h3>
            <h5><?=$nombreGrafica.$nombre_actual.$pais_seleccionado; ?></h5>
        </div>
        <div class="hr"><hr></div>
    </div>
    <?php 
        if (isset($_REQUEST['idUsuario'])) {
           $idU = $_REQUEST['idUsuario'];
        }else{
            $idU = 0;
        }
        if (isset($_REQUEST['rep_ani'])) {
           $ani = $_REQUEST['rep_ani'];
        }else{
            $ani = null;
        }
        //echo $idU." - ".$ani;
        $datfYear = array();
        $datfYear['maestro'] = cargarDatosGrafica($ani,$idU);
        $metfYear = array(); 
        $metfYear['maestro'] = cargarMetasGrafica($ani,$idU);
         ?>
        
        <script type="text/javascript">
            google.charts.load("current", {packages:["corechart", "treemap"]});
            google.charts.setOnLoadCallback(drawChart);
            function drawChart() {
                var data = google.visualization.arrayToDataTable([
                    ['Nombre', 'Meta', 'Datos Plan maestro '], 
                    <?php echo "['".obtenerPorcentaje($datfYear['maestro']['evangelismo'], $metfYear['maestro']['evangelismo'])."%  Evangelismo',".intval($metfYear['maestro']['evangelismo']).",".intval($datfYear['maestro']['evangelismo'])."],";
                    echo "['".obtenerPorcentaje($datfYear['maestro']['discipulado'], $metfYear['maestro']['discipulado'])."% Discipulado',".intval($metfYear['maestro']['discipulado']).",".intval($datfYear['maestro']['discipulado'])."],";
                    /*echo "['".obtenerPorcentaje($datfYear['maestro']['desiciones'], $metfYear['maestro']['desiciones'])."% Desiciones',".intval($metfYear['maestro']['desiciones']).",".intval($datfYear['maestro']['desiciones'])."],";*/
                    echo "['".obtenerPorcentaje($datfYear['maestro']['bautizos'], $metfYear['maestro']['bautizos'])."% Bautizos',".intval($metfYear['maestro']['bautizos']).",".intval($datfYear['maestro']['bautizos'])."],";
                    echo "['".obtenerPorcentaje($datfYear['maestro']['gen-1'], $metfYear['maestro']['gen-1'])."% IPG Generación 1',".intval($metfYear['maestro']['gen-1']).",".intval($datfYear['maestro']['gen-1'])."],";
                    echo "['".obtenerPorcentaje($datfYear['maestro']['gen-2'], $metfYear['maestro']['gen-2'])."% IPG Generación 2',".intval($metfYear['maestro']['gen-2']).",".intval($datfYear['maestro']['gen-2'])."],";
                    echo "['".obtenerPorcentaje($datfYear['maestro']['gen-3'], $metfYear['maestro']['gen-3'])."% IPG Generación 3',".intval($metfYear['maestro']['gen-3']).",".intval($datfYear['maestro']['gen-3'])."],";
                    echo "['Total grupos y Asistencia',".intval($datfYear['maestro']['gen-1']+$datfYear['maestro']['gen-2']+$datfYear['maestro']['gen-3']).",".intval($datfYear['maestro']['asistencia'])."]";
                    ?>]);

                var view = new google.visualization.DataView(data);
              view.setColumns(
                  [0, { calc: "stringify",
                     sourceColumn: 2,
                     type: "string",
                     role: "annotation" }, 1,
                   { calc: "stringify",
                     sourceColumn: 1,
                     type: "string",
                     role: "annotation" }, 2]);

                var options = {
                chartArea: {
                  // leave room for y-axis labels
                  width: '70%',
                  height: '80%'
                },
                bar: {groupWidth: "95%"},
                legend: { position: 'none' },
                width: '90%',
                colors: ['limegreen', 'crimson']
            };
        var chart = new google.visualization.BarChart(document.getElementById('graficaMaestro'));
        chart.draw(view, options);
    }
    </script>
    <div id="graficaMaestro" class="chart"></div>
    <?php
}
?>
</div>