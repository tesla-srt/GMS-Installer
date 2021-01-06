<?php
if (substr_count($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip')) ob_start("ob_gzhandler"); else ob_start();	
$iso_db_info = array
	( 
	'iso_vm_per' => array(), 'iso_vm_mode' => array(), 'iso_vm_shunta' => array(), 'iso_vm_shuntmv' => array(), 'iso_vm_adj' => array()
	);	

$iso_db_info['iso_vm_per'][] = "";
$iso_db_info['iso_vm_mode'][] = "";
$iso_db_info['iso_vm_shunta'][] = "";
$iso_db_info['iso_vm_shuntmv'][] = "";
$iso_db_info['iso_vm_adj'][] = "";

$rms_info = array
	(
	'vm_raw' => array(), 'thedate', 'thetime'
	);
$rms_info['vm_raw'][] = "";

$myFile = "/var/rmsdata/settings/iso_vm";
if (file_exists($myFile)) 
	{
		$fh = fopen($myFile, 'r');
		for ($i=1; $i<7; $i++)	{	$rms_info['vm_raw'][] = trim(fgets($fh));	}
		fclose($fh);
	}
else
	{
		for ($i=1; $i<7; $i++)	{	$rms_info['vm_raw'][] = 0.000000;	}
	}

$rms_info['thedate'] = date('M-d-Y');
$rms_info['thetime'] = date('h:i:s A');
	
$myFile = "/var/rmsdata/settings/iso_db_info";
if (file_exists($myFile))
	{
		$fh = fopen($myFile, 'r');
		for ($i=1; $i<7; $i++)
			{
			$iso_db_info['iso_vm_per'][] = trim(fgets($fh));
			$iso_db_info['iso_vm_mode'][] = trim(fgets($fh));
			$iso_db_info['iso_vm_shunta'][] = trim(fgets($fh));
			$iso_db_info['iso_vm_shuntmv'][] = trim(fgets($fh));
			$iso_db_info['iso_vm_adj'][] = trim(fgets($fh));
			}
		fclose($fh);	
	}	
else
	{
		for ($i=1; $i<7; $i++)
			{
			$iso_db_info['iso_vm_per'][] = 4;
			$iso_db_info['iso_vm_mode'][] = v;
			$iso_db_info['iso_vm_shunta'][] = 0;
			$iso_db_info['iso_vm_shuntmv'][] = 0;
			$iso_db_info['iso_vm_adj'][] = 1.00000;
			}
	}




if($_GET["element"] == "array_dump")
	{
	print "<pre>";
	print "iso_db_info:\n";
	print_r ($iso_db_info);
	print "rms_info:\n";
	print_r ($rms_info);
	print "</pre>";
	}

$element = (isset($_GET["element"]) ? $_GET["element"] : ""); 
$ans = null;
switch ($element) {

case "vm1":
  $ans = get_vm(1);
  break;
case "vm2":
  $ans = get_vm(2);
  break;
case "vm3":
  $ans = get_vm(3);
  break;
case "vm4":
  $ans = get_vm(4);
  break;
case "vm5":
  $ans = get_vm(5);
  break;
case "vm6":
  $ans = get_vm(6);
  break;  
case "vmall":
  $ans = vmall();
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
case "vm4all":
  $ans = get_vm_all(4);
  break;      
case "vm5all":
  $ans = get_vm_all(5);
  break;      
case "vm6all":
  $ans = get_vm_all(6);
  break;

default:
  $ans = usage();
 }
 
//  send it back to the caller
print ($ans);

// ------------------------------------------------------------------------------------------

function usage()
	{
	print "Usage Example: http://10.10.10.10/iso_vm_server.php?element=vm1<BR>Call this file with element=xxxxx, Where xxxxx is one of the following:<br>";
	//print "For <br>";
	print "<pre>"; 
	print "
	vm1
	vm2
	vm3
	vm4
	vm5
	vm6
	vm1all
	vm2all
	vm3all
	vm4all
	vm5all
	vm6all
	";
	print "</pre>"; 
	}




function get_vm( $vmnum )
	{
	global $iso_db_info, $rms_info;
	$voltmeter = $rms_info['vm_raw'][$vmnum];
 	
 	if($iso_db_info['iso_vm_mode'][$vmnum] == "a")
		{	
 			$current_shunt_a = $iso_db_info['iso_vm_shunta'][$vmnum];
 			$current_shunt_mv = $iso_db_info['iso_vm_shuntmv'][$vmnum];
 			
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
		}

	if($iso_db_info['iso_vm_per'][$vmnum] == 0)	{	$voltmeter = sprintf('%3.0f',$voltmeter);	}
	if($iso_db_info['iso_vm_per'][$vmnum] == 1)	{	$voltmeter = sprintf('%3.1f',$voltmeter);	}
	if($iso_db_info['iso_vm_per'][$vmnum] == 2)	{	$voltmeter = sprintf('%3.2f',$voltmeter);	}
	if($iso_db_info['iso_vm_per'][$vmnum] == 3)	{	$voltmeter = sprintf('%3.3f',$voltmeter);	}
	if($iso_db_info['iso_vm_per'][$vmnum] == 4)	{	$voltmeter = sprintf('%3.4f',$voltmeter);	}
	if($iso_db_info['iso_vm_per'][$vmnum] == 5)	{	$voltmeter = sprintf('%3.5f',$voltmeter);	}
	if($iso_db_info['iso_vm_per'][$vmnum] == 6)	{	$voltmeter = sprintf('%3.6f',$voltmeter);	}
	return $voltmeter;
	}


function get_vm_all( $vmnum )
	{
	global $iso_db_info, $rms_info;
 	if($iso_db_info['iso_vm_per'][$vmnum]==0){$vm_mv = sprintf('%6.0f',$rms_info['vm_raw'][$vmnum] * 1000);}
	if($iso_db_info['iso_vm_per'][$vmnum]==1){$vm_mv = sprintf('%6.1f',$rms_info['vm_raw'][$vmnum] * 1000);}
	if($iso_db_info['iso_vm_per'][$vmnum]==2){$vm_mv = sprintf('%6.2f',$rms_info['vm_raw'][$vmnum] * 1000);}
	if($iso_db_info['iso_vm_per'][$vmnum]==3){$vm_mv = sprintf('%6.3f',$rms_info['vm_raw'][$vmnum] * 1000);}
	if($iso_db_info['iso_vm_per'][$vmnum]==4){$vm_mv = sprintf('%6.4f',$rms_info['vm_raw'][$vmnum] * 1000);}	
 	if($iso_db_info['iso_vm_per'][$vmnum]==5){$vm_mv = sprintf('%6.5f',$rms_info['vm_raw'][$vmnum] * 1000);}
 	if($iso_db_info['iso_vm_per'][$vmnum]==6){$vm_mv = sprintf('%6.6f',$rms_info['vm_raw'][$vmnum] * 1000);}
 	if($iso_db_info['iso_vm_mode'][$vmnum] == "a")	{	$mode = "Amps";	}	else {	$mode = "Volts";	}
	$vm = get_vm($vmnum);
	if($vmnum == 1)
	{
		$data = array('vm1all'=>array
		(
		'vm1p1'=>$vm,
		'vm1p2'=>$mode,
		'vm1p3'=>$vm_mv,
		'vm1p4'=>$rms_info['thedate'],
		'vm1p5'=>$rms_info['thetime'],
		'vm1p6'=>$rms_info['vm_raw'][$vmnum]
		));
	}
	
	if($vmnum == 2)
	{
		$data = array('vm2all'=>array
		(
		'vm2p1'=>$vm,
		'vm2p2'=>$mode,
		'vm2p3'=>$vm_mv,
		'vm2p4'=>$rms_info['thedate'],
		'vm2p5'=>$rms_info['thetime'],
		'vm2p6'=>$rms_info['vm_raw'][$vmnum]
		));
	}
	
	if($vmnum == 3)
	{
		$data = array('vm3all'=>array
		(
		'vm3p1'=>$vm,
		'vm3p2'=>$mode,
		'vm3p3'=>$vm_mv,
		'vm3p4'=>$rms_info['thedate'],
		'vm3p5'=>$rms_info['thetime'],
		'vm3p6'=>$rms_info['vm_raw'][$vmnum]
		));
	}
	
	if($vmnum == 4)
	{
		$data = array('vm4all'=>array
		(
		'vm4p1'=>$vm,
		'vm4p2'=>$mode,
		'vm4p3'=>$vm_mv,
		'vm4p4'=>$rms_info['thedate'],
		'vm4p5'=>$rms_info['thetime'],
		'vm4p6'=>$rms_info['vm_raw'][$vmnum]
		));
	}
	
	if($vmnum == 5)
	{
		$data = array('vm5all'=>array
		(
		'vm5p1'=>$vm,
		'vm5p2'=>$mode,
		'vm5p3'=>$vm_mv,
		'vm5p4'=>$rms_info['thedate'],
		'vm5p5'=>$rms_info['thetime'],
		'vm5p6'=>$rms_info['vm_raw'][$vmnum]
		));
	}
	
	if($vmnum == 6)
	{
		$data = array('vm6all'=>array
		(
		'vm6p1'=>$vm,
		'vm6p2'=>$mode,
		'vm6p3'=>$vm_mv,
		'vm6p4'=>$rms_info['thedate'],
		'vm6p5'=>$rms_info['thetime'],
		'vm6p6'=>$rms_info['vm_raw'][$vmnum]
		));
	}
	
	$sd_string = json_encode($data);
	return $sd_string;
	}
 

function  vmall()
	{
	$vm1=get_vm(1);
	$vm2=get_vm(2);
	$vm3=get_vm(3);
	$vm4=get_vm(4);
	$vm5=get_vm(5);
	$vm6=get_vm(6);
	$data = array('vms'=>array('vm1'=>$vm1, 'vm2'=>$vm2,'vm3'=>$vm3,'vm4'=>$vm4,'vm5'=>$vm5,'vm6'=>$vm6));
	$sd_string = json_encode($data);
	return $sd_string;	
	}
 


?>
