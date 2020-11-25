<?php
//error_reporting(E_ALL);
const PC_STATE_PATH = "/var/rmsdata/pcstate";
const IS_REBOOTING_PATH = "/var/rmsdata/isrebooting";
const HAS_PC_ON_BEEN_TRIGGERED_PATH = "/var/rmsdata/hasPcOnBeenTriggered";
const HAS_PC_OFF_BEEN_TRIGGERED_PATH = "/var/rmsdata/hasPcOffBeenTriggered";
const TEMP_GRAPH_PNG = "/var/rrd/temp-hour.png";
const ON_STATE = 1;
const OFF_STATE = 0;
const CHECKED_STATE = "CHECKED";
const UNCHECKED_STATE = " ";

const SHUT_DOWN_PC_SCR_PATH = "/data/custom/scripts/actions/shutdownPc.php";
const BOOT_UP_PC_SCR_PATH = "/data/custom/scripts/actions/bootUpPc.php";
const SYSTEM_REBOOT_SCR_PATH = "/data/custom/scripts/actions/systemReboot.php";

function getFwVersion() { return file_get_contents("/data/custom/html/mattLib/version.txt"); }
function getFormattedHostname() {return formatHostname(trim(file_get_contents("/etc/hostname")));}
function getMacAddress() {return trim(file_get_contents("/var/macaddress"));}
function getIpAddress() {return $_SERVER['SERVER_ADDR'];}
function getServerDate()
{
    date_default_timezone_set('America/New_York');
    return date("c");
}

/**
 * backup ping check.  HACK fix in case ping gets stuck in off, when ping is alive.  You can just refresh to correct.
 */
function setPcStateOnWhenPingAlive()
{
    if(isPcPingAlive())
    {
        setPcState(ON_STATE);
        setHasPcOnBeenTriggered(OFF_STATE);
    }
    usleep(100);
    return isPcStateOn();
}

/**
 * Returns "11" for fail, "12" for success
 */
function syncTime()
{
    system("/etc/init.scripts/S49ntpclient sync > /dev/null");
    sleep(1);
    $sd2 = file_get_contents("/tmp/ntp.sd2");
    unlink("/tmp/ntp.sd1");
    unlink("/tmp/ntp.sd2");
    if(strlen($sd2)>1)
    {
        return "11"; //SYNC FAIL
    }
    else
    {
        $sd_seconds = strftime("%S");
        $sd_minutes = strftime("%M");
        $sd_hours = strftime("%H");
        $sd_day = strftime("%d");
        $sd_weekday = strftime("%u");
        if( $sd_weekday == 7){$sd_weekday = 0;}
        $sd_day = strftime("%d");
        $sd_month = strftime("%m");
        $sd_year = strftime("%y");

        $command = sprintf("rmsrtc writetime %s %s %s %02d %02d %02d %s",$sd_seconds,$sd_minutes,$sd_hours,$sd_day,$sd_weekday,$sd_month,$sd_year);
        system($command);
        return "12"; //SYNC SUCCESS
    }
}

function getDateTime() {return date('n.d.Y')."-".date('g:i:s A')." ";}

function login_attempt() {
    $remote = $_SERVER['REMOTE_ADDR'];

    $fh = fopen("/mnt/usbflash/log/accessLog.log", a);
    fwrite($fh, getDateTime() . " Login Successful @ " . getFormattedHostname() . " from " . $remote . "<br>\n");
    fclose($fh);
}

/**
 * Replaces dashes with spaces
 */
function formatHostname($name)
{
    $sOut = "";
    $sArr = explode("-", $name);
    foreach ($sArr as $str)
    {
        $sOut = $sOut.$str." ";
    }
    return $sOut;
}

function printCopyright()
{
    $year = date("Y");
    if($year < "2019")
    {
        $year = "2020";
    }
    echo "<small>®</small>".$year." Micro Enterprises";
}

function getLocation() {
    $location ="";
    $dbh = new PDO('sqlite:/etc/rms100.db');
    $result  = $dbh->query("SELECT syslocation FROM snmp_config;");
    foreach($result as $row)
    {
        $location = $row['syslocation'];
    }
    return $location;
}

//custom vars stored in ram
function isPcStateOn()
{
    if(!file_exists(PC_STATE_PATH))
    {
        setPcState(ON_STATE);
    }
    return trim(file_get_contents(PC_STATE_PATH));
}

