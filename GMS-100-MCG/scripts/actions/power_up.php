<?php
include "/data/custom/scripts/classes/Logger.php";
include "/data/custom/html/mattLib/Utils.php";

if (file_exists("/mnt/usbflash/running")) {
    Logger::LogEvent(Logger::VM1,"<font color=red><b>[ANOMOLOUS] System shutdown detected.</b></font>", true);
    Logger::LogEvent(Logger::VM2,"<font color=red><b>[ANOMOLOUS] System shutdown detected.</b></font>", true);
    Logger::LogEvent(Logger::VM3,"<font color=red><b>[ANOMOLOUS] System shutdown detected.</b></font>", true);

    Logger::LogEvent(Logger::ALARM1,"<font color=red><b>[ANOMOLOUS] System shutdown detected.</b></font>", true);
    Logger::LogEvent(Logger::ALARM2,"<font color=red><b>[ANOMOLOUS] System shutdown detected.</b></font>", true);
    Logger::LogEvent(Logger::ALARM3,"<font color=red><b>[ANOMOLOUS] System shutdown detected.</b></font>", true);
    Logger::LogEvent(Logger::ALARM4,"<font color=red><b>[ANOMOLOUS] System shutdown detected.</b></font>", true);
} else {
    $myfile = fopen("/mnt/usbflash/running", "w");
    fclose($myfile);
}

setHasPcOffBeenTriggered(OFF_STATE);

//if(!isPcStateOn()) {
   
    
//}


?>