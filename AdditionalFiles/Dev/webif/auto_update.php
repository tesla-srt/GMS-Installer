<?php
include "lib.php";

$hostname = trim(file_get_contents("/etc/hostname"));
echo "<!DOCTYPE html>";
echo "<html>";
echo "<head>";
echo "  <meta charset='utf-8'>";
echo "  <meta name='viewport' content='width=device-width, initial-scale=1.0'>";
echo "  <meta http-equiv='X-UA-Compatible' content='IE=edge'>";
echo "  <!-- Page title -->";
echo "  <title>". $hostname . "</title>";
$myrand = rand();
echo "  <link rel='shortcut icon' type='image/ico' href='rms100favicon.ico?".$myrand."' />";
echo "  <!-- CSS -->";
echo "  <link rel='stylesheet' href='css/fontawesome/css/font-awesome.css' />";
echo "  <link rel='stylesheet' href='css/animate.css' />";
echo "  <link rel='stylesheet' href='css/bootstrap.css' />";
echo "	<link rel='stylesheet' href='css/awesome-bootstrap-checkbox.css' />";
echo "  <link rel='stylesheet' href='css/sweetalert.css' />";
echo "  <link rel='stylesheet' href='css/ethertek.css'>";
echo "  <!-- Java Scripts -->";
echo "	<script src='javascript/jquery.min.js'></script>";
echo "	<script src='javascript/bootstrap.min.js'></script>";
echo "	<script src='javascript/sweetalert.min.js'></script>";
echo "	<script src='javascript/conhelp.js'></script>";
echo "	<script src='javascript/ethertek.js'></script>";
echo "	<script language='javascript' type='text/javascript'>";
echo "		SetContext('setup');";
echo "	</script>";
echo "</head>";
echo "<body class='fixed-navbar fixed-sidebar'>";
echo "	<div class='splash'> <div class='solid-line'></div><div class='splash-title'><h1>Updating Firmware...</h1><div class='spinner'> <div class='rect1'></div> <div class='rect2'></div> <div class='rect3'></div> <div class='rect4'></div> <div class='rect5'></div> </div> </div> </div>";
echo "	<!--[if lt IE 7]>";
echo "	<p class='alert alert-danger'>You are using an <strong>old</strong> web browser. Please upgrade your browser to improve your experience.</p>";
echo "	<![endif]-->";
start_header();
left_nav("setup");
echo "<script language='javascript' type='text/javascript'>";
echo "	SetContext('setup');";
echo "</script>";
echo "<!-- Main Wrapper -->\n";
echo "<div id='wrapper'>\n";
echo "	<div class='row'>\n";
echo "		<div class='col-sm-12'>\n";
echo "			<div class='hpanel4'>\n";
echo "     		<div class='panel-body'>\n";


