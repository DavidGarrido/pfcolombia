<?php
// Control de acceso a través del menú - Verificar si el usuario tiene acceso
$tieneAcceso = false;
if(isset($_SESSION["id"])) {
    $sql = "SELECT COUNT(*) as acceso FROM usuarios_menu WHERE idUsuario = ".$_SESSION["id"]." AND idMenu = 79";
    $PSN_acceso = new DBbase_Sql;
    $PSN_acceso->query($sql);
    if($PSN_acceso->next_record()) {
        $tieneAcceso = ($PSN_acceso->f('acceso') > 0);
    }
}

if(!$tieneAcceso) {
    ?>
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="alert alert-danger">
                    <h4><i class="fas fa-exclamation-triangle"></i> Acceso Denegado</h4>
                    <p>No tienes permisos para acceder a esta sección.</p>
                </div>
            </div>
        </div>
    </div>
    <?php
    return;
}

// Configurar fechas por defecto (año actual)
$fecha_inicio_default = date('Y-01-01'); // Primer día del año actual
$fecha_fin_default = date('Y-12-31'); // Último día del año actual

// Obtener fechas del formulario o usar valores por defecto
$fecha_inicio = isset($_POST['fecha_inicio']) ? $_POST['fecha_inicio'] : $fecha_inicio_default;
$fecha_fin = isset($_POST['fecha_fin']) ? $_POST['fecha_fin'] : $fecha_fin_default;

$filtro_estado = isset($_POST['filtro_estado']) ? $_POST['filtro_estado'] : 'todos';
$filtro_regional = isset($_POST['filtro_regional']) ? $_POST['filtro_regional'] : 'todos';
$facilitadores_excluidos_incluir = isset($_POST['facilitadores_excluidos']) ? $_POST['facilitadores_excluidos'] : array();

// Definir escala de evaluación fija
$escala_actual = array(
    'nombre' => 'Escala de Evaluación',
    'excelente_min' => 3.0,
    'bueno_min' => 2.5,
    'regular_min' => 2.0
);

// Obtener lista de regionales para el filtro
$PSN_regionales = new DBbase_Sql;
$sql_regionales = "SELECT DISTINCT c.id, c.descripcion 
                   FROM categorias c 
                   INNER JOIN usuario_empresa ue ON c.id = ue.empresa_pd 
                   INNER JOIN usuario u ON ue.idUsuario = u.id 
                   WHERE u.tipo IN (163, 162) AND u.acceso = 1 
                   ORDER BY c.descripcion";
$PSN_regionales->query($sql_regionales);
$regionales_lista = array();
while($PSN_regionales->next_record()) {
    $regionales_lista[] = array(
        'id' => $PSN_regionales->f('id'),
        'nombre' => $PSN_regionales->f('descripcion')
    );
}

// Obtener lista de facilitadores excluidos para mostrar en el filtro
$PSN_excluidos = new DBbase_Sql;
$sql_excluidos = "SELECT id, nombre FROM usuario WHERE tipo IN (163, 162) AND acceso = 1 AND excluido_reportes = 1 ORDER BY nombre";
$PSN_excluidos->query($sql_excluidos);
$facilitadores_excluidos_lista = array();
while($PSN_excluidos->next_record()) {
    $facilitadores_excluidos_lista[] = array(
        'id' => $PSN_excluidos->f('id'),
        'nombre' => $PSN_excluidos->f('nombre')
    );
}

?>

<div class="container-fluid" style="max-width: 98%; padding: 10px;">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-primary custom-panel">
                <div class="panel-heading">
                    <h3 class="panel-title">
                        <i class="fas fa-chart-bar"></i> Dashboard de Promedios por Usuario
                    </h3>
                </div>
                <div class="panel-body">
                    
                    <!-- Formulario de Filtro -->
                    <form method="POST" class="form-horizontal" style="margin-bottom: 20px;">
                        <div class="row filter-row">
                            <div class="col-lg-3 col-md-4 col-sm-6 col-xs-12">
                                <div class="form-group">
                                    <label for="fecha_inicio" class="control-label filter-label">
                                        <i class="fas fa-calendar-alt"></i> Fecha Inicio:
                                    </label>
                                    <input type="date" class="form-control filter-input" id="fecha_inicio" name="fecha_inicio" 
                                           value="<?php echo htmlspecialchars($fecha_inicio); ?>" required>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-4 col-sm-6 col-xs-12">
                                <div class="form-group">
                                    <label for="fecha_fin" class="control-label filter-label">
                                        <i class="fas fa-calendar-alt"></i> Fecha Fin:
                                    </label>
                                    <input type="date" class="form-control filter-input" id="fecha_fin" name="fecha_fin" 
                                           value="<?php echo htmlspecialchars($fecha_fin); ?>" required>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-4 col-sm-6 col-xs-12">
                                <div class="form-group">
                                    <label for="filtro_estado" class="control-label filter-label">
                                        <i class="fas fa-filter"></i> Filtrar por Estado:
                                    </label>
                                    <select class="form-control filter-select" id="filtro_estado" name="filtro_estado">
                                        <option value="todos" <?php echo ($filtro_estado == 'todos') ? 'selected' : ''; ?>>
                                            👥 Todos los Estados
                                        </option>
                                        <option value="excelente" <?php echo ($filtro_estado == 'excelente') ? 'selected' : ''; ?>>
                                            ⭐ Excelente (3.0-4.0)
                                        </option>
                                        <option value="bueno" <?php echo ($filtro_estado == 'bueno') ? 'selected' : ''; ?>>
                                            👍 Bueno (2.5-2.9)
                                        </option>
                                        <option value="regular" <?php echo ($filtro_estado == 'regular') ? 'selected' : ''; ?>>
                                            📊 Regular (2.0-2.4)
                                        </option>
                                        <option value="necesita_mejora" <?php echo ($filtro_estado == 'necesita_mejora') ? 'selected' : ''; ?>>
                                            ⚠️ Por Mejorar (≤1.9)
                                        </option>
                                        <option value="sin_reportes" <?php echo ($filtro_estado == 'sin_reportes') ? 'selected' : ''; ?>>
                                            📭 Sin Reportes
                                        </option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-4 col-sm-6 col-xs-12">
                                <div class="form-group">
                                    <label for="filtro_regional" class="control-label filter-label">
                                        <i class="fas fa-map-marker-alt"></i> Filtrar por Regional:
                                    </label>
                                    <select class="form-control filter-select" id="filtro_regional" name="filtro_regional">
                                        <option value="todos" <?php echo ($filtro_regional == 'todos') ? 'selected' : ''; ?>>
                                            🌍 Todas las Regionales
                                        </option>
                                        <?php foreach($regionales_lista as $regional) { ?>
                                        <option value="<?php echo $regional['id']; ?>" <?php echo ($filtro_regional == $regional['id']) ? 'selected' : ''; ?>>
                                            📍 <?php echo htmlspecialchars($regional['nombre']); ?>
                                        </option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-2 col-md-12 col-sm-4 col-xs-12">
                                <div class="form-group">
                                    <label class="control-label">&nbsp;</label>
                                    <button type="submit" class="btn btn-primary btn-block filter-button">
                                        <i class="fas fa-search"></i> Filtrar
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Filtro de Facilitadores Excluidos -->
                        <?php if(count($facilitadores_excluidos_lista) > 0) { ?>
                        <div class="row" style="margin-top: 15px;">
                            <div class="col-md-12">
                                <div class="panel panel-warning" style="border-radius: 8px;">
                                    <div class="panel-heading" style="background-color: #f0ad4e; border-color: #eea236;">
                                        <h4 class="panel-title" style="color: white;">
                                            <i class="fas fa-users-slash"></i> Facilitadores Normalmente Excluidos 
                                            <small style="font-weight: normal;">(Selecciona los que debían reportar en este período)</small>
                                        </h4>
                                    </div>
                                    <div class="panel-body" style="background-color: #fff9e6;">
                                        <div class="row">
                                            <?php 
                                            $col_count = 0;
                                            foreach($facilitadores_excluidos_lista as $facilitador_excluido) { 
                                                if($col_count % 3 == 0 && $col_count > 0) echo '</div><div class="row">';
                                            ?>
                                            <div class="col-md-4 col-sm-6" style="margin-bottom: 10px;">
                                                <label class="checkbox-inline" style="font-weight: normal; color: #856404;">
                                                    <input type="checkbox" name="facilitadores_excluidos[]" 
                                                           value="<?php echo $facilitador_excluido['id']; ?>"
                                                           <?php echo in_array($facilitador_excluido['id'], $facilitadores_excluidos_incluir) ? 'checked' : ''; ?>>
                                                    <span style="margin-left: 5px;"><?php echo htmlspecialchars($facilitador_excluido['nombre']); ?></span>
                                                </label>
                                            </div>
                                            <?php 
                                                $col_count++;
                                            } 
                                            ?>
                                        </div>
                                        <div class="alert alert-info" style="margin-top: 15px; margin-bottom: 0; font-size: 12px;">
                                            <i class="fas fa-info-circle"></i> 
                                            <strong>Nota:</strong> Estos facilitadores están normalmente excluidos de los reportes. 
                                            Selecciona solo aquellos que debían reportar durante el período seleccionado.
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php } ?>
                    </form>

                    <!-- Información del período seleccionado -->
                    <div class="alert alert-info">
                        <i class="fas fa-calendar-alt"></i> 
                        Mostrando promedios del <strong><?php echo date('d/m/Y', strtotime($fecha_inicio)); ?></strong> 
                        al <strong><?php echo date('d/m/Y', strtotime($fecha_fin)); ?></strong>
                        <?php if($filtro_estado != 'todos') { ?>
                            | Estado: <strong><?php echo ucfirst(str_replace('_', ' ', $filtro_estado)); ?></strong>
                        <?php } ?>
                        <?php if($filtro_regional != 'todos') { 
                            $regional_seleccionada = '';
                            foreach($regionales_lista as $reg) {
                                if($reg['id'] == $filtro_regional) {
                                    $regional_seleccionada = $reg['nombre'];
                                    break;
                                }
                            }
                        ?>
                            | Regional: <strong><?php echo $regional_seleccionada; ?></strong>
                        <?php } ?>
                        <?php if(!empty($facilitadores_excluidos_incluir)) { ?>
                            | <span style="color: #f0ad4e;"><strong><?php echo count($facilitadores_excluidos_incluir); ?> facilitador(es) normalmente excluido(s) incluido(s)</strong></span>
                        <?php } ?>
                    </div>

                    <!-- Información de la escala actual -->
                    <div class="alert alert-warning">
                        <strong>Escala de Evaluación:</strong>
                        <span class="label label-success">Excelente: 3.0-4.0</span>
                        <span class="label label-warning">Bueno: 2.5-2.9</span>
                        <span class="label label-info">Regular: 2.0-2.4</span>
                        <span class="label label-danger">Por Mejorar: ≤1.9</span>
                        <span class="label label-default">Sin Reportes: 0</span>
                    </div>

                    <?php
                    // Conexión a la base de datos
                    $PSN = new DBbase_Sql;
                    
                    // Consulta SQL para obtener promedios por facilitador
                    // Solo incluir reportes con mapeo (generaciones 0,1-5, excluyendo 77, 8)
                    // ADAPTACIÓN PFCOLOMBIA: Convertir campos varchar a numeric usando CAST
                    $sql = "SELECT
                                u.nombre AS facilitador_nombre,
                                u.id AS facilitador_id,
                                c.descripcion AS regional_nombre,
                                IFNULL(ROUND(AVG(
                                    (CAST(r.mapeo_oracion AS UNSIGNED) + CAST(r.mapeo_companerismo AS UNSIGNED) + 
                                     CAST(r.mapeo_adoracion AS UNSIGNED) + CAST(r.mapeo_biblia AS UNSIGNED) + 
                                     CAST(r.mapeo_evangelizar AS UNSIGNED) + CAST(r.mapeo_cena AS UNSIGNED) + 
                                     CAST(r.mapeo_dar AS UNSIGNED) + CAST(r.mapeo_bautizar AS UNSIGNED) + 
                                     CAST(r.mapeo_trabajadores AS UNSIGNED)) / 9
                                ), 2), 0) AS promedio_facilitador,
                                COUNT(r.id) AS total_reportes
                            FROM
                                usuario u
                            LEFT JOIN usuario_empresa ue ON u.id = ue.idUsuario
                            LEFT JOIN categorias c ON c.id = ue.empresa_pd
                            LEFT JOIN
                                sat_reportes r ON u.id = r.creacionUsuario 
                                AND r.fechaReporte BETWEEN '".$fecha_inicio."' AND '".$fecha_fin."'
                                AND r.generacionNumero BETWEEN 1 AND 5 
                                AND r.mapeo_oracion IS NOT NULL 
                                AND r.mapeo_oracion != ''
                            WHERE
                                u.tipo IN (163, 162) AND u.acceso = 1 AND (
                                    (u.excluido_reportes IS NULL OR u.excluido_reportes = 0) 
                                    ".(!empty($facilitadores_excluidos_incluir) ? "OR u.id IN (" . implode(',', array_map('intval', $facilitadores_excluidos_incluir)) . ")" : "")."
                                )
                                ".($filtro_regional != 'todos' ? " AND ue.empresa_pd = ".intval($filtro_regional) : "")."
                            GROUP BY
                                u.id, u.nombre, c.descripcion
                            ORDER BY
                                promedio_facilitador DESC, u.nombre";
                    
                    $PSN->query($sql);
                    
                    // Función para determinar categoría según escala
                    function determinarCategoria($promedio, $escala) {
                        if($promedio == 0) {
                            return array(
                                'categoria' => 'sin_reportes',
                                'nombre' => 'Sin reportes',
                                'color' => 'default'
                            );
                        } elseif($promedio >= $escala['excelente_min']) {
                            return array(
                                'categoria' => 'excelente',
                                'nombre' => 'Excelente',
                                'color' => 'success'
                            );
                        } elseif($promedio >= $escala['bueno_min']) {
                            return array(
                                'categoria' => 'bueno',
                                'nombre' => 'Bueno',
                                'color' => 'warning'
                            );
                        } elseif($promedio >= $escala['regular_min']) {
                            return array(
                                'categoria' => 'regular',
                                'nombre' => 'Regular',
                                'color' => 'info'
                            );
                        } else {
                            return array(
                                'categoria' => 'necesita_mejora',
                                'nombre' => 'Por Mejorar',
                                'color' => 'danger'
                            );
                        }
                    }
                    
                    // Recolectar y filtrar datos
                    $facilitadores_filtrados = array();
                    $total_facilitadores = 0;
                    $suma_promedios = 0;
                    
                    while($PSN->next_record()) {
                        $promedio = floatval($PSN->f('promedio_facilitador'));
                        $categoria = determinarCategoria($promedio, $escala_actual);
                        
                        // Aplicar filtro por estado
                        if($filtro_estado == 'todos' || $categoria['categoria'] == $filtro_estado) {
                            $facilitadores_filtrados[] = array(
                                'nombre' => $PSN->f('facilitador_nombre'),
                                'regional' => $PSN->f('regional_nombre'),
                                'promedio' => $promedio,
                                'total_reportes' => intval($PSN->f('total_reportes')),
                                'categoria' => $categoria
                            );
                            $suma_promedios += $promedio;
                            $total_facilitadores++;
                        }
                    }
                    
                    if(count($facilitadores_filtrados) > 0) {
                        ?>
                        
                        <!-- Tabla de Resultados -->
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered table-hover">
                                <thead class="bg-primary">
                                    <tr>
                                        <th style="color: white;">#</th>
                                        <th style="color: white;">Nombre del Usuario</th>
                                        <th style="color: white;">Regional</th>
                                        <th style="color: white;" class="text-center">Promedio de Mapeo</th>
                                        <th style="color: white;" class="text-center">Total de Reportes</th>
                                        <th style="color: white;" class="text-center">Estado</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $contador = 1;
                                    
                                    foreach($facilitadores_filtrados as $facilitador) {
                                        ?>
                                        <tr>
                                            <td><?php echo $contador; ?></td>
                                            <td><?php echo htmlspecialchars($facilitador['nombre']); ?></td>
                                            <td>
                                                <small style="color: #666;">
                                                    <i class="fas fa-map-marker-alt"></i> 
                                                    <?php echo htmlspecialchars($facilitador['regional'] ?: 'Sin asignar'); ?>
                                                </small>
                                            </td>
                                            <td class="text-center">
                                                <span class="label label-<?php echo $facilitador['categoria']['color']; ?>" style="font-size: 12px; padding: 4px 8px;">
                                                    <?php echo number_format($facilitador['promedio'], 2); ?>
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <?php echo $facilitador['total_reportes']; ?>
                                            </td>
                                            <td class="text-center">
                                                <span class="label label-<?php echo $facilitador['categoria']['color']; ?>">
                                                    <?php echo $facilitador['categoria']['nombre']; ?>
                                                </span>
                                            </td>
                                        </tr>
                                        <?php
                                        $contador++;
                                    }
                                    
                                    // Calcular promedio general
                                    $promedio_general = $total_facilitadores > 0 ? $suma_promedios / $total_facilitadores : 0;
                                    ?>
                                </tbody>
                                <tfoot class="bg-info">
                                    <tr>
                                        <th colspan="3" style="color: white;">
                                            PROMEDIO GENERAL (<?php echo $total_facilitadores; ?> usuarios<?php echo ($filtro_estado != 'todos' || $filtro_regional != 'todos') ? ' filtrados' : ''; ?>)
                                        </th>
                                        <th class="text-center" style="color: white;">
                                            <?php echo number_format($promedio_general, 2); ?>
                                        </th>
                                        <th colspan="2" style="color: white;"></th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        <!-- Estadísticas adicionales -->
                        <div class="row stats-container" style="margin-top: 20px;">
                            <?php
                            // Recalcular estadísticas basadas en todos los facilitadores (no solo filtrados)
                            $PSN->query($sql);
                            $stats = array(
                                'excelente' => 0,
                                'bueno' => 0,
                                'regular' => 0,
                                'necesita_mejora' => 0,
                                'sin_reportes' => 0
                            );
                            
                            while($PSN->next_record()) {
                                $promedio = floatval($PSN->f('promedio_facilitador'));
                                $categoria = determinarCategoria($promedio, $escala_actual);
                                $stats[$categoria['categoria']]++;
                            }
                            ?>
                            
                            <div class="col-lg-2 col-md-3 col-sm-4 col-xs-6">
                                <div class="panel panel-success stats-panel">
                                    <div class="panel-heading text-center">
                                        <h5>Excelente (3.0-4.0)</h5>
                                    </div>
                                    <div class="panel-body text-center">
                                        <h3><?php echo $stats['excelente']; ?></h3>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-2 col-md-3 col-sm-4 col-xs-6">
                                <div class="panel panel-warning stats-panel">
                                    <div class="panel-heading text-center">
                                        <h5>Bueno (2.5-2.9)</h5>
                                    </div>
                                    <div class="panel-body text-center">
                                        <h3><?php echo $stats['bueno']; ?></h3>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-2 col-md-3 col-sm-4 col-xs-6">
                                <div class="panel panel-info stats-panel">
                                    <div class="panel-heading text-center">
                                        <h5>Regular (2.0-2.4)</h5>
                                    </div>
                                    <div class="panel-body text-center">
                                        <h3><?php echo $stats['regular']; ?></h3>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-2 col-md-3 col-sm-4 col-xs-6">
                                <div class="panel panel-danger stats-panel">
                                    <div class="panel-heading text-center">
                                        <h5>Por mejorar (≤1.9)</h5>
                                    </div>
                                    <div class="panel-body text-center">
                                        <h3><?php echo $stats['necesita_mejora']; ?></h3>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-2 col-md-3 col-sm-4 col-xs-6">
                                <div class="panel panel-default">
                                    <div class="panel-heading text-center">
                                        <h5>Sin reportes (0)</h5>
                                    </div>
                                    <div class="panel-body text-center">
                                        <h3><?php echo $stats['sin_reportes']; ?></h3>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <?php
                    } else {
                        ?>
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle"></i> 
                            No se encontraron usuarios activos en el período seleccionado.
                        </div>
                        <?php
                    }
                    ?>

                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Estilos adicionales para mejorar la presentación */
.table th {
    font-weight: bold;
    font-size: 12px;
}

.table td {
    font-size: 12px;
    vertical-align: middle;
}

.label {
    display: inline-block;
    min-width: 50px;
}

.panel-title {
    font-size: 16px;
    font-weight: bold;
}

.bg-primary th {
    background-color: #FF1B34 !important;
}

.bg-info th {
    background-color: #FF1B34 !important;
}

/* Personalizar panel header */
.custom-panel .panel-heading {
    background-color: #FF1B34 !important;
    border-color: #FF1B34 !important;
}

.custom-panel .panel-title {
    color: white !important;
}

/* Mejorar contraste en paneles de estadísticas */
.stats-panel .panel-heading {
    color: white !important;
    text-shadow: 1px 1px 2px rgba(0,0,0,0.7);
}

.stats-panel .panel-heading h5 {
    color: white !important;
    font-weight: bold;
    text-shadow: 1px 1px 2px rgba(0,0,0,0.7);
}

/* Centrar estadísticas en la parte inferior */
.stats-container {
    display: flex;
    justify-content: center;
    flex-wrap: wrap;
    gap: 15px;
}

.stats-container .col-lg-2,
.stats-container .col-md-3,
.stats-container .col-sm-4,
.stats-container .col-xs-6 {
    flex: 0 0 auto;
    max-width: 200px;
}

@media (max-width: 1200px) {
    .stats-container .col-lg-2 {
        flex: 0 0 calc(20% - 12px);
        max-width: calc(20% - 12px);
    }
}

@media (max-width: 992px) {
    .stats-container .col-md-3 {
        flex: 0 0 calc(33.33% - 10px);
        max-width: calc(33.33% - 10px);
    }
}

@media (max-width: 768px) {
    .stats-container .col-sm-4 {
        flex: 0 0 calc(50% - 8px);
        max-width: calc(50% - 8px);
    }
}

@media (max-width: 576px) {
    .stats-container .col-xs-6 {
        flex: 0 0 calc(50% - 8px);
        max-width: calc(50% - 8px);
    }
}

/* Estilos para los filtros mejorados */
.filter-row {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-radius: 10px;
    padding: 20px;
    margin: 0 15px 20px 15px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    border: 1px solid #dee2e6;
}

.filter-label {
    font-weight: bold;
    color: #495057;
    font-size: 13px;
    margin-bottom: 8px;
}

.filter-label i {
    color: #FF1B34;
    margin-right: 5px;
}

.filter-input, .filter-select {
    border: 2px solid #e9ecef;
    border-radius: 8px;
    padding: 12px 15px;
    font-size: 14px;
    transition: all 0.3s ease;
    background-color: #fff;
    height: 42px;
    line-height: 1.4;
}

.filter-input:focus, .filter-select:focus {
    border-color: #FF1B34;
    box-shadow: 0 0 0 0.2rem rgba(255,27,52,0.25);
    background-color: #fff;
}

.filter-select {
    cursor: pointer;
}

.filter-select option {
    padding: 10px 8px;
    font-size: 14px;
    line-height: 1.5;
}

.filter-button {
    background: linear-gradient(45deg, #FF1B34, #D91628);
    border: none;
    border-radius: 8px;
    padding: 10px 15px;
    font-weight: bold;
    font-size: 12px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    transition: all 0.3s ease;
    box-shadow: 0 3px 10px rgba(255,27,52,0.3);
}

.filter-button:hover {
    background: linear-gradient(45deg, #D91628, #B8121F);
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(255,27,52,0.4);
}

.filter-button:active {
    transform: translateY(0);
}

/* Responsive adjustments for filters */
@media (max-width: 768px) {
    .filter-row {
        padding: 15px;
    }
    
    .filter-label {
        font-size: 12px;
    }
    
    .filter-input, .filter-select {
        font-size: 13px;
        padding: 10px 12px;
        height: 38px;
    }
    
    .filter-button {
        margin-top: 10px;
        font-size: 11px;
    }
}
</style>