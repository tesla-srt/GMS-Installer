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
	
	$log = "0";
	
/////////////////////////////////////////////////////////////////
//                                                             //
//                    GET PROCESSING                           //
//                                                             //
/////////////////////////////////////////////////////////////////
if(isset ($_GET['show_log']))
{
	if($_GET['show_log'] == "system"){$log = "0";}
	if($_GET['show_log'] == "rmsd"){$log = "1";}
	if($_GET['show_log'] == "rmspingd"){$log = "2";}
	if($_GET['show_log'] == "rmstimerd"){$log = "3";}
	if($_GET['show_log'] == "webserver"){$log = "4";}
	if($_GET['show_log'] == "snmp"){$log = "5";}
	if($_GET['show_log'] == "netstat"){$log = "6";}
	if($_GET['show_log'] == "secure"){$log = "7";}
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
		<script src="javascript/jquery-ui.min.js"></script>
		<script src="javascript/bootstrap.min.js"></script>
		<script src="javascript/sweetalert.min.js"></script>
		<script src="javascript/conhelp.js"></script>
		<script src="javascript/ethertek.js"></script>
		<script language="javascript" type="text/javascript">
			SetContext('setup');
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
	SetContext('setup');
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
  		<div class="col-sm-12"><legend>Log Files</legend></div>
  	</div>
  	<form name='Logs' action='setup_logs.php' method='post' class="form-horizontal">  	
    	<fieldset>
  			<div class="row">
    			<div class="col-md-12">
    		  	<div class="hpanel3">
    		  	  <div class="panel-body" style="text-align:center; background:#F1F3F6;border:none;">
    				  	<div class="table-responsive">
    				   		<table cellpadding="1" cellspacing="1" width="70%">
    				   			<tbody>
    				    			<tr> 
    						    		<td width="12%">
    						    			<a href="setup_logs.php?show_log=system" onMouseOver="mouse_move(&#039;sd_system&#039;);" onMouseOut="mouse_move();">
    				  	    			<img src="images/btn_action-log_bg.gif"><br><span>System Log</span>
    				  	    			</a>
    						    		</td>
    		              	
    						    		<td width="12%">
    						    			<a href="setup_logs.php?show_log=rmsd" onMouseOver="mouse_move(&#039;sd_rmsd&#039;);" onMouseOut="mouse_move();">
    				  	    				<img src="images/btn_action-log_bg.gif"><br><span>RMSD Log</span>
    				  	    			</a>
    						    		</td>
    						    		
    						    		<td width="12%">
    						    			<a href="setup_logs.php?show_log=rmspingd" onMouseOver="mouse_move(&#039;sd_rmspingd&#039;);" onMouseOut="mouse_move();">
    				  	    				<img src="images/btn_action-log_bg.gif"><br><span>Ping Log</span>
    				  	    			</a>
    						    		</td>
    						    		
    						    		<td width="12%">
    						    			<a href="setup_logs.php?show_log=rmstimerd" onMouseOver="mouse_move(&#039;sd_rmstimerd&#039;);" onMouseOut="mouse_move();">
    				  	    				<img src="images/btn_action-log_bg.gif"><br><span>Timer Log</span>
    				  	    			</a>
    						    		</td>
    						    		
    						    		<td width="12%">
    						    			<a href="setup_logs.php?show_log=webserver" onMouseOver="mouse_move(&#039;sd_boa&#039;);" onMouseOut="mouse_move();">
    				  	    				<img src="images/btn_action-log_bg.gif"><br><span>Web Log</span>
    				  	    			</a>
    						    		</td>
    						    		
    						    		<td width="12%">
    						    			<a href="setup_logs.php?show_log=snmp" onMouseOver="mouse_move(&#039;sd_snmp&#039;);" onMouseOut="mouse_move();">
    				  	    				<img src="images/btn_action-log_bg.gif"><br><span>SNMP Log</span>
    				  	    			</a>
    						    		</td>
    						    		
    						    		<td width="12%">
    						    			<a href="setup_logs.php?show_log=netstat" onMouseOver="mouse_move(&#039;sd_netstat&#039;);" onMouseOut="mouse_move();">
    				  	    				<img src="images/btn_action-log_bg.gif"><br><span>Network Status</span>
    				  	    			</a>
    						    		</td>
    						    		
    						    		<td width="12%">
    						    			<a href="setup_logs.php?show_log=secure" onMouseOver="mouse_move(&#039;sd_secure&#039;);" onMouseOut="mouse_move();">
    				  	    				<img src="images/btn_action-log_bg.gif"><br><span>Security Log</span>
    				  	    			</a>
    						    		</td>
    						    	</tr>
    				    		</tbody>
									</table>      	    
    		  	  	</div> <!-- END TABLE RESPONSIVE -->
    		  		</div> <!-- END PANEL BODY --> 
    		  	</div> <!-- END HPANEL3 --> 
    		  </div> <!-- END COL-SM-12 --> 
    		</div> <!-- END ROW --> 	
    		<br>  
    		<div class="row">
    			<div class="col-sm-12">
    				<div class="hpanel3">
    				  <div class="panel-body" style="background:#F1F3F6;border:none;">
    				  	<div class="table-responsive">
    				   		<table cellpadding="1" cellspacing="1" width="70%">
    				   			<tbody>
    				    			<tr> 
    				    				<td>
    				    					<?php
  													if($log == "0")
  													{
  														$log_text = file_get_contents("/var/log/messages");
  														echo "<legend> System Log</legend>";
  				    								echo "	<textarea rows='25' style='width: 100%;'>$log_text</textarea>";
  													}
  													
  													if($log == "1")
  													{
  														$log_text = file_get_contents("/var/log/rmsd.log");
  														echo "<legend> RMS Daemon Log</legend>";
  				    								echo "	<textarea rows='25' style='width: 100%;'>$log_text</textarea>";
  													}
  													
  													if($log == "2")
  													{
  														if(file_exists("/var/log/rmspingd.log"))
  														{
  															$log_text = file_get_contents("/var/log/rmspingd.log");
  														}
  														else
  														{
  															$log_text = "Ping Daemon Not Running...";
  														}
  														echo "<legend> Ping Daemon Log</legend>";
  				    								echo "	<textarea rows='25' style='width: 100%;'>$log_text</textarea>";
  													}
  													
  													if($log == "3")
  													{
  														if(file_exists("/var/log/rmstimerd.log"))
  														{
  															$log_text = file_get_contents("/var/log/rmstimerd.log");
  														}
  														else
  														{
  															$log_text = "Timer Daemon Not Running...";
  														}
  														echo "<legend> Timer Daemon Log</legend>";
  				    								echo "	<textarea rows='25' style='width: 100%;'>$log_text</textarea>";
  													}
  													
  													if($log == "4")
  													{
  														$log_text = file_get_contents("/var/log/lighttpd.log");
  														echo "<legend> Web Server Log</legend>";
  				    								echo "	<textarea rows='25' style='width: 100%;'>$log_text</textarea>";
  													}
  													
  													if($log == "5")
  													{
  														$log_text = file_get_contents("/var/log/snmpd.log");
  														echo "<legend> SNMP Log</legend>";
  				    								echo "	<textarea rows='25' style='width: 100%;'>$log_text</textarea>";
  													}
  													
  													if($log == "6")
  													{
  														system("netstat -an > /tmp/tmp-netstat");
  														$log_text = file_get_contents("/tmp/tmp-netstat");
  														echo "<legend> Network Status Log</legend>";
  				    								echo "	<textarea rows='25' style='width: 100%;'>$log_text</textarea>";
  													}
  													
  													if($log == "7")
  													{
  														$log_text = file_get_contents("/var/log/secure");
  														echo "<legend> Security Log</legend>";
  				    								echo "	<textarea rows='25' style='width: 100%;'>$log_text</textarea>";
  													}
  												?>
    				    				</td>
    				    			</tr>
    				    		</tbody>
									</table>      	    
  							</div> <!-- END TABLE RESPONSIVE -->
  							<br>
  							<div class="col-sm-12">
  								<button name="refresh_btn" class="btn btn-success" type="submit" onMouseOver="mouse_move(&#039;b_refresh&#039;);" onMouseOut="mouse_move();"><i class="fa fa-check" ></i> Refresh</button>
  								<button name="cancel_btn" class="btn btn-primary" type="submit" onMouseOver="mouse_move(&#039;b_cancel&#039;);" onMouseOut="mouse_move();" formnovalidate><i class="fa fa-times"></i> Cancel</button> 
  							</div>
    					</div> <!-- END PANEL BODY --> 
  					</div> <!-- END HPANEL3 --> 
  				</div> <!-- END COL-MD-12 -->
  			</div> <!-- END ROW -->
  		</fieldset>
  	</form>	
  </div> <!-- END CONTENT -->    
</div> <!-- END Main Wrapper --> 			

</body>
</html> 
