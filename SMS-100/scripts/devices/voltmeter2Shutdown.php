#!/usr/bin/php
<?php
    //error_reporting(E_ALL);
    include "/data/custom/scripts/classes/Logger.php";
    include_once "/data/custom/html/mattLib/Utils.php";

    if(isAutoShutdownOn())
    {
        Logger::LogEvent(Logger::VM1,"<font color=red>[LOW] PC shutdown initiated.</font>", true);
        turnPcOff();
    }
    else 
    {
        Logger::LogEvent(Logger::VM1);
    }
?>