#!/usr/bin/php
<?php
    //error_reporting(E_ALL);
    include_once "/data/custom/html/mattLib/Utils.php";
    $ip = getPcIp();
    echo "<script> alert('". $ip ."');</script>";
    exec("echo 'COMMAND SHUTDOWN PC' | nc -w 1 -u ".$ip." 8080");
?>