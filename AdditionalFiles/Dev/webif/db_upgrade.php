<?php
include "lib.php";

$alert_flag = "0";
$sd_rmsd = "0";
$sd_rmstimerd = "0";
$sd_rmspingd = "0";
$sd_snmpd = "0";
$sd_rmsvmd = "0";

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
echo "  <link rel='shortcut icon' type='image/ico' href='rms100favicon.ico?<?php echo rand(); ?>' />";
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
echo "<!-- Main Wrapper -->\n";
echo "<div id='wrapper'>\n";
echo "	<div class='row'>\n";
echo "		<div class='col-lg-12'>\n";
echo "			<div class='hpanel4'>\n";
echo "     		<div class='panel-body'>\n";

$target_path = "/data/";

$target_path = $target_path . basename( $_FILES['dbfile']['name']); 
//echo $target_path;
if(move_uploaded_file($_FILES['dbfile']['tmp_name'], $target_path)) 
{
	$uploaded_file_name = basename( $_FILES['dbfile']['name']);
   
	echo "The file <b>".$uploaded_file_name." </b>has been uploaded<BR><BR>";
   
	$query = sprintf("mv %s /data/rms100.db.new",$target_path);
	exec($query);
	
	$target_path = "/data/rms100.db.new";
	
	
	
	// check for RMS-100 database file
	$handle = fopen($target_path, "r");
	$contents = fread($handle, 15);
	fclose($handle);
	   
	if($contents != "SQLite format 3")
	{
   	$text = "This file is not an SQLite 3 file!";
		$alert_flag = "2";
		unlink($target_path);
		goto an_error;
	}
  
  echo 'SQLite Header check... <span style="color:green"><b>[PASSED]</b></span><BR><BR>'; 
  
  $dbh = new PDO('sqlite:/etc/rms100.db');
  $query = "SELECT * FROM ethertek;";
	$result  = $dbh->query($query);
	foreach($result as $row)
	{
		$db_cur_version = $row['dbversion'];
	}
  $dbh = NULL;
  
  
  $pdo_imported = "sqlite:".$target_path; 
  try
  {
		$dbh_imported = new PDO($pdo_imported);
		$query = "SELECT * FROM ethertek;";
  }
  catch (Exception $e) 
  {
  	$text = "This database is not for an RMS-100!";
		$alert_flag = "2";
		unlink($target_path);
		goto an_error;
  }
  
  $result_imported  = $dbh_imported->query($query);
	foreach($result_imported as $row)
	{
		$dbname = $row['dbname'];
		$db_imported_version = $row['dbversion'];
  }
  
  if($dbname !== "RMS100")
  {
  	$text = "This database is not for an RMS-100!";
		$alert_flag = "2";
		unlink($target_path);
		goto an_error;
  }
  
  echo 'RMS-100 database check... <span style="color:green"><b>[PASSED]</b></span><BR><BR>'; 
  
	echo "Starting Database Restore Operation...<BR><BR>"; 
   
  if(($db_imported_version == 1) && ($db_imported_version < $db_cur_version))		{		upgrade_1_to_2();		}
	if(($db_imported_version == 2) && ($db_imported_version < $db_cur_version))		{		upgrade_2_to_3();		}
	if(($db_imported_version == 3) && ($db_imported_version < $db_cur_version))		{		upgrade_3_to_4();		}
	if(($db_imported_version == 4) && ($db_imported_version < $db_cur_version))		{		upgrade_4_to_5();		} 
	if(($db_imported_version == 5) && ($db_imported_version < $db_cur_version))		{		upgrade_5_to_6();		}
	if(($db_imported_version == 6) && ($db_imported_version < $db_cur_version))		{		upgrade_6_to_7();		}
	if(($db_imported_version == 7) && ($db_imported_version < $db_cur_version))		{		upgrade_7_to_8();		}
	if(($db_imported_version == 8) && ($db_imported_version < $db_cur_version))		{		upgrade_8_to_9();		}
	if(($db_imported_version == 9) && ($db_imported_version < $db_cur_version))		{		upgrade_9_to_10();		}
	if(($db_imported_version == 10) && ($db_imported_version < $db_cur_version))	{		upgrade_10_to_11();		}
	if(($db_imported_version == 11) && ($db_imported_version < $db_cur_version))	{		upgrade_11_to_12();		}
	if(($db_imported_version == 12) && ($db_imported_version < $db_cur_version))	{		upgrade_12_to_13();		}
	if(($db_imported_version == 13) && ($db_imported_version < $db_cur_version))	{		upgrade_13_to_14();		}
	if(($db_imported_version == 14) && ($db_imported_version < $db_cur_version))	{		upgrade_14_to_15();		}
	if(($db_imported_version == 15) && ($db_imported_version < $db_cur_version))	{		upgrade_15_to_16();		}
	if(($db_imported_version == 16) && ($db_imported_version < $db_cur_version))	{		upgrade_16_to_17();		}
	if(($db_imported_version == 17) && ($db_imported_version < $db_cur_version))	{		upgrade_17_to_18();		}  
  if(($db_imported_version == 18) && ($db_imported_version < $db_cur_version))	{		upgrade_18_to_19(); 	}
  if(($db_imported_version == 19) && ($db_imported_version < $db_cur_version))	{		upgrade_19_to_20(); 	}
  if(($db_imported_version == 20) && ($db_imported_version < $db_cur_version))	{		upgrade_20_to_21(); 	}
  
  if(!file_exists("/var/run/rmsd.pid"))
  {
  	$sd_rmsd = "1";
  }
  
  if(!file_exists("/var/run/rmstimerd.pid"))
  {
  	$sd_rmstimerd = "1";
  }
  
  if(!file_exists("/var/run/rmspingd.pid"))
  {
  	$sd_rmspingd = "1";
  }
  
  if(!file_exists("/var/run/snmpd.pid"))
  {
  	$sd_snmpd = "1";
  }
  
  if(!file_exists("/var/run/rmsvmd.pid"))
  {
  	$sd_rmsvmd = "1";
  }
  
  echo"Stopping Dependant Services...<BR>";
  
  if($sd_rmsd == "0")
	{
	 	echo"&nbsp;&nbsp;Stopping RMSD...<BR>";	
	 	exec("/etc/init.scripts/S79rmsd stop 1>/dev/null 2>/dev/null");
  }
  
  if($sd_rmspingd == "0")
	{
		echo"&nbsp;&nbsp;Stopping RMSpingD...<BR>";
	 	exec("/etc/init.scripts/S94rmspingd stop 1>/dev/null 2>/dev/null");
  }
  
  if($sd_rmstimerd == "0")
	{
		echo"&nbsp;&nbsp;Stopping RMStimerD...<BR>";
	 	exec("/etc/init.scripts/S95rmstimerd stop 1>/dev/null 2>/dev/null");
  }
  
  if($sd_snmpd == "0")
	{
		echo"&nbsp;&nbsp;Stopping snmpD...<BR>";
	 	exec("/etc/init.scripts/S59snmpd stop 1>/dev/null 2>/dev/null");
  }
  
  if($sd_rmsvmd == "0")
	{
		echo"&nbsp;&nbsp;Stopping rmsvmD...<BR>";
	 	exec("/etc/init.scripts/S98rmsvmd stop 1>/dev/null 2>/dev/null");
  }
  	
	printf("Dependant Services Stopped...<span style='color:green'><b>[OK]</b></span><BR><BR>\n");
  
  echo"Move new Sqlite3 database...";
	exec("mv /data/rms100.db.new /etc/rms100.db");
	unlink("/data/rms100.db.new");
	echo"<span style='color:green'><b>[OK]</b></span><BR><BR>";
	echo"Restarting Dependant Services...<BR>";	
  
  if($sd_rmsd == "0")
	{
		echo"&nbsp;&nbsp;Starting RMSD...<BR>";
	 	exec("/etc/init.scripts/S79rmsd start 1>/dev/null 2>/dev/null");
  }

	if($sd_rmspingd == "0")
	{
		echo "&nbsp;&nbsp;Starting RMSpingD...<BR>";
	 	system("/etc/init.scripts/S94rmspingd start 1>/dev/null 2>/dev/null");
  }
  
  if($sd_rmstimerd == "0")
	{
		echo "&nbsp;&nbsp;Starting RMStimerD...<BR>";
	 	exec("/etc/init.scripts/S95rmstimerd start 1>/dev/null 2>/dev/null");
  }
  
  if($sd_snmpd == "0")
	{
		echo "&nbsp;&nbsp;Starting snmpD...<BR>";
	 	exec("/etc/init.scripts/S59snmpd start 1>/dev/null 2>/dev/null");
  }
  
  if($sd_rmsvmd == "0")
	{
		echo "&nbsp;&nbsp;Starting RMSvmD...<BR>";
	 	exec("/etc/init.scripts/S98rmsvmd start 1>/dev/null 2>/dev/null");
  }
  
	echo "Restarting Dependant Services...<span style='color:green'><b>[OK]</b></span><BR><BR>";
	echo "Database Restore Operation Successfull!<br><br>";
	echo "<form name='Database' action='setup.php' method='post' class='form-horizontal'>";
	echo "	<div class='form-group'>";
  echo "		<div class='col-sm-3'>";      						
  echo "			<button name='back' class='btn btn-success' type='submit' onMouseOver='mouse_move(&#039;b_cancel&#039;);' onMouseOut='mouse_move();' formnovalidate><i class='fa fa-check'></i> Back to Setup</button>";
  echo "		</div>";
  echo "	</div>";
  echo "</form>";
  $text = "Database Restore Operation Successfull!";
  $alert_flag = "1";

} 

