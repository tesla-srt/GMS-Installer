<?php
include "lib.php";

$alert_flag = "0";
$text = "";

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
echo "	<script src='javascript/jquery-ui.min.js'></script>";
echo "	<script src='javascript/bootstrap.min.js'></script>";
echo "	<script src='javascript/sweetalert.min.js'></script>";
echo "	<script src='javascript/conhelp.js'></script>";
echo "	<script src='javascript/ethertek.js'></script>";
echo "	<script language='javascript' type='text/javascript'>";
echo "		SetContext('setup');";
echo "	</script>";
echo "</head>";
echo "<body class='fixed-navbar fixed-sidebar'>";
echo "	<div class='splash'> <div class='solid-line'></div><div class='splash-title'><h1>Restoring Graph Database...</h1><div class='spinner'> <div class='rect1'></div> <div class='rect2'></div> <div class='rect3'></div> <div class='rect4'></div> <div class='rect5'></div> </div> </div> </div>";
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
echo "		<div class='col-lg-12'>\n";
echo "			<div class='hpanel4'>\n";
echo "     		<div class='panel-body'>\n";
$target_path = "/data/";

$target_path = $target_path . basename( $_FILES['rrdfile']['name']); 

if(move_uploaded_file($_FILES['rrdfile']['tmp_name'], $target_path)) 
{
   echo "The file <b>".  basename( $_FILES['rrdfile']['name']). 
   " </b>has been uploaded<BR><BR>";
    
   echo "Starting RRD Graph Database Restore...<BR><BR>"; 
   
   // check for rrd file
   $pos = strpos(basename( $_FILES['rrdfile']['name']), ".rrd"); 
   if ($pos === false) 
   	{
    	echo "<span style='color:red'>Not an RRD Graph database file! Aborting...</span><BR>";
    	system("rm /data/". basename( $_FILES['rrdfile']['name']));
    	$text = "Not an RRD Graph database file!";
			$alert_flag = "1";
			goto escape_hatch;
		} 
  
  echo "Validating uploaded file....<BR>";
  
  //check filesize
  $size = filesize("/data/". basename( $_FILES['rrdfile']['name']));
  echo "File Size: ".$size."<br>";
  if($size != 1265376)
	{
  	echo '<span style="color:red"><b>File size is bad! Aborting ...</b></span><BR><BR>';
   	$text = "File size is bad!";
		$alert_flag = "1";
		goto escape_hatch;
	} 
	echo 'File size check... <span style="color:green"><b>[PASSED]</b></span><BR><BR>';
  
  echo "Shutting down rmsrrd daemon ... ";
	system("/etc/init.scripts/S91rmsrrdd stop > /dev/null");
	//fflush(stdout);
	sleep(1);
	echo "<span style='color:green'><strong>[OK]</strong></span><BR>";
	echo "Backing up existing RRD database ... ";
	system("mv /data/rrd/rms.rrd /data/rrd/rms.rrd.old");
	//fflush(stdout);
	sleep(1);
	echo "<span style='color:green'><strong>[OK]</strong></span><BR>";
	echo "Copy uploaded RRD database to /data/rrd/rms.rrd ... ";
	system("mv /data/rms.rrd /data/rrd/rms.rrd");
	//fflush(stdout);
	sleep(1);
	system("chmod 666 /data/rrd/rms.rrd");
	echo "<span style='color:green'><strong>[OK]</strong></span><BR>";
	echo "Removing ram graph files ... ";
	system("rm /data/rrd/tmp/*");
	//fflush(stdout);
	sleep(1);
	echo "<span style='color:green'><strong>[OK]</strong></span><BR>";
	echo "RRD database restore complete ... <BR>";
	echo "Restarting rmsrrd daemon ... ";
	system("/etc/init.scripts/S91rmsrrdd start > /dev/null");
	echo "<span style='color:green'><strong>[OK]</strong></span><br><br>";
	
	$alert_flag = "2";
	
//	//fflush(stdout);
  
  escape_hatch:
    		
	 //$sdcommand = "/sbin/wrootfsupgrade " . basename( $_FILES['rootfsfile']['name']);
	 //system($sdcommand);
} 

else
{
	$text = "There was an error uploading the file, please try again!";
	$alert_flag = "1";
}

if($alert_flag == "1")
{
	echo"<script>";
	echo"swal({";
	echo"  title:'Error!',";
	echo"  text: '".$text."',";
	echo"  type: 'error',";
	echo"  showConfirmButton: false,";
	echo"	 html: true,";
	echo"  timer: 2500";
	echo"});";
	echo"setTimeout(function() {document.location.href='setup_graph_options.php'}, 2000);";
	echo"</script>";
}

if($alert_flag == "2")
{
echo"<script>";
echo"swal({";
echo"  title:'Success!',";
echo"  text: 'RRD Graph Database Restored!',";
echo"  type: 'success',";
echo"  showConfirmButton: false,";
echo"	 html: true,";
echo"  timer: 2500";
echo"});";
echo"setTimeout(function() {document.location.href='setup.php'}, 2000);";
echo"</script>";
}

echo "					</div><!-- PANEL BODY -->\n";
echo "				</div><!-- PANEL -->\n";
echo "			</div><!-- END COL-LG-12 -->\n";
echo "		</div> <!-- END ROW -->\n";
echo "	</div> <!-- END Main Wrapper -->\n";
?>
</body>
</html>  
