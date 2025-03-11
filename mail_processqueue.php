<?php
//phpinfo();
//exit;
//Descubrir el path real del archivo para los trabajos cron.
//var_dump(__FILE__);
if(isset($_REQUEST["test"])){
	?><textarea cols="90" rows="80"><?php
}

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
//
// Ignore user aborts and allow the script
// to run forever
ignore_user_abort(true);
set_time_limit(0);
ini_set('max_execution_time', 1200); //300 seconds = 5 minutes
//
include_once('/home/icarqxql/public_html/phpmailer/src/PHPMailer.php');
include_once('/home/icarqxql/public_html/phpmailer/src/SMTP.php');
include_once('/home/icarqxql/public_html/phpmailer/src/Exception.php');
$mail = new PHPMailer();
//
include_once('/home/icarqxql/public_html/funciones.php');
//
//
$PSN = new DBbase_Sql;
$PSNT = new DBbase_Sql;

//
$sql= "SELECT * ";
$sql.=" FROM mail_workqueue ";
$sql.=" ORDER BY id ASC";
$sql.=" LIMIT 25";
//
$PSN->query($sql);
if($PSN->num_rows() == 0)
{
	//Nada
	echo "No hay nada en la cola...";
}
else
{
	while($PSN->next_record())
	{
		$idHistorico = $PSN->f('id');
		$idCampana = $PSN->f('idCampana');
		$idUsuario = $PSN->f('idUsuario');
		//
		$audit_usuario = $idUsuario;
		$audit_ip = $_SERVER['REMOTE_ADDR'];
		//
		//
		//$mail->isSMTP();
		$mail->isMail();
		$mail->IsHTML(true);
		//
		//
		$mail->Host = $PSN->f('host');
		$mail->Username = $PSN->f('username');
		$mail->Password = $PSN->f('password');
		$mail->Port = $PSN->f('port');
		//
		$remite_correo = $PSN->f('remite_correo');
		$remite_nombre = utf8_decode($PSN->f('remite_nombre'));
		//
		//$mail->SMTPAuth = true;
            //$mail->SMTPSecure = "tls"; //ssl
		//$mail->SMTPKeepAlive = true; // SMTP connection will not close after each email sent, reduces SMTP overhead
		
		$mail->setFrom($remite_correo, $remite_nombre);
		$mail->addReplyTo($remite_correo, $remite_nombre);
		$mail->Subject = utf8_decode(stripslashes($PSN->f('subject')));
		//
		/*echo "<hr />";
		echo "<hr />";
		echo $PSN->f('host')." - host<br />";
		echo $PSN->f('username')." - usuario<br />";
		echo $PSN->f('password')." - pass<br />";
		echo $PSN->f('port')." - puerto<br />";
		echo $PSN->f('remite_correo')." - remite_correo<br />";
		echo $PSN->f('remite_nombre')." - remite_nombre<br />";
		echo $PSN->f('remite_nombre')." - remite_nombre<br />";*/
		
		if($PSN->f('attachment_ruta') != ""){
			//echo "\nRUTA NO VACIA";
			$arrayArchivosRutas = explode(",", $PSN->f('attachment_ruta'));
			$arrayArchivosNames = explode(",", $PSN->f('attachment_nombre'));
			$maxArchivos = count($arrayArchivosRutas);
			$i = 0;
			while($i < $maxArchivos){
				//echo "\nENTRO AL WHILE ".$i;
				if(file_exists($arrayArchivosRutas[$i])){
						//echo "\n\nArchivo encontrado".$arrayArchivosRutas[$i]." - ".$arrayArchivosNames[$i];
						//You'll need to alter this to match your database
						$mail->AddAttachment("/home/icarqxql/public_html/".$arrayArchivosRutas[$i], $arrayArchivosNames[$i]); //Assumes the image data is stored in the 
				}else{
					//echo "\n\nNO encontro ruta ".$arrayArchivosRutas[$i];
				}
				$i++;
			}
		}
		//
		$mail_email = $PSN->f('mail_email');
		$mail_nombres = utf8_decode($PSN->f('mail_nombres'));
		$msgActual = utf8_decode(stripslashes($PSN->f('message')));
		$mail->msgHTML($msgActual);
		//If you generate a different body for each recipient (e.g. you're using a templating system),
		$mail->AltBody = 'Tu correo no admite mensajes en formato HTML. :(';
		//
		//
		$mail->addAddress($mail_email, $mail_nombres);
		if(!$mail->send()){
			echo "\nError de correo (".$mail_email. ') '.$remite_correo."-".$mail->ErrorInfo;
			//break; //Abandon sending
			//
			//Guardamos el log de errores.
			$sql = "INSERT INTO mail_historico (idCampana, error, fecha, audit_usuario, audit_fecha, audit_ip) ";
			$sql .= " VALUES('".$idCampana."', 1, '".date("Y-m-d")."', '".$audit_usuario."', NOW(), '".$audit_ip."')";
			$sql .= " ON DUPLICATE KEY UPDATE error = (error+1)";
			$PSNT->query($sql);
			//
			$contadorError++;								
		}
		else 
		{
			$array_usr_sent[] = $mail_email;
			echo "\nMensaje enviado a : ".$mail_nombres.' (' .$mail_email. ')';
			//
			//Guardamos el log de envio.
			$sql = "INSERT INTO mail_historico (idCampana, correos, fecha, audit_usuario, audit_fecha, audit_ip) ";
			$sql .= " VALUES('".$idCampana."', 1, '".date("Y-m-d")."', '".$audit_usuario."', NOW(), '".$audit_ip."')";
			$sql .= " ON DUPLICATE KEY UPDATE correos = (correos+1)";
			$PSNT->query($sql);
			//
			$contadorOk++;
		}
		$sql = "DELETE FROM mail_workqueue WHERE id = '".$idHistorico."'";
		$PSNT->query($sql);
		//
		$mail->ClearAllRecipients();//clearAddresses();	
	}//Fin Envio a listas.
}
//
if($contadorOk > 0)
{
	echo "\n\n\nMENSAJES ENVIADOS CON EXITO: ".$contadorOk;
}
if($contadorError > 0)
{
	echo "\n\n\nMENSAJES EN ERROR: ".$contadorError;
}

if(isset($_REQUEST["test"])){
	?></textarea><?php
}

?>