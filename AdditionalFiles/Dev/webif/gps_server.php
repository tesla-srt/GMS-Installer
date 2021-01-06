<?php


# this script shouldn't take more than a few seconds to run
set_time_limit(3);
ini_set('max_execution_time', 3);

$element = (isset($_GET["element"]) ? $_GET["element"] : ""); 
$ans = null;
switch ($element) {
case "pollgps":
  $ans = pollgps();
  break;

default:
  $ans = usage();
 }
 
//  send it back to the caller
print ($ans);

// ------------------------------------------------------------------------------------------

function usage()
	{
	print "Usage Example: http://10.10.10.10/gps_server.php?element=pollgps<BR>Call this file with element=xxxxx, Where xxxxx is one of the following:<br>";
	//print "For <br>";
	print "<pre>"; 
	print "pollgps";
	print "</pre>"; 
	}


function pollgps()
{
	//open RMS-100 database	
//	try
//	{
//	 $dbh = new PDO('sqlite:/etc/rms100.db');
//	}
//
//	catch(PDOException $e)
//	{
// 		echo $e->getMessage();
//	}
//
//	$sd_query = "SELECT device FROM device_mgr WHERE type='GPS';";
//	foreach ($dbh->query($sd_query) as $row) 
//	{
//  	$device = $row[0];
//	}
//
//	if(strlen($device)==0)
//	{
//		$device = "ttyUSB0";
//	}
	//echo $device;
	$server = 'localhost';
	$port = 2947;


	$sock = @fsockopen($server, $port, $errno, $errstr, 2);
	@fwrite($sock, "?POLL;\n");
	for($tries = 0; $tries < 10; $tries++)
	{
		$resp = @fread($sock, 1536); # SKY can be pretty big
		if (preg_match('/{"class":"POLL".+}/i', $resp, $m))
		{
			$resp = $m[0];
			break;
		}
	}
	@fclose($sock);
	if (!$resp)
	{
		$lat = "INVALID";
		$lon = "INVALID";
		$time = "INVALID";
		$alt = "INVALID";
		$track  = "INVALID";
		$speed  = "INVALID";
		$data = array('gpsdata'=>array('lat'=>"$lat",'lon'=>"$lon",'time'=>"$time",'alt'=>"$alt",'track'=>"$track",'speed'=>"$speed"));
		$sd_string = json_encode($data);
		return $sd_string;
	}

	//echo $resp;

	$GPS = json_decode($resp, true);
	if ($GPS['class'] != 'POLL')
	{
		die("json_decode error: $resp");
	}
		
	$lat = (float)$GPS['fixes'][0]['lat'];
	$lon = (float)$GPS['fixes'][0]['lon'];
	$time = $GPS['fixes'][0]['time'];
	$alt = (float)$GPS['fixes'][0]['alt'];
	$track  = (float)$GPS['fixes'][0]['track'];
	$speed  = (float)$GPS['fixes'][0]['speed'];

	$data = array('gpsdata'=>array('lat'=>"$lat",'lon'=>"$lon",'time'=>"$time",'alt'=>"$alt",'track'=>"$track",'speed'=>"$speed"));
	$sd_string = json_encode($data);
	return $sd_string;

}

?>