function isSystemRebooting()
{
    if(!file_exists(IS_REBOOTING_PATH))
    {
        setIsRebooting(OFF_STATE);
    }
    return trim(file_get_contents(IS_REBOOTING_PATH));
}
function hasPcOnBeenTriggered()
{
    if(!file_exists(HAS_PC_ON_BEEN_TRIGGERED_PATH))
    {
        setHasPcOnBeenTriggered(OFF_STATE);
    }
    return trim(file_get_contents(HAS_PC_ON_BEEN_TRIGGERED_PATH));
}

function hasPcOffBeenTriggered()
{
    if(!file_exists(HAS_PC_OFF_BEEN_TRIGGERED_PATH))
    {
        setHasPcOffBeenTriggered(OFF_STATE);
    }
    return trim(file_get_contents(HAS_PC_OFF_BEEN_TRIGGERED_PATH));
}

function setPcState($val){ setFile($val,PC_STATE_PATH); }
function setIsRebooting($val){ setFile($val,IS_REBOOTING_PATH); }
function setHasPcOnBeenTriggered($val){setFile($val, HAS_PC_ON_BEEN_TRIGGERED_PATH);}
function setHasPcOffBeenTriggered($val){setFile($val, HAS_PC_OFF_BEEN_TRIGGERED_PATH);}

function setFile($val, $file)
{
    $fh = fopen($file, "w");
    fwrite($fh, $val);
    fclose($fh);
}


//built in vars
/**
 * This only works when there is only 1 active ping target.  It will just
 * search for the next possible "alive" state.
 */
function isPcPingAlive()
{
    $pingMsg = file_get_contents("/tmp/ping.sd");
    $len = strlen($pingMsg);

    for($i = 0; $i < $len-1; $i++)
    {
        if($pingMsg[$i] == "a" && $pingMsg[$i+1] == "l")
        {
            return true;
        }
    }
    return false;
}

function getSysPowerVoltage(){return trim(file_get_contents("/var/rmsdata/vm1"));}
function getBatteryVoltage(){return trim(file_get_contents("/var/rmsdata/vm2"));}
function getPoeVoltage(){return trim(file_get_contents("/var/rmsdata/vm3"));}
function getTemp(){return trim(file_get_contents("/var/rmsdata/tempf"));}
function isFanOff() {return trim(file_get_contents("/var/rmsdata/relay2"));}
function setFanOn() {exec("rmsrelay 2 on");}
function setFanOff() {exec("rmsrelay 2 off");}
function turnPcOn() {exec("rmsscript 6.");}
function turnPcOff() {runScriptInBackground(SHUT_DOWN_PC_SCR_PATH);}
function cycleSystemRebootRelay() {exec("rmsscript 7."); unlink("/mnt/usbflash/running");}

//scripts run in background
/**
 * Cycles GPIO 1 To High, then Low, w/ a 5 second delay inbetween.  The current functionality
 * of the GPIO will also do a hard shut down when the PC is currently on.  Could be fixed in future
 * hardware configurations.
 */
function turnPcOnInBackground() {runScriptInBackground(BOOT_UP_PC_SCR_PATH);}
function systemRebootInBackground() {runScriptInBackground(SYSTEM_REBOOT_SCR_PATH);}

function runScriptInBackground($scriptPath)
{
    system($scriptPath. " > /tmp/cli.txt &");
    unlink("/tmp/cli.txt");
}

//vars pulled from database Query
/**
 * Gets the IP of the Ping Target, w/ ID 1.  This should be set to
 * the connected pc's IP.
 */
function getPcIp($dbh = null)
{
    if($dbh === null)
    {
        $dbh = new PDO('sqlite:/etc/rms100.db');
    }
    $query = sprintf("SELECT * FROM ping_targets WHERE id=1");
    $result  = $dbh->query($query);
    foreach($result as $row)
    {
        $ip = $row['ip'];
    }

    if($ip == null)
    {
        return 0;
    }
    return $ip;
}
function isAutoShutdownOn($dbh = null)
{
    if($dbh === null)
    {
        $dbh = new PDO('sqlite:/etc/rms100.db');
    }
    $query = sprintf("SELECT * FROM voltmeters WHERE id='2';");
    $result  = $dbh->query($query);
    foreach($result as $row)
    {
        $autoShutdownState = $row['l_en'];
    }
    return $autoShutdownState;
}

