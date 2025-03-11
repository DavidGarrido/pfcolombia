<?php
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

// Instanciar la clase de conexión a la base de datos
$PSN1 = new DBbase_Sql;
$data = array();

switch ($proyecto) {
    case 'proyecto-felipe':
        // Para el proyecto "proyecto-felipe" se utiliza la consulta proporcionada
        // Si es necesario, se podría agregar un filtro adicional por proyecto, por ejemplo:
        // $filtroProyecto = " AND rep_text2 = 'proyecto-felipe'";
        // En este ejemplo se usa únicamente el filtro por año.
        $sqlFiltro = $filtroAnio;
        
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
        $data = ['message' => 'Consulta para el proyecto Evangelistas no está implementada aún.'];
        break;

    case 'lpp':
        $data = ['message' => 'Consulta para el proyecto LPP no está implementada aún.'];
        break;

    case 'cm':
        $data = ['message' => 'Consulta para el proyecto C&M no está implementada aún.'];
        break;

    case 'instituto-biblico':
        $data = ['message' => 'Consulta para el proyecto Instituto Bíblico no está implementada aún.'];
        break;

    default:
        $data = ['error' => 'Proyecto no reconocido'];
        break;
}

// Retornar la respuesta en formato JSON
echo json_encode($data);
?>
