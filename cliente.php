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

//
//	PARAMETROS CONFIGURABLES
//
$tablaConsulta = "cliente";
$webArchivo = "cliente";
$nombreConsulta = "PROSPECTOS";

$PSN1 = new DBbase_Sql;
if($opc == 1)
{
		if(isset($_POST["nombre"]))
		{
			$PSN = new DBbase_Sql;

			$sql= "SELECT ".$tablaConsulta.".id";
			$sql.=" FROM ".$tablaConsulta;
			$sql.=" WHERE ".$tablaConsulta.".nombre = '".eliminarInvalidos($_REQUEST["nombre"])."'";
			$PSN->query($sql);
			$num=$PSN->num_rows();
			if($num > 0)
			{
				if($PSN->next_record())
				{
					?>
					<SCRIPT LANGUAGE="JavaScript">
					alert("YA EXISTE ESE NOMBRE EN EL SISTEMA, SERÁ AHORA DIRIGIDO AL MISMO");
					window.location.href= "index.php?doc=".$webArchivo."&opc=2&id=<?=$PSN->f('id'); ?>";
					</script>
					<?php
				}
			}
			else
			{
				$nombre = eliminarInvalidos($_POST["nombre"]);
				$idComercial = soloNumeros($_POST["idComercial"]);
				$nit = eliminarInvalidos($_POST["nit"]);
				$representante_legal = eliminarInvalidos($_POST["representante_legal"]);
				$direccion = eliminarInvalidos($_POST["direccion"]);
				$telefono = soloNumeros($_POST["telefono"]);
				$celular = soloNumeros($_POST["celular"]);
				$email = eliminarInvalidos($_POST["email"]);
				$estado = soloNumeros($_POST["estado"]);
				$tipoCliente = soloNumeros($_POST["tipoCliente"]);
				$observaciones = eliminarInvalidos($_POST["observaciones"]);
				$paginaweb = eliminarInvalidos($_POST["paginaweb"]);
				
				$creacionUsuario = $_SESSION["id"];
				//$audit_usuario = $_SESSION["id"];
				//$audit_ip = $_SERVER['REMOTE_ADDR'];
				$sql = 'insert into '.$tablaConsulta.' (
                    idComercial,
					nombre,  
					nit,  
					representante_legal,  
					direccion,  
					telefono,  
					celular,  
					email,  
					estado,  
					tipoCliente,  
					observaciones,
					paginaweb,
					creacionUsuario,
					creacionFecha
				) ';

				$sql .= 'values (
					"'.$idComercial.'", 
					"'.$nombre.'", 
					"'.$nit.'", 
					"'.$representante_legal.'", 
					"'.$direccion.'", 
					"'.$telefono.'", 
					"'.$celular.'", 
					"'.$email.'", 
					"'.$estado.'", 
					"'.$tipoCliente.'", 
					"'.$observaciones.'", 
					"'.$paginaweb.'", 
					"'.$creacionUsuario.'", 
					NOW()
				)';

				$ultimoQuery = $PSN->query($sql);
				$ultimoId = $PSN->ultimoId();

				?>
				<SCRIPT LANGUAGE="JavaScript">
				alert("Se ha creado correctamente el registro.");
				window.location.href= "index.php?doc=<?=$webArchivo; ?>&opc=2&id=<?=$ultimoId; ?>";
				</script>
				<?php
			}
		}
		else
		{
			$PSN = new DBbase_Sql;
			?>
			<div class="container">
            <form method="post" enctype="multipart/form-data" name="form1" id="form1" class="form-horizontal">

                <div class="form-group">
                    <h2 class="alert alert-info text-center">.CREACION DE <?=$nombreConsulta; ?> EN EL SISTEMA.</h2>
                </div>
                
                
              <div class="form-group">
                    <label class="control-label col-sm-2" for="tipoCliente">Tipo de ministerio</label>
                    <div class="col-sm-4">
                    <select name="tipoCliente" class="form-control">
                        <?php
                        $PSNTEMP = new DBbase_Sql;
                        $sql= "SELECT categorias.* ";
                        $sql.=" FROM categorias ";					
                        $sql.=" WHERE idSec = 15 ";
                        $sql.=" ORDER BY descripcion asc ";

                        $PSNTEMP->query($sql);
                        $num=$PSNTEMP->num_rows();
                        if($num > 0)
                        {
                            while($PSNTEMP->next_record())
                            {
                                ?><option value="<?=$PSNTEMP->f('id'); ?>" <?php
                                if(soloNumeros($_POST['tipoCliente']) == $PSNTEMP->f('id'))
                                {
                                    ?>selected="selected"<?php 
                                }
                                ?>><?=$PSNTEMP->f('descripcion'); ?><br /><?php
                            }
                        }
                        ?>
                        </select>
    				  </div>
                  
                  
                    <label class="control-label col-sm-2" for="idComercial">Facilitador encargado</label>
                    <div class="col-sm-4">
                    <select name="idComercial" id="idComercial" class="form-control">
                        <?php
                        $PSNTEMP = new DBbase_Sql;
                        $sql= "SELECT id, nombre ";
                        $sql.=" FROM usuario ";					
                        $sql.=" WHERE tipo = 163 ";
                        //Si es un comercial solo puede agregar prospectos de si mismo
                        if($_SESSION["perfil"] == 163){
                            $sql.=" AND id = '".$_SESSION["id"]."'";
                        }
                        $sql.=" ORDER BY nombre asc ";
                        /*
                        *
                        */

                        $PSNTEMP->query($sql);
                        $num=$PSNTEMP->num_rows();
                        if($num > 0)
                        {
                            while($PSNTEMP->next_record())
                            {
                                ?><option value="<?=$PSNTEMP->f('id'); ?>" <?php
                                if(soloNumeros($_POST['idComercial']) == $PSNTEMP->f('id'))
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
					<label class="control-label col-sm-2" for="nombre">Nombre</label>
				  <div class="col-sm-4">
					  <input name="nombre" id="nombre" type="text" maxlength="250" value="<?=eliminarInvalidos($_POST['nombre']); ?>" class="form-control" />
					</div>

                  <label class="control-label col-sm-2" for="nit">NIT/CC</label>
				  <div class="col-sm-4">
					  <input name="nit" type="text" maxlength="250" value="<?=eliminarInvalidos($_POST['nit']); ?>" class="form-control" />
					</div>
				</div>

              <div class="form-group">
					<label class="control-label col-sm-2" for="representante_legal">Representante Legal</label>
				    <div class="col-sm-4">
					  <input name="representante_legal" type="text" maxlength="250" value="<?=eliminarInvalidos($_POST['representante_legal']); ?>" class="form-control" />
					</div>

                    <label class="control-label col-sm-2" for="direccion">Direcci&oacute;n</label>
                      <div class="col-sm-4">
                         <input name="direccion" type="text" maxlength="250" value="<?=eliminarInvalidos($_POST['direccion']); ?>"  class="form-control"/>
                        </div>
				</div>

              <div class="form-group">
					<label class="control-label col-sm-2" for="telefono">Tel&eacute;fono</label>
				  <div class="col-sm-4">
					 <input name="telefono" type="text" maxlength="250" value="<?=soloNumeros($_POST['telefono']); ?>" class="form-control" />
					</div>
					<label class="control-label col-sm-2" for="celular">Celular</label>
				  <div class="col-sm-4">
					 <input name="celular" type="text" maxlength="250" value="<?=soloNumeros($_POST['celular']); ?>" class="form-control" />
					</div>
				</div>

              <div class="form-group">
					<label class="control-label col-sm-2" for="email">Email</label>
				  <div class="col-sm-4">
					 <input name="email" type="text" maxlength="250" value="<?=eliminarInvalidos($_POST['email']); ?>" class="form-control" />
					</div>
					<label class="control-label col-sm-2" for="estado">Estado</label>
				  <div class="col-sm-4">
					 <select name="estado" class="form-control"><option value="1" <?php if($_POST['estado'] == 1){ echo 'selected="selected="'; } ?>>Activo</option><option value="0">Inactivo</option></select>
					</div>
				</div>

              <div class="form-group">
					<label class="control-label col-sm-2" for="observaciones">Observaciones</label>
				  <div class="col-sm-4">
					<input name="observaciones" type="text" maxlength="250" value="<?=eliminarInvalidos($_POST['observaciones']); ?>" class="form-control" />
					</div>
					<label class="control-label col-sm-2" for="paginaweb">P&aacute;gina Web</label>
				  <div class="col-sm-4">
					<input name="paginaweb" type="text" maxlength="250" value="<?=eliminarInvalidos($_POST['paginaweb']); ?>" class="form-control" />
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
                        if(confirm("Esta accion generara el REGISTRO en el sistema, esta seguro que desea continuar?"))
                        {
                            if(document.getElementById('nombre').value != "")
                            {
                                document.getElementById('funcion').value = "insertar";
                                return true;
                            }
                            else
                            {
                                alert("La informacion es primordial para brindarle un excelente servicio, por favor digite al menos el campo de NOMBRE");
                                return false;
                            }
                        }else{
                            return false;
                        }
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
				<?php
		}
}
else
{
	if(isset($_GET["id"]))
	{
		$PSN = new DBbase_Sql;
		if(isset($_POST["nombre"]))
		{
			/*
			*	ACTUALIZAR
			*/
			if($_POST["funcion"] == "actualizar")
			{
				$nombre = eliminarInvalidos($_POST["nombre"]);
                $idComercial = soloNumeros($_POST["idComercial"]);
				$nit = eliminarInvalidos($_POST["nit"]);
				$representante_legal = eliminarInvalidos($_POST["representante_legal"]);
				$direccion = eliminarInvalidos($_POST["direccion"]);
				$telefono = soloNumeros($_POST["telefono"]);
				$celular = soloNumeros($_POST["celular"]);
				$email = eliminarInvalidos($_POST["email"]);
				$estado = soloNumeros($_POST["estado"]);
				$tipoCliente = soloNumeros($_POST["tipoCliente"]);
				$observaciones = eliminarInvalidos($_POST["observaciones"]);				
				$paginaweb = eliminarInvalidos($_POST["paginaweb"]);
				
				//
				$modUsuario = $_SESSION["id"];
				//$modFecha;
				
				$sql = 'update '.$tablaConsulta.' set 
                            idComercial = "'.$idComercial.'",
							nombre ="'.$nombre.'"
							,nit ="'.$nit.'"
							,representante_legal ="'.$representante_legal.'"
							,direccion ="'.$direccion.'"
							,telefono ="'.$telefono.'"
							,celular ="'.$celular.'"
							,email ="'.$email.'"
							,estado ="'.$estado.'"
							,tipoCliente ="'.$tipoCliente.'"
							,observaciones ="'.$observaciones.'"
							,paginaweb ="'.$paginaweb.'"
							,modUsuario ="'.$modUsuario.'"
							,modFecha = NOW() where id='.soloNumeros($_GET["id"]);				
				$PSN->query($sql);
	
				?>
				<SCRIPT LANGUAGE="JavaScript">
				alert("Se ha ACTUALIZADO correctamente el registro!");
				window.location.href= "index.php?doc=<?=$webArchivo; ?>&opc=2&id=<?=soloNumeros($_GET["id"]); ?>";
				</script>
				<?php
			}
			/*
			*	ELIMINAR
			*/
			else if($_POST["funcion"] == "eliminar" && soloNumeros ($_GET["id"]) != "" && soloNumeros($_GET["id"]) > 0)
			{
				$sql = 'UPDATE '.$tablaConsulta.' SET estado = 0, modFecha = NOW(), modUsuario = "'.$_SESSION["id"].'" WHERE id = "'.soloNumeros($_GET["id"]).'"';
				$PSN->query($sql);
				?>
				<SCRIPT LANGUAGE="JavaScript">
				alert("Se ha ELIMINADO correctamente el registro!");
				window.location.href= "index.php?doc=<?=$webArchivo; ?>&opc=2&id=<?=soloNumeros($_GET["id"]); ?>";
				</script>
				<?php
			}
		}
		else
		{
			$sql= "SELECT ".$tablaConsulta.".*, usuario.nombre as  creacionUsuarioNom, modif.nombre as modUsuarioNom";
			$sql.=" FROM ".$tablaConsulta;
				$sql.=" LEFT JOIN usuario ON usuario.id = ".$tablaConsulta.".creacionUsuario ";
				$sql.=" LEFT JOIN usuario modif ON modif.id = ".$tablaConsulta.".modUsuario ";			
			$sql.=" WHERE ".$tablaConsulta.".id='".soloNumeros($_GET["id"])."'";
            //Si es un comercial solo puede agregar prospectos de si mismo
            if($_SESSION["perfil"] == 163){
                $sql.=" AND idComercial = '".$_SESSION["id"]."'";
            }
			
			$PSN->query($sql);
			$num=$PSN->num_rows();
			if($num > 0)
			{
				$izq = 1;
				if($PSN->next_record())
				{
					$PSN2 = new DBbase_Sql;
 			?><div class="container">
	            <form method="post" enctype="multipart/form-data" name="form1" id="form1" class="form-horizontal">
				
                <div class="form-group">
                    <h2 class="alert alert-info text-center">.ACTUALIZACI&Oacute;N DE <?=$nombreConsulta; ?> EN EL SISTEMA.</h2>
                </div>
	            <?php
				/*	
					if($PSN->f('estado') == 2){
						?><tr height="50px" bgcolor="#900"><td colspan="4"><center><h3 style="color:#fff">.ESTE REGISTRO HA SIDO ELIMINADO DE LA BASE PRINCIPAL, SOLO ESTA DISPONIBLE PARA CONSULTA PERO NO AFECTA LOS VALORES DEL TABLERO DE CONTROL NI LAS CONSULTAS PRINCIPALES.</h3></center></td></tr><?php
					}	
				*/
				?><div class="form-group">
					<label class="control-label col-sm-2" for="tipoCliente">Tipo</label>
				  <div class="col-sm-4">
					<select name="tipoCliente" class="form-control">
						<?php
						$PSNTEMP = new DBbase_Sql;
						$sql= "SELECT categorias.* ";
						$sql.=" FROM categorias ";					
						$sql.=" WHERE idSec = 15 ";
						$sql.=" ORDER BY descripcion asc ";

						$PSNTEMP->query($sql);
						$num=$PSNTEMP->num_rows();
						if($num > 0)
						{
							while($PSNTEMP->next_record())
							{
								?><option value="<?=$PSNTEMP->f('id'); ?>" <?php
								if($PSN->f('tipoCliente') == $PSNTEMP->f('id'))
								{
									?>selected="selected"<?php 
								}
                                ?>><?=$PSNTEMP->f('descripcion'); ?></option><?php
							}
						}
						?>
						</select>
				  </div>

                    <label class="control-label col-sm-2" for="idComercial">Facilitador encargado</label>
                    <div class="col-sm-4">
                    <select name="idComercial" id="idComercial" class="form-control">
                        <?php
                        $PSNTEMP = new DBbase_Sql;
                        $sql= "SELECT id, nombre ";
                        $sql.=" FROM usuario ";					
                        $sql.=" WHERE tipo = 163 ";
                        //Si es un comercial solo puede agregar prospectos de si mismo
                        if($_SESSION["perfil"] == 163){
                            $sql.=" AND id = '".$_SESSION["id"]."'";
                        }
                        $sql.=" ORDER BY nombre asc ";
                        /*
                        *
                        */

                        $PSNTEMP->query($sql);
                        $num=$PSNTEMP->num_rows();
                        if($num > 0)
                        {
                            while($PSNTEMP->next_record())
                            {
                                ?><option value="<?=$PSNTEMP->f('id'); ?>" <?php
                                if($PSN->f('idComercial') == $PSNTEMP->f('id'))
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
					<label class="control-label col-sm-2" for="nombre">Nombre</label>
    				  <div class="col-sm-4">
					  <input name="nombre" id="nombre" type="text" maxlength="250" value="<?=$PSN->f('nombre'); ?>" class="form-control" />
					</div>

                  <label class="control-label col-sm-2" for="nit">NIT/CC</label>
				  <div class="col-sm-4">
					  <input name="nit" type="text" maxlength="250" value="<?=$PSN->f('nit'); ?>" class="form-control" />
					</div>
				</div>


                    
                <div class="form-group">
					<label class="control-label col-sm-2" for="representante_legal">Pastor principal</label>
				  <div class="col-sm-4">
					  <input name="representante_legal" type="text" maxlength="250" value="<?=$PSN->f('representante_legal'); ?>" class="form-control" />
					</div>
					<label class="control-label col-sm-2" for="direccion">Direcci&oacute;n</label>
				  <div class="col-sm-4">
					 <input name="direccion" type="text" maxlength="250" value="<?=$PSN->f('direccion'); ?>" class="form-control" />
					</div>
				</div>

              <div class="form-group">
					<label class="control-label col-sm-2" for="telefono">Tel&eacute;fono</label>
				  <div class="col-sm-4">
					 <input name="telefono" type="text" maxlength="250" value="<?=$PSN->f('telefono'); ?>" class="form-control" />
					</div>
					<label class="control-label col-sm-2" for="celular">Celular</label>
				  <div class="col-sm-4">
					 <input name="celular" type="text" maxlength="250" value="<?=$PSN->f('celular'); ?>" class="form-control" />
					</div>
				</div>

                  <div class="form-group">
					<label class="control-label col-sm-2" for="email">Email</label>
				  <div class="col-sm-4">
					 <input name="email" type="text" maxlength="250" value="<?=$PSN->f('email'); ?>"class="form-control" />
					</div>
					<label class="control-label col-sm-2" for="estado">Estado</label>
				  <div class="col-sm-4">
					 <select name="estado"class="form-control"><option value="1" <?php if($PSN->f('estado') == 1){ echo 'selected="selected="'; } ?>>Activo</option><option value="0">Inactivo</option></select>
					</div>
                </div>

              <div class="form-group">
					<label class="control-label col-sm-2" for="observaciones">Observaciones</label>
				  <div class="col-sm-4">
					<input name="observaciones" type="text" maxlength="250" value="<?=$PSN->f('observaciones'); ?>"class="form-control" />
					</div>
					<label class="control-label col-sm-2" for="paginaweb">P&aacute;gina Web</label>
				  <div class="col-sm-4">
					<input name="paginaweb" type="text" maxlength="250" value="<?=$PSN->f('paginaweb'); ?>"class="form-control" />
					</div>
				</div>		
												

                <div class="form-group">
                  <div class="col-sm-2">
						<strong>Usuario que digito el registro</strong>
					</div>
					<div class="col-sm-4">
						<?=$PSN->f('creacionUsuarioNom'); ?>
					</div>
                  <div class="col-sm-2">
						<strong>Fecha de creaci&oacute;n del registro</strong>
					</div>
					<div class="col-sm-4">
						<?=$PSN->f('creacionFecha'); ?>
					</div>
				</div>


                <div class="form-group">
                      <div class="col-sm-2">
						<strong>Usuario que realizo ultima modificaci&oacute;n</strong>
					</div>
					<div class="col-sm-4">
						<?=$PSN->f('modUsuarioNom'); ?>
					</div>
                      <div class="col-sm-2">
						<strong>Ultima modificaci&oacute;n Fecha</strong>
					</div>
					<div class="col-sm-4">
						<?=$PSN->f('modFecha'); ?>
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
                        if(confirm("Esta accion actualizara el REGISTRO en el sistema, esta seguro que desea continuar?"))
                        {
                            if(document.getElementById('nombre').value != "")
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


                    function generarFormDel(){
                            if(confirm("Esta accion ELIMINARA el REGISTRO en el sistema, esta seguro que desea continuar?"))
                            {
                                document.getElementById('funcion').value = "eliminar";
                                document.getElementById('form1').submit();
                            }
                    }

                    function init(){
                        document.getElementById('form1').onsubmit = function(){
                                return generarForm();
                        }
                    }

                    function regresar()
                    {
                        window.location.href = "index.php?doc=cliente";
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
				?><div class="row"><h2><font color="#FF0000">ID Incorrecto. No Existe o no esta autorizado para visualizar la misma.</font></h2></div><?php
			}	
		}
	}
	else
	{
		/********************************************************************************
		*********************************************************************************
		*****************				¡¡¡CONSULTA!!!
		*****************				¡¡¡CONSULTA!!!
		*********************************************************************************
		*********************************************************************************/
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
        //
        if($_SESSION["perfil"] != 163){
            $sqlB= "SELECT ".$tablaConsulta.".*, categorias.descripcion as nomTipoCliente, usuario.nombre as nombre_comercial";
            $sqlB.=" FROM ".$tablaConsulta;
                $sqlB.=" LEFT JOIN categorias ON categorias.id = ".$tablaConsulta.".tipoCliente ";
                $sqlB.=" LEFT JOIN usuario ON usuario.id = ".$tablaConsulta.".idComercial ";
            $sqlB.=" WHERE 1 ";
        }
        else{
            //
            $sqlB= "SELECT ".$tablaConsulta.".*, categorias.descripcion as nomTipoCliente";
            $sqlB.=" FROM ".$tablaConsulta;
                $sqlB.=" LEFT JOIN categorias ON categorias.id = ".$tablaConsulta.".tipoCliente ";
            $sqlB.=" WHERE idComercial = '".$_SESSION["id"]."' ";
            //
        }
        
		$sqlC= "SELECT count(".$tablaConsulta.".id) as conteo"; // sum(".$tablaConsulta.".valor_prima_neta) as total_neto";
		$sqlC.=" FROM ".$tablaConsulta;
		$sqlC.=" WHERE 1 ";
        
		if(eliminarInvalidos($_GET["nombre"]) != "")
		{
			$sql.=" AND ".$tablaConsulta.".nombre  LIKE '%".eliminarInvalidos($_GET["nombre"])."%'";
		}

		if(eliminarInvalidos($_GET["nit"]) != "")
		{
			$sql.=" AND ".$tablaConsulta.".nit  LIKE '%".eliminarInvalidos($_GET["nit"])."%'";
		}

		if(eliminarInvalidos($_GET["representante_legal"]) != "")
		{
			$sql.=" AND ".$tablaConsulta.".representante_legal  LIKE '%".eliminarInvalidos($_GET["representante_legal"])."%'";
		}

		if(eliminarInvalidos($_GET["direccion"]) != "")
		{
			$sql.=" AND ".$tablaConsulta.".direccion  LIKE '%".eliminarInvalidos($_GET["direccion"])."%'";
		}

		if(soloNumeros($_GET["telefono"]) != "")
		{
			$sql.=" AND ".$tablaConsulta.".telefono LIKE '%".soloNumeros($_GET["telefono"])."%'";
		}
		
		if(soloNumeros($_GET["celular"]) != "")
		{
			$sql.=" AND ".$tablaConsulta.".celular LIKE '%".soloNumeros($_GET["celular"])."%'";
		}
		
		if(eliminarInvalidos($_GET["email"]) != "")
		{
			$sql.=" AND ".$tablaConsulta.".email LIKE '%".eliminarInvalidos($_GET["email"])."%'";
		}

		if(soloNumeros($_GET["estado"]) != "")
		{
			$sql.=" AND ".$tablaConsulta.".estado LIKE '%".soloNumeros($_GET["estado"])."%'";
		}

		if(soloNumeros($_GET["tipoCliente"]) != "")
		{
			$sql.=" AND ".$tablaConsulta.".tipoCliente LIKE '%".soloNumeros($_GET["tipoCliente"])."%'";
		}
        
		if(soloNumeros($_GET["idComercial"]) != "")
		{
			$sql.=" AND ".$tablaConsulta.".idComercial = '".soloNumeros($_GET["idComercial"])."'";
		}
        

		/*
		if(eliminarInvalidos($_GET["fechaInicial"]) != "")
		{
			$sql.=" and principal.fecha_expedicion >= '".eliminarInvalidos($_GET["fechaInicial"])."'";
			$fechaInicial = eliminarInvalidos($_GET["fechaInicial"]);
		}

		if(eliminarInvalidos($_GET["fechaFinal"]) != "")
		{
			$sql.=" and principal.fecha_expedicion <= '".eliminarInvalidos($_GET["fechaFinal"])."'";
			$fechaFinal = eliminarInvalidos($_GET["fechaFinal"]);
		}
		*/
		
		$sql.=" ORDER BY ".$tablaConsulta.".nombre ASC";
		$PSN->query($sqlC.$sql);
		$num=$PSN->num_rows();
		if($num > 0)
		{
			if($PSN->next_record()){
				$num = $PSN->f('conteo');
				$numTotal = $PSN->f('conteo');
			}
		}

		$total_registros = $num;
		$total_paginas = ceil($total_registros / $registros); 

		$sql.=" LIMIT ".$inicio.", ".$registros;;
		$PSN->query($sqlB.$sql);
        //echo $sqlB.$sql;
		$numclientes=$PSN->num_rows();
		?>
		<div class="container">
            <form action="index.php" name="form" id="form" method="get" class="form-horizontal">
    	    <input type="hidden" name="doc" value="<?=$webArchivo; ?>" />
	        <input type="hidden" name="opc" value="2" />
			
            <div class="form-group">
                <h2 class="text-center well">.FILTROS DE BUSQUEDA - <?=$nombreConsulta; ?>.</h2>
            </div>
                
                
            <div class="form-group">
				<label class="control-label col-sm-2" for="nombre">Nombre</label>
				<div class="col-sm-4">
					<input type="text" name="nombre" id="nombre" value="<?=eliminarInvalidos($_GET["nombre"]); ?>" class="form-control" />
				</div>
				<label class="control-label col-sm-2" for="nit">NIT</label>
				<div class="col-sm-4">
					<input type="text" name="nit" id="nit" value="<?=eliminarInvalidos($_GET["nit"]); ?>" class="form-control" />
				</div>
			</div>

            <div class="form-group">
				<label class="control-label col-sm-2" for="tipoCliente">Tipo de ministerio</label>
				<div class="col-sm-4">
					<select name="tipoCliente" class="form-control">
					<option value="">Todos</option>
					<?php
					$PSNTEMP = new DBbase_Sql;
					$sql= "SELECT categorias.* ";
						$sql.=" FROM categorias ";
						$sql.=" WHERE idSec = 15 ";
					$sql.=" ORDER BY descripcion asc ";

					$PSNTEMP->query($sql);
					$num=$PSNTEMP->num_rows();
					if($num > 0)
					{
						while($PSNTEMP->next_record())
						{
							?><option value="<?=$PSNTEMP->f('id'); ?>" <?php
							if(soloNumeros($_GET["tipoCliente"]) == $PSNTEMP->f('id'))
							{
								?>selected="selected"<?php 
							}
							?>><?=$PSNTEMP->f('descripcion'); ?></option><?php
						}
					}
					?>
					</select>
				</div>
                
                <label class="control-label col-sm-2" for="idComercial">Comercial encargado</label>
                <div class="col-sm-4">
                <select name="idComercial" id="idComercial" class="form-control">
                    <option value="">Todos</option>
                    <?php
                    $PSNTEMP = new DBbase_Sql;
                    $sql= "SELECT id, nombre ";
                    $sql.=" FROM usuario ";					
                    $sql.=" WHERE tipo = 163 ";
                    //Si es un comercial solo puede agregar prospectos de si mismo
                    if($_SESSION["perfil"] == 163){
                        $sql.=" AND id = '".$_SESSION["id"]."'";
                    }
                    $sql.=" ORDER BY nombre asc ";
                    /*
                    *
                    */

                    $PSNTEMP->query($sql);
                    $num=$PSNTEMP->num_rows();
                    if($num > 0)
                    {
                        while($PSNTEMP->next_record())
                        {
                            ?><option value="<?=$PSNTEMP->f('id'); ?>" <?php
                            if(soloNumeros($_REQUEST['idComercial']) == $PSNTEMP->f('id'))
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

            <div class="row text-center">
                <input type="submit" value="Buscar" class="btn btn-success" />
            </div>

            </form>
		</div>
		

    <div class="container">
    <div class="row">
        <h2 class="text-center well">.Se encontraron <?=intval($numTotal); ?> registros.</h2>
    </div>

    <table border="0" cellspacing="0" cellpadding="2"  align="center" class="table table-bordered" style="font-size:12px">
        <thead>
		<tr>
			<th align="center">No.</th>
            <?php
            if($_SESSION["perfil"] != 163){
                ?><th align="center">Comercial Encargado</th><?php
            }
            ?>
            
			<th align="center">Nombre</th>
			<th align="center">NIT</th>
			<th align="center">Pastor principal</th>
			<th align="center">Direcci&oacute;n</th>
			<th align="center">Tel&eacute;fono</th>
			<th align="center">Celular</th>
			<th align="center">Email</th>
			<th align="center">Estado</th>
			<th align="center">Tipo de Ministerio</th>
		</tr>
        </thead>
        <tbody>
            <?php
			if($numclientes > 0)
			{
				$izq = 1;
				$contador = $inicio+1;
				while($PSN->next_record())
				{
				$fechaVencimiento = date("Y-m-d", strtotime("+364 days", strtotime($PSN->f('fecha_vigencia_ini'))));
					?>
					<tr <?php if($contador%2==0){ ?>bgcolor="#EEEEEE"<?php } ?>>
						<td><a href="index.php?doc=<?=$webArchivo; ?>&opc=2&id=<?=$PSN->f('id');?>"><?=$contador; ?></a></td>
                        <?php
                        if($_SESSION["perfil"] != 163){
                            ?><td><a href="index.php?doc=<?=$webArchivo; ?>&opc=2&id=<?=$PSN->f('id');?>"><?=$PSN->f('nombre_comercial');?><?php
                        }
                        ?>
                        <td><a href="index.php?doc=<?=$webArchivo; ?>&opc=2&id=<?=$PSN->f('id');?>"><?=$PSN->f('nombre');?></td></a>
						<td><a href="index.php?doc=<?=$webArchivo; ?>&opc=2&id=<?=$PSN->f('id');?>"><?=$PSN->f('nit');?></a></td>
						<td><?=$PSN->f('representante_legal');?></td>
						<td><?=$PSN->f('direccion');?></td>
						<td><?=$PSN->f('telefono');?></td>
						<td><?=$PSN->f('celular');?></td>
						<td><?=$PSN->f('email');?></td>
						<td><?php if($PSN->f('estado') == 1){ echo "Activo"; }else{ echo "Inactivo"; };?></td>
						<td><?=$PSN->f('nomTipoCliente');?></td>
					</tr>
					<?php
					$contador++;
				}
			}		
			else
			{
				?><tr>
				  <td colspan="11" align="center"><h2>.No hay registros.</h2></td>
				</tr><?php
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