<?php
	error_reporting(E_ALL);
	include "lib.php";
	$hostname = trim(file_get_contents("/etc/hostname"));
	$alert_flag = "0";
	$result = "";
	$sd_status = "";
	$daemon_status = "";
	$daemon_running = "";
	$sdhtml = "";
	$myid = "0";
	
	$dbh = new PDO('sqlite:/etc/rms100.db');
	
	$result  = $dbh->query("SELECT * FROM display_options;");			
	foreach($result as $row)
	{
		$screen_animations = $row['screen_animations'];
	}
	
	if(file_exists("/var/run/rmstimerd.pid"))
	{
		$daemon_status = "on";
		$daemon_running = "Running";
	}
else
	{
		$daemon_status = "off";
		$daemon_running = "Not Running";
	}
	
	
	
//	$pageWasRefreshed = isset($_SERVER['HTTP_CACHE_CONTROL']) && $_SERVER['HTTP_CACHE_CONTROL'] === 'max-age=0';
//	if($pageWasRefreshed ) 
//	{
//   	//page was refreshed;
//   	goto escape_hatch;
//	}
	
		
/////////////////////////////////////////////////////////////////
//                                                             //
//                    GET PROCESSING                           //
//                                                             //
/////////////////////////////////////////////////////////////////
	
if(isset($_GET['timers']))
	{
		$action = $_GET['timers'];
		if($action == "stop")
		{
			$myid = $_GET['id'];
			$query = sprintf("UPDATE timers SET en='off' WHERE id=%s;",$myid);
			$result  = $dbh->exec($query);
 			system("kill -HUP `cat /var/run/rmstimerd.pid`");
			$text = "Timer # " . $myid . " Stopped";		   						
			$alert_flag = "2";
		}
		else if($action == "run")
		{
			$myid = $_GET['id'];
			$query = sprintf("UPDATE timers SET en='on',firstrun='1' WHERE id=%s;",$myid);
			$result  = $dbh->exec($query);
 			system("kill -HUP `cat /var/run/rmstimerd.pid`");
			$text = "Timer # " . $myid . " Started";		   						
			$alert_flag = "2";
		}
		else
		{
			echo "This web page must be accessed through the RMS web interface!";
			exit(0);
		}
	}
	
	if(isset ($_GET['action']))
	{
		$action = $_GET['action'];
		if($action == "edit")
		{
			$success = $_GET['success'];
			if($success = "yes")
			{
				$myid = $_GET['id'];
				$text = "Timer # " . $myid . " Updated";		   						
				$alert_flag = "2";
			}	
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



escape_hatch:
	
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
			SetContext('timers');
		</script>
		<script language="javascript" type="text/javascript">

			function display_timers()
			{
			        var myRandom = parseInt(Math.random()*999999999);
			        $.getJSON('sdserver.php?element=get_timers&rand=' + myRandom,
			              function(data)
			              {
			                 setTimeout (display_timers, 1000);
			                 $('#timer1').replaceWith("<div id='timer1' style='color:" + data.timers.t1c + "'><u class='dotted'>" + data.timers.t1 + "</u></div>");
			                 $('#timer2').replaceWith("<div id='timer2' style='color:" + data.timers.t2c + "'><u class='dotted'>" + data.timers.t2 + "</u></div>");
			                 $('#timer3').replaceWith("<div id='timer3' style='color:" + data.timers.t3c + "'><u class='dotted'>" + data.timers.t3 + "</u></div>");
			                 $('#timer4').replaceWith("<div id='timer4' style='color:" + data.timers.t4c + "'><u class='dotted'>" + data.timers.t4 + "</u></div>");
			                 $('#timer5').replaceWith("<div id='timer5' style='color:" + data.timers.t5c + "'><u class='dotted'>" + data.timers.t5 + "</u></div>");
			                 $('#timer6').replaceWith("<div id='timer6' style='color:" + data.timers.t6c + "'><u class='dotted'>" + data.timers.t6 + "</u></div>");
			                 $('#timer7').replaceWith("<div id='timer7' style='color:" + data.timers.t7c + "'><u class='dotted'>" + data.timers.t7 + "</u></div>");
			                 $('#timer8').replaceWith("<div id='timer8' style='color:" + data.timers.t8c + "'><u class='dotted'>" + data.timers.t8 + "</u></div>");
			                 $('#timer9').replaceWith("<div id='timer9' style='color:" + data.timers.t9c + "'><u class='dotted'>" + data.timers.t9 + "</u></div>");
			                 $('#timer10').replaceWith("<div id='timer10' style='color:" + data.timers.t10c + "'><u class='dotted'>" + data.timers.t10 + "</u></div>");
			                 $('#timer11').replaceWith("<div id='timer11' style='color:" + data.timers.t11c + "'><u class='dotted'>" + data.timers.t11 + "</u></div>");
			                 $('#timer12').replaceWith("<div id='timer12' style='color:" + data.timers.t12c + "'><u class='dotted'>" + data.timers.t12 + "</u></div>");
			                 $('#timer13').replaceWith("<div id='timer13' style='color:" + data.timers.t13c + "'><u class='dotted'>" + data.timers.t13 + "</u></div>");
			                 $('#timer14').replaceWith("<div id='timer14' style='color:" + data.timers.t14c + "'><u class='dotted'>" + data.timers.t14 + "</u></div>");
			                 $('#timer15').replaceWith("<div id='timer15' style='color:" + data.timers.t15c + "'><u class='dotted'>" + data.timers.t15 + "</u></div>");
			              }
			        );
			}
			
			display_timers();
		</script>
	
</head>
<body class="fixed-navbar fixed-sidebar">
	
<!--[if lt IE 7]>
<p class="alert alert-danger">You are using an <strong>old</strong> web browser. Please upgrade your browser to improve your experience.</p>
<![endif]-->

<?php start_header(); ?>

<?php left_nav("setup"); ?>
<script language="javascript" type="text/javascript">
	SetContext('timers');
</script>
<!-- Main Wrapper -->
<div id="wrapper">
	
	<?php
		if($screen_animations == "CHECKED")
		{
			echo '<div class="content animate-panel" data-effect="fadeInRightBig">';
		}
		else
		{
			echo '<div class="content">';
		}
	?>
  	<!-- INFO BLOCK START -->
  	<div class="row">
  		<div class="col-sm-12"><legend><img src='images/timers.gif'> Timers</legend></div>
  	</div>
  	<div class="row">
  		<div class="col-sm-12">
  			<p><br>
  				<b>Start or Stop the RMS-100 timer daemon in the <a href='setup_services.php'><u class="dotted">service manager.</u></a>
  				&nbsp;&nbsp;&nbsp;<img src="images/serv<?php echo $daemon_status; ?>.gif" width="16" height="16">&nbsp;&nbsp;&nbsp;Timer service <?php echo $daemon_running; ?>.
  				</b>
  			</p>
  		</div>
  	</div>
  	<form name='Timers' action='setup_timers.php' method='post' class="form-horizontal">  	
    	<fieldset>
  			<div class="row">
    			<div class="col-sm-12">
    		  	<div class="hpanel3">
    		  	  <div class="panel-body" style="text-align:center; background:#F1F3F6;border:none;">
    				  	<div class="table-responsive">
    				   		<table width="100%" class="table table-striped table-condensed table-hover">
    				   			<thead>
    				   				<tr>
    				   					<th width="5%" style="background:#ABBEEF; border: 1px solid white;">
    				   						<div style="text-align:center">Status</div>
    				   					</th>
    				   					<th width="5%" style="background:#D6DFF7; border: 1px solid white;">
    				   						<div style="text-align:center">ID</div>
    				   					</th>
    				   					<th width="10%" style="background:#D6DFF7; border: 1px solid white;">
    				   						<div style="text-align:center">Time Left</div>
    				   					</th>
    				   					<th width="20%" style="background:#D6DFF7; border: 1px solid white;">
    				   						<div style="text-align:left">Name</div>
    				   					</th>
    				   					<th width="25%" style="background:#D6DFF7; border: 1px solid white;">
    				   						<div style="text-align:left">Description</div>
    				   					</th>
    				   					<th width="25%" style="background:#D6DFF7; border: 1px solid white;">
    				   						<div style="text-align:left">Command to Run</div>
    				   					</th>
    				   					<th width="10%" style="background:#D6DFF7; border: 1px solid white;">
    				   						<div style="text-align:center">Actions</div>
    				   					</th>
    				   				</tr>
    				   			</thead>
    				   			<tbody>
    				   				<?php
    				   					$result  = $dbh->query("SELECT * FROM timers ORDER BY id");
												foreach($result as $row)
												{
													$id = $row['id'];
													$name = $row['name'];
													$notes = $row['notes'];
													$en = $row['en'];
													$cmds = $row['cmds'];
													$start_time = $row['start_time'];
													

													$tmp = "/tmp/timer".$id;
		  										if(file_exists($tmp))
		  										{
		  											$timer = file_get_contents($tmp);
		  										}
		  										else
		  										{
		  											if($start_time == "0")
		  											{
		  												$timer = "0";
		  											}
		  											else
		  											{
		  												$timer = $start_time;
		  											}
													}
													
													$theTime = secondsToTime($timer);
													$days = $theTime['d'];
													$hours = $theTime['h'];
													$minutes = $theTime['m'];
													$seconds = $theTime['s'];
    				   						$tmp = sprintf("%02d:%02d:%02d:%02d",$days,$hours,$minutes,$seconds);			
													
    				   						if($en == "on")
	    										{
	    											$sdhtml = "green";
	    											if($daemon_status == "on")
	    											{
	    												$sd_status = "ok";
	    											}
	    											else
	    											{
	    												$sd_status = "servwarn";
	    											}
	    											
	    										}
													else{
														$sdhtml = "red";
														$sd_status = "off";
													}
													
													echo "<tr>";
													echo " <td style='text-align:center'>";
													echo "  <img src='images/".$sd_status.".gif' width='16' height='16'>";
													echo " </td>";
													echo " <td style='text-align:center'>";
													echo "  <a href='setup_timers_edit.php?timers=edit&id=".$id."'><u>".$id."</u></a>";
													echo " </td>";
													echo " <td style='text-align:center'>";
													echo "  <a href='setup_timers_edit.php?timers=edit&id=".$id."'>";
													echo "   <div id='timer".$id."' style='color:red'><u class='dotted'>".$tmp."</u></div>";
													echo " 	</a>";
													echo " </td>";
													echo " <td style='text-align:left'>".$name."</td>";
													echo " <td style='text-align:left'>".$notes."</td>";
													echo " <td style='text-align:left'>".$cmds."</td>";
													echo " <td><a href='setup_timers.php?timers=stop&id=".$id."' onMouseOver ='mouse_move(\"sd_timers_stop\");' onMouseOut='mouse_move();'>";
													echo "  <img src='images/stop.gif' width='16' height='16' title='STOP'></a>";												
													echo "  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
													echo "  <a href='setup_timers.php?timers=run&id=".$id."' onMouseOver ='mouse_move(\"sd_timers_start\");'	onMouseOut='mouse_move();'>";
													echo "  <img src='images/on.gif' width='16' height='16' title='START'></a>";
													echo " </td>";
													echo "</tr>";
													
													
	
    				   					}
    				   				
    				   				
    				   				?>
    				   			</tbody>
  								</table>      	    
    		  	  	</div> <!-- END TABLE RESPONSIVE -->
    		  	  	
    		  	  	<div class="form-group">
              		<div class="col-sm-12" style="text-align:left">
              	  	<button name="refresh_btn" class="btn btn-success " type="submit" onMouseOver="mouse_move(&#039;b_refresh&#039;);" onMouseOut="mouse_move();"><i class="fa fa-check"></i> Refresh</button>
              	  	<button name="cancel_btn" class="btn btn-primary " type="submit" onMouseOver="mouse_move(&#039;b_cancel&#039;);" onMouseOut="mouse_move();"><i class="fa fa-times"></i> Cancel</button>
              	  </div>
              	</div>
    		  	  	
    		  	  	
    		  	  	
    		  	  	
    		  	  	
    		  	  	
    		  	  	
    		  		</div> <!-- END PANEL BODY --> 
    		  	</div> <!-- END HPANEL3 --> 
    		  </div> <!-- END COL-MD-12 --> 
    		</div> <!-- END ROW --> 	
    	</fieldset>
    </form>			
  </div> <!-- END CONTENT -->    
</div> <!-- END Main Wrapper -->



<?php 

if($alert_flag == "2")
{
echo"<script>";
echo"swal({";
echo"  title:'Success!',";
echo"  text: '" . $text . "',";
echo"  type: 'success',";
echo"  showConfirmButton: false,";
echo"	 html: true,";
echo"  timer: 2500";
echo"});";
echo"</script>";
}

echo "</body>";
echo "</html>";

?>







 
















