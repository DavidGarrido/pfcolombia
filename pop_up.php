<?
session_start();
//session_register("SESSION");
include_once('funciones.php');

$array_meses = array(
"Enero",
"Enero",
"Febrero",
"Marzo",
"Abril",
"Mayo",
"Junio",
"Julio",
"Agosto",
"Septiembre",
"Octubre",
"Noviembre",
"Diciembre"
);

$array_semana = array(
"Mon" => "Lunes",
"Tue" => "Martes", 
"Wed" => "Miercoles", 
"Thu" => "Jueves", 
"Fri" => "Viernes", 
"Sat" => "S&aacute;bado", 
"Sun" => "Domingo",
); 

$mesactual = intval(date("m"));
$anhoactual = intval(date("Y"));
$diaactual = intval(date("d"));
$semanaactual = date("D"); 

if(trim($_SESSION["imagen"]) == "")
{
	$_SESSION["imagen"] = "LogoWeb.jpg";				
}				
?><html>
<head>
<title><?=$gloPrograma; ?> - <?=$gloEmpresa	; ?></title>
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<? /*<link rel="stylesheet" type="text/css" href="estilos.css" />
<link rel="stylesheet" href="menu/menu_style.css" type="text/css" />
<script language="javascript" type="text/javascript" src="scripts/prototype.js"></script> */ ?>

<!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
<!-- jQuery library -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<!-- Latest compiled JavaScript -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

    
<script type="text/javascript">
function getfocus(id){
	if(document.getElementById(id))
    {
		document.getElementById(id).focus()
	}
}
</script>
</head>
<body>
<?
/*
*	AQUI VA EL CONTENIDO.
*/
if(isset($_GET["doc"]) && !empty( $_GET["doc"]) && is_logged_in())
{
	/*
	* MENU - MENU - MENU
	*/
	?><div class="container-fluid">
        <nav class="navbar navbar-default">
            <div class="container-fluid">
            <div class="navbar-header">
                <a href="index.php?doc=main"><img src="images/logoanglo.png" height="50px" /></a>
            </div>
                
            <ul class="nav navbar-nav navbar-right">
              <li><a href="javascript:window.close();void(0);">Cerrar</a></li>
            </ul>
        </nav>
    <?
	/*
	* FIN DEL MENU - MENU - MENU
	*/
	$docu = eliminarInvalidos($_GET["doc"]);
	if(trim($docu) == "")
	{
	}
	else
	{
		include_once("$docu.php");
	}
	?>
	<span id="footer"><center>
	<hr color="#0000FF">
	<font size="1"><?=$gloPrograma; ?> - <?=$gloEmpresa; ?>
	<br />
	Copyright 2018 - <?=date("Y"); ?> <?=$gloEmpresa; ?></font></center></span>
    </div><?
}
else
{
	?>DEBE ESTAR LOGUEADO PARA ACCEDER A ESTA APLICACION
	<br />
	<br />
	<span id="footer"><center>
	<hr color="#0000FF">
	<font size="1"><?=$gloPrograma; ?> - <?=$gloEmpresa; ?>
	<br />
	Copyright 2018 - <?=date("Y"); ?> <?=$gloEmpresa; ?></font></center></span><?
}
?>
</body></html>