<?php
/*
*   $PSN = new DBbase_Sql;
*/
// Objeto de Base de Datos
$PSN1 = new DBbase_Sql;
$PSN2 = new DBbase_Sql;
$PSN3 = new DBbase_Sql;


if(!isset($_REQUEST["fechaInicial"]) || eliminarInvalidos($_REQUEST["fechaInicial"]) == ""){
    //$three_months_ago = date("Y-m-d", strtotime("-3 months"));
        $three_months_ago = '2000-01-01';
    $_REQUEST["fechaInicial"] = $three_months_ago;
}
if(!isset($_REQUEST["fechaFinal"]) || eliminarInvalidos($_REQUEST["fechaFinal"]) == ""){
    $siguiente_anho = date("Y", strtotime("+1 year"));
    //$_REQUEST["fechaFinal"] = $siguiente_anho."-01-31";
    $_REQUEST["fechaFinal"] = date("Y-m-d");
}
    //
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

 /*
    *   TRAEMOS LOS registros.
    */
    $sql = "SELECT count(DISTINCT sat_reportes.id) as conteo ";
    $sql .= " FROM sat_reportes ";
    $sql .= " LEFT JOIN usuario AS U ON U.id = sat_reportes.idUsuario 
LEFT JOIN usuario_empresa AS UE ON UE.idUsuario = U.id 
LEFT JOIN tbl_regional_ubicacion AS RU ON RU.reub_id = sat_reportes.sitioReunion
LEFT JOIN categorias AS C ON C.id = RU.reub_reg_fk 
LEFT JOIN categorias AS CA ON CA.id = C.idSec ";
    $sql .= " WHERE sat_reportes.rep_tip = 317 ";
    //
    if($_SESSION["perfil"] == 163){
        $_REQUEST["idUsuario"] = $_SESSION["id"];
    }
    //
    if(isset($_REQUEST["idUsuario"]) && soloNumeros($_REQUEST["idUsuario"]) != ""){
        $buscar_idUsuario = soloNumeros($_REQUEST["idUsuario"]);
        $sqlFiltro .= " AND sat_reportes.idUsuario = '".$buscar_idUsuario."'";
    }

    if ($_SESSION["id_zona"]!="" && $_SESSION["id_zona"]!=0) {
        $sqlFiltro .= " AND C.idSec = '".$_SESSION["id_zona"]."'";
        $_REQUEST["empresa_sitio_cor"] = $_SESSION["id_zona"];
        $buscar_zona = $_SESSION["id_zona"];
    }
    
    if(isset($_REQUEST["empresa_pd"]) && soloNumeros($_REQUEST["empresa_pd"]) != ""){
        $buscar_regional = soloNumeros($_REQUEST["empresa_pd"]);
        $sqlFiltro .= " AND RU.reub_reg_fk = '".$buscar_regional."'";
    }else if ($_SESSION["empresa_pd"]!="" && $_SESSION["empresa_pd"]!=0) {
        $buscar_regional = soloNumeros($_SESSION["empresa_pd"]);
        $sqlFiltro .= " AND RU.reub_reg_fk = '".$_SESSION["empresa_pd"]."'";
        $_REQUEST["empresa_pd"] = $_SESSION["empresa_pd"];
    }
    
    if(isset($_REQUEST["sitioReunion"]) && soloNumeros($_REQUEST["sitioReunion"]) != ""){
        $buscar_prision = soloNumeros($_REQUEST["sitioReunion"]);
        $sqlFiltro .= " AND sat_reportes.sitioReunion = ".$buscar_prision."";
    }
    if(isset($_REQUEST["empresa_sitio_cor"]) && soloNumeros($_REQUEST["empresa_sitio_cor"]) != ""){
        $buscar_zona = soloNumeros($_REQUEST["empresa_sitio_cor"]);
        $sqlFiltro .= " AND C.idSec = '".$buscar_zona."'";
    }
    if(isset($_REQUEST["rep_ndis"]) && soloNumeros($_REQUEST["rep_ndis"]) != ""){
        $buscar_diplo = soloNumeros($_REQUEST["rep_ndis"]);
        $sqlFiltro .= " AND sat_reportes.rep_ndis = '".$buscar_diplo."'";
    }
    //
    if(isset($_REQUEST["rep_inex"]) && eliminarInvalidos($_REQUEST["rep_inex"]) != ""){
        $tipo = eliminarInvalidos($_REQUEST["rep_inex"]);
        if ($tipo == 2) {
            $sqlFiltro .= " AND sat_reportes.sitioReunion = 0 ";
        }else{
            $sqlFiltro .= " AND sat_reportes.sitioReunion <> 0 ";
        }    
    }else{
        $_REQUEST["rep_inex"] = "";
    }
    if(isset($_REQUEST["fechaInicial"]) && eliminarInvalidos($_REQUEST["fechaInicial"]) != ""){
        $fechaInicial = eliminarInvalidos($_REQUEST["fechaInicial"]);
        $sqlFiltro .= " AND sat_reportes.fechaReporte >= '".$fechaInicial."'";
    }
    //
    if(isset($_REQUEST["fechaFinal"]) && eliminarInvalidos($_REQUEST["fechaFinal"]) != ""){
        $fechaFinal = eliminarInvalidos($_REQUEST["fechaFinal"]);
        $sqlFiltro .= " AND sat_reportes.fechaReporte <= '".$fechaFinal."'";
    }    
    
    //    
    $sql .= $sqlFiltro." ORDER BY sat_reportes.id DESC";
    //
    $PSN1->query($sql);
    //echo $sql;
    if($PSN1->num_rows() > 0)
    {
        if($PSN1->next_record())
        {
            $total_registros = $PSN1->f('conteo');
        }
    }
    $total_paginas = ceil($total_registros / $registros); 

    $sql = "SELECT C.descripcion AS regional, CD.descripcion AS diplomado, RU.*,sat_reportes.*, U.nombre as nombreUsuario, sat_grupos.nombre as nombreGrupo, tbl_adjuntos.adj_url FROM sat_reportes ";
    $sql .= " LEFT JOIN usuario AS U ON U.id = sat_reportes.idUsuario 
