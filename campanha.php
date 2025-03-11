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
$tablaConsulta = "campana";
$webArchivo = "campanha";
$nombreConsulta = "CAMPA&Ntilde;A";

$PSN1 = new DBbase_Sql;
if($opc == 1)
{
		if(isset($_POST["nombre"]))
		{
			//
			$PSN = new DBbase_Sql;
			//
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
				$fechaInicial = eliminarInvalidos($_POST["fechaInicial"]);
				$fechaFinal = eliminarInvalidos($_POST["fechaFinal"]);
				$estado = soloNumeros($_POST["estado"]);
				$tipoCampana = soloNumeros($_POST["tipoCampana"]);
				$observaciones = eliminarInvalidos($_POST["observaciones"]);			
				$creacionUsuario = $_SESSION["id"];
				//$audit_usuario = $_SESSION["id"];
				//$audit_ip = $_SERVER['REMOTE_ADDR'];
				$sql = 'insert into '.$tablaConsulta.' (
					nombre,  
					fechaInicial,  
					fechaFinal,  
					estado,  
					tipoCampana,  
					observaciones,
					creacionUsuario,
					creacionFecha
				) ';

				$sql .= 'values (
					"'.$nombre.'", 
					"'.$fechaInicial.'", 
					"'.$fechaFinal.'", 
					"'.$estado.'", 
					"'.$tipoCampana.'", 
					"'.$observaciones.'", 
					"'.$creacionUsuario.'", 
					NOW()
				)';

				$ultimoQuery = $PSN->query($sql);
				$ultimoId = mysql_insert_id();

				?><div class="container">
                    <div class="form-group">
                        <h2 class="alert alert-info text-center">.CREACION DE <?=$nombreConsulta; ?> EN EL SISTEMA.</h2>
                    </div>

                    <div class="form-group">
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
					<label class="control-label col-sm-2" for="tipoCampana">Tipo Campa&ntilde;a</label>
				  <div class="col-sm-4">
					<select name="tipoCampana" class="form-control">
						<?php
						$PSNTEMP = new DBbase_Sql;
						$sql= "SELECT categorias.* ";
						$sql.=" FROM categorias ";					
						$sql.=" WHERE idSec = 23 ";
						$sql.=" ORDER BY descripcion asc ";

						$PSNTEMP->query($sql);
						$num=$PSNTEMP->num_rows();
						if($num > 0)
						{
							while($PSNTEMP->next_record())
							{
								?><option value="<?=$PSNTEMP->f('id'); ?>" <?php
								if(soloNumeros($_POST['tipoCampana']) == $PSNTEMP->f('id'))
								{
									?>selected="selected"<?php 
								}
								?>><?=$PSNTEMP->f('descripcion'); ?><br /><?php
							}
						}
						?>
						</select>
				  </div>
					<label class="control-label col-sm-2" for="nombre">Nombre</label>
				  <div class="col-sm-4">
					  <input name="nombre" id="nombre" type="text" maxlength="250" value="<?=eliminarInvalidos($_POST['nombre']); ?>" class="form-control" />
					</div>
				</div>
				
				<div class="form-group">
					<label class="control-label col-sm-2" for="fechaInicial">Fecha Inicial</label>
				  <div class="col-sm-4">
					  <input name="fechaInicial" type="date"  placeholder="AAAA-MM-DD" value="<?=eliminarInvalidos($_POST['fechaInicial']); ?>" class="form-control" />
					</div>
					<label class="control-label col-sm-2" for="fechaInicial">Fecha Final</label>
				  <div class="col-sm-4">
					  <input name="fechaFinal" type="date"  placeholder="AAAA-MM-DD" value="<?=eliminarInvalidos($_POST['fechaFinal']); ?>" class="form-control" />
					</div>
				</div>

				<div class="form-group">
					<label class="control-label col-sm-2" for="estado">Estado</label>
				  <div class="col-sm-4">
					 <select name="estado" class="form-control"><option value="1" <?php if($_POST['estado'] == 1){ echo 'selected="selected="'; } ?>>Activo</option><option value="0">Inactivo</option></select>
					</div>
					<label class="control-label col-sm-2" for="observaciones">Observaciones</label>
				  <div class="col-sm-4">
					<input name="observaciones" type="text" maxlength="250" value="<?=eliminarInvalidos($_POST['observaciones']); ?>" class="form-control" />
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
				$fechaInicial = eliminarInvalidos($_POST["fechaInicial"]);
				$fechaFinal = eliminarInvalidos($_POST["fechaFinal"]);
				$estado = soloNumeros($_POST["estado"]);
				$tipoCampana = soloNumeros($_POST["tipoCampana"]);
				$observaciones = eliminarInvalidos($_POST["observaciones"]);							
				//
				$modUsuario = $_SESSION["id"];
				//$modFecha;
				
				$sql = 'update '.$tablaConsulta.' set 
							nombre ="'.$nombre.'"
							,fechaInicial ="'.$fechaInicial.'"
							,fechaFinal ="'.$fechaFinal.'"
							,estado ="'.$estado.'"
							,tipoCampana ="'.$tipoCampana.'"
							,observaciones ="'.$observaciones.'"
							,modUsuario ="'.$modUsuario.'"
							,modFecha = NOW() where id='.soloNumeros($_GET["id"]);				
				$PSN->query($sql);
	
				?><div class="container">
                    <div class="form-group">
                        <h2 class="alert alert-info text-center">.ACTUALIZACIÓN DE <?=$nombreConsulta; ?> EN EL SISTEMA.</h2>
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

	            <?php
				/*	
					if($PSN->f('estado') == 2){
						?><tr height="50px" bgcolor="#900"><td colspan="4"><center><h3 style="color:#fff">.ESTE REGISTRO HA SIDO ELIMINADO DE LA BASE PRINCIPAL, SOLO ESTA DISPONIBLE PARA CONSULTA PERO NO AFECTA LOS VALORES DEL TABLERO DE CONTROL NI LAS CONSULTAS PRINCIPALES.</h3></center></td></tr><?php
					}	
				*/
				?>

					
				<div class="form-group">
                    <label class="control-label col-sm-2" for="tipoCampana">Tipo Campana</label>
                    <div class="col-sm-4">
					<select name="tipoCampana" class="form-control">
						<?php
						$PSNTEMP = new DBbase_Sql;
						$sql= "SELECT categorias.* ";
						$sql.=" FROM categorias ";					
						$sql.=" WHERE idSec = 23 ";
						$sql.=" ORDER BY descripcion asc ";

						$PSNTEMP->query($sql);
						$num=$PSNTEMP->num_rows();
						if($num > 0)
						{
							while($PSNTEMP->next_record())
							{
								?><option value="<?=$PSNTEMP->f('id'); ?>" <?php
								if($PSN->f('tipoCampana') == $PSNTEMP->f('id'))
								{
									?>selected="selected"<?php 
								}
								?>><?=$PSNTEMP->f('descripcion'); ?><br /><?php
							}
						}
						?>
						</select>
				  </div>
					<label class="control-label col-sm-2" for="nombre">Nombre</label>
				  <div class="col-sm-4">
					  <input name="nombre" id="nombre" type="text" maxlength="250" value="<?=$PSN->f('nombre'); ?>" class="form-control" />
					</div>
				</div>

				
				<div class="form-group">
					<label class="control-label col-sm-2" for="fechaInicial">Fecha Inicial</label>
				  <div class="col-sm-4">
					  <input name="fechaInicial" type="date"  placeholder="AAAA-MM-DD" value="<?=$PSN->f('fechaInicial'); ?>" class="form-control" />
					</div>
					<label class="control-label col-sm-2" for="fechaInicial">Fecha Final</label>
				  <div class="col-sm-4">
					  <input name="fechaFinal" type="date"  placeholder="AAAA-MM-DD" value="<?=$PSN->f('fechaFinal'); ?>" class="form-control" />
					</div>
				</div>

				<div class="form-group">
					<label class="control-label col-sm-2" for="estado">Estado</label>
				  <div class="col-sm-4">
					 <select name="estado" class="form-control"><option value="1" <?php if($PSN->f('estado') == 1){ echo 'selected="selected="'; } ?>>Activo</option><option value="0">Inactivo</option></select>
					</div>
					<label class="control-label col-sm-2" for="observaciones">Observaciones</label>
				  <div class="col-sm-4">
					<input name="observaciones" type="text" maxlength="250" value="<?=$PSN->f('observaciones'); ?>" class="form-control" />
					</div>
				</div>

				<div class="form-group">
                    <div class="col-sm-2"><strong>Usuario que digito el registro</strong></div>
					<div class="col-sm-4">
						<?=$PSN->f('creacionUsuarioNom'); ?>
					</div>
                    <div class="col-sm-2"><strong>Fecha de creaci&oacute;n del registro</strong></div>
					</div>
					<div class="col-sm-4">
						<?=$PSN->f('creacionFecha'); ?>
					</div>
				</div>


				<div class="form-group">
                    <div class="col-sm-2"><strong>Usuario que realizo ultima modificaci&oacute;n</strong></div>
					<div class="col-sm-4">
						<?=$PSN->f('modUsuarioNom'); ?>
					</div>
                    <div class="col-sm-2"><strong>Ultima modificaci&oacute;n Fecha</strong></div>
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
				
				<!--<br />
				<center><input type="button" name="button" onclick="generarFormDel()" value="Eliminar Registro" /></center>//-->
				</form>
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
                        window.location.href = "index.php?doc=campanha";
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
				?><div class="form-group"><h2><font color="#FF0000">ID Incorrecto. No Existe o no esta autorizado para visualizar la misma.</font></h2></div><?php
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
		$sqlB= "SELECT ".$tablaConsulta.".*, categorias.descripcion as nomTipoCampana, sum(mail_historico.correos) as totalCorreos, sum(sms_historico.mensajes) as totalSMS";
		$sqlB.=" FROM ".$tablaConsulta;
			$sqlB.=" LEFT JOIN categorias ON categorias.id = ".$tablaConsulta.".tipoCampana ";
			$sqlB.=" LEFT JOIN mail_historico ON mail_historico.idCampana = ".$tablaConsulta.".id ";
			$sqlB.=" LEFT JOIN sms_historico ON sms_historico.idCampana = ".$tablaConsulta.".id ";
		$sqlB.=" WHERE 1 ";

		//
		$sqlC= "SELECT count(".$tablaConsulta.".id) as conteo"; // sum(".$tablaConsulta.".valor_prima_neta) as total_neto";
		$sqlC.=" FROM ".$tablaConsulta;
		$sqlC.=" WHERE 1 ";


		if(eliminarInvalidos($_GET["nombre"]) != "")
		{
			$sql.=" AND ".$tablaConsulta.".nombre  LIKE '%".eliminarInvalidos($_GET["nombre"])."%'";
		}

		if(soloNumeros($_GET["estado"]) != "")
		{
			$sql.=" AND ".$tablaConsulta.".estado LIKE '%".soloNumeros($_GET["estado"])."%'";
		}

		if(soloNumeros($_GET["tipoCampana"]) != "")
		{
			$sql.=" AND ".$tablaConsulta.".tipoCampana LIKE '%".soloNumeros($_GET["tipoCampana"])."%'";
		}

		
		if(eliminarInvalidos($_GET["fechaInicial"]) != "")
		{
			$sql.=" and ".$tablaConsulta.".fechaInicial >= '".eliminarInvalidos($_GET["fechaInicial"])."'";
			$fechaInicial = eliminarInvalidos($_GET["fechaInicial"]);
		}

		if(eliminarInvalidos($_GET["fechaFinal"]) != "")
		{
			$sql.=" and ".$tablaConsulta.".fechaFinal <= '".eliminarInvalidos($_GET["fechaFinal"])."'";
			$fechaFinal = eliminarInvalidos($_GET["fechaFinal"]);
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
		?><div class="container">
            <form action="index.php" name="form" id="form" method="get" class="form-horizontal">
    	    <input type="hidden" name="doc" value="<?=$webArchivo; ?>" />
	        <input type="hidden" name="opc" value="2" />
			
            <div class="form-group">
                <h2 class="alert alert-info text-center">.FILTROS DE BUSQUEDA - <?=$nombreConsulta; ?>.</h2>
            </div>
                
			<div class="form-group">
				<label class="control-label col-sm-2" for="nombre">Nombre</label>
				<div class="col-sm-4">
					<input type="text" name="nombre" id="nombre" value="<?=eliminarInvalidos($_GET["nombre"]); ?>" class="form-control" />
				</div>
				<label class="control-label col-sm-2" for="tipoCampana">Tipo de campa&ntilde;a</label>
				<div class="col-sm-4">
					<select name="tipoCampana" class="form-control">
					<option value="">Todos</option>
					<?php
					$PSNTEMP = new DBbase_Sql;
					$sql= "SELECT categorias.* ";
						$sql.=" FROM categorias ";
						$sql.=" WHERE idSec = 23 ";
					$sql.=" ORDER BY descripcion asc ";

					$PSNTEMP->query($sql);
					$num=$PSNTEMP->num_rows();
					if($num > 0)
					{
						while($PSNTEMP->next_record())
						{
							?><option value="<?=$PSNTEMP->f('id'); ?>" <?php
							if(soloNumeros($_GET["tipoCampana"]) == $PSNTEMP->f('id'))
							{
								?>selected="selected"<?php 
							}
							?>><?=$PSNTEMP->f('descripcion'); ?></option><?php
						}
					}
					?>
					</select>
				</div>

            </div>

			<div class="form-group">				
				<label class="control-label col-sm-2" for="fechaInicial">Fecha inicial</label>
				<div class="col-sm-4">
					<input name="fechaInicial" type="date" id="fechaInicial" placeholder="AAAA-MM-DD" value="<?=eliminarInvalidos($_GET["fechaInicial"]); ?>" class="form-control" />
				</div>
				<label class="control-label col-sm-2" for="fechaFinal">Fecha final</label>
				<div class="col-sm-4">
					<input name="fechaFinal" type="date" id="fechaFinal" placeholder="AAAA-MM-DD" value="<?=eliminarInvalidos($_GET["fechaFinal"]); ?>" class="form-control" />
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
			<th align="center">Estado</th>
			<th align="center">Tipo de Campa&ntilde;a</th>
			<th align="center">Emails Enviados</th>
			<th align="center">Emails Leidos*</th>
			<th align="center">SMS Enviados</th>
		</tr>
        </thead>
        <tbody>    
        <?php
			if($num > 0)
			{
				$izq = 1;
				$contador = $inicio+1;
				while($PSN->next_record())
				{
					$totalLeidos = 0;
					$sql = "SELECT sum(mail_log.visto) as totalLeidos";
					$sql.= " FROM mail_log";
					$sql.=" WHERE idCampana = '".$PSN->f('id')."'";
					$PSNTEMP->query($sql);
					$num=$PSNTEMP->num_rows();
					if($num > 0)
					{
						if($PSNTEMP->next_record())
						{
							$totalLeidos = $PSNTEMP->f('totalLeidos');
						}
					}

					?>
					<tr <?php if($contador%2==0){ ?>bgcolor="#EEEEEE"<?php } ?>>
						<td><a href="index.php?doc=<?=$webArchivo; ?>&opc=2&id=<?=$PSN->f('id');?>"><?=$contador; ?></a></td>
						<td><a href="index.php?doc=<?=$webArchivo; ?>&opc=2&id=<?=$PSN->f('id');?>"><?=$PSN->f('nombre');?></a></td>
						<td><?php if($PSN->f('estado') == 1){ echo "Activo"; }else{ echo "Inactivo"; };?></td>
						<td><?=$PSN->f('nomTipoCampana');?></td>
						<td align="center"><?=$PSN->f('totalCorreos'); ?></td>
						<td align="center"><?=$totalLeidos; ?></td>
						<td align="center"><?=abs($PSN->f('totalSMS')); ?></td>
					</tr>
					<?php
					$contador++;
				}
			}		
			?>  
        </tr>
        </tbody> 
		</table>
        <br />
		<h4><i>*: El numero de emails leidos puede variar segun la configuracion de correo del usuario si el usuario acepta correos en formato html con imagenes.</i></h4>
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