<?php
/*if($_SESSION["perfil"] == 3 || $_SESSION["perfil"] == 4 || $_SESSION["perfil"] == 160)
{
	die("<h1>No esta autorizado para ver esta informaci&oacute;n</h1>");
}*/

if(isset($_GET["id"]))
{
	$idUsuario = soloNumeros($_GET["id"]);
}
else
{
	die("Debe especificarse un ID de solicitud.");
}

/*if($_SESSION["perfil"] != 2)
{
	die("No esta autorizado para estar aqui.");
}*/
// Objeto de Base de Datos
$PSN1 = new DBbase_Sql;
// Array que nos servira para ir llevando cuenta de los requerimientos.
$arrayRequerimientos = array();


//
// COMPROBAMOS QUE EL USUARIO PERTENEZCA A ESTE REPORTE O SEA LIDER/ADMINISTRADOR
//
if($idUsuario != 0){
    $sql = "SELECT usuario.id";
    $sql.=" FROM usuario ";
    $sql.=" WHERE usuario.id = '".$idUsuario."'";
    //
    $PSN1->query($sql);
    $numero=$PSN1->num_rows();
    if($numero == 0)
    {
        die("<h1>No esta autorizado para ver esta información</h1>");
    }
}
//
//	FIN COMPROBACION
//

if(isset($_POST["funcion"]))
{
	if($_POST["funcion"] == "insertarMeta")
	{
        $anho = soloNumeros($_POST["anho"]);
        $evangelismo = soloNumeros($_POST["evangelismo"]);
        $discipulado = soloNumeros($_POST["discipulado"]);
        $bautizos = soloNumeros($_POST["bautizos"]);
        $iglesias = soloNumeros($_POST["iglesias"]);
        $iglesias2 = soloNumeros($_POST["iglesias2"]);
        $iglesias3 = soloNumeros($_POST["iglesias3"]);
        $otra_meta1 = soloNumeros($_POST["otra_meta1"]);

        
		$sql = 'REPLACE INTO usuario_metas (
					idUsuario, 
                    anho,
					evangelismo, 
					discipulado, 
                    bautizos,
                    iglesias,
                    iglesias2,
                    iglesias3,
                    otra_meta1
				) ';
		$sql .= 'VALUES (
                    '.$idUsuario.',
                    "'.$anho.'", 
                    "'.$evangelismo.'", 
                    "'.$discipulado.'", 
                    "'.$bautizos.'", 
                    "'.$iglesias.'", 
                    "'.$iglesias2.'", 
                    "'.$iglesias3.'", 
                    "'.$otra_meta1.'" 
        )';

		$ultimoQuery = $PSN1->query($sql);		
        $ultimoId =  $PSN1->ultimoId();
		$varExito = 1;

		//DEBEMOS ACTUALIZAR EL REPORTE PARA SABER QUIEN FUE LA ULTIMA PERSONA EN ESCRIBIR
		//YA SEA UNA MODIFICACIÓN A LA INFORMACIÓN, UNA OBSERVACIÓN, ETC.
        if($ultimoId > 0){
            $sql = "UPDATE usuario SET ";
                $sql .= "modUsuario = '".$_SESSION["id"]."', ";
                $sql .= "modFecha = NOW()";
            $sql .= " WHERE id = '".$idUsuario."'";
            $PSN1->query($sql);
        }
		//FIN ACTUALIZACIÓN DE REGISTROS
	}
}




/*
*	TRAEMOS LOS RECURSOS
DATE_SUB( observacion.fechaCreacion, INTERVAL 0 MINUTE )
*/
$sql = "SELECT 
            usuario_metas.* ";
$sql.=" FROM usuario_metas ";
$sql.=" WHERE usuario_metas.idUsuario = '".$idUsuario."'";
$sql.=" ORDER BY usuario_metas.anho ASC";
//
$PSN1->query($sql);
$numero=$PSN1->num_rows();
//
?><div class="container">
    <div class="row">
        <h3 class="text-center well">METAS<?php
        if($idUsuario == 0)    {
            ?> SATURA COLOMBIA<?php
        }
        ?></h3>
    </div>

<div class="row">

<div class="col-md-10 col-md-offset-1">
<table border="0" cellspacing="12" cellpadding="12"  align="center" class="table table-bordered" style="font-size:12px">
    <thead>
        <tr>
            <th><center>Año</center></th>
            <th><center>Evangelismo</center></th>
            <th><center>Discipulado</center></th>
            <th><center>Bautizos</center></th>
            <th><center>Iglesias Peq. Grupos Gen1</center></th>
            <th><center>Iglesias Peq. Grupos Gen2</center></th>
            <th><center>Iglesias Peq. Grupos Gen3</center></th>
        </tr>
    </thead>