function isDoorAlertOn($dbh = null)
{
    $doorAlertState = '1';
    if($dbh === null)
    {
        $dbh = new PDO('sqlite:/etc/rms100.db');
    }
    $query = sprintf("SELECT * FROM io WHERE id='2' AND type='alarm';");
    $result  = $dbh->query($query);
    foreach($result as $row)
    {
        $doorAlertState = $row['HI_alert_cmds'];
    }
    if($doorAlertState == '5.') {
        $doorAlertState = true;
    } else {
        $doorAlertState = false;
    }
    return $doorAlertState;
}

function isSurgeAlertOn($dbh = null)
{
    $surgeAlertState = '1';
    if($dbh === null)
    {
        $dbh = new PDO('sqlite:/etc/rms100.db');
    }
    $query = sprintf("SELECT * FROM io WHERE id='3' AND type='alarm';");
    $result  = $dbh->query($query);
    foreach($result as $row)
    {
        $surgeAlertState = $row['HI_alert_cmds'];
    }
    if($surgeAlertState == '6.') {
        $surgeAlertState = true;
    } else {
        $surgeAlertState = false;
    }
    return $surgeAlertState;
}

function isSwitchAlertOn($dbh = null)
{
    $switchAlertState = '1';
    if($dbh === null)
    {
        $dbh = new PDO('sqlite:/etc/rms100.db');
    }
    $query = sprintf("SELECT * FROM io WHERE id='4' AND type='alarm';");
    $result  = $dbh->query($query);
    foreach($result as $row)
    {
        $switchAlertState = $row['LO_alert_cmds'];
    }
    if($switchAlertState == '7.') {
        $switchAlertState = true;
    } else {
        $switchAlertState = false;
    }
    return $switchAlertState;
}

function isPowerAlertOn($dbh = null)
{
    $powerAlertState = '1';
    if($dbh === null)
    {
        $dbh = new PDO('sqlite:/etc/rms100.db');
    }
    $query = sprintf("SELECT * FROM voltmeters WHERE id='1';");
    $result  = $dbh->query($query);
    foreach($result as $row)
    {
        $powerAlertState = $row['LO_alert_cmds'];
    }
    if($powerAlertState == '8.') {
        $powerAlertState = true;
    } else {
        $powerAlertState = false;
    }
    return $powerAlertState;
}

function isTamperAlertOn($dbh = null)
{
    $tamperAlertState = '1';
    if($dbh === null)
    {
        $dbh = new PDO('sqlite:/etc/rms100.db');
    }
    $query = sprintf("SELECT * FROM io WHERE id='1' AND type='alarm';");
    $result  = $dbh->query($query);
    foreach($result as $row)
    {
        $tamperAlertState = $row['HI_alert_cmds'];
    }
    if($tamperAlertState == '4.') {
        $tamperAlertState = true;
    } else {
        $tamperAlertState = false;
    }
    return $tamperAlertState;
}

function isTempAlertOn($dbh = null) {
    $tempAlertState = '1';
    if ($dbh == null) {
        $dbh = new PDO('sqlite:/etc/rms100.db');
    }
    $query = sprintf("SELECT * FROM custom;");
    $result  = $dbh->query($query);
    foreach($result as $row)
    {
        $tempAlertState = $row['tempAlertActive'];
    }

    return $tempAlertState;
}

function getPowerDownThreshold($dbh = null)
{
    if($dbh === null)
    {
        $dbh = new PDO('sqlite:/etc/rms100.db');
    }
    $query = sprintf("SELECT * FROM voltmeters WHERE id='2';");
    $result  = $dbh->query($query);
    foreach($result as $row)
    {
        $powerDownThreshold = $row['lo_t'];
    }
    return $powerDownThreshold;
}

/**
 * Used to reset voltmeter triggers after changes had been made.
 */
function restartSomeServices()
{
    //RMSD
    if(file_exists("/var/run/rmsd.pid"))
    {
        system("kill -HUP `cat /var/run/rmsd.pid`");
    }

    // //RMSpingD
    // if(file_exists("/var/run/rmspingd.pid"))
    // {
    // 	system("kill -HUP `cat /var/run/rmspingd.pid`");
    // }

    //RMSvmD (USB iso voltmeter board)
    if(file_exists("/var/run/rmsvmd.pid"))
    {
        system("kill -HUP `cat /var/run/rmsvmd.pid`");
    }
}

function isFirstRun() {
    return file_exists("/data/custom/html/first_run");
}

?>