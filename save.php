<?php
	require_once 'conn.php';
	
	if(ISSET($_POST['save'])){
		$Nombre = $_POST['Nombre'];
		$Tarjeta = $_POST['Tarjeta'];
		$Regional = $_POST['Regional'];
		$Ciudad = $_POST['Ciudad'];
		$Fecha = $_POST['Fecha'];
		
		mysqli_query($conn, "INSERT INTO `LPP` VALUE('', '$Nombre', '$Tarjeta', '$Regional','$Ciudad','$Fecha')") or die(mysqli_errno());
		header('location: consultar-sub-programa-lpp.php');
			
	}
?>