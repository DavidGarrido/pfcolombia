<?php
// Test insert para menú
require_once('funciones.php');

$PSN = new DBbase_Sql;

// Intentar insertar el menú
$sql = "INSERT INTO menu (nombre, php, orden, opc, extra, imagen, principal, paracliente, estado, directo) 
        VALUES ('Promedios por Regionales', 'promedios_por_regionales', 3.00, 0, '', 'fas fa-chart-area', 7, 0, 1, 0)";

echo "Ejecutando SQL: " . $sql . "<br><br>";

try {
    $result = $PSN->query($sql);
    echo "INSERT exitoso!<br>";
    echo "Nuevo ID: " . $PSN->ultimoId() . "<br>";
    
    // Verificar que se insertó
    $check_sql = "SELECT id, nombre, php FROM menu WHERE php = 'promedios_por_regionales'";
    $PSN->query($check_sql);
    if($PSN->next_record()) {
        echo "Menú encontrado - ID: " . $PSN->f('id') . ", Nombre: " . $PSN->f('nombre') . "<br>";
    } else {
        echo "ERROR: Menú no encontrado después del INSERT<br>";
    }
    
} catch (Exception $e) {
    echo "Error en INSERT: " . $e->getMessage() . "<br>";
}

// Mostrar error de MySQL si existe
if($PSN->Error) {
    echo "Error MySQL: " . $PSN->Error . " (Errno: " . $PSN->Errno . ")<br>";
}
?>