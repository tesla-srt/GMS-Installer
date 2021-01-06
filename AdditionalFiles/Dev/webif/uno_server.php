<?				/* ---------------- ARDUINO UNO SERVER For RMS-100 (C) 2014 ETHERTEK CIRCUITS ---------------- */


# this script shouldn't take more than a few seconds to run
set_time_limit(3);
ini_set('max_execution_time', 3);

$element = (isset($_GET["element"]) ? $_GET["element"] : ""); 
$ans = null;
switch ($element) {
case "info":
  $ans = info();
  break;

default:
  $ans = usage();
 }
 
//  send it back to the caller
print ($ans);

// ------------------------------------------------------------------------------------------

function usage()
	{
	print "Usage Example: http://10.10.10.10/uno_server.php?element=info<BR>Call this file with element=xxxxx, Where xxxxx is one of the following:<br>";
	//print "For <br>";
	print "<pre>"; 
	print "info";
	print "</pre>"; 
	}


function info()
{
	if (file_exists("/var/rmsdata/unodat")) 
		{
			$file = fopen("/var/rmsdata/unodat","r");
			$unodatraw = fgets($file);
			fclose($file);
			$parts = explode("|", $unodatraw);
			
			$io2dir=$parts[1];
			$io2state=$parts[2];
			$io3dir=$parts[3];
			$io3state=$parts[4];
			$io4dir=$parts[5];
			$io4state=$parts[6];
			$io5dir=$parts[7];
			$io5state=$parts[8];
			$io6dir=$parts[19];
			$io6state=$parts[10];
			$io7dir=$parts[11];
			$io7state=$parts[12];
			$io8dir=$parts[13];
			$io8state=$parts[14];
			$io9dir=$parts[15];
			$io9state=$parts[16];
			$io10dir=$parts[17];
			$io10state=$parts[18];
			$io11dir=$parts[19];
			$io11state=$parts[20];
			$io12dir=$parts[21];
			$io12state=$parts[22];
			$io13dir=$parts[23];
			$io13state=$parts[24];
			$vm1=$parts[25];
			$vm2=$parts[26];
			$vm3=$parts[27];
			$vm4=$parts[28];
			$vm5=$parts[29];
			$vm6=$parts[30];
			
			if($io2state=="0"){$io2state="low";}else{$io2state="high";}
			if($io3state=="0"){$io3state="low";}else{$io3state="high";}
			if($io4state=="0"){$io4state="low";}else{$io4state="high";}
			if($io5state=="0"){$io5state="low";}else{$io5state="high";}
			if($io6state=="0"){$io6state="low";}else{$io6state="high";}
			if($io7state=="0"){$io7state="low";}else{$io7state="high";}
			if($io8state=="0"){$io8state="low";}else{$io8state="high";}
			if($io9state=="0"){$io9state="low";}else{$io9state="high";}
			if($io10state=="0"){$io10state="low";}else{$io10state="high";}
			if($io11state=="0"){$io11state="low";}else{$io11state="high";}
			if($io12state=="0"){$io12state="low";}else{$io12state="high";}
			if($io13state=="0"){$io13state="low";}else{$io13state="high";}
		}
	else
		{
			
		}		

	$data = array('unodata'=>array('io2dir'=>"$io2dir",'io2state'=>"$io2state",'io3dir'=>"$io3dir",'io3state'=>"$io3state",'io4dir'=>"$io4dir",'io4state'=>"$io4state",'io5dir'=>"$io5dir",'io5state'=>"$io5state",'io6dir'=>"$io6dir",'io6state'=>"$io6state",'io7dir'=>"$io7dir",'io7state'=>"$io7state",'io8dir'=>"$io8dir",'io8state'=>"$io8state",'io9dir'=>"$io9dir",'io9state'=>"$io9state",'io10dir'=>"$io10dir",'io10state'=>"$io10state",'io11dir'=>"$io11dir",'io11state'=>"$io11state",'io12dir'=>"$io12dir",'io12state'=>"$io12state",'io13dir'=>"$io13dir",'io13state'=>"$io13state"));
	$sd_string = json_encode($data);
	return $sd_string;

}

?>
