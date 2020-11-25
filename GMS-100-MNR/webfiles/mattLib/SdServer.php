<?php
//Contains functions that allows javascript to access data below the html layer.  Can be used with ajax() or getJson() through jquery.
//error_reporting(E_ALL);
include_once "Utils.php";

if (substr_count($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip')) ob_start("ob_gzhandler");
else ob_start();
$db_info = array(
	'vm_mjumper' => array(), 'vm_per' => array(), 'vm_mode' => array(), 'vm_shunta' => array(), 'vm_shuntmv' => array(),
	'relay_no_label' => array(), 'relay_nc_label' => array(), 'relayconf', 'hi_icon1', 'lo_icon1', 'hi_icon2', 'lo_icon2', 'hi_icon3', 'lo_icon3',
	'hi_icon4', 'lo_icon4', 'hi_icon5', 'lo_icon5', 'watt_mode_enabled' => array(), 'wattmode_vm_base' => array(),
	'a1hi', 'a1lo', 'a2hi', 'a2lo', 'a3hi', 'a3lo', 'a4hi', 'a4lo', 'a5hi', 'a5lo', 'r1nc_color', 'r1no_color', 'r2nc_color', 'r2no_color'
);
$db_info['vm_mjumper'][] = "";
$db_info['vm_per'][] = "";
$db_info['vm_mode'][] = "";
$db_info['vm_shunta'][] = "";
$db_info['vm_shuntmv'][] = "";
$db_info['relay_no_label'][] = "";
$db_info['relay_nc_label'][] = "";
$db_info['watt_mode_enabled'][] = "";
$db_info['wattmode_vm_base'][] = "";

$fh = fopen("/var/rmsdata/settings/db_info", 'r');
for ($i = 1; $i < 4; $i++) {
	$db_info['vm_mjumper'][] = trim(fgets($fh));
	$db_info['vm_per'][] = trim(fgets($fh));
	$db_info['vm_mode'][] = trim(fgets($fh));
	$db_info['vm_shunta'][] = trim(fgets($fh));
	$db_info['vm_shuntmv'][] = trim(fgets($fh));
}
for ($i = 1; $i < 3; $i++) {
	$db_info['relay_no_label'][] = trim(fgets($fh));
	$db_info['relay_nc_label'][] = trim(fgets($fh));
}
$db_info['relayconf'] = trim(fgets($fh));

$db_info['hi_icon1'] = trim(fgets($fh));
$db_info['lo_icon1'] = trim(fgets($fh));
$db_info['hi_icon2'] = trim(fgets($fh));
$db_info['lo_icon2'] = trim(fgets($fh));
$db_info['hi_icon3'] = trim(fgets($fh));
$db_info['lo_icon3'] = trim(fgets($fh));
$db_info['hi_icon4'] = trim(fgets($fh));
$db_info['lo_icon4'] = trim(fgets($fh));
$db_info['hi_icon5'] = trim(fgets($fh));
$db_info['lo_icon5'] = trim(fgets($fh));

//Skip 14 names
for ($i = 1; $i < 15; $i++) {
	$un_needed_name_field = trim(fgets($fh));
}

for ($i = 1; $i < 4; $i++) {
	$db_info['watt_mode_enabled'][] = trim(fgets($fh));
	$db_info['wattmode_vm_base'][] = trim(fgets($fh));
}

$db_info['a1hi'] = trim(fgets($fh));
$db_info['a1lo'] = trim(fgets($fh));
$db_info['a2hi'] = trim(fgets($fh));
$db_info['a2lo'] = trim(fgets($fh));
$db_info['a3hi'] = trim(fgets($fh));
$db_info['a3lo'] = trim(fgets($fh));
$db_info['a4hi'] = trim(fgets($fh));
$db_info['a4lo'] = trim(fgets($fh));
$db_info['a5hi'] = trim(fgets($fh));
$db_info['a5lo'] = trim(fgets($fh));

$db_info['r1nc_color'] = trim(fgets($fh));
$db_info['r1no_color'] = trim(fgets($fh));
$db_info['r2nc_color'] = trim(fgets($fh));
$db_info['r2no_color'] = trim(fgets($fh));

fclose($fh);

$rms_info = array(
	'vm_raw' => array(), 'relay_state' => array(), 'alarm_state' => array(), 'gpio_state' => array(),
	'button_state', 'tempc', 'tempf', 'temps', 'thedate', 'thetime', 'uptime', 'diskinfo', 'meminfo',
	'amp_hour' => array(), 'watt_hour' => array(), 'gpio_dir' => array()
);
$rms_info['vm_raw'][] = "";
$rms_info['relay_state'][] = "";
$rms_info['alarm_state'][] = "";
$rms_info['gpio_state'][] = "";
$rms_info['button_state'][] = "";
$rms_info['amp_hour'][] = "";
$rms_info['watt_hour'][] = "";
$rms_info['gpio_dir'][] = "";

$fh = fopen("/var/rmsdata/settings/rms_info", 'r');
for ($i = 1; $i < 4; $i++) {
	$rms_info['vm_raw'][] = trim(fgets($fh));
}
for ($i = 1; $i < 3; $i++) {
	$rms_info['relay_state'][] = trim(fgets($fh));
}
for ($i = 1; $i < 6; $i++) {
	$rms_info['alarm_state'][] = trim(fgets($fh));
}
for ($i = 1; $i < 5; $i++) {
	$rms_info['gpio_state'][] = trim(fgets($fh));
}
$rms_info['button_state'] = trim(fgets($fh));
$rms_info['tempc'] = trim(fgets($fh));
$rms_info['tempf'] = trim(fgets($fh));
for ($i = 1; $i < 4; $i++) {
	$rms_info['amp_hour'][] = trim(fgets($fh));
}
for ($i = 1; $i < 4; $i++) {
	$rms_info['watt_hour'][] = trim(fgets($fh));
}
for ($i = 1; $i < 5; $i++) {
	$rms_info['gpio_dir'][] = trim(fgets($fh));
}
fclose($fh);

$rms_info['temps'] = $rms_info['tempc'] . " C " . $rms_info['tempf'] . " F";
date_default_timezone_set('America/New_York');
$rms_info['thedate'] = date('M-d-Y');
$rms_info['thetime'] = date('h:i:s A');

$fh = fopen("/proc/uptime", 'r');
$uptime_line = fgets($fh);
fclose($fh);
$uptime = explode(' ', $uptime_line);
$rms_info['uptime'] = sprintf('%5.2f Days', ($uptime[0] / 60 / 60 / 24));

$rms_info['diskinfo'] = sprintf('%3.0f', (100 - (disk_free_space("/") / 1024 / 1024)));

$fh = fopen("/proc/meminfo", 'r');


$memtotal = ereg_replace("[^0-9]", "", fgets($fh));
$memfree = ereg_replace("[^0-9]", "", fgets($fh));
fclose($fh);


$rms_info['meminfo'] = sprintf('%3.0f', (($memtotal - $memfree) / $memtotal * 100));

if ($_GET["element"] == "array_dump") {
	print "<pre>";
	print "db_info:\n";
	print_r($db_info);
	print "\nrms_info:\n";
	print_r($rms_info);
	print "</pre>";
}

$callback = null;
if (isset($_GET["callback"]) && !empty($_GET["callback"])) {
	$callback = $_GET["callback"];
}

$element = (isset($_GET["element"]) ? $_GET["element"] : "");
$filename = (isset($_GET["filename"]) ? $_GET["filename"] : "");
$ans = null;
switch ($element) {
	case "vmall":
		$ans = vmall();
		break;
	case "vmallraw":
		$ans = vmallraw();
		break;
	case "homeall":
		$ans = homeall();
		break;
	case 'get_v1Log':
		$ans = get_v1Log();
		break;
	case 'get_v2Log':
		$ans = get_v2Log();
		break;
	case 'get_v3Log':
		$ans = get_v3Log();
		break;
	case 'get_a1Log':
		$ans = get_a1Log();
		break;
	case 'get_a2Log':
		$ans = get_a2Log();
		break;
	case 'get_a3Log':
		$ans = get_a3Log();
		break;
	case 'get_a4Log':
		$ans = get_a4Log();
		break;
    case 'get_accessLog':
        $ans = get_accessLog();
        break;
	case "pcBooter":
		$ans = get_gpio(1);
		break;
    case "get_tempLog":
        $ans = get_tempfLog();
        break;
}

if ($callback != null && strlen($callback) < 51) {
	$ans = $callback . "(" . $ans . ");";
}

//  send it back to the caller
print ($ans);


// ------------------------------------------------------------------------------------------
function  vmall()
{
	$vm1=get_vm(1);
	$vm2=get_vm(2);
	$vm3=get_vm(3);
	$data = array('vm1'=>$vm1, 'vm2'=>$vm2,'vm3'=>$vm3);
	$sd_string = json_encode($data);
	return $sd_string;	
}

function vmallraw()
{
	$vm1=getSysPowerVoltage();
	$vm2=getBatteryVoltage();
	$vm3=getPoeVoltage();
	$data = array('vm1'=>$vm1,'vm2'=>$vm2,'vm3'=>$vm3);
	$sd_string = json_encode($data);
	return $sd_string;
}

function  homeall()
{
	global $db_info;
	$vm1=get_vm(1);
	$vm2=get_vm(2);
	$vm3=get_vm(3);

	$tempf=get_tempf();
	$tempf=$tempf . '°F';

	$theuptime=get_uptime();
	$meminfo=get_meminfo();
	$diskinfo=get_diskinfo();

	$thealarm1=get_alarm(1);
	$thealarm2=get_alarm(2);
	$thealarm3=get_alarm(3);
	$thealarm4=get_alarm(4);

	$therelay1=get_relay(1);
	$therelay2=get_relay(2);
	$therelay1NO=get_relayNO(1);
	$therelay1NC=get_relayNC(1);
	$therelay2NO=get_relayNO(2);
	$therelay2NC=get_relayNC(2);

	//Alarm state names
	$a1hi=$db_info['a1hi'];
	$a1lo=$db_info['a1lo'];
	$a2hi=$db_info['a2hi'];
	$a2lo=$db_info['a2lo'];
	$a3hi=$db_info['a3hi'];
	$a3lo=$db_info['a3lo'];
	$a4hi=$db_info['a4hi'];
	$a4lo=$db_info['a4lo'];

	$pcstate = isPcStateOn();
	$isrebooting = isSystemRebooting();
	$hasPcOnBeenTriggered = hasPcOnBeenTriggered();
	$hasPcOffBeenTriggered = hasPcOffBeenTriggered();
    $pingstate = isPcPingAlive(); 
	$data = array(
		'vm1'=>$vm1, 
		'vm2'=>$vm2,
		'vm3'=>$vm3,
		'tempf'=>$tempf,
		'uptime'=>$theuptime,
		'meminfo'=>$meminfo,
		'diskinfo'=>$diskinfo,
		//alarm states
		'a1'=>$thealarm1,
		'a2'=>$thealarm2,
		'a3'=>$thealarm3,
		'a4'=>$thealarm4,
		//relay states
		'r1'=>$therelay1,
		'r2'=>$therelay2,
		//relay names
		'r1NO'=>$therelay1NO,
		'r1NC'=>$therelay1NC,
		'r2NO'=>$therelay2NO,
		'r2NC'=>$therelay2NC,
		//alarm names
		'a1hi'=>$a1hi,
		'a1lo'=>$a1lo,
		'a2hi'=>$a2hi,
		'a2lo'=>$a2lo,
		'a3hi'=>$a3hi,
		'a3lo'=>$a3lo,
		'a4hi'=>$a4hi,
		'a4lo'=>$a4lo,
		//created vars
		'pcstate'=>$pcstate,
        'pingstate' => $pingstate,
		'isrebooting'=>$isrebooting,
		'hasPcOnBeenTriggered'=>$hasPcOnBeenTriggered,
		'hasPcOffBeenTriggered'=>$hasPcOffBeenTriggered
	);
	$sd_string = json_encode($data);
	return $sd_string;
}

function get_v1Log()
{
	$v1Log = file_get_contents(get_path("vm1"));
	$v1Log = convertFalse2Empty($v1Log);
	return json_encode($v1Log);
}
function get_v2Log()
{
	$v2Log = file_get_contents(get_path("vm2"));
	$v2Log = convertFalse2Empty($v2Log);
	return json_encode($v2Log);
}

function get_v3Log()
{
	$v3Log = file_get_contents(get_path("vm3"));
	$v3Log = convertFalse2Empty($v3Log);
	return json_encode($v3Log);
}

function get_a1Log()
{
	$a1Log = file_get_contents(get_path("alarm1"));
	$a1Log = convertFalse2Empty($a1Log);
	return json_encode($a1Log);
}
function get_a2Log()
{
	$a2Log = file_get_contents(get_path("alarm2"));
	$a2Log = convertFalse2Empty($a2Log);
	return json_encode($a2Log);
}
function get_a3Log()
{
	$a3Log = file_get_contents(get_path("alarm3"));
	$a3Log = convertFalse2Empty($a3Log);
	return json_encode($a3Log);
}
function get_a4Log()
{
	$a4Log = file_get_contents(get_path("alarm4"));
	$a4Log = convertFalse2Empty($a4Log);
	return json_encode($a4Log);
}
function get_accessLog() {
    $accessLog = file_get_contents("/mnt/usbflash/log/accessLog.log");
    $accessLog = convertFalse2Empty($accessLog);
    return json_encode($accessLog);
}

function get_path($name) {return "/mnt/usbflash/log/event_log_".$name.".log";}
function convertFalse2Empty($str) {return $str === false ? "" : $str;}

function get_gpio($gpionum)
{
	global $rms_info;
	return $rms_info['gpio_state'][$gpionum];
}


function get_relayNO($relaynum)
{
	global $db_info;
	return $db_info['relay_no_label'][$relaynum];
}

function get_relayNC($relaynum)
{
	global $db_info;
	return $db_info['relay_nc_label'][$relaynum];
}

function get_relay($relaynum)
{
	$relay_state = get_relay_state($relaynum);
	return $relay_state;
}

function get_alarm($alarmnum)
{
	global $rms_info;
	return $rms_info['alarm_state'][$alarmnum];
}

function get_diskinfo()
{
	global $rms_info;
	return $rms_info['diskinfo'];
}

function  get_meminfo()
{
	global $rms_info;
	return $rms_info['meminfo'];
}

function  get_tempf()
{
	global $rms_info;
	return $rms_info['tempf'];
}

function get_tempfLog() 
{
	$tempLog = convertFalse2Empty(get_tempf());
	return json_encode($tempLog);
}

function  get_uptime()
{
	global $rms_info;
	return $rms_info['uptime'];
}

function get_vm($vmnum)
{
	global $db_info, $rms_info;
	$voltmeter = $rms_info['vm_raw'][$vmnum];
	if ($db_info['vm_mode'][$vmnum] == "a") {
		$current_shunt_a = $db_info['vm_shunta'][$vmnum];
		$current_shunt_mv = $db_info['vm_shuntmv'][$vmnum];
		if ($voltmeter > 0) {
			$sine = "POS";
		} else {
			$sine = "NEG";
		}
		$voltmeter = abs($voltmeter); //change to positive number
		$voltmeter = sprintf('%3.5f', $voltmeter);
		$voltmeter = ($current_shunt_a / $current_shunt_mv) * ($voltmeter * 1000);

		if ($sine == "NEG") {
			$voltmeter = "-" . $voltmeter;
		}

		if ($db_info['watt_mode_enabled'][$vmnum] == "CHECKED") {
			$vb = $db_info['wattmode_vm_base'][$vmnum];
			$voltmeter_base_voltage = $rms_info['vm_raw'][$vb];
			$voltmeter_base_voltage = sprintf('%3.3f', $voltmeter_base_voltage);
			$voltmeter = $voltmeter * $voltmeter_base_voltage;
		}
	}
	if ($db_info['vm_per'][$vmnum] == 0) {
		$voltmeter = sprintf('%3.0f', $voltmeter);
	}
	if ($db_info['vm_per'][$vmnum] == 1) {
		$voltmeter = sprintf('%3.1f', $voltmeter);
	}
	if ($db_info['vm_per'][$vmnum] == 2) {
		$voltmeter = sprintf('%3.2f', $voltmeter);
	}
	if ($db_info['vm_per'][$vmnum] == 3) {
		$voltmeter = sprintf('%3.3f', $voltmeter);
	}
	if ($db_info['vm_per'][$vmnum] == 4) {
		$voltmeter = sprintf('%3.4f', $voltmeter);
	}
	if ($db_info['vm_per'][$vmnum] == 5) {
		$voltmeter = sprintf('%3.5f', $voltmeter);
	}
	if ($db_info['vm_per'][$vmnum] == 6) {
		$voltmeter = sprintf('%3.6f', $voltmeter);
	}
	return $voltmeter;
}

function tempAlert() {
    /**
     * if temp is < loTempTrigger or temp > hiTempTrigger
     * then write 0 to /var/rmsdata/alarm5
     * else write 1 to /var/rmsdata/alarm5
     *
     * ADD script as cron
     */
}

?>