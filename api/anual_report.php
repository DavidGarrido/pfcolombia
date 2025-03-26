<?php
session_start();
header('Content-Type: application/json');
include_once('../funciones.php');

// Verificar que la solicitud se haga por POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  echo json_encode(['error' => 'Método no permitido']);
  exit;
}

// Recibir y validar los datos enviados
$anio = isset($_POST['anio']) ? intval($_POST['anio']) : 0;
$proyecto = isset($_POST['proyecto']) ? trim($_POST['proyecto']) : '';

if ($anio <= 0 || empty($proyecto)) {
  echo json_encode(['error' => 'Datos de filtro inválidos']);
  exit;
}

// Filtrar por año utilizando la columna fechaReporte
$filtroAnio = " AND YEAR(fechaReporte) = " . $anio;
$sqlFiltro = "";

$sqlUser = "";
if ($_SESSION["perfil"] == 163) {
  $buscar_idUsuario = soloNumeros($_SESSION["id"]);
  $sqlUser .= "sat_reportes.idUsuario = '" . $buscar_idUsuario . "' AND ";
}
if ($_SESSION["perfil"] == 167) {
  if ($_SESSION["id_zona"] != "" && $_SESSION["id_zona"] != 0) {
    $sqlFiltro .= " AND C.idSec = '" . $_SESSION["id_zona"] . "'";
    $_REQUEST["empresa_sitio_cor"] = $_SESSION["id_zona"];
    $buscar_zona = $_SESSION["id_zona"];
  }
}
if ($_SESSION["perfil"] == 162) {
  if ($_SESSION["empresa_pd"] != "" && $_SESSION["empresa_pd"] != 0) {
    $sqlFiltro .= " AND UE.empresa_pd = '" . $_SESSION["empresa_pd"] . "'";
    $_REQUEST["empresa_pd"] = $_SESSION["empresa_pd"];
  }
}

// Instanciar la clase de conexión a la base de datos
$PSN = new DBbase_Sql;
$PSN1 = new DBbase_Sql;
$PSN4 = new DBbase_Sql;
$data = array();