LEFT JOIN sat_grupos ON sat_grupos.id = sat_reportes.idGrupoMadre 
LEFT JOIN tbl_adjuntos ON sat_reportes.id = tbl_adjuntos.adj_rep_fk 
LEFT JOIN tbl_regional_ubicacion AS RU ON RU.reub_id = sat_reportes.sitioReunion
LEFT JOIN categorias AS C ON C.id = RU.reub_reg_fk 
LEFT JOIN usuario_empresa AS UE ON UE.idUsuario = sat_reportes.idUsuario
LEFT JOIN categorias AS CA ON CA.id = C.idSec 
LEFT JOIN categorias AS CD ON CD.id = sat_reportes.rep_ndis ";
    //
    $sql.=" WHERE 1 ".$sqlFiltro." AND sat_reportes.rep_tip = 317 GROUP BY sat_reportes.id ORDER BY C.descripcion, U.nombre ASC";
    $sql.= " LIMIT ".$inicio.", ".$registros;
    //
    
    $PSN1->query($sql);
    //echo $sql;
    //$total_registros=$PSN1->num_rows();
    //$total_paginas = ceil($total_registros / $registros);

?><div class="container">

<form name="form" id="form" method="get" class="form-horizontal">
    <input type="hidden" name="doc" value="consultar-sub-programa-instituto-biblico" />
    <div>
        <h3 class="alert alert-info text-center">CONSULTA DE INSTITUTO BIBLICO</h3>
    </div>
    <div class="cont-tit">
        <div class="hr"><hr></div>
        <div class="tit-cen">
            <h3>FILTRO DE BUSQUEDA</h3>
            <h5>de REPORTES</h5>
        </div>
        <div class="hr"><hr></div>
    </div>
    <div class="form-group">
        <div class="col-sm-2">
            <strong>Zona:</strong>
            <select name="empresa_sitio_cor" id="zona" class="form-control" onchange="this.form.submit()">
                <option value="" <?php if($empresa_pd == ""){?>
                            selected="selected" <?php
                        } ?>>Todas la zonas</option>
                <?php
                /*
                *   TRAEMOS LOS TIPOS DE CLIENTE/EMPRESA (15)
                */
                $sql = "SELECT * ";
                $sql.=" FROM categorias ";
                $sql.=" WHERE idSec = 85 ";
                if ($_SESSION["id_zona"]!="" && $_SESSION["id_zona"]!=0) {
                    $sql.=" AND id = ".$_SESSION["id_zona"];
                }
                $sql .= " ORDER BY descripcion ASC ";

                $PSN3->query($sql);
                $numero=$PSN3->num_rows();
                if($numero > 0){
                    while($PSN3->next_record()){
                        ?><option value="<?=$PSN3->f('id'); ?>" <?php
                        if($buscar_zona == $PSN3->f('id')){?>
                            selected="selected" <?php
                        }
                        ?>><?=$PSN3->f('descripcion'); ?></option><?php
                    }
                }?>
            </select>
        </div>
        <div class="col-sm-2">
            <strong>Regional:</strong>
            <select  name="empresa_pd" id="regional" class="form-control" onchange="this.form.submit()">
                <?php echo($zona == "" && $_SESSION["perfil"]!=163 && $_SESSION["perfil"]!=162 )?'<option value="" selected >Todas la regionales</option>':"";
                $sql = "SELECT C.id, C.descripcion AS regional, CA.descripcion AS zona FROM categorias AS C";
                $sql.=" LEFT JOIN categorias AS CA ON CA.id = C.idSec
                WHERE CA.idSec = 85 ";
                if (!empty($buscar_zona)) {
                    $sql.=" AND CA.id = ".$buscar_zona;
                }
                if ($_SESSION["perfil"]!=167) {
                    if ($_SESSION["empresa_pd"]!="" && $_SESSION["empresa_pd"]!=0) {
                        $sql.=" AND C.id = ".$_SESSION["empresa_pd"];
                    }
                }
                
                $PSN2->query($sql); 
                //echo $sql;
                $numero=$PSN2->num_rows();
                if($numero > 0){
                    while($PSN2->next_record()){?>
                        <option value="<?=$PSN2->f('id'); ?>" <?php
                        if($buscar_regional == $PSN2->f('id')){
                            ?>selected="selected"<?php
                        }
                        ?> ><?=$PSN2->f('regional'); ?></option><?php
                    }
                }
                ?>
            </select>                    
        </div>
        <div class="col-sm-2">
            <strong>Miembro de la regional:</strong>
            <select name="idUsuario" onchange="this.form.submit()" class="form-control">
            <?php
            if($_SESSION["perfil"] != 163){
                ?><option value="">Ver todos</option><?php
            }
    
            /*
            *   TRAEMOS LOS USUARIOS
            */
            $sql = "SELECT * ";
            $sql.=" FROM usuario AS U
            LEFT JOIN usuario_empresa AS UE ON UE.idUsuario = U.id";
            $sql.=" WHERE U.tipo IN (162, 163, 167) ";
            if($_SESSION["perfil"] == 163){
                $sql.=" AND U.id = '".$_SESSION["id"]."'";
            }
            if (!empty($buscar_regional)) {
                $sql.=" AND UE.empresa_pd = ".$buscar_regional." ";
            }
            $sql.=" ORDER BY U.nombre asc";

            $PSN2->query($sql);
            $numero_coo=$PSN2->num_rows();
            if($numero_coo > 0)
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
            <div class="col-sm-3">
                <strong>Prisión:</strong>
                <select name="sitioReunion" id="rep_carcel" class="form-control" onchange="this.form.submit()">
                    <?php
                    /*
                    *   TRAEMOS LOS TIPOS DE SERVICIOS QUE PRESTA (25)
                    */
                    if ($_SESSION['empresa_pd'] != "") {
                        echo '<option value="">Sin especificar</option>';
                        $sql = "SELECT * ";
                        $sql.=" FROM tbl_regional_ubicacion ";
                        if(!empty($buscar_regional)){
                            $sql.=" WHERE reub_reg_fk = ".$buscar_regional;
                        }
                        $sql.=" ORDER BY reub_reg_fk asc";

                        $PSN2->query($sql);
                        $numero_pri=$PSN2->num_rows();
                        if($numero_pri > 0){
                            while($PSN2->next_record()){
                                ?><option value="<?=$PSN2->f('reub_id'); ?>" <?php
                                if($buscar_prision == $PSN2->f('reub_id'))
                                {
                                    ?>selected="selected"<?php
                                }
                                ?>><?=$PSN2->f('reub_nom'); ?></option><?php
                            }
                        }
                    }else{
                        echo '<option value="">Sin regional asignada</option>';
                    }
                    ?>
                </select>
            </div>
            <div class="col-sm-3">
                <strong>Diplomado:</strong>
                <select name="rep_ndis" class="form-control" onchange="this.form.submit()">
                    <option value="">Seleccione el diplomado</option>
                    <?php
                        $sql = "SELECT * ";
                        $sql.=" FROM categorias AS C";
                        $sql.=" WHERE C.idSec = 78 ";
                        $PSN2->query($sql);
                        $numero_cat=$PSN2->num_rows();
                        if($numero_cat > 0){
                            while($PSN2->next_record()){
                                ?><option value="<?=$PSN2->f('id'); ?>" <?php echo($PSN2->f('id')==$buscar_diplo)?"selected":""; ?> >
                                    <?=$PSN2->f('descripcion'); ?></option>
                            <?php }
                        }
                        ?>
                    
                </select>
            </div>
            <div class="col-sm-2">
                <strong>Fecha Inicial:</strong>
                <input type="date" name="fechaInicial" id="fechaInicial" value="<?=$fechaInicial; ?>" class="form-control" />
            </div>
            <div class="col-sm-2">
                <strong>Fecha Final:</strong>
                <input type="date" name="fechaFinal" id="fechaFinal" value="<?=$fechaFinal; ?>" class="form-control" />
            </div>
            <div class="col-sm-1" >
                <br>
                <input type="submit" value="Filtrar" class="btn btn-success" />
            </div>
        </div>
    </form>
