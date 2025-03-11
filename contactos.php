<?php
/*
*	LOGUEO
*/
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
if($opc == 1)
{
		if(isset($_POST["nombres"]))
		{
			$PSN = new DBbase_Sql;
			$nombres = eliminarInvalidos($_POST["nombres"]);
			$email = eliminarInvalidos($_POST["email"]);
			$celular = soloNumeros($_POST["celular"]);
			$celular2 = soloNumeros($_POST["celular2"]);
			$notas = eliminarInvalidos($_POST["notas"]);
			$audit_usuario = $_SESSION["id"];
			$audit_ip = $_SERVER['REMOTE_ADDR'];
			//
			$idCliente = soloNumeros($_POST["idCliente"]);
			$identificacion = eliminarInvalidos($_POST["identificacion"]);
			$tipoIdentificacion = soloNumeros($_POST["tipoIdentificacion"]);
			$direccion = eliminarInvalidos($_POST["direccion"]);
			$paginaWeb = eliminarInvalidos($_POST["paginaWeb"]);
			$telfijo = eliminarInvalidos($_POST["telfijo"]);
			//
			$sql = 'insert into sms_usuarios (
				idCliente,
				nombres,
				email,
				telfijo,
				celular,
				celular2,
				identificacion,
				tipoIdentificacion,
				direccion,
				paginaWeb,
				notas,
				audit_usuario,
				audit_fecha,
				audit_ip
			) ';

			$sql .= 'values (
				"'.$idCliente.'",
				"'.$nombres.'",
				"'.$email.'",
				"'.$telfijo.'",
				"'.$celular.'",
				"'.$celular2.'",
				"'.$identificacion.'",
				"'.$tipoIdentificacion.'",
				"'.$direccion.'",
				"'.$paginaWeb.'",
				"'.$notas.'",
				"'.$audit_usuario.'",
				NOW(),
				"'.$audit_ip.'"
			)';

			$ultimoQuery = $PSN->query($sql);
			$ultimoId = $PSN->ultimoId();

			if(isset($_POST['listaasociar']))
			{
				if (is_array($_POST['listaasociar']))
				{
					foreach($_POST['listaasociar'] as $value)
					{
						if(soloNumeros($value) != "")
						{
							$sql = "REPLACE INTO sms_asociacion (id_grupo, id_usuario) VALUES (".soloNumeros($value).",".$ultimoId.")";
							$ultimoQuery = $PSN->query($sql);
						}
					}
				}
				else
				{
					//echo $value;
				}
			}


			?>
			<SCRIPT LANGUAGE="JavaScript">
			alert("Se ha creado correctamente el contacto!!!");
			window.location.href= "index.php?doc=contactos&opc=2&id=<?=$ultimoId; ?>";
			</script>
			<?php
		}
		else
		{
			$PSN = new DBbase_Sql;
			?><div class="container">
            <form method="post" enctype="multipart/form-data" name="form1" id="form1" class="form-horizontal">

    <div class="row">
        <h2 class="alert alert-info text-center">.CREACI&Oacute;N DE CONTACTO.</h2>
    </div>

                
    <ul class="nav nav-tabs">
        <li class="active"><a data-toggle="tab" href="#general">General</a></li>
        <li><a data-toggle="tab" href="#grupos">Grupos para envio</a></li>
    </ul>


    <div class="row">
    <div class="tab-content">

        <div id="general" class="tab-pane fade in active">

            <div class="row">
                <h2 class="text-center well">.INFORMACI&Oacute;N GENERAL.</h2>
            </div>

				<div id="container">
				<div class="form-group">
				    <label class="control-label col-sm-2" for="idCliente" class="form-control">Prospecto al que pertenece</label>
					<div class="col-sm-4">
						<select name="idCliente" class="form-control">
							<option value="0">Sin seleccionar</option>
						<?php
						$PSNTEMP = new DBbase_Sql;
						$sql= "SELECT cliente.id, cliente.nombre ";
							$sql.=" FROM cliente ";
                        //Si es un comercial solo puede agregar prospectos de si mismo
                        if($_SESSION["perfil"] == 163){
                            $sql.=" WHERE cliente.idComercial = '".$_SESSION["id"]."'";
                        }
                        //
						$sql.=" ORDER BY nombre asc ";

						$PSNTEMP->query($sql);
						$num=$PSNTEMP->num_rows();
						if($num > 0)
						{
							while($PSNTEMP->next_record())
							{
								?><option value="<?=$PSNTEMP->f('id'); ?>" <?php
								if(soloNumeros($_POST["idCliente"]) == $PSNTEMP->f('id'))
								{
									?>selected="selected"<?php 
								}
								?>><?=$PSNTEMP->f('nombre'); ?></option><?php
							}
						}
						?>
						</select>
					</div>
				</div>

				<div class="form-group">
						<label class="control-label col-sm-2" for="nombres">Nombre</label>
					<div class="col-sm-4">
						<input name="nombres" type="text" id="nombres" maxlength="250" value="<?=eliminarInvalidos($_POST["nombres"]); ?>" class="form-control" />
					</div>
						<label class="control-label col-sm-2" for="identificacion">Identificaci&oacute;n</label>
					<div class="col-sm-4">
						<input name="identificacion" type="text" id="identificacion" maxlength="250" value="<?=eliminarInvalidos($_POST["identificacion"]); ?>" class="form-control" />
					</div>
				</div>

				<div class="form-group">
						<label class="control-label col-sm-2" for="tipoIdentificacion">Tipo de identificaci&oacute;n</label>
					<div class="col-sm-4">
						<select name="tipoIdentificacion" class="form-control">
							<?php
							$PSNTEMP = new DBbase_Sql;
							$sql= "SELECT categorias.* ";
							$sql.=" FROM categorias ";					
							$sql.=" WHERE idSec = 2 ";
							$sql.=" ORDER BY descripcion asc ";

							$PSNTEMP->query($sql);
							$num=$PSNTEMP->num_rows();
							if($num > 0)
							{
								while($PSNTEMP->next_record())
								{
									?><option value="<?=$PSNTEMP->f('id'); ?>" <?php
									if(soloNumeros($_POST['tipoIdentificacion']) == $PSNTEMP->f('id'))
									{
										?>selected="selected"<?php 
									}
									?>><?=$PSNTEMP->f('descripcion'); ?><br /><?php
								}
							}
							?>
							</select>
					</div>
						<label class="control-label col-sm-2" for="direccion">Direcci&oacute;n</label>
					<div class="col-sm-4">
						<input name="direccion" type="text" id="direccion" maxlength="250" value="<?=eliminarInvalidos($_POST["direccion"]); ?>" class="form-control" />
					</div>
				</div>

				<div class="form-group">
						<label class="control-label col-sm-2" for="paginaWeb">Pagina Web</label>
					<div class="col-sm-4">
						<input name="paginaWeb" type="text" id="paginaWeb" maxlength="250" value="<?=eliminarInvalidos($_POST["paginaWeb"]); ?>" class="form-control" />
					</div>
						<label class="control-label col-sm-2" for="email">Email</label>
					<div class="col-sm-4">
						<input name="email" type="text" id="email" maxlength="250" value="<?=eliminarInvalidos($_POST["email"]); ?>" class="form-control" />
					</div>
				</div>

				<div class="form-group">
						<label class="control-label col-sm-2" for="celular">Celular</label>
					<div class="col-sm-4">
						<input name="celular" type="text" id="celular" maxlength="250" value="<?=soloNumeros($_POST["celular"]); ?>" class="form-control" />
					</div>
						<label class="control-label col-sm-2" for="telfijo">Tel&eacute;fono fijo</label>
					<div class="col-sm-4">
						<input name="telfijo" type="text" id="telfijo" maxlength="250" value="<?=soloNumeros($_POST["telfijo"]); ?>" class="form-control" />
					</div>
				</div>

				<div class="form-group">
						<label class="control-label col-sm-2" for="notas">Notas</label>
					<div class="col-sm-4">
						<input name="notas" type="text" id="notas" maxlength="255" value="<?=eliminarInvalidos($_POST["notas"]); ?>" class="form-control" />
					</div>
				</div>

            </div>
		</div>
        
        <div id="grupos" class="tab-pane fade">
        
				<div class="form-group">
                    <h2 class="text-center well">.Seleccione los grupos a los que pertenecer&aacute; el contacto.</h2>
				</div>

				<div class="form-group">
                    <?php
                    $sql= "SELECT sms_grupos.* ";
                    $sql.=" FROM sms_grupos";
                    $sql.=" ORDER BY nombre asc ";

                    $PSN->query($sql);
                    $num=$PSN->num_rows();
                    if($num > 0)
                    {
                        while($PSN->next_record())
                        {
                            ?><div class="col-sm-4">
                                <input type="checkbox" name="listaasociar[]" value="<?=$PSN->f('id'); ?>" class="form-control" /><?=$PSN->f('nombre'); ?>
                            </div><?php
                        }
                    }
				?>
				</div>
        </div>

        <div class="row text-center">
            <input type="hidden" name="funcion" id="funcion" value="" />
            <input type="submit" value="Guardar cambios" class="btn btn-success" /> <a href="index.php?doc=main" class="btn btn-danger">Cerrar</a>
        </div>
        </form>

            </div>
                
            <script language="javascript">

				
				function generarForm(){
						if(confirm("Esta accion generara el CONTACTO en el sistema, esta seguro que desea continuar?"))
						{
							if(document.getElementById('nombres').value != "")
							{
								document.getElementById('funcion').value = "insertar";
								//document.getElementById('form1').submit();
								return true;
							}
							else
							{
								alert("La informacion es primordial para brindarle un excelente servicio, por favor digite al menos el campo de NOMBRE");
								//return false;
							}
						}
						else{
    						//return false;
						}
                        return false;
				}
				
				function init(){
					// Get the element with id="defaultOpen" and click on it
					document.getElementById('form1').onsubmit = function(){
							return generarForm();
					}

					
					<?php
					if($varExitoUSU == 1)
					{
						?>alert("Se ha colocado correctamente el CONTACTO, espere mientras es dirigido.");
						window.location.href = "index.php?doc=contactos&id=<?=$ultimoId;?>&opc=2";<?php
					}
					?>
				}
				
				window.onload = function(){
					init();
				}
			</script><?php
		}
}
else
{
	if(isset($_GET["id"]))
	{
		$PSN = new DBbase_Sql;
		if(isset($_POST["nombres"]))
		{
			/*
			*	ACTUALIZAR
			*/
			if($_POST["funcion"] == "actualizar")
			{
				$idCliente = soloNumeros($_POST["idCliente"]);
				$nombres = eliminarInvalidos($_POST["nombres"]);
				$email = eliminarInvalidos($_POST["email"]);
				$celular = soloNumeros($_POST["celular"]);
				$celular2 = soloNumeros($_POST["celular2"]);
				$audit_usuario = $_SESSION["id"];
				$audit_ip = $_SERVER['REMOTE_ADDR'];
				$idActual = soloNumeros($_GET["id"]);
				//
				$identificacion = eliminarInvalidos($_POST["identificacion"]);
				$tipoIdentificacion = soloNumeros($_POST["tipoIdentificacion"]);
				$direccion = eliminarInvalidos($_POST["direccion"]);
				$paginaWeb = eliminarInvalidos($_POST["paginaWeb"]);
				$telfijo = soloNumeros($_POST["telfijo"]);
				$notas = eliminarInvalidos($_POST["notas"]);
				//

				$sql = 'update sms_usuarios set 
					idCliente="'.$idCliente.'", 
					nombres="'.$nombres.'", 
					identificacion="'.$identificacion.'", 
					tipoIdentificacion="'.$tipoIdentificacion.'", 
					direccion="'.$direccion.'", 
					paginaWeb="'.$paginaWeb.'", 
					email="'.$email.'", 
					telfijo="'.$telfijo.'", 
					celular="'.$celular.'", 
					celular2="'.$celular2.'", 
					notas="'.$notas.'", 
					audit_usuario="'.$audit_usuario.'", 
					audit_ip="'.$audit_ip.'", 
					audit_fecha = NOW()
					where id='.soloNumeros($_GET["id"]);
				$PSN->query($sql);
	
				$sql = 'delete from sms_asociacion where id_usuario='.soloNumeros($_GET["id"]);
				$PSN->query($sql);
				if(isset($_POST['listaasociar']))
				{
					if (is_array($_POST['listaasociar']))
					{
						foreach($_POST['listaasociar'] as $value)
						{
							if(soloNumeros($value) != "")
							{
								$sql = "REPLACE INTO sms_asociacion (id_grupo, id_usuario) VALUES (".soloNumeros($value).",".$idActual.")";
								$ultimoQuery = $PSN->query($sql);
							}
						}
					}
					else
					{
						//echo $value;
					}
				}
	
				?>
				<SCRIPT LANGUAGE="JavaScript">
				alert("Se ha ACTUALIZADO correctamente el contacto!");
				window.location.href= "index.php?doc=contactos&opc=2&id=<?=soloNumeros($_GET["id"]); ?>";
				</script>
				<?php
			}
			/*
			*	ELIMINAR
			*/
			else if($_POST["funcion"] == "eliminar" && soloNumeros ($_GET["id"]) != "" && soloNumeros($_GET["id"]) > 0)
			{
				if($_SESSION["perfil"] == 1)
				{
					$sql = 'delete from sms_asociacion where id_usuario = '.soloNumeros($_GET["id"]);
					$PSN->query($sql);
					$sql = 'delete from sms_usuarios where id = '.soloNumeros($_GET["id"]);
					$PSN->query($sql);
					?>
					<SCRIPT LANGUAGE="JavaScript">
						alert("Se ha ELIMINADO el contacto!");
						window.location.href= "index.php?doc=contactos&opc=2";
					</script>
					<?php
				}
			}
		}
		else
		{
			$sql= "SELECT sms_usuarios.* ";
			$sql.=" FROM sms_usuarios";
			$sql.=" WHERE id=".soloNumeros($_GET["id"]);
            $sql.=" ORDER BY nombres asc ";
			
			$PSN->query($sql);
			$num=$PSN->num_rows();
			if($num > 0)
			{
				$izq = 1;
				if($PSN->next_record())
				{
					$PSN2 = new DBbase_Sql;
 			?><form method="post" enctype="multipart/form-data" name="form1" id="form1" class="form-horizontal">

            <div class="form-group">
                <h2 class="alert alert-info text-center">.ACTUALIZACI&Oacute;N DE CONTACTOS.</h2>
            </div>                

            <ul class="nav nav-tabs">
                <li class="active"><a data-toggle="tab" href="#general">General</a></li>
                <li><a data-toggle="tab" href="#grupos">Grupos para envio</a></li>
            </ul>


            <div class="row">
            <div class="tab-content">

                <div id="general" class="tab-pane fade in active">

                <div class="row">
                    <h2 class="text-center well">.INFORMACI&Oacute;N GENERAL.</h2>
                </div>

				<div id="container">                
					<div class="form-group">
						<label class="control-label col-sm-2" for="idCliente">Prospecto al que pertenece</label>
                        <div class="col-sm-4">
						<select name="idCliente" class="form-control" >
							<option value="0">Sin seleccionar</option>
						<?php
						$PSNTEMP = new DBbase_Sql;
						$sql= "SELECT cliente.id, cliente.nombre ";
							$sql.=" FROM cliente ";
						$sql.=" ORDER BY nombre asc ";

						$PSNTEMP->query($sql);
						$num=$PSNTEMP->num_rows();
						if($num > 0)
						{
							while($PSNTEMP->next_record())
							{
								?><option value="<?=$PSNTEMP->f('id'); ?>" <?php
								if($PSN->f('idCliente') == $PSNTEMP->f('id'))
								{
									?>selected="selected"<?php 
								}
								?>><?=$PSNTEMP->f('nombre'); ?></option><?php
							}
						}
						?>
						</select>
					</div>
				</div>

                              
                <div class="form-group">
					<label class="control-label col-sm-2" for="nombres">Nombre</label>
				<div class="col-sm-4">
					<input name="nombres" type="text" id="nombres" maxlength="250" value="<?=$PSN->f('nombres'); ?>" class="form-control"  />
				</div>
					<label class="control-label col-sm-2" for="identificacion">Identificaci&oacute;n</label>
				<div class="col-sm-4">
					<input name="identificacion" type="text" id="identificacion" maxlength="250" value="<?=$PSN->f('identificacion'); ?>" class="form-control"  />
				</div>
			</div>

                <div class="form-group">
					<label class="control-label col-sm-2" for="tipoIdentificacion">Tipo de identificaci&oacute;n</label>
				<div class="col-sm-4">
					<select name="tipoIdentificacion" class="form-control">
						<?php
						$PSNTEMP = new DBbase_Sql;
						$sql= "SELECT categorias.* ";
						$sql.=" FROM categorias ";					
						$sql.=" WHERE idSec = 2 ";
						$sql.=" ORDER BY descripcion asc ";

						$PSNTEMP->query($sql);
						$num=$PSNTEMP->num_rows();
						if($num > 0)
						{
							while($PSNTEMP->next_record())
							{
								?><option value="<?=$PSNTEMP->f('id'); ?>" <?php
								if($PSN->f('tipoIdentificacion') == $PSNTEMP->f('id'))
								{
									?>selected="selected"<?php 
								}
								?>><?=$PSNTEMP->f('descripcion'); ?><br /><?php
							}
						}
						?>
						</select>
				</div>
                <label class="control-label col-sm-2" for="direccion">Direcci&oacute;n</label>
				<div class="col-sm-4">
					<input name="direccion" type="text" id="direccion" maxlength="250" value="<?=$PSN->f('direccion'); ?>" class="form-control"  />
				</div>
			</div>

            <div class="form-group">
					<label class="control-label col-sm-2" for="paginaWeb">Pagina Web</label>
				<div class="col-sm-4">
					<input name="paginaWeb" type="text" id="paginaWeb" maxlength="250" value="<?=$PSN->f('paginaWeb'); ?>" class="form-control"  />
				</div>
					<label class="control-label col-sm-2" for="email">Email</label>
				<div class="col-sm-4">
					<input name="email" type="text" id="email" maxlength="250" value="<?=$PSN->f('email'); ?>" class="form-control" />
				</div>
			</div>

		
            <div class="form-group">
                <label class="control-label col-sm-2" for="celular">Celular</label>
				<div class="col-sm-4">
					<input name="celular" type="text" id="celular" maxlength="250" value="<?=$PSN->f('celular'); ?>" class="form-control" />
				</div>
					<label class="control-label col-sm-2" for="telfijo">Tel&eacute;fono fijo</label>
				<div class="col-sm-4">
					<input name="telfijo" type="text" id="telfijo" maxlength="250" value="<?=$PSN->f('telfijo'); ?>" class="form-control" />
				</div>
			</div>

            <div class="form-group">
					<label class="control-label col-sm-2" for="notas">Notas</label>
				<div class="col-sm-4">
					<input name="notas" type="text" id="notas" maxlength="250" value="<?=$PSN->f('notas'); ?>" class="form-control" />
				</div>
			</div>


            </div>
		</div>
        
        <div id="grupos" class="tab-pane fade">
        
            <div class="form-group">
                <h2 class="text-center well">.Seleccione los grupos a los que pertenecer&aacute; el contacto.</h2>
            </div>

            <div class="form-group">
				<?php
					$sql= "SELECT sms_grupos.*, sms_asociacion.id_usuario ";
					$sql.=" FROM sms_grupos LEFT JOIN sms_asociacion ON id_grupo = sms_grupos.id AND id_usuario = ".$PSN->f('id');
					$sql.=" ORDER BY nombre asc ";
					
					$PSN->query($sql);
					$num=$PSN->num_rows();
					if($num > 0)
					{
						while($PSN->next_record())
						{
							?><div class="col-sm-4">
                           		<input type="checkbox" name="listaasociar[]" value="<?=$PSN->f('id'); ?>" <?php
								if($PSN->f('id_usuario') != "")
								{
									?>checked="checked"<?php 
								}
								?> class="form-control"><?=$PSN->f('nombre'); ?>
							</div><?php
						}
					}
					?>
			</div>
        </div>

                
        <div class="row text-center">
            <input type="hidden" name="funcion" id="funcion" value="" />
            <input type="submit" value="Guardar cambios" class="btn btn-success" /> <a href="index.php?doc=main" class="btn btn-danger">Cerrar</a>
        </div>
                
        <?php
        if($_SESSION["perfil"] == 1)
        {
            ?><div class="row"><input type="button" name="button" onclick="generarFormDel()" class="btn btn-danger" value="Eliminar Contacto" /></div><?php
        }
        ?>
        </form>
               
			<script language="javascript">
                function generarForm(){
                    if(confirm("Esta accion actualizara el CONTACTO en el sistema, ¿esta seguro que desea continuar?"))
                    {
                        if(document.getElementById('nombres').value != "" 
                        )
                        {
                            document.getElementById('funcion').value = "actualizar";
                            return true;
                        }
                        else
                        {
                            alert("La informacion es primordial para brindarle un excelente servicio, por favor digite al menos el campo de USUARIO");
                        }
                    }
                    return false;
                }
				function regresar()
				{
					window.location.href = "index.php?doc=contactos";
				}
				
				<?php
				if($_SESSION["perfil"] == 1)
				{
					?>
                function generarFormDel(){
                        if(confirm("Esta accion ELIMINARA el CONTACTO en el sistema, esta seguro que desea continuar?"))
                        {
							document.getElementById('funcion').value = "eliminar";
							document.getElementById('form1').submit();
                        }
                }
					<?php
				}
				?>
                function init(){
                    document.getElementById('form1').onsubmit = function(){
                            return generarForm();
					}
				}
                
                
                window.onload = function(){
                    init();
                }
                </script>
			<?php
				}
			}		
			else
			{
				?><h2><font color="#FF0000">ID Incorrecto. No Existe o no esta autorizado para visualizar la misma.</font></h2><?php
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

		$sql= "SELECT sms_usuarios.*, cliente.nombre as nomcliente ";
		$sql.=" FROM sms_usuarios";
		$sql.=" LEFT JOIN cliente ON cliente.id = sms_usuarios.idCliente";
		//
		if(soloNumeros($_GET["bus_grupo"]) != "")
		{
			$sql.=", sms_asociacion ";
		}

		$sql.=" WHERE 1 ";
        
        //Si es un comercial solo puede agregar prospectos de si mismo
        if($_SESSION["perfil"] == 163){
            $sql.=" AND cliente.idComercial = '".$_SESSION["id"]."'";
        }

		if(soloNumeros($_GET["bus_grupo"]) != "")
		{
			$sql.=" AND sms_asociacion.id_usuario = sms_usuarios.id ";
			$sql.=" AND sms_asociacion.id_grupo = '".soloNumeros($_GET["bus_grupo"])."' ";
		}

		if($_GET["bus_nombres"] != "")
		{
			$sql.=" AND sms_usuarios.nombres LIKE '%".eliminarInvalidos($_GET["bus_nombres"])."%'";
		}

		if($_GET["bus_email"] != "")
		{
			$sql.=" AND sms_usuarios.email LIKE '%".eliminarInvalidos($_GET["bus_email"])."%'";
		}

		if($_GET["bus_celular"] != "")
		{
			$sql.=" AND sms_usuarios.celular LIKE '%".soloNumeros($_GET["bus_celular"])."%'";
		}

		if($_GET["idCliente"] != "")
		{
			$sql.=" AND sms_usuarios.idCliente = '".soloNumeros($_GET["idCliente"])."'";
		}

		$sql.=" ORDER BY nombres asc";
		$PSN->query($sql);
		$num=$PSN->num_rows();

		$total_registros = $num;
		$total_paginas = ceil($total_registros / $registros); 

		$sql.=" LIMIT ".$inicio.", ".$registros;;
		$PSN->query($sql);
		$num=$PSN->num_rows();
		?><div class="container">
            <form action="index.php" name="form" id="form" method="get" class="form-horizontal">
    	    <input type="hidden" name="doc" value="contactos" />
        	<input type="hidden" name="opc" value="2" />

            <div class="form-group">
                <h2 class="text-center well">.FILTROS DE BUSQUEDA - <?=$nombreConsulta; ?>.</h2>
            </div>

            <div class="form-group">
                <label class="control-label col-sm-2" for="idCliente">Iglesia al que pertenece</label>
				<div class="col-sm-4">
					<select name="idCliente" class="form-control">
						<option value="">Todos</option>
					<?php
					$PSNTEMP = new DBbase_Sql;
					$sql= "SELECT cliente.id, cliente.nombre ";
						$sql.=" FROM cliente ";
                    //Si es un comercial solo puede agregar prospectos de si mismo
                    if($_SESSION["perfil"] == 163){
                        $sql.=" WHERE cliente.idComercial = '".$_SESSION["id"]."'";
                    }
        
					$sql.=" ORDER BY nombre asc ";

					$PSNTEMP->query($sql);
					$numT=$PSNTEMP->num_rows();
					if($numT > 0)
					{
						while($PSNTEMP->next_record())
						{
							?><option value="<?=$PSNTEMP->f('id'); ?>" <?php
							if(soloNumeros($_GET["idCliente"]) == $PSNTEMP->f('id'))
							{
								?>selected="selected"<?php 
							}
							?>><?=$PSNTEMP->f('nombre'); ?></option><?php
						}
					}
					?>
					</select>
				</div>

                <label class="control-label col-sm-2" for="bus_grupo">Grupo</label>
				<div class="col-sm-4">
					<select name="bus_grupo" class="form-control">
						<option value="">Todos</option>
						<?php
						$PSNTEMP = new DBbase_Sql;
						$sql= "SELECT sms_grupos.* ";
						$sql.=" FROM sms_grupos ";
						$sql.=" ORDER BY nombre asc ";

						$PSNTEMP->query($sql);
						$numT=$PSNTEMP->num_rows();
						if($numT > 0)
						{
							while($PSNTEMP->next_record())
							{
								?><option value="<?=$PSNTEMP->f('id'); ?>" <?php
								if(soloNumeros($_GET["bus_grupo"]) == $PSNTEMP->f('id'))
								{
									?>selected="selected"<?php 
								}
								?>><?=$PSNTEMP->f('nombre'); ?><br /><?php
							}
						}
						?>
					</select>
				</div>
			</div>   
			                
            <div class="form-group">
                <label class="control-label col-sm-2" for="bus_nombres">Nombre</label>
				<div class="col-sm-4">
					<input type="text" name="bus_nombres" id="bus_nombres" value="<?=eliminarInvalidos($_GET["bus_nombres"]); ?>" class="form-control" />
				</div>
                <label class="control-label col-sm-2" for="bus_email">Email</label>
				<div class="col-sm-4">
					<input type="text" name="bus_email" id="bus_email" value="<?=eliminarInvalidos($_GET["bus_email"]); ?>" class="form-control" />
				</div>
			</div>                  

                
			<div class="form-group">
                <label class="control-label col-sm-2" for="bus_celular">Celular</label>
				<div class="col-sm-4">
					<input type="number" name="bus_celular" id="bus_celular" value="<?=soloNumeros($_GET["bus_celular"]); ?>" class="form-control" />
				</div>
			</div>                  

            <div class="row text-center">
                <input type="submit" value="Buscar" class="btn btn-success" />
            </div>
        </form>

        <div class="container">
        <div class="row">
            <h2 class="text-center well">.Se encontraron <?=$numTotal; ?> registros.</h2>
        </div>

        <table border="0" cellspacing="0" cellpadding="2"  align="center" class="table table-bordered" style="font-size:12px">
        <thead>
		<tr>
		  <th align="center">No.</th>
		  <th align="center">Nombre</th>
		  <th align="center">E-mail</th>
		  <th align="center">Celular</th>
		</tr>
        </thead>
        <tbody<<?php
			if($num > 0)
			{
				$izq = 1;
				$contador = $inicio+1;
				while($PSN->next_record())
				{
					?>
					<tr <?php if($contador%2==0){ ?>bgcolor="#EEEEEE"<?php } ?>>
						<td><?=$contador; ?></td>
						<td><?php
						?><a href="index.php?doc=contactos&opc=2&id=<?=$PSN->f('id');?>"><strong><?=$PSN->f('nombres'); ?></strong></a><?php
						if($PSN->f('nomcliente')){
							echo "<i> - ".$PSN->f('nomcliente')."</i>";
						}
					?></td>
					  <td><?=$PSN->f('email');?></td>
					  <td><?=$PSN->f('celular');?></td>
					</tr>
					<?php
					$contador++;
				}
			}		
			?>  
        </tbody>
        </table>
		</div>

        <br />
        <center>
        <div class="container">
            <ul class="pagination">
                <?php
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
        </center>
        <?php
	}
}
?>