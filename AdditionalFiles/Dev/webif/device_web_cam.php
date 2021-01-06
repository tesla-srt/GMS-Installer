<?php
	error_reporting(E_ALL);
	include "lib.php";
	$hostname = trim(file_get_contents("/etc/hostname"));
	$alert_flag = "0";
	$ip =  $_SERVER['SERVER_ADDR'];
	$daemon_status = "off";
	$daemon_running = "Not Running";
	
	system("ps | grep /bin/mjpg_streamer > /tmp/ps.txt");
	$thesize = filesize("/tmp/ps.txt");
	unlink("/tmp/ps.txt");
	
	if($thesize > 120)
	{
		$daemon_status = "on";
		$daemon_running = "Running";
	}
	else
	{
		$daemon_status = "on";
		$daemon_running = "Running";
	}
	
	$dbh = new PDO('sqlite:/etc/rms100.db');
	
	//RELAY ACTION CONFIRMATION
	$result  = $dbh->query("SELECT * FROM relayconf;");
	foreach($result as $row)
	{
		$relay_confirmation = $row['confirmation'];
	}
	if($relay_confirmation == "1")
	{
		$relay_confirmation = "checked";
	}
	else
	{
		$relay_confirmation = "";
	}
	
	//RELAY 1
	$result  = $dbh->query("SELECT * FROM relays WHERE id='1';");
	foreach($result as $row)
	{
		$rly1_name = $row['name'];
		$rly1_notes = $row['notes'];
		$rly1_notes = str_replace(array("\r\n", "\r", "\n"), "<br>", $rly1_notes);
		$rly1_nc_color = $row['nc_color'];
		$rly1_no_color = $row['no_color'];
	}
	
	//RELAY 2
	$result  = $dbh->query("SELECT * FROM relays WHERE id='2';");
	foreach($result as $row)
	{
		$rly2_name = $row['name'];
		$rly2_notes = $row['notes'];
		$rly2_notes = str_replace(array("\r\n", "\r", "\n"), "<br>", $rly2_notes);
		$rly2_nc_color = $row['nc_color'];
		$rly2_no_color = $row['no_color'];
	}
	
	$result  = $dbh->query("SELECT * FROM relay_script_cmds WHERE command='00';");
	foreach($result as $row)
	{
		$NO1 = $row['state'];
	}
	
	$result  = $dbh->query("SELECT * FROM relay_script_cmds WHERE command='01';");
	foreach($result as $row)
	{
		$NC1 = $row['state'];
	}
	
	$result  = $dbh->query("SELECT * FROM relay_script_cmds WHERE command='02';");
	foreach($result as $row)
	{
		$NO2 = $row['state'];
	}
	
	$result  = $dbh->query("SELECT * FROM relay_script_cmds WHERE command='03';");
	foreach($result as $row)
	{
		$NC2 = $row['state'];
	}
	
	
