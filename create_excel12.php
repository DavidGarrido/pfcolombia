<?php
session_start(); // Inicia la sesión (debe estar presente en todos los archivos que usen sesiones)
require_once 'conn.php'; // Importa la conexión a la base de datos

// Verifica si el usuario está autenticado
if(isset($_SESSION['nombredelusuario'])){
    // Si el usuario está autenticado, verifica si se ha enviado el formulario de exportación
    if(isset($_POST['export'])){
        // Si se ha enviado el formulario de exportación, genera el archivo Excel
        $output = "<table><thead><tr><th>Nombre</th><th>Tarjeta</th><th>Fecha</th><th>Ciudad</th></tr></thead><tbody>";
        
        // Construye la consulta SQL
        $query = "SELECT t.`adj_nom`, t.`adj_url`, t.`adj_fec`, s.`municipio` AS ciudad
                    FROM `tbl_adjuntos` AS t 
                    INNER JOIN `sat_reportes` AS r ON t.adj_rep_fk = r.id
                    INNER JOIN `dane_municipios` AS s ON r.ciudad = s.id_municipio
                    WHERE t.`adj_fec` >= '2024-01-01' AND t.`adj_fec` <= CURDATE()";

        // Ejecuta la consulta SQL
        $result = mysqli_query($conn, $query) or die(mysqli_error($conn));
        
        // Itera sobre los resultados obtenidos de la consulta
        while($fetch = mysqli_fetch_array($result)){
            $output .= "<tr><td>".$fetch['adj_nom']."</td><td>".$fetch['adj_url']."</td><td>".$fetch['adj_fec']."</td><td>".$fetch['ciudad']."</td></tr>";
        }
        
        $output .= "</tbody></table>";
        
        // Envía los encabezados para descargar el archivo
        header("Content-Type: application/xls");    
        header("Content-Disposition: attachment; filename=documento_exportado_" . date('Y-m-d H:i:s') . ".xls");
        header("Pragma: no-cache"); 
        header("Expires: 0");

        // Envía el contenido como un archivo descargable
        echo $output;
        exit(); // Esto evita cualquier salida adicional que pueda romper la descarga del archivo
   }
} else {
    // Si el usuario no está autenticado, redirige a la página de inicio de sesión
    header("Location: inicio.html");
    exit();
}
?>
