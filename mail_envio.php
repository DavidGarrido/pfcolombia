<?php
ini_set('max_execution_time', 600); //300 seconds = 5 minutes
//
if($_SESSION["perfil"] != 1 && $_SESSION["perfil"] != 2 && $_SESSION["perfil"] != 161 && $_SESSION["perfil"] != 162 && $_SESSION["perfil"] != 163)
{
	die("<h1>No esta autorizado para ver esta informaci&oacute;n</h1>");
}

if(!isset($_GET["opc"]))
{
	$opc = 1;
}
else
{
	$opc = soloNumeros($_GET["opc"]);
}

$PSN = new DBbase_Sql;
if($opc == 1)
{
	//
	//
	$idCampana = soloNumeros($_POST["idCampana"]);
	if(isset($_POST["mensajecorreo"]))
	{
		$mensajecorreo = eliminarInvalidos($_POST["mensajecorreo"]);
		$asuntocorreo = eliminarInvalidos($_POST["asuntocorreo"]);
		$idConfig = soloNumeros($_POST["idConfig"]);
		//
		if(isset($_POST['listaasociar']))
		{
			$listas = implode(",", $_POST['listaasociar']);
		}
		//
		if($mensajecorreo != "" && $asuntocorreo != "" && $idConfig != "" && $idConfig != "0")
		{
			//
			$array_usr_sent = array();
			//
			$sql= "SELECT * ";
			$sql.=" FROM mail_config ";
			$sql.=" WHERE id = '".$idConfig."'";
			//
			$PSN->query($sql);
			if($PSN->num_rows() == 0)
			{
				//Nada
			}
			else
			{
				if($PSN->next_record())
				{
					//
					$audit_usuario = $_SESSION["id"];
					$audit_ip = $_SERVER['REMOTE_ADDR'];
					//
					$body = str_replace('/videoexpress/scripts/', 'https://icarsoluciones.app/scripts/', $_REQUEST["mensajecorreo"]);
					//
					//
              		//$mail->SMTPDebug = 2;
					//
					$host = $PSN->f('host');
					$username = $PSN->f('usuario');
					$password = $PSN->f('password');
					$port = $PSN->f('puerto');
					//
					$remite_correo = $PSN->f('correo');
					$remite_nombre = $PSN->f('nombre');
					$subject = eliminarInvalidos($_REQUEST["asuntocorreo"]);
					//
					//
					$temp_attachment_ruta = array();
					$temp_attachment_nombre = array();
					//
					if($_FILES['archivos']['name'][0] != ""){

						$file_ary = array();
						$file_count = count($_FILES['archivos']['name']);
						$file_keys = array_keys($_FILES['archivos']);

						for ($i=0; $i<$file_count; $i++) {
							foreach ($file_keys as $key) {
								$file_ary[$i][$key] = $_FILES['archivos'][$key][$i];
							}
						}

						if($file_count > 0) {
							foreach ($file_ary as $file) {
								//do your upload stuff here
								$ruta_temporal = $file['tmp_name'];
								$nombre_archivo = basename($file['name']);
								$tipo_archivo = $file['type'];
								$tamano_archivo = $file['size'];
								//You'll need to alter this to match your database
        						if(move_uploaded_file($ruta_temporal, "temporales/".$nombre_archivo)){
									$temp_attachment_ruta[] = "temporales/".$nombre_archivo;
									$temp_attachment_nombre[] = $nombre_archivo;
								}
							}
						}				
					}
					//
					if(count($temp_attachment_ruta) > 0 && count($temp_attachment_nombre) > 0){
						$attachment_ruta = implode(",", $temp_attachment_ruta);
						$attachment_nombre = implode(",", $temp_attachment_nombre);
					}

					//ENVIO A LISTAS
					if(count($_POST['listaasociar']) > 0 && trim($listas) != "" && $listas != ",")
					{
						$sql= "SELECT sms_usuarios.id, sms_usuarios.nombres, sms_usuarios.email ";
						$sql.=" FROM sms_grupos, sms_asociacion, sms_usuarios";
						$sql.=" WHERE sms_grupos.id IN (".$listas.") AND sms_asociacion.id_grupo = sms_grupos.id AND sms_usuarios.id = sms_asociacion.id_usuario  AND sms_usuarios.email != '' ORDER BY sms_usuarios.id ASC";

						$PSN->query($sql);
						$num=$PSN->num_rows();
						if($num > 0)
						{
							$PSNT = new DBbase_Sql;
							while($PSN->next_record())
							{
								if(!in_array($PSN->f('email'), $array_usr_sent))
								{
									$queue_idUsuario = $PSN->f('id');
									$mail_email = $PSN->f('email');
									$mail_nombres = $PSN->f('nombres');
									
									if(filter_var($mail_email, FILTER_VALIDATE_EMAIL)) {
										//
										//
										$message = $body."<img src='https://icarsoluciones.app/mail/img.php?usr=".$PSN->f('id')."&cam=".$idCampana."&tmp=".strtotime("now")."' />";

										$array_usr_sent[] = $mail_email;
										//Guardamos el log de envio.
										$sql = "INSERT INTO mail_workqueue (
											idUsuario, 
											idCampana,
											host,
											username,
											password,
											port,
											remite_correo,
											remite_nombre,
											subject,
											attachment_ruta,
											attachment_nombre,
											message,
											mail_email,
											mail_nombres											
											) 
											VALUES (";
										
										$sql .= "
											'".$_SESSION["id"]."', 
											'".$idCampana."',
											'".$host."',
											'".$username."',
											'".$password."',
											'".$port."',
											'".$remite_correo."',
											'".$remite_nombre."',
											'".addslashes($subject)."',
											'".$attachment_ruta."',
											'".$attachment_nombre."',
											'".addslashes($message)."',
											'".$mail_email."',
											'".$mail_nombres."'											
											)";
										
										$PSNT->query($sql);
										//
										$contadorOk++;
									}
									else
									{
										$mensajes_error .= "\nCorreo INVALIDO (" . str_replace("@", "&#64;", $mail_email) . ') ' . $mail->ErrorInfo . ' NO fue a&ntilde;adido a la cola de trabajo';
										$contadorError++;
									}
								}
								else
								{
									$mensajes_error .= "\nCorreo DUPLICADO (" . str_replace("@", "&#64;", $PSN->f('email')) . ') ' . $mail->ErrorInfo . '  YA fue a&ntilde;adido a la cola de trabajo';
										$contadorError++;
								}
							}
						}
					}//Fin Envio a listas.

					
					/*
					*
					*	ENVIO A CORREOS MANUALES
					*
					*/
					if(isset($_POST['mensajeusumanual']) && trim($_POST['mensajeusumanual']) != "")
					{
						$sql= "SELECT id, nombres, email ";
						$sql.=" FROM sms_usuarios";
						$sql.=" WHERE id IN (".htmlspecialchars($_POST['mensajeusumanual']).") AND email != ''";

						$PSN->query($sql);
						$num=$PSN->num_rows();
						if($num > 0)
						{
							$PSNT = new DBbase_Sql;
							while($PSN->next_record())
							{
								if(!in_array($PSN->f('email'), $array_usr_sent))
								{
									$queue_idUsuario = $PSN->f('id');
									$mail_email = $PSN->f('email');
									$mail_nombres = $PSN->f('nombres');
									
									if(filter_var($mail_email, FILTER_VALIDATE_EMAIL)) {
										//
										//
										$message = $body."<img src='https://icarsoluciones.app/mail/img.php?usr=".$PSN->f('id')."&cam=".$idCampana."&tmp=".strtotime("now")."' />";

										$array_usr_sent[] = $mail_email;
										//Guardamos el log de envio.
										$sql = "INSERT INTO mail_workqueue (
											idUsuario, 
											idCampana,
											host,
											username,
											password,
											port,
											remite_correo,
											remite_nombre,
											subject,
											attachment_ruta,
											attachment_nombre,
											message,
											mail_email,
											mail_nombres											
											) 
											VALUES (";
										
										$sql .= "
											'".$_SESSION["id"]."', 
											'".$idCampana."',
											'".$host."',
											'".$username."',
											'".$password."',
											'".$port."',
											'".$remite_correo."',
											'".$remite_nombre."',
											'".addslashes($subject)."',
											'".$attachment_ruta."',
											'".$attachment_nombre."',
											'".addslashes($message)."',
											'".$mail_email."',
											'".$mail_nombres."'											
											)";
										
										$PSNT->query($sql);
										//
										$contadorOk++;
									}
									else
									{
										$mensajes_error .= "\nCorreo INVALIDO (" . str_replace("@", "&#64;", $mail_email) . ') ' . $mail->ErrorInfo . ' NO fue a&ntilde;adido a la cola de trabajo';
										$contadorError++;
									}
								}
								else
								{
									$mensajes_error .= "\nCorreo DUPLICADO (" . str_replace("@", "&#64;", $PSN->f('email')) . ') ' . $mail->ErrorInfo . '  YA fue a&ntilde;adido a la cola de trabajo';
										$contadorError++;
								}
							}
						}
					}//Fin correos manuales
					
					/*
					*
					*	ENVIO A CLIENTES
					*
					*/
					if(isset($_POST['idCliente']) && trim($_POST['idCliente']) != "" && soloNumeros($_POST['idCliente']) != "" && soloNumeros($_POST['idCliente']) != "0")
					{
						$sql= "SELECT id, nombre, email ";
						$sql.=" FROM cliente";
						if($_POST['idCliente'] == -99){
							$sql.=" WHERE email != ''";
						}
						else{
							$sql.=" WHERE id = '".soloNumeros($_POST['idCliente'])."' AND email != ''";
						}
						$PSN->query($sql);
						if($PSN->num_rows() > 0)
						{
							if($PSN->next_record())
							{
								if(!in_array($PSN->f('email'), $array_usr_sent))
								{
									$queue_idUsuario = $PSN->f('id');
									$mail_email = $PSN->f('email');
									$mail_nombres = $PSN->f('nombres');
									
									if(filter_var($mail_email, FILTER_VALIDATE_EMAIL)) {
										//
										//
										$message = $body."<img src='https://icarsoluciones.app/mail/img.php?usr=".$PSN->f('id')."&cam=".$idCampana."&tmp=".strtotime("now")."' />";

										$array_usr_sent[] = $mail_email;
										//Guardamos el log de envio.
										$sql = "INSERT INTO mail_workqueue (
											idUsuario, 
											idCampana,
											host,
											username,
											password,
											port,
											remite_correo,
											remite_nombre,
											subject,
											attachment_ruta,
											attachment_nombre,
											message,
											mail_email,
											mail_nombres											
											) 
											VALUES (";
										
										$sql .= "
											'".$_SESSION["id"]."', 
											'".$idCampana."',
											'".$host."',
											'".$username."',
											'".$password."',
											'".$port."',
											'".$remite_correo."',
											'".$remite_nombre."',
											'".addslashes($subject)."',
											'".$attachment_ruta."',
											'".$attachment_nombre."',
											'".addslashes($message)."',
											'".$mail_email."',
											'".$mail_nombres."'											
											)";
										
										$PSNT->query($sql);
										//
										$contadorOk++;
									}
									else
									{
										$mensajes_error .= "\nCorreo INVALIDO (" . str_replace("@", "&#64;", $mail_email) . ') ' . $mail->ErrorInfo . ' NO fue a&ntilde;adido a la cola de trabajo';
										$contadorError++;
									}
								}
								else
								{
									$mensajes_error .= "\nCorreo DUPLICADO (" . str_replace("@", "&#64;", $PSN->f('email')) . ') ' . $mail->ErrorInfo . '  YA fue a&ntilde;adido a la cola de trabajo';
										$contadorError++;
								}
							}
						}
						$sql= "SELECT id, nombres, email ";
						$sql.=" FROM sms_usuarios";
						if($_POST['idCliente'] == -99){
							$sql.=" WHERE idCliente != 0 AND email != ''";
						}
						else{
							$sql.=" WHERE idCliente = '".soloNumeros($_POST['idCliente'])."' AND email != ''";
						}

						$PSN->query($sql);
						$num=$PSN->num_rows();
						if($num > 0)
						{
							$PSNT = new DBbase_Sql;
							while($PSN->next_record())
							{
								if(!in_array($PSN->f('email'), $array_usr_sent))
								{
									$queue_idUsuario = $PSN->f('id');
									$mail_email = $PSN->f('email');
									$mail_nombres = $PSN->f('nombres');
									
									if(filter_var($mail_email, FILTER_VALIDATE_EMAIL)) {
										//
										//
										$message = $body."<img src='https://icarsoluciones.app/mail/img.php?usr=".$PSN->f('id')."&cam=".$idCampana."&tmp=".strtotime("now")."' />";

										$array_usr_sent[] = $mail_email;
										//Guardamos el log de envio.
										$sql = "INSERT INTO mail_workqueue (
											idUsuario, 
											idCampana,
											host,
											username,
											password,
											port,
											remite_correo,
											remite_nombre,
											subject,
											attachment_ruta,
											attachment_nombre,
											message,
											mail_email,
											mail_nombres											
											) 
											VALUES (";
										
										$sql .= "
											'".$_SESSION["id"]."', 
											'".$idCampana."',
											'".$host."',
											'".$username."',
											'".$password."',
											'".$port."',
											'".$remite_correo."',
											'".$remite_nombre."',
											'".addslashes($subject)."',
											'".$attachment_ruta."',
											'".$attachment_nombre."',
											'".addslashes($message)."',
											'".$mail_email."',
											'".$mail_nombres."'											
											)";
										
										$PSNT->query($sql);
										//
										$contadorOk++;
									}
									else
									{
										$mensajes_error .= "\nCorreo INVALIDO (" . str_replace("@", "&#64;", $mail_email) . ') ' . $mail->ErrorInfo . ' NO fue a&ntilde;adido a la cola de trabajo';
										$contadorError++;
									}
								}
								else
								{
									$mensajes_error .= "\nCorreo DUPLICADO (" . str_replace("@", "&#64;", $PSN->f('email')) . ') ' . $mail->ErrorInfo . '  YA fue a&ntilde;adido a la cola de trabajo';
										$contadorError++;
								}
							}
						}
					}//Fin correos clientes					
				}
			}
		}

		if($contadorOk > 0){
			echo "<strong>Se han enviado <strong>".intval($contadorOk)."</strong> correos a la cola de trabajo.
			<br/><br />
			Tenga en cuenta que segun la configuracion del servidor los archivos enviados por medio de la cola de trabajo seran evacuados a razon de aproximadamente 300 correos por hora, esto para evitar que su servidor sea marcado como SPAM.</strong><br /><br />";			
		}
		
		if($contadorError > 0)
		{
			?><center><textarea cols="80" rows="20"><?=$mensajes_error; ?></textarea></center><?php
		}
	}

	echo '<script type="text/javascript" src="scripts/jquery-1.7.1.min.js"></script>'."\n";
	echo '<script type="text/javascript" src="scripts/jquery-ui/js/jquery-1.7.1.min.js"></script>'."\n";
	echo '<script type="text/javascript" src="scripts/jquery-ui/js/jquery-ui-1.8.17.custom.min.js"></script>'."\n";
	echo '<script type="text/javascript" src="scripts/jquery.form.js"></script>'."\n";
	echo '<script type="text/javascript" src="scripts/jquery.validate.js"></script>'."\n";
	echo '<script type="text/javascript" src="scripts/jquery.validate.additional-methods.js"></script>'."\n";
	echo '<script type="text/javascript" src="scripts/jquery.tokeninput.js"></script>'."\n";
	echo '<script type="text/javascript" language="javascript" src="scripts/jquery.corner.js"></script>'."\n";
	echo '<link href="scripts/token-input.css" rel="stylesheet" type="text/css">';
	echo '<script type="text/javascript" src="scripts/ckeditor/ckeditor.js"></script>'."\n";

	?><div class="container">

            <div class="form-group">
                <h2 class="alert alert-info text-center">.ENVIO DE CORREOS.</h2>
            </div>
    
        <ul class="nav nav-tabs">
            <li class="active"><a data-toggle="tab" href="#tab1">Datos generales</a></li>
            <li><a data-toggle="tab" href="#tab2">Grupos para envio</a></li>
            <li><a data-toggle="tab" href="#tab3">Contactos para envio</a></li>
            <li><a data-toggle="tab" href="#tab4">Prospecto para envio</a></li>
        </ul>
    
		<form action="index.php?doc=mail_envio&opc=1" method="post" id="envio_masivo_mail" name="envio_masivo_mail"  enctype="multipart/form-data" class="form-horizontal">

            
