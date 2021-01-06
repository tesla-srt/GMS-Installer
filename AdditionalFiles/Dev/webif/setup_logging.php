<?php
	error_reporting(E_ALL);
	include "lib.php";
	$hostname = trim(file_get_contents("/etc/hostname"));
	$dbh = new PDO('sqlite:/etc/rms100.db');
	$result  = $dbh->query("SELECT * FROM display_options;");			
	foreach($result as $row)
	{
		$screen_animations = $row['screen_animations'];
	}
	
	$alert_flag = "0";
	$ip = "";
	$cloud_username = "";
	$cloud_password = "";
	$rms_latitude = "";
	$rms_longitude = "";
	$update_interval = "";
	$enabled = "";
	
	$result  = $dbh->query("SELECT * FROM cloud;");			
	foreach($result as $row)
	{
		$enabled = $row['enabled'];
		$rms_latitude = $row['latitude'];
		$rms_longitude = $row['longitude'];
		$cloud_username = $row['username'];
		$cloud_password = $row['pass'];
		$update_interval = $row['update_interval'];
	}
	
	
	
/////////////////////////////////////////////////////////////////
//                                                             //
//                    GET PROCESSING                           //
//                                                             //
/////////////////////////////////////////////////////////////////
	
	
	$my_meth = $_SERVER['REQUEST_METHOD'];
	if($my_meth = "GET")
	{
		system("/sbin/rms-syslog.sh > /tmp/syslog");
		$f = fopen("/tmp/syslog", 'r');
		$line = trim(fgets($f));
		fclose($f);
		unlink("/tmp/syslog");
		if(strlen($line) !== 0)
		{
			$parts = explode("@",$line);
			$ip = $parts[1];
		}
	}

/////////////////////////////////////////////////////////////////
//                                                             //
//                    POST PROCESSING                          //
//                                                             //
/////////////////////////////////////////////////////////////////

// Strip illegal characters from $_POST data
$input_arr = array();
foreach ($_POST as $key => $input_arr)
{
  	$_POST[$key] = preg_replace("/[^a-zA-Z0-9\s!@#$%&*()_\-=+?.,:\/]/", "", $input_arr);
}

// Cancel Button	was clicked
if(isset ($_POST['cancel_btn']))
{
	header("Location: setup.php");
}

// Cancel Button	was clicked
if(isset ($_POST['cancel_btn2']))
{
	header("Location: setup.php");
}

	
// SysLog Save Button	was clicked
if(isset ($_POST['save_btn']))
{	
	$oldip = $_POST['oldip'];
	$ip = $_POST['serverip'];
	
	if(strlen($oldip) == 0)
	{
		//ADD Mode
		
		if (filter_var($ip, FILTER_VALIDATE_IP) === false) 
		{
    	$text = $ip." is not a valid IP address!";
    	$alert_flag = "2";
		} 
		else 
		{
    	$myFile = "/etc/syslog.conf";
			$fh = fopen($myFile, 'a');
			$stringData = "*.* @".$ip."\n";
			fwrite($fh, $stringData);
			fclose($fh);
			$alert_flag = "1";
		}
	}
	else
	{
		//EDIT Mode
		
		if(strlen($ip) == 0)
		{
			//DELETE ip
			system("sed -i '/*.* /d' /etc/syslog.conf");
			system("kill -HUP `cat /var/run/syslogd.pid`");
			$alert_flag = "1";
		}
		else if (filter_var($ip, FILTER_VALIDATE_IP) === false) 
		{
    	$text = $ip." is not a valid IP address!";
    	$alert_flag = "2";
		} 
		else 
		{
    	system("sed -i '/*.* /d' /etc/syslog.conf");
    	$myFile = "/etc/syslog.conf";
			$fh = fopen($myFile, 'a');
			$stringData = "*.* ".$ip."\n";
			fwrite($fh, $stringData);
			fclose($fh);
			system("kill -HUP `cat /var/run/syslogd.pid`");
			$alert_flag = "1";
		}
	}
}
	
