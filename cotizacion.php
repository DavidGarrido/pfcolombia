<?php ?><!-- jQuery UI -->
			<script src="scripts/jquery-1.12.4.js"></script>
			<link rel="stylesheet" href="scripts/jquery-ui.css">
			<script src="scripts/jquery-ui.js"></script>
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
$tablaConsulta = "cotizacion";
$webArchivo = "cotizacion";
$nombreConsulta = "COTIZACIONES";

$PSN1 = new DBbase_Sql;
if($opc == 1)
{
		if(isset($_POST["contactoNombre"]))
		{
			$PSN = new DBbase_Sql;

			/*$sql= "SELECT ".$tablaConsulta.".id";
			$sql.=" FROM ".$tablaConsulta;
			$sql.=" WHERE ".$tablaConsulta.".contactoNombre = '".eliminarInvalidos($_REQUEST["contactoNombre"])."'";
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
			else*/
			if(1)
			{
				$idCliente = soloNumeros($_POST["idCliente"]);
				$idCoctacto = soloNumeros($_POST["idContacto"]);
				$estado = soloNumeros($_POST["estado"]);
				$estadoPago = soloNumeros($_POST["estadoPago"]);
				$categoriaPrincipal = soloNumeros($_POST["categoriaPrincipal"]);
				$metodoCotizacion = soloNumeros($_POST["metodoCotizacion"]);
				$tipoContacto = soloNumeros($_POST["tipoContacto"]);
				$contactoNombre = eliminarInvalidos($_POST["contactoNombre"]);
				$contactoTelefono = eliminarInvalidos($_POST["contactoTelefono"]);
				$contactoCelular = eliminarInvalidos($_POST["contactoCelular"]);
				$contactoEmail = eliminarInvalidos($_POST["contactoEmail"]);
				$descripcion = eliminarInvalidos($_POST["descripcion"]);
				$valorCotizado = soloNumeros($_POST["valorCotizado"]);
				$valorCobrado = soloNumeros($_POST["valorCobrado"]);
				$valorRecibido = soloNumeros($_POST["valorRecibido"]);
				$fechaCotizacion = eliminarInvalidos($_POST["fechaCotizacion"]);
				$fechaFacturacion = eliminarInvalidos($_POST["fechaFacturacion"]);
				$fechaUltimoPago = eliminarInvalidos($_POST["fechaUltimoPago"]);
                
                
				$creacionUsuario = $_SESSION["id"];
				//$audit_usuario = $_SESSION["id"];
				//$audit_ip = $_SERVER['REMOTE_ADDR'];
				$sql = 'insert into '.$tablaConsulta.' (
					idCliente,  
					idContacto,
					estado,  
					estadoPago,  
					categoriaPrincipal,  
					metodoCotizacion,  
					tipoContacto,  
					contactoNombre,  
					contactoTelefono,  
					contactoCelular,  
					contactoEmail,  
					descripcion,  
					valorCotizado,  
					valorCobrado,  
					valorRecibido,  
					fechaCotizacion,  
					fechaFacturacion,  
					fechaUltimoPago, 
					creacionUsuario,
					creacionFecha
				) ';

				$sql .= 'values (
					"'.$idCliente.'", 
					"'.$idContacto.'", 
					"'.$estado.'", 
					"'.$estadoPago.'", 
					"'.$categoriaPrincipal.'", 
					"'.$metodoCotizacion.'", 
					"'.$tipoContacto.'", 
					"'.$contactoNombre.'", 
					"'.$contactoTelefono.'", 
					"'.$contactoCelular.'", 
					"'.$contactoEmail.'", 
					"'.$descripcion.'", 
					"'.$valorCotizado.'", 
					"'.$valorCobrado.'", 
					"'.$valorRecibido.'", 
					"'.$fechaCotizacion.'", 
					"'.$fechaFacturacion.'", 
					"'.$fechaUltimoPago.'", 
					"'.$creacionUsuario.'", 
					NOW()
				)';

				$ultimoQuery = $PSN->query($sql);
				$ultimoId = $PSN->ultimoId();
				//
				//
				if($idContacto != "" && $idContacto != 0 && $idContacto != "0"){
					$sql = 'UPDATE sms_usuarios SET 
						nombres ="'.$contactoNombre.'"
						,telfijo ="'.$contactoTelefono.'"
						,celular ="'.$contactoCelular.'"
						,email ="'.$contactoEmail.'" 
					WHERE id = "'.$idContacto.'"
					';
					$PSN->query($sql);
				}

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
			?><div class="container">
            <form method="post" enctype="multipart/form-data" name="form1" id="form1" class="form-horizontal">
                
            <div class="form-group">
                <h2 class="alert alert-info text-center">.CREACION DE COTIZACI&Oacute;N.</h2>
            </div>

			<div class="form-group">
            	<label class="control-label col-sm-2" for="idCliente">Prospecto</label>
				<div class="col-sm-4">
					<select name="idCliente" id="idCliente" onChange="cambioCliente()" class="form-control">
						<option value="">Seleccione un prospecto...</option>
					<?php
					$PSNTEMP = new DBbase_Sql;
					$sql= "SELECT cliente.id, cliente.nombre ";
						$sql.=" FROM cliente WHERE estado != 0";
                        //Si es un comercial solo puede agregar prospectos de si mismo
                        if($_SESSION["perfil"] == 163){
                            $sql.=" AND cliente.idComercial = '".$_SESSION["id"]."'";
                        }
					$sql.=" ORDER BY nombre asc";

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
				<label class="control-label col-sm-2" for="estado">Estado Cotizaci&oacute;n</label>
				<div class="col-sm-4">
					<select name="estado" class="form-control">
					<?php
					$PSNTEMP = new DBbase_Sql;
					$sql= "SELECT categorias.* ";
						$sql.=" FROM categorias ";
						$sql.=" WHERE idSec = 16 ";
					$sql.=" ORDER BY descripcion asc ";

					$PSNTEMP->query($sql);
					$num=$PSNTEMP->num_rows();
					if($num > 0)
					{
						while($PSNTEMP->next_record())
						{
							?><option value="<?=$PSNTEMP->f('id'); ?>" <?php
							if(soloNumeros($_POST["estado"]) == $PSNTEMP->f('id'))
							{
								?>selected="selected"<?php 
							}
							?>><?=$PSNTEMP->f('descripcion'); ?></option><?php
						}
					}
					?>
					</select>
				</div>
				<label class="control-label col-sm-2" for="estadoPago">Estado del pago</label>
				<div class="col-sm-4">
					<select name="estadoPago" class="form-control">
					<?php
					$PSNTEMP = new DBbase_Sql;
					$sql= "SELECT categorias.* ";
						$sql.=" FROM categorias ";
						$sql.=" WHERE idSec = 17 ";
					$sql.=" ORDER BY descripcion asc ";

					$PSNTEMP->query($sql);
					$num=$PSNTEMP->num_rows();
					if($num > 0)
					{
						while($PSNTEMP->next_record())
						{
							?><option value="<?=$PSNTEMP->f('id'); ?>" <?php
							if(soloNumeros($_POST["estadoPago"]) == $PSNTEMP->f('id'))
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
				<label class="control-label col-sm-2" for="categoriaPrincipal">Categoria</label>
				<div class="col-sm-4">
					<select name="categoriaPrincipal"  class="form-control">
					<?php
					$PSNTEMP = new DBbase_Sql;
					$sql= "SELECT categorias.* ";
						$sql.=" FROM categorias ";
						$sql.=" WHERE idSec = 18 ";
					$sql.=" ORDER BY descripcion asc ";

					$PSNTEMP->query($sql);
					$num=$PSNTEMP->num_rows();
					if($num > 0)
					{
						while($PSNTEMP->next_record())
						{
							?><option value="<?=$PSNTEMP->f('id'); ?>" <?php
							if(soloNumeros($_POST["categoriaPrincipal"]) == $PSNTEMP->f('id'))
							{
								?>selected="selected"<?php 
							}
							?>><?=$PSNTEMP->f('descripcion'); ?></option><?php
						}
					}
					?>
                	</select>
				</div>
				<label class="control-label col-sm-2" for="metodoCotizacion">Metodo cotizaci&oacute;n</label>
				<div class="col-sm-4">
					<select name="metodoCotizacion" class="form-control">
					<?php
					$PSNTEMP = new DBbase_Sql;
					$sql= "SELECT categorias.* ";
						$sql.=" FROM categorias ";
						$sql.=" WHERE idSec = 19 ";
					$sql.=" ORDER BY descripcion asc ";

					$PSNTEMP->query($sql);
					$num=$PSNTEMP->num_rows();
					if($num > 0)
					{
						while($PSNTEMP->next_record())
						{
							?><option value="<?=$PSNTEMP->f('id'); ?>" <?php
							if(soloNumeros($_POST["metodoCotizacion"]) == $PSNTEMP->f('id'))
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
				<label class="control-label col-sm-2" for="tipoContacto">Tipo de contacto</label>
				<div class="col-sm-4">
					<select name="tipoContacto" class="form-control">
					<?php
					$PSNTEMP = new DBbase_Sql;
					$sql= "SELECT categorias.* ";
						$sql.=" FROM categorias ";
						$sql.=" WHERE idSec = 20 ";
					$sql.=" ORDER BY descripcion asc ";

					$PSNTEMP->query($sql);
					$num=$PSNTEMP->num_rows();
					if($num > 0)
					{
						while($PSNTEMP->next_record())
						{
							?><option value="<?=$PSNTEMP->f('id'); ?>" <?php
							if(soloNumeros($_POST["tipoContacto"]) == $PSNTEMP->f('id'))
							{
								?>selected="selected"<?php 
							}
							?>><?=$PSNTEMP->f('descripcion'); ?></option><?php
						}
					}
					?>
					</select>
				</div>
                <label class="control-label col-sm-2" for="contactoNombre">Nombre del contacto</label>
				<div class="col-sm-4">
					<input type="hidden" name="idContacto" id="idContacto" />
					<div class="ui-widget"><input name="contactoNombre" id="contactoNombre" type="text" maxlength="250" value="<?=eliminarInvalidos($_POST['contactoNombre']); ?>" class="form-control" /></div>
				</div>
			</div>
					 
			<div class="form-group">
                <label class="control-label col-sm-2" for="contactoTelefono">Tel&eacute;fono del contacto</label>
				<div class="col-sm-4">
					<input name="contactoTelefono" id="contactoTelefono" type="text" maxlength="250" value="<?=eliminarInvalidos($_POST['contactoTelefono']); ?>" class="form-control" />
				</div>
                <label class="control-label col-sm-2" for="contactoTelefono">Celular del contacto</label>
				<div class="col-sm-4">
					<input name="contactoCelular" id="contactoCelular" type="text" maxlength="250" value="<?=eliminarInvalidos($_POST['contactoCelular']); ?>" class="form-control" />
				</div>
			</div>
				
			<div class="form-group">
                <label class="control-label col-sm-2" for="contactoEmail">Email del contacto</label>
				<div class="col-sm-4">
					<input name="contactoEmail" id="contactoEmail" type="text" maxlength="250" value="<?=eliminarInvalidos($_POST['contactoEmail']); ?>" class="form-control" />
				</div>
			</div>
				
				
			<div class="form-group">
                <label class="control-label col-sm-2" for="contactoEmail">Descripci&oacute;n de la cotizaci&oacute;n</label>
				<div class="col-sm-4">
					<textarea name="descripcion" rows="10" cols="100" class="form-control"><?=eliminarInvalidos($_POST['descripcion']); ?></textarea>
				</div>
            </div>
                
			<div class="form-group">
                <label class="control-label col-sm-2" for="valorCotizado">Valor cotizado</label>
				<div class="col-sm-4">
					<input name="valorCotizado" type="number" maxlength="250" value="<?=eliminarInvalidos($_POST['valorCotizado']); ?>" class="form-control" />
				</div>
                <label class="control-label col-sm-2" for="valorCobrado">Valor cobrado realmente</label>
				<div class="col-sm-4">
					<input name="valorCobrado" type="number" maxlength="250" value="<?=eliminarInvalidos($_POST['valorCobrado']); ?>" class="form-control" />
				</div>
			</div>

			<div class="form-group">
                <label class="control-label col-sm-2" for="valorRecibido">Valor recibido realmente</label>
				<div class="col-sm-4">
					<input name="valorRecibido" type="number" maxlength="250" value="<?=eliminarInvalidos($_POST['valorRecibido']); ?>" class="form-control" />
				</div>
                <label class="control-label col-sm-2" for="fechaCotizacion">Fecha de Cotizaci&oacute;n</label>
				<div class="col-sm-4">
					<input name="fechaCotizacion"  type="date"  placeholder="AAAA-MM-DD" maxlength="250" value="<?=eliminarInvalidos($_POST['fechaCotizacion']); ?>" class="form-control" />
				</div>
			</div>

			<div class="form-group">
                <label class="control-label col-sm-2" for="fechaFacturacion">Fecha de Facturaci&oacute;n</label>
				<div class="col-sm-4">
					<input name="fechaFacturacion"  type="date"  placeholder="AAAA-MM-DD" maxlength="250" value="<?=eliminarInvalidos($_POST['fechaFacturacion']); ?>" class="form-control" />
				</div>
                <label class="control-label col-sm-2" for="fechaUltimoPago">Fecha del ultimo pago recibido</label>
				<div class="col-sm-4">
					<input name="fechaUltimoPago"  type="date"  placeholder="AAAA-MM-DD" maxlength="250" value="<?=eliminarInvalidos($_POST['fechaUltimoPago']); ?>" class="form-control" />
				</div>
			</div>

            <div class="row text-center">
                <input type="hidden" name="funcion" id="funcion" value="" />
                <input type="submit" value="Guardar cambios" class="btn btn-success" />
            </div>

            </form>
			</div>
				
            <script language="javascript">
                function openTab(nomTab,elmnt,color)
                {
                    var i, tabcontent, tablinks;
                    tabcontent = document.getElementsByClassName("tabcontent");
                    for (i = 0; i < tabcontent.length; i++) {
                        tabcontent[i].style.display = "none";
                    }
                    tablinks = document.getElementsByClassName("tablink");
                    for (i = 0; i < tablinks.length; i++) {
                        tablinks[i].style.backgroundColor = "";
                    }
                    document.getElementById(nomTab).style.display = "block";
                    elmnt.style.backgroundColor = color;
                }

                Array.prototype.forEach.call( inputs, function( input )
                {
                    var label	 = input.nextElementSibling,
                        labelVal = label.innerHTML;

                    input.addEventListener( 'change', function( e )
                    {
                        var fileName = '';
                        if( this.files && this.files.length > 1 )
                            fileName = ( this.getAttribute( 'data-multiple-caption' ) || '' ).replace( '{count}', this.files.length );
                        else
                            fileName = e.target.value.split( '\\' ).pop();

                        if( fileName )
                            label.querySelector( 'span' ).innerHTML = fileName;
                        else
                            label.innerHTML = labelVal;
                    });
                });
                
                
                function cambioCliente(){
					document.getElementById('idContacto').value = "0";
					document.getElementById('contactoNombre').value = "";
					document.getElementById('contactoTelefono').value = "";
					document.getElementById('contactoCelular').value = "";
					document.getElementById('contactoEmail').value = "";
					//
					var varidCliente = document.getElementById('idCliente').value; // selected value
					// AJAX
					$.ajax({
						url: 'busquedaContacto.php',
						type: 'post',
						data: {
							idCliente:varidCliente,
							request:3
						},
						dataType: 'json',
						success:function(response){
							var len = response.length;
							if(len > 0)
							{
								var id = response[0]['id'];
								var contactonombre = response[0]['contactonombre'];
								var contactotelefono = response[0]['contactotelefono'];
								var contactocelular = response[0]['contactocelular'];
								var contactoemail = response[0]['contactoemail'];

								// Set value to textboxes
								document.getElementById('idContacto').value = id;
								document.getElementById('contactoNombre').value = contactonombre;
								document.getElementById('contactoTelefono').value = contactotelefono;
								document.getElementById('contactoCelular').value = contactocelular;
								document.getElementById('contactoEmail').value = contactoemail;
								//
							}
						}
					});
				}

				function generarForm(){
                        if(confirm("Esta accion generara el REGISTRO en el sistema, esta seguro que desea continuar?"))
                        {
                            if(document.getElementById('contactoNombre').value != "" && document.getElementById('idCliente').value != "")
                            {
                                document.getElementById('funcion').value = "insertar";
                              //  document.getElementById('form1').submit();
                              return true;
                            }
                            else
                            {
								document.getElementById('idContacto').value = "0";
                                alert("La informacion es primordial para brindarle un excelente servicio, por favor seleccione al menos un prospecto y digite el campo de NOMBRE DEL CONTACTO");
                            }
                        }
                              return false;
                }
                function init(){
                    document.getElementById('form1').onsubmit = function(){
                            return generarForm();
                    }

					$(function() {
						//
						$("#contactoNombre").autocomplete({
							source: function( request, response ) {
								var varidCliente = document.getElementById('idCliente').value; // selected 
								// Set value to textboxes
								document.getElementById('idContacto').value = "0";
								document.getElementById('contactoTelefono').value = "";
								document.getElementById('contactoCelular').value = "";
								document.getElementById('contactoEmail').value = "";
								//
								//
								$.ajax( {
									url: "busquedaContacto.php",
									dataType: "jsonp",
									data: {
										term: request.term,
										idCliente: varidCliente,
										request: "1"
									},
									success: function( data ) {
										response(data);
									}
								} );
							},
							minLength: 2,
							select: function(event, ui) {
								//alert(ui.item.value + " aka " + ui.item.id);
								var userid = ui.item.value; // selected value
								var varidCliente = document.getElementById('idCliente').value; // selected value
								// AJAX
								$.ajax({
									url: 'busquedaContacto.php',
									type: 'post',
									data: {
										id:userid,
										idCliente:varidCliente,
										request:2
									},
									dataType: 'json',
									success:function(response){
										var len = response.length;
										if(len > 0)
										{
											var id = response[0]['id'];
											var contactonombre = response[0]['contactonombre'];
											var contactotelefono = response[0]['contactotelefono'];
											var contactocelular = response[0]['contactocelular'];
											var contactoemail = response[0]['contactoemail'];

											// Set value to textboxes
											document.getElementById('idContacto').value = id;
											document.getElementById('contactoNombre').value = contactonombre;
											document.getElementById('contactoTelefono').value = contactotelefono;
											document.getElementById('contactoCelular').value = contactocelular;
											document.getElementById('contactoEmail').value = contactoemail;
											//
										}
									}
								});
								return false;						
								//Fin
							}
						});
					});
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
		if(isset($_POST["contactoNombre"]))
		{
			/*
			*	ACTUALIZAR
			*/
			if($_POST["funcion"] == "actualizar")
			{
				$idCliente = soloNumeros($_POST["idCliente"]);
				$idContacto = soloNumeros($_POST["idContacto"]);
				$estado = soloNumeros($_POST["estado"]);
				$estadoPago = soloNumeros($_POST["estadoPago"]);
				$categoriaPrincipal = soloNumeros($_POST["categoriaPrincipal"]);
				$metodoCotizacion = soloNumeros($_POST["metodoCotizacion"]);
				$tipoContacto = soloNumeros($_POST["tipoContacto"]);
				$contactoNombre = eliminarInvalidos($_POST["contactoNombre"]);
				$contactoTelefono = eliminarInvalidos($_POST["contactoTelefono"]);
				$contactoCelular = eliminarInvalidos($_POST["contactoCelular"]);
				$contactoEmail = eliminarInvalidos($_POST["contactoEmail"]);
				$descripcion = eliminarInvalidos($_POST["descripcion"]);
				$valorCotizado = soloNumeros($_POST["valorCotizado"]);
				$valorCobrado = soloNumeros($_POST["valorCobrado"]);
				$valorRecibido = soloNumeros($_POST["valorRecibido"]);
				$fechaCotizacion = eliminarInvalidos($_POST["fechaCotizacion"]);
				$fechaFacturacion = eliminarInvalidos($_POST["fechaFacturacion"]);
				$fechaUltimoPago = eliminarInvalidos($_POST["fechaUltimoPago"]);
				//
                $mensajecotizacion = str_replace('/videoexpress/scripts/', 'https://icarsoluciones.app/scripts/', $_REQUEST["mensajecotizacion"]);
                //
                
				$modUsuario = $_SESSION["id"];
				//$modFecha;
				
				$sql = 'update '.$tablaConsulta.' set 
							idCliente ="'.$idCliente.'"
							,idContacto ="'.$idContacto.'"
							,estado ="'.$estado.'"
							,estadoPago ="'.$estadoPago.'"
							,categoriaPrincipal ="'.$categoriaPrincipal.'"
							,metodoCotizacion ="'.$metodoCotizacion.'"
							,tipoContacto ="'.$tipoContacto.'"
							,contactoNombre ="'.$contactoNombre.'"
							,contactoTelefono ="'.$contactoTelefono.'"
							,contactoCelular ="'.$contactoCelular.'"
							,contactoEmail ="'.$contactoEmail.'"
							,descripcion ="'.$descripcion.'"
							,mensajecotizacion ="'.addslashes($mensajecotizacion).'"
							,valorCotizado ="'.$valorCotizado.'"
							,valorCobrado ="'.$valorCobrado.'"
							,valorRecibido ="'.$valorRecibido.'"
							,fechaCotizacion ="'.$fechaCotizacion.'"
							,fechaFacturacion ="'.$fechaFacturacion.'"
							,fechaUltimoPago ="'.$fechaUltimoPago.'"
							,modUsuario ="'.$modUsuario.'"
							,modFecha = NOW() where id='.soloNumeros($_GET["id"]);				
				$PSN->query($sql);

				
				//
				//
				if($idContacto != "" && $idContacto != 0 && $idContacto != "0"){
					$sql = 'UPDATE sms_usuarios SET 
						nombres ="'.$contactoNombre.'"
						,telfijo ="'.$contactoTelefono.'"
						,celular ="'.$contactoCelular.'"
						,email ="'.$contactoEmail.'" 
					WHERE id = "'.$idContacto.'"
					';
					$PSN->query($sql);
				}
				//
				//
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
			
			$PSN->query($sql);
			$num=$PSN->num_rows();
			if($num > 0)
			{
				$izq = 1;
				if($PSN->next_record())
				{
					$PSN2 = new DBbase_Sql;


            echo '<script type="text/javascript" src="scripts/ckeditor/ckeditor.js"></script>'."\n";

            ?><div class="container">
            <div class="form-group">
                <h2 class="alert alert-info text-center">.ACTUALIZACI&Oacute;N DE COTIZACI&Oacute;N.</h2>
            </div>
                    
                <ul class="nav nav-tabs">
                    <li class="active"><a data-toggle="tab" href="#tab1">Datos generales</a></li>
                    <li><a data-toggle="tab" href="#tab2">Cotización</a></li>
                </ul>
                    
        <div class="row">
        <div class="tab-content">

            <div id="tab1" class="tab-pane fade in active">
            <div id="container">

            <form method="post" enctype="multipart/form-data" name="form1" id="form1" class="form-horizontal">
            <br />

            <div class="form-group">
				<label class="control-label col-sm-2" for="idCliente">Prospecto</label>
				<div class="col-sm-4">
					<select name="idCliente" id="idCliente" onChange="cambioCliente()" class="form-control">
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
					$num=$PSNTEMP->num_rows();
					if($num > 0)
					{
						while($PSNTEMP->next_record())
						{
							?><option value="<?=$PSNTEMP->f('id'); ?>" <?php
							if($PSN->f("idCliente") == $PSNTEMP->f('id'))
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
				<label class="control-label col-sm-2" for="estado">Estado Cotizaci&oacute;n</label>
				<div class="col-sm-4">
					<select name="estado" class="form-control">
					<?php
					$PSNTEMP = new DBbase_Sql;
					$sql= "SELECT categorias.* ";
						$sql.=" FROM categorias ";
						$sql.=" WHERE idSec = 16 ";
					$sql.=" ORDER BY descripcion asc ";

					$PSNTEMP->query($sql);
					$num=$PSNTEMP->num_rows();
					if($num > 0)
					{
						while($PSNTEMP->next_record())
						{
							?><option value="<?=$PSNTEMP->f('id'); ?>" <?php
							if($PSN->f("estado") == $PSNTEMP->f('id'))
							{
								?>selected="selected"<?php 
							}
							?>><?=$PSNTEMP->f('descripcion'); ?></option><?php
						}
					}
					?>
					</select>
				</div>
				
				<label class="control-label col-sm-2" for="estadoPago">Estado del pago</label>
				<div class="col-sm-4">
					<select name="estadoPago" class="form-control">
					<?php
					$PSNTEMP = new DBbase_Sql;
					$sql= "SELECT categorias.* ";
						$sql.=" FROM categorias ";
						$sql.=" WHERE idSec = 17 ";
					$sql.=" ORDER BY descripcion asc ";

					$PSNTEMP->query($sql);
					$num=$PSNTEMP->num_rows();
					if($num > 0)
					{
						while($PSNTEMP->next_record())
						{
							?><option value="<?=$PSNTEMP->f('id'); ?>" <?php
							if($PSN->f("estadoPago") == $PSNTEMP->f('id'))
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
				<label class="control-label col-sm-2" for="categoriaPrincipal">Categoria</label>
				<div class="col-sm-4">
					<select name="categoriaPrincipal" class="form-control">
					<?php
					$PSNTEMP = new DBbase_Sql;
					$sql= "SELECT categorias.* ";
						$sql.=" FROM categorias ";
						$sql.=" WHERE idSec = 18 ";
					$sql.=" ORDER BY descripcion asc ";

					$PSNTEMP->query($sql);
					$num=$PSNTEMP->num_rows();
					if($num > 0)
					{
						while($PSNTEMP->next_record())
						{
							?><option value="<?=$PSNTEMP->f('id'); ?>" <?php
							if($PSN->f("categoriaPrincipal") == $PSNTEMP->f('id'))
							{
								?>selected="selected"<?php 
							}
							?>><?=$PSNTEMP->f('descripcion'); ?></option><?php
						}
					}
					?>
                	</select>
				</div>
				
				<label class="control-label col-sm-2" for="metodoCotizacion">Metodo cotizaci&oacute;n</label>
				<div class="col-sm-4">
					<select name="metodoCotizacion" class="form-control">
					<?php
					$PSNTEMP = new DBbase_Sql;
					$sql= "SELECT categorias.* ";
						$sql.=" FROM categorias ";
						$sql.=" WHERE idSec = 19 ";
					$sql.=" ORDER BY descripcion asc ";

					$PSNTEMP->query($sql);
					$num=$PSNTEMP->num_rows();
					if($num > 0)
					{
						while($PSNTEMP->next_record())
						{
							?><option value="<?=$PSNTEMP->f('id'); ?>" <?php
							if($PSN->f("metodoCotizacion") == $PSNTEMP->f('id'))
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
				<label class="control-label col-sm-2" for="tipoContacto">Tipo de contacto</label>
				<div class="col-sm-4">
					<select name="tipoContacto" class="form-control">
					<?php
					$PSNTEMP = new DBbase_Sql;
					$sql= "SELECT categorias.* ";
						$sql.=" FROM categorias ";
						$sql.=" WHERE idSec = 20 ";
					$sql.=" ORDER BY descripcion asc ";

					$PSNTEMP->query($sql);
					$num=$PSNTEMP->num_rows();
					if($num > 0)
					{
						while($PSNTEMP->next_record())
						{
							?><option value="<?=$PSNTEMP->f('id'); ?>" <?php
							if($PSN->f('tipoContacto') == $PSNTEMP->f('id'))
							{
								?>selected="selected"<?php 
							}
							?>><?=$PSNTEMP->f('descripcion'); ?></option><?php
						}
					}
					?>
					</select>
				</div>
				
                <label class="control-label col-sm-2" for="contactoNombre">Nombre del contacto</label>
				<div class="col-sm-4">
					<input type="hidden" name="idContacto" id="idContacto" value="<?=$PSN->f('idContacto'); ?>" />
					<div class="ui-widget"><input name="contactoNombre" id="contactoNombre" type="text" maxlength="250" value="<?=$PSN->f('contactoNombre'); ?>" class="form-control" /></div>
				</div>
			</div>
					 
			<div class="form-group">
                <label class="control-label col-sm-2" for="contactoTelefono">Tel&eacute;fono del contacto</label>
				<div class="col-sm-4">
					<input name="contactoTelefono" id="contactoTelefono" type="text" maxlength="250" value="<?=$PSN->f('contactoTelefono'); ?>" class="form-control" />
				</div>
                <label class="control-label col-sm-2" for="contactoTelefono">Celular del contacto</label>
				<div class="col-sm-4">
					<input name="contactoCelular" id="contactoCelular" type="text" maxlength="250" value="<?=$PSN->f('contactoCelular'); ?>" class="form-control" />
				</div>
			</div>
				
			<div class="form-group">
                <label class="control-label col-sm-2" for="contactoEmail">Email del contacto</label>
				<div class="col-sm-4">
					<input name="contactoEmail" id="contactoEmail" type="text" maxlength="250" value="<?=$PSN->f('contactoEmail'); ?>" class="form-control" />
				</div>
			</div>
				
				
			<div class="form-group">
                <label class="control-label col-sm-2" for="contactoEmail">Descripci&oacute;n de la cotizaci&oacute;n</label>
				<div class="col-sm-4">
					<textarea name="descripcion" rows="10" cols="100" class="form-control"><?=$PSN->f('descripcion'); ?></textarea>
				</div>
			</div>

			<div class="form-group">
                <label class="control-label col-sm-2" for="valorCotizado">Valor cotizado</label>
				<div class="col-sm-4">
					<input name="valorCotizado" type="number" maxlength="250" value="<?=$PSN->f('valorCotizado'); ?>" class="form-control" />
				</div>
                <label class="control-label col-sm-2" for="valorCobrado">Valor cobrado realmente</label>
				<div class="col-sm-4">
					<input name="valorCobrado" type="number" maxlength="250" value="<?=$PSN->f('valorCobrado'); ?>" class="form-control" />
				</div>
			</div>

			<div class="form-group">
                <label class="control-label col-sm-2" for="valorRecibido">Valor recibido realmente</label>
				<div class="col-sm-4">
					<input name="valorRecibido" type="number" maxlength="250" value="<?=$PSN->f('valorRecibido'); ?>" class="form-control" />
				</div>
                <label class="control-label col-sm-2" for="fechaCotizacion">Fecha de Cotizaci&oacute;n</label>
				<div class="col-sm-4">
					<input name="fechaCotizacion"  type="date"  placeholder="AAAA-MM-DD" maxlength="250" value="<?=$PSN->f('fechaCotizacion'); ?>" class="form-control" />
				</div>
			</div>


			<div class="form-group">
                <label class="control-label col-sm-2" for="fechaFacturacion">Fecha de Facturaci&oacute;n</label>
				<div class="col-sm-4">
					<input name="fechaFacturacion"  type="date"  placeholder="AAAA-MM-DD" maxlength="250" value="<?=$PSN->f('fechaFacturacion'); ?>" class="form-control" />
				</div>
                <label class="control-label col-sm-2" for="fechaUltimoPago">Fecha del ultimo pago recibido</label>
				<div class="col-sm-4">
					<input name="fechaUltimoPago"  type="date"  placeholder="AAAA-MM-DD" maxlength="250" value="<?=$PSN->f('fechaUltimoPago'); ?>" class="form-control" />
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
        </div>
        </div>

        <div id="tab2" class="tab-pane fade">
		<div id="container">
            <br />
            
            <div class="form-group">
                <label class="control-label col-sm-2" for="mensajecotizacion">Cotizaión</label>
                <div class="col-sm-10">
                    <textarea name="mensajecotizacion" id="mensajecotizacion" cols="40" rows="6" class="ckeditor"><?=stripslashes($PSN->f('mensajecotizacion')); ?></textarea>
                </div>
            </div>        
            
            <?php
            if($PSN->f('mensajecotizacion') != ""){
                ?><br /><br />
                <div class="form-group">
                    <div class="row text-center">
                        <a href="imprimir.php?doc=cotizacion_imp&id=<?=$PSN->f('id'); ?>" class="btn btn-info" target="_blank">Imprimir</a>
                    </div>
                </div><?php                
            }
            ?>
            
        </div>
        </div>
    </div>
    </div>
            
        <br />
        <div class="row text-center">
            <input type="hidden" name="funcion" id="funcion" value="" />
            <input type="submit" value="Guardar cambios" class="btn btn-success" /> <a href="index.php?doc=main" class="btn btn-danger">Cerrar</a>
        </div>
                
            
                

            <?php
			//if($PSN->f('estado') != 2){
			?>
				<!--<br />
				<center><input type="button" name="button" onclick="generarFormDel()" value="Eliminar Registro" /></center>//-->
                    
            <div class="form-group">
                <h2 class="text-center well">.SEGUIMIENTOS.</h2>
            </div>

			<div class="form-group">
				<iframe src ="int.php?doc=int_seguimientos&id=<?=soloNumeros($_GET["id"]); ?>#final" name="frameObs" id="frameObs" width="100%" height="300px" frameborder="0" marginheight="0" marginwidth="0"></iframe>
			</div>

			<div class="form-group">
				<input type="button" class="btn btn-success" name="button" onclick="asignarObservacion()" value="+ Seguimiento">
			</div>

							
										           
            <div class="form-group">
                <h2 class="text-center well">.GASTOS.</h2>
            </div>

			<div class="form-group">
				<iframe src ="int.php?doc=int_gastos&id=<?=soloNumeros($_GET["id"]); ?>#final" name="frameObsG" id="frameObsG" width="100%" height="300px" frameborder="0" marginheight="0" marginwidth="0"></iframe>
			</div>

			<div class="form-group">
				<input type="button" class="btn btn-success" name="button" onclick="asignarGasto()" value="+ Gasto">
			</div>
							           
            <div class="form-group">
                <h2 class="text-center well">.FACTURACI&Oacute;N.</h2>
            </div>
                
			<div class="form-group">
				<iframe src ="int.php?doc=int_facturacion&id=<?=soloNumeros($_GET["id"]); ?>#final" name="frameObsF" id="frameObsF" width="100%" height="300px" frameborder="0" marginheight="0" marginwidth="0"></iframe>
			</div>

			<div class="form-group">
				<input type="button" class="btn btn-success" name="button" onclick="asignarAbono()" value="+ Abono">
			</div>

			</div>



            </form>

			<script language="javascript">
				function cambioCliente(){
					document.getElementById('idContacto').value = "0";
					document.getElementById('contactoNombre').value = "";
					document.getElementById('contactoTelefono').value = "";
					document.getElementById('contactoCelular').value = "";
					document.getElementById('contactoEmail').value = "";

				}
				function generarForm(){
						if(confirm("Esta accion actualizara el REGISTRO en el sistema, esta seguro que desea continuar?"))
						{
							if(document.getElementById('contactoNombre').value != "")
							{
								document.getElementById('funcion').value = "actualizar";
								//document.getElementById('form1').submit();
								return true;
							}
							else
							{
								alert("La informacion es primordial para brindarle un excelente servicio, por favor digite al menos el campo de NOMBRE");
							}
						}
						return false;
				}

				function regresar()
				{
					window.location.href = "index.php?doc=cotizacion";
				}
				
				function asignarObservacion(idd){
					window.open("pop_up.php?doc=pop_seguimiento&id=<?=soloNumeros($_GET["id"]); ?>", "seguimiento", "status=1, scrollbars=1, height=600, width=840");
				}

				function asignarAbono(idd){
					window.open("pop_up.php?doc=pop_abono&id=<?=soloNumeros($_GET["id"]); ?>", "abono", "status=1, scrollbars=1, height=600, width=840");
				}

				function asignarGasto(idd){
					window.open("pop_up.php?doc=pop_gasto&id=<?=soloNumeros($_GET["id"]); ?>", "gasto", "status=1, scrollbars=1, height=600, width=840");
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
					
					$(function() {
						//
						$("#contactoNombre").autocomplete({
							source: function( request, response ) {
								var varidCliente = document.getElementById('idCliente').value; // selected 
								// Set value to textboxes
								document.getElementById('idContacto').value = "0";
								document.getElementById('contactoTelefono').value = "";
								document.getElementById('contactoCelular').value = "";
								document.getElementById('contactoEmail').value = "";
								//
								//
								$.ajax( {
									url: "busquedaContacto.php",
									dataType: "jsonp",
									data: {
										term: request.term,
										idCliente: varidCliente,
										request: "1"
									},
									success: function( data ) {
										response(data);
									}
								} );
							},
							minLength: 2,
							select: function(event, ui) {
								//alert(ui.item.value + " aka " + ui.item.id);
								var userid = ui.item.value; // selected value
								var varidCliente = document.getElementById('idCliente').value; // selected value
								// AJAX
								$.ajax({
									url: 'busquedaContacto.php',
									type: 'post',
									data: {
										id:userid,
										idCliente:varidCliente,
										request:2
									},
									dataType: 'json',
									success:function(response){
										var len = response.length;
										if(len > 0)
										{
											var id = response[0]['id'];
											var contactonombre = response[0]['contactonombre'];
											var contactotelefono = response[0]['contactotelefono'];
											var contactocelular = response[0]['contactocelular'];
											var contactoemail = response[0]['contactoemail'];

											// Set value to textboxes
											document.getElementById('idContacto').value = id;
											document.getElementById('contactoNombre').value = contactonombre;
											document.getElementById('contactoTelefono').value = contactotelefono;
											document.getElementById('contactoCelular').value = contactocelular;
											document.getElementById('contactoEmail').value = contactoemail;
											//
										}
									}
								});
								return false;						
								//Fin
							}
						});
					});					
					
				}
				
				window.onload = function(){
					init();
				}
				</script>
				<?php
				//}
				}
			}		
			else
			{
				?><div class="container"></div><div class="form-group">
				<h2><font color="#FF0000">ID Incorrecto. No Existe o no esta autorizado para visualizar la misma.</font></h2></div>
				</div><?php
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
		$sqlB= "SELECT ".$tablaConsulta.".*, 
						categorias.descripcion as nomEstado, 
						categoriasPa.descripcion as nomEstadoPago, 
						categoriasPr.descripcion as nomCategoriaPrincipal, 
						categoriasCo.descripcion as nomMetodoCotizacion, 
			cliente.nombre as nombreCliente";
		$sqlB.=" FROM ".$tablaConsulta;
			$sqlB.=" LEFT JOIN categorias ON categorias.id = ".$tablaConsulta.".estado ";
			$sqlB.=" LEFT JOIN categorias categoriasPa ON categoriasPa.id = ".$tablaConsulta.".estadoPago ";
			$sqlB.=" LEFT JOIN categorias categoriasPr ON categoriasPr.id = ".$tablaConsulta.".categoriaPrincipal ";
			$sqlB.=" LEFT JOIN categorias categoriasCo ON categoriasCo.id = ".$tablaConsulta.".metodoCotizacion ";
			$sqlB.=" LEFT JOIN cliente ON cliente.id = ".$tablaConsulta.".idCliente ";
		$sqlB.=" WHERE 1 ";
        //
        //Si es un comercial solo puede agregar prospectos de si mismo
        if($_SESSION["perfil"] == 163){
            $sql.=" AND cliente.idComercial = '".$_SESSION["id"]."'";
        }
    
		
		//
		$sqlC= "SELECT count(".$tablaConsulta.".id) as conteo"; // sum(".$tablaConsulta.".valor_prima_neta) as total_neto";
		$sqlC.=" FROM ".$tablaConsulta;
		$sqlC.=" LEFT JOIN cliente ON cliente.id = cotizacion.idCliente";
		$sqlC.=" WHERE 1 ";


		if(eliminarInvalidos($_GET["contactoNombre"]) != "")
		{
			$sql.=" AND ".$tablaConsulta.".contactoNombre  LIKE '%".eliminarInvalidos($_GET["contactoNombre"])."%'";
		}

		if(eliminarInvalidos($_GET["contactoTelefono"]) != "")
		{
			$sql.=" AND ".$tablaConsulta.".contactoTelefono  LIKE '%".eliminarInvalidos($_GET["contactoTelefono"])."%'";
		}

		if(eliminarInvalidos($_GET["contactoCelular"]) != "")
		{
			$sql.=" AND ".$tablaConsulta.".contactoCelular  LIKE '%".eliminarInvalidos($_GET["contactoCelular"])."%'";
		}

		if(eliminarInvalidos($_GET["contactoEmail"]) != "")
		{
			$sql.=" AND ".$tablaConsulta.".contactoEmail  LIKE '%".eliminarInvalidos($_GET["contactoEmail"])."%'";
		}

		if(soloNumeros($_GET["estado"]) != "")
		{
			$sql.=" AND ".$tablaConsulta.".estado = '".soloNumeros($_GET["estado"])."'";
		}
		
		
		if(soloNumeros($_GET["idCliente"]) != "")
		{
			$sql.=" AND ".$tablaConsulta.".idCliente = '".soloNumeros($_GET["idCliente"])."'";
		}
		
		if(soloNumeros($_GET["estadoPago"]) != "")
		{
			$sql.=" AND ".$tablaConsulta.".estadoPago = '".soloNumeros($_GET["estadoPago"])."'";
		}
		
		if(soloNumeros($_GET["categoriaPrincipal"]) != "")
		{
			$sql.=" AND ".$tablaConsulta.".categoriaPrincipal = '".soloNumeros($_GET["categoriaPrincipal"])."'";
		}

		if(soloNumeros($_GET["metodoCotizacion"]) != "")
		{
			$sql.=" AND ".$tablaConsulta.".metodoCotizacion = '".soloNumeros($_GET["metodoCotizacion"])."'";
		}

		if(soloNumeros($_GET["tipoContacto"]) != "")
		{
			$sql.=" AND ".$tablaConsulta.".tipoContacto = '".soloNumeros($_GET["tipoContacto"])."'";
		}

		
		if(eliminarInvalidos($_GET["fechaInicial"]) != "")
		{
			$sql.=" and ".$tablaConsulta.".fechaCotizacion >= '".eliminarInvalidos($_GET["fechaInicial"])."'";
			$fechaInicial = eliminarInvalidos($_GET["fechaInicial"]);
		}

		if(eliminarInvalidos($_GET["fechaFinal"]) != "")
		{
			$sql.=" and ".$tablaConsulta.".fechaCotizacion <= '".eliminarInvalidos($_GET["fechaFinal"])."'";
			$fechaFinal = eliminarInvalidos($_GET["fechaFinal"]);
		}
		
		
		$sqlO =" ORDER BY ".$tablaConsulta.".id DESC";
		
		if($opc == 2){
			$PSN->query($sqlC.$sql.$sqlO);
			$num=$PSN->num_rows();
			if($num > 0)
			{
				if($PSN->next_record()){
					$num = $PSN->f('conteo');
					$numTotal = $PSN->f('conteo');
					$totalValorNeto = $PSN->f('total_neto');
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
                    <h2 class="text-center well">.FILTROS DE BUSQUEDA - <?=$nombreConsulta; ?>.</h2>
                </div>
                
				<div class="form-group">
					<label class="control-label col-sm-2" for="idCliente">Prospecto</label>
					<div class="col-sm-4">
						<select name="idCliente" class="form-control">
						<option value="">Todos</option>
						<?php
						$PSNTEMP = new DBbase_Sql;
						$sql= "SELECT cliente.id, cliente.nombre, count(cotizacion.id) AS totalCot ";
							$sql.=" FROM cliente, cotizacion WHERE cotizacion.idCliente = cliente.id";
        
                        //Si es un comercial solo puede agregar prospectos de si mismo
                        if($_SESSION["perfil"] == 163){
                            $sql.=" AND cliente.idComercial = '".$_SESSION["id"]."'";
                        }
            
						$sql.=" GROUP BY cliente.id ORDER BY nombre asc ";

						$PSNTEMP->query($sql);
						$num=$PSNTEMP->num_rows();
						if($num > 0)
						{
							while($PSNTEMP->next_record())
							{
								?><option value="<?=$PSNTEMP->f('id'); ?>" <?php
								if(soloNumeros($_GET["idCliente"]) == $PSNTEMP->f('id'))
								{
									?>selected="selected"<?php 
								}
								?>><?=$PSNTEMP->f('nombre')." (".$PSNTEMP->f('totalCot').")"; ?><br /><?php
							}
						}
						?>
						</select>
					</div>
                </div>
                
				<div class="form-group">
                    <label class="control-label col-sm-2" for="contactoNombre">Nombre del contacto</label>
					<div class="col-sm-4">
						<input type="text" name="contactoNombre" id="contactoNombre" value="<?=eliminarInvalidos($_GET["contactoNombre"]); ?>" class="form-control" />
					</div>
                    <label class="control-label col-sm-2" for="contactoCelular">Celular del contacto</label>
					<div class="col-sm-4">
						<input type="text" name="contactoCelular" id="contactoCelular" value="<?=eliminarInvalidos($_GET["contactoCelular"]); ?>" class="form-control" />
					</div>
				</div>

				<div class="form-group">
                    <label class="control-label col-sm-2" for="fechaInicial">Fecha inicial</label>
					<div class="col-sm-4">
						<input name="fechaInicial" type="date" id="fechaInicial" placeholder="AAAA-MM-DD" value="<?=eliminarInvalidos($_GET["fechaInicial"]); ?>" class="form-control" />
                    </div>
                    <label class="control-label col-sm-2" for="fechaFinal">Fecha Final</label>
					<div class="col-sm-4">
						<input name="fechaFinal" type="date" id="fechaFinal" placeholder="AAAA-MM-DD" value="<?=eliminarInvalidos($_GET["fechaFinal"]); ?>" class="form-control" />
					</div>
				</div>


				<div class="form-group">
                    <label class="control-label col-sm-2" for="estado">Estado de la cotizaci&oacute;n</label>
					<div class="col-sm-4">
						<select name="estado" class="form-control">
						<option value="">Todos</option>
						<?php
						$PSNTEMP = new DBbase_Sql;
						$sql= "SELECT categorias.* ";
							$sql.=" FROM categorias ";
							$sql.=" WHERE idSec = 16 ";
						$sql.=" ORDER BY descripcion asc ";

						$PSNTEMP->query($sql);
						$num=$PSNTEMP->num_rows();
						if($num > 0)
						{
							while($PSNTEMP->next_record())
							{
								?><option value="<?=$PSNTEMP->f('id'); ?>" <?php
								if(soloNumeros($_GET["estado"]) == $PSNTEMP->f('id'))
								{
									?>selected="selected"<?php 
								}
								?>><?=$PSNTEMP->f('descripcion'); ?><br /><?php
							}
						}
						?>
						</select>
					</div>

                    <label class="control-label col-sm-2" for="estadoPago">Estado del pago</label>
					<div class="col-sm-4">
						<select name="estadoPago" class="form-control">
						<option value="">Todos</option>
						<?php
						$PSNTEMP = new DBbase_Sql;
						$sql= "SELECT categorias.* ";
							$sql.=" FROM categorias ";
							$sql.=" WHERE idSec = 17 ";
						$sql.=" ORDER BY descripcion asc ";

						$PSNTEMP->query($sql);
						$num=$PSNTEMP->num_rows();
						if($num > 0)
						{
							while($PSNTEMP->next_record())
							{
								?><option value="<?=$PSNTEMP->f('id'); ?>" <?php
								if(soloNumeros($_GET["estadoPago"]) == $PSNTEMP->f('id'))
								{
									?>selected="selected"<?php 
								}
								?>><?=$PSNTEMP->f('descripcion'); ?><br /><?php
							}
						}
						?>
						</select>
					</div>
				</div>
					

				<div class="form-group">
                    <label class="control-label col-sm-2" for="categoriaPrincipal">Categoria</label>
					<div class="col-sm-4">
						<select name="categoriaPrincipal" class="form-control">
						<option value="">Todos</option>
						<?php
						$PSNTEMP = new DBbase_Sql;
						$sql= "SELECT categorias.* ";
							$sql.=" FROM categorias ";
							$sql.=" WHERE idSec = 18 ";
						$sql.=" ORDER BY descripcion asc ";

						$PSNTEMP->query($sql);
						$num=$PSNTEMP->num_rows();
						if($num > 0)
						{
							while($PSNTEMP->next_record())
							{
								?><option value="<?=$PSNTEMP->f('id'); ?>" <?php
								if(soloNumeros($_GET["categoriaPrincipal"]) == $PSNTEMP->f('id'))
								{
									?>selected="selected"<?php 
								}
								?>><?=$PSNTEMP->f('descripcion'); ?><br /><?php
							}
						}
						?>
						</select>
					</div>

                    <label class="control-label col-sm-2" for="metodoCotizacion">Metodo de cotizaci&oacute;n</label>
					<div class="col-sm-4">
						<select name="metodoCotizacion"class="form-control">
						<option value="">Todos</option>
						<?php
						$PSNTEMP = new DBbase_Sql;
						$sql= "SELECT categorias.* ";
							$sql.=" FROM categorias ";
							$sql.=" WHERE idSec = 19 ";
						$sql.=" ORDER BY descripcion asc ";

						$PSNTEMP->query($sql);
						$num=$PSNTEMP->num_rows();
						if($num > 0)
						{
							while($PSNTEMP->next_record())
							{
								?><option value="<?=$PSNTEMP->f('id'); ?>" <?php
								if(soloNumeros($_GET["metodoCotizacion"]) == $PSNTEMP->f('id'))
								{
									?>selected="selected"<?php 
								}
								?>><?=$PSNTEMP->f('descripcion'); ?><br /><?php
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
                <h2 class="text-center well">.Se encontraron <?=$numTotal; ?> registros.</h2>
            </div>

            <table border="0" cellspacing="0" cellpadding="2"  align="center" class="table table-bordered" style="font-size:12px">
            <thead>
			<tr>
				<th align="center">No</th>
				<?php /*<th align="center">ID</th>*/ ?>
				<th align="center">Prospecto</th>
				<th style="white-space:nowrap;" align="center">Estado</th>
				<th style="white-space:nowrap;" align="center">Estado Pago</th>
				<th align="center">Categoria Principal</th>
				<th align="center">Metodo Cotizacion</th>
				<th align="center">Contacto Nombre</th>
				<th align="center">Valor Cotizado</th>
				<th align="center">Valor Cobrado</th>
				<?php /* <th align="center">valorRecibido</th>
				<th align="center">fechaCotizacion</th>
				<th align="center">fechaFacturacion</th>
				<th align="center">fechaUltimoPago</th> */ ?>
			</tr>
            </thead>
            <tbody><?php
				if($num > 0)
				{
					$izq = 1;
					$contador = $inicio+1;
					while($PSN->next_record())
					{
					$fechaVencimiento = date("Y-m-d", strtotime("+364 days", strtotime($PSN->f('fecha_vigencia_ini'))));
						?>
						<tr <?php if($contador%2==0){ ?>bgcolor="#EEEEEE"<?php } ?>>
							<td><a href="index.php?doc=<?=$webArchivo; ?>&opc=2&id=<?=$PSN->f('id');?>"><?=$contador; ?></a></td>
							<?php /*<td><a href="index.php?doc=<?=$webArchivo; ?>&opc=2&id=<?=$PSN->f('id');?>"><?=$PSN->f('id');?></a></td> */ ?>
							<td><a href="index.php?doc=<?=$webArchivo; ?>&opc=2&id=<?=$PSN->f('id');?>"><?=$PSN->f('nombreCliente');?></a></td>
							<td align="center" style="white-space:nowrap;"><a href="index.php?doc=<?=$webArchivo; ?>&opc=2&id=<?=$PSN->f('id');?>"><?=$PSN->f('nomEstado');?></a></td>
							<td align="center"><?=$PSN->f('nomEstadoPago');?></td>
							<td><?=$PSN->f('nomCategoriaPrincipal');?></td>
							<td align="center"><?=$PSN->f('nomMetodoCotizacion');?></td>
							<td><?=$PSN->f('contactoNombre');?></td>
							<td align="right">$<?=number_format($PSN->f('valorCotizado'), 0, "," , ".");?></td>
							<td align="right">$<?=number_format($PSN->f('valorCobrado'), 0, "," , ".");?></td>
							<?php /*<td><?=number_format($PSN->f('valorRecibido'), 0, "," , ".");?></td>
							<td><?=$PSN->f('fechaCotizacion');?></td>
							<td><?=$PSN->f('fechaFacturacion');?></td>
							<td><?=$PSN->f('fechaUltimoPago');?></td>*/ ?>
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


			<center><?php
			echo "<a href='index.php?doc=cotizacion&opc=3&excelX=
						&idCliente=".eliminarInvalidos($_GET["idCliente"])."
						&estado=".eliminarInvalidos($_GET["estado"])."
						&estadoPago=".eliminarInvalidos($_GET["estadoPago"])."
						&categoriaPrincipal=".eliminarInvalidos($_GET["categoriaPrincipal"])."
						&metodoCotizacion=".eliminarInvalidos($_GET["metodoCotizacion"])."
						&tipoContacto=".eliminarInvalidos($_GET["tipoContacto"])."
						&contactoNombre=".eliminarInvalidos($_GET["contactoNombre"])."
						&contactoTelefono=".eliminarInvalidos($_GET["contactoTelefono"])."
						&contactoCelular=".eliminarInvalidos($_GET["contactoCelular"])."
						&contactoEmail=".eliminarInvalidos($_GET["contactoEmail"])."
						&descripcion=".eliminarInvalidos($_GET["descripcion"])."
						&valorCotizado=".eliminarInvalidos($_GET["valorCotizado"])."
						&valorCobrado=".eliminarInvalidos($_GET["valorCobrado"])."
						&valorRecibido=".eliminarInvalidos($_GET["valorRecibido"])."
						&fechaCotizacion=".eliminarInvalidos($_GET["fechaCotizacion"])."
						&fechaFacturacion=".eliminarInvalidos($_GET["fechaFacturacion"])."
						&fechaUltimoPago=".eliminarInvalidos($_GET["fechaUltimoPago"])."
						&excel=1'
						class='btn btn-info'><span class='glyphicon glyphicon-cloud-download'></span> DESCARGAR PARA EXCEL</a> ";    
            ?></center><?php
		}
		else if(isset($_GET["excel"]))
		{
			//
			$sqlB= "SELECT ".$tablaConsulta.".*, 
							categorias.descripcion as nomEstado, 
							categoriasPa.descripcion as nomEstadoPago, 
							categoriasPr.descripcion as nomCategoriaPrincipal, 
							categoriasCo.descripcion as nomMetodoCotizacion, 
							SUM(abonos.valor) as totalAbonos,
							cliente.nombre as nombreCliente";
			$sqlB.=" FROM ".$tablaConsulta;
				$sqlB.=" LEFT JOIN categorias ON categorias.id = ".$tablaConsulta.".estado ";
				$sqlB.=" LEFT JOIN categorias categoriasPa ON categoriasPa.id = ".$tablaConsulta.".estadoPago ";
				$sqlB.=" LEFT JOIN categorias categoriasPr ON categoriasPr.id = ".$tablaConsulta.".categoriaPrincipal ";
				$sqlB.=" LEFT JOIN categorias categoriasCo ON categoriasCo.id = ".$tablaConsulta.".metodoCotizacion ";
				$sqlB.=" LEFT JOIN cliente ON cliente.id = ".$tablaConsulta.".idCliente ";
				$sqlB.=" LEFT JOIN abonos ON abonos.idCotizacion = ".$tablaConsulta.".id ";
			$sqlB.=" WHERE 1 ";
			
			$sql .= " GROUP BY ".$tablaConsulta.".id";
			$PSN->query($sqlB.$sql);
			$num=$PSN->num_rows();
			?><table>
			<tr>
				<th colspan="15">Se han encontrado <?=$total_registros; ?> registros.</th>
			</tr>
			<tr>
				<th>No</th>
				<th>ID en la Base de Datos</th>
				<th>Prospecto</th>
				<th>Estado</th>
				<th>Estado Pago</th>
				<th>Categoria Principal</th>
				<th>Metodo Cotizacion</th>
				<th>Contacto Nombre</th>
				<th>Valor Cotizado</th>
				<th>Valor Cobrado</th>
				<th>Abonos recibidos</th>
				<th>Saldo</th>				
				<th>Fecha Cotizacion</th>
				<th>Fecha Facturacion</th>
				<th>Fecha Ultimo Pago</th>
			</tr><?php
				if($num > 0)
				{
					$izq = 1;
					$contador = $inicio+1;
					while($PSN->next_record())
					{
					$fechaVencimiento = date("Y-m-d", strtotime("+364 days", strtotime($PSN->f('fecha_vigencia_ini'))));
						?>
						<tr <?php if($contador%2==0){ ?>bgcolor="#EEEEEE"<?php } ?>>
							<td><?=$contador; ?></td>
							<td><?=$PSN->f('id');?></td>
							<td><?=$PSN->f('nombreCliente');?></td>
							<td align="center" style="white-space:nowrap;"><?=$PSN->f('nomEstado');?></td>
							<td align="center"><?=$PSN->f('nomEstadoPago');?></td>
							<td><?=$PSN->f('nomCategoriaPrincipal');?></td>
							<td align="center"><?=$PSN->f('nomMetodoCotizacion');?></td>
							<td><?=$PSN->f('contactoNombre');?></td>
							<td align="right"><?=$PSN->f('valorCotizado');?></td>
							<td align="right"><?=$PSN->f('valorCobrado');?></td>
							<td><?=$PSN->f('totalAbonos');?></td>
							<td><?=$PSN->f('valorCobrado')-$PSN->f('totalAbonos');?></td>
							<td><?=$PSN->f('fechaCotizacion');?></td>
							<td><?=$PSN->f('fechaFacturacion');?></td>
							<td><?=$PSN->f('fechaUltimoPago');?></td>
						</tr>
						<?php
						$contador++;
					}
				}		
				else
				{
					?><tr>
					  <td colspan="10" align="center"><h2>.No hay registros.</h2></td>
					</tr><?php
				}	
				?>  
			</table>
			<br />
			<p align="left">
			<?php			
		}
	}
}
?>