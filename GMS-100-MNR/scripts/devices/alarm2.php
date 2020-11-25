#!/usr/bin/php
<?php
    //Door Contact Alarm
    //error_reporting(E_ALL);
    include "/data/custom/scripts/classes/Logger.php";
    Logger::LogEvent(Logger::ALARM2);
    $clean_file   = '/tmp/door';	// Define the folder to clean (keep trailing slashes)
    $value = trim(file_get_contents("/var/rmsdata/alarm2"));
    if (!file_exists($clean_file)) {
        $myfile = '';
        $myfile = fopen($clean_file, "w");
        fclose($myfile);
        exec("rmsalert 5.");
    } else {
        if ($value == 0) {
            unlink($clean_file);
        }
    }
?>