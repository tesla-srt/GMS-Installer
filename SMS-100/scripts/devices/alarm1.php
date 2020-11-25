#!/usr/bin/php
<?php
//Tamper Alert
include "/data/custom/scripts/classes/Logger.php";
Logger::LogEvent(Logger::ALARM1);


    
$clean_file   = '/tmp/tamper';	// Define the folder to clean (keep trailing slashes)
$expire_time    = 60; 						// Here you can define after how many minutes the files should get deleted
$time_now = time();
$myfile = '';


if (!file_exists($clean_file)) {
    $myfile = fopen($clean_file, "w");
    fclose($myfile);
} else {
    $FileCreationTime = filectime($myFile);
    $FileAge = $time_now - $FileCreationTime;
    if ($FileAge > $expire_time) {
        exec("/bin/rmsalert 4.");
        unlink($clean_file);
    } else {
        unlink($clean_file);
    }
}


     /**
     
     
     IF tmp/tamper.a1 does not exist
        create tamper.a1
    ELSE
    if tamper.a1 is older than 30sec
        rmsAlert()
    else 
         delete file.
     
     
     
     */
    
?>