<tbody>
<?php
if($numero > 0)
{
	$contador = 0;
	while($PSN1->next_record())
	{
		$anho = $PSN1->f('anho');
		$evangelismo = $PSN1->f('evangelismo');
		$discipulado = $PSN1->f('discipulado');
		$bautizos = $PSN1->f('bautizos');
		$iglesias = $PSN1->f('iglesias');
		$iglesias2 = $PSN1->f('iglesias2');
		$iglesias3 = $PSN1->f('iglesias3');
		$otra_meta1 = $PSN1->f('otra_meta1');
        
		?><tr <?php if($contador%2==0){ ?>bgcolor="#EEEEEE"<?php } ?>> 
            <td><center><?=$anho; ?></center></td>
            <td><center><?=$evangelismo; ?></center></td>
            <td><center><?=$discipulado; ?></center></td>
            <td><center><?=$bautizos; ?></center></td>
            <td><center><?=$iglesias; ?></center></td>
            <td><center><?=$iglesias2; ?></center></td>
            <td><center><?=$iglesias3; ?></center></td>
		</tr>
		<?php
		$contador++;
	}
}
else
{
	?>
	<tr><td colspan="5">Sin metas.</td></tr>
	<?php
}
?>
	</tbody>
</table>
</div>
</div>


<form method="post" enctype="multipart/form-data" name="form1" id="form1" class="form-horizontal">
<input type="hidden" name="id" value="<?=$idUsuario; ?>" />
    
    <div class="panel panel-warning">
      <div class="panel-heading text-center">AGREGAR NUEVA META</div>
      <div class="panel-body"><div class="form-group">
        <label class="control-label col-sm-1" for="tipo"><strong>Año:</strong></label>
        <div class="col-sm-2"><select name="anho" class="form-control">
            <?php
            for($i = 2021; $i <= date("Y", strtotime("+1 year")); $i++){
                ?><option value="<?=$i; ?>"><?=$i; ?></option><?php
            }
            ?>
        </select></div>
        
        <label class="control-label col-sm-1" for="evangelismo"><strong>Evangelismo</strong></label>
        <div class="col-sm-2"><input type="number" min="0" name="evangelismo" id="evangelismo" class="form-control" required /></div>
        
        <label class="control-label col-sm-1" for="discipulado"><strong>Discipulado</strong></label>
        <div class="col-sm-2"><input type="number" min="0" name="discipulado" id="discipulado" class="form-control" required /></div>

        <label class="control-label col-sm-1" for="bautizos"><strong>Bautizos</strong></label>
        <div class="col-sm-2"><input type="number" min="0" name="bautizos" id="bautizos" class="form-control" required /></div>
        </div>
          
        <div class="form-group">
            <div class="col-sm-3">&nbsp;</div>
            <label class="control-label col-sm-1" for="iglesias"><strong>Iglesias pequeños grupos Gen. 1</strong></label>
            <div class="col-sm-2"><input type="number" min="0" name="iglesias" id="iglesias" class="form-control" required /></div>
            <label class="control-label col-sm-1" for="iglesias2"><strong>Iglesias pequeños grupos Gen. 2</strong></label>
            <div class="col-sm-2"><input type="number" min="0" name="iglesias2" id="iglesias2" class="form-control" required /></div>
            <label class="control-label col-sm-1" for="iglesias3"><strong>Iglesias pequeños grupos Gen. 3</strong></label>
            <div class="col-sm-2"><input type="number" min="0" name="iglesias3" id="iglesias3" class="form-control" required /></div>
        </div>
        
        <div class="row"><center><input type="submit" name="button" value="Guardar meta" class="btn btn-success"></center></div>
        <br />
    </div>
    </div>
    </div>
    <input type="hidden" name="funcion" id="funcion" value="" />
</form>    

</div>
<a name="final">&nbsp;</a>
    
<script language="javascript">
function generarForm(){
        if(confirm("Esta accion guardara los cambios en el sistema, ¿esta seguro que desea continuar?"))
        {
            //Todo bien
            document.getElementById('funcion').value = "insertarMeta";                
        }else{
            return false;
        }
        return true;
}
//
function init(){
    document.getElementById('form1').onsubmit = function(){
        return generarForm();
    }
}

window.onload = function(){
    init();
}

</script>