else
{
 	$text = "There was an error uploading the file, please try again!";
	$alert_flag = "2";  
}


an_error:
	
	echo "					</div><!-- PANEL BODY -->\n";
	echo "				</div><!-- PANEL -->\n";
	echo "			</div><!-- END COL-LG-12 -->\n";
	echo "		</div> <!-- END ROW -->\n";
	echo "	</div> <!-- END Main Wrapper -->\n";
	
	if($alert_flag == "1")
	{
		echo"<script>";
		echo"swal({";
		echo"  title:'Success!',";
		echo"  text: '" . $text . "',";
		echo"  type: 'success',";
		echo"  showConfirmButton: false,";
		echo"	 html: true,";
		echo"  timer: 2000";
		echo"});";
		//echo"setTimeout(function() {document.location.href='setup_database.php'}, 2000);";
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
		echo"  timer: 3000";
		echo"});";
		echo"setTimeout(function() {document.location.href='setup_database.php'}, 2000);";
		echo"</script>";
	}


echo "</body>";
echo "</html>";





function upgrade_1_to_2()
{
	global $db_imported_version, $result_imported, $dbh_imported;
	
	echo "Upgrading from Ver ".$db_imported_version." to Ver 2...<BR>";
	 
	$query = "ALTER TABLE alerts ADD COLUMN port TEXT NOT NULL DEFAULT '25';";
	$result_imported  = $dbh_imported->exec($query); 
	
	$query = "UPDATE ethertek SET dbversion='2';";				
	$result_imported  = $dbh_imported->exec($query);
	$db_imported_version = 2;
	echo "Done.<br>";
}

