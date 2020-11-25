#!/usr/bin/php
<?php
    //error_reporting(E_ALL);
    include_once "/data/custom/html/mattLib/Utils.php";
    if(!isPcStateOn())
    {
        if(!hasPcOnBeenTriggered())//turns off at pingAlive.php
        {
            setHasPcOffBeenTriggered(OFF_STATE);
            setHasPcOnBeenTriggered(ON_STATE);
            turnPcOn();
        }
    }
?>