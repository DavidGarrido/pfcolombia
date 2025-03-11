<?php
    session_start(); // Inicia la sesión (debe estar presente en todos los archivos que usen sesiones)
    require_once 'conn.php';
    
    // Verifica si el usuario está autenticado
    if(isset($_SESSION['nombredelusuario'])){
        // Si el usuario está autenticado, verifica si se ha enviado el formulario de exportación
        if(isset($_POST['export'])){
            // Si se ha enviado el formulario de exportación, genera el archivo Excel
            $output = "<table><thead><tr><th>Nombre</th><th>Tarjeta</th><th>Regional</th><th>Ciudad</th><th>Fecha</th></tr></thead><tbody>";
            
            $query = mysqli_query($conn, "SELECT * FROM `LPP`") or die(mysqli_error($conn));
            while($fetch = mysqli_fetch_array($query)){
                $output .= "<tr><td>".$fetch['Nombre']."</td><td>".$fetch['Tarjeta']."</td><td>".$fetch['Regional']."</td><td>".$fetch['Ciudad']."</td><td>".$fetch['Fecha']."</td></tr>";
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
        // Redirige al usuario a la página de inicio de sesión si no está autenticado
        header("Location: login_registrar.php");
        exit();
    }
?>
