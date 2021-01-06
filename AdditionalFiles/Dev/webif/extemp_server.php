<?php	/* --- EXTERNAL USB TEMPERATURE SENSOR SERVER For RMS-100 (C) 2016 ETHERTEK CIRCUITS --- */


// this script shouldn't take more than a few seconds to run
set_time_limit(3);
ini_set('max_execution_time', 3);

$element = (isset($_GET["element"]) ? $_GET["element"] : ""); 
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

default:
  $ans = usage();
 }
 
//  send it back to the caller
print ($ans);

// ------------------------------------------------------------------------------------------

function usage()
	{
	print "Usage Example: http://10.10.10.10/extemp_server.php?element=temps<BR>Call this file with element=xxxxx, Where xxxxx is one of the following:<br>";
	//print "For <br>";
	print "<pre>"; 
	print "
	tempc
	tempf
	temps
	";
	print "</pre>"; 
	}


function get_tempc()
{
	if (file_exists("/var/rmsdata/extempc")) 
		{
			$tempc = trim(file_get_contents("/var/rmsdata/extempc"));
		}
	else
		{
			$tempc = "0.0";
		}		

	$data = array('temp'=>array('tempc'=>"$tempc"));
	$sd_string = json_encode($data);
	return $sd_string;
}

function get_tempf()
{
	if (file_exists("/var/rmsdata/extempf")) 
		{
			$tempf = trim(file_get_contents("/var/rmsdata/extempf"));
		}
	else
		{
			$tempf = "0.0";
		}		

	$data = array('temp'=>array('tempf'=>"$tempf"));
	$sd_string = json_encode($data);
	return $sd_string;
}

function get_temps()
{
	if (file_exists("/var/rmsdata/extempc")) 
		{
			$tempc = trim(file_get_contents("/var/rmsdata/extempc"));
		}
	else
		{
			$tempc = "0.0";
		}	
	
	if (file_exists("/var/rmsdata/extempf")) 
		{
			$tempf = trim(file_get_contents("/var/rmsdata/extempf"));
		}
	else
		{
			$tempf = "0.0";
		}		

	$data = array('temp'=>array('tempc'=>"$tempc",'tempf'=>"$tempf"));
	$sd_string = json_encode($data);
	return $sd_string;
}

?>