function upgrade_2_to_3()
{
	global $db_imported_version, $result_imported, $dbh_imported;
	
	echo "Upgrading from Ver ".$db_imported_version." to Ver 3...<BR>";
		
	$query = "CREATE TABLE iso_vm_polarity (id, polarity, vpolarity, averaging, weight);";
	$result_imported  = $dbh_imported->exec($query); 
		
  $query = "INSERT INTO iso_vm_polarity VALUES ('1', 'BOTH', 'NORMAL', 'UNCHECKED', '0.05');";
	$result_imported  = $dbh_imported->exec($query); 
	
	$query = "INSERT INTO iso_vm_polarity VALUES ('2', 'BOTH', 'NORMAL', 'UNCHECKED', '0.05');";
	$result_imported  = $dbh_imported->exec($query);
	
	$query = "INSERT INTO iso_vm_polarity VALUES ('3', 'BOTH', 'NORMAL', 'UNCHECKED', '0.05');";
	$result_imported  = $dbh_imported->exec($query);
	
	$query = "INSERT INTO iso_vm_polarity VALUES ('4', 'BOTH', 'NORMAL', 'UNCHECKED', '0.05');";
	$result_imported  = $dbh_imported->exec($query);
	
	$query = "INSERT INTO iso_vm_polarity VALUES ('5', 'BOTH', 'NORMAL', 'UNCHECKED', '0.05');";
	$result_imported  = $dbh_imported->exec($query);
	
	$query = "INSERT INTO iso_vm_polarity VALUES ('6', 'BOTH', 'NORMAL', 'UNCHECKED', '0.05');";
	$result_imported  = $dbh_imported->exec($query);
	
	$query = "ALTER TABLE iso_voltmeters ADD COLUMN polling TEXT NOT NULL DEFAULT 'CHECKED';";
	$result_imported  = $dbh_imported->exec($query);
	
	$query = "DROP TABLE iso_vm_refresh;";
	$result_imported  = $dbh_imported->exec($query);
	
	$query = "DROP TABLE iso_vm_enabled;";
	$result_imported  = $dbh_imported->exec($query);
	
	$query = "UPDATE ethertek SET dbversion='3';";
	$result_imported  = $dbh_imported->exec($query);			
	$db_imported_version = 3;
	echo"Done.<br>";
}

function upgrade_3_to_4()
{
	global $db_imported_version, $result_imported, $dbh_imported;
	
	echo "Upgrading from Ver ".$db_imported_version." to Ver 4...<BR>";
		
	$query = "ALTER TABLE voltmeters ADD COLUMN watt_mode_enabled TEXT NOT NULL DEFAULT 'UNCHECKED';";
	$result_imported  = $dbh_imported->exec($query); 
	
	$query = "ALTER TABLE voltmeters ADD COLUMN watt_base_vm TEXT NOT NULL DEFAULT '1';";
	$result_imported  = $dbh_imported->exec($query); 
	
	$query = "ALTER TABLE iso_voltmeters ADD COLUMN watt_mode_enabled TEXT NOT NULL DEFAULT 'UNCHECKED';";
	$result_imported  = $dbh_imported->exec($query); 
	
	$query = "ALTER TABLE iso_voltmeters ADD COLUMN watt_base_vm TEXT NOT NULL DEFAULT '1';";
	$result_imported  = $dbh_imported->exec($query); 
	
	$query = "UPDATE ethertek SET dbversion='4';";
	$result_imported  = $dbh_imported->exec($query);			
	$db_imported_version = 4;
	echo"Done.<br>";
}

function upgrade_4_to_5()
{
	global $db_imported_version, $result_imported, $dbh_imported;
	
	echo "Upgrading from Ver ".$db_imported_version." to Ver 5...<BR>";
		
	$query = "INSERT INTO svc_mgr VALUES (NULL, 'extemp', 'External Temperature Sensor', 'S90extemp', '90', '1');";
	$result_imported  = $dbh_imported->exec($query); 
	
	$query = "UPDATE ethertek SET dbversion='5';";				
	$result_imported  = $dbh_imported->exec($query);
	$db_imported_version = 5;
	echo"Done.<br>";
}
	


function upgrade_5_to_6()
{
	global $db_imported_version, $dbh_imported, $result_imported;
	
	echo "Upgrading from Ver ".$db_imported_version." to Ver 6...<BR>";
	
	$query = "CREATE TABLE extemp (calibration_value);";
	$result_imported  = $dbh_imported->exec($query); 
	
	$query = "INSERT INTO extemp VALUES ('4.0');";
	$result_imported  = $dbh_imported->exec($query); 
	
	$query = "UPDATE ethertek SET dbversion='6';";				
	$result_imported  = $dbh_imported->exec($query);
	$db_imported_version = 6;
	echo"Done.<br>";
}

function upgrade_6_to_7()
{
	global $db_imported_version, $dbh_imported, $result_imported;
	
	echo "Upgrading from Ver ".$db_imported_version." to Ver 7...<BR>";
	
	$query = "ALTER TABLE relays ADD nc_color DEFAULT 'GREEN' NOT NULL;";
	$result_imported  = $dbh_imported->exec($query); 
	
	$query = "ALTER TABLE relays ADD no_color DEFAULT 'RED' NOT NULL;";
	$result_imported  = $dbh_imported->exec($query); 
	
	$query = "ALTER TABLE display_options ADD alarm_block DEFAULT 'CHECKED' NOT NULL;";
	$result_imported  = $dbh_imported->exec($query); 
	
	$query = "UPDATE ethertek SET dbversion='7';";				
	$result_imported  = $dbh_imported->exec($query);
	$db_imported_version = 7;
	echo"Done.<br>";
}

function upgrade_7_to_8()
{
	global $db_imported_version, $dbh_imported, $result_imported;
	
	echo "Upgrading from Ver ".$db_imported_version." to Ver 8...<BR>";
	
	$query = "CREATE TABLE vm_polling_speed (polling_speed_value);";
	$result_imported  = $dbh_imported->exec($query); 
	
	$query = "INSERT INTO vm_polling_speed VALUES ('0');";
	$result_imported  = $dbh_imported->exec($query); 
	
	$query = "CREATE TABLE heart_beat_led (led_value);";
	$result_imported  = $dbh_imported->exec($query); 
	
	$query = "INSERT INTO heart_beat_led VALUES ('on');";
	$result_imported  = $dbh_imported->exec($query); 

	$query = "UPDATE ethertek SET dbversion='8';";				
	$result_imported  = $dbh_imported->exec($query);
	$db_imported_version = 8;
	echo"Done.<br>";
}

function upgrade_8_to_9()
{
	global $db_imported_version, $dbh_imported, $result_imported;
	
	echo "Upgrading from Ver ".$db_imported_version." to Ver 9...<BR>";
	
	$query = "INSERT INTO svc_mgr VALUES (NULL, 'rmsunod', 'RMS Ardunio Uno Daemon', 'S80rmsunod', '80', '1');";
	$result_imported  = $dbh_imported->exec($query); 
	
	$query = "CREATE TABLE uno_io (id, type, name, notes, en, HI_alert_cmds, LO_alert_cmds, HI_script_cmds, LO_script_cmds, hi_flap, lo_flap, dos, RunHiIoFile, RunLowIoFile, iodir, iostate, pullup, glitch);";
	$result_imported  = $dbh_imported->exec($query); 
	
	$query = "INSERT INTO uno_io VALUES ('2', 'egpio', 'Not Named', 'Enter notes on how this IO is hooked up...', '0', '', '', '', '', '0', '0', '','','','output','low','off','off');";
	$result_imported  = $dbh_imported->exec($query); 
	
	$query = "INSERT INTO uno_io VALUES ('3', 'egpio', 'Not Named', 'Enter notes on how this IO is hooked up...', '0', '', '', '', '', '0', '0', '','','','output','low','off','off');";
	$result_imported  = $dbh_imported->exec($query); 
	
	$query = "INSERT INTO uno_io VALUES ('4', 'egpio', 'Not Named', 'Enter notes on how this IO is hooked up...', '0', '', '', '', '', '0', '0', '','','','output','low','off','off');";
	$result_imported  = $dbh_imported->exec($query); 
	
	$query = "INSERT INTO uno_io VALUES ('5', 'egpio', 'Not Named', 'Enter notes on how this IO is hooked up...', '0', '', '', '', '', '0', '0', '','','','output','low','off','off');";
	$result_imported  = $dbh_imported->exec($query); 
	
	$query = "INSERT INTO uno_io VALUES ('6', 'egpio', 'Not Named', 'Enter notes on how this IO is hooked up...', '0', '', '', '', '', '0', '0', '','','','output','low','off','off');";
	$result_imported  = $dbh_imported->exec($query); 
	
	$query = "INSERT INTO uno_io VALUES ('7', 'egpio', 'Not Named', 'Enter notes on how this IO is hooked up...', '0', '', '', '', '', '0', '0', '','','','output','low','off','off');";
	$result_imported  = $dbh_imported->exec($query); 
	
	$query = "INSERT INTO uno_io VALUES ('8', 'egpio', 'Not Named', 'Enter notes on how this IO is hooked up...', '0', '', '', '', '', '0', '0', '','','','output','low','off','off');";
	$result_imported  = $dbh_imported->exec($query); 
	
	$query = "INSERT INTO uno_io VALUES ('9', 'egpio', 'Not Named', 'Enter notes on how this IO is hooked up...', '0', '', '', '', '', '0', '0', '','','','output','low','off','off');";
	$result_imported  = $dbh_imported->exec($query); 
	
	$query = "INSERT INTO uno_io VALUES ('10', 'egpio', 'Not Named', 'Enter notes on how this IO is hooked up...', '0', '', '', '', '', '0', '0', '','','','output','low','off','off');";
	$result_imported  = $dbh_imported->exec($query); 
	
	$query = "INSERT INTO uno_io VALUES ('11', 'egpio', 'Not Named', 'Enter notes on how this IO is hooked up...', '0', '', '', '', '', '0', '0', '','','','output','low','off','off');";
	$result_imported  = $dbh_imported->exec($query); 
	
	$query = "INSERT INTO uno_io VALUES ('12', 'egpio', 'Not Named', 'Enter notes on how this IO is hooked up...', '0', '', '', '', '', '0', '0', '','','','output','low','off','off');";
	$result_imported  = $dbh_imported->exec($query); 
	
	$query = "INSERT INTO uno_io VALUES ('13', 'egpio', 'Not Named', 'Enter notes on how this IO is hooked up...', '0', '', '', '', '', '0', '0', '','','','output','low','off','off');";
	$result_imported  = $dbh_imported->exec($query); 
	
	$query = "INSERT INTO io_script_cmds VALUES ('UNO-GPIO 2 - ','Not Named',' - ','HIGH', '1A'";
	$result_imported  = $dbh_imported->exec($query); 
	
	$query = "INSERT INTO io_script_cmds VALUES ('UNO-GPIO 2 - ','Not Named',' - ','LOW', '1B'";
	$result_imported  = $dbh_imported->exec($query); 
	
	$query = "INSERT INTO io_script_cmds VALUES ('UNO-GPIO 3 - ','Not Named',' - ','HIGH', '1C'";
	$result_imported  = $dbh_imported->exec($query); 
	
	$query = "INSERT INTO io_script_cmds VALUES ('UNO-GPIO 3 - ','Not Named',' - ','LOW', '1D'";
	$result_imported  = $dbh_imported->exec($query); 
	
	$query = "INSERT INTO io_script_cmds VALUES ('UNO-GPIO 4 - ','Not Named',' - ','HIGH', '1E'";
	$result_imported  = $dbh_imported->exec($query); 
	
	$query = "INSERT INTO io_script_cmds VALUES ('UNO-GPIO 4 - ','Not Named',' - ','LOW', '1F'";
	$result_imported  = $dbh_imported->exec($query); 
	
	$query = "INSERT INTO io_script_cmds VALUES ('UNO-GPIO 5 - ','Not Named',' - ','HIGH', '1G'";
	$result_imported  = $dbh_imported->exec($query); 
	
	$query = "INSERT INTO io_script_cmds VALUES ('UNO-GPIO 5 - ','Not Named',' - ','LOW', '1H'";
	$result_imported  = $dbh_imported->exec($query); 
	
	$query = "INSERT INTO io_script_cmds VALUES ('UNO-GPIO 6 - ','Not Named',' - ','HIGH', '1I'";
	$result_imported  = $dbh_imported->exec($query); 
	
	$query = "INSERT INTO io_script_cmds VALUES ('UNO-GPIO 6 - ','Not Named',' - ','LOW', '1J'";
	$result_imported  = $dbh_imported->exec($query); 
	
	$query = "INSERT INTO io_script_cmds VALUES ('UNO-GPIO 7 - ','Not Named',' - ','HIGH', '1K'";
	$result_imported  = $dbh_imported->exec($query); 
	
	$query = "INSERT INTO io_script_cmds VALUES ('UNO-GPIO 7 - ','Not Named',' - ','LOW', '1L'";
	$result_imported  = $dbh_imported->exec($query); 
	
	$query = "INSERT INTO io_script_cmds VALUES ('UNO-GPIO 8 - ','Not Named',' - ','HIGH', '1M'";
	$result_imported  = $dbh_imported->exec($query); 
	
	$query = "INSERT INTO io_script_cmds VALUES ('UNO-GPIO 8 - ','Not Named',' - ','LOW', '1N'";
	$result_imported  = $dbh_imported->exec($query); 
	
	$query = "INSERT INTO io_script_cmds VALUES ('UNO-GPIO 9 - ','Not Named',' - ','HIGH', '1O'";
	$result_imported  = $dbh_imported->exec($query); 
	
	$query = "INSERT INTO io_script_cmds VALUES ('UNO-GPIO 9 - ','Not Named',' - ','LOW', '1P'";
	$result_imported  = $dbh_imported->exec($query); 
	
	$query = "INSERT INTO io_script_cmds VALUES ('UNO-GPIO 10 - ','Not Named',' - ','HIGH', '1Q'";
	$result_imported  = $dbh_imported->exec($query); 
	
	$query = "INSERT INTO io_script_cmds VALUES ('UNO-GPIO 10 - ','Not Named',' - ','LOW', '1R'";
	$result_imported  = $dbh_imported->exec($query); 
	
	$query = "INSERT INTO io_script_cmds VALUES ('UNO-GPIO 11 - ','Not Named',' - ','HIGH', '1S'";
	$result_imported  = $dbh_imported->exec($query); 
	
	$query = "INSERT INTO io_script_cmds VALUES ('UNO-GPIO 11 - ','Not Named',' - ','LOW', '1T'";
	$result_imported  = $dbh_imported->exec($query);
	
	$query = "INSERT INTO io_script_cmds VALUES ('UNO-GPIO 12 - ','Not Named',' - ','HIGH', '1U'";
	$result_imported  = $dbh_imported->exec($query);
	
	$query = "INSERT INTO io_script_cmds VALUES ('UNO-GPIO 12 - ','Not Named',' - ','LOW', '1V'";
	$result_imported  = $dbh_imported->exec($query);
	
	$query = "INSERT INTO io_script_cmds VALUES ('UNO-GPIO 13 - ','Not Named',' - ','HIGH', '1W'";
	$result_imported  = $dbh_imported->exec($query);
	
	$query = "INSERT INTO io_script_cmds VALUES ('UNO-GPIO 13 - ','Not Named',' - ','LOW', '1X'";
	$result_imported  = $dbh_imported->exec($query);

	$query = "UPDATE ethertek SET dbversion='9';";				
	$result_imported  = $dbh_imported->exec($query);
	$db_imported_version = 9;
	echo"Done.<br>";
}

function upgrade_9_to_10()
{
	global $db_imported_version, $dbh_imported, $result_imported;
	
	echo "Upgrading from Ver ".$db_imported_version." to Ver 10...<BR>";
	
	$query = "CREATE TABLE vm_graph_opts (vm,slope_enable,limit_enable,lower_limit,upper_limit);";
	$result_imported  = $dbh_imported->exec($query); 
	
	$query = "INSERT INTO vm_graph_opts VALUES ('1', 'UNCHECKED', 'UNCHECKED', '0', '100');";
	$result_imported  = $dbh_imported->exec($query); 
	
	$query = "INSERT INTO vm_graph_opts VALUES ('2', 'UNCHECKED', 'UNCHECKED', '0', '100');";
	$result_imported  = $dbh_imported->exec($query); 
	
	$query = "INSERT INTO vm_graph_opts VALUES ('3', 'UNCHECKED', 'UNCHECKED', '0', '100');";
	$result_imported  = $dbh_imported->exec($query); 
	
	$query = "CREATE TABLE global_graph_opts (timespan_view);";
	$result_imported  = $dbh_imported->exec($query); 
	
	$query = "INSERT INTO global_graph_opts VALUES ('hour');";
	$result_imported  = $dbh_imported->exec($query); 
	
	$query = "CREATE TABLE iso_vm_graph_opts (vm,slope_enable,limit_enable,lower_limit,upper_limit);";
	$result_imported  = $dbh_imported->exec($query); 
	
	$query = "INSERT INTO iso_vm_graph_opts VALUES ('1', 'UNCHECKED', 'UNCHECKED', '0', '100');";
	$result_imported  = $dbh_imported->exec($query); 
	
	$query = "INSERT INTO iso_vm_graph_opts VALUES ('2', 'UNCHECKED', 'UNCHECKED', '0', '100');";
	$result_imported  = $dbh_imported->exec($query); 
	
	$query = "INSERT INTO iso_vm_graph_opts VALUES ('3', 'UNCHECKED', 'UNCHECKED', '0', '100');";
	$result_imported  = $dbh_imported->exec($query);
	
	$query = "INSERT INTO iso_vm_graph_opts VALUES ('4', 'UNCHECKED', 'UNCHECKED', '0', '100');";
	$result_imported  = $dbh_imported->exec($query);
	
	$query = "INSERT INTO iso_vm_graph_opts VALUES ('5', 'UNCHECKED', 'UNCHECKED', '0', '100');";
	$result_imported  = $dbh_imported->exec($query);
	
	$query = "INSERT INTO iso_vm_graph_opts VALUES ('6', 'UNCHECKED', 'UNCHECKED', '0', '100');";
	$result_imported  = $dbh_imported->exec($query);
	
	$query = "CREATE TABLE iso_global_graph_opts (timespan_view);";
	$result_imported  = $dbh_imported->exec($query);
	
	$query = "INSERT INTO iso_global_graph_opts VALUES ('hour');";
	$result_imported  = $dbh_imported->exec($query);

	$query = "UPDATE ethertek SET dbversion='10';";				
	$result_imported  = $dbh_imported->exec($query);
	$db_imported_version = 10;
	echo"Done.<br>";
}

function upgrade_10_to_11()
{
	global $db_imported_version, $dbh_imported, $result_imported;
	
	echo "Upgrading from Ver ".$db_imported_version." to Ver 11...<BR>";
	
	$query = "ALTER TABLE global_graph_opts ADD graph_width DEFAULT '400' NOT NULL;";
	$result_imported  = $dbh_imported->exec($query); 
	
	$query = "ALTER TABLE global_graph_opts ADD graph_height DEFAULT '100' NOT NULL;";
	$result_imported  = $dbh_imported->exec($query);
	
	$query = "ALTER TABLE iso_global_graph_opts ADD graph_width DEFAULT '400' NOT NULL;";
	$result_imported  = $dbh_imported->exec($query);
	
	$query = "ALTER TABLE iso_global_graph_opts ADD graph_height DEFAULT '100' NOT NULL;";
	$result_imported  = $dbh_imported->exec($query);
	
	$query = "UPDATE ethertek SET dbversion='11';";				
	$result_imported  = $dbh_imported->exec($query);
	$db_imported_version = 11;
	echo"Done.<br>";
}

function upgrade_11_to_12()
{
	global $db_imported_version, $dbh_imported, $result_imported;
	
	echo "Upgrading from Ver ".$db_imported_version." to Ver 12...<BR>";
	
	$query = "ALTER TABLE voltmeters ADD vmadd DEFAULT '0.0000' NOT NULL;";
	$result_imported  = $dbh_imported->exec($query); 
	
	$query = "UPDATE ethertek SET dbversion='12';";				
	$result_imported  = $dbh_imported->exec($query);
	$db_imported_version = 12;
	echo"Done.<br>";
}

function upgrade_12_to_13()
{
	global $db_imported_version, $dbh_imported, $result_imported;
	
	echo "Upgrading from Ver ".$db_imported_version." to Ver 13...<BR>";
	
	$query = "CREATE TABLE update_notice_conf (confirmation);";
	$result_imported  = $dbh_imported->exec($query); 
	
	$query = "INSERT INTO update_notice_conf VALUES ('on');";
	$result_imported  = $dbh_imported->exec($query); 
	
	$query = "UPDATE ethertek SET dbversion='13';";				
	$result_imported  = $dbh_imported->exec($query);
	$db_imported_version = 13;
	echo"Done.<br>";
}

function upgrade_13_to_14()
{
	global $db_imported_version, $dbh_imported, $result_imported;
	
	echo "Upgrading from Ver ".$db_imported_version." to Ver 14...<BR>";
	
	$query = "INSERT INTO svc_mgr VALUES (NULL, 'rmsmodbusd', 'RMS ModBus Server Daemon', 'S88rmsmodbusd', '88', '1');";
	$result_imported  = $dbh_imported->exec($query); 
	
	$query = "UPDATE ethertek SET dbversion='14';";				
	$result_imported  = $dbh_imported->exec($query);
	$db_imported_version = 14;
	echo"Done.<br>";
}

function upgrade_14_to_15()
{
	global $db_imported_version, $dbh_imported, $result_imported;
	
	echo "Upgrading from Ver ".$db_imported_version." to Ver 15...<BR>";
	
	$query = "ALTER TABLE display_options ADD screen_animations DEFAULT 'CHECKED' NOT NULL;";
	$result_imported  = $dbh_imported->exec($query); 
	
	$query = "CREATE TABLE extemperature (notes, hi_t, lo_t, h_en, l_en, HI_alert_cmds, HI_N_alert_cmds, HI_script_cmds, HI_N_script_cmds, LO_alert_cmds, LO_N_alert_cmds, LO_script_cmds, LO_N_script_cmds, hi_flap, lo_flap, default_temp, adj, RunHiFile, RunHiNFile, RunLowFile, RunLowNFile, hi_t_min, lo_t_max);";
	$result_imported  = $dbh_imported->exec($query); 
	$query = "INSERT INTO extemperature VALUES ('Enter notes on how the External USB temperature sensor is monitored.','45','5','0','0','','','','','','','','','0','0','C', '1.0','','','','','40','10');";
	$result_imported  = $dbh_imported->exec($query);
	
	$query = "CREATE TABLE v_units (id, override, name);";
	$result_imported  = $dbh_imported->exec($query); 
	
	$query = "INSERT INTO v_units VALUES ('1','UNCHECKED', 'Not Named');";
	$result_imported  = $dbh_imported->exec($query);
	$query = "INSERT INTO v_units VALUES ('2','UNCHECKED', 'Not Named');";
	$result_imported  = $dbh_imported->exec($query);
	$query = "INSERT INTO v_units VALUES ('3','UNCHECKED', 'Not Named');";
	$result_imported  = $dbh_imported->exec($query);
	
	$query = "UPDATE ethertek SET dbversion='15';";				
	$result_imported  = $dbh_imported->exec($query);
	$db_imported_version = 15;
	echo"Done.<br>";
}

function upgrade_15_to_16()
{
	global $db_imported_version, $dbh_imported, $result_imported;
	
	echo "Upgrading from Ver ".$db_imported_version." to Ver 16...<BR>";
	
	$query = "CREATE TABLE throttle (delay);";
	$result_imported  = $dbh_imported->exec($query); 
	
	$query = "INSERT INTO throttle VALUES ('1000');";
	$result_imported  = $dbh_imported->exec($query);
	
	$query = "UPDATE ethertek SET dbversion='16';";				
	$result_imported  = $dbh_imported->exec($query);
	$db_imported_version = 16;
	echo"Done.<br>";
}

function upgrade_16_to_17()
{
	global $db_imported_version, $dbh_imported, $result_imported;
	
	echo "Upgrading from Ver ".$db_imported_version." to Ver 17...<BR>";
	
	$query = "INSERT INTO svc_mgr VALUES (NULL, 'rmsefoyd', 'RMS Efoy Daemon', 'S82rmsefoyd', '82', '1');";
	$result_imported  = $dbh_imported->exec($query);
	
	$query = "UPDATE ethertek SET dbversion='17';";				
	$result_imported  = $dbh_imported->exec($query);
	$db_imported_version = 17;
	echo"Done.<br>";
}

function upgrade_17_to_18()
{
	global $db_imported_version, $dbh_imported, $result_imported;
	
	echo "Upgrading from Ver ".$db_imported_version." to Ver 18...<BR>";
	
	$query = "CREATE TABLE cloud (enabled,latitude,longitude,username,pass,update_interval);";
	$result_imported  = $dbh_imported->exec($query); 
	
	$query = "INSERT INTO cloud VALUES ('UNCHECKED','0','0','username','password','300');";
	$result_imported  = $dbh_imported->exec($query);
	
	$query = "INSERT INTO svc_mgr VALUES (NULL, 'rmscloud', 'RMS Cloud Daemon', 'S83rmscloud', '83', '1');";
	$result_imported  = $dbh_imported->exec($query); 
	
	$query = "UPDATE ethertek SET dbversion='18';";				
	$result_imported  = $dbh_imported->exec($query);
	$db_imported_version = 18;
	echo"Done.<br>";
}

function upgrade_18_to_19()
{
	global $db_imported_version, $dbh_imported, $result_imported;
	
	echo "Upgrading from Ver ".$db_imported_version." to Ver 19...<BR>";
	
	$query = "CREATE TABLE v_order (id, v_oo);";
	$result_imported  = $dbh_imported->exec($query); 
	
	$query = "INSERT INTO v_order VALUES ('1','UNCHECKED');";
	$result_imported  = $dbh_imported->exec($query);
	
	$query = "INSERT INTO v_order VALUES ('2','UNCHECKED');";
	$result_imported  = $dbh_imported->exec($query);
	
	$query = "INSERT INTO v_order VALUES ('3','UNCHECKED');";
	$result_imported  = $dbh_imported->exec($query);
	
	$query = "INSERT INTO v_order VALUES ('4','UNCHECKED');";
	$result_imported  = $dbh_imported->exec($query);
	
	$query = "INSERT INTO v_order VALUES ('5','UNCHECKED');";
	$result_imported  = $dbh_imported->exec($query);
	
	$query = "INSERT INTO v_order VALUES ('6','UNCHECKED');";
	$result_imported  = $dbh_imported->exec($query);
	
	$query = "UPDATE ethertek SET dbversion='19';";				
	$result_imported  = $dbh_imported->exec($query);
	$db_imported_version = 19;
	echo"Done.<br>";
}

function upgrade_19_to_20()
{
	global $db_imported_version, $dbh_imported, $result_imported;
	
	echo "Upgrading from Ver ".$db_imported_version." to Ver 20...<BR>";
	
	$query = "ALTER TABLE alerts ADD company_name DEFAULT ' ' NOT NULL;";
	$result_imported  = $dbh_imported->exec($query); 
	
	$query = "ALTER TABLE alerts ADD company_phone DEFAULT ' ' NOT NULL;";
	$result_imported  = $dbh_imported->exec($query); 
	
	$query = "ALTER TABLE alerts ADD company_notes DEFAULT ' ' NOT NULL;";
	$result_imported  = $dbh_imported->exec($query); 

	$query = "UPDATE ethertek SET dbversion='20';";				
	$result_imported  = $dbh_imported->exec($query);
	$db_imported_version = 20;
	echo"Done.<br>";
}

function upgrade_20_to_21()
{
	global $db_imported_version, $dbh_imported, $result_imported;
	
	echo "Upgrading from Ver ".$db_imported_version." to Ver 21...<BR>";
	
	$query = "ALTER TABLE cloud ADD url DEFAULT 'https://cloud.remotemonitoringsystems.ca/listener.php' NOT NULL;";
	$result_imported  = $dbh_imported->exec($query); 
	
	$query = "UPDATE ethertek SET dbversion='21';";				
	$result_imported  = $dbh_imported->exec($query);
	$db_imported_version = 21;
	echo"Done.<br>";
}


?>