// Auto Update Check
if(isset ($_POST['autoupdate']) || isset ($_GET['autoupdate']))
{
	echo "Checking for new firmware...<BR><BR>";

	$url = 'https://remotemonitoringsystems.ca/rms100/downloads/current_firmware_version';
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,0);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_FAILONERROR,true);
	$data = curl_exec($ch);
	
	if($data === false)
	{
    echo 'Connection Error: ' . curl_error($ch) . ' aborting.<br><br>';
    exit(0);
	}
	curl_close($ch);

	$build = trim(file_get_contents("/etc/BUILDNUM"));
	$build = explode("=",$build);
	if($data <= $build[1])
	{
		echo "No update available...<br><br>";
		exit(0);	
	}
	
	echo "New Firmware Available... Getting Filename...<br>";
	
	$url = 'https://remotemonitoringsystems.ca/rms100/downloads/0new_firmware_version';
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,0);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_FAILONERROR,true);
	$filename = curl_exec($ch);
	
	if($filename === false)
	{
    echo 'Connection Error: ' . curl_error($ch) . ' aborting.<br><br>';
    exit(0);
	}
	
	echo $filename . "<br><br>";
	
	curl_close($ch);
	
	echo "Getting File size...<br>";
	
	$url = 'https://remotemonitoringsystems.ca/rms100/downloads/' . $filename;

  $ch = curl_init($url);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_HEADER, true);
  curl_setopt($ch, CURLOPT_NOBODY, true);
  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,0);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  $data = curl_exec($ch);
  $size = curl_getinfo($ch, CURLINFO_CONTENT_LENGTH_DOWNLOAD);

	if($data === false)
	{
    echo 'File Size Error: ' . curl_error($ch) . ' aborting.<br><br>';
    exit(0);
	}
	curl_close($ch);
	
	echo "File Size = " . $size . " bytes.<br><br>";
	
	//DOWNLOAD FILE
	
	echo "Downloading...<br>";
	echo "<div id='size'>File Size = 0 bytes.</div>";
	echo "<br><br>";
	echo "<p id='reboot'></p>";
	echo "<br><br>";
	echo "<p id='msg'></p>";
	echo "<br><br>";
	
	
	$curl_command = sprintf("curl -m 600 -k -s -o /data/%s '%s' > /dev/null 2>&1 &",$filename,$url);
	exec ($curl_command);

	echo "<script>\n";
	echo "var tval;\n";
	echo "var theCount = 90;\n";
	echo "var myRandom;\n";

	echo "	$(document).ready(function()\n";
	echo "		{\n";
	echo "			update();\n";
	echo "		}\n";
	echo "  );\n";
	
	echo "	function update()\n";
	echo "	{\n";
	echo "		tval = setTimeout(update,2000);\n";
	echo "	  myRandom = parseInt(Math.random()*999999999);\n";
	echo "    $.getJSON('sdserver.php?element=get_fw_filesize&filename=" . $filename . "&rand=' + myRandom,\n";
	echo "    function(data)\n";
	echo "    	{\n";
	echo "				var fsize = data.size;\n";
	echo "      	$('#size').replaceWith(\"<div id='size'>File Size = \" + fsize + \" bytes.</div>\");\n";
	echo "        if(fsize == " . $size . ")\n";
	echo "        {\n";
	echo "           clearTimeout(tval);\n";
	echo "           reboot();\n";
	echo "        }\n";
	echo "			}\n";
	echo "  	);\n";
	echo "	}\n";
	
	echo "	function reboot()\n";
	echo "	{\n";
	echo "  	document.getElementById('reboot').innerHTML = 'Updating Firmware.. Please wait...';\n";
	echo "	  myRandom = parseInt(Math.random()*999999999);\n";
	echo "    $.getJSON('sdserver.php?element=update&filename=" . $filename . "&rand=' + myRandom,\n";
	echo "    function(data)\n";
	echo "    	{\n";
	echo "				var msg = data.msg;\n";
	echo "        if(msg == 'OK')\n";
	echo "        {\n";
	echo "  				document.getElementById(\"msg\").innerHTML = \"The RMS-100 will be back online in <span id='time' style='color:blue'></span> seconds.<br><br>Please wait for the Timer to run out before attempting to connect...<br><br>\"\n";
	echo "					cntDown();\n";
	echo "        }\n";
	echo "				else\n";
	echo "				{\n";
	echo "  				document.getElementById('reboot').innerHTML = 'Firmware Update Fail...';\n";           
	echo "				}\n";
	echo "			}\n";
	echo "  	);\n";
	echo "	}\n";
	
	echo "	function cntDown()\n";
	echo "	{\n";
	echo "		setTimeout (cntDown,1000);";
	echo "  	document.getElementById('time').innerHTML = theCount;";
	echo "    theCount--;";
	echo "    if(theCount <= 0)";
	echo "			{";
	echo "        window.location.replace('index.php');";
	echo "			}";
	echo "	}\n";
	echo "</script>\n";
	echo "</body>\n";
	echo "</html>\n";

}
