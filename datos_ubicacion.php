<?php 
session_start();
include_once('funciones.php');
$PSN = new DBbase_Sql;
if ($_POST['id_depa']!="") {
	$departamento = $_POST['id_depa'];
}else{
	$departamento = 0;
}
$sql = "SELECT * ";
$sql.=" FROM dane_municipios ";
$sql.=" WHERE departamento_id = ".$departamento." ORDER BY municipio asc";
$PSN->query($sql);
?>
<strong>Municipio</strong>
<select required name="municipio" id="municipio" class="form-control">
    <option value="">Sin especificar</option>
	<?php 
	$numero=$PSN->num_rows();
    if($numero > 0){
        while($PSN->next_record()){?>
        	<option value="<?=$PSN->f('id_municipio'); ?>" <?php
            if($_SESSION['muni'] == $PSN->f('id_municipio')){
                ?>selected="selected"<?php
            }
            ?> ><?=$PSN->f('municipio'); ?></option><?php
        }
    }
    ?>
</select>