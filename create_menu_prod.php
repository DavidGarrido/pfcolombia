<?php
// Crear menú y permisos en producción
require_once('funciones.php');

$PSN = new DBbase_Sql;

echo "=== CREANDO MENÚ PROMEDIOS POR REGIONALES EN PRODUCCIÓN ===<br><br>";

// 1. Crear el menú
$sql = "INSERT INTO menu (nombre, php, orden, opc, extra, imagen, principal, paracliente, estado, directo) 
        VALUES ('Promedios por Regionales', 'promedios_por_regionales', 3.00, 0, '', '<i class=\"fas fa-chart-area\"></i>', 7, 0, 1, 0)";

echo "1. Creando menú...<br>";
echo "SQL: " . $sql . "<br>";

try {
    $result = $PSN->query($sql);
    echo "✅ Menú creado exitosamente!<br>";
    $menuId = $PSN->ultimoId();
    echo "   Nuevo ID: " . $menuId . "<br><br>";
    
} catch (Exception $e) {
    echo "❌ Error creando menú: " . $e->getMessage() . "<br>";
    if($PSN->Error) {
        echo "   MySQL Error: " . $PSN->Error . "<br><br>";
    }
    exit;
}

// 2. Asignar permisos a coordinadores
$sql2 = "INSERT INTO usuarios_menu (idUsuario, idMenu) 
         SELECT u.id, " . $menuId . " FROM usuario u 
         WHERE u.tipo = 2";

echo "2. Asignando permisos a coordinadores...<br>";
echo "SQL: " . $sql2 . "<br>";

try {
    $result2 = $PSN->query($sql2);
    echo "✅ Permisos asignados exitosamente!<br>";
    echo "   Filas afectadas: " . $PSN->affected_rows() . "<br><br>";
    
} catch (Exception $e) {
    echo "❌ Error asignando permisos: " . $e->getMessage() . "<br>";
    if($PSN->Error) {
        echo "   MySQL Error: " . $PSN->Error . "<br><br>";
    }
}

// 3. Verificar resultado final
echo "3. Verificando resultado...<br>";
$check_sql = "SELECT id, nombre, php, imagen FROM menu WHERE php = 'promedios_por_regionales'";
$PSN->query($check_sql);
if($PSN->next_record()) {
    echo "✅ Menú verificado:<br>";
    echo "   ID: " . $PSN->f('id') . "<br>";
    echo "   Nombre: " . $PSN->f('nombre') . "<br>";
    echo "   PHP: " . $PSN->f('php') . "<br>";
    echo "   Imagen: " . htmlspecialchars($PSN->f('imagen')) . "<br><br>";
} else {
    echo "❌ Error: Menú no encontrado después de la creación<br><br>";
}

// 4. Verificar permisos
$permisos_sql = "SELECT COUNT(*) as total FROM usuarios_menu WHERE idMenu = " . $menuId;
$PSN->query($permisos_sql);
if($PSN->next_record()) {
    echo "✅ Permisos verificados: " . $PSN->f('total') . " coordinadores tienen acceso<br>";
}

echo "<br>=== PROCESO COMPLETADO ===<br>";
?>