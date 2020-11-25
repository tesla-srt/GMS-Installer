<?php
// output headers so that the file is downloaded rather than displayed
//header("Content-type: text/csv");
//header("Content-disposition: attachment; filename = temperature_report.csv");
//readfile("/mnt/usbflash/log/temperature_report.csv");
$tempCsvData = base64_encode(file_get_contents("/mnt/usbflash/log/temperature_report.csv"));
$vmCsvData = base64_encode(file_get_contents("/mnt/usbflash/log/vm_report.csv"));
    
$csvArray = array (
    'temp' => $tempCsvData,
    'vm' => $vmCsvData
 );

print(json_encode($csvArray));
    
?>