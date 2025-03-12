<?php
$sql = "SELECT SUM(sat_reportes.asistencia_total) AS total_poblacion,SUM(sat_reportes.asistencia_hom) AS prns_invitados, SUM(sat_reportes.asistencia_muj) AS prns_iniciaron, SUM(sat_reportes.asistencia_jov) AS cursos_act, SUM(sat_reportes.asistencia_nin) AS prns_graduados, SUM(sat_reportes.bautizados) AS internos, SUM(sat_reportes.desiciones) AS externos, SUM(sat_reportes.bautizados + sat_reportes.desiciones) AS voluntarios, SUM(sat_reportes.rep_ndis) AS discipulos FROM sat_reportes";
$sql .= " LEFT JOIN usuario AS U ON U.id = sat_reportes.idUsuario 
                        LEFT JOIN tbl_regional_ubicacion AS RU ON RU.reub_id = sat_reportes.sitioReunion 
                        LEFT JOIN categorias AS C ON C.id = RU.reub_reg_fk 
                        LEFT JOIN usuario_empresa AS UE ON UE.idUsuario = sat_reportes.idUsuario 
                        LEFT JOIN categorias AS CA ON CA.id = C.idSec ";
$sql .= " WHERE 1 AND sat_reportes.rep_tip = 319 " . $sqlFiltro . "";
$PSN1->query($sql);
$num = $PSN1->num_rows();
if ($num > 0) {
  while ($PSN1->next_record()) {
    $total_poblacion = intval($PSN1->f('total_poblacion'));
    $prns_invitados = intval($PSN1->f('prns_invitados'));
    $prns_iniciaron = intval($PSN1->f('prns_iniciaron'));
    $cursos_act = intval($PSN1->f('cursos_act'));
    $prns_graduados = intval($PSN1->f('prns_graduados'));
    $invt_internos = intval($PSN1->f('internos'));
    $invt_externos = intval($PSN1->f('externos'));
    $voluntarios = intval($PSN1->f('voluntarios'));
    $discipulos = intval($PSN1->f('discipulos'));
  }
} else {
  $varError = 1;
}


$sql = "SELECT count(sat_reportes.id) AS total_prisiones FROM sat_reportes";
$sql .= " LEFT JOIN usuario AS U ON U.id = sat_reportes.idUsuario 
                        LEFT JOIN tbl_regional_ubicacion AS RU ON RU.reub_id = sat_reportes.sitioReunion 
                        LEFT JOIN categorias AS C ON C.id = RU.reub_reg_fk 
                        LEFT JOIN usuario_empresa AS UE ON UE.idUsuario = sat_reportes.idUsuario 
                        LEFT JOIN categorias AS CA ON CA.id = C.idSec ";
$sql .= " WHERE 1 AND sat_reportes.rep_tip = 319 " . $sqlFiltro . " GROUP BY sat_reportes.sitioReunion";
//

$PSN1->query($sql);