/////////////////////////////////////////////////////////////////
//                                                             //
//                    GET PROCESSING                           //
//                                                             //
/////////////////////////////////////////////////////////////////
if(isset ($_GET['relay']))
	{
		$relay_num = $_GET['relay'];
		$command = $_GET['command'];
		
		if($relay_confirmation == "checked")
		{
			$alert_flag = "5";
		}
		else
		{
			$command = "rmsrelay ".$relay_num." ".$command;
			exec($command);
		}
	}
	
	if(isset ($_GET['execute']))
	{
		$execute = $_GET['execute'];
		if($execute == "yes")
		{
			$rly = $_GET['relay'];
			$cmd = $_GET['command'];
			$sd = "rmsrelay ".$rly." ".$cmd;
			exec($sd);
			$text = "Relay Command Executed!";
			$alert_flag = "2";
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
	header("Location: setup.php?context=setup");
}

	
$relay1 = trim(file_get_contents("/var/rmsdata/relay1"));
$relay2 = trim(file_get_contents("/var/rmsdata/relay2"));
	



	
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
		</script>
		<script language="javascript" type="text/javascript" src="javascript/functions.js"></script>
		<script language="javascript">

      function send_command(cmd) {
        document.getElementById('hints').firstChild.nodeValue = "Send command: " + cmd;
        AJAX_get('/?action=command&command='+ cmd)
      }

      function AJAX_response(text) {
      if(text < 0)
      	{
      		msg="Command Failed";
      	}
      else
      	{
      		msg="Command OK"
      	}
        document.getElementById('hints').firstChild.nodeValue = "Got response: " + msg;
      }

      function KeyDown(ev) {
        ev = ev || window.event;
        pressed = ev.which || ev.keyCode;

        switch (pressed) {
          case 37:
              send_command('pan_plus');
            break;
          case 39:
              send_command('pan_minus');
            break;
          case 38:
              send_command('tilt_minus');
            break;
          case 40:
              send_command('tilt_plus');
            break;
          case 32:
              send_command('reset_pan_tilt');
          break;
          default:
              break;
        }
      }

      document.onkeydown = KeyDown;
			
    </script>

<script type="text/javascript">

/* Copyright (C) 2007 Richard Atterer, richard©atterer.net
   This program is free software; you can redistribute it and/or modify it
   under the terms of the GNU General Public License, version 2. See the file
   COPYING for details. */

var imageNr = 0; // Serial number of current image
      var finished = new Array(); // References to img objects which have finished downloading
      var paused = false;
      var previous_time = new Date();

      function createImageLayer() 
      {
        var img = new Image();
        img.style.position = "absolute";
        img.style.zIndex = -1;
        img.onload = imageOnload;
        img.onclick = imageOnclick;
        img.width = 512;
        img.height = 384;
        img.src = "/?action=snapshot&n=" + (++imageNr);
        var webcam = document.getElementById("webcam");
        webcam.insertBefore(img, webcam.firstChild);
      }

			// Two layers are always present (except at the very beginning), to avoid flicker
      function imageOnload() 
      {
        this.style.zIndex = imageNr; // Image finished, bring to front!
        while (1 < finished.length) 
        {
          var del = finished.shift(); // Delete old image(s) from document
          del.parentNode.removeChild(del);
        }
        finished.push(this);
        current_time = new Date();
        delta = current_time.getTime() - previous_time.getTime();
        fps   = (1000.0 / delta).toFixed(3);
        document.getElementById('info').firstChild.nodeValue = delta + " ms (" + fps + " fps)";
        previous_time = current_time;
        createImageLayer();
      }

			function imageOnclick() 
			{ // Clicking on the image will pause the stream
  			paused = !paused;
  			if (!paused) createImageLayer();
			}

		function display_relays ()
		{
		        var myRandom = parseInt(Math.random()*999999999);
		        $.getJSON('sdserver.php?element=relays&rand=' + myRandom,
		            function(data)
		            {
										setTimeout (display_relays, 1000);
		                
		                  if (data.rly.r1 == 1)
		                  {
		                  	$('#relay1').replaceWith("<div id='relay1'><a href='device_web_cam.php?relay=1&command=no'><img src='images/sdnc.gif' onMouseOver='mouse_move(&#039;b_relays_relaycontrol_on&#039;);'	onMouseOut='mouse_move();'></a></div>");
		                  	$('#r1N').replaceWith("<div id='r1N'><span style='color:" + data.rly.r1nc_color + "'>" + data.rly.r1NC + "</span></div>");
		                  }
		                  else
		                  {
		                    $('#relay1').replaceWith("<div id='relay1'><a href='device_web_cam.php?relay=1&command=nc'><img src='images/sdno.gif' onMouseOver='mouse_move(&#039;b_relays_relaycontrol_off&#039;);'	onMouseOut='mouse_move();'></a></div>");
		                  	$('#r1N').replaceWith("<div id='r1N'><span style='color:" + data.rly.r1no_color + "'>" + data.rly.r1NO + "</span></div>");
		                  }
											
											if (data.rly.r2 == 1)
		                  {
		                  	$('#relay2').replaceWith("<div id='relay2'><a href='device_web_cam.php?relay=2&command=no'><img src='images/sdnc.gif' onMouseOver='mouse_move(&#039;b_relays_relaycontrol_on&#039;);'	onMouseOut='mouse_move();'></a></div>");
		                  	$('#r2N').replaceWith("<div id='r2N'><span style='color:" + data.rly.r2nc_color + "'>" + data.rly.r2NC + "</span></div>");
		                  }
		                  else
		                  {
		                    $('#relay2').replaceWith("<div id='relay2'><a href='device_web_cam.php?relay=2&command=nc'><img src='images/sdno.gif' onMouseOver='mouse_move(&#039;b_relays_relaycontrol_off&#039;);'	onMouseOut='mouse_move();'></a></div>");
		                  	$('#r2N').replaceWith("<div id='r2N'><span style='color:" + data.rly.r2no_color + "'>" + data.rly.r2NO + "</span></div>");
		                  }
												
		            }
		        );
		}

	
		display_relays ();

</script>
</head>
<body class="fixed-navbar fixed-sidebar" onload="createImageLayer();">
<div class='splash'> <div class='solid-line'></div><div class='splash-title'><h1>Loading... Please Wait...</h1><div class='spinner'> <div class='rect1'></div> <div class='rect2'></div> <div class='rect3'></div> <div class='rect4'></div> <div class='rect5'></div> </div> </div> </div>

<!--[if lt IE 7]>
<p class="alert alert-danger">You are using an <strong>old</strong> web browser. Please upgrade your browser to improve your experience.</p>
<![endif]-->

<?php start_header(); ?>

<?php left_nav("camera"); ?>
<script language="javascript" type="text/javascript">
	SetContext('setup');
</script>
<!-- Main Wrapper -->
<div id="wrapper">
	<div class="content animate-panel" data-effect="fadeInUp">
  	<!-- INFO BLOCK START -->
  	<div class="row">
    	<div class="col-sm-12">
      	<div class="hpanel4">
      		<div class="panel-body" style="min-width:479px; max-width:580px">
      	  	<form name='jcam' action='device_web_cam.php' method='post' class="form-horizontal">  	
      	    	<fieldset>
      	    		<legend><img src="images/webcam32x32.gif"> RMS-100 J-Cam</legend> 
      	    		<div>
      	    			Start or Stop the Camera Server in the  <a href="setup_services.php"><u>Service Manager.</u></a>
      	    			<img src="images/serv<?php echo $daemon_status;?>.gif" width="16" height="16"> 
      	    			Camera Server <?php echo $daemon_running; ?>. 
      	    		</div>
      	    		<div id="webcam">
      	    			<iframe width="520" height="560" frameBorder="0" src="http://<?php echo $ip; ?>:7070"></iframe>
      	    			<noscript><img src="/?action=snapshot" width="512px" height="384px" /></noscript>
      	    		</div>
 
								<div class="table-responsive">
            			<table style="width:100%;">
              			<thead>
              		  	<tr>
              		    	<th colspan="2" style="text-align:center; background-color:#D6DFF7;"><span style="color:black">Multi-Purpose Power Relays</span></th>
              		  	</tr>
              		  </thead>
              		  <tr>
              		  	<td style="text-align:center;width:50%;"><a href="device_web_cam.php?relay=1" onMouseOver="Tip('<?php echo $rly1_notes; ?>',TITLE,'Relay 1 - <?php echo $rly1_name; ?>',TITLEBGCOLOR,'#000000',BGCOLOR,'#ffff33');" onMouseOut="UnTip();"><span class="an">RELAY 1</span></a></td>
              		    <td style="text-align:center;width:50%;"><a href="device_web_cam.php?relay=2" onMouseOver="Tip('<?php echo $rly2_notes; ?>',TITLE,'Relay 2 - <?php echo $rly2_name; ?>',TITLEBGCOLOR,'#000000',BGCOLOR,'#ffff33');" onMouseOut="UnTip();"><span class="an">RELAY 2</span></a></td>
              		  </tr>
              		  <tr>
              		  	
              		  		<?php
              		  			echo "<td style='text-align:center;width:50%;'>";
              		  			if($relay1 == "1")
              		  			{
              		  				echo "<div id='relay1'><a href='device_web_cam.php?relay=1&command=no'><img src='images/sdnc.gif'></a></div>";
              		  			}
              		  			else
              		  			{
              		  				echo "<div id='relay1'><a href='device_web_cam.php?relay=1&command=nc'><img src='images/sdno.gif'></a></div>";
              		  			}
              		  			echo "</td>";
              		  			
              		  			echo "<td style='text-align:center;width:50%;'>";
              		  			if($relay2 == "1")
              		  			{
              		  				echo "<div id='relay2'><a href='device_web_cam.php?relay=2&command=no'><img src='images/sdnc.gif'></a></div>";
              		  			}
              		  			else
              		  			{
              		  				echo "<div id='relay2'><a href='device_web_cam.php?relay=2&command=nc'><img src='images/sdno.gif'></a></div>";
              		  			}
              		  			echo "</td>";
              		  		?>
              		  		
              		    
              		  </tr>
              		  <tr>
              		  	<td style="text-align:center;width:50%;"><a href="device_web_cam.php?relay=1" onMouseOver="Tip('<?php echo $rly1_notes; ?>',TITLE,'Relay 1 - <?php echo $rly1_name; ?>',TITLEBGCOLOR,'#000000',BGCOLOR,'#ffff33');" onMouseOut="UnTip();"><span style="color:blue"><?php echo $rly1_name; ?></span></a></td>
              		    <td style="text-align:center;width:50%;"><a href="device_web_cam.php?relay=2" onMouseOver="Tip('<?php echo $rly2_notes; ?>',TITLE,'Relay 2 - <?php echo $rly2_name; ?>',TITLEBGCOLOR,'#000000',BGCOLOR,'#ffff33');" onMouseOut="UnTip();"><span style="color:blue"><?php echo $rly2_name; ?></span></a></td>
              		  </tr>
              		  <tr>
              		  	<?php
              		  		echo "<td style='text-align:center;width:50%;'>";
              		  		if($relay1 == "1")
              		  		{
              		  			echo "<div id='r1N'><span style='color:" . $rly1_nc_color . "'>" . $NC1 . "</span></div>";
              		  		}
              		  		else
              		  		{
              		  			echo "<div id='r1N'><span style='color:" . $rly1_no_color . "'>" . $NO1 . "</span></div>";
              		  		}
              		  		echo "</td>";
              		  		
              		  		echo "<td style='text-align:center;width:50%;'>";
              		  		if($relay2 == "1")
              		  		{
              		  			echo "<div id='r2N'><span style='color:" . $rly2_nc_color . "'>" . $NC2 . "</span></div>";
              		  		}
              		  		else
              		  		{
              		  			echo "<div id='r2N'><span style='color:" . $rly2_no_color . "'>" . $NO2 . "</span></div>";
              		  		}
              		  		echo "</td>";
              		  		
              		  	?>
              		  	</tr>
              		</table>
								</div>	
              </fieldset>  
						</form>
      		</div> <!-- END PANEL BODY --> 
      	</div> <!-- END PANEL WRAPPER --> 
      </div>  <!-- END COL --> 
    </div> <!-- END ROW --> 
  </div> <!-- END CONTENT -->    
</div> <!-- END Main Wrapper -->
<script type="text/javascript" src="javascript/wz_tooltip.js"></script>
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
echo"  title:'Success!',";
echo"  text: '" . $text . "',";
echo"  type: 'success',";
echo"  showConfirmButton: false,";
echo"	 html: true,";
echo"  timer: 1000";
echo"});";
echo"</script>";
}

if($alert_flag == "5")
{
	echo"<script>";
	echo"	swal({";
	echo"		title: 'Execute Relay Action<br>Are you really sure?',";
	echo"		type: 'warning',";
	echo"		showCancelButton: true,";
	echo"		html: true,";
	echo"		confirmButtonColor: '#DD6B55',";
	echo"		confirmButtonText: 'Yes, execute!',";
	echo"		closeOnConfirm: false";
	echo"	},";
	echo"	function(){";
	echo"		window.location.href = 'device_web_cam.php?execute=yes&relay=" . $relay_num . "&command=".$command."';";
	echo"	});";
	echo"</script>";
}


?>
</body>
</html> 
