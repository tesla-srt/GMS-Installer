<?	/* --- EFOY SERVER For RMS-100 (C) 2017 ETHERTEK CIRCUITS --- */
if (substr_count($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip')) ob_start("ob_gzhandler"); else ob_start();

// this script shouldn't take more than a few seconds to run
set_time_limit(3);
ini_set('max_execution_time', 3);

$element = (isset($_GET["element"]) ? $_GET["element"] : ""); 
$ans = null;
switch ($element) {
case "efoyall":
  $ans = efoyall();
  break;

default:
  $ans = usage();
 }
 
//  send it back to the caller

print ($ans);

// ------------------------------------------------------------------------------------------

function usage()
	{
	print "Usage Example: http://10.10.10.10/efoy_server.php?element=efoyall<BR>Call this file with element=xxxxx, Where xxxxx is one of the following:<br>";
	//print "For <br>";
	print "<pre>"; 
	print "
	efoyall
	";
	print "</pre>"; 
	}


	
	

function efoyall()
{
	if(file_exists("/tmp/efoy.txt"))
	{
		$lines = file("/tmp/efoy.txt");
		$numLines = count($lines);
		
		//SFC
		$txt = trim($lines[0]);
		$txt = strstr($txt,"SFC");
		if($txt == FALSE)
		{
			//Error in Data
			$volts = "0";
			$amps = "0";
			$o_time = "No Data";
			$o_state = "No Data";
			$mode = "No Data";
			$coe = "No Data";
			$error = "No Data";
			$msg = "error1, SFC not found!";
			$data = array('efoy'=>array('volts'=>$volts,'amps'=>$amps,'o_time'=>$o_time,'o_state'=>$o_state,'mode'=>$mode,'coe'=>$coe,'error'=>$error,'msg'=>$msg));
			$sd_string = json_encode($data);
			return $sd_string;
		}
		
		//Voltage
		$txt = trim($lines[1]);
		$txt = strstr($txt,"battery voltage ");
		if($txt == FALSE)
		{
			//Error in Data
			$volts = "0";
			$amps = "0";
			$o_time = "No Data";
			$o_state = "No Data";
			$mode = "No Data";
			$coe = "No Data";
			$error = "No Data";
			$msg = "error2, no battery voltage!";
			$data = array('efoy'=>array('volts'=>$volts,'amps'=>$amps,'o_time'=>$o_time,'o_state'=>$o_state,'mode'=>$mode,'coe'=>$coe,'error'=>$error,'msg'=>$msg));
			$sd_string = json_encode($data);
			return $sd_string;
		}
		$txt = substr($txt,16);
		$txt = substr($txt, 0, -1);
		$volts = $txt;
		
		//Current
		$txt = trim($lines[2]);
		$txt = strstr($txt,"output current ");
		if($txt == FALSE)
		{
			//Error in Data
			$amps = "0";
			$o_time = "No Data";
			$o_state = "No Data";
			$mode = "No Data";
			$coe = "No Data";
			$error = "No Data";
			$msg = "error3, no output current!";
			$data = array('efoy'=>array('volts'=>$volts,'amps'=>$amps,'o_time'=>$o_time,'o_state'=>$o_state,'mode'=>$mode,'coe'=>$coe,'error'=>$error,'msg'=>$msg));
			$sd_string = json_encode($data);
			return $sd_string;
		}
		$txt = substr($txt,15);
		$txt = substr($txt, 0, -1);
		$amps = $txt;
		
		//Operation Time
		$txt = trim($lines[3]);
		$txt = strstr($txt,"operation time (charge mode) ");
		if($txt == FALSE)
		{
			//Error in Data
			$o_time = "No Data";
			$o_state = "No Data";
			$mode = "No Data";
			$coe = "No Data";
			$error = "No Data";
			$msg = "error4, no operation time!";
			$data = array('efoy'=>array('volts'=>$volts,'amps'=>$amps,'o_time'=>$o_time,'o_state'=>$o_state,'mode'=>$mode,'coe'=>$coe,'error'=>$error,'msg'=>$msg));
			$sd_string = json_encode($data);
			return $sd_string;
		}
		$txt = substr($txt,29);
		$txt = substr($txt, 0, -1);
		$o_time = $txt;
		
		//Operation State
		$txt = trim($lines[4]);
		$txt = strstr($txt,"operating state: ");
		if($txt == FALSE)
		{
			//Error in Data
			$o_state = "No Data";
			$mode = "No Data";
			$coe = "No Data";
			$error = "No Data";
			$msg = "error5, no operating state!";
			$data = array('efoy'=>array('volts'=>$volts,'amps'=>$amps,'o_time'=>$o_time,'o_state'=>$o_state,'mode'=>$mode,'coe'=>$coe,'error'=>$error,'msg'=>$msg));
			$sd_string = json_encode($data);
			return $sd_string;
		}
		$txt = substr($txt,17);
		$o_state = $txt;
		
		//Operating Mode
		$txt = trim($lines[5]);
		$txt = strstr($txt,"operating mode: ");
		if($txt == FALSE)
		{
			//Error in Data
			$mode = "No Data";
			$coe = "No Data";
			$error = "No Data";
			$msg = "error6, no operating mode!";
			$data = array('efoy'=>array('volts'=>$volts,'amps'=>$amps,'o_time'=>$o_time,'o_state'=>$o_state,'mode'=>$mode,'coe'=>$coe,'error'=>$error,'msg'=>$msg));
			$sd_string = json_encode($data);
			return $sd_string;
		}
		$txt = substr($txt,15);
		$mode = $txt;
		
		//Cumulative Output Energy
		$txt = trim($lines[6]);
		$txt = strstr($txt,"cumulative output energy ");
		if($txt == FALSE)
		{
			//Error in Data
			$coe = "No Data";
			$error = "No Data";
			$msg = "error7, no cumulative output!";
			$data = array('efoy'=>array('volts'=>$volts,'amps'=>$amps,'o_time'=>$o_time,'o_state'=>$o_state,'mode'=>$mode,'coe'=>$coe,'error'=>$error,'msg'=>$msg));
			$sd_string = json_encode($data);
			return $sd_string;
		}
		$txt = substr($txt,24);
		$txt = substr($txt, 0, -1);
		$coe = $txt;
		
		//Error State
		$txt = trim($lines[7]);	
		$error = $txt;
		
		//Helpful Message
		$txt = trim($lines[8]);
		$txt = str_replace('l)','L)',$txt);
		$msg = $txt;
		
		//return $txt;

	}	
	else
	{
		$volts = "0";
		$amps = "0";
		$o_time = "No Data";
		$o_state = "No Data";
		$mode = "No Data";
		$coe = "No Data";
		$error = "No Data";
		$msg = "No Data";
	}
	$data = array('efoy'=>array('volts'=>$volts,'amps'=>$amps,'o_time'=>$o_time,'o_state'=>$o_state,'mode'=>$mode,'coe'=>$coe,'error'=>$error,'msg'=>$msg));
	$sd_string = json_encode($data);
	return $sd_string;
}



?>
