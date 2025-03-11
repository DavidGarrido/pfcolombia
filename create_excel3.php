<?php
      error_reporting(E_ALL);
      ini_set('display_errors', 1);

    require_once 'conn.php';
    
    if(isset($_POST['export'])){
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
?>
