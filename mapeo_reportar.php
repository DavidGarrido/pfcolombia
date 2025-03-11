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
$tablaConsulta = "sat_pdfs";
$webArchivo = "mapeo_reportar";
$nombreConsulta = "MAPEO DE IGLESIAS";

$PSN1 = new DBbase_Sql;
if($opc == 1)
{
		if(isset($_POST["cuarto"]))
		{
			$PSN = new DBbase_Sql;

			$cuarto = soloNumeros($_POST["cuarto"]);
			$anho = soloNumeros($_POST["anho"]);
			$creacionUsuario = $_SESSION["id"];
            $nombre_archivo = $_FILES['archivo']['name'];
            $temp_location = $_FILES['archivo']['tmp_name'];
            $temp_ext = extension_archivo($nombre_archivo);
			//
			$sql = 'insert into '.$tablaConsulta.' (
                idFacilitador,
				fecha,  
				anho,  
				cuarto,
				ext
			) ';

			$sql .= 'values (
				"'.$creacionUsuario.'", 
				"'.date("Y-m-d").'", 
				"'.$anho.'", 
				"'.$cuarto.'",
				"'.$temp_ext.'"
			)';
			$ultimoQuery = $PSN->query($sql);
			$ultimoId = $PSN->ultimoId();
            echo $ultimoId;
            if($ultimoId > 0 && $_FILES['archivo']['name'] != ""){
                //
                //  Documento de PDF
                //
                $temp_nombreFile = $ultimoId.".".$temp_ext;
                echo "<br/>Arc: ".$nombre_archivo;
                echo "<br/>Loc: ".$temp_location;
                if(move_uploaded_file($temp_location, "archivos/mapeos/".$temp_nombreFile))
                {
                }
            }

			?>
			<SCRIPT LANGUAGE="JavaScript">
			alert("Se ha creado correctamente el registro.");
			window.location.href= "index.php?doc=<?=$webArchivo; ?>&opc=2&id=<?=$ultimoId; ?>";
			</script>
			<?php
		}
		else
		{
			$PSN = new DBbase_Sql;
			?>
			<div class="container">
            <form method="post" enctype="multipart/form-data" name="form1" id="form1" class="form-horizontal">

                <div class="form-group">
                    <h2 class="alert alert-info text-center">.SUBIR MAPEO AL SISTEMA.</h2>
                </div>
                
                <div class="form-group">
					<label class="control-label col-sm-2" for="nombre">Año</label>
				    <div class="col-sm-4">
					  <input name="anho" id="anho" type="number" maxlength="4" value="<?=date("Y"); ?>" class="form-control" />
					</div>

                    <label class="control-label col-sm-2" for="tipoCliente">Cuarto:</label>
                    <div class="col-sm-4">
                    <select name="cuarto" class="form-control">
                        <option value="1" <?php if($_POST['cuarto'] == 1){ ?>selected<?php } ?>>Q1</option>
                        <option value="2" <?php if($_POST['cuarto'] == 2){ ?>selected<?php } ?>>Q2</option>
                        <option value="3" <?php if($_POST['cuarto'] == 3){ ?>selected<?php } ?>>Q3</option>
                        <option value="4" <?php if($_POST['cuarto'] == 4){ ?>selected<?php } ?>>Q4</option>
                    </select>
    			    </div>
                </div>

                <div class="form-group">
                  <label class="control-label col-sm-2" for="nit">Archivo PDF:</label>
				  <div class="col-sm-4">
					  <input name="archivo" type="file" class="form-control" />
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
                                return true;
                            }
                            else
                            {
                                alert("La informacion es primordial para brindarle un excelente servicio, por favor digite al menos el campo de NOMBRE");
                                return false;
                            }
                        }else{
                            return false;
                        }
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
		if(isset($_POST["anho"]))
		{
			/*
			*	ACTUALIZAR
			*/
			if ($_POST["funcion"] == "actualizar")
			{
				$cuarto = soloNumeros($_POST["cuarto"]);
                $anho = soloNumeros($_POST["anho"]);
				$modUsuario = $_SESSION["id"];
				//
                $nombre_archivo = $_FILES['archivo']['name'];
                $temp_location = $_FILES['archivo']['tmp_name'];
                $temp_ext = extension_archivo($nombre_archivo);
                //
				$sql = 'update '.$tablaConsulta.' set 
                            cuarto = "'.$cuarto.'",
							anho = "'.$anho.'"';
				//
				if($nombre_archivo != ""){
					$sql .= ', ext = "'.$temp_ext.'"';
				}
				//
				$sql .= ' WHERE  id='.soloNumeros($_GET["id"]);
				$PSN->query($sql);
				//
                if($_FILES['archivo']['name'] != ""){
                    //
                    //  Documento de PDF
                    //
                    $temp_nombreFile = soloNumeros($_GET["id"]).".".$temp_ext;
                    
                    if(move_uploaded_file($temp_location, "archivos/mapeos/".$temp_nombreFile))
                    {
                    }
                }
				?>
				<SCRIPT LANGUAGE="JavaScript">
				    alert("Se ha ACTUALIZADO correctamente el registro!");
				    window.location.href= "index.php?doc=<?=$webArchivo; ?>&opc=2&id=<?=soloNumeros($_GET["id"]); ?>";
				</script>
				<?php
			}
		}
		else
		{
			$sql= "SELECT ".$tablaConsulta.".*";
			$sql.=" FROM ".$tablaConsulta;
			$sql.=" WHERE ".$tablaConsulta.".id='".soloNumeros($_GET["id"])."'";
            //Si es un facilitador solo puede ver sus archivos  de si mismo
            if($_SESSION["perfil"] == 163){
                $sql.=" AND idFacilitador = '".$_SESSION["id"]."'";
            }
			
			$PSN->query($sql);
			$num=$PSN->num_rows();
			if($num > 0)
			{
				$izq = 1;
				if($PSN->next_record())
				{
					$PSN2 = new DBbase_Sql;
 			    ?><div class="container">
    	            <form method="post" enctype="multipart/form-data" name="form1" id="form1" class="form-horizontal">
    				
                    <div class="form-group">
                        <h2 class="alert alert-info text-center">.ACTUALIZACI&Oacute;N DE <?=$nombreConsulta; ?> EN EL SISTEMA.</h2>
                    </div>

                    <div class="form-group">
    					<label class="control-label col-sm-2" for="nombre">Año</label>
    				    <div class="col-sm-4">
    					  <input name="anho" id="anho" type="number" maxlength="4" value="<?=$PSN->f('anho'); ?>" class="form-control" />
    					</div>
    
                        <label class="control-label col-sm-2" for="tipoCliente">Cuarto:</label>
                        <div class="col-sm-4">
                        <select name="cuarto" class="form-control">
                            <option value="1" <?php if($PSN->f('cuarto') == 1){ ?>selected<?php } ?>>Q1</option>
                            <option value="2" <?php if($PSN->f('cuarto') == 2){ ?>selected<?php } ?>>Q2</option>
                            <option value="3" <?php if($PSN->f('cuarto') == 3){ ?>selected<?php } ?>>Q3</option>
                            <option value="4" <?php if($PSN->f('cuarto') == 4){ ?>selected<?php } ?>>Q4</option>
                        </select>
        			    </div>
                    </div>
                    
    
                    <div class="form-group">
                        <label class="control-label col-sm-2" for="nit">Nuevo archivo PDF:</label>
    				    <div class="col-sm-4">
    					  <input name="archivo" type="file" class="form-control" />
    					</div>

                        <label class="control-label col-sm-2" for="nit">Descargar:</label>
    				    <div class="col-sm-4">
                            <a href="archivos/mapeos/<?=$PSN->f('id'); ?>.<?=$PSN->f('ext'); ?>" target="_blank" class="btn btn-info"><span class="glyphicon glyphicon-cloud-download"></span> DESCARGAR ARCHIVO</a></center>
                        </div>
    			    </div>
    			    
    
                    <div class="form-group">
                    

                    <div class="row text-center">
                        <input type="hidden" name="funcion" id="funcion" value="actualizar" />
                        <input type="submit" value="Guardar cambios" class="btn btn-success" /> 
                        <a href="index.php?doc=main" class="btn btn-danger">Cerrar</a>
                    </div>

                    </form>
                </div>
                <script language="javascript">
                    function generarForm(){
                        if(confirm("Esta accion actualizara el REGISTRO en el sistema, esta seguro que desea continuar?"))
                        {
                            if(document.getElementById('anho').value != "")
                            {
                                document.getElementById('funcion').value = "actualizar";
                            }
                            else
                            {
                                return false;
                                alert("La informacion es primordial para brindarle un excelente servicio, por favor digite el anho");
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
                        window.location.href = "index.php?doc=cliente";
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
				?><div class="row"><h2><font color="#FF0000">ID Incorrecto. No Existe o no esta autorizado para visualizar la misma.</font></h2></div><?php
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
		$PSN2 = new DBbase_Sql;

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
        $sqlB= "SELECT ".$tablaConsulta.".*, usuario.nombre as nombre_facilitador";
        $sqlB.=" FROM ".$tablaConsulta;
            $sqlB.=" LEFT JOIN usuario ON usuario.id = ".$tablaConsulta.".idFacilitador ";
        $sqlB.=" WHERE 1 ";
        //

		$sqlC= "SELECT count(".$tablaConsulta.".id) as conteo";
		$sqlC.=" FROM ".$tablaConsulta;
		$sqlC.=" WHERE 1 ";
        
		if(eliminarInvalidos($_GET["anho"]) != "")
		{
			$sql.=" AND ".$tablaConsulta.".anho  = '".soloNumeros($_GET["anho"])."'";
		}
		
		if(soloNumeros($_GET["cuarto"]) != "")
		{
			$sql.=" AND ".$tablaConsulta.".cuarto  = '".soloNumeros($_GET["cuarto"])."'";
		}

		if(soloNumeros($_GET["idFacilitador"]) != "")
		{
		    $buscar_idUsuario = soloNumeros($_GET["idFacilitador"]);
			$sql.=" AND ".$tablaConsulta.".idFacilitador = '".soloNumeros($_GET["idFacilitador"])."'";
		}
		$sql.=" ORDER BY ".$tablaConsulta.".fecha DESC";
		
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
        //echo $sqlB.$sql;
		$numclientes=$PSN->num_rows();
		?>
		<div class="container">
            <form action="index.php" name="form" id="form" method="get" class="form-horizontal">
    	    <input type="hidden" name="doc" value="<?=$webArchivo; ?>" />
	        <input type="hidden" name="opc" value="2" />
			
            <div class="form-group">
                <h2 class="text-center well">.FILTROS DE BUSQUEDA - <?=$nombreConsulta; ?>.</h2>
            </div>

            <div class="form-group">
                <label class="control-label col-sm-2" for="idFacilitador"><strong>Facilitador Satura:</strong></label>
                <div class="col-sm-4"><?php
                ?><select name="idFacilitador" onchange="this.form.submit()" class="form-control">
                <?php
                if($_SESSION["perfil"] != 163){
                    ?><option value="">Ver todos</option><?php
                }
        
                /*
                *	TRAEMOS LOS USUARIOS
                */
                $sql = "SELECT * ";
                $sql.=" FROM usuario ";
                $sql.=" WHERE tipo IN (162, 163) ";
                if($_SESSION["perfil"] == 163){
                    $sql.=" AND id = '".$_SESSION["id"]."'";
                }
                $sql.=" ORDER BY nombre asc";
    
                $PSN2->query($sql);
                $numero=$PSN2->num_rows();
                if($numero > 0)
                {
                    while($PSN2->next_record())
                    {
                        ?><option value="<?=$PSN2->f('id'); ?>" <?php
                        if($buscar_idUsuario == $PSN2->f('id'))
                        {
                            ?>selected="selected"<?php
                        }
                        ?>><?=$PSN2->f('nombre'); ?></option><?php
                    }
                }
                ?></select>
                </div>
			</div>

            <div class="form-group">
				<label class="control-label col-sm-2" for="nombre">Año</label>
			    <div class="col-sm-4">
				  <input name="anho" id="anho" type="number" maxlength="4" value="<?=soloNumeros($_REQUEST["anho"]); ?>" class="form-control" />
				</div>

                <label class="control-label col-sm-2" for="cuarto">Cuarto:</label>
                <div class="col-sm-4">
                <select name="cuarto" class="form-control">
                    <option value="">Ver todos</option>
                    <option value="1" <?php if($_REQUEST['cuarto'] == 1){ ?>selected<?php } ?>>Q1</option>
                    <option value="2" <?php if($_REQUEST['cuarto'] == 2){ ?>selected<?php } ?>>Q2</option>
                    <option value="3" <?php if($_REQUEST['cuarto'] == 3){ ?>selected<?php } ?>>Q3</option>
                    <option value="4" <?php if($_REQUEST['cuarto'] == 4){ ?>selected<?php } ?>>Q4</option>
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
        <h2 class="text-center well">.Se encontraron <?=intval($numTotal); ?> registros.</h2>
    </div>

    <table border="0" cellspacing="0" cellpadding="2"  align="center" class="table table-bordered" style="font-size:12px">
        <thead>
		<tr>
			<th align="center">No.</th>
			<th align="center">Nombre Facilitador</th>
			<th align="center">Año</th>
			<th align="center">Cuarto</th>
			<th align="center">Archivo</th>
		</tr>
        </thead>
        <tbody>
            <?php
			if($numclientes > 0)
			{
				$izq = 1;
				$contador = $inicio+1;
				while($PSN->next_record())
				{
				$fechaVencimiento = date("Y-m-d", strtotime("+364 days", strtotime($PSN->f('fecha_vigencia_ini'))));
					?>
					<tr <?php if($contador%2==0){ ?>bgcolor="#EEEEEE"<?php } ?>>
						<td><a href="index.php?doc=<?=$webArchivo; ?>&opc=2&id=<?=$PSN->f('id');?>"><?=$contador; ?></a></td>
                        <td><a href="index.php?doc=<?=$webArchivo; ?>&opc=2&id=<?=$PSN->f('id');?>"><?=$PSN->f('nombre_facilitador');?></td></a>
						<td><a href="index.php?doc=<?=$webArchivo; ?>&opc=2&id=<?=$PSN->f('id');?>"><?=$PSN->f('anho');?></a></td>
						<td><a href="index.php?doc=<?=$webArchivo; ?>&opc=2&id=<?=$PSN->f('id');?>"><?php
						    switch($PSN->f('cuarto')){
						        case 1:
						            echo "Q1";
						            break;
						        case 2:
						            echo "Q2";
						            break;
						        case 3:
						            echo "Q3";
						            break;
						        case 4:
						            echo "Q4";
						            break;
					           default:
					               break;
						    }
						?></a></td>
						<td><a href="archivos/mapeos/<?=$PSN->f('id'); ?>.<?=$PSN->f('ext'); ?>" target="_blank" class="btn btn-info"><span class="glyphicon glyphicon-cloud-download"></span> DESCARGAR ARCHIVO</a></center></td>
					</tr>
					<?php
					$contador++;
				}
			}		
			else
			{
				?><tr>
				  <td colspan="11" align="center"><h2>.No hay registros.</h2></td>
				</tr><?php
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
		<?	
	}
}
?>