$sqlFiltro .= $filtroAnio;
switch ($proyecto) {
  case 'proyecto-felipe':
    // Para el proyecto "proyecto-felipe" se utiliza la consulta proporcionada
    // Si es necesario, se podría agregar un filtro adicional por proyecto, por ejemplo:
    // $filtroProyecto = " AND rep_text2 = 'proyecto-felipe'";
    // En este ejemplo se usa únicamente el filtro por año.

    // Primer query: Sumar valores de los campos de interés
    $sql = "SELECT 
                    SUM(asistencia_total) AS total_poblacion,
                    SUM(asistencia_hom) AS prns_invitados,
                    SUM(asistencia_muj) AS prns_iniciaron,
                    SUM(asistencia_jov) AS cursos_act,
                    SUM(asistencia_nin) AS prns_graduados,
                    SUM(bautizados) AS internos,
                    SUM(desiciones) AS externos,
                    SUM(bautizados + desiciones) AS voluntarios,
                    SUM(rep_ndis) AS discipulos
                FROM sat_reportes
                LEFT JOIN usuario AS U ON U.id = sat_reportes.idUsuario 
                LEFT JOIN tbl_regional_ubicacion AS RU ON RU.reub_id = sat_reportes.sitioReunion 
                LEFT JOIN categorias AS C ON C.id = RU.reub_reg_fk 
                LEFT JOIN usuario_empresa AS UE ON UE.idUsuario = sat_reportes.idUsuario 
                LEFT JOIN categorias AS CA ON CA.id = C.idSec
                WHERE sat_reportes.rep_tip = 319 " . $sqlFiltro;
    $PSN1->query($sql);
    $num = $PSN1->num_rows();
    if ($num > 0) {
      while ($PSN1->next_record()) {
        $data = [
          'total_poblacion' => intval($PSN1->f('total_poblacion')),
          'prns_invitados'  => intval($PSN1->f('prns_invitados')),
          'prns_iniciaron'  => intval($PSN1->f('prns_iniciaron')),
          'cursos_act'      => intval($PSN1->f('cursos_act')),
          'prns_graduados'  => intval($PSN1->f('prns_graduados')),
          'internos'        => intval($PSN1->f('internos')),
          'externos'        => intval($PSN1->f('externos')),
          'voluntarios'     => intval($PSN1->f('voluntarios')),
          'discipulos'      => intval($PSN1->f('discipulos'))
        ];
      }
    } else {
      $data = ['error' => 'No se encontraron registros para los filtros proporcionados'];
    }

    // Segundo query: Contar registros agrupados por sitioReunion (ejemplo: total de prisiones)
    $sql2 = "SELECT COUNT(sat_reportes.id) AS total_prisiones 
                 FROM sat_reportes
                 LEFT JOIN usuario AS U ON U.id = sat_reportes.idUsuario 
                 LEFT JOIN tbl_regional_ubicacion AS RU ON RU.reub_id = sat_reportes.sitioReunion 
                 LEFT JOIN categorias AS C ON C.id = RU.reub_reg_fk 
                 LEFT JOIN usuario_empresa AS UE ON UE.idUsuario = sat_reportes.idUsuario 
                 LEFT JOIN categorias AS CA ON CA.id = C.idSec
                 WHERE sat_reportes.rep_tip = 319 " . $sqlFiltro . " GROUP BY sat_reportes.sitioReunion";
    $PSN1->query($sql2);
    $total_prisiones = $PSN1->num_rows();
    $data['total_prisiones'] = $total_prisiones;

    // Tercer query: Contar niveles para códigos de 320 a 322
    $total_nivel = array();
    for ($i = 320; $i < 323; $i++) {
      $sqlNivel = "SELECT COUNT(AD.adj_can) AS nivel 
                         FROM tbl_adjuntos AS AD 
                         LEFT JOIN sat_reportes ON AD.adj_rep_fk = sat_reportes.id
                         LEFT JOIN usuario AS U ON U.id = sat_reportes.idUsuario 
                         LEFT JOIN usuario_empresa AS UE ON UE.idUsuario = U.id 
                         LEFT JOIN tbl_regional_ubicacion AS RU ON RU.reub_id = sat_reportes.sitioReunion 
                         LEFT JOIN categorias AS C ON C.id = RU.reub_reg_fk 
                         LEFT JOIN categorias AS CA ON CA.id = C.idSec
                         WHERE AD.adj_can = '" . $i . "' AND sat_reportes.rep_tip = 319 " . $sqlFiltro . " ORDER BY sat_reportes.fechaReporte";
      $PSN1->query($sqlNivel);
      $numNivel = $PSN1->num_rows();
      if ($numNivel > 0) {
        while ($PSN1->next_record()) {
          $total_nivel[$i] = $PSN1->f('nivel');
        }
      } else {
        $total_nivel[$i] = 0;
      }
    }
    $data['total_nivel'] = $total_nivel;
    break;
  case 'evangelistas':
    $rep_prom = "SUM";
    $sql = "SELECT     
                        " . $rep_prom . "(asistencia_total) as prisiones_atendidas,
                        " . $rep_prom . "(asistencia_hom) as grupos_intramuros,
                        " . $rep_prom . "(asistencia_muj) as grupos_extramuros,
                        " . $rep_prom . "(asistencia_jov) as total_creyente,
                        " . $rep_prom . "(asistencia_nin) as total_discipulos,
                        " . $rep_prom . "(bautizados) as bautizados,
                        " . $rep_prom . "(discipulado) as discipulado,
                        " . $rep_prom . "(desiciones) as decisiones,
                        " . $rep_prom . "(preparandose) as preparandose,
                        COUNT(sat_reportes.id) AS tot_registros,
                        SUM(mapeo_oracion) AS act_oracion,
                        SUM(mapeo_companerismo) AS act_companerismo,
                        SUM(mapeo_adoracion) AS act_adoracion,
                        SUM(mapeo_biblia) AS act_biblia,
                        SUM(mapeo_evangelizar) AS act_evangelizar,
                        SUM(mapeo_cena) AS act_cena,
                        SUM(mapeo_dar) AS act_dar,
                        SUM(mapeo_bautizar) AS act_bautizar,
                        SUM(mapeo_trabajadores) AS act_trabajadores
                        ";
    $sql .= " FROM sat_reportes ";
    $sql .= " LEFT JOIN usuario AS U ON U.id = sat_reportes.idUsuario  LEFT JOIN usuario_empresa AS UE ON UE.idUsuario = U.id LEFT JOIN categorias AS C ON C.id = UE.empresa_pd LEFT JOIN categorias AS CA ON CA.id = C.idSec";
    $sql .= " WHERE " . $sqlUser . " 1 " . $sqlFiltro . " AND sat_reportes.rep_tip = 318 ";
    //echo $sql;;
    $PSN->query($sql);
    $num = $PSN->num_rows();
    if ($num > 0) {
      while ($PSN->next_record()) {
        $data = [
          'prisiones_atendidas' => round($PSN->f('prisiones_atendidas')),
          'grupos_intramuros' => round($PSN->f('grupos_intramuros')),
          'grupos_extramuros' => round($PSN->f('grupos_extramuros')),
          'total_creyente' => round($PSN->f('total_creyente')),
          'total_discipulos' => round($PSN->f('total_discipulos')),
          'bautizados' => round($PSN->f('bautizados')),
          'discipulado' => round($PSN->f('discipulado')),
          'decisiones' => round($PSN->f('decisiones')),
          'preparandose' => round($PSN->f('preparandose')),
          'tot_registros' => round($PSN->f('tot_registros')),
          'act_oracion' => round($PSN->f('act_oracion')),
          'act_companerismo' => round($PSN->f('act_companerismo')),
          'act_adoracion' => round($PSN->f('act_adoracion')),
          'act_biblia' => round($PSN->f('act_biblia')),
          'act_evangelizar' => round($PSN->f('act_evangelizar')),
          'act_cena' => round($PSN->f('act_cena')),
          'act_dar' => round($PSN->f('act_dar')),
          'act_bautizar' => round($PSN->f('act_bautizar')),
          'act_trabajadores' => round($PSN->f('act_trabajadores')),
        ];
      }
    } else {
      $varError = 1;
    }
    $data['filtro'] = $sqlFiltro;
    break;
  case 'lpp':
    $datos = array();
    //
    $sql = "SELECT SUM(sat_reportes.asistencia_total) AS total_poblacion,SUM(sat_reportes.asistencia_hom) AS prns_invitados, SUM(sat_reportes.asistencia_muj) AS prns_iniciaron,SUM(sat_reportes.asistencia_jov) AS cursos_act, SUM(sat_reportes.asistencia_nin) AS prns_graduados, SUM(sat_reportes.bautizados) AS internos, SUM(sat_reportes.desiciones) AS externos, SUM(sat_reportes.bautizados + sat_reportes.desiciones) AS voluntarios, SUM(sat_reportes.rep_ndis) AS discipulos FROM sat_reportes";
    $sql .= " LEFT JOIN usuario AS U ON U.id = sat_reportes.idUsuario 
                        LEFT JOIN tbl_regional_ubicacion AS RU ON RU.reub_id = sat_reportes.sitioReunion 
                        LEFT JOIN categorias AS C ON C.id = RU.reub_reg_fk 
                        LEFT JOIN usuario_empresa AS UE ON UE.idUsuario = sat_reportes.idUsuario 
                        LEFT JOIN categorias AS CA ON CA.id = C.idSec ";
    $sql .= " WHERE " . $sqlUser . " 1 AND sat_reportes.rep_tip = 307 " . $sqlFiltro . "";
    $datosArr[] = '["Tipo", "Cantidad"]';
    $datosArr2[] = '["Tipo", "Cantidad"]';
    //
    $PSN->query($sql);
    //echo $sql;
    $num = $PSN->num_rows();
    if ($num > 0) {
      while ($PSN->next_record()) {
        //$total_poblacion = intval($PSN->f('total_poblacion'));
        $data = [
          'prns_invitados' => intval($PSN->f('prns_invitados')),
          'prns_iniciaron' => intval($PSN->f('prns_iniciaron')),
          'cursos_act' => intval($PSN->f('cursos_act')),
          'prns_graduados' => intval($PSN->f('prns_graduados')),
          'invt_internos' => intval($PSN->f('internos')),
          'invt_externos' => intval($PSN->f('externos')),
          'voluntarios' => intval($PSN->f('voluntarios')),
          'discipulos' => intval($PSN->f('discipulos')),
        ];
      }
    } else {
      $varError = 1;
    }

    $data['sql'] = $sql;
    $sql = "SELECT count(sat_reportes.id) AS total_prisiones FROM sat_reportes";
    $sql .= " LEFT JOIN usuario AS U ON U.id = sat_reportes.idUsuario 
                        LEFT JOIN tbl_regional_ubicacion AS RU ON RU.reub_id = sat_reportes.sitioReunion 
                        LEFT JOIN categorias AS C ON C.id = RU.reub_reg_fk 
                        LEFT JOIN usuario_empresa AS UE ON UE.idUsuario = sat_reportes.idUsuario 
                        LEFT JOIN categorias AS CA ON CA.id = C.idSec ";
    $sql .= " WHERE " . $sqlUser . " 1 AND sat_reportes.rep_tip = 307 " . $sqlFiltro . " GROUP BY sat_reportes.sitioReunion";
    //

    $PSN->query($sql);
    //echo $sql;
    $data['total_prisiones'] = $PSN->num_rows();
    $sql = "SELECT sat_reportes.asistencia_total AS total_poblacion
                            FROM sat_reportes ";
    $sql .= "LEFT JOIN usuario AS U ON U.id = sat_reportes.idUsuario LEFT JOIN tbl_regional_ubicacion AS RU ON RU.reub_id = sat_reportes.sitioReunion LEFT JOIN categorias AS C ON C.id = RU.reub_reg_fk LEFT JOIN usuario_empresa AS UE ON UE.idUsuario = sat_reportes.idUsuario LEFT JOIN categorias AS CA ON CA.id = C.idSec ";
    $sql .= " WHERE 1 AND sat_reportes.rep_tip = 307 " . $sqlFiltro . " 
      ORDER BY sat_reportes.fechaReporte";
    //GROUP BY RU.reub_id 

    $sql = "SELECT sat_reportes.asistencia_total AS total_poblacion
                            FROM sat_reportes ";
    $sql .= "LEFT JOIN usuario AS U ON U.id = sat_reportes.idUsuario LEFT JOIN tbl_regional_ubicacion AS RU ON RU.reub_id = sat_reportes.sitioReunion LEFT JOIN categorias AS C ON C.id = RU.reub_reg_fk LEFT JOIN usuario_empresa AS UE ON UE.idUsuario = sat_reportes.idUsuario LEFT JOIN categorias AS CA ON CA.id = C.idSec ";
    $sql .= " WHERE 1 AND sat_reportes.rep_tip = 307 " . $sqlFiltro . " 
          GROUP BY RU.reub_id 
          ORDER BY sat_reportes.fechaReporte";
    $data['sql2'] = $sql;
    $PSN->query($sql);
    //echo $sql;
    $num = $PSN->num_rows();
    $data['num'] = $PSN->num_rows();
    $data['total_poblacion'] = 0;
    if ($num > 0) {
      while ($PSN->next_record()) {
        $data['total_poblacion'] += intval($PSN->f('total_poblacion'));
      }
    }
    $data['filtro'] = $sqlFiltro;
    $data['perfil'] = $_SESSION['perfil'];
    // $data = ['message' => 'Consulta para el proyecto LPP no está implementada aún.'];
    break;

  case 'cm':
    $sql = "SELECT 
                        SUM(asistencia_total) as asistencia_total,
                        SUM(discipulado) as discipulado,
                        SUM(desiciones) as decisiones,
                        SUM(bautizadosPeriodo) as bautizos,
                        SUM(graduadosPeriodo) as graduados,
                        COUNT(sat_reportes.id) as total_grupos";
    $sql .= " FROM sat_reportes ";
    $sql .= " LEFT JOIN usuario AS U ON U.id = sat_reportes.idUsuario 
                        LEFT JOIN tbl_regional_ubicacion AS RU ON RU.reub_id = sat_reportes.sitioReunion 
                        LEFT JOIN categorias AS C ON C.id = RU.reub_reg_fk 
                        LEFT JOIN usuario_empresa AS UE ON UE.idUsuario = sat_reportes.idUsuario 
                        LEFT JOIN categorias AS CA ON CA.id = C.idSec ";
    $sql .= " WHERE " . $sqlUser . " sat_reportes.rep_tip = 308 " . $sqlFiltro . "";
    $PSN->query($sql);
    $num = $PSN->num_rows();
    if ($num > 0) {
      while ($PSN->next_record()) {
        $data = [
          'satura_asistencia_total' => intval($PSN->f('asistencia_total')),
          'satura_discipulado' => intval($PSN->f('discipulado')),
          'satura_decisiones' => intval($PSN->f('decisiones')),
          'satura_bautizos' => intval($PSN->f('bautizos')),
          'satura_graduados' => intval($PSN->f('graduados')),
          'satura_total_grupos' => intval($PSN->f('total_grupos')),
        ];
      }
    }
    $data['sql'] = $sql;

    break;

  case 'instituto-biblico':
    $sql = "SELECT SUM(sat_reportes.asistencia_total) AS total_poblacion,SUM(sat_reportes.asistencia_hom) AS prns_invitados, SUM(sat_reportes.asistencia_muj) AS prns_iniciaron, SUM(sat_reportes.asistencia_jov) AS cursos_act, SUM(sat_reportes.asistencia_nin) AS prns_graduados, SUM(sat_reportes.bautizados) AS internos, SUM(sat_reportes.desiciones) AS externos, SUM(sat_reportes.bautizados + sat_reportes.desiciones) AS voluntarios, SUM(sat_reportes.rep_ndis) AS discipulos FROM sat_reportes";
    $sql .= " LEFT JOIN usuario AS U ON U.id = sat_reportes.idUsuario 
                        LEFT JOIN tbl_regional_ubicacion AS RU ON RU.reub_id = sat_reportes.sitioReunion 
                        LEFT JOIN categorias AS C ON C.id = RU.reub_reg_fk 
                        LEFT JOIN usuario_empresa AS UE ON UE.idUsuario = sat_reportes.idUsuario 
                        LEFT JOIN categorias AS CA ON CA.id = C.idSec ";
    $sql .= " WHERE 1 AND sat_reportes.rep_tip = 317 " . $sqlFiltro . "";
    //

    $PSN->query($sql);
    //echo $sql;
    $num = $PSN->num_rows();
    if ($num > 0) {
      while ($PSN->next_record()) {
        $data = [
          'total_poblacion' => intval($PSN->f('total_poblacion')),
          'prns_invitados' => intval($PSN->f('prns_invitados')),
          'prns_iniciaron' => intval($PSN->f('prns_iniciaron')),
          'cursos_act' => intval($PSN->f('cursos_act')),
          'prns_graduados' => intval($PSN->f('prns_graduados')),
          'invt_internos' => intval($PSN->f('internos')),
          'invt_externos' => intval($PSN->f('externos')),
          'voluntarios' => intval($PSN->f('voluntarios')),
          'discipulos' => intval($PSN->f('discipulos')),
        ];
      }
    } else {
      $varError = 1;
    }

    $data['sql1'] = $sql;
    $sql = "SELECT count(sat_reportes.id) AS total_prisiones FROM sat_reportes";
    $sql .= " LEFT JOIN usuario AS U ON U.id = sat_reportes.idUsuario 
                        LEFT JOIN tbl_regional_ubicacion AS RU ON RU.reub_id = sat_reportes.sitioReunion 
                        LEFT JOIN categorias AS C ON C.id = RU.reub_reg_fk 
                        LEFT JOIN usuario_empresa AS UE ON UE.idUsuario = sat_reportes.idUsuario 
                        LEFT JOIN categorias AS CA ON CA.id = C.idSec ";
    $sql .= " WHERE 1 AND sat_reportes.rep_tip = 317 " . $sqlFiltro . " GROUP BY sat_reportes.sitioReunion";
    $PSN1->query($sql);
    //echo $sql;

    $total_prisiones = $PSN1->num_rows();
    $data['total_prisiones'] = $PSN1->num_rows();
    $data['sql2'] = $sql;

    break;

  default:
    $data = ['error' => 'Proyecto no reconocido'];
    break;
}

// Retornar la respuesta en formato JSON
echo json_encode($data);
