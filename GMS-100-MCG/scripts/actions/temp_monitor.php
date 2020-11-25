<?php
//error_reporting(E_ALL);
include_once "/data/custom/html/mattLib/Utils.php";

$currTemp = floatval(getTemp());
$tempAlertState = '0';
$tempAlertLoTrigger = "0";
$tempAlertFlap = '0';
$tempAlertHiTrigger = "0";

$dbh = new PDO('sqlite:/etc/rms100.db');
$result = $dbh->query("SELECT * FROM custom");
foreach($result as $row)
{
    $tempAlertLoTrigger = floatval($row['tempAlertLoTrigger']);
}
$result = $dbh->query("SELECT * FROM custom");

foreach($result as $row)
{
    $tempAlertHiTrigger = floatval($row['tempAlertHiTrigger']);
    $tempAlertState = $row['tempAlertActive'];
    $tempAlertFlap = floatval($row['tempAlertFlap']);
    $lastTempAlertTime = floatval($row['LastAlertTime']);
}

$time_delta = time() - $lastTempAlertTime;

if ($tempAlertState == TRUE) {
//    $time_delta = 0;
    if ($time_delta >= $tempAlertFlap || $time_delta  < 1) {
        if ($currTemp <= $tempAlertLoTrigger) {
            $query = sprintf("UPDATE custom SET LastAlertTime='%s';", time());
            $result  = $dbh->exec($query);
            $query = sprintf("UPDATE alerts SET v4='%s' WHERE id='12';", "Temperature: " . getTemp());
            $result  = $dbh->exec($query);
            exec("rmsalert 12.");
        } else if ($currTemp >= $tempAlertHiTrigger) {
            $query = sprintf("UPDATE custom SET LastAlertTime='%s';", time());
            $result  = $dbh->exec($query);
            $query = sprintf("UPDATE alerts SET v4='%s' WHERE id='13';", "Temperature: " . getTemp());
            $result  = $dbh->exec($query);
            exec("rmsalert 13.");
        }
    }
}   
?>