#!/usr/bin/php
<?php
    //error_reporting(E_ALL);
    //Battery Power
    include "/data/custom/scripts/classes/Logger.php";
    include_once "/data/custom/html/mattLib/DatabaseHandler.php";

    Logger::LogEvent(Logger::VM2);
    $value = trim(file_get_contents("/var/rmsdata/vm2"));

    if($value < $v2LoT) {
        exec("rmsalert 9.");
    }
?>