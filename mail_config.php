<?php
/*
*	LOGUEO
*/
if($_SESSION["perfil"] != 1 && $_SESSION["perfil"] != 2 && $_SESSION["perfil"] != 161 && $_SESSION["perfil"] != 162 && $_SESSION["perfil"] != 163)
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
$tablaConsulta = "mail_config";
$webArchivo = "mail_config";
$nombreConsulta = "CONFIGURACI&Oacute;N DE CORREO";

$PSN1 = new DBbase_Sql;
if($opc == 1)
{
		if(isset($_POST["nombre"]))
		{
			$PSN = new DBbase_Sql;

			$sql= "SELECT ".$tablaConsulta.".id";
			$sql.=" FROM ".$tablaConsulta;
			$sql.=" WHERE ".$tablaConsulta.".usuario = '".eliminarInvalidos($_REQUEST["usuario"])."'";
			$PSN->query($sql);
			$num=$PSN->num_rows();
			if($num > 0)
			{
				if($PSN->next_record())
				{
					?>
					<SCRIPT LANGUAGE="JavaScript">
					alert("YA EXISTE ESE USUARIO DE CORREO EN EL SISTEMA, SERÁ AHORA DIRIGIDO AL MISMO");
					window.location.href= "index.php?doc=".$webArchivo."&opc=2&id=<?=$PSN->f('id'); ?>";
					</script>
					<?php
				}
			}
			else
			{
				//
				$nombre = eliminarInvalidos($_POST["nombre"]);
				$host = eliminarInvalidos($_POST["host"]);
				$usuario = eliminarInvalidos($_POST["usuario"]);
				$password = eliminarInvalidos($_POST["password"]);
				$puerto = soloNumeros($_POST["puerto"]);
				$correo = eliminarInvalidos($_POST["correo"]);			
				$defecto = soloNumeros($_POST["defecto"]);
				//
				$creacionUsuario = $_SESSION["id"];
				//$audit_usuario = $_SESSION["id"];
				//$audit_ip = $_SERVER['REMOTE_ADDR'];
				$sql = 'insert into '.$tablaConsulta.' (
					nombre,  
					host,  
					usuario,  
					password,  
					puerto,  
					correo,
					defecto,
					audit_usuario,
					audit_fecha 
				) ';

				$sql .= 'values (
					"'.$nombre.'", 
					"'.$host.'", 
					"'.$usuario.'", 
					"'.$password.'", 
					"'.$puerto.'", 
					"'.$correo.'", 
					"'.$defecto.'", 
					"'.$creacionUsuario.'", 
					NOW()
				)';

				$ultimoQuery = $PSN->query($sql);
				$ultimoId =  $PSN->ultimoId();
				//
				if($defecto == 1 && $ultimoId > 0){
					$sql = 'UPDATE '.$tablaConsulta.' SET defecto = 0 WHERE id != "'.$ultimoId.'"';
					$PSN->query($sql);
				}
				//
                ?><div class="container">
                    <div class="row">
                        <h2 class="alert alert-info text-center">.CREACIÓN DE CONFIGURACI&Oacute;N DE CONFIGURACI&Oacute;N DE CORREO.</h2>
                    </div>

                    <div class="row">
                        <h5 class="alert alert-warning text-center">Se ha creado correctamente el registro, en breve será redirigido, si no es redirigido de <a href="index.php?doc=<?=$webArchivo; ?>&opc=2&id=<?=$ultimoId; ?>">clic aquí</a>.</h5>
                    </div>
                </div>
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
			?><div class="container">

            <div class="form-group">
                <h2 class="alert alert-info text-center">.CREACIÓN DE <?=$nombreConsulta; ?>.</h2>
            </div>

            <form method="post" enctype="multipart/form-data" name="form1" id="form1" class="form-horizontal">

                <div class="form-group">
                    <label class="control-label col-sm-2"  for="nombre">Nombre</label>
                    <div class="col-sm-4"  class="col-sm-4">
                    <input name="nombre" id="nombre" type="text" maxlength="250" value="<?=eliminarInvalidos($_POST['nombre']); ?>" class="form-control" required />
                    </div>
					<label class="control-label col-sm-2"  for="correo">Correo</label>
				  <div class="col-sm-4">
					  <input name="correo" id="correo" type="text" maxlength="100" value="<?=eliminarInvalidos($_POST['correo']); ?>" class="form-control" required />
					</div>
				</div>
				
				
              <div class="form-group">
					<label class="control-label col-sm-2"  for="host">Host</label>
				  <div class="col-sm-4">
					  <input name="host" id="host" type="text" maxlength="50" value="<?=eliminarInvalidos($_POST['host']); ?>" class="form-control" required />
					</div>
					<label class="control-label col-sm-2"  for="puerto">Puerto</label>
				  <div class="col-sm-4">
					  <input name="puerto" id="puerto" type="number" maxlength="10" value="<?=soloNumeros($_POST['puerto']); ?>" class="form-control" required />
					</div>
				</div>
							
				
                <div class="form-group">
                    <label class="control-label col-sm-2"  for="usuario">Usuario</label>
                    <div class="col-sm-4">
                    <input name="usuario" id="usuario" type="text" maxlength="100" value="<?=eliminarInvalidos($_POST['usuario']); ?>" class="form-control" required />
                    </div>
                    <label class="control-label col-sm-2"  for="password">Password</label>
                    <div class="col-sm-4">
                    <input name="password" id="password" type="text" maxlength="255" value="<?=eliminarInvalidos($_POST['password']); ?>" class="form-control" required />
                    </div>
                </div>

                <div class="form-group">
                    <label class="control-label col-sm-2"  for="defecto">Usar esta cuenta por defecto</label>
                    <div class="col-sm-4" style="text-align:left">
                    <input type="checkbox" name="defecto" value="1" <? if($_POST['defecto'] == 1){ echo 'checked'; } ?> style="text-align:left" class="form-control"  />
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
                        if(
                            document.getElementById('nombre').value != "" && 
                            document.getElementById('correo').value != "" && 
                            document.getElementById('host').value != "" && 
                            document.getElementById('puerto').value != "" && 
                            document.getElementById('usuario').value != "" && 
                            document.getElementById('password').value != "" 
                          )
                        {
                            document.getElementById('funcion').value = "insertar";
                        }
                        else
                        {
                            return false;
                            alert("La informacion es primordial para brindarle un excelente servicio, por favor digite todos los campos");
                        }
                    }
                    else{
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
                //                
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
				//
				$nombre = eliminarInvalidos($_POST["nombre"]);
				$host = eliminarInvalidos($_POST["host"]);
				$usuario = eliminarInvalidos($_POST["usuario"]);
				$password = eliminarInvalidos($_POST["password"]);
				$puerto = soloNumeros($_POST["puerto"]);
				$correo = eliminarInvalidos($_POST["correo"]);			
				$defecto = soloNumeros($_POST["defecto"]);
				//
				$modUsuario = $_SESSION["id"];
				//$modFecha;
				
				$sql = 'update '.$tablaConsulta.' set 
							nombre ="'.$nombre.'"
							,host ="'.$host.'"
							,usuario ="'.$usuario.'"
							,password ="'.$password.'"
							,puerto ="'.$puerto.'"
							,defecto ="'.$defecto.'"
							,audit_usuario ="'.$modUsuario.'"
							,audit_fecha = NOW() where id='.soloNumeros($_GET["id"]);				
				$PSN->query($sql);
				//
				if($defecto == 1){
					$sql = 'UPDATE '.$tablaConsulta.' SET defecto = 0 WHERE id != "'.soloNumeros($_GET["id"]).'"';
					$PSN->query($sql);
				}
				//

				?><div class="container">
                    <div class="form-group">
                        <h2 class="alert alert-info text-center">.ACTUALIZACIÓN DE <?=$nombreConsulta; ?>.</h2>
                    </div>

                    <div class="form-group">
                        <h5 class="alert alert-warning text-center">Se ha ACTUALIZADO correctamente el registro, en breve será redirigido, si no es redirigido de <a href="index.php?doc=<?=$webArchivo; ?>&opc=2&id=<?=$ultimoId; ?>">clic aquí</a>.</h5>
                    </div>
                </div>
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
				/*
				$sql = 'UPDATE '.$tablaConsulta.' SET estado = 0, modFecha = NOW(), modUsuario = "'.$_SESSION["id"].'" WHERE id = "'.soloNumeros($_GET["id"]).'"';
				$PSN->query($sql);
				?>
				<SCRIPT LANGUAGE="JavaScript">
				alert("Se ha ELIMINADO correctamente el registro!");
				window.location.href= "index.php?doc=<?=$webArchivo; ?>&opc=2&id=<?=soloNumeros($_GET["id"]); ?>";
				</script>
				<?php
				*/
			}
		}
		else
		{
			$sql= "SELECT ".$tablaConsulta.".*";
			$sql.=" FROM ".$tablaConsulta;
			$sql.=" WHERE ".$tablaConsulta.".id='".soloNumeros($_GET["id"])."'";
			
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
                    <h2 class="alert alert-info text-center">.ACTUALIZACI&Oacute;N DE CONFIGURACI&Oacute;N DE CORREO.</h2>
                </div>

                <form method="post" enctype="multipart/form-data" name="form1" id="form1" class="form-horizontal">

	            <?php
				/*	
					if($PSN->f('estado') == 2){
						?><tr height="50px" bgcolor="#900"><td colspan="4"><center><h3 style="color:#fff">.ESTE REGISTRO HA SIDO ELIMINADO DE LA BASE PRINCIPAL, SOLO ESTA DISPONIBLE PARA CONSULTA PERO NO AFECTA LOS VALORES DEL TABLERO DE CONTROL NI LAS CONSULTAS PRINCIPALES.</h3></center></td></tr><?php
					}	
				*/
				?>
				
				
                <div class="form-group">
                    <label class="control-label col-sm-2"  for="nombre">Nombre</label>
                    <div class="col-sm-4">
                        <input name="nombre" id="nombre" type="text" maxlength="250" value="<?=$PSN->f('nombre'); ?>" class="form-control" required />
                    </div>
					<label class="control-label col-sm-2"  for="correo">Correo</label>
                    <div class="col-sm-4">
                        <input name="correo" id="correo" type="text" maxlength="100" value="<?=$PSN->f('correo'); ?>" class="form-control" required />
                    </div>
				</div>
				
				
                <div class="form-group">
                    <label class="control-label col-sm-2"  for="host">Host</label>
                    <div class="col-sm-4">
                        <input name="host" id="host" type="text" maxlength="50" value="<?=$PSN->f('host'); ?>" class="form-control" required />
                    </div>
                    <label class="control-label col-sm-2"  for="puerto">Puerto</label>
                    <div class="col-sm-4">
                        <input name="puerto" id="puerto" type="number" maxlength="10" value="<?=$PSN->f('puerto'); ?>" class="form-control" required />
                    </div>
                </div>


                <div class="form-group">
                    <label class="control-label col-sm-2"  for="usuario">Usuario</label>
                    <div class="col-sm-4">
                        <input name="usuario" id="usuario" type="text" maxlength="100" value="<?=$PSN->f('usuario'); ?>" class="form-control" required />
                    </div>
                    <label class="control-label col-sm-2"  for="password">Password</label>
                    <div class="col-sm-4">
                    <input name="password" id="password" type="text" maxlength="255" value="<?=$PSN->f('password'); ?>" class="form-control" required />
                    </div>
                </div>


                <div class="form-group">
                    <label class="control-label col-sm-2"  for="estado">Usar esta cuenta por defecto</label>
                    <div class="col-sm-4" style="text-align:left">
                        <input type="checkbox" name="defecto" value="1" <? if($PSN->f('defecto') == 1){ echo 'checked'; } ?> style="text-align:left" class="form-control" />
                    </div>
                </div>
				
				
                <div class="row text-center">
					<input type="hidden" name="funcion" id="funcion" value="" />
                    <input type="submit" value="Guardar cambios" class="btn btn-success" /> 
                    <a href="index.php?doc=main" class="btn btn-danger">Cerrar</a>
				</div>
				
				<!--<br />
				<center><input type="button" name="button" onclick="generarFormDel()" value="Eliminar Registro" /></center>//-->
                    
				</form>
				</div>


                <script language="javascript">
                function generarForm(){
                        if(confirm("Esta accion actualizara el REGISTRO en el sistema, esta seguro que desea continuar?"))
                        {
                            if(
                                document.getElementById('nombre').value != "" && 
                                document.getElementById('host').value != "" && 
                                document.getElementById('usuario').value != "" && 
                                document.getElementById('password').value != "" && 
                                document.getElementById('puerto').value != "" && 
                                document.getElementById('correo').value != ""  
                            )
                            {
                                document.getElementById('funcion').value = "actualizar";
                            }
                            else
                            {
                                alert("La informacion es primordial para brindarle un excelente servicio, por favor digite todos los campos de configuración");
                                return false;
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
                            return false;
                    }
                }

                function regresar()
                {
                    window.location.href = "index.php?doc=mail_config";
                }

                window.onload = function(){
                    init();
                }
                </script>
                <?php
			}
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
		$sqlB= "SELECT ".$tablaConsulta.".*";
		$sqlB.=" FROM ".$tablaConsulta;
		$sqlB.=" WHERE 1 ";

		//
		$sqlC= "SELECT count(".$tablaConsulta.".id) as conteo"; // sum(".$tablaConsulta.".valor_prima_neta) as total_neto";
		$sqlC.=" FROM ".$tablaConsulta;
		$sqlC.=" WHERE 1 ";


		if(eliminarInvalidos($_GET["nombre"]) != "")
		{
			$sql.=" AND ".$tablaConsulta.".nombre  LIKE '%".eliminarInvalidos($_GET["nombre"])."%'";
		}

		
		$sql.=" GROUP BY ".$tablaConsulta.".id ORDER BY ".$tablaConsulta.".nombre ASC";
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
		$num=$PSN->num_rows();
		?>
        <div class="container">
        <form action="index.php" name="form" id="form" method="get" class="form-horizontal">
            <input type="hidden" name="doc" value="<?=$webArchivo; ?>" />
            <input type="hidden" name="opc" value="2" />

            <div class="form-group">
                <h2 class="text-center well">.FILTROS DE BUSQUEDA - <?=$nombreConsulta; ?>.</h2>
            </div>

            <div class="form-group">
                <label class="control-label col-sm-2"  for="nombre">Nombre</label>
                <div class="col-sm-4">
                    <input type="text" name="nombre" id="nombre" value="<?=eliminarInvalidos($_GET["nombre"]); ?>" class="form-control" />
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
			<th align="center">No.</th>
			<th align="center">Nombre</th>
			<th align="center">Host</th>
			<th align="center">Correo</th>
        </thead>
        <tbody>
		</tr><?php
			if($num > 0)
			{
				$izq = 1;
				$contador = $inicio+1;
				while($PSN->next_record())
				{
					?>
					<tr <? if($contador%2==0){ ?>bgcolor="#EEEEEE"<? } ?>>
						<td><a href="index.php?doc=<?=$webArchivo; ?>&opc=2&id=<?=$PSN->f('id');?>"><?=$contador; ?></a></td>
						<td><a href="index.php?doc=<?=$webArchivo; ?>&opc=2&id=<?=$PSN->f('id');?>"><?=$PSN->f('nombre');?></a></td>
						<td><?=$PSN->f('host');?></td>
						<td><?=$PSN->f('correo');?></td>
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
        </center><?php
	}
}
?>