<div class="row">
<div class="tab-content">
            
<div id="tab1" class="tab-pane fade in active">
            
		<div id="container">
        <br />
		<div class="form-group">
			<label class="control-label col-sm-2" for="idCampana">Campa&ntilde;a</label>
			<div class="col-sm-10">
				<select name="idCampana" class="form-control">
                <option value="">Ninguna</option>
				<?php
				$PSNTEMP = new DBbase_Sql;
				$sql= "SELECT id, nombre ";
					$sql.=" FROM campana ";
				$sql.=" WHERE fechaInicial <= '".date("Y-m-d")."' and fechaFinal >= '".date("Y-m-d")."'";
				$sql.=" ORDER BY nombre asc ";

				$PSNTEMP->query($sql);
				$num=$PSNTEMP->num_rows();
				if($num > 0)
				{
					while($PSNTEMP->next_record())
					{
						?><option value="<?=$PSNTEMP->f('id'); ?>" <?php
						if(soloNumeros($_POST["idCampana"]) == $PSNTEMP->f('id'))
						{
							?>selected="selected"<? 
						}
						?>><?=$PSNTEMP->f('nombre'); ?></option><?php
					}
				}
				?>
				</select>
			</div>
		</div>


		<div class="form-group">
			<label class="control-label col-sm-2" for="idConfig">Cuenta de remitente</label>
			<div class="col-sm-10">
				<select name="idConfig" class="form-control">
				<?php
				$PSNTEMP = new DBbase_Sql;
				$sql= "SELECT id, nombre, defecto ";
					$sql.=" FROM mail_config ";
				$sql.=" ORDER BY nombre asc ";

				$PSNTEMP->query($sql);
				$num=$PSNTEMP->num_rows();
				if($num > 0)
				{
					while($PSNTEMP->next_record())
					{
						?><option value="<?=$PSNTEMP->f('id'); ?>" <?php
						if(!isset($_POST["idConfig"]) && $PSNTEMP->f('defecto') == 1)
						{
							?>selected="selected"<? 							
						}
						else if(soloNumeros($_POST["idConfig"]) == $PSNTEMP->f('id'))
						{
							?>selected="selected"<? 
						}
						?>><?=$PSNTEMP->f('nombre'); ?></option><?php
					}
				}
				?>
				</select>
			</div>
		</div>

		<div class="form-group">
            <label class="control-label col-sm-2" for="asuntocorreo">Asunto</label>
			<div class="col-sm-10">
				<input type="text" name="asuntocorreo" id="asuntocorreo" placeholder="Asunto del correo" class="form-control" />
			</div>
		</div>

		<div class="form-group">
            <label class="control-label col-sm-2" for="mensajecorreo">Mensaje</label>
			<div class="col-sm-10">
				<textarea name="mensajecorreo" id="mensajecorreo" cols="40" rows="6" class="ckeditor"></textarea>
			</div>
		</div>

		<div class="form-group">
            <label class="control-label col-sm-2" for="archivos">Archivos adjuntos</label>
			<div class="col-sm-10">
				<input type="file" name="archivos[]" id="archivos" class="inputfile form-control" data-multiple-caption="{count} archivos seleccionados" multiple />
				<label class="control-label col-sm-2" for="archivos"><span><img src="images/outbox.png" height="26px" /> Escoger archivo(s) para adjuntar</span></label>
			</div>
		</div>
		</div>
