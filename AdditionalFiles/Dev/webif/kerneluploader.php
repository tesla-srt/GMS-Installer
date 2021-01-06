<?php
include "lib.php";

// Cancel Button	was clicked
if(isset ($_POST['cancel_btn']))
{
	header("Location: setup.php");
}



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
echo "  <link rel='shortcut icon' type='image/ico' href='rms100favicon.ico?".$myrand."' />\n"; 		
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
echo "		function startTimer(duration, display) {";
echo "    var timer = duration, minutes, seconds;";
echo "    var handle = setInterval(function () {";
echo "     minutes = parseInt(timer / 60, 10);";
echo "        seconds = parseInt(timer % 60, 10);";
echo "        minutes = minutes < 10 ? '0' + minutes : minutes;";
echo "        seconds = seconds < 10 ? '0' + seconds : seconds;";
echo "        display.textContent = minutes + ':' + seconds;";
echo "        if (--timer < 0) {";
echo "            timer = 0;";
echo "            clearInterval(handle);";
echo "            window.location.replace('index.php');";
echo "        }";
echo "    }, 1000);";
echo "}";

echo "window.onload = function () {";
echo "    var startTime = 45,";
echo "        display = document.querySelector('#time');";
echo "    startTimer(startTime, display);";
echo "};";
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

$target_path = "/data/";

$target_path = $target_path . basename( $_FILES['kernelfile']['name']); 

if(move_uploaded_file($_FILES['kernelfile']['tmp_name'], $target_path)) 
	{
   echo "The file <b>".  basename( $_FILES['kernelfile']['name']). 
   " </b>has been uploaded<BR><BR>";
    
   echo "Starting Kernel Upgrade...<BR><BR>"; 
   
   // check for kernel uImage file
   $pos = strpos(basename( $_FILES['kernelfile']['name']), "uImage-"); 
   if ($pos === false) 
   	{
    	echo "Not a Kernel uImage file! Aborting...<BR>";
    	system("rm /data/". basename( $_FILES['kernelfile']['name']));
    	return;
		} 
    
    //check filesize
    $size = filesize("/data/". basename( $_FILES['kernelfile']['name']));
    if($size != 4194304)
    {
    	echo '<span style="color:red"><b>File size is bad! Aborting ...</b></span><BR><BR>';
    	return;
    } 
    echo 'File size check... <span style="color:green"><b>[PASSED]</b></span><BR><BR>';
		
		echo "The RMS-100 will be back online in <span id='time'style='color:blue'></span> seconds.<br><br>Please wait for the Timer to run out before attempting to connect...<br><br>";
		
		$sdcommand = "/sbin/wkernelupgrade " . basename( $_FILES['kernelfile']['name']) . "  > /dev/null 2>&1 &";
		
		//echo $sdcommand;
		
		
		exec($sdcommand);
	
	} 

else
{
 
echo"<script>";
echo"swal({";
echo"  title:'Error!',";
echo"  text: 'There was an error uploading the file, please try again!',";
echo"  type: 'error',";
echo"  showConfirmButton: false,";
echo"	 html: true,";
echo"  timer: 2500";
echo"});";
echo"setTimeout(function() {document.location.href='setup_firmware.php'}, 2000);";
echo"</script>";
 
    
}
echo "					</div><!-- PANEL BODY -->\n";
echo "				</div><!-- PANEL -->\n";
echo "			</div><!-- END COL-SM-12 -->\n";
echo "		</div> <!-- END ROW -->\n";
echo "	</div> <!-- END Main Wrapper -->\n";
?>
</body>
</html> 