<?php
	header("Content-Type: application/xls");    
	header("Content-Disposition: attachment; filename=documento_exportado_" . date('Y:m:d:m:s').".xls");
	header("Pragma: no-cache"); 
	header("Expires: 0");

	require_once 'conn.php';
	
	$output = "";
	
	if(ISSET($_POST['export'])){
		$output .="
			<table>
				<thead>
					<tr>
						<th>Nombre</th>
						<th>Tarjeta</th>
						<th>Regional</th>
						<th>Ciudad</th>
						<th>Fecha</th>
					</tr>
				<tbody>
		";
		
		$query = mysqli_query($conn, "SELECT * FROM `LPP`") or die(mysqli_errno());
		while($fetch = mysqli_fetch_array($query)){
			
		$output .= "
					<tr>
						<td>".$fetch['Nombre']."</td>
						<td>".$fetch['Tarjeta']."</td>
						<td>".$fetch['Regional']."</td>
						<td>".$fetch['Ciudad']."</td>
						<td>".$fetch['Fecha']."</td>
					</tr>
		";
		}
		
		$output .="
				</tbody>
				
			</table>
		";
		
		echo $output;
	}
	
?>