<?php
/*if($_SESSION["superusuario"] != 1)
{
	die("<h1>No esta autorizado a visualizar esta opcion.</h1>");
}*/
if(isset($_GET["idcat"]))
{
	$varMiIdCat = $_GET["idcat"];
}
else
{
	die("Debe especificarse un ID de categoria.");
}

$varMiId = 0;
if(isset($_GET["id"]) && $_GET["id"] > 0)
{
	$varMiId = $_GET["id"];
}
else if(isset($_POST["id"]) && $_POST["id"] > 0)
{
	$varMiId = $_POST["id"];
}

// Objeto de Base de Datos
$PSN1 = new DBbase_Sql;
$PSN2 = new DBbase_Sql;
$PSN3 = new DBbase_Sql;

if(isset($_POST["funcion"]))
{
	if($_POST["funcion"] == "insertarCategoria" && trim(htmlspecialchars($_POST["categoria"])) != "")
	{
		if($varMiId == 0){
			if ($varMiIdCat==84) {
				$sql = 'insert into categorias (idSec, descripcion, detalle) ';
				$sql .= 'values ('.htmlspecialchars($_POST["zona"]).', "'.htmlspecialchars($_POST["categoria"]).'", "'.$varMiIdCat.'")';
			}else if ($varMiIdCat!=83) {
				$sql = 'insert into categorias (idSec, descripcion, detalle) ';
				$sql .= 'values ('.$varMiIdCat.', "'.htmlspecialchars($_POST["categoria"]).'", "'.htmlspecialchars($_POST["categoria"]).'")';
			}else{
				$sql = 'INSERT INTO tbl_regional_ubicacion (reub_nom, reub_dir, reub_reg_fk, reub_mun_fk) ';
				$sql .= 'VALUES ("'.$_POST["categoria"].'", "'.$_POST["direccion"].'", '.$_POST["regional"].', '.$_POST["municipio"].')';
			}
			
		}else{
			if ($varMiIdCat!=83) {
				$sql = 'update categorias set descripcion = "'.htmlspecialchars($_POST["categoria"]).'", detalle = "'.htmlspecialchars($_POST["categoria"]).'" where id = '.$varMiId;
			}else{
				$sql = 'UPDATE tbl_regional_ubicacion';
				$sql .= ' SET reub_nom = "'.$_POST["categoria"].'", reub_dir="'.$_POST["direccion"].'", reub_reg_fk='.$_POST["regional"].', reub_mun_fk='.$_POST["municipio"].' WHERE reub_id = '.$varMiId;
			}
		}

		$ultimoQuery = $PSN1->query($sql);		
		$varExito = 1;
	}
	else if($_POST["funcion"] == "borrarDato" && $varMiId != 0)
	{
		if ($varMiIdCat!=83) {
			$sql = "DELETE FROM categorias where id = ".$varMiId;
			$ultimoQuery = $PSN1->query($sql);	
		}else{
			$sql = "DELETE FROM tbl_regional_ubicacion where reub_id = ".$varMiId;
			$ultimoQuery = $PSN1->query($sql);
		}	
		$varExito = 1;
	}
}

// Array que nos servira para ir llevando cuenta de los requerimientos.
/*
*	TRAEMOS LAS CATEGORIAS
*/
if ($varMiIdCat!=83) {
	$sql = "SELECT * ";
	$sql.=" FROM categorias  ";
}


?><div class="container">
	<div>
        <h3 class="alert alert-info text-center">CATEGORÍAS DE "<?php
		switch($varMiIdCat){
	        case 38:
	            echo "PROGRAMAS";
	            $sql.=" WHERE idSec = ".$varMiIdCat." ORDER BY id ASC";
	            break;
	        case 78:
	            echo "DIPLOMADOS";
	            $sql.=" WHERE idSec = ".$varMiIdCat." ORDER BY id ASC";
	            break;
	        case 87:
	            echo "NIVELES DE GRADO";
	            $sql.=" WHERE idSec = ".$varMiIdCat." ORDER BY id ASC";
	            break;
	        case 85:
	            echo "ZONAS";
	            $sql.=" WHERE idSec = ".$varMiIdCat." ORDER BY id ASC";
	            break;
	        case 84:
	            echo "REGIONALES";
	            $sql.=" WHERE detalle = ".$varMiIdCat." ORDER BY id ASC";
	            break;
	        case 83:
	            echo "PRISIONES";
	            $sql = "SELECT P.*, C.descripcion AS regional, CM.municipio ";
				$sql.=" FROM tbl_regional_ubicacion AS P 
				LEFT JOIN categorias AS C ON C.id = P.reub_reg_fk
				LEFT JOIN dane_municipios AS CM ON CM.id_municipio = P.reub_mun_fk ";
	            break;
	        case 305:
	            echo "SUB-PROGRAMA";
	            $sql.=" WHERE idSec = ".$varMiIdCat." ORDER BY id ASC";
	            break;
			default:
				echo "SIN CATEGORIA";
				break;
		}?>"
		</h3>
	</div>
