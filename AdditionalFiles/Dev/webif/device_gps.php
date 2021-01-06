<?php
	error_reporting(E_ALL);
	include "lib.php";
	$hostname = trim(file_get_contents("/etc/hostname"));
	$alert_flag = "0";
	
	$dbh = new PDO('sqlite:/etc/rms100.db');
	$result  = $dbh->query("SELECT * FROM display_options;");			
	foreach($result as $row)
	{
		$screen_animations = $row['screen_animations'];
	}
	

	
/////////////////////////////////////////////////////////////////
//                                                             //
//                    GET PROCESSING                           //
//                                                             //
/////////////////////////////////////////////////////////////////


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

//Reset Button was clicked.
if(isset($_POST["gps_reset_btn"]))
{
	system("/etc/init.d/S96gpsd stop > /dev/null");
	sleep(1);
	system("/etc/init.d/S96gpsd start > /dev/null");
	sleep(1);	
	$pid_file = "/var/run/gpsd.pid";
	$alert_flag = "1";
}
	
if (file_exists("/var/run/gpsd.pid"))
{
	$daemon_status = "on";
	$daemon_running = "Running";
}
else
{
	$daemon_status = "off";
	$daemon_running = "Not Running";
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
    <link rel="shortcut icon" type="image/ico" href="rms100favicon.ico?<?php echo rand(); ?>" />

    <!-- CSS -->
    <link rel="stylesheet" href="css/fontawesome/css/font-awesome.css" />
    <link rel="stylesheet" href="css/animate.css" />
    <link rel="stylesheet" href="css/bootstrap.css" />
    <link rel="stylesheet" href="css/sweetalert.css" />
    <link rel="stylesheet" href="css/ethertek.css">
    
    <!-- Java Scripts -->
		<script src="javascript/jquery.min.js"></script>
		<script src="javascript/bootstrap.min.js"></script>
		<script src="javascript/sweetalert.min.js"></script>
		<script src="javascript/conhelp.js"></script>
		<script src="javascript/ethertek.js"></script>
		<script language="javascript" type="text/javascript">
			SetContext('setup');
			

		function display_gps()
		{
		     var myRandom = parseInt(Math.random()*999999999);
		     $.getJSON('gps_server.php?element=pollgps&rand=' + myRandom,
		     function(data)
		      {
		         $.each (data.gpsdata, function (k, v) { $('#' + k).text (v); });
		         setTimeout (display_gps, 5000);
		         $('#timestr').replaceWith("<div class='col-sm-8' id='timestr'><input type='text' class='form-control' value='" + data.gpsdata.time + "' /></div>");
		         $('#latstr').replaceWith("<div class='col-sm-8' id='latstr'><input type='text' class='form-control' value='" + data.gpsdata.lat + "' /></div>"); 
		         $('#lonstr').replaceWith("<div class='col-sm-8' id='lonstr'><input type='text' class='form-control' value='" + data.gpsdata.lon + "' /></div>"); 
		         $('#altstr').replaceWith("<div class='col-sm-8' id='altstr'><input type='text' class='form-control' value='" + data.gpsdata.alt + "' /></div>");
		         $('#trackstr').replaceWith("<div class='col-sm-8' id='trackstr'><input type='text' class='form-control' value='" + data.gpsdata.track + "' /></div>");
		         $('#speedstr').replaceWith("<div class='col-sm-8' id='speedstr'><input type='text' class='form-control' value='" + data.gpsdata.speed + "' /></div>");
		         $('#googlemap').replaceWith("<div class='col-sm-8' id='googlemap'><a href='http://maps.google.com/maps?q=" + data.gpsdata.lat + "," + data.gpsdata.lon + "'><u>Click for Google Maps</u></a></div>");  
		      }
		    );
		}
		display_gps();

		</script>
</head>
<body class="fixed-navbar fixed-sidebar">
<div class='splash'> <div class='solid-line'></div><div class='splash-title'><h1>Loading... Please Wait...</h1><div class='spinner'> <div class='rect1'></div> <div class='rect2'></div> <div class='rect3'></div> <div class='rect4'></div> <div class='rect5'></div> </div> </div> </div>


<!--[if lt IE 7]>
<p class="alert alert-danger">You are using an <strong>old</strong> web browser. Please upgrade your browser to improve your experience.</p>
<![endif]-->

<?php start_header(); ?>

<?php left_nav("gps"); ?>
<script language="javascript" type="text/javascript">
	SetContext('setup');
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
  	<div class="row">
    	<div class="col-sm-12">
      	<div class="hpanel4">
      		<div class="panel-body" style="max-width:500px">
      	  	<form name='jcam' action='device_gps.php' method='post' class="form-horizontal">  	
      	    	<fieldset>
      	    		<legend><img src="images/gps32x32.gif"> RMS-300 GPS</legend> 
      	    		<div>
      	    			Start or Stop the GPS Server in the  <a href="setup_services.php"><u>Service Manager.</u></a>
      	    			<img src="images/serv<?php echo $daemon_status;?>.gif" width="16" height="16"> 
      	    			GPS Server <?php echo $daemon_running; ?>. 
      	    		</div>
      	    		<br>
      	    		<div class="form-group"><label class="col-sm-3 control-label">UTC Time:</label>  			
              		<div class="col-sm-4" id="timestr" style="min-width:150px; max-width:150px"><input type='text' class='form-control' value='' /></div>
              	</div>
              	<div class="form-group"><label class="col-sm-3 control-label">Longitude:</label>
              		<div class="col-sm-8" style="min-width:150px; max-width:150px" id='lonstr'>0</div>
              	</div>
              	<div class="form-group"><label class="col-sm-3 control-label">Latitude:</label>
              		<div class="col-sm-8" id='latstr'>0</div>
              	</div>
              	<div class="form-group"><label class="col-sm-3 control-label">Altitude:</label>
              		<div class="col-sm-8" id='altstr'>0</div>
              	</div>
              	<div class="form-group"><label class="col-sm-3 control-label">Track:</label>
              		<div class="col-sm-8" id='trackstr'>0</div>
              	</div>
              	<div class="form-group"><label class="col-sm-3 control-label">Speed in Knots:</label>
              		<div class="col-sm-8" style="min-width:150px; max-width:150px" id='speedstr'>0</div>
              	</div>
              	<div class="form-group">
              		<div class="col-sm-8 col-sm-offset-3">
              	  	<button name="gps_reset_btn" class="btn btn-success" type="submit" onMouseOver="mouse_move(&#039;sd_gps_reset&#039;);" onMouseOut="mouse_move();"><i class="fa fa-refresh " ></i> Reset GPS</button>
              	  </div>
              	</div>
              	<br>
              	<div id='googlemap'><a href='http://maps.google.com/maps?q=0,0'><u>Click for Google Maps</u></a></div>
              	
              </fieldset>  
						</form>
      		</div> <!-- END PANEL BODY --> 
      	</div> <!-- END PANEL WRAPPER --> 
      </div>  <!-- END COL --> 
    </div> <!-- END ROW --> 
  </div> <!-- END CONTENT -->    
</div> <!-- END Main Wrapper -->
<?php 
if($alert_flag == "1")
{
echo"<script>";
echo"swal({";
echo"  title:'Success!',";
echo"  text: 'GPS Daemon Reset!',";
echo"  type: 'success',";
echo"  showConfirmButton: false,";
echo"  timer: 2000";
echo"});";
echo"</script>";
}
?>
</body>
</html> 
