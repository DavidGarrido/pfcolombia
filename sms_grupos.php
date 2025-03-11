<?php
//Si es un usuario externo o cliente o proveedor NO mostrar.
if($_SESSION["perfil"] == 3 || $_SESSION["perfil"] == 4 || $_SESSION["perfil"] == 160)
{
	die("<h1>No esta autorizado para ver esta informaci&oacute;n</h1>");
}


if(!isset($_GET["opc"]))
{
	$opc = 2;
}
else
{
	$opc = eliminarInvalidos($_GET["opc"]);
}

$PSN1 = new DBbase_Sql;
$webArchivo = "sms_grupos";
$nombreConsulta = "GRUPO";

if($opc == 1)
{
		if(isset($_POST["nombre"]))
		{
			$PSN = new DBbase_Sql;
			$nombre = eliminarInvalidos($_POST["nombre"]);
			$descripcion = eliminarInvalidos($_POST["descripcion"]);

			$sql = 'insert into sms_grupos (nombre,descripcion) ';
			$sql .= 'values ("'.$nombre.'","'.$descripcion.'")';

			$ultimoQuery = $PSN->query($sql);
			$ultimoId = $PSN->ultimoId();

			?><div class="container">
                    <div class="form-group">
                        <h2 class="alert alert-info text-center">.CREACIÓN DE GRUPOS EN EL SISTEMA.</h2>
                    </div>

                    <div class="form-group">
                        <h5 class="alert alert-warning text-center">Se ha creado correctamente el registro, en breve será redirigido, si no es redirigido de <a href="index.php?doc=sms_grupos&opc=2&id=<?=$ultimoId; ?>">clic aquí</a>.</h5>
                    </div>
                </div>
			<SCRIPT LANGUAGE="JavaScript">
			alert("Se ha creado correctamente el grupo!!!");
			window.location.href= "index.php?doc=sms_grupos&opc=2&id=<?=$ultimoId; ?>";
			</script>
			<?
		}
		else
		{
			$PSN = new DBbase_Sql;

			?><div class="container">

            <div class="form-group">
                <h2 class="alert alert-info text-center">.CREACIÓN DE <?=$nombreConsulta; ?>.</h2>
            </div>

            <form method="post" enctype="multipart/form-data" name="form1" id="form1" class="form-horizontal">
				

                <div class="form-group">
						<label class="control-label col-sm-2" for="nombre">Nombre del grupo</label>
					<div class="col-sm-4">
						<input name="nombre" type="text" id="nombre" maxlength="250" value="<?=eliminarInvalidos($_POST["nombre"]); ?>" class="form-control" />
					</div>
                    <label class="control-label col-sm-2" for="descripcion">Descripci&oacute;n del grupo</label>
					<div class="col-sm-4">
						<textarea name="descripcion" id="descripcion" rows="5" class="form-control"><?=eliminarInvalidos($_POST["descripcion"]); ?></textarea>
					</div>
				</div>

                <div class="row text-center">
					<input type="hidden" name="funcion" id="funcion" value="" />
                    <input type="submit" value="Guardar cambios" class="btn btn-success" /> 
                    <a href="index.php?doc=main" class="btn btn-danger">Cerrar</a>
				</div>
			</form>
            </div>

			<script language="javascript">
			function generarForm(){
					if(confirm("Esta accion generara el GRUPO en el sistema, ¿esta seguro que desea continuar?"))
					{
						if(document.getElementById('nombre').value != "" 
						)
						{
							document.getElementById('funcion').value = "insertar";
						}
						else
						{
                            return false;
							alert("La informacion es primordial para brindarle un excelente servicio, por favor digite al menos el campo de NOMBRE");
						}
					}
                    else{
                        return false;
                    }
                    return true;
			}
			function init(){
				document.getElementById('form1').onsubmit = function(){
						return generarForm();
				}
			}
			window.onload = function(){
				init();
			}
			</script>
			<?
		}
}
else
{
	if(isset($_GET["id"]))
	{
		$PSN = new DBbase_Sql;
		if(isset($_POST["nombre"]))
		{
			$nombre = eliminarInvalidos($_POST["nombre"]);
			$descripcion = eliminarInvalidos($_POST["descripcion"]);
			
			$sql = 'update sms_grupos set nombre="'.$nombre.'",descripcion="'.$descripcion.'"  where id='.soloNumeros($_GET["id"]);
			
			$PSN->query($sql);

			?><div class="container">
                    <div class="form-group">
                        <h2 class="alert alert-info text-center">.ACTUALIZACIÓN DE <?=$nombreConsulta; ?> EN EL SISTEMA.</h2>
                    </div>

                    <div class="form-group">
                        <h5 class="alert alert-warning text-center">Se ha ACTUALIZADO correctamente el registro, en breve será redirigido, si no es redirigido de <a href="index.php?doc=sms_grupos&opc=2&id=<?=soloNumeros($_GET["id"]); ?>">clic aquí</a>.</h5>
                    </div>
                </div>
			<SCRIPT LANGUAGE="JavaScript">
			alert("Se ha ACTUALIZADO correctamente el grupo!");
			window.location.href= "index.php?doc=sms_grupos&opc=2&id=<?=soloNumeros($_GET["id"]); ?>";
			</script>
			<?
		}
		else
		{
			$sql= "SELECT sms_grupos.* ";
			$sql.=" FROM sms_grupos";
			$sql.=" WHERE id=".soloNumeros($_GET["id"])." ORDER BY nombre asc ";
			
			$PSN->query($sql);
			$num=$PSN->num_rows();
			if($num > 0)
			{
				$izq = 1;
				if($PSN->next_record())
				{
					$PSN2 = new DBbase_Sql;
 			?><div class="container">

                <div class="form-group">
                    <h2 class="alert alert-info text-center">.ACTUALIZACI&Oacute;N DE <?=$nombreConsulta; ?>.</h2>
                </div>

                <form method="post" enctype="multipart/form-data" name="form1" id="form1" class="form-horizontal">
				
                <div class="form-group">
                    <label class="control-label col-sm-2" for="nombre">Nombre del grupo</label>
					<div class="col-sm-4">
						<input name="nombre" type="text" id="nombre" maxlength="250" value="<?=$PSN->f('nombre');?>" class="form-control" />
					</div>
                    <label class="control-label col-sm-2" for="nombre">Descripci&oacute;n</label>
					<div class="col-sm-4">
						<textarea name="descripcion" id="descripcion" rows="5" class="form-control"><?=$PSN->f('descripcion');?></textarea>
					</div>
				</div>
	        
	            
                <div class="row text-center">
					<input type="hidden" name="funcion" id="funcion" value="" />
                    <input type="submit" value="Guardar cambios" class="btn btn-success" /> 
                    <a href="index.php?doc=main" class="btn btn-danger">Cerrar</a>
				</div>
				</form>
				</div>
           
	            <script language="javascript">
                function generarForm(){
                    if(confirm("Esta accion actualizara el GRUPO en el sistema, ¿esta seguro que desea continuar?"))
                    {
                        if(document.getElementById('nombre').value != "" 
                        )
                        {
                            document.getElementById('funcion').value = "actualizar";
                        }
                        else
                        {
                            return false;
                            alert("La informacion es primordial para brindarle un excelente servicio, por favor digite al menos el campo de NOMBRE");
                        }
                    }
                    else{
                        return false;
                    }
                    return true;
                }
				
				function regresar()
				{
					window.location.href = "index.php?doc=sms_grupos";
				}

				function init(){
                    document.getElementById('form1').onsubmit = function(){
                            return generarForm();
                    }
                }
                window.onload = function(){
                    init();
                }
                </script>
				<?
				}
			}		
			else
			{
				?><div class="container">
					<h2><font color="#FF0000">ID Incorrecto. No Existe o no esta autorizado para visualizar la misma.</font></h2>
				</div>
				</div><?
			}	
		}
	}
	else
	{
		$PSN = new DBbase_Sql;
		$PSNB = new DBbase_Sql;

		$registros = 50;
		$pagina = soloNumeros($_GET["pagina"]);
		if (!$pagina) { 
			$inicio = 0; 
			$pagina = 1; 
		} 
		else
		{ 
			$inicio = ($pagina - 1) * $registros; 
		}
		
		/* PAGINACIÓN */		
		$sql= "SELECT sms_grupos.id";
		$sql.=" FROM sms_grupos";
		$PSN->query($sql);
		$num=$PSN->num_rows();
		$total_registros = $num;
		$total_paginas = ceil($total_registros / $registros); 
		
		/* SELECCIÓN REAL */		
		$sql= "SELECT sms_grupos.*, count(sms_asociacion.id_usuario) as conteo ";
		$sql.=" FROM sms_grupos LEFT JOIN sms_asociacion ON sms_asociacion.id_grupo = sms_grupos.id ";
		$sql.=" GROUP BY sms_grupos.id ORDER BY nombre asc";
		$sql.=" LIMIT ".$inicio.", ".$registros;;
		
		$PSN->query($sql);
		$num=$PSN->num_rows();

		?><div class="container">
            <form action="index.php" name="form" id="form" method="get" class="form-horizontal">
    	    <input type="hidden" name="doc" value="sms_usuarios" />
        	<input type="hidden" name="opc" value="2" />
			
            <div class="form-group">
                <h2 class="text-center well">.FILTROS DE BUSQUEDA - GRUPOS.</h2>
            </div>
                
        <div class="container">
        <div class="row">
            <h2 class="text-center well">.Se encontraron <?=intval($num); ?> registros.</h2>
        </div>

        <table border="0" cellspacing="0" cellpadding="2"  align="center" class="table table-bordered" style="font-size:12px">
            <thead>
			<tr>
			  <th align="center">No.</th>
			  <th align="center">Nombre</th>
			  <th align="center">Descripción</th>
			  <th align="center">Usuarios</th>
			</tr>
            </thead>
            <tbody><?
			if($num > 0)
			{
				$izq = 1;
				$contador = $inicio+1;
				while($PSN->next_record())
				{
					?>
					<tr <? if($contador%2==0){ ?>bgcolor="#EEEEEE"<? } ?>>
						<td><?=$contador; ?></td>
						<td><?
						?><a href="index.php?doc=sms_grupos&opc=2&id=<?=$PSN->f('id');?>"><strong><?=$PSN->f('nombre');?></strong></a></td>
					  <td><?=$PSN->f('descripcion');?></td>
					  <td align="center"><a href="index.php?doc=contactos&opc=2&bus_grupo=<?=$PSN->f('id');?>"><?=$PSN->f('conteo');?></a></td>
					</tr>
					<?
					$contador++;
				}
			}		
			else
			{
				?><tr>
				  <td colspan="10" align="center"><h2>.No hay grupos.</h2></td>
				</tr><?
			}	
			?></tbody>
            </table>
		</div>
                
        <br />
        <center>
        <div class="container">
            <ul class="pagination">
                <?
                //
                $paginaActualTxT = "&pagina=".$pagina;
                $_SERVER['REQUEST_URI'] = str_replace($paginaActualTxT,"", $_SERVER['REQUEST_URI']);
                //
                if(($pagina - 1) > 0)
                {
                    echo "<li><a href='".$_SERVER['REQUEST_URI']."&pagina=".($pagina-1)."'>&laquo;</a></li>"; 
                }

                for ($i=1; $i<=$total_paginas; $i++)
                { 
                    if ($pagina == $i)
                    {
                        echo "<li class='active'><a href='".$_SERVER['REQUEST_URI']."&pagina=$i'>$i</a>"; 
                    }
                    else 
                    { 
                        echo "<li><a href='".$_SERVER['REQUEST_URI']."&pagina=$i'>$i</a></li>";
                    } 
                }

                if(($pagina + 1)<=$total_paginas)
                { 
                    echo "<li><a href='".$_SERVER['REQUEST_URI']."&pagina=".($pagina+1)."'>&raquo;</a></li>"; 
                }
                ?>
            </ul>
        </div>                
		<?
	}
}
?>