<?php 
$PSN1->query($sql);

$numero=$PSN1->num_rows();
if ($varMiIdCat!=83) {
?>
<div class="row">
	<div class="col-md-6 col-md-offset-3">
		<table border="0" cellspacing="0" cellpadding="2"  align="center" class="table table-bordered" style="font-size:12px">
		    <thead>
			<tr> 
				<th>Id</th>
				<th>Nombre</th>
			</tr>
		    </thead>
		    <tbody>
				<?php
				if($numero > 0){
					$contador = 0;
					while($PSN1->next_record()){
						//Solo si no se ha modificado ya el formulario.
						$id = $PSN1->f('id');
						$descripcion = $PSN1->f('descripcion');
						?><tr <?php if($contador%2==0){ ?>bgcolor="#EEEEEE"<?php } ?>>
						<td><a href="index.php?doc=admin_buscarcat&idcat=<?=$varMiIdCat; ?>&id=<?=$id; ?>&opc=1#insertar"><?=str_pad($id, 6, "0", STR_PAD_LEFT); ?></a></td>
						<td><a href="index.php?doc=admin_buscarcat&idcat=<?=$varMiIdCat; ?>&id=<?=$id; ?>&opc=1#insertar"><?=$descripcion; ?></a></td>
						</tr>
						<?php
						$contador++;
					}
				}

				/*
				*	TRAEMOS LAS CATEGORIAS
				*/
				$sql = "SELECT * ";
				$sql.=" FROM categorias  ";
				$sql.=" WHERE id = ".$varMiId." ORDER BY descripcion ASC";

				$PSN1->query($sql);
				$numero=$PSN1->num_rows();

				if($numero > 0)
				{
					$contador = 0;
					$PSN1->next_record();
				}?>
			</tbody>
		</table>
	</div>
</div>
<?php 
}else{
?>
<div class="row">
	<div class="col-md-10 col-md-offset-1">
		<table border="0" cellspacing="0" cellpadding="2"  align="center" class="table table-bordered" style="font-size:12px">
		    <thead>
			<tr> 
				<th>Id</th>
				<th>Nombre</th>
				<th>Dirección</th>
				<th>Regional</th>
				<th>Municipio</th>
			</tr>
		    </thead>
		    <tbody>
				<?php
				if($numero > 0){
					$contador = 0;
					while($PSN1->next_record()){
						//Solo si no se ha modificado ya el formulario.
						$id = $PSN1->f('reub_id');
						$nombre = $PSN1->f('reub_nom');
						$direccion = $PSN1->f('reub_dir');
						$regional = $PSN1->f('regional');
						$municipio = $PSN1->f('municipio');
						?><tr <?php if($contador%2==0){ ?>bgcolor="#EEEEEE"<?php } ?>>
						<td><a href="index.php?doc=admin_buscarcat&idcat=<?=$varMiIdCat; ?>&id=<?=$id; ?>&opc=1#insertar"><?=str_pad($id, 6, "0", STR_PAD_LEFT); ?></a></td>
						<td><?=$nombre; ?></td>
						<td><?=$direccion; ?></td>
						<td><a href="index.php?doc=admin_buscarcat&idcat=<?=$varMiIdCat; ?>&id=<?=$id; ?>&opc=1#insertar"><?=$regional; ?></a></td>
						<td><?=$municipio; ?></td>
						</tr>
						<?php
						$contador++;
					}
				}

				/*
				*	TRAEMOS LAS CATEGORIAS
				*/
				$sql = "SELECT P.*, C.descripcion AS regional, CM.* ";
				$sql.=" FROM tbl_regional_ubicacion AS P 
				LEFT JOIN categorias AS C ON C.id = P.reub_reg_fk
				LEFT JOIN dane_municipios AS CM ON CM.id_municipio = P.reub_mun_fk";
				$sql.=" WHERE reub_id = ".$varMiId;
				//echo $sql;
				$PSN1->query($sql);
				$numero=$PSN1->num_rows();

				if($numero > 0)
				{
					$contador = 0;
					$PSN1->next_record();
				}?>
			</tbody>
		</table>
	</div>
</div>
<?php
} 
?>