</div>      
<style>
.table tbody tr:hover td, .table tbody tr:hover th {
    background-color: #E0EEEE;
    cursor:pointer;
    color:#000;
}

.table thead tr{
    background-color: #C7C7C7;
}

.table thead th{
    vertical-align: middle;text-align: center;
}

.table a{
    color:#000;
}

</style>

<div class="container">
    <div class="cont-tit">
        <div class="hr"><hr></div>
        <div class="tit-cen">
            <h3 class="text-center">RESULTADOS DE BUSQUEDA</h3>
            <h5><?php echo $total_registros; ?> Registros encontrados</h5>
        </div>
        <div class="hr"><hr></div>
    </div>
    <div style="overflow-x: auto;">
        <table border="0" cellspacing="0" cellpadding="2"  align="center" class="table table-striped" style="font-size:12px">
            <thead>
                <tr> 
                    <th>Regional</th>
                    <th>Prisión</th>
                    <th>Usuario</th>
                    <th>Diplomado</th>
                    <th>Prisioneros invitados</th>
                    <th>Prisioneros inscritos</th>
                    <th>Prisioneros que iniciaron</th>
                    <th>Total de graduados</th>
                    <th>Doc. Testimonio</th>
                    <th>Foto</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if($total_registros > 0)
                {
                    $contador = 0;
                    while($PSN1->next_record())
                    {
                        //Solo si no se ha modificado ya el formulario.
                        $id = $PSN1->f('id');
                        $regional = $PSN1->f("regional");                            
                        $prision = $PSN1->f("reub_nom");
                        $plantador = $PSN1->f("plantador");
                        $fechaReporte = $PSN1->f("fechaReporte");
                        $fechaInicio = $PSN1->f("fechaInicio");        
                        $sitioReunion = $PSN1->f("sitioReunion");
                        $grupoMadre_txt = $PSN1->f("grupoMadre_txt");
                        $idGrupoMadre = $PSN1->f("idGrupoMadre");
                        $pabellon = $PSN1->f("pabellon");
                        $ciudad = $PSN1->f("ciudad");
                        $direccion = $PSN1->f("direccion");
                        $generacionNumero = intval($PSN1->f("generacionNumero"));
                        
                        $nombreUsuario = $PSN1->f("nombreUsuario");
                        $nombreGrupo = $PSN1->f("nombreGrupo");
                        
                        
                        
                        $mapeo_comprometido = $PSN1->f("mapeo_comprometido");
                        $nombreGrupo_txt = $PSN1->f("nombreGrupo_txt");
                        $mapeo_fecha = $PSN1->f("mapeo_fecha");  

                        $mapeo_oracion = $PSN1->f("mapeo_oracion");  
                        $mapeo_companerismo = $PSN1->f("mapeo_companerismo");  
                        $mapeo_adoracion = $PSN1->f("mapeo_adoracion");  
                        $mapeo_biblia = $PSN1->f("mapeo_biblia");  
                        $mapeo_evangelizar = $PSN1->f("mapeo_evangelizar");  
                        $mapeo_cena = $PSN1->f("mapeo_cena");  
                        $mapeo_dar = $PSN1->f("mapeo_dar");  
                        $mapeo_bautizar = $PSN1->f("mapeo_bautizar");  
                        $mapeo_trabajadores = $PSN1->f("mapeo_trabajadores");  
                        

                        $ext1 = $PSN1->f("ext1");
                        $ext2 = $PSN1->f("ext2");
                        $ext3 = $PSN1->f("ext3");
                        

                        $asistencia_hom = $PSN1->f("asistencia_hom");
                        $asistencia_muj = $PSN1->f("asistencia_muj");
                        $asistencia_jov = $PSN1->f("asistencia_jov");
                        $asistencia_nin = $PSN1->f("asistencia_nin");

                        $bautizados = $PSN1->f("bautizados");

                        //Calculados:
                        $asistencia_total  = $PSN1->f("asistencia_total");
                        $rep_ndis  = $PSN1->f("rep_ndis");
                        $desiciones  = $PSN1->f("desiciones");
                        $comentario  = $PSN1->f("comentario");
                        $url_baut  = $PSN1->f("adj_url");
                        $diplomado  = $PSN1->f("diplomado");
                        
                        $iglesias_reconocidas = $PSN1->f("iglesias_reconocidas");  
                        
                        ?><tr class='clickable-row' data-href='index.php?doc=gestionar-sub-programa-instituto-biblico&id=<?=$id; ?>' >
                            <!--<td><a href="index.php?doc=gestionar-sub-programa-instituto-biblico&id=<?=$id; ?>"><?=str_pad($id, 6, "0", STR_PAD_LEFT); ?></a></td>//-->
                            <td><?=$regional; ?></td>
                            <td><?= $prision; ?></td>
                            <td><?=$nombreUsuario; ?></td>
                            <td><?=$diplomado; ?></td>
                            <td><?=$asistencia_total; ?></td>
                            <td><?=$asistencia_hom; ?></td>
                            <td><?=$asistencia_muj; ?></td>
                            <td><?=$asistencia_nin; ?></td>
                            
                            <td align="center"><?php
                            if($ext2 != "" || $ext2 != null){
                                ?><i class="fas fa-thumbs-up ico-lik"></i>
                                <!--<img src="images/png/thumb-up.png" width="20px" />--><?php
                            }else{
                                ?>
                                <i class="fas fa-thumbs-down ico-dli"></i>
                                <!--<img src="images/png/thumb-down.png" width="20px" />--><?php                            
                            }
                            ?></td> 
                            <td align="center"><?php
                            if($ext1 != "" || $ext1 != null){
                                ?><i class="fas fa-thumbs-up ico-lik"></i>
                                <!--<img src="images/png/thumb-up.png" width="20px" />--><?php
                            }else{
                                ?>
                                <i class="fas fa-thumbs-down ico-dli"></i>
                                <!--<img src="images/png/thumb-down.png" width="20px" />--><?php                            
                            }
                            ?></td>                           
                        </tr>
                        <?php
                        $contador++;
                    }
                }
                ?>
            </tbody>
        </table>
    </div>       
</div>
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

<script language="javascript">
function init(){
}
window.onload = function(){
    init();
}
</script>


<script>
jQuery(document).ready(function($) {
    $(".clickable-row").click(function() {
        window.location = $(this).data("href");
    });
});
</script>