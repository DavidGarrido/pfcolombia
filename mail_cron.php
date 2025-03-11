<?php
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://icarsoluciones.app/mail_processqueue.php"); //sfdsfdfsdf
curl_setopt($ch, CURLOPT_HEADER, 0);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$output = curl_exec($ch);
curl_close($ch);
echo $output;
?>