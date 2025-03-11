<?php
// fdfd
// Objeto de Base de Datos
//
$PSN1 = new DBbase_Sql;
$PSN = new DBbase_Sql;
$webArchivo = "preoperacional";
$temp_letrero = "PROGRAMA DE EVANGELISMO Y DISCIPULADO";



/*
*   VERIFICAMOS CON QUE GENERACIÓN NOS ESTAMOS ENFRENTANDO ACTUALMENTE.
*/
$preguntarGeneracion = 0;
if(isset($_REQUEST["generacion"]) && $_REQUEST["generacion"] != ""){
    $generacionActual = eliminarInvalidos($_REQUEST["generacion"]);
}else{
    $preguntarGeneracion = 1;
}


if($preguntarGeneracion == 1){
    ?><div class="container">
    <div class="row">
        <h3 class="alert alert-info text-center">PROGRAMA DE EVANGELISMO Y DISCIPULADO</h3>
    </div>

    <form method="post" enctype="multipart/form-data" name="form1" id="form1" class="form-horizontal">

    <fieldset>
        <div class="cont-tit">
            <div class="hr"><hr></div>
            <div class="tit-cen">
                <h3 class="text-center">REPORTAR INFORMACIÓN</h3>
                <p>Escoja una de las siguientes opciones</p>
            </div>
            <div class="hr"><hr></div>
        </div>
        <?php 
            $sql = "SELECT id AS registro FROM sat_reportes ";
            $sql .= "WHERE rep_tip = 317 AND idUsuario = ".$_SESSION['id']." AND YEAR(fechaReporte) = '".date('Y')."' AND MONTH(fechaReporte) = '".date('m')."';";
            $resul = 0;
            $PSN1->query($sql);
            if($PSN1->next_record()){
                $resul = $PSN1->f('registro');  
            }
         ?>       
         <?php echo($resul>0)?"<div class='alert alert-danger text-center'><b>Ya existe un reporte de Evangelistas para el mes de ".$array_meses[date('n')]."</b></div>":""; ?>
        <div class="cont-flex fl-cent">
                <a href="?doc=subcategoria-eva<?php echo($resul>0)?"&id=".$PSN1->f('registro'):""; ?>" class="btn-mar btn btn-primary ">EVANGELISTAS<br><span class="btn-desc">Reporte<?php echo($resul>0)?": ".$PSN1->f('registro'):""; ?></span></a>
                <a href="?doc=subcategoria-lpp" class="btn-mar btn btn-danger">LA PEREGRINACIÓN<br><span class="btn-desc">DEL PRISIONERO</span></a>
                <a href="?doc=subcategoria-ecc" class="btn-mar btn btn-success">ECC<br><span class="btn-desc">(CADA COMUNIDAD PARA CRISTO "SATURA")</span></a>
                      
        </div><br><br>
        
        <input type="hidden" name="funcion" id="funcion" value="" />
        <input type="hidden" name="generacion" id="generacion" value="<?=$idVehiculo; ?>" />
    </form>
<?php }?>