// Cloud Save Button	was clicked
if(isset ($_POST['cloud_save_btn']))
{	
	$cloud_username = $_POST['cloud_username'];
	$cloud_password = $_POST['cloud_password'];
	$rms_latitude = $_POST['rms_latitude'];
	$rms_longitude = $_POST['rms_longitude'];
	$update_interval = $_POST['update_interval'];
	if(isset ($_POST['enabled']))
	{
		$enabled = "CHECKED";
	}
	else
	{
		$enabled = "UNCHECKED";
	}
	
	$query = sprintf("UPDATE cloud SET enabled='%s', latitude='%s', longitude='%s', username='%s', pass='%s', update_interval='%s';",$enabled, $rms_latitude, $rms_longitude, $cloud_username,$cloud_password,$update_interval);
	//echo $query;
	$result  = $dbh->exec($query);
	
	if($enabled == "CHECKED")
	{
		copy("/etc/init.scripts/S83rmscloud","/etc/init.d/S83rmscloud");
		exec("/etc/init.scripts/S83rmscloud restart");
	}
	else
	{
		unlink("/etc/init.d/S83rmscloud");
		exec("/etc/init.scripts/S83rmscloud stop");
	}

	$alert_flag = "1";
}



	
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <!-- Page title -->
    <title><?php echo $hostname; ?></title>
    <link rel="shortcut icon" type="image/ico" href="rms300favicon.ico?<?php echo rand(); ?>" />

    <!-- CSS -->
    <link rel="stylesheet" href="css/fontawesome/css/font-awesome.css" />
    <link rel="stylesheet" href="css/animate.css" />
    <link rel="stylesheet" href="css/bootstrap.css" />
    <link rel="stylesheet" href="css/awesome-bootstrap-checkbox.css" />
    <link rel="stylesheet" href="css/sweetalert.css" />
    <link rel="stylesheet" href="css/ethertek.css">
    
    <!-- Java Scripts -->
		<script src="javascript/jquery.min.js"></script>
		<script src="javascript/bootstrap.min.js"></script>
		<script src="javascript/sweetalert.min.js"></script>
		<script src="javascript/conhelp.js"></script>
		<script src="javascript/ethertek.js"></script>
		<script language="javascript" type="text/javascript">
			SetContext('remotesyslog');
		</script>
		<script>
			function showPassword()
			{
  			var showPasswordCheckBox = document.getElementById("en");
  			if(showPasswordCheckBox.checked)
  			{
        	document.getElementById("cloud_password").type="PASSWORD";
  			}
  			else
  			{
      		document.getElementById("cloud_password").type="TEXT";
  			}
			}
		</script>
		
</head>
<body class="fixed-navbar fixed-sidebar">
<div class='splash'> <div class='solid-line'></div><div class='splash-title'><h1>Loading... Please Wait...</h1><div class='spinner'> <div class='rect1'></div> <div class='rect2'></div> <div class='rect3'></div> <div class='rect4'></div> <div class='rect5'></div> </div> </div> </div>

<!--[if lt IE 7]>
<p class="alert alert-danger">You are using an <strong>old</strong> web browser. Please upgrade your browser to improve your experience.</p>
<![endif]-->

<?php start_header(); ?>

<?php left_nav("setup"); ?>
<script language="javascript" type="text/javascript">
	SetContext('remotesyslog');