</div>

<div id="tab2" class="tab-pane fade">
		<div id="container">
        <div class="row">
            <h3 class="text-center well">.GRUPOS.</h3>
        </div>

		<div class="form-group">
		<?php
		$sql= "SELECT sms_grupos.* ";
		$sql.=" FROM sms_grupos ";
		$sql.=" ORDER BY nombre asc ";

		$PSN->query($sql);
		$num=$PSN->num_rows();
		if($num > 0)
		{
			while($PSN->next_record())
			{
				?><div class="col-sm-6">
					<input type="checkbox" name="listaasociar[]" value="<?=$PSN->f('id'); ?>" class="form-control" /><?=$PSN->f('nombre'); ?>
				</div><?php
			}
		}
		?>	
		</div>
		</div>
</div>

<div id="tab3" class="tab-pane fade">
    <div id="container">
    <br />
    <div class="form-group">
        <label class="control-label col-sm-2" for="mensajeusumanual">Usuarios Individuales:</label>
        <div class="col-sm-10">
            <input type="text" id="mensajeusumanual" name="mensajeusumanual" />
        </div>
    </div>
    </div>
</div>

<div id="tab4" class="tab-pane fade">
    <div id="container">
    <br />
    <div class="form-group">
        <label class="control-label col-sm-2" for="idCliente">Prospecto para envio</label>
        <div class="col-sm-10">
            <select name="idCliente" class="form-control">
                <option value="">Sin seleccionar</option>
                <option value="-99">TODOS LOS PROSPECTOS (OJO!)</option>
            <?php
            $PSN = new DBbase_Sql;
            $sql= "SELECT cliente.id, cliente.nombre ";
                $sql.=" FROM cliente ";
            //Si es un comercial solo puede agregar prospectos de si mismo
            if($_SESSION["perfil"] == 162){
                $sql.=" WHERE idComercial = '".$_SESSION["id"]."'";
            }
    
            $sql.=" ORDER BY nombre asc ";

            $PSN->query($sql);
            $num=$PSN->num_rows();
            if($num > 0)
            {
                while($PSN->next_record())
                {
                    ?><option value="<?=$PSN->f('id'); ?>" <?php
                    /*if(soloNumeros($_POST["idCliente"]) == $PSN->f('id'))
                    {
                        ?>selected="selected"<? 
                    }*/
                    ?>><?=$PSN->f('nombre'); ?></option><?php
                }
            }
            ?>
            </select>
        </div>
        </div>
    </div>
