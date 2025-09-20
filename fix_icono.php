<?php
require_once('funciones.php');

$PSN = new DBbase_Sql;

// Actualizar el ícono del menú
$sql = "UPDATE menu SET imagen = '<i class=\"fas fa-chart-area\"></i>' WHERE id = 80";

echo "Ejecutando SQL: " . $sql . "<br><br>";

try {
    $result = $PSN->query($sql);
    echo "Ícono actualizado exitosamente!<br>";
    echo "Filas afectadas: " . $PSN->affected_rows() . "<br>";
    
    // Verificar que se actualizó
    $check_sql = "SELECT id, nombre, imagen FROM menu WHERE id = 80";
    $PSN->query($check_sql);
    if($PSN->next_record()) {
        echo "Menú ID: " . $PSN->f('id') . "<br>";
        echo "Nombre: " . $PSN->f('nombre') . "<br>";
        echo "Imagen: " . htmlspecialchars($PSN->f('imagen')) . "<br>";
    }
    
} catch (Exception $e) {
    echo "Error en UPDATE: " . $e->getMessage() . "<br>";
}

if($PSN->Error) {
    echo "Error MySQL: " . $PSN->Error . " (Errno: " . $PSN->Errno . ")<br>";
}
?>