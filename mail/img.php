<?php
if(isset($_REQUEST["usr"]) || isset($_REQUEST["cli"])){

	//session_register("SESSION");
	include_once('../funciones.php');
	$id = soloNumeros($_REQUEST['usr']);
	$idCampana = soloNumeros($_REQUEST['cam']);
	$idCliente = soloNumeros($_REQUEST['cli']);
	$audit_ip = $_SERVER['REMOTE_ADDR'];
	//
	$PSN = new DBbase_Sql;
	$sql = "INSERT INTO mail_log (idCampana, idCliente, idUsuario, fecha, visto, vistoadicional, audit_ip) ";
	$sql .= " VALUES('".$idCampana."', '".$idCliente."', '".$id."', '".date("Y-m-d")."', 1, 0, '".$audit_ip."')";
	$sql .= " ON DUPLICATE KEY UPDATE vistoadicional = (vistoadicional+1)";
	$PSN->query($sql);


	$graphic_http = 'blank.png';
	$filesize = filesize('blank.png');
	//
	header( 'Pragma: public' );
	header( 'Expires: 0' );
	header( 'Cache-Control: must-revalidate, post-check=0, pre-check=0' );
	header( 'Cache-Control: private',false );
	header( 'Content-Disposition: attachment; filename="'.strtotime('now').$id.$idCampana.'.png"');
	header( 'Content-Transfer-Encoding: binary' );
	header( 'Content-Length: '.$filesize );
	readfile($graphic_http);
	exit;
}

?>