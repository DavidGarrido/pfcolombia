<?php
/*if($_SESSION["superusuario"] != 1)
{
	die("<h1>No esta autorizado a visualizar esta opcion.</h1>");
}*/
?><div class="container">
	<div class="row">
        <h2 class="text-center well">CATEGORÍAS DISPONIBLES</h2>
	</div>

	<div class="row text-center">
    <?php
	$contador = 34;
	while($contador < 306){
		//Solo si no se ha modificado ya el formulario.
		$id = $contador+1;
            switch($id){
                case 85:
                    echo '<div class="col-sm-3" style="margin-top: 5px; margin-bottom: 5px;"><a href="index.php?doc=admin_buscarcat&idcat='.$id.'" class="btn btn-primary btn-block">ZONAS</a></div>';
                    break;
                case 84:
                    echo '<div class="col-sm-3" style="margin-top: 5px; margin-bottom: 5px;"><a href="index.php?doc=admin_buscarcat&idcat='.$id.'" class="btn btn-primary btn-block">REGIONALES</a></div>';
                    break;
                case 83:
                    echo '<div class="col-sm-3" style="margin-top: 5px; margin-bottom: 5px;"><a href="index.php?doc=admin_buscarcat&idcat='.$id.'" class="btn btn-primary btn-block">PRISIONES</a></div>';
                    break;
                case 38:
                    echo '<div class="col-sm-3" style="margin-top: 5px; margin-bottom: 5px;"><a href="index.php?doc=admin_buscarcat&idcat='.$id.'" class="btn btn-primary btn-block">PROGRAMAS</a></div>';
                    break;
                case 305:
                    echo '<div class="col-sm-3" style="margin-top: 5px; margin-bottom: 5px;"><a href="index.php?doc=admin_buscarcat&idcat='.$id.'" class="btn btn-primary btn-block">SUBPROGRAMAS</a></div>';
                    break;
                case 78:
                    echo '<div class="col-sm-3" style="margin-top: 5px; margin-bottom: 5px;"><a href="index.php?doc=admin_buscarcat&idcat='.$id.'" class="btn btn-primary btn-block">DIPLOMADOS</a></div>';
                    break;
                case 87:
                    echo '<div class="col-sm-3" style="margin-top: 5px; margin-bottom: 5px;"><a href="index.php?doc=admin_buscarcat&idcat='.$id.'" class="btn btn-primary btn-block">NIVELES DE GRADO</a></div>';
                    break;
            }
			$contador++;
		}
        ?></div></center>
</div>