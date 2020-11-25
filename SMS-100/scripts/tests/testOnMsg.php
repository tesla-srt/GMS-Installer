#!/usr/bin/php
<?php
    include_once "/data/custom/html/mattLib/Utils.php";
    $ip = getPcIp();
    exec("echo 'test ON' | nc -w 1 -u ".$ip." 8080");
?>