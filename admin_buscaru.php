<?php
if($_SESSION["perfil"] != 2 AND $_SESSION["perfil"] != 164){
	die("<h1>No esta autorizado para ver esta informaci&oacute;n</h1>");
}
/*
*	$PSN = new DBbase_Sql;
*/
// Objeto de Base de Datos
$PSN1 = new DBbase_Sql;
$PSN2 = new DBbase_Sql;
// Array que nos servira para ir llevando cuenta de los requerimientos.
if(isset($_GET["del"]))
{
	$sql = 'DELETE FROM usuario WHERE id = "'.$_GET["del"].'" and tipo != 4 and tipo != 5 and tipo != 1';
	$ultimoQuery = $PSN1->query($sql);
	$varExitoDEL = 1;
}

// Array que nos servira para ir llevando cuenta de los requerimientos.
/*
*	TRAEMOS LOS colegioS.
*/
$sql = "SELECT usuario.id, usuario.nombre, usuario.telefono1, usuario.celular, usuario.email, categorias.descripcion, cliente.nombre as nomcliente ";
$sql.=" FROM usuario ";
$sql.=" LEFT JOIN categorias ";
$sql.=" ON categorias.id = usuario.tipo";
$sql.=" LEFT JOIN usuario_relacion ON usuario_relacion.idUsuario1 = usuario.id ";
$sql.=" LEFT JOIN usuario as cliente ON cliente.id = usuario_relacion.idUsuario2 AND cliente.tipo = 4";
//
$sql.=" WHERE usuario.id != 2 ORDER BY usuario.nombre ASC";

$PSN1->query($sql);
$numero=$PSN1->num_rows();
?><div class="container">
	<div class="row">
		<h2>.ACCESOS.</h2>
	</div>
	<div class="row">
		<table width="100%" border="0" cellspacing="2" cellpadding="2"  align="center" class="accesos">
			<tr> 
				<th>Id</th>
				<th>Nombre</th>
				<th>Tel&eacute;fono</th>
				<th>Celular</th>
				<th>E-Mail</th>
				<th>Tipo</th>
				<th>Opciones</th>
			</tr>
		<?php
		if($numero > 0)
		{
			$contador = 0;
			while($PSN1->next_record())
			{
				//Solo si no se ha modificado ya el formulario.
				$id = $PSN1->f('id');
				$nomcliente = $PSN1->f('nomcliente');
				$tipodesc = $PSN1->f('descripcion');
				$nombre = $PSN1->f('nombre');
				$telefono1 = $PSN1->f('telefono1');
				$celular = $PSN1->f('celular');
				$email = $PSN1->f('email');

				?><tr <?php if($contador%2==0){ ?>bgcolor="#EEEEEE"<?php } ?>>
				<td><a href="index.php?doc=admin_usu4&id=<?=$id; ?>"><?=str_pad($id, 6, "0", STR_PAD_LEFT); ?></a></td>
				<td><a href="index.php?doc=admin_usu4&id=<?=$id; ?>"><?=$nombre; ?></a></td>
				<td><a href="tel:031<?=$telefono1; ?>"><?=$telefono1; ?></a></td>
				<td><a href="tel:<?=celular; ?>"><?=$celular; ?></a></td>
				<td><?=$email; ?></td>
				<td><?=$tipodesc; ?></td>
				<td>[<a href="javascript:borrarRegistro('<?=$id; ?>');void(0);">BORRAR</a>]</td>
				</tr>
				<?php
				$contador++;
			}
		}
		?>
		</table>
	</div>
</div>

<script language="javascript">
function borrarRegistro(registro){
		if(confirm("Esta accion BORRARA el USUARIO de el sistema, ¿esta seguro que desea continuar?"))
		{
			if(confirm("Recuerde que si el USUARIO tiene Ordenes de Servicio activas esto puede causar perdida de integridad en los datos, esta seguro que desea eliminar este USUARIO?"))
			{
				window.location.href = "index.php?doc=admin_buscaru&del="+registro;
			}
		}
}
function init(){
	<?php
	if($varExitoDEL == 1)
	{
		?>alert("Se ha BORRADO correctamente el USUARIO, espere mientras es dirigido de nuevo a la busqueda.");
		window.location.href = "index.php?doc=admin_buscaru";<?php
	}
	?>
}
window.onload = function(){
	init();
}
</script>