<div class="row text-center">
	<input type="button" name="button" onclick="nuevoRegistro()" value="Insertar Registro" class="btn btn-success" /> 
	<input type="button" name="button" onclick="regresar()" value="Regresar" class="btn btn-danger" />
</div>

</div>
<?php
if(isset($_REQUEST["opc"]))
{
	?><div class="container">
	<form method="post" enctype="multipart/form-data" name="form1" id="form1" action="index.php?doc=admin_buscarcat&idcat=<?=$varMiIdCat; ?>&id=<?=$varMiId; ?>" class="form-horizontal">

    
    <a name="insertar"></a>
    <div class="cont-tit">
        <div class="hr"><hr></div>
        <div class="tit-cen">
            <h3 class="text-center"><?php
			if($varMiId > 0){
				echo "ACTUALIZAR";
			}else{
				echo "INSERTAR";
			}?></h3>
            <h5>REGISTRO ID: <?=$varMiId; ?></h5>
        </div>
        <div class="hr"><hr></div>
    </div>         
	<div class="form-group">
        <?php if ($varMiIdCat!=83 && $varMiIdCat!=84) { ?>
			<div class="col-sm-4"></div>
			<div class="col-sm-4">
				<strong>Nombre</strong>
				<input type="text" name="categoria" id="categoria" value="<?=$PSN1->f('descripcion'); ?>" class="form-control" />
			</div>
		<?php }else if ($varMiIdCat==84) { ?>
			<div class="col-sm-2"></div>
			<div class="col-sm-4">
				<strong>Zona:</strong>
				<select  name="zona" class="form-control" >
	                <option value="" >Seleccionar zona</option>
	                <?php 
	                $sql = "SELECT C.id, C.descripcion AS zona";
	                $sql.=" FROM categorias AS C WHERE C.idSec = 85 ";
	                $PSN2->query($sql); 
	                //echo $sql;
	                $numero=$PSN2->num_rows();
	                if($numero > 0){
	                    while($PSN2->next_record()){?>
	                        <option value="<?=$PSN2->f('id'); ?>" <?php
	                        if($PSN2->f('id') == $PSN1->f('reub_reg_fk')){
	                            ?>selected="selected"<?php
	                        }
	                        ?> ><?=$PSN2->f('zona'); ?></option><?php
	                    }
	                }
	                ?>
	            </select>  
			</div>
			<div class="col-sm-4">
				<strong>Nombre:</strong>
				<input type="text" name="categoria" id="categoria" value="<?=$PSN1->f('descripcion'); ?>" class="form-control" />
			</div>
		<?php }else{?>
			<div class="col-sm-3">
				<strong>Nombre de prision</strong>
				<input type="text" name="categoria" id="categoria" value="<?=$PSN1->f('reub_nom'); ?>" class="form-control" />
			</div>
			<div class="col-sm-3">
				<strong>Dirección de prision</strong>
				<input type="text" name="direccion" id="direccion" value="<?=$PSN1->f('reub_dir'); ?>" class="form-control" />
			</div>
			<div class="col-sm-2">
	            <strong>Regional:</strong>
	            <select  name="regional" class="form-control" >
	                <option value="" >Todas la regionales</option>
	                <?php 
	                $sql = "SELECT C.id, C.descripcion AS regional, CA.descripcion AS zona ";
	                $sql.=" FROM categorias AS C LEFT JOIN categorias AS CA ON CA.id = C.idSec WHERE CA.idSec = 85 ";
	                $PSN2->query($sql); 
	                //echo $sql;
	                $numero=$PSN2->num_rows();
	                if($numero > 0){
	                    while($PSN2->next_record()){?>
	                        <option value="<?=$PSN2->f('id'); ?>" <?php
	                        if($PSN2->f('id') == $PSN1->f('reub_reg_fk')){
	                            ?>selected="selected"<?php
	                        }
	                        ?> ><?=$PSN2->f('regional'); ?></option><?php
	                    }
	                }
	                ?>
	            </select>                    
	        </div>
	        <div class="col-sm-2">
                <strong>Departamento</strong>
                <select required name="departamento" id="departamento" style="text-transform: capitalize;" class="form-control">
                    <option value="">Sin especificar</option>
                    <?php
                    /*
                    *   TRAEMOS LOS TIPOS DE IDENTIFICACION (2)
                    */
                    $sql = "SELECT id_departamento,lower(departamento) as departamento ";
                    $sql.=" FROM dane_departamentos ";
                    $sql.=" ORDER BY departamento asc";
                    $PSN3->query($sql);
                    $numero=$PSN3->num_rows();
                    if($numero > 0){
                        while($PSN3->next_record()){
                            ?><option style="text-transform: capitalize;" value="<?=$PSN3->f('id_departamento'); ?>" <?php
                            if($PSN3->f('id_departamento') == $PSN1->f('departamento_id'))
                            {
                                ?>selected="selected" <?php
                            }
                            ?> ><?=$PSN3->f('departamento'); ?></option><?php
                        }
                    }
                    ?>
                </select>
            </div>
            <div class="col-sm-2">
                <?php $_SESSION['muni'] = $PSN1->f('reub_mun_fk'); ?>
                <div id="municipio"></div>
            </div>
            <script type="text/javascript">
			    $(document).ready(function(){
			        recargaLista();
			        $('#departamento').change(function(){
			            recargaLista();
			        });
			        recargaListaZona();
			        $('#zona').change(function(){
			            recargaListaZona();
			        });
			    })
			</script>
            <script type="text/javascript">
			    function recargaLista(){
			        $.ajax({
			            type: "POST",
			            url: "datos_ubicacion.php",
			            data: "id_depa=" + $('#departamento').val(),
			            success: function(r){
			                $('#municipio').html(r);
			            }
			        })
			    }
			    function recargaListaZona(){
			        $.ajax({
			            type: "POST",
			            url: "datos_zona.php",
			            data: "id_zona=" + $('#zona').val(),
			            success: function(r){
			                $('#regional').html(r);
			            }
			        })
			    }
			</script>
		<?php } ?>
	</div>         
	<div class="form-group">
		<div class="col-sm-4"></div>
        <div class="col-sm-4" style="text-align: center;">
            <input type="button" name="button" onclick="generarForm()" value="<?php
                if($varMiId > 0)
                {
                    echo "Guardar";
                }
                else
                {
                    echo "Insertar";
                }?>" class="btn btn-success" /> <input type="button" name="button" onclick="generarFormDel()" class="btn btn-danger" value="Eliminar categoria" />
        </div>
    </div>

        
	<input type="hidden" name="funcion" id="funcion" value="" />
	</form>
	</div>


	<script language="javascript">
		function generarForm(){
			if(confirm("Esta accion insertara un dato a la categoria actual, Esta seguro que desea continuar?"))
			{
				if(document.getElementById('categoria').value != "")
				{
					document.getElementById('funcion').value = "insertarCategoria";
					document.getElementById('form1').submit();
				}
				else
				{
					alert("Debe escribir algo.");
				}
			}
		}

		function generarFormDel(){
			if(confirm("Esta accion va a ELIMINAR el dato a la categoria actual, Esta seguro que desea continuar?"))
			{
				if(document.getElementById('categoria').value != "")
				{
					document.getElementById('funcion').value = "borrarDato";
					document.getElementById('form1').submit();
				}
				else
				{
					alert("Debe escribir algo.");
				}
			}
		}

		function init(){
			document.getElementById('form1').onsubmit = function(){
					return false;
			}
			<?php
			if($varExito == 1)
			{
				?>alert("Operacion exitosa!");<?php
			}
			?>
		}

		function nuevoRegistro()
		{
			window.location.href = "index.php?doc=admin_buscarcat&idcat=<?=$varMiIdCat; ?>&opc=1#insertar";
		}

		function regresar()
		{
			window.location.href = "index.php?doc=admin_buscarcatm";
		}

		window.onload = function(){
			init();
		}
		</script><?php
}
else{
	?>
	<script language="javascript">
	function init(){
		<?php
		if($varExito == 1)
		{
			?>alert("Operacion exitosa!");<?php
		}
		?>
	}

	function nuevoRegistro()
	{
		window.location.href = "index.php?doc=admin_buscarcat&idcat=<?=$varMiIdCat; ?>&opc=1#insertar";
	}

	function regresar()
	{
		window.location.href = "index.php?doc=admin_buscarcatm";
	}

	window.onload = function(){
		init();
	}
	</script><?php
}
?>