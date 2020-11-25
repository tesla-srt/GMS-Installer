#!/usr/bin/php
<?php
    //error_reporting(E_ALL);
    //boots pc when auto boot is on, system power is on, and pc ping is dead.

    include_once "/data/custom/html/mattLib/Utils.php";
    setPcState(OFF_STATE);

    if(!isSystemRebooting())
    {
            if(getSysPowerVoltage() >= 5)
            {
                if(!hasPcOnBeenTriggered())//turns off at pingAlive.php
                {
                    if(!hasPcOffBeenTriggered()) {
                        setHasPcOnBeenTriggered(ON_STATE);
                        turnPcOn();
                    } 
                }
            }
    }

?>