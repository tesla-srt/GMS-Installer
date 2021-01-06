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
    <link rel="stylesheet" href="css/ethertek.css">
    
    <!-- Java Scripts -->
		<script src="javascript/jquery.min.js"></script>
		<script src="javascript/bootstrap.min.js"></script>
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
  		<div class="col-sm-12"><legend>System</legend></div>
  	</div>
  	
  	<div class="row">
    	<div class="col-sm-12">
      	<div class="hpanel3">
      	  <div class="panel-body" style="text-align:center; background:#F1F3F6;border:none;">
    		  	<div class="table-responsive">
    		   		<table cellpadding="1" cellspacing="1" width="100%">
    		   			<tbody>
    		    			<tr> 
    		    				<td width="16%">
    		    					<a href="setup_company.php" onMouseOver="mouse_move(&#039;sd_general_setup&#039;);" onMouseOut="mouse_move();">
      	    					<img src="images/company_setup.gif"><br><span>Company</span>
      	    					</a>
    		    				</td>
                	
    		    				<td width="16%">
    		    					<a href="setup_network.php" onMouseOver="mouse_move(&#039;b_ip_aliasing&#039;);" onMouseOut="mouse_move();">
      	    						<img src="images/network_setup.gif"><br><span>Network</span>
      	    					</a>
    		    				</td>
    		    				
    		    				<td width="16%">
    		    					<a href="setup_system_time.php" onMouseOver="mouse_move(&#039;b_system_time&#039;);" onMouseOut="mouse_move();">
      	    						<img src="images/btn_system-time_bg.gif"><br><span>Time</span>
      	    					</a>
    		    				</td>
    		    				
    		    				<td width="16%">
    		    					<a href="setup_statistics.php" onMouseOver="mouse_move(&#039;b_statistics&#039;);" onMouseOut="mouse_move();">
      	    						<img src="images/btn_statistics_bg.gif"><br><span>Statistics</span>
      	    					</a>
    		    				</td>
    		    				
    		    				<td width="16%">
    		    					<a href="setup_logs.php" onMouseOver="mouse_move(&#039;b_action_log&#039;);" onMouseOut="mouse_move();">
      	    						<img src="images/log4.gif"><br><span>Logs</span>
      	    					</a>
    		    				</td>
                		
    		    				<td width="16%">
    		    					<a href="setup_device_manager.php" onMouseOver="mouse_move(&#039;sd_devicemgr&#039;);" onMouseOut="mouse_move();">
      	    						<img src="images/devices32x32.gif"><br><span>Devices</span>
      	    					</a>
    		    				</td>
    		    			</tr>
    		    		</tbody>
							</table>      	    
      	  	</div> <!-- END TABLE RESPONSIVE -->
      		</div> <!-- END PANEL BODY --> 
      	</div> <!-- END HPANEL3 --> 
      </div> <!-- END COL-MD-12 --> 
    </div> <!-- END ROW --> 	
    <br>  
    
    <div class="row">
  		<div class="col-sm-12"><legend>Services</legend></div>
  	</div>  
    
    <div class="row">
    	<div class="col-sm-12">
      	<div class="hpanel3">
      	  <div class="panel-body" style="text-align:center; background:#F1F3F6;border:none;">
    		  	<div class="table-responsive">
    		   		<table cellpadding="1" cellspacing="1" width="100%">
    		   			<tbody>
    		    			<tr> 
    		    				<td width="16%">
    		    					<a href="setup_services.php" onMouseOver="mouse_move(&#039;b_restart_services&#039;);" onMouseOut="mouse_move();">
      	    					<img src="images/btn_restart-services_bg.gif"><br><span>Services</span>
      	    					</a>
    		    				</td>
                	
    		    				<td width="16%">
    		    					<a href="setup_cron.php" onMouseOver="mouse_move(&#039;b_crontab_unix&#039;);" onMouseOut="mouse_move();">
      	    						<img src="images/schedule.gif"><br><span>Cron</span>
      	    					</a>
    		    				</td>
    		    				
    		    				<td width="16%">
    		    					<a href="setup_ping.php" onMouseOver="mouse_move(&#039;sd_ping_control&#039;);" onMouseOut="mouse_move();">
      	    						<img src="images/ping2.gif"><br><span>Ping</span>
      	    					</a>
    		    				</td>
    		    				
    		    				<td width="16%">
    		    					<a href="setup_timers.php" onMouseOver="mouse_move(&#039;sd_timer&#039;);" onMouseOut="mouse_move();">
      	    						<img src="images/timers.gif"><br><span>Timers</span>
      	    					</a>
    		    				</td>
    		    				
    		    				<td width="16%">
    		    					<a href="setup_logging.php" onMouseOver="mouse_move(&#039;logging&#039;);" onMouseOut="mouse_move();">
      	    						<img src="images/remotesyslog.gif"><br><span>Logging</span>
      	    					</a>
    		    				</td>
                		
    		    				<td width="16%">
    		    					<a href="setup_snmp.php" onMouseOver="mouse_move(&#039;sd_snmp&#039;);" onMouseOut="mouse_move();">
      	    						<img src="images/snmp.gif"><br><span>SNMP</span>
      	    					</a>
    		    				</td>
    		    			</tr>
    		    		</tbody>
							</table>      	    
      	  	</div> <!-- END TABLE RESPONSIVE -->
      		</div> <!-- END PANEL BODY --> 
      	</div> <!-- END HPANEL3 --> 
      </div> <!-- END COL-MD-12 --> 
    </div> <!-- END ROW --> 	
   
   <br>  
    
    <div class="row">
  		<div class="col-sm-12"><legend>Control Panel</legend></div>
  	</div>  
    
    <div class="row">
    	<div class="col-sm-12">
      	<div class="hpanel3">
      	  <div class="panel-body" style="text-align:center; background:#F1F3F6;border:none;">
    		  	<div class="table-responsive">
    		   		<table cellpadding="1" cellspacing="1" width="100%">
    		   			<tbody>
    		    			<tr> 
    		    				<td width="16%">
    		    					<a href="setup_firmware.php" onMouseOver="mouse_move(&#039;b_autoinstaller&#039;);" onMouseOut="mouse_move();">
      	    					<img src="images/srms.gif"><br><span>Firmware</span>
      	    					</a>
    		    				</td>
                	
    		    				<td width="16%">
    		    					<a href="setup_database.php" onMouseOver="mouse_move(&#039;sd_database&#039;);" onMouseOut="mouse_move();">
      	    						<img src="images/db.gif"><br><span>Database</span>
      	    					</a>
    		    				</td>
    		    				
    		    				<td width="16%">
    		    					<a href="setup_file_explorer.php" onMouseOver="mouse_move(&#039;sd_explorer&#039;);" onMouseOut="mouse_move();">
      	    						<img src="images/explorer.gif"><br><span>Files</span>
      	    					</a>
    		    				</td>
    		    				
    		    				<td width="16%">
    		    					<a href="setup_cli.php" onMouseOver="mouse_move(&#039;sd_cli&#039;);" onMouseOut="mouse_move();">
      	    						<img src="images/clianim.gif"><br><span>CLI</span>
      	    					</a>
    		    				</td>
    		    				
    		    				<td width="16%">
    		    					<a href="setup_scripts.php" onMouseOver="mouse_move(&#039;sd_scripts&#039;);" onMouseOut="mouse_move();">
      	    						<img src="images/scripts.gif"><br><span>Scripts</span>
      	    					</a>
    		    				</td>
                		
    		    				<td width="16%">
    		    					<a href="setup_notifications.php" onMouseOver="mouse_move(&#039;b_notifications&#039;);" onMouseOut="mouse_move();">
      	    						<img src="images/btn_notifications_bg.gif"><br><span>Alerts</span>
      	    					</a>
    		    				</td>
    		    			</tr>
    		    		</tbody>
							</table>      	    
      	  	</div> <!-- END TABLE RESPONSIVE -->
      		</div> <!-- END PANEL BODY --> 
      	</div> <!-- END HPANEL3 --> 
      </div> <!-- END COL-MD-12 --> 
    </div> <!-- END ROW --> 
  	
  	<br>  
    
    <div class="row">
  		<div class="col-sm-12"><legend>Administration</legend></div>
  	</div>  
    
    <div class="row">
    	<div class="col-sm-12">
      	<div class="hpanel3">
      	  <div class="panel-body" style="text-align:center; background:#F1F3F6;border:none;">
    		  	<div class="table-responsive">
    		   		<table cellpadding="1" cellspacing="1" width="100%">
    		   			<tbody>
    		    			<tr> 
    		    				<td width="16%">
    		    					<a href="setup_password.php" onMouseOver="mouse_move(&#039;b_change_passwd&#039;);" onMouseOut="mouse_move();">
      	    					<img src="images/btn_change-passwd_bg.gif"><br><span>Password</span>
      	    					</a>
    		    				</td>
                	
    		    				<td width="16%">
    		    					<a href="setup_firewall.php" onMouseOver="mouse_move(&#039;sd_database&#039;);" onMouseOut="mouse_move();">
      	    						<img src="images/firewall.gif"><br><span>Firewall</span>
      	    					</a>
    		    				</td>
    		    				
    		    				<td width="16%">
    		    					<a href="setup_display.php" onMouseOver="mouse_move(&#039;sd_display&#039;);" onMouseOut="mouse_move();">
      	    						<img src="images/display.gif"><br><span>Display</span>
      	    					</a>
    		    				</td>
    		    				
    		    				<td width="16%">
    		    					<a href="setup_graph_options.php" onMouseOver="mouse_move(&#039;sd_graph&#039;);" onMouseOut="mouse_move();">
      	    						<img src="images/graph-32x32.gif"><br><span>Graphs</span>
      	    					</a>
    		    				</td>
    		    				
    		    				<td width="16%">
    		    					<a href="setup_heartbeat.php" onMouseOver="mouse_move(&#039;heartbeat_default&#039;);" onMouseOut="mouse_move();">
      	    						<img src="images/hardware.jpg"><br><span>Hardware</span>
      	    					</a>
    		    				</td>
                		
    		    				<td width="16%">
    		    					<a href="setup_power.php" onMouseOver="mouse_move(&#039;b_power&#039;);" onMouseOut="mouse_move();">
      	    						<img src="images/btn_reboot_bg.gif"><br><span>Power</span>
      	    					</a>
    		    				</td>
    		    			</tr>
    		    		</tbody>
							</table>      	    
      	  	</div> <!-- END TABLE RESPONSIVE -->
      		</div> <!-- END PANEL BODY --> 
      	</div> <!-- END HPANEL3 --> 
      </div> <!-- END COL-MD-12 --> 
    </div> <!-- END ROW --> 
  	
  	
 
  </div> <!-- END CONTENT -->    
</div> <!-- END Main Wrapper -->
</body>
</html> 