</div>		
			
    <div class="container">
        <div class="row">
            <center><input type="submit" name="button" value="Enviar mensaje" class="btn btn-success"> <a href="index.php?doc=main" class="btn btn-danger">Cerrar</a> </center>
        </div>
    </div>
    </form>
    </div>

    <script type="text/javascript">
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

    $(document).ready(function() {
        $("#mensajeusumanual").tokenInput("busquedaE.php");

    });

    var inputs = document.querySelectorAll( '.inputfile' );
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

    window.onload = function(){
        document.getElementById("defaultOpen").click();
    }				
    </script>

	<?php
}
else if($opc == 2)
{
	$registros = 50;
	$pagina = $_GET["pagina"];
	if (!$pagina) { 
		$inicio = 0; 
		$pagina = 1; 
	} 
	else
	{ 
		$inicio = ($pagina - 1) * $registros; 
	}

	$PSN = new DBbase_Sql;
	$saldo_actual = 0;
	$sql= "SELECT SUM(mensajes) as mensajes ";

	$sql.=" FROM sms_historico";
	$PSN->query($sql);
	$num=$PSN->num_rows();
	if($num > 0)
	{
		if($PSN->next_record())
		{
			$saldo_actual = $PSN->f('mensajes');
		}
	}
	/*
	*
	*
	*/
	$sql= "SELECT mail_historico.*, usuario.nombre ";
	$sql.=" FROM mail_historico LEFT JOIN usuario";
	//
	$sql.=" ON usuario.id = mail_historico.audit_usuario ";
	$sql.=" ORDER BY mail_historico.audit_fecha DESC";
	$PSN->query($sql);
	$num=$PSN->num_rows();

	$total_registros = $num;
	$total_paginas = ceil($total_registros / $registros); 

	$sql= "SELECT mail_historico.*, usuario.nombre, campana.nombre as nombrecam ";
	$sql.=" FROM mail_historico LEFT JOIN usuario ON  usuario.id = mail_historico.audit_usuario";
	$sql.=" LEFT JOIN campana ON campana.id = mail_historico.idCampana";
	//
	$sql.=" ORDER BY audit_fecha DESC LIMIT ".$inicio.", ".$registros;;
	$PSN->query($sql);
	$num=$PSN->num_rows();
	

	?><div class="container">
        <div class="row">
            <h2 class="text-center well">.HISTORICO EMAIL.</h2>
        </div>
                
        <div class="row">
            <h2 class="text-center well">.Se encontraron <?=intval($num); ?> registros.</h2>
        </div>

        <table border="0" cellspacing="0" cellpadding="2"  align="center" class="table table-bordered" style="font-size:12px">
            <thead>
				<tr>
				  <th align="center">No.</th>
				  <th align="center">Campa&nacute;a</th>
				  <th align="center">Fecha</th>
				  <th align="center">Correos Enviados</th>
				  <th align="center">Correos en Error</th>
				  <th align="center">Usuario</th>
				</tr>
            </thead>
            <tbody><?php
            if($num > 0)
            {
                $izq = 1;
                $contador = 1;
                while($PSN->next_record())
                {
                    $tipo = $PSN->f('tipo');
                    ?>
                    <tr <? if($contador%2==0){ ?>bgcolor="#EEEEEE"<? } ?>>
                        <td><?=$contador; ?></td>
                        <td><?=$PSN->f('nombrecam');?></td>
                        <td><?=$PSN->f('fecha');?></td>
                        <td style="text-align:center"><?=$PSN->f('correos'); ?></td>
                        <td style="text-align:center"><?=$PSN->f('error');?></td>
                        <td><?=$PSN->f('nombre');?></td>
                    </tr>
                    <?php
                    $contador++;
                }
            }		
            ?></tbody>
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
else if($opc == 3)
{
	$registros = 50;
	$pagina = $_GET["pagina"];
	if (!$pagina) { 
		$inicio = 0; 
		$pagina = 1; 
	} 
	else
	{ 
		$inicio = ($pagina - 1) * $registros; 
	}

	$PSN = new DBbase_Sql;
	/*
	*
	*
	*/
	$sql= "SELECT mail_workqueue.id";
	$sql.=" FROM mail_workqueue";
	$PSN->query($sql);
	$num=$PSN->num_rows();

	$total_registros = $num;
	$total_paginas = ceil($total_registros / $registros); 

	$sql= "SELECT mail_workqueue.subject, mail_workqueue.mail_email, mail_workqueue.mail_nombres, usuario.nombre, campana.nombre as nombrecam ";
	$sql.=" FROM mail_workqueue LEFT JOIN usuario ON  usuario.id = mail_workqueue.idUsuario";
	$sql.=" LEFT JOIN campana ON campana.id = mail_workqueue.idCampana";
	//
	$sql.=" ORDER BY mail_workqueue.id ASC LIMIT ".$inicio.", ".$registros;;
	$PSN->query($sql);
	$num=$PSN->num_rows();
	

	?><div class="container">
        <div class="row">
            <h2 class="text-center well">.COLA DE TRABAJO.</h2>
            
        </div>
            
        <div class="alert alert-warning">
            Correos pendientes por enviar: <?=intval($total_registros); ?>
        </div>
                
        <div class="row">
            <h2 class="text-center well">.Se encontraron <?=intval($num); ?> registros.</h2>
        </div>

        <table border="0" cellspacing="0" cellpadding="2"  align="center" class="table table-bordered" style="font-size:12px">
            <thead>
				<tr>
				  <th align="center">No.</th>
				  <th align="center">Campa&nacute;a</th>
				  <th align="center">Usuario</th>
				  <th align="center">Asunto</th>
				  <th align="center">Para - Nombre</th>
				  <th align="center">Para - Correo</th>
				</tr>
            </thead>
            <tbody><?php
		if($num > 0)
		{
			$izq = 1;
			$contador = 1;
			while($PSN->next_record())
			{
				$tipo = $PSN->f('tipo');
				?>
				<tr <? if($contador%2==0){ ?>bgcolor="#EEEEEE"<? } ?>>
                    <td><?=$contador; ?></td>
                    <td><?=$PSN->f('nombrecam');?></td>
                    <td><?=$PSN->f('nombre');?></td>
                    <td><?=$PSN->f('subject');?></td>
                    <td><?=$PSN->f('mail_nombres');?></td>
                    <td><?=$PSN->f('mail_email');?></td>
                </tr>
				<?php
				$contador++;
			}
		}		
		?></tbody>
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
            
	<script>
		setInterval(function() {
                  window.location.reload();
                }, 300000); 
	</script><?php
}
?>