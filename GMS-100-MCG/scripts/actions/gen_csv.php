<?php
include "/usr/local/webif/lib.php";
//$tmp_path = "/var/rrd";
$img_type = "png";	// png or svg
$tmp_path = "/usr/local/webif/rrd/tmp";
$log_path = "/mnt/usbflash/log";
//$tmp_path = "/data/custom/html/mattLib/graphs";
$hostname = trim(file_get_contents("/etc/hostname"));
$time_now = time() - 60;
$graph_width = "400";
$graph_height = "100";
$month = date('F');
$day = date('d');
$year = date('Y');
$hour = date('H');
$min = date('i');
$sec = date('s');
$start_date = $month . " " . $day . " " . $year . " " . $hour . ":". $min . ":" . $sec;

// Add a CRON Job to run this script every minute: nice -n 10 php /usr/local/webif/rms-graph.php gen_graphs

$hostname = trim(file_get_contents("/etc/hostname"));
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

$templist = array ($start_date, $rms_info['tempf'], "F");

  // Creates a new csv file and store it in tmp directory
$new_csv = fopen($log_path . '/temperature_report.csv', 'a');
fputcsv($new_csv, $templist);
fclose($new_csv);



$new_csv = fopen($log_path . '/vm_report.csv', 'a');
$vmlist = array($start_date, getSysPowerVoltage(), getBatteryVoltage(), getPoeVoltage(), "V");
fputcsv($new_csv, $vmlist);
fclose($new_csv);

function getSysPowerVoltage(){return trim(file_get_contents("/var/rmsdata/vm1"));}
function getBatteryVoltage(){return trim(file_get_contents("/var/rmsdata/vm2"));}
function getPoeVoltage(){return trim(file_get_contents("/var/rmsdata/vm2"));}
    
?>