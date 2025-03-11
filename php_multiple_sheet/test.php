<?php
require 'php_multiple.class.php';

$Xls = new Excel ( 'Sheet'); // constructor parameter name to the first sheet
$Xls-> worksheets [ 'Sheet'] -> addRow (array ( "1", "2", "3")); // add a row, data is 1,2,3
 $Xls-> addsheet ( 'Test'); // Create a sheet, the sheet name parameter
 $Xls-> worksheets [ 'Test'] -> addRow (array ( "3", "2", "3")); // add a row to a second sheet
 $Xls-> generate ( 'my-test'); // download excel, parameter is the file name
?>