</script>
<!-- Main Wrapper -->
<div id="wrapper">
	
	<?php
		if($screen_animations == "CHECKED")
		{
			echo '<div class="content animate-panel" data-effect="fadeInUp">';
		}
		else
		{
			echo '<div class="content">';
		}
	?>
  	<!-- INFO BLOCK START -->
  	<form name='Logging' action='setup_logging.php' method='post' class="form-horizontal">
  		<fieldset>
  			<div class="row">
  	  		<div class="col-sm-12">
  	  	  	<div class="hpanel4">
  	  	  		<div class="panel-body" style="max-width:400px">	
  	  	  	  	<legend><img src="images/remotesyslog.gif"> Remote System Log</legend> 
  	  	  	  	<div class="form-group"><label class="col-sm-4 control-label" style="text-align:left; max-width:140px; min-width:140px">Syslog Server IP:</label>
  	  	      		<div class="col-sm-12" style="max-width:200px">
  	  	      			<input type="text" class="form-control" name='serverip' value='<?php echo $ip; ?>' />
  	  	      		</div>
  	  	      	</div>	    	    	
  	  	      	
  	  	      	<div class="row">
  	  	      		<div class="col-sm-12">
  	  	      			<input type="hidden" name="oldip" value="<?php echo $ip; ?>">
  	  	      	  	<button name="save_btn" class="btn btn-success" type="submit" onMouseOver="mouse_move(&#039;b_apply&#039;);" onMouseOut="mouse_move();"><i class="fa fa-check" ></i> Save</button>
  	  	      	  	<button name="cancel_btn" class="btn btn-primary" type="submit" onMouseOver="mouse_move(&#039;b_cancel&#039;);" onMouseOut="mouse_move();" formnovalidate><i class="fa fa-times"></i> Cancel</button>
  	  	      	  </div>
  	  	      	</div>  
  	  	      	<br>
  	  	  		</div> <!-- END PANEL BODY --> 
  	  	  	</div> <!-- END PANEL WRAPPER --> 
  	  	  </div>  <!-- END COL --> 
  	  	</div> <!-- END ROW -->
  	  	<br>
  	  	<div class="row">
  	  		<div class="col-sm-12">
  	  	  	<div class="hpanel4">
  	  	  		<div class="panel-body" style="max-width:400px">  	
  	  	  	  	<legend><img src="images/cloud.gif"> Cloud Setup</legend> 
  	  	  	  	
  	  	  	  	<div class="form-group"><label class="col-sm-4 control-label" style="text-align:left; max-width:140px; min-width:140px">Cloud Username:</label>
  	  	      		<div class="col-sm-12" style="max-width:200px">
  	  	      			<input type="text" class="form-control" name="cloud_username" value="<?php echo $cloud_username; ?>" required />
  	  	      		</div>
  	  	      	</div>	    	    	
  	  	      	
  	  	      	<div class="form-group"><label class="col-sm-4 control-label" style="text-align:left; max-width:140px; min-width:140px">Cloud Password:</label>
  	  	      		<div class="col-sm-12" style="max-width:200px">
  	  	      			<input type="password" class="form-control" name="cloud_password" id="cloud_password" value="<?php echo $cloud_password; ?>" required />
  	  	      		</div>
  	  	      	</div>	
  	  	      	
  	  	      	<div class="form-group"><label class="col-sm-4 control-label" style="text-align:left; max-width:140px; min-width:140px">RMS Latitude:</label>
  	  	      		<div class="col-sm-12" style="max-width:200px">
  	  	      			<input type="text" class="form-control" name="rms_latitude" value="<?php echo $rms_latitude; ?>" onMouseOver="mouse_move(&#039;cloud_lat&#039;);" onMouseOut="mouse_move();" required />
  	  	      		</div>
  	  	      	</div>
  	  	      	
  	  	      	<div class="form-group"><label class="col-sm-4 control-label" style="text-align:left; max-width:140px; min-width:140px">RMS Longitude:</label>
  	  	      		<div class="col-sm-12" style="max-width:200px">
  	  	      			<input type="text" class="form-control" name="rms_longitude" value="<?php echo $rms_longitude; ?>" onMouseOver="mouse_move(&#039;cloud_lon&#039;);" onMouseOut="mouse_move();" required />
  	  	      		</div>
  	  	      	</div>
  	  	      	
  	  	      	<div class="form-group"><label class="col-sm-4 control-label" style="text-align:left; max-width:140px; min-width:140px">Update Interval:</label>
  	  	      		<div class="col-sm-12" style="max-width:200px">
    				   			<select class="form-control input-sm" style="min-width:120px;max-width:120px;" name="update_interval">
    				   				<?php
												for($ii=1; $ii<60; $ii++)	{	if($update_interval==($ii*60)) 		{$chan = "selected";} else {$chan = " ";} if($ii==1){echo"<option " . $chan . " value='" . $ii*60 . "'>" . $ii . " Minute</option>";}else{echo"<option " . $chan . " value='" . $ii*60 . "'>" . $ii . " Minutes</option>";}	}
												for($ii=1; $ii<25; $ii++)	{ if($update_interval==($ii*60*60)) {$chan = "selected";} else {$chan = " ";} if($ii==1){echo"<option " . $chan . " value='" . $ii*60*60 . "'>" . $ii . " Hour</option>";}else{echo"<option " . $chan . " value='" . $ii*60*60 . "'>" . $ii . " Hours</option>";}	}
    				   				?>
    				   			</select>
    				   		</div>
  	  	      	</div>
  	  	      	
  	  	      	<div class="form-group"><label class="col-sm-4 control-label" style="text-align:left; max-width:140px; min-width:140px">Enabled:</label>
  	  	      		<div class="col-sm-12" style="max-width:200px">
  	  	      			<div class="checkbox checkbox-success">
                			<input type="checkbox" id="enabled" name="enabled" value="1" <?php echo $enabled; ?> />
                  		<label for="enabled"></label>
                		</div>
                	</div>
                </div>
  	  	      	
  	  	      	<div class="row">
  	  	      		<div class="col-sm-12">
  	  	      	  	<button name="cloud_save_btn" class="btn btn-success" type="submit" onMouseOver="mouse_move(&#039;b_apply&#039;);" onMouseOut="mouse_move();"><i class="fa fa-check" ></i> Save</button>
  	  	      	  	<button name="cancel_btn2" class="btn btn-primary" type="submit" onMouseOver="mouse_move(&#039;b_cancel&#039;);" onMouseOut="mouse_move();" formnovalidate><i class="fa fa-times"></i> Cancel</button>
  	  	      	  </div>
  	  	      	</div>  
  	  	      	<br>
  	  	      	<div class="row">
              		<div class="checkbox checkbox-success">
                    <input type="checkbox" id="en" name="en" onclick="showPassword()"; checked> />
                    <label for="en">Hide Cloud Password</label>
                  </div>
                </div><br><br>
                
                <div class="row">
                  </div>
                </div><br><br>
		  	
  	  	  		</div> <!-- END PANEL BODY --> 
  	  	  	</div> <!-- END PANEL WRAPPER --> 
  	  	  </div>  <!-- END COL --> 
  	  	</div> <!-- END ROW --> 
  	  </fieldset>
  	</form> 
  </div> <!-- END CONTENT -->    
</div> <!-- END Main Wrapper -->
<?php 
if($alert_flag == "1")
{
echo"<script>";
echo"swal({";
echo"  title:'Success!',";
echo"  text: 'Settings Saved!',";
echo"  type: 'success',";
echo"  showConfirmButton: false,";
echo"  timer: 2000";
echo"});";
echo"</script>";
}

if($alert_flag == "2")
{
echo"<script>";
echo"swal({";
echo"  title:'Error!',";
echo"  text: '" . $text . "',";
echo"  type: 'error',";
echo"  showConfirmButton: false,";
echo"	 html: true,";
echo"  timer: 2500";
echo"});";
echo"</script>";
}


?>
</body>
</html> 
