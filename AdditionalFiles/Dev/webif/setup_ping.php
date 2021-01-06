<?php
	error_reporting(E_ALL);
	include "lib.php";
	$hostname = trim(file_get_contents("/etc/hostname"));
	$alert_flag = "0";
	$id = "0";
	$sd_status = "";
	$daemon_status = "";
	$daemon_running = "";
	
	$dbh = new PDO('sqlite:/etc/rms100.db');
	
	$result  = $dbh->query("SELECT * FROM display_options;");			
	foreach($result as $row)
	{
		$screen_animations = $row['screen_animations'];
	}
	
	if(file_exists("/var/run/rmspingd.pid"))
	{
		$daemon_status = "on";
		$daemon_running = "Running";
	}
	else
	{
		$daemon_status = "off";
		$daemon_running = "Not Running";
	}
	
	
	
		
/////////////////////////////////////////////////////////////////
//                                                             //
//                    GET PROCESSING                           //
//                                                             //
/////////////////////////////////////////////////////////////////

if(isset ($_GET['action']))
{
	$action = $_GET['action'];
	if($action == "edit")
	{
		$success = $_GET['success'];
		if($success = "yes")
		{
			$myid = $_GET['id'];
			$text = "Ping Target # " . $myid . " Updated";		   						
			$alert_flag = "2";
		}	
	}
}

if(isset ($_GET['pingtarget']))
{
	$action = $_GET['pingtarget'];
	if($action == "stop")
	{
		$myid = $_GET['sid'];
		$query = sprintf("UPDATE ping_targets SET en='off' WHERE id=%s;",$myid);
		$result  = $dbh->exec($query);
 		system("kill -HUP `cat /var/run/rmspingd.pid`");
		$text = "Ping Target # " . $myid . " Stopped";		   						
		$alert_flag = "2";
	}
	else if($action == "run")
	{
		$myid = $_GET['sid'];
		$query = sprintf("UPDATE ping_targets SET en='on' WHERE id=%s;",$myid);
		$result  = $dbh->exec($query);
 		system("kill -HUP `cat /var/run/rmspingd.pid`");
		$text = "Ping Target # " . $myid . " Started";		   						
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
	header("Location: setup.php");
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
			SetContext('ping');
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
	SetContext('ping');
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
  		<div class="col-md-12"><legend>Ping Targets</legend></div>
  	</div>
  	<div class="row">
  		<div class="col-md-1"><img src='images/ping2.gif'></div>
  	</div>
		<div class="row">
  		<div class="col-md-12">
  			<p><br>
  				<b>Start or Stop the RMS-100 ping daemon in the <a href='setup_services.php'><u class="dotted">service manager.</u></a>
  				&nbsp;&nbsp;&nbsp;<img src="images/serv<?php echo $daemon_status; ?>.gif" width="16" height="16">&nbsp;&nbsp;&nbsp;Ping Monitor service <?php echo $daemon_running; ?>.
  				</b>
  			</p>
  		</div>
  	</div>
  	<form name='Ping' action='setup_ping.php' method='post' class="form-horizontal">  	
    	<fieldset>
  			<div class="row">
    			<div class="col-md-12">
    		  	<div class="hpanel3">
    		  	  <div class="panel-body" style="text-align:center; background:#F1F3F6;border:none;">
    				  	<div class="table-responsive">
    				   		<table width="100%" class="table table-striped table-condensed table-hover">
    				   			<thead>
    				   				<tr>
    				   					<th width="5%" style="background:#ABBEEF; border: 1px solid white;">
    				   						<div style="text-align:center">Enabled</div>
    				   					</th>
    				   					<th width="5%" style="background:#D6DFF7; border: 1px solid white;">
    				   						<div style="text-align:center">ID</div>
    				   					</th>
    				   					<th width="10%" style="background:#D6DFF7; border: 1px solid white;">
    				   						<div>Target</div>
    				   					</th>
    				   					<th width="60%" style="background:#D6DFF7; border: 1px solid white;">
    				   						<div>Description</div>
    				   					</th>
    				   					<th width="20%" style="background:#D6DFF7; border: 1px solid white;">
    				   						<div style="text-align:left">Actions</div>
    				   					</th>
    				   				</tr>
    				   			</thead>
    				   			<tbody>
    				   				<?php
												$result  = $dbh->query("SELECT * FROM ping_targets ORDER BY id");
												foreach($result as $row)
												{
													$id = $row['id'];
													$ip = $row['ip'];
													$notes = $row['notes'];
													$en = $row['en'];
													
    				   						if($en == "on")
	    										{
	    											if($daemon_status == "on")
	    											{
	    												$sd_status = "ok";
	    											}
	    											else
	    											{
	    												$sd_status = "servwarn";
	    											}
	    										}
													else
													{
														$sd_status = "off";
													}	
    				   				  	
    				   						echo "<tr><td>";
    				   						echo "<img src='images/" . $sd_status . ".gif' width='16' height='16'>";
    				   						echo "</td><td><u><a href='setup_ping_edit.php?pingtarget=edit&sid=" . $id . "'</u>" . $id . "</a></td><td style='text-align:left'><u class='dotted'><a href='setup_ping_edit.php?pingtarget=edit&sid=" . $id . "' onMouseOver='mouse_move(\"sd_pingtarget_edit\");' onMouseOut='mouse_move();'>" . $ip . "</u></a></td><td style='text-align:left'>" . $notes . "</td>";
    				   						echo "<td style='text-align:left'><a href='setup_ping.php?pingtarget=stop&sid=" . $id . "' onMouseOver ='mouse_move(\"sd_pingtarget_stop\");'	onMouseOut='mouse_move();'>";
    				   						echo "<img  src='images/stop.gif' width='16' height='16' title='STOP'></a>";
    				   						echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href='setup_ping.php?pingtarget=run&sid=" . $id . "' onMouseOver='mouse_move(\"sd_pingtarget_start\");'	onMouseOut='mouse_move();'>";
    				   						echo "<img  src='images/on.gif' width='16' height='16' title='START'></a></td></tr>";
    				   					}	
    				   				?>
    				   			</tbody>
  								</table>      	    
    		  	  	</div> <!-- END TABLE RESPONSIVE -->
    		  	  	<div class="form-group">
        					<div class="col-sm-1">
        						<button name="cancel_btn" class="btn btn-primary" type="submit" onMouseOver="mouse_move(&#039;b_cancel&#039;);" onMouseOut="mouse_move();" formnovalidate><i class="fa fa-times"></i> Cancel</button>
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









 
















