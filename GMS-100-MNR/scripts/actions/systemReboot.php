#!/usr/bin/php
<?php
    //error_reporting(E_ALL);
    include "/data/custom/scripts/classes/Logger.php";
    include_once "/data/custom/html/mattLib/Utils.php";

    setIsRebooting(ON_STATE);
    
    //sleep when pc is on
    if(isPcStateOn())
    {
        turnPcOff();
        sleep(40);
    }
    setHasPcOnBeenTriggered(ON_STATE);
    
    Logger::LogEvent(Logger::VM2,"<font color=red>System reboot initiated.</font>", true);
    Logger::LogEvent(Logger::VM1,"<font color=red>System reboot initiated.</font>", true);
    Logger::LogEvent(Logger::VM3,"<font color=red>System reboot initiated.</font>", true);
    Logger::LogEvent(Logger::ALARM2,"<font color=red>System reboot initiated.</font>", true);
    Logger::LogEvent(Logger::ALARM3,"<font color=red>System reboot initiated.</font>", true);
    Logger::LogEvent(Logger::ALARM4,"<font color=red>System reboot initiated.</font>", true);
    
    cycleSystemRebootRelay();
    setIsRebooting(OFF_STATE);
    setHasPcOnBeenTriggered(OFF_STATE);
    setHasPcOffBeenTriggered(OFF_STATE);
?>