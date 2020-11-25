#!/usr/bin/php
<?php
//PoE Switch Power
    //error_reporting(E_ALL);
    include "/data/custom/scripts/classes/Logger.php";
    include_once "/data/custom/html/mattLib/DatabaseHandler.php";

    Logger::LogEvent(Logger::VM3);
    $value = trim(file_get_contents("/var/rmsdata/vm3"));

    if($value < $v3LoT) {
      exec("rmsalert 10.");
    }
?>