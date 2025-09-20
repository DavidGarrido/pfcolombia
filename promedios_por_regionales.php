<?php
// Control de acceso a través del menú - Verificar si el usuario tiene acceso
$tieneAcceso = false;
if(isset($_SESSION["id"])) {
    $sql = "SELECT COUNT(*) as acceso FROM usuarios_menu WHERE idUsuario = ".$_SESSION["id"]." AND idMenu = 80";
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

// Definir escala de evaluación fija
$escala_actual = array(
    'nombre' => 'Escala de Evaluación',
    'excelente_min' => 3.0,
    'bueno_min' => 2.5,
    'regular_min' => 2.0
);

?>

<div class="container-fluid" style="max-width: 98%; padding: 10px;">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-primary custom-panel">
                <div class="panel-heading">
                    <h3 class="panel-title">
                        <i class="fas fa-chart-area"></i> Promedios Consolidados por Regional
                    </h3>
                </div>
                <div class="panel-body">
                    
                    <!-- Formulario de Filtro -->
                    <form method="POST" class="form-horizontal" style="margin-bottom: 20px;">
                        <div class="row filter-row">
                            <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12">
                                <div class="form-group">
                                    <label for="fecha_inicio" class="control-label filter-label">
                                        <i class="fas fa-calendar-alt"></i> Fecha Inicio:
                                    </label>
                                    <input type="date" class="form-control filter-input" id="fecha_inicio" name="fecha_inicio" 
                                           value="<?php echo htmlspecialchars($fecha_inicio); ?>" required>
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12">
                                <div class="form-group">
                                    <label for="fecha_fin" class="control-label filter-label">
                                        <i class="fas fa-calendar-alt"></i> Fecha Fin:
                                    </label>
                                    <input type="date" class="form-control filter-input" id="fecha_fin" name="fecha_fin" 
                                           value="<?php echo htmlspecialchars($fecha_fin); ?>" required>
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-12 col-sm-12 col-xs-12">
                                <div class="form-group">
                                    <label class="control-label">&nbsp;</label>
                                    <button type="submit" class="btn btn-primary btn-block filter-button">
                                        <i class="fas fa-chart-line"></i> Generar Dashboard
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>

                    <!-- Información del período seleccionado -->
                    <div class="alert alert-info">
                        <i class="fas fa-calendar-alt"></i> 
                        Dashboard de regionales del <strong><?php echo date('d/m/Y', strtotime($fecha_inicio)); ?></strong> 
                        al <strong><?php echo date('d/m/Y', strtotime($fecha_fin)); ?></strong>
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
                    
                    // Consulta SQL para obtener promedios consolidados por regional
                    $sql = "SELECT
                                c.descripcion AS regional_nombre,
                                c.id AS regional_id,
                                COUNT(DISTINCT u.id) AS total_facilitadores,
                                COUNT(r.id) AS total_reportes,
                                IFNULL(ROUND(AVG(
                                    (CAST(r.mapeo_oracion AS UNSIGNED) + CAST(r.mapeo_companerismo AS UNSIGNED) + 
                                     CAST(r.mapeo_adoracion AS UNSIGNED) + CAST(r.mapeo_biblia AS UNSIGNED) + 
                                     CAST(r.mapeo_evangelizar AS UNSIGNED) + CAST(r.mapeo_cena AS UNSIGNED) + 
                                     CAST(r.mapeo_dar AS UNSIGNED) + CAST(r.mapeo_bautizar AS UNSIGNED) + 
                                     CAST(r.mapeo_trabajadores AS UNSIGNED)) / 9
                                ), 2), 0) AS promedio_regional
                            FROM
                                categorias c
                            LEFT JOIN usuario_empresa ue ON c.id = ue.empresa_pd
                            LEFT JOIN usuario u ON ue.idUsuario = u.id AND u.tipo IN (163, 162) AND u.acceso = 1
                            LEFT JOIN sat_reportes r ON u.id = r.creacionUsuario 
                                AND r.fechaReporte BETWEEN '".$fecha_inicio."' AND '".$fecha_fin."'
                                AND r.generacionNumero BETWEEN 1 AND 5 
                                AND r.mapeo_oracion IS NOT NULL 
                                AND r.mapeo_oracion != ''
                            WHERE
                                c.id IN (SELECT DISTINCT ue2.empresa_pd FROM usuario_empresa ue2 
                                         INNER JOIN usuario u2 ON ue2.idUsuario = u2.id 
                                         WHERE u2.tipo IN (163, 162) AND u2.acceso = 1)
                            GROUP BY
                                c.id, c.descripcion
                            ORDER BY
                                promedio_regional DESC, c.descripcion";
                    
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
                    
                    // Recolectar datos
                    $regionales_data = array();
                    $suma_promedios_general = 0;
                    $total_regionales = 0;
                    $total_facilitadores_general = 0;
                    $total_reportes_general = 0;
                    
                    while($PSN->next_record()) {
                        $promedio = floatval($PSN->f('promedio_regional'));
                        $categoria = determinarCategoria($promedio, $escala_actual);
                        $total_facilitadores = intval($PSN->f('total_facilitadores'));
                        $total_reportes = intval($PSN->f('total_reportes'));
                        
                        if($total_facilitadores > 0) { // Solo mostrar regionales con facilitadores activos
                            $regionales_data[] = array(
                                'nombre' => $PSN->f('regional_nombre'),
                                'id' => $PSN->f('regional_id'),
                                'promedio' => $promedio,
                                'total_facilitadores' => $total_facilitadores,
                                'total_reportes' => $total_reportes,
                                'categoria' => $categoria
                            );
                            
                            if($promedio > 0) {
                                $suma_promedios_general += $promedio;
                                $total_regionales++;
                            }
                            $total_facilitadores_general += $total_facilitadores;
                            $total_reportes_general += $total_reportes;
                        }
                    }
                    
                    if(count($regionales_data) > 0) {
                        $promedio_general = $total_regionales > 0 ? $suma_promedios_general / $total_regionales : 0;
                        $categoria_general = determinarCategoria($promedio_general, $escala_actual);
                        ?>
                        
                        <!-- Resumen General -->
                        <div class="row" style="margin-bottom: 30px;">
                            <div class="col-md-3">
                                <div class="panel panel-<?php echo $categoria_general['color']; ?>">
                                    <div class="panel-heading text-center">
                                        <h5 style="margin: 8px 0; font-weight: bold;"><i class="fas fa-trophy"></i> PROMEDIO NACIONAL</h5>
                                    </div>
                                    <div class="panel-body text-center">
                                        <h1 style="margin: 10px 0; font-weight: bold; font-size: 2.5em;">
                                            <?php echo number_format($promedio_general, 2); ?>
                                        </h1>
                                        <span class="label label-<?php echo $categoria_general['color']; ?>" style="font-size: 12px; padding: 6px 12px;">
                                            <?php echo $categoria_general['nombre']; ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="panel panel-info">
                                    <div class="panel-heading text-center">
                                        <h5 style="margin: 8px 0; font-weight: bold;"><i class="fas fa-map-marked-alt"></i> REGIONALES ACTIVAS</h5>
                                    </div>
                                    <div class="panel-body text-center">
                                        <h1 style="margin: 10px 0; font-weight: bold; font-size: 2.5em;">
                                            <?php echo count($regionales_data); ?>
                                        </h1>
                                        <span class="label label-info" style="font-size: 12px; padding: 6px 12px;">
                                            Con facilitadores
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="panel panel-primary">
                                    <div class="panel-heading text-center">
                                        <h5 style="margin: 8px 0; font-weight: bold;"><i class="fas fa-users"></i> FACILITADORES</h5>
                                    </div>
                                    <div class="panel-body text-center">
                                        <h1 style="margin: 10px 0; font-weight: bold; font-size: 2.5em;">
                                            <?php echo $total_facilitadores_general; ?>
                                        </h1>
                                        <span class="label label-primary" style="font-size: 12px; padding: 6px 12px;">
                                            Total activos
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="panel panel-warning">
                                    <div class="panel-heading text-center">
                                        <h5 style="margin: 8px 0; font-weight: bold;"><i class="fas fa-file-alt"></i> REPORTES</h5>
                                    </div>
                                    <div class="panel-body text-center">
                                        <h1 style="margin: 10px 0; font-weight: bold; font-size: 2.5em;">
                                            <?php echo $total_reportes_general; ?>
                                        </h1>
                                        <span class="label label-warning" style="font-size: 12px; padding: 6px 12px;">
                                            En período
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Tabla de Regionales -->
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered table-hover">
                                <thead class="bg-primary">
                                    <tr>
                                        <th style="color: white;">#</th>
                                        <th style="color: white;">Regional</th>
                                        <th style="color: white;" class="text-center">Facilitadores</th>
                                        <th style="color: white;" class="text-center">Reportes</th>
                                        <th style="color: white;" class="text-center">Promedio Regional</th>
                                        <th style="color: white;" class="text-center">Estado</th>
                                        <th style="color: white;" class="text-center">% vs Nacional</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $contador = 1;
                                    
                                    foreach($regionales_data as $regional) {
                                        $diferencia_nacional = $promedio_general > 0 ? (($regional['promedio'] - $promedio_general) / $promedio_general) * 100 : 0;
                                        $color_diferencia = $diferencia_nacional >= 0 ? 'text-success' : 'text-danger';
                                        $icono_diferencia = $diferencia_nacional >= 0 ? '▲' : '▼';
                                        ?>
                                        <tr>
                                            <td><strong><?php echo $contador; ?></strong></td>
                                            <td>
                                                <strong><?php echo htmlspecialchars($regional['nombre']); ?></strong>
                                                <br><small style="color: #666;">ID: <?php echo $regional['id']; ?></small>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge" style="background-color: #FF1B34;">
                                                    <?php echo $regional['total_facilitadores']; ?>
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <?php echo $regional['total_reportes']; ?>
                                            </td>
                                            <td class="text-center">
                                                <span class="label label-<?php echo $regional['categoria']['color']; ?>" style="font-size: 14px; padding: 6px 12px;">
                                                    <?php echo number_format($regional['promedio'], 2); ?>
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <span class="label label-<?php echo $regional['categoria']['color']; ?>">
                                                    <?php echo $regional['categoria']['nombre']; ?>
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <span class="<?php echo $color_diferencia; ?>" style="font-weight: bold;">
                                                    <?php echo $icono_diferencia; ?> <?php echo number_format(abs($diferencia_nacional), 1); ?>%
                                                </span>
                                            </td>
                                        </tr>
                                        <?php
                                        $contador++;
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>

                        <?php
                    } else {
                        ?>
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle"></i> 
                            No se encontraron regionales con datos en el período seleccionado.
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
/* Estilos adicionales para la vista de regionales */
.custom-panel .panel-heading {
    background: linear-gradient(135deg, #FF1B34 0%, #D91628 100%) !important;
    border-color: #FF1B34 !important;
    color: white !important;
}

.custom-panel .panel-title {
    color: white !important;
    font-size: 18px;
    font-weight: 600;
}

.filter-row {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-radius: 10px;
    padding: 20px;
    margin: 0 0 20px 0;
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

.filter-input {
    border: 2px solid #e9ecef;
    border-radius: 8px;
    padding: 12px 15px;
    font-size: 14px;
    transition: all 0.3s ease;
    background-color: #fff;
}

.filter-input:focus {
    border-color: #FF1B34;
    box-shadow: 0 0 0 0.2rem rgba(255,27,52,0.25);
    background-color: #fff;
}

.filter-button {
    background: linear-gradient(45deg, #FF1B34, #D91628);
    border: none;
    border-radius: 8px;
    padding: 12px 15px;
    font-weight: bold;
    font-size: 13px;
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

.table th {
    font-weight: bold;
    font-size: 13px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.table td {
    font-size: 13px;
    vertical-align: middle;
}

.bg-primary th {
    background: linear-gradient(135deg, #FF1B34 0%, #D91628 100%) !important;
    color: #fff !important;
}

.badge {
    font-size: 12px;
    padding: 6px 10px;
    border-radius: 4px;
}

/* Paneles de resumen mejorados */
.panel-success, .panel-info, .panel-warning, .panel-danger, .panel-primary {
    border: none;
    border-radius: 8px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
    overflow: hidden;
}

.panel-success:hover, .panel-info:hover, .panel-warning:hover, 
.panel-danger:hover, .panel-primary:hover {
    transform: translateY(-3px);
    box-shadow: 0 6px 20px rgba(0,0,0,0.15);
}

.panel-success .panel-heading {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%) !important;
    color: white !important;
}

.panel-info .panel-heading {
    background: linear-gradient(135deg, #17a2b8 0%, #20c997 100%) !important;
    color: white !important;
}

.panel-warning .panel-heading {
    background: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%) !important;
    color: white !important;
}

.panel-danger .panel-heading {
    background: linear-gradient(135deg, #dc3545 0%, #e83e8c 100%) !important;
    color: white !important;
}

.panel-primary .panel-heading {
    background: linear-gradient(135deg, #007bff 0%, #6610f2 100%) !important;
    color: white !important;
}
</style>