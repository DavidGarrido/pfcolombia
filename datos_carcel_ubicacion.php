<?php 
session_start();
include_once('funciones.php');
$PSN = new DBbase_Sql;
$id_carcel = 0;
if ($_POST['id_carcel']!= "") {
    $id_carcel = $_POST['id_carcel'];
}
$sqlR = "SELECT RU.reub_nom, LOWER(D.departamento) AS departamento, M.municipio,M.id_municipio, RU.reub_dir ";
$sqlR .= "FROM tbl_regional_ubicacion AS RU
INNER JOIN categorias AS C ON C.id = RU.reub_reg_fk
LEFT JOIN dane_municipios AS M ON M.id_municipio = RU.reub_mun_fk
LEFT JOIN dane_departamentos AS D ON D.id_departamento = M.departamento_id
WHERE reub_id = ".$id_carcel." ORDER BY reub_nom asc ";
$PSN->query($sqlR);
$numero=$PSN->num_rows();
if($numero > 0){
    while($PSN->next_record()){
        $id_municipio = $PSN->f('id_municipio');
        $municipio = $PSN->f('municipio');
        $departamento = $PSN->f('departamento');
        $direccion = $PSN->f('reub_dir');
    }
}

?>


<div class="col-sm-2">
    <strong>Departamento:</strong>
    <input readonly style="text-transform: capitalize;" name="departamento" type="text" id="departamento" maxlength="250" value="<?=$departamento; ?>" class="form-control" required />
</div>
<div class="col-sm-2">
    <strong>Municipio:</strong>
    <input name="municipio" type="hidden" value="<?=$id_municipio; ?>" class="form-control" required />
    <input readonly name="ciudad" type="text" id="municipio" maxlength="250" value="<?=$municipio; ?>" class="form-control" required />
</div>
<div class="col-sm-3">
    <strong>Dirección:</strong>
    <input readonly name="direccion" type="text" id="direccion" maxlength="250" value="<?=$direccion; ?>" class="form-control" required />
</div>