<?php
if (substr_count($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip')) ob_start("ob_gzhandler"); else ob_start();
$db_info = array
	( 
	'vm_mjumper' => array(), 'vm_per' => array(), 'vm_mode' => array(), 'vm_shunta' => array(), 'vm_shuntmv' => array(),
	'relay_no_label' => array(), 'relay_nc_label' => array(), 'relayconf', 'hi_icon1', 'lo_icon1', 'hi_icon2', 'lo_icon2', 'hi_icon3', 'lo_icon3',
	'hi_icon4', 'lo_icon4', 'hi_icon5', 'lo_icon5', 'watt_mode_enabled' => array(), 'wattmode_vm_base' => array(),
	'a1hi','a1lo','a2hi','a2lo','a3hi','a3lo','a4hi','a4lo','a5hi','a5lo','r1nc_color','r1no_color','r2nc_color','r2no_color'
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
for ($i=1; $i<4; $i++)
	{
	$db_info['vm_mjumper'][] = trim(fgets($fh));
	$db_info['vm_per'][] = trim(fgets($fh));
	$db_info['vm_mode'][] = trim(fgets($fh));
	$db_info['vm_shunta'][] = trim(fgets($fh));
	$db_info['vm_shuntmv'][] = trim(fgets($fh));
	}
for ($i=1; $i<3; $i++)
	{
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
for ($i=1; $i<15; $i++)
	{
		$un_needed_name_field = trim(fgets($fh));
	}

for ($i=1; $i<4; $i++)
	{
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

$rms_info = array
	(
	'vm_raw' => array(), 'relay_state' => array(), 'alarm_state' => array(), 'gpio_state' => array(),
	'button_state', 'tempc', 'tempf', 'temps', 'thedate', 'thetime', 'uptime', 'diskinfo', 'meminfo',
	'amp_hour' => array(),'watt_hour' => array(),'gpio_dir' => array()
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
for ($i=1; $i<4; $i++)	{	$rms_info['vm_raw'][] = trim(fgets($fh));	}
for ($i=1; $i<3; $i++)	{	$rms_info['relay_state'][] = trim(fgets($fh));	}
for ($i=1; $i<6; $i++)	{	$rms_info['alarm_state'][] = trim(fgets($fh));	}
for ($i=1; $i<5; $i++)	{	$rms_info['gpio_state'][] = trim(fgets($fh));	}
$rms_info['button_state'] = trim(fgets($fh));
$rms_info['tempc'] = trim(fgets($fh));
$rms_info['tempf'] = trim(fgets($fh));
for ($i=1; $i<4; $i++)	{	$rms_info['amp_hour'][] = trim(fgets($fh));	}
for ($i=1; $i<4; $i++)	{	$rms_info['watt_hour'][] = trim(fgets($fh));	}
for ($i=1; $i<5; $i++)	{	$rms_info['gpio_dir'][] = trim(fgets($fh));	}
fclose($fh);

$rms_info['temps'] = $rms_info['tempc'] . " C " . $rms_info['tempf'] . " F";
$rms_info['thedate'] = date('M-d-Y');
$rms_info['thetime'] = date('h:i:s A');

$fh = fopen("/proc/uptime", 'r');
$uptime_line = fgets($fh);
fclose($fh);
$uptime = explode(' ', $uptime_line);
$rms_info['uptime'] = sprintf('%5.2f Days', ($uptime[0] / 60 / 60 /24) );

$rms_info['diskinfo'] = sprintf('%3.0f', (100 - (disk_free_space("/") / 1024 / 1024)) );

$fh = fopen("/proc/meminfo", 'r');
$memtotal = ereg_replace("[^0-9]", "", fgets($fh));
$memfree = ereg_replace("[^0-9]", "", fgets($fh));
fclose($fh);
$rms_info['meminfo'] = sprintf('%3.0f', (($memtotal - $memfree) / $memtotal * 100) );

if($_GET["element"] == "array_dump")
	{
	print "<pre>";
	print "db_info:\n";
	print_r ($db_info);
	print "\nrms_info:\n";
	print_r ($rms_info);
	print "</pre>";
	}

$callback = null;
if (isset($_GET["callback"]) && !empty($_GET["callback"])) 
{
    $callback = $_GET["callback"];
}

$element = (isset($_GET["element"]) ? $_GET["element"] : "");
$filename = (isset($_GET["filename"]) ? $_GET["filename"] : "");
$ans = null;
switch ($element) {
case "tempc":
  $ans = get_tempc();
  break;
case "tempf":
  $ans = get_tempf();
  break;
case "temps":
  $ans = get_temps();
  break;
case "vm1":
  $ans = get_vm(1);
  break;
case "vm2":
  $ans = get_vm(2);
  break;
case "vm3":
  $ans = get_vm(3);
  break;
case "date":
  $ans = get_date();
  break;
case "time":
  $ans = get_time();
  break;
case "uptime":
  $ans = get_uptime();
  break;                          
case "vmall":
  $ans = vmall();
  break;            
case "homeall":
  $ans = homeall();
  break;
case "meminfo":
  $ans = get_meminfo();
  break;    
case "diskinfo":
  $ans = get_diskinfo();
  break; 
case "ios":
  $ans = get_ios();
  break;   
case "alarm1":
  $ans = get_alarm(1);
  break;       
case "alarm2":
  $ans = get_alarm(2);
  break;       
case "alarm3":
  $ans = get_alarm(3);
  break;       
case "alarm4":
  $ans = get_alarm(4);
  break;       
case "alarm5":
  $ans = get_alarm(5);
  break;
case "alarmall":
  $ans = alarmall();
  break;         
case "input1":
  $ans = get_gpio(1);
  break;       
case "input2":
  $ans = get_gpio(2);
  break;       
case "input3":
  $ans = get_gpio(3);
  break;       
case "input4":
  $ans = get_gpio(4);
  break;            
case "relay1":
  $ans = get_relay(1);
  break;            
case "relay2":
  $ans = get_relay(2);
  break;                       
case "button1":
  $ans = get_button(1);
  break;            
case "relay1NO":
  $ans = get_relayNO(1);
  break;            
case "relay1NC":
  $ans = get_relayNC(1);
  break;            
case "relay2NO":
   $ans = get_relayNO(2);
  break;            
case "relay2NC":
  $ans = get_relayNC(2);
  break;
case "relays":
  $ans = relays();
  break;   
case "relayconf":
  $ans = get_relayconf();
  break;   
case "vm1all":
  $ans = get_vm_all(1);
  break;      
case "vm2all":
  $ans = get_vm_all(2);
  break;      
case "vm3all":
  $ans = get_vm_all(3);
  break;      
case "tempall":
  $ans = get_tempall();
  break;      
case "relay1ON":
  $ans = relay1ON();
  break;
case "relay1OFF":
  $ans = relay1OFF();
  break;
case "relay2ON":
  $ans = relay2ON();
  break;
case "relay2OFF":
  $ans = relay2OFF();
  break;
case "usbrelays":
  $ans = usbrelays();
  break;  
case "get_timers":
  $ans = get_timers();
  break; 
case "get_fw_filesize":
	$ans = get_Firmware_File_Size( $filename );
	break;	
case "update":
	$ans = update( $filename );
	break;
	
	
default:
  $ans = usage();
 }

if($callback != null && strlen($callback) < 51)
 {
	$ans = $callback . "(" . $ans . ");";
 }
 
//  send it back to the caller
print ($ans);

// ------------------------------------------------------------------------------------------

function usage()
	{
	print "Usage Example: http://10.10.10.10/sdserver.php?element=tempc<BR>Call this file with element=xxxxx, Where xxxxx is one of the following:<br>";
	//print "For <br>";
	print "<pre>"; 
	print "
	tempc
	tempf
	temps
	vm1
	vm2
	vm3
	date
	time
	uptime
	vmall
	homeall
	meminfo
	diskinfo
	ios
	alarm1
	alarm2
	alarm3
	alarm4
	alarm5
	button1
	input1
	input2
	input3
	input4
	relay1
	relay2
	relay1NO
	relay1NC
	relay2NO
	relay2NC
	relays
	relayconf
	vm1all
	vm2all
	vm3all
	tempall
	relay1ON
	relay1OFF
	relay2ON
	relay2OFF
	usbrelays
	get_timers";
	print "</pre>"; 
	}

function  usbrelays()
	{
		$myFile = "/var/rmsdata/rdbrelayall";
		if (file_exists($myFile)) 
		{
			$fh = fopen($myFile, 'r');
			$rdb_relay1 = trim(fgets($fh));
			$rdb_relay2 = trim(fgets($fh));
			$rdb_relay3 = trim(fgets($fh));
			$rdb_relay4 = trim(fgets($fh));
			$rdb_relay5 = trim(fgets($fh));
 		}
 		else
 		{
			$rdb_relay1 = 0;
			$rdb_relay2 = 0;
			$rdb_relay3 = 0;
			$rdb_relay4 = 0;
			$rdb_relay5 = 0;
		}
	$data = array('rdb'=>array('relay1'=>$rdb_relay1, 'relay2'=>$rdb_relay2,'relay3'=>$rdb_relay3,'relay4'=>$rdb_relay4,'relay5'=>$rdb_relay5));
	$sd_string = json_encode($data);
	return $sd_string;
	}

function  relay1ON()
	{
	system("/bin/rmsrelay 1 on");
	$sd_string = " - OK";
	return $sd_string;
	}

function  relay1OFF()
	{
	system("/bin/rmsrelay 1 off");
	$sd_string = " - OK";
	return $sd_string;
	}

function  relay2ON() 
	{
	system("/bin/rmsrelay 2 on");
	$sd_string = " - OK";
	return $sd_string;
	}

function  relay2OFF() 
	{
	system("/bin/rmsrelay 2 off");
	$sd_string = " - OK";
	return $sd_string;
	}

function  get_tempall()
	{
	global $rms_info;
	$data = array('tmp'=>array('tmpc'=>$rms_info['tempc'],'tmpf'=>$rms_info['tempf']));
	$sd_string = json_encode($data);
	return $sd_string;	
	}

function get_vm_all( $vmnum )
	{
	global $db_info, $rms_info;
// 	if($db_info['per'][$vmnum]==0){$vm_mv = sprintf('%6.0f',$rms_info['vm_raw'][$vmnum] * 1000);}
//	if($db_info['per'][$vmnum]==1){$vm_mv = sprintf('%6.1f',$rms_info['vm_raw'][$vmnum] * 1000);}
//	if($db_info['per'][$vmnum]==2){$vm_mv = sprintf('%6.2f',$rms_info['vm_raw'][$vmnum] * 1000);}
//	if($db_info['per'][$vmnum]==3){$vm_mv = sprintf('%6.3f',$rms_info['vm_raw'][$vmnum] * 1000);}
//	if($db_info['per'][$vmnum]==4){$vm_mv = sprintf('%6.4f',$rms_info['vm_raw'][$vmnum] * 1000);}	
// 	if($db_info['per'][$vmnum]==5){$vm_mv = sprintf('%6.5f',$rms_info['vm_raw'][$vmnum] * 1000);}
// 	if($db_info['per'][$vmnum]==6){$vm_mv = sprintf('%6.6f',$rms_info['vm_raw'][$vmnum] * 1000);}
 	
 	$vm_mv = sprintf('%6.6f',$rms_info['vm_raw'][$vmnum] * 1000);
 	
 	if($db_info['vm_mode'][$vmnum] == "a")	
 		{	
 			$mode = "Amps";
 			if($db_info['watt_mode_enabled'][$vmnum] == "CHECKED")
 				{
 					$mode = "Watts";
 				}	
 		}	
 	else 
 		{	
 			$mode = "Volts";	
 		}
 		
	$vm = get_vm($vmnum);
	$data = array('vmall'=>array
		(
		'vmp1'=>$vm,
		'vmp2'=>$mode,
		'vmp3'=>$vm_mv,
		'vmp4'=>$rms_info['thedate'],
		'vmp5'=>$rms_info['thetime'],
		'vmp6'=>$rms_info['vm_raw'][$vmnum],
		'vmp7'=>$db_info['watt_mode_enabled'][$vmnum],
		'vmp8'=>$db_info['wattmode_vm_base'][$vmnum],
		'vmp9'=>$rms_info['amp_hour'][$vmnum],
		'vmp10'=>$rms_info['watt_hour'][$vmnum]
		));
	$sd_string = json_encode($data);
	return $sd_string;
	}
 
function  relays()
	{
	global $db_info;		
	$therelay1=get_relay(1);
	$therelay2=get_relay(2);
	$therelay1NO=get_relayNO(1);
	$therelay1NC=get_relayNC(1);
	$therelay2NO=get_relayNO(2);
	$therelay2NC=get_relayNC(2);
	$therelayconf=get_relayconf();
	$r1nc_color=$db_info['r1nc_color'];
	$r1no_color=$db_info['r1no_color'];
	$r2nc_color=$db_info['r2nc_color'];
	$r2no_color=$db_info['r2no_color'];
	$data = array('rly'=>array('r1'=>$therelay1,'r2'=>$therelay2,'r1NO'=>$therelay1NO,'r1NC'=>$therelay1NC,'r2NO'=>$therelay2NO,'r2NC'=>$therelay2NC,'rc'=>$therelayconf,'r1nc_color'=>$r1nc_color,'r1no_color'=>$r1no_color,'r2nc_color'=>$r2nc_color,'r2no_color'=>$r2no_color));
	$sd_string = json_encode($data);
	return $sd_string;	
	}

function  vmall()
	{
	$vm1=get_vm(1);
	$vm2=get_vm(2);
	$vm3=get_vm(3);
	$data = array('vms'=>array('vm1'=>$vm1, 'vm2'=>$vm2,'vm3'=>$vm3));
	$sd_string = json_encode($data);
	return $sd_string;	
	}

function alarmall()
{
	global $db_info, $rms_info;
	//Alarm states
	$a1=get_alarm(1);
	$a2=get_alarm(2);
	$a3=get_alarm(3);
	$a4=get_alarm(4);
	$a5=get_alarm(5);
	//Alarm state colors
	$alarm_hi_icon1=$db_info['hi_icon1'];
	$alarm_lo_icon1=$db_info['lo_icon1'];
	$alarm_hi_icon2=$db_info['hi_icon2'];
	$alarm_lo_icon2=$db_info['lo_icon2'];
	$alarm_hi_icon3=$db_info['hi_icon3'];
	$alarm_lo_icon3=$db_info['lo_icon3'];
	$alarm_hi_icon4=$db_info['hi_icon4'];
	$alarm_lo_icon4=$db_info['lo_icon4'];
	$alarm_hi_icon5=$db_info['hi_icon5'];
	$alarm_lo_icon5=$db_info['lo_icon5'];
	//Alarm state names
	$a1hi=$db_info['a1hi'];
	$a1lo=$db_info['a1lo'];
	$a2hi=$db_info['a2hi'];
	$a2lo=$db_info['a2lo'];
	$a3hi=$db_info['a3hi'];
	$a3lo=$db_info['a3lo'];
	$a4hi=$db_info['a4hi'];
	$a4lo=$db_info['a4lo'];
	$a5hi=$db_info['a5hi'];
	$a5lo=$db_info['a5lo'];
	
	$data = array('alarms'=>array('a1'=>$a1, 'a2'=>$a2,'a3'=>$a3,'a4'=>$a4,'a5'=>$a5,'aHi1'=>$alarm_hi_icon1,'aLi1'=>$alarm_lo_icon1,'aHi2'=>$alarm_hi_icon2,'aLi2'=>$alarm_lo_icon2,'aHi3'=>$alarm_hi_icon3,'aLi3'=>$alarm_lo_icon3,'aHi4'=>$alarm_hi_icon4,'aLi4'=>$alarm_lo_icon4,'aHi5'=>$alarm_hi_icon5,'aLi5'=>$alarm_lo_icon5,'a1hi'=>$a1hi,'a1lo'=>$a1lo,'a2hi'=>$a2hi,'a2lo'=>$a2lo,'a3hi'=>$a3hi,'a3lo'=>$a3lo,'a4hi'=>$a4hi,'a4lo'=>$a4lo,'a5hi'=>$a5hi,'a5lo'=>$a5lo));
	$sd_string = json_encode($data);
	return $sd_string;
	
}

function  homeall()
	{
	global $db_info, $rms_info;
	$vm1=get_vm(1);
	$vm2=get_vm(2);
	$vm3=get_vm(3);
	$tempc=get_tempc();
	$tempc=$tempc . ' C';
	$tempf=get_tempf();
	$tempf=$tempf . ' F';
	$thedate=get_date();
	$thetime=get_time();
	$theuptime=get_uptime();
	$meminfo=get_meminfo();
	$diskinfo=get_diskinfo();
	$thealarm1=get_alarm(1);
	$thealarm2=get_alarm(2);
	$thealarm3=get_alarm(3);
	$thealarm4=get_alarm(4);
	$thealarm5=get_alarm(5);
	$thegpio1=get_gpio(1);
	$thegpio2=get_gpio(2);
	$thegpio3=get_gpio(3);
	$thegpio4=get_gpio(4);
	$thegpio1dir=get_gpio_dir(1);
	$thegpio2dir=get_gpio_dir(2);
	$thegpio3dir=get_gpio_dir(3);
	$thegpio4dir=get_gpio_dir(4);
	$thebutton1=get_button(1);
	$therelay1=get_relay(1);
	$therelay2=get_relay(2);
	$therelay1NO=get_relayNO(1);
	$therelay1NC=get_relayNC(1);
	$therelay2NO=get_relayNO(2);
	$therelay2NC=get_relayNC(2);
	//Alarm state colors
	$alarm_hi_icon1=$db_info['hi_icon1'];
	$alarm_lo_icon1=$db_info['lo_icon1'];
	$alarm_hi_icon2=$db_info['hi_icon2'];
	$alarm_lo_icon2=$db_info['lo_icon2'];
	$alarm_hi_icon3=$db_info['hi_icon3'];
	$alarm_lo_icon3=$db_info['lo_icon3'];
	$alarm_hi_icon4=$db_info['hi_icon4'];
	$alarm_lo_icon4=$db_info['lo_icon4'];
	$alarm_hi_icon5=$db_info['hi_icon5'];
	$alarm_lo_icon5=$db_info['lo_icon5'];
	//Alarm state names
	$a1hi=$db_info['a1hi'];
	$a1lo=$db_info['a1lo'];
	$a2hi=$db_info['a2hi'];
	$a2lo=$db_info['a2lo'];
	$a3hi=$db_info['a3hi'];
	$a3lo=$db_info['a3lo'];
	$a4hi=$db_info['a4hi'];
	$a4lo=$db_info['a4lo'];
	$a5hi=$db_info['a5hi'];
	$a5lo=$db_info['a5lo'];
	//Relay State Colors
	$r1nc_color=$db_info['r1nc_color'];
	$r1no_color=$db_info['r1no_color'];
	$r2nc_color=$db_info['r2nc_color'];
	$r2no_color=$db_info['r2no_color'];
	$data = array('vms'=>array('vm1'=>$vm1, 'vm2'=>$vm2,'vm3'=>$vm3,'tempc'=>$tempc,'tempf'=>$tempf,'date'=>$thedate,'time'=>$thetime,'uptime'=>$theuptime,'meminfo'=>$meminfo,'diskinfo'=>$diskinfo,'a1'=>$thealarm1,'a2'=>$thealarm2,'a3'=>$thealarm3,'a4'=>$thealarm4,'a5'=>$thealarm5,'io1'=>$thegpio1,'io2'=>$thegpio2,'io3'=>$thegpio3,'io4'=>$thegpio4,'r1'=>$therelay1,'r2'=>$therelay2,'r1NO'=>$therelay1NO,'r1NC'=>$therelay1NC,'r2NO'=>$therelay2NO,'r2NC'=>$therelay2NC,'aHi1'=>$alarm_hi_icon1,'aLi1'=>$alarm_lo_icon1,'aHi2'=>$alarm_hi_icon2,'aLi2'=>$alarm_lo_icon2,'aHi3'=>$alarm_hi_icon3,'aLi3'=>$alarm_lo_icon3,'aHi4'=>$alarm_hi_icon4,'aLi4'=>$alarm_lo_icon4,'aHi5'=>$alarm_hi_icon5,'aLi5'=>$alarm_lo_icon5,'a1hi'=>$a1hi,'a1lo'=>$a1lo,'a2hi'=>$a2hi,'a2lo'=>$a2lo,'a3hi'=>$a3hi,'a3lo'=>$a3lo,'a4hi'=>$a4hi,'a4lo'=>$a4lo,'a5hi'=>$a5hi,'a5lo'=>$a5lo,'r1nc_color'=>$r1nc_color,'r1no_color'=>$r1no_color,'r2nc_color'=>$r2nc_color,'r2no_color'=>$r2no_color,'io1dir'=>$thegpio1dir,'io2dir'=>$thegpio2dir,'io3dir'=>$thegpio3dir,'io4dir'=>$thegpio4dir));
	$sd_string = json_encode($data);
	return $sd_string;
	}

function  get_relayconf()
	{
	global $db_info;
	return $db_info['relayconf'];
	}

function get_relayNO ($relaynum)
	{
	global $db_info;
	return $db_info['relay_no_label'][$relaynum];
	}

function get_relayNC ($relaynum)
	{
	global $db_info;
	return $db_info['relay_nc_label'][$relaynum];
	} 

function get_relay ($relaynum)
	{
		$relay_state = get_relay_state($relaynum);
		return $relay_state;
	} 

function get_button ($buttonnum)
	{
		$button_state = get_button_state($buttonnum);
		return $button_state;
	} 

function get_gpio ($gpionum)
	{
	global $rms_info;
	return $rms_info['gpio_state'][$gpionum];
	}

function get_gpio_dir ($gpionum)
	{
	global $rms_info;
	return $rms_info['gpio_dir'][$gpionum];
	}

function get_alarm ($alarmnum)
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

function  get_tempc()
	{
	global $rms_info;
	return $rms_info['tempc'];
	}

function  get_tempf()
	{
	global $rms_info;
	return $rms_info['tempf'];
	}

function  get_temps()
	{
	global $rms_info;
	return $rms_info['temps'];
	}

function  get_date()
	{
	global $rms_info;
	return $rms_info['thedate'];
	}

function  get_time()
	{
	global $rms_info;
	return $rms_info['thetime'];
	}

function  get_uptime()
	{
	global $rms_info;
	return $rms_info['uptime'];
	}

function get_vm( $vmnum )
	{
	global $db_info, $rms_info;
	$voltmeter = $rms_info['vm_raw'][$vmnum];
 	if($db_info['vm_mode'][$vmnum] == "a")
		{	
 			$current_shunt_a = $db_info['vm_shunta'][$vmnum];
 			$current_shunt_mv = $db_info['vm_shuntmv'][$vmnum];
			if($voltmeter > 0)
 				{
 					$sine = "POS";
 				}
 			else
 				{
 					$sine = "NEG";
 				}	
			$voltmeter=abs($voltmeter); //change to positive number
			$voltmeter = sprintf('%3.5f',$voltmeter);
			$voltmeter = ($current_shunt_a / $current_shunt_mv) * ($voltmeter * 1000);	
			
			if($sine == "NEG"){$voltmeter = "-" . $voltmeter;}
			
			if($db_info['watt_mode_enabled'][$vmnum] == "CHECKED")
				{	
					$vb = $db_info['wattmode_vm_base'][$vmnum];
					$voltmeter_base_voltage = $rms_info['vm_raw'][$vb];
					$voltmeter_base_voltage = sprintf('%3.3f',$voltmeter_base_voltage);
					$voltmeter = $voltmeter * $voltmeter_base_voltage;
				}
		}
	if($db_info['vm_per'][$vmnum] == 0)	{	$voltmeter = sprintf('%3.0f',$voltmeter);	}
	if($db_info['vm_per'][$vmnum] == 1)	{	$voltmeter = sprintf('%3.1f',$voltmeter);	}
	if($db_info['vm_per'][$vmnum] == 2)	{	$voltmeter = sprintf('%3.2f',$voltmeter);	}
	if($db_info['vm_per'][$vmnum] == 3)	{	$voltmeter = sprintf('%3.3f',$voltmeter);	}
	if($db_info['vm_per'][$vmnum] == 4)	{	$voltmeter = sprintf('%3.4f',$voltmeter);	}
	if($db_info['vm_per'][$vmnum] == 5)	{	$voltmeter = sprintf('%3.5f',$voltmeter);	}
	if($db_info['vm_per'][$vmnum] == 6)	{	$voltmeter = sprintf('%3.6f',$voltmeter);	}
	return $voltmeter;
	}
 
 function  get_timers()
	{
		$timer1=get_timer(1);
		$timer2=get_timer(2);
		$timer3=get_timer(3);
		$timer4=get_timer(4);
		$timer5=get_timer(5);
		$timer6=get_timer(6);
		$timer7=get_timer(7);
		$timer8=get_timer(8);
		$timer9=get_timer(9);
		$timer10=get_timer(10);
		$timer11=get_timer(11);
		$timer12=get_timer(12);
		$timer13=get_timer(13);
		$timer14=get_timer(14);
		$timer15=get_timer(15);
		$dbh = new PDO("sqlite:/etc/rms100.db");
		for($i = 1; $i < 16; $i++)
			{
				$result  = $dbh->query("SELECT en FROM timers WHERE id=" . $i);
				foreach($result as $row)
				if($row[0] == "on")	{	$timerc[$i] = "green";	}	else {	$timerc[$i] = "red";	}
			}
		$dbh = NULL;	
			
		$data = array('timers'=>array('t1'=>$timer1,'t1c'=>$timerc[1],'t2'=>$timer2,'t2c'=>$timerc[2],'t3'=>$timer3,'t3c'=>$timerc[3],'t4'=>$timer4,'t4c'=>$timerc[4],'t5'=>$timer5,'t5c'=>$timerc[5],'t6'=>$timer6,'t6c'=>$timerc[6],'t7'=>$timer7,'t7c'=>$timerc[7],'t8'=>$timer8,'t8c'=>$timerc[8],'t9'=>$timer9,'t9c'=>$timerc[9],'t10'=>$timer10,'t10c'=>$timerc[10],'t11'=>$timer11,'t11c'=>$timerc[11],'t12'=>$timer12,'t12c'=>$timerc[12],'t13'=>$timer13,'t13c'=>$timerc[13],'t14'=>$timer14,'t14c'=>$timerc[14],'t15'=>$timer15,'t15c'=>$timerc[15]));
		$sd_string = json_encode($data);
		return $sd_string;	
	}
 
 function  get_ios()
	{
		global $db_info, $rms_info;
		$thealarm1=get_alarm(1);
		$thealarm2=get_alarm(2);
		$thealarm3=get_alarm(3);
		$thealarm4=get_alarm(4);
		$thealarm5=get_alarm(5);
		$thegpio1=get_gpio(1);
		$thegpio2=get_gpio(2);
		$thegpio3=get_gpio(3);
		$thegpio4=get_gpio(4);
		$thegpio1dir=get_gpio_dir(1);
		$thegpio2dir=get_gpio_dir(2);
		$thegpio3dir=get_gpio_dir(3);
		$thegpio4dir=get_gpio_dir(4);
		$thebutton1=get_button(1);
		$alarm_hi_icon1=$db_info['hi_icon1'];
		$alarm_lo_icon1=$db_info['lo_icon1'];
		$alarm_hi_icon2=$db_info['hi_icon2'];
		$alarm_lo_icon2=$db_info['lo_icon2'];
		$alarm_hi_icon3=$db_info['hi_icon3'];
		$alarm_lo_icon3=$db_info['lo_icon3'];
		$alarm_hi_icon4=$db_info['hi_icon4'];
		$alarm_lo_icon4=$db_info['lo_icon4'];
		$alarm_hi_icon5=$db_info['hi_icon5'];
		$alarm_lo_icon5=$db_info['lo_icon5'];
		//Alarm state names
		$a1hi=$db_info['a1hi'];
		$a1lo=$db_info['a1lo'];
		$a2hi=$db_info['a2hi'];
		$a2lo=$db_info['a2lo'];
		$a3hi=$db_info['a3hi'];
		$a3lo=$db_info['a3lo'];
		$a4hi=$db_info['a4hi'];
		$a4lo=$db_info['a4lo'];
		$a5hi=$db_info['a5hi'];
		$a5lo=$db_info['a5lo'];	
		
		$data = array('ios'=>array('alarm1'=>$thealarm1,'alarm2'=>$thealarm2,'alarm3'=>$thealarm3,'alarm4'=>$thealarm4,'alarm5'=>$thealarm5,'io1'=>$thegpio1,'io2'=>$thegpio2,'io3'=>$thegpio3,'io4'=>$thegpio4,'btn1'=>$thebutton1,'aHi1'=>$alarm_hi_icon1,'aLi1'=>$alarm_lo_icon1,'aHi2'=>$alarm_hi_icon2,'aLi2'=>$alarm_lo_icon2,'aHi3'=>$alarm_hi_icon3,'aLi3'=>$alarm_lo_icon3,'aHi4'=>$alarm_hi_icon4,'aLi4'=>$alarm_lo_icon4,'aHi5'=>$alarm_hi_icon5,'aLi5'=>$alarm_lo_icon5,'a1hi'=>$a1hi,'a1lo'=>$a1lo,'a2hi'=>$a2hi,'a2lo'=>$a2lo,'a3hi'=>$a3hi,'a3lo'=>$a3lo,'a4hi'=>$a4hi,'a4lo'=>$a4lo,'a5hi'=>$a5hi,'a5lo'=>$a5lo,'io1dir'=>$thegpio1dir,'io2dir'=>$thegpio2dir,'io3dir'=>$thegpio3dir,'io4dir'=>$thegpio4dir));	
		$sd_string = json_encode($data);
		return $sd_string;	
	}
 
 function get_timer( $timer )
	{
		$myfile = "/tmp/timer" . $timer;
		if(file_exists($myfile))
			{
				$fh = fopen($myfile, 'r');
				$secs = trim(fgets($fh));
				fclose($fh);
			}
		else
			{
				$time = "00:00:00:00";			
				return $time;	
			}
		
		$myDay = gmdate("z", $secs);
		if($myDay < 10)
			{
				$time = gmdate("0z:H:i:s", $secs);
			}
		else
			{
				$time = gmdate("z:H:i:s", $secs);
			}	
			
		return $time;
	}

 	function get_Firmware_File_Size( $filename )
	{
		if($filename != "")
		{
			if(file_exists("/data/".$filename))
			{
				$size = filesize("/data/".$filename);
			}
			else
			{
				$size = "0";
			}
		}
		else
		{
			$size = "0";
		}	

		$data = array('size'=>$size);	
		$sd_string = json_encode($data);
		return $sd_string;
	}
 	
 	function update( $filename )
	{
		if($filename != "")
		{
			if(file_exists("/data/".$filename))
			{
				exec("rootfsupgrade " . $filename . " > /dev/null 2>&1 &");
				$msg = "OK";
			}
			else
			{
				$msg = "error";
			}
		}
		else
		{
			$msg = "error";
		}
		$data = array('msg'=>$msg);	
		$sd_string = json_encode($data);
		return $sd_string;
	}
 	
 
?>