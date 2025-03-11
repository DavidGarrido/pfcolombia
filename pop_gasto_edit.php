<?php
//Id de estudiante a partir del 20 de Marzo de 2017 - 25797
if(isset($_GET["id"]) && isset($_GET["abn"]))
{
	$varMiId = soloNumeros($_GET["id"]);
	$varMiAbn = soloNumeros($_GET["abn"]);
}
else
{
	die("Debe especificarse un ID de Solicitud.");
}

// Objeto de Base de Datos
$PSN1 = new DBbase_Sql;


//
// COMPROBAMOS QUE EL USUARIO PERTENEZCA A ESTE REPORTE O SEA LIDER/ADMINISTRADOR
//
$sql = "SELECT cotizacion.id, cotizacion.estado, cotizacion.idCliente, cotizacion.contactoNombre, gastos.tipo, gastos.observacion, gastos.fechaCreacion, gastos.valor";
$sql.=" FROM cotizacion, gastos ";
//$sql.=" LEFT JOIN usuario as cliente ON cliente.id = estudiante.idColegio";
$sql.=" WHERE cotizacion.id = ".$varMiId." AND gastos.idCotizacion = cotizacion.id AND gastos.id = '".$varMiAbn."'";
//
$PSN1->query($sql);
$numero=$PSN1->num_rows();
if($numero == 0)
{
	die("<h1>No esta autorizado para ver esta informaci&oacute;n</h1>");
}
else
{
	if($PSN1->next_record())
	{
		$varEstado = $PSN1->f('estado');
		$idClienteOk = $PSN1->f('idCliente');
		$contactoNombre = $PSN1->f('contactoNombre');

		$fechaCreacion = $PSN1->f('fechaCreacion');
		$abonoTipo = $PSN1->f('tipo');
		$abonoValor = $PSN1->f('valor');
		$abonoObservacion = $PSN1->f('observacion');

	}
}
//
//	FIN COMPROBACION
//

// Array que nos servira para ir llevando cuenta de los requerimientos.
$arrayRequerimientos = array();
if(isset($_POST["funcion"]))
{
	if($_POST["funcion"] == "updateAbono" && eliminarInvalidos($_POST["valor"]) != "")
	{
		/*$nombre_archivo = $_FILES['archivo']['name'];
		$tipo_archivo = $_FILES['archivo']['type'];
		$tamano_archivo = $_FILES['archivo']['size'];
		$ext = extension_archivo($nombre_archivo);		
		
		echo "<strong>Resultado archivo: </strong>";
		switch($_FILES['archivo']['error'])
		{
			case UPLOAD_ERR_OK:
				//$messageErr = false;;
				echo "UPLOAD_ERR_OK";
				break;
			case UPLOAD_ERR_INI_SIZE:
				//echo ' - file too large (limit of '.get_max_upload().' bytes).';
				echo "UPLOAD_ERR_INI_SIZE";
				break;
			case UPLOAD_ERR_FORM_SIZE:
				echo ' - file too large (limit of '.get_max_upload().' bytes).';
				break;
			case UPLOAD_ERR_PARTIAL:
				echo ' - file upload was not completed.';
				break;
			case UPLOAD_ERR_NO_FILE:
				echo ' - zero-length file uploaded.';
				break;
			default:
				echo ' - internal error #'.$_FILES['archivo']['error'];
				break;
		}
		echo "<br />";
		echo "<strong>Nombre archivo: </strong>";
		echo $nombre_archivo;
		echo "<br />";
		echo "<strong>Tipo de archivo: </strong>";
		echo $tipo_archivo;
		echo "<br />";
		echo "<strong>Tamaño de archivo: </strong>";
		echo $tamano_archivo;
		echo "<br />";
		echo "<strong>Ext de archivo: </strong>";
		echo $ext;
		echo "<br />";
		echo "<strong>Ubicacion de archivo: </strong>";
		echo $_FILES['archivo']['tmp_name'];*/
		
		$sql = 'UPDATE gastos SET 
					tipo = "'.soloNumeros($_POST["tipo"]).'", 
					valor = "'.soloNumeros($_POST["valor"]).'",
					fechaCreacion = "'.eliminarInvalidos($_POST["fechaCreacion"]).'", 
					observacion = "'.eliminarInvalidos($_POST["observacion"]).'" 
				WHERE 
					id = "'.$varMiAbn.'"
				';		
		$ultimoQuery = $PSN1->query($sql);		
		$varExito = 1;

		//DEBEMOS ACTUALIZAR EL REPORTE PARA SABER QUIEN FUE LA ULTIMA PERSONA EN ESCRIBIR
		//YA SEA UNA MODIFICACIÓN A LA INFORMACIÓN, UNA OBSERVACIÓN, ETC.
		$sql = "UPDATE cotizacion SET ";
			$sql .= "modUsuario = '".$_SESSION["id"]."'";
			$sql .= ", modFecha = NOW()";
		$sql .= " WHERE id = '".$varMiId."'";
		$PSN1->query($sql);
		//FIN ACTUALIZACIÓN DE REGISTROS
	
		//Compruebo si las características del archivo son las que deseo 
		/*if(move_uploaded_file($_FILES['archivo']['tmp_name'], "archivos/abn".$ultimoId.".".$ext))
		{
			echo "Se movio...";
		}
		else
		{
			echo "NO se movio...";
		}*/
	}
}

