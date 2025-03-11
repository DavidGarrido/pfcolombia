<?php
/*
*	CONSOLIDADO CON SUMATORIA DE CAMPOS
*/
// Objeto de Base de Datos
$PSN1 = new DBbase_Sql;
$PSN2 = new DBbase_Sql;


if(!isset($_REQUEST["fechaInicial"]) || eliminarInvalidos($_REQUEST["fechaInicial"]) == ""){
    $_REQUEST["fechaInicial"] = date("Y-m-01", strtotime("-2 months"));
}
if(!isset($_REQUEST["fechaFinal"]) || eliminarInvalidos($_REQUEST["fechaFinal"]) == ""){
    $_REQUEST["fechaFinal"] = date("Y-m-d");
}


/*
*   GENERAR EXCEL
*/
if(isset($_REQUEST["excelXML"])){

    //
    if($_SESSION["perfil"] == 163){
        $_REQUEST["idUsuario"] = $_SESSION["id"];
    }
    //
    if(isset($_REQUEST["idUsuario"]) && soloNumeros($_REQUEST["idUsuario"]) != ""){
        $buscar_idUsuario = soloNumeros($_REQUEST["idUsuario"]);
        $sqlFiltro .= " AND sat_reportes.idUsuario = '".$buscar_idUsuario."'";
    }
    //
    if(isset($_REQUEST["idGrupoMadre"]) && soloNumeros($_REQUEST["idGrupoMadre"]) != ""){
        $buscar_idGrupoMadre = soloNumeros($_REQUEST["idGrupoMadre"]);
        $sqlFiltro .= " AND sat_reportes.idGrupoMadre = '".$buscar_idGrupoMadre."'";
    }
    //
    if(isset($_REQUEST["nombre"]) && eliminarInvalidos($_REQUEST["nombre"]) != ""){
        $buscar_nombre = eliminarInvalidos($_REQUEST["nombre"]);
        $sqlFiltro .= " AND sat_reportes.plantador LIKE '%".$buscar_nombre."%'";
    }
    //
    if(isset($_REQUEST["fechaInicial"]) && eliminarInvalidos($_REQUEST["fechaInicial"]) != ""){
        $fechaInicial = eliminarInvalidos($_REQUEST["fechaInicial"]);
        $sqlFiltro .= " AND sat_reportes.fechaReporte >= '".$fechaInicial."'";
    }
    //
    if(isset($_REQUEST["fechaFinal"]) && eliminarInvalidos($_REQUEST["fechaFinal"]) != ""){
        $fechaFinal = eliminarInvalidos($_REQUEST["fechaFinal"]);
        $sqlFiltro .= " AND sat_reportes.fechaReporte <= '".$fechaFinal."'";
    }
    
    $sql = "SELECT
                sat_reportes.idUsuario,
                SUM(CASE WHEN sat_reportes.generacionNumero > 0 THEN 1 ELSE 0 END) AS gruposConteo,
                
                SUM(sat_reportes.asistencia_total) as asistencia_total,
                SUM(sat_reportes.asistencia_hom) as asistencia_hom,
                SUM(sat_reportes.asistencia_muj) as asistencia_muj,
                SUM(sat_reportes.asistencia_jov) as asistencia_jov,
                SUM(sat_reportes.asistencia_nin) as asistencia_nin,
                
                SUM(sat_reportes.bautizados) as bautizados,
                SUM(sat_reportes.bautizadosPeriodo) as bautizadosPeriodo,                
                SUM(sat_reportes.discipulado) as discipulado,
                SUM(sat_reportes.desiciones) as desiciones,
                SUM(sat_reportes.preparandose) as preparandose,
                SUM(sat_reportes.iglesias_reconocidas) as iglesias_reconocidas,
                SUM(sat_reportes.bautizados) as bautizados,
                usuario.nombre as nombreUsuario,
                usuario_empresa.empresa_sitio,
                usuario_empresa.empresa_rm,
                usuario_empresa.empresa_proceso                
                ";
    $sql.=" FROM sat_reportes ";
    $sql .= " LEFT JOIN usuario ON usuario.id = sat_reportes.idUsuario";
    $sql .= " LEFT JOIN usuario_empresa ON usuario_empresa.idUsuario = sat_reportes.idUsuario";
    //
    $sql.=" WHERE 1 ".$sqlFiltro." GROUP BY sat_reportes.idUsuario ORDER BY usuario.nombre ASC";
    
    //
    $PSN1->query($sql);
    $numero=$PSN1->num_rows();
    
    
    
    $sql = "SELECT usuario.*, usuario_empresa.* FROM usuario LEFT JOIN usuario_empresa ON usuario_empresa.idUsuario = usuario.id WHERE usuario.id = '".$_SESSION["id"]."'";
    $PSN2->query($sql);
    if($PSN2->num_rows() > 0)
    {
        if($PSN2->next_record())
        {
            $empresa_pais = $PSN2->f('empresa_pais');
            $empresa_sitio_cor = $PSN2->f('empresa_sitio_cor');
            $empresa_socio = $PSN2->f('empresa_socio');   
            $empresa_rm = $PSN2->f('empresa_rm');
        }
    }
    ?>
    <Worksheet ss:Name="Coor">
    <Table ss:ExpandedColumnCount="20">
        <Row>
            <Cell ss:StyleID="verdosoBold" ss:MergeAcross="3"><Data ss:Type="String">Informe del coordinador</Data></Cell>
            <Cell ss:StyleID="verdoso"><Data ss:Type="String"> </Data></Cell>
            <Cell><Data ss:Type="String"><?=$empresa_pais; ?></Data></Cell>
            <Cell ss:MergeAcross="9" ss:StyleID="verdoso"><Data ss:Type="String"> </Data></Cell>
        </Row>
            
        <Row>
            <Cell><Data ss:Type="String"><?=$fechaInicial; ?></Data></Cell>
            <Cell ss:StyleID="verdoso"><Data ss:Type="String">-</Data></Cell>
            <Cell><Data ss:Type="String"><?=$fechaFinal; ?></Data></Cell>
            <Cell ss:StyleID="verdoso"><Data ss:Type="String"> </Data></Cell>
            <Cell ss:StyleID="verdoso"><Data ss:Type="String"> </Data></Cell>
            <Cell ss:StyleID="verdoso"><Data ss:Type="String">nombre del pais</Data></Cell>
            <Cell ss:MergeAcross="9" ss:StyleID="verdoso"><Data ss:Type="String"> </Data></Cell>
        </Row>
        
        <Row>
            <Cell ><Data ss:Type="String"><?=date("Y-m-d"); ?></Data></Cell>
            <Cell ss:StyleID="verdoso"><Data ss:Type="String">?</Data></Cell>
            <Cell ss:StyleID="verdoso"><Data ss:Type="String"><?=date("Y"); ?></Data></Cell>
            <Cell ss:StyleID="verdoso"><Data ss:Type="String"> </Data></Cell>
            <Cell ss:StyleID="verdoso"><Data ss:Type="String"> </Data></Cell>
            <Cell ><Data ss:Type="String"><?=$empresa_socio; ?></Data></Cell>
            <Cell ss:MergeAcross="9" ss:StyleID="verdoso"><Data ss:Type="String"> </Data></Cell>
        </Row>
        
        <Row>
            <Cell ss:StyleID="verdoso"><Data ss:Type="String"><?="fecha - informe"; ?></Data></Cell>
            <Cell ss:StyleID="verdoso"><Data ss:Type="String"><?="trmstr."; ?></Data></Cell>
            <Cell ss:StyleID="verdoso"><Data ss:Type="String"><?="año"; ?></Data></Cell>
            <Cell ss:StyleID="verdoso"><Data ss:Type="String"> </Data></Cell>
            <Cell ss:StyleID="verdoso"><Data ss:Type="String"> </Data></Cell>
            <Cell ss:StyleID="verdoso"><Data ss:Type="String"><?="nombre del socio"; ?></Data></Cell>
            <Cell ss:MergeAcross="9" ss:StyleID="verdoso"><Data ss:Type="String"> </Data></Cell>
        </Row>
                    
        <Row>
            <Cell ss:MergeAcross="2"><Data ss:Type="String"><?=$_SESSION["nombre"]; ?></Data></Cell>
            <Cell ss:StyleID="verdoso" ss:Formula='=COUNTIF(R[+4]C:R[+6]C,&quot;SI&quot;)'><Data ss:Type="Number">0</Data></Cell>
            <Cell ss:StyleID="verdoso"><Data ss:Type="String"> </Data></Cell>
            <Cell><Data ss:Type="String"><?=$empresa_sitio_cor; ?></Data></Cell>
            <Cell ss:MergeAcross="9" ss:StyleID="verdoso"><Data ss:Type="String"> </Data></Cell>
        </Row>
                    
        <Row>
            <Cell ss:MergeAcross="2" ss:StyleID="verdoso"><Data ss:Type="String"><?="nombre del coordinador"; ?></Data></Cell>
            <Cell ss:StyleID="verdoso"><Data ss:Type="String">~rm</Data></Cell>
            <Cell ss:StyleID="verdoso"><Data ss:Type="String"> </Data></Cell>
            <Cell ss:StyleID="verdoso"><Data ss:Type="String"><?="sitio del coordinador"; ?></Data></Cell>
            <Cell ss:MergeAcross="9" ss:StyleID="verdoso"><Data ss:Type="String"> </Data></Cell>
        </Row>
    
        

        
                            
        <Row>
            <Cell ss:MergeAcross="5" ss:StyleID="verdoso"><Data ss:Type="String"> </Data></Cell>
            <Cell ss:StyleID="verdosoBold"><Data ss:Type="String"><?="Grupos"; ?></Data></Cell>
            <Cell ss:StyleID="verdosoBold"><Data ss:Type="String"><?="Grupos Nuevos"; ?></Data></Cell>
            <Cell ss:StyleID="verdosoBold"><Data ss:Type="String"><?="Bautizados"; ?></Data></Cell>
                <Cell ss:StyleID="verdosoBold"><Data ss:Type="String"><?="Asistencia"; ?></Data></Cell>
            <Cell ss:StyleID="verdosoBold"><Data ss:Type="String"><?="Decisiones"; ?></Data></Cell>
            <Cell ss:StyleID="verdosoBold"><Data ss:Type="String"><?="Preparandose"; ?></Data></Cell>
                <Cell ss:StyleID="verdosoBold"><Data ss:Type="String"><?="Bautizados este periodo"; ?></Data></Cell>
            <Cell ss:StyleID="verdosoBold"><Data ss:Type="String"><?="En Discipulado"; ?></Data></Cell>
                <Cell ss:StyleID="verdosoBold"><Data ss:Type="String"><?="Lideres Capacitandose"; ?></Data></Cell>
            <Cell ss:StyleID="verdosoBold"><Data ss:Type="String"><?="Iglesias Reconocidas"; ?></Data></Cell>
        </Row>
            
                            
        <Row>
            <Cell ss:MergeAcross="2" ss:StyleID="verdoso"><Data ss:Type="String"><?="Nom. del Facilitador"; ?></Data></Cell>
            <Cell ss:StyleID="verdosoBold"><Data ss:Type="String"><?="Rm."; ?></Data></Cell>
            <Cell ss:StyleID="verdosoBold"><Data ss:Type="String"><?="Proceso"; ?></Data></Cell>
            <Cell ss:StyleID="verdosoBold"><Data ss:Type="String"><?="Sitio del Facilitador"; ?></Data></Cell>

            <Cell ss:StyleID="verdosoBold" ss:Formula='=SUM(R[+1]C:R[+<?=intval($numero); ?>]C)'><Data ss:Type="Number">0</Data></Cell>
            <Cell ss:StyleID="verdosoBold" ss:Formula='=SUM(R[+1]C:R[+<?=intval($numero); ?>]C)'><Data ss:Type="Number">0</Data></Cell>
            <Cell ss:StyleID="verdosoBold" ss:Formula='=SUM(R[+1]C:R[+<?=intval($numero); ?>]C)'><Data ss:Type="Number">0</Data></Cell>
                <Cell ss:StyleID="verdosoBold" ss:Formula='=SUM(R[+1]C:R[+<?=intval($numero); ?>]C)'><Data ss:Type="Number">0</Data></Cell>
            <Cell ss:StyleID="verdosoBold" ss:Formula='=SUM(R[+1]C:R[+<?=intval($numero); ?>]C)'><Data ss:Type="Number">0</Data></Cell>
            <Cell ss:StyleID="verdosoBold" ss:Formula='=SUM(R[+1]C:R[+<?=intval($numero); ?>]C)'><Data ss:Type="Number">0</Data></Cell>
            <Cell ss:StyleID="verdosoBold" ss:Formula='=SUM(R[+1]C:R[+<?=intval($numero); ?>]C)'><Data ss:Type="Number">0</Data></Cell>
                <Cell ss:StyleID="verdosoBold" ss:Formula='=SUM(R[+1]C:R[+<?=intval($numero); ?>]C)'><Data ss:Type="Number">0</Data></Cell>
            <Cell ss:StyleID="verdosoBold" ss:Formula='=SUM(R[+1]C:R[+<?=intval($numero); ?>]C)'><Data ss:Type="Number">0</Data></Cell>
                <Cell ss:StyleID="verdosoBold" ss:Formula='=SUM(R[+1]C:R[+<?=intval($numero); ?>]C)'><Data ss:Type="Number">0</Data></Cell>                
        </Row>
            

        <?php
        if($numero > 0)
        {
            $contador = 1;
            while($PSN1->next_record())
            {
                //Solo si no se ha modificado ya el formulario.
                $idUsuario = $PSN1->f('idUsuario');
                $plantador = $PSN1->f("plantador");
                $fechaReporte = $PSN1->f("fechaReporte");
                $fechaInicio = $PSN1->f("fechaInicio");        
                $sitioReunion = $PSN1->f("sitioReunion");
                $grupoMadre_txt = $PSN1->f("grupoMadre_txt");
                $idGrupoMadre = $PSN1->f("idGrupoMadre");
                $generacionNumero = intval($PSN1->f("generacionNumero"));

                $gruposConteo = $PSN1->f("gruposConteo");

                $sql = "SELECT COUNT(DISTINCT sat_reportes.idGrupoMadre) as conteo ";
                $sql.=" FROM sat_reportes ";
                //
                //
                $sqlFiltro = "";
                if(isset($_REQUEST["fechaInicial"]) && eliminarInvalidos($_REQUEST["fechaInicial"]) != ""){
                    $sqlFiltro .= " AND sat_reportes.fechaInicio >= '".$fechaInicial."'";
                }
                //
                if(isset($_REQUEST["fechaFinal"]) && eliminarInvalidos($_REQUEST["fechaFinal"]) != ""){
                    $sqlFiltro .= " AND sat_reportes.fechaInicio <= '".$fechaFinal."'";
                }

                $sql.=" WHERE idUsuario = '".$idUsuario."' ".$sqlFiltro;
                $PSN2->query($sql);
                if($PSN2->num_rows() > 0)
                {
                    if($PSN2->next_record())
                    {
                        $gruposNuevos = $PSN2->f('conteo');
                    }
                }

                $nombreUsuario = $PSN1->f("nombreUsuario");
                    $empresa_sitio = $PSN1->f("empresa_sitio");
                    $empresa_rm = $PSN1->f("empresa_rm");
                    $empresa_proceso = $PSN1->f("empresa_proceso");

                $asistencia_hom = $PSN1->f("asistencia_hom");
                $asistencia_muj = $PSN1->f("asistencia_muj");
                $asistencia_jov = $PSN1->f("asistencia_jov");
                $asistencia_nin = $PSN1->f("asistencia_nin");

                $bautizados = $PSN1->f("bautizados");
                $bautizadosPeriodo = $PSN1->f("bautizadosPeriodo");

                //Calculados:
                $asistencia_total  = $PSN1->f("asistencia_total");
                $discipulado  = $PSN1->f("discipulado");
                $desiciones  = $PSN1->f("desiciones");
                $preparandose  = $PSN1->f("preparandose");
                $iglesias_reconocidas = $PSN1->f("iglesias_reconocidas");  

                $lideresCapacitandose = 0;                        

                $sql = "SELECT SUM(asistencia_total) as total, COUNT(sat_reportes.id) as conteo ";
                $sql.=" FROM sat_reportes ";
                $sql .= " LEFT JOIN usuario ON usuario.id = sat_reportes.idUsuario";
                $sql .= " LEFT JOIN usuario_empresa ON usuario_empresa.idUsuario = sat_reportes.idUsuario";
                //
                $sql.=" WHERE 1 ".$sqlFiltro." AND  sat_reportes.idUsuario = '".$idUsuario."' AND sat_reportes.generacionNumero = 0 GROUP BY sat_reportes.idUsuario";
                $PSN2->query($sql);
                if($PSN2->num_rows() > 0)
                {
                    if($PSN2->next_record())
                    {
                        $lideresCapacitandose = ($PSN2->f('total')-$PSN2->f('conteo'));
                    }
                }
                //
                ?>
                <Row>
                    <Cell ss:MergeAcross="2"><Data ss:Type="String"><?=$nombreUsuario; ?></Data></Cell>
                        <Cell><Data ss:Type="String"><?=$empresa_rm; ?></Data></Cell>
                        <Cell><Data ss:Type="String"><?=$empresa_proceso; ?></Data></Cell>
                        <Cell><Data ss:Type="String"><?=$empresa_sitio; ?></Data></Cell>

                    <Cell><Data ss:Type="Number"><?=intval($gruposConteo); ?></Data></Cell>
                    <Cell><Data ss:Type="Number"><?=intval($gruposNuevos); ?></Data></Cell>
                    <Cell><Data ss:Type="Number"><?=intval($bautizados); ?></Data></Cell>
                        <Cell><Data ss:Type="Number"><?=intval($asistencia_total); ?></Data></Cell>
                    <Cell><Data ss:Type="Number"><?=intval($desiciones); ?></Data></Cell>
                    <Cell><Data ss:Type="Number"><?=intval($preparandose); ?></Data></Cell>
                        <Cell><Data ss:Type="Number"><?=intval($bautizadosPeriodo); ?></Data></Cell>
                    <Cell><Data ss:Type="Number"><?=intval($discipulado); ?></Data></Cell>
                        <Cell><Data ss:Type="Number"><?=intval($lideresCapacitandose); ?></Data></Cell>
                    <Cell><Data ss:Type="Number"><?=intval($iglesias_reconocidas); ?></Data></Cell>
                </Row>
                <?php
                $contador++;
            }
        }
        ?>
        </Table>
    </Worksheet>
    <?php
    /*********************************************************************************************************
    *********************************************************************************************************    
                DETALLE DETALLE DETALLE DETALLE DETALLE DETALLE DETALLE DETALLE DETALLE DETALLE 
                DETALLE DETALLE DETALLE DETALLE DETALLE DETALLE DETALLE DETALLE DETALLE DETALLE 
                DETALLE DETALLE DETALLE DETALLE DETALLE DETALLE DETALLE DETALLE DETALLE DETALLE 
                DETALLE DETALLE DETALLE DETALLE DETALLE DETALLE DETALLE DETALLE DETALLE DETALLE 
    *********************************************************************************************************    *********************************************************************************************************/
    $sql = "SELECT
                    sat_reportes.*,
                    
                    usuario.id as idUsuario,
                    usuario.nombre as nombreUsuario,
                    usuario_empresa.empresa_pais,
                    usuario_empresa.empresa_sitio,
                    usuario_empresa.empresa_sitio_cor,
                    usuario_empresa.empresa_socio,
                    usuario_empresa.empresa_rm,
                    usuario_empresa.empresa_proceso                
                    ";
        $sql.=" FROM sat_reportes ";
        $sql .= " LEFT JOIN usuario ON usuario.id = sat_reportes.idUsuario";
        $sql .= " LEFT JOIN usuario_empresa ON usuario_empresa.idUsuario = sat_reportes.idUsuario";
        //
        $sql.=" WHERE 1 ".$sqlFiltro." ORDER BY usuario.nombre ASC, usuario.id ASC, sat_reportes.plantador ASC, sat_reportes.grupoMadre_txt ASC";

        //
        $PSN1->query($sql);
        $numero=$PSN1->num_rows();    
        if($numero > 0)
        {
            $openTMP = 0;
            $contador = 1;
            $idUsuario = -99;

            while($PSN1->next_record())
            {
                $empresa_pais = $PSN1->f('empresa_pais');
                $empresa_sitio = $PSN1->f('empresa_sitio');
                $empresa_socio = $PSN1->f('empresa_socio');            
                $empresa_rm = $PSN1->f('empresa_rm');
                //
                //
                if($PSN1->f('idUsuario') != $idUsuario){
                    //
                    if($openTMP != 0){
                        ?></Table>
                        </Worksheet>
                        <?php                    
                    }
                    $openTMP = 1;
                    $idUsuario = $PSN1->f('idUsuario');

                    ?>
                    <Worksheet ss:Name="<?=$contador; ?>">
                        <Table ss:ExpandedColumnCount="20">
                            <Row>
                                <Cell ss:StyleID="verdosoBold" ss:MergeAcross="1"><Data ss:Type="String">Informe del Capacitador</Data></Cell>
                                <Cell><Data ss:Type="String"><?=$empresa_pais; ?></Data></Cell>
                                <Cell><Data ss:Type="String"><?=$empresa_sitio; ?></Data></Cell>
                                <Cell ss:MergeAcross="11" ss:StyleID="verdoso"><Data ss:Type="String"> </Data></Cell>
                            </Row>

                            <Row>
                                <Cell><Data ss:Type="String"><?=$fechaInicial; ?></Data></Cell>
                                <Cell><Data ss:Type="String"><?=$fechaFinal; ?></Data></Cell>
                                <Cell ss:StyleID="verdoso"><Data ss:Type="String">nombre del pais</Data></Cell>
                                <Cell ss:StyleID="verdoso"><Data ss:Type="String">Nombre del socio</Data></Cell>
                                <Cell ss:MergeAcross="11" ss:StyleID="verdoso"><Data ss:Type="String"> </Data></Cell>
                            </Row>

                            <Row>
                                <Cell><Data ss:Type="String"><?=$PSN1->f('nombreUsuario'); ?></Data></Cell>
                                <Cell><Data ss:Type="String"><?=$PSN1->f('empresa_proceso'); ?></Data></Cell>
                                <Cell><Data ss:Type="String"><?=$empresa_sitio; ?></Data></Cell>
                                <Cell><Data ss:Type="String"><?=$fechaFinal; ?></Data></Cell>
                                <Cell ss:MergeAcross="11" ss:StyleID="verdoso"><Data ss:Type="String"> </Data></Cell>
                            </Row>

                            <Row>
                                <Cell ss:StyleID="verdoso"><Data ss:Type="String"><?="nom. del capacitador"; ?></Data></Cell>
                                <Cell ss:StyleID="verdoso"><Data ss:Type="String">proceso</Data></Cell>
                                <Cell ss:StyleID="verdoso"><Data ss:Type="String"><?="sitio del coordinador"; ?></Data></Cell>
                                <Cell ss:StyleID="verdoso"><Data ss:Type="String"><?="fecha del informe"; ?></Data></Cell>
                                <Cell ss:MergeAcross="11" ss:StyleID="verdoso"><Data ss:Type="String"> </Data></Cell>
                            </Row>

                            <Row>
                                <Cell ss:StyleID="verdosoBold"><Data ss:Type="String"><?="Plantadores-Igles."; ?></Data></Cell>
                                <Cell ss:StyleID="verdosoBold"><Data ss:Type="String"><?="Fecha-inicio"; ?></Data></Cell>
                                <Cell ss:StyleID="verdosoBold"><Data ss:Type="String"><?="Sitio de la reunión"; ?></Data></Cell>
                                    <Cell ss:StyleID="verdosoBold"><Data ss:Type="String"><?="Grupo Madre"; ?></Data></Cell>
                                <Cell ss:StyleID="verdosoBold"><Data ss:Type="String"><?="Generación"; ?></Data></Cell>
                                <Cell ss:StyleID="verdosoBold"><Data ss:Type="String"><?="Miembros bautizados"; ?></Data></Cell>
                                <Cell ss:StyleID="verdosoBold"><Data ss:Type="String"><?="Asistencia"; ?></Data></Cell>
                                    <Cell ss:StyleID="verdosoBold"><Data ss:Type="String"><?="Hombres"; ?></Data></Cell>
                                    <Cell ss:StyleID="verdosoBold"><Data ss:Type="String"><?="Mujeres"; ?></Data></Cell>
                                    <Cell ss:StyleID="verdosoBold"><Data ss:Type="String"><?="Jóvenes"; ?></Data></Cell>
                                    <Cell ss:StyleID="verdosoBold"><Data ss:Type="String"><?="Niños"; ?></Data></Cell>
                                <Cell ss:StyleID="verdosoBold"><Data ss:Type="String"><?="Decisiones para Cristo"; ?></Data></Cell>
                                <Cell ss:StyleID="verdosoBold"><Data ss:Type="String"><?="Prepárandose para Bautismo"; ?></Data></Cell>
                                <Cell ss:StyleID="verdosoBold"><Data ss:Type="String"><?="Bautizos este período"; ?></Data></Cell>
                                <Cell ss:StyleID="verdosoBold"><Data ss:Type="String"><?="En Discipulado"; ?></Data></Cell>
                                <Cell ss:StyleID="verdosoBold"><Data ss:Type="String"><?="Iglesias Reconocidas"; ?></Data></Cell>
                            </Row>                        
                            <?php
                    $contador++;
                }
                ?>
                <Row>
                    <Cell><Data ss:Type="String"><?=$PSN1->f('plantador'); ?></Data></Cell>
                        <Cell><Data ss:Type="String"><?=$PSN1->f('fechaInicio'); ?></Data></Cell>
                        <Cell><Data ss:Type="String"><?=$PSN1->f('sitioReunion'); ?></Data></Cell>
                        <Cell><Data ss:Type="String"><?=$PSN1->f('grupoMadre_txt'); ?></Data></Cell>

                    <Cell><Data ss:Type="Number"><?=intval($PSN1->f('generacion')); ?></Data></Cell>
                    <Cell><Data ss:Type="Number"><?=intval($PSN1->f('bautizados')); ?></Data></Cell>
                    <Cell><Data ss:Type="Number"><?=intval($PSN1->f('asistencia_total')); ?></Data></Cell>
                        <Cell><Data ss:Type="Number"><?=intval($PSN1->f('asistencia_hom')); ?></Data></Cell>
                        <Cell><Data ss:Type="Number"><?=intval($PSN1->f('asistencia_muj')); ?></Data></Cell>
                        <Cell><Data ss:Type="Number"><?=intval($PSN1->f('asistencia_jov')); ?></Data></Cell>
                        <Cell><Data ss:Type="Number"><?=intval($PSN1->f('asistencia_nin')); ?></Data></Cell>
                    <Cell><Data ss:Type="Number"><?=intval($PSN1->f('desiciones')); ?></Data></Cell>
                    <Cell><Data ss:Type="Number"><?=intval($PSN1->f('preparandose')); ?></Data></Cell>
                    <Cell><Data ss:Type="Number"><?=intval($PSN1->f('bautizadosPeriodo')); ?></Data></Cell>
                    <Cell><Data ss:Type="Number"><?=intval($PSN1->f('discipulado')); ?></Data></Cell>
                    <Cell><Data ss:Type="Number"><?=intval($PSN1->f('iglesias_reconocidas')); ?></Data></Cell>
                </Row>
                <?php
            }
            //
            if($openTMP == 1){
                ?></Table>
                </Worksheet>
                <?php                    
            }
                
        }   
    
    
    
}
else{
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
    *	TRAEMOS EL CONTEO ED LOS REGISTROS POR USUARIO QUE ES EL AGRUPADOR.
    */
    $sql = "SELECT count(DISTINCT sat_reportes.idUsuario) as conteo ";
    $sql .= " FROM sat_reportes ";
    $sql .= " LEFT JOIN usuario ON usuario.id = sat_reportes.idUsuario";
    $sql .= " WHERE 1 ";
    //
    if($_SESSION["perfil"] == 163){
        $_REQUEST["idUsuario"] = $_SESSION["id"];
    }
    //
    if(isset($_REQUEST["idUsuario"]) && soloNumeros($_REQUEST["idUsuario"]) != ""){
        $buscar_idUsuario = soloNumeros($_REQUEST["idUsuario"]);
        $sqlFiltro .= " AND sat_reportes.idUsuario = '".$buscar_idUsuario."'";
    }
    //
    if(isset($_REQUEST["idGrupoMadre"]) && soloNumeros($_REQUEST["idGrupoMadre"]) != ""){
        $buscar_idGrupoMadre = soloNumeros($_REQUEST["idGrupoMadre"]);
        $sqlFiltro .= " AND sat_reportes.idGrupoMadre = '".$buscar_idGrupoMadre."'";
    }
    //
    if(isset($_REQUEST["nombre"]) && eliminarInvalidos($_REQUEST["nombre"]) != ""){
        $buscar_nombre = eliminarInvalidos($_REQUEST["nombre"]);
        $sqlFiltro .= " AND sat_reportes.plantador LIKE '%".$buscar_nombre."%'";
    }
    //
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
    if($PSN1->num_rows() > 0)
    {
        if($PSN1->next_record())
        {
            $total_registros = $PSN1->f('conteo');
        }
    }
    $total_paginas = ceil($total_registros / $registros); 

    //GRupos nuevos es el conteo de grupos cuya generaci贸n sea mayor a 0.
    $sql = "SELECT
                sat_reportes.idUsuario,
                SUM(CASE WHEN sat_reportes.generacionNumero > 0 THEN 1 ELSE 0 END) AS gruposConteo,
                
                SUM(sat_reportes.asistencia_total) as asistencia_total,
                SUM(sat_reportes.asistencia_hom) as asistencia_hom,
                SUM(sat_reportes.asistencia_muj) as asistencia_muj,
                SUM(sat_reportes.asistencia_jov) as asistencia_jov,
                SUM(sat_reportes.asistencia_nin) as asistencia_nin,
                
                SUM(sat_reportes.bautizados) as bautizados,
                SUM(sat_reportes.bautizadosPeriodo) as bautizadosPeriodo,                
                SUM(sat_reportes.discipulado) as discipulado,
                SUM(sat_reportes.desiciones) as desiciones,
                SUM(sat_reportes.preparandose) as preparandose,
                SUM(sat_reportes.iglesias_reconocidas) as iglesias_reconocidas,
                SUM(sat_reportes.bautizados) as bautizados,
                usuario.nombre as nombreUsuario,
                usuario_empresa.empresa_sitio,
                usuario_empresa.empresa_rm,
                usuario_empresa.empresa_proceso                
                ";
    $sql.=" FROM sat_reportes ";
    $sql .= " LEFT JOIN usuario ON usuario.id = sat_reportes.idUsuario";
    $sql .= " LEFT JOIN usuario_empresa ON usuario_empresa.idUsuario = sat_reportes.idUsuario";
    //
    $sql.=" WHERE 1 ".$sqlFiltro." GROUP BY sat_reportes.idUsuario ORDER BY usuario.nombre ASC";
    $sql.= " LIMIT ".$inicio.", ".$registros;
    //
    $PSN1->query($sql);
    $numero=$PSN1->num_rows();

    ?><div class="container">

    <form name="form" id="form" method="get" class="form-horizontal">
    <input type="hidden" name="doc" value="reportar_buscar_con2" />

        <div class="form-group">
            <h2 class="text-center well">.FILTROS DE BUSQUEDA - REPORTES.</h2>
        </div>

        <div class="form-group">
            <label class="control-label col-sm-2" for="idUsuario"><strong>Facilitador Satura:</strong></label>
            <div class="col-sm-4"><?php
            ?><select name="idUsuario" onchange="this.form.submit()" class="form-control">
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
            
            <!--<label class="control-label col-sm-2" for="idGrupoMadre"><strong>Grupo Madre:</strong></label>
            <div class="col-sm-4"><select name="idGrupoMadre" onchange="this.form.submit()" class="form-control">
                <option value="">Ver todos</option><?php
                //
                //	TRAEMOS LOS GRUPOS MADRES
                //
                $sql = "SELECT id, nombre ";
                $sql.=" FROM sat_grupos ";
                if($_SESSION["perfil"] == 163){
                    $sql.=" WHERE idUsuario = '".$_SESSION["id"]."'";
                }
                $sql.=" ORDER BY nombre asc";
                //
                $PSN2->query($sql);
                $numero=$PSN2->num_rows();
                if($numero > 0)
                {
                    while($PSN2->next_record())
                    {
                        ?><option value="<?=$PSN2->f('id'); ?>" <?php
                        if($buscar_idGrupoMadre == $PSN2->f('id')){
                            ?>selected="selected"<?php
                        }
                        ?></optio><?=$PSN2->f('nombre'); ?></option><?php
                    }
                }
                ?></select></div>//-->
        
        </div>

        <div class="form-group">
            <label class="control-label col-sm-2" for="fechaInicial"><strong>Fecha Inicial:</strong></label>
            <div class="col-sm-4"><input type="date" name="fechaInicial" id="fechaInicial" value="<?=$fechaInicial; ?>" class="form-control" /></div>

            <label class="control-label col-sm-2" for="fechaFinal"><strong>Fecha Final:</strong></label>
            <div class="col-sm-4"><input type="date" name="fechaFinal" id="fechaFinal" value="<?=$fechaFinal; ?>" class="form-control" /></div>
        </div>
        
        <div class="row text-center">
            <input type="submit" value="Buscar" class="btn btn-success" />
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
    <div class="row">
        <h2 class="text-center well">.<?php echo $total_registros; ?> REGISTROS ENCONTRADOS.</h2>
    </div>

    <table border="0" cellspacing="0" cellpadding="2"  align="center" class="table table-bordered" style="font-size:12px">
        <thead>
            <tr> 
                <th>Id</th>
                <th>Facilitador</th>
                    <th>RM</th>
                    <th>Proceso</th>
                    <th>Sitio</th>
                <th>Grupos</th>
                <th>Grupos Nuevos</th>
                <th>Bautizados</th>
                    <th>Asistencia</th>
                <th>Decisiones</th>
                <th>Preparandose</th>
                    <th>Bautizados este periodo</th>
                <th>En Discipulado</th>
                    <th>Lideres capacitandose</th>
                <th>Iglesias Reconocidas</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if($total_registros > 0)
            {
                $contador = 1;
                while($PSN1->next_record())
                {
                    //Solo si no se ha modificado ya el formulario.
                    $idUsuario = $PSN1->f('idUsuario');
                    $plantador = $PSN1->f("plantador");
                    $fechaReporte = $PSN1->f("fechaReporte");
                    $fechaInicio = $PSN1->f("fechaInicio");        
                    $sitioReunion = $PSN1->f("sitioReunion");
                    $grupoMadre_txt = $PSN1->f("grupoMadre_txt");
                    $idGrupoMadre = $PSN1->f("idGrupoMadre");
                    $generacionNumero = intval($PSN1->f("generacionNumero"));

                    $gruposConteo = $PSN1->f("gruposConteo");
                    
                    $sql = "SELECT COUNT(DISTINCT sat_reportes.idGrupoMadre) as conteo ";
                    $sql.=" FROM sat_reportes ";
                    //
                    //
                    $sqlFiltro = "";
                    if(isset($_REQUEST["fechaInicial"]) && eliminarInvalidos($_REQUEST["fechaInicial"]) != ""){
                        $sqlFiltro .= " AND sat_reportes.fechaInicio >= '".$fechaInicial."'";
                    }
                    //
                    if(isset($_REQUEST["fechaFinal"]) && eliminarInvalidos($_REQUEST["fechaFinal"]) != ""){
                        $sqlFiltro .= " AND sat_reportes.fechaInicio <= '".$fechaFinal."'";
                    }

                    $sql.=" WHERE idUsuario = '".$idUsuario."' ".$sqlFiltro;
                    $PSN2->query($sql);
                    if($PSN2->num_rows() > 0)
                    {
                        if($PSN2->next_record())
                        {
                            $gruposNuevos = $PSN2->f('conteo');
                        }
                    }
                    
                    
                    
                    $nombreUsuario = $PSN1->f("nombreUsuario");
                        $empresa_sitio = $PSN1->f("empresa_sitio");
                        $empresa_rm = $PSN1->f("empresa_rm");
                        $empresa_proceso = $PSN1->f("empresa_proceso");

                    $asistencia_hom = $PSN1->f("asistencia_hom");
                    $asistencia_muj = $PSN1->f("asistencia_muj");
                    $asistencia_jov = $PSN1->f("asistencia_jov");
                    $asistencia_nin = $PSN1->f("asistencia_nin");

                    $bautizados = $PSN1->f("bautizados");
                    $bautizadosPeriodo = $PSN1->f("bautizadosPeriodo");

                    //Calculados:
                    $asistencia_total  = $PSN1->f("asistencia_total");
                    $discipulado  = $PSN1->f("discipulado");
                    $desiciones  = $PSN1->f("desiciones");
                    $preparandose  = $PSN1->f("preparandose");
                    $iglesias_reconocidas = $PSN1->f("iglesias_reconocidas");  
                    
                    $lideresCapacitandose = 0;                        
                    
                    $sql = "SELECT SUM(asistencia_total) as total, COUNT(sat_reportes.id) as conteo ";
                    $sql.=" FROM sat_reportes ";
                    $sql .= " LEFT JOIN usuario ON usuario.id = sat_reportes.idUsuario";
                    $sql .= " LEFT JOIN usuario_empresa ON usuario_empresa.idUsuario = sat_reportes.idUsuario";
                    //
                    $sql.=" WHERE 1 ".$sqlFiltro." AND  sat_reportes.idUsuario = '".$idUsuario."' AND sat_reportes.generacionNumero = 0 GROUP BY sat_reportes.idUsuario";
                    $PSN2->query($sql);
                    if($PSN2->num_rows() > 0)
                    {
                        if($PSN2->next_record())
                        {
                            $lideresCapacitandose = ($PSN2->f('total')-$PSN2->f('conteo'));
                        }
                    }
                    //
                    ?><tr>
                        <td><?=$contador; ?></td>
                        <td><?=$nombreUsuario; ?></td>
                            <td><?=$empresa_rm; ?></td>
                            <td><?=$empresa_proceso; ?></td>
                            <td><?=$empresa_sitio; ?></td>
                        <td align="center"><?=$gruposConteo; ?></td>
                        <td align="center"><?=$gruposNuevos; ?></td>
                        <td align="center"><?=$bautizados; ?></td>
                            <td align="center"><?=$asistencia_total; ?></td>
                        <td align="center"><?=$desiciones; ?></td>
                        <td align="center"><?=$preparandose; ?></td>
                            <td align="center"><?=$bautizadosPeriodo; ?></td>
                        <td align="center"><?=$discipulado; ?></td>
                            <td align="center"><?=$lideresCapacitandose; ?></td>
                        <td align="center"><?=$iglesias_reconocidas; ?></td>
                    </tr>
                    <?php
                    $contador++;
                }
            }
            ?>
        </tbody>
        </table>
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

    <br />
    <center>
    <a href="<?=$_SERVER['REQUEST_URI']; ?>&excelXML=" class="btn btn-info"><span class="glyphicon glyphicon-cloud-download"></span> DESCARGAR PARA EXCEL</a></center>


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
    </script><?php
}
?>