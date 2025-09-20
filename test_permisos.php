<?php
// Test permisos para coordinadores
require_once('funciones.php');

$PSN = new DBbase_Sql;

// Insertar permisos para coordinadores (tipo 2)
$sql = "INSERT INTO usuarios_menu (idUsuario, idMenu) 
        SELECT u.id, 80 FROM usuario u 
        WHERE u.tipo = 2";

echo "Ejecutando SQL: " . $sql . "<br><br>";

try {
    $result = $PSN->query($sql);
    echo "Permisos insertados exitosamente!<br>";
    echo "Filas afectadas: " . $PSN->affected_rows() . "<br>";
    
    // Verificar permisos insertados
    $check_sql = "SELECT COUNT(*) as total FROM usuarios_menu WHERE idMenu = 80";
    $PSN->query($check_sql);
    if($PSN->next_record()) {
        echo "Total de usuarios con acceso al menú 80: " . $PSN->f('total') . "<br>";
    }
    
} catch (Exception $e) {
    echo "Error en INSERT: " . $e->getMessage() . "<br>";
}

// Mostrar error de MySQL si existe
if($PSN->Error) {
    echo "Error MySQL: " . $PSN->Error . " (Errno: " . $PSN->Errno . ")<br>";
}
?>