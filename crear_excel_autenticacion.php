<?php
session_start();

include('conn.php');

$nombre = $_POST["txtusuario"];
$pass 	= $_POST["txtpassword"];

//Para iniciar sesión
if(isset($_POST["btnloginx"]))
{

$queryusuario = mysqli_query($conn,"SELECT * FROM login WHERE usu = '$nombre'");
$nr 		= mysqli_num_rows($queryusuario); 
$mostrar	= mysqli_fetch_array($queryusuario); 
	
if (($nr == 1) && (password_verify($pass,$mostrar['pass'])) )
	{ 
		session_start();
		$_SESSION['nombredelusuario']=$nombre;
		header("Location: principal.php");
	}
else
	{
	echo "<script> alert('Usuario o contraseña incorrecto.');window.location= 'index.html' </script>";
	}
}

// Verifica si el usuario está autenticado
if(!isset($_SESSION['authenticated']) || $_SESSION['authenticated'] !== true) {
    // El usuario no está autenticado, redirige a la página de inicio de sesión
    header('Location: login.html');
    exit();
}

// El usuario está autenticado, permite la descarga del archivo
require_once 'conn.php';

// Construye el contenido del archivo Excel
$output = "<table><thead><tr><th>Nombre</th><th>Tarjeta</th><th>Regional</th><th>Ciudad</th><th>Fecha</th></tr></thead><tbody>";

$query = mysqli_query($conn, "SELECT * FROM `LPP`") or die(mysqli_error($conn));
while($fetch = mysqli_fetch_array($query)){
    $output .= "<tr><td>".$fetch['Nombre']."</td><td>".$fetch['Tarjeta']."</td><td>".$fetch['Regional']."</td><td>".$fetch['Ciudad']."</td><td>".$fetch['Fecha']."</td></tr>";
}

$output .= "</tbody></table>";

// Envía los encabezados para descargar el archivo
header("Content-Type: application/xls");    
header("Content-Disposition: attachment; filename=documento_exportado_" . date('Y-m-d H:i:s') . ".xls");
header("Pragma: no-cache"); 
header("Expires: 0");

// Envía el contenido como un archivo descargable
echo $output;
exit(); // Esto evita cualquier salida adicional que pueda romper la descarga del archivo
?>