if($varExito != 1){	
?><form method="post" enctype="multipart/form-data" name="form1" id="form1" action="pop_up.php?doc=pop_gasto_edit&id=<?=$varMiId; ?>&abn=<?=$varMiAbn ; ?>" class="form-horizontal">
	
    <div class="form-group">
        <h3 class="text-center well">.EDITAR GASTO.</h3>
    </div>

    <div class="form-group">
        <label class="control-label col-sm-2" for="tipo">Tipo</label>
        <div class="col-sm-10"><select name="tipo" class="form-control">
		<?
		/*
		*	TRAEMOS LOS TIPOS DE GASTOS
		*/
		$sql = "SELECT * ";
		$sql.=" FROM categorias ";
		$sql.=" WHERE idSec = 24 ORDER BY descripcion asc";
		
		$PSN1->query($sql);
		$numero=$PSN1->num_rows();
		if($numero > 0)
		{
			while($PSN1->next_record())
			{
				?><option value="<?=$PSN1->f('id'); ?>" <?
				if($abonoTipo == $PSN1->f('id'))
				{
					?>selected="selected"<?
				}
				?>><?=$PSN1->f('descripcion'); ?></option><?
			}
		}
		?>
		</select></div>
	</div>

    
    <div class="form-group">
        <label class="control-label col-sm-2" for="fechaCreacion">Fecha del Gasto</label>
        <div class="col-sm-10"><input name="fechaCreacion" type="date"  placeholder="AAAA-MM-DD" value="<?=$fechaCreacion; ?>" class="form-control" /></div>
	</div>

    <div class="form-group">
        <label class="control-label col-sm-2" for="valor">Gasto</label>
        <div class="col-sm-10"><input type="number" name="valor" id="valor" value="<?=$abonoValor; ?>" class="form-control" /></div>
	</div>

    <div class="form-group">
        <label class="control-label col-sm-2" for="observacion">Detalle</label>
        <div class="col-sm-10"><textarea name="observacion" id="observacion" cols="80" rows="5" class="form-control"><?=$abonoObservacion; ?></textarea></div>
	</div>

    <div class="row text-center">
        <input type="hidden" name="funcion" id="funcion" value="" />
        <input type="submit" value="Guardar cambios" class="btn btn-success" /> <a href="javascript:window.close();void(0)" class="btn btn-danger">Cerrar</a>
    </div>
    

</form>
<script language="javascript">
function generarForm(){
	if(confirm("Esta accion actualizara el gasto de la cotizacion seleccionada, esta seguro que desea continuar?"))
	{
		if(document.getElementById('valor').value != "")
		{
			document.getElementById('funcion').value = "updateAbono";
			return true;
		}
		else
		{
			alert("Debe escribir algo.");
		}
	}
	return false;
}

function init(){
	document.getElementById('form1').onsubmit = function(){
			return generarForm();
	}
	<?
	if($varExito == 1)
	{
		?>opener.parent.location.href = 'index.php?doc=cotizacion&id=<?=$varMiId; ?>';
		window.close();<?
	}
	?>
}

window.onload = function(){
	init();
}
</script><?
}
else
{
	?><script language="javascript">
	function init(){
		<?
		if($varExito == 1)
		{
			?>opener.parent.location.href = 'index.php?doc=cotizacion&id=<?=$varMiId; ?>';
			window.close();<?
		}
		?>
	}
	
	window.onload = function(){
		init();
	}
</script><?
}
?>