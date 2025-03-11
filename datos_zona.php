<?php 
session_start();
include_once('funciones.php');
$PSN = new DBbase_Sql;
if ($_POST['id_zona']!=0) {
	$zona = $_POST['id_zona'];
}else{
	$zona = 0;
}
$sql = "SELECT C.id, C.descripcion AS regional, CA.descripcion AS zona FROM categorias AS C";
$sql.=" LEFT JOIN categorias AS CA ON CA.id = C.idSec
WHERE CA.idSec = 85 AND CA.id = ".$zona;
$PSN->query($sql);
?>
<strong>Regional a la que pertenece:</strong>
<select required name="empresa_pd" id="regional" class="form-control">
    <option value="">Sin especificar</option>
    <?php echo($zona == 0)?'<option value="0" selected >Todas la regionales</option>':""; 
	$numero=$PSN->num_rows();
    if($numero > 0){
        while($PSN->next_record()){?>
        	<option value="<?=$PSN->f('id'); ?>" <?php
            if($_SESSION['regional'] == $PSN->f('id')){
                ?>selected="selected"<?php
            }
            ?> ><?=$PSN->f('regional'); ?></option><?php
        }
    }
    ?>
</select>