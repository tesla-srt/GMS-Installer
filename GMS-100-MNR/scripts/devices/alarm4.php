#!/usr/bin/php
<?php
    //error_reporting(E_ALL);
    //PoE Switch
        //** MCG: a HIGH STATE is NORMAL when no fault is detected on the switch
        //** MNR: Revise solution so logic is reversed
        //** MNR switch has no fault functionality. Encode for static state */
// */


    include "/data/custom/scripts/classes/Logger.php";
    Logger::LogEvent(Logger::ALARM4);
    $clean_file   = '/tmp/poe';	// Define the folder to clean (keep trailing slashes)
    $value = trim(file_get_contents("/var/rmsdata/alarm4"));
    if (!file_exists($clean_file)) {
        $myfile = '';
        $myfile = fopen($clean_file, "w");
        fclose($myfile);
        if ($value == 0) {
         exec("rmsalert 7.");
        }
    }
    if ($value == 1) {
        unlink($clean_file);
    }
?>