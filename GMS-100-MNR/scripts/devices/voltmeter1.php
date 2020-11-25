<?php
    //error_reporting(E_ALL);
    //System Power
    include "/data/custom/scripts/classes/Logger.php";
    include_once "/data/custom/html/mattLib/DatabaseHandler.php";

    Logger::LogEvent(Logger::VM1);
    $value = trim(file_get_contents("/var/rmsdata/vm1"));

    if($value < $v1LoT) {
        exec("rmsalert 8.");
    }
?>