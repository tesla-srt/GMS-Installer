#!/usr/bin/php
<?php
    //error_reporting(E_ALL);

    include "/data/custom/scripts/classes/Logger.php";
    Logger::LogEvent(Logger::ALARM3);
    $clean_file   = '/tmp/surge';	// Define the folder to clean (keep trailing slashes)
    $value = trim(file_get_contents("/var/rmsdata/alarm3"));
    if (!file_exists($clean_file)) {
        $myfile = '';
        $myfile = fopen($clean_file, "w");
        fclose($myfile);
        exec("rmsalert 6.");
    } else {
        if ($value == 0) {
            unlink($clean_file);
        }
    }
?>