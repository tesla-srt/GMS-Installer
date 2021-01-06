<?php
	error_reporting(E_ALL);
	include "lib.php";
	$hostname = trim(file_get_contents("/etc/hostname"));
	$alert_flag = "0";
	
	$log = "0";
	$gpsflag = "0";
	$gprsflag = "0";
	$rdbflag = "0";
	$vdbflag = "0";
	$camflag = "0";
	$extempflag = "0";
	$unoflag = "0";
	$efoyflag = "0";
	$text = "";
	$confirm_id = "0";
	$confirm_name = "";
	$confirm_type = "";
	
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

// Delete Device Button	was clicked
if(isset ($_GET['confirm']))
{
	$command = $_GET['confirm'];
	if($command == "delete")
	{
		$confirm_id = $_GET['id'];
		$confirm_type = $_GET['type'];
		$confirm_name = $_GET['name'];
		$alert_flag = "3";
	}
}


// Delete Device Button	was clicked
if(isset ($_GET['device']))
{
	$command = $_GET['device'];
	if($command == "delete")
	{
		$id = $_GET['id'];
		$type = $_GET['type'];
		$result  = $dbh->exec("DELETE from device_mgr WHERE id=" . $id .";");
		if($type == "CAMERA")
		{
			//Delete CAMERA			
			system("stop_daemon /bin/mjpg_streamer");		
			system("rm /etc/init.d/S44mjpg_streamer");	
			$alert_flag = "1";
		}
		if($type == "GPS")
		{
			//Delete GPS
			system("rm /etc/init.d/S96gpsd");			
			system("kill `cat /var/run/gpsd.pid`");
			system("rm /var/run/gpsd.pid");
			$alert_flag = "1";
		}
		if($type == "GPRS")
		{
			//Delete GPRS	
			system("rm /etc/init.d/S89gprsd");		
			system("kill `cat /var/run/gprsd.pid`");
			system("rm /var/run/gprsd.pid");
			$alert_flag = "1";
		}
		if($type == "RDB")
		{
			//Delete RDB
			system("rm /etc/init.d/S97rmsrbd");		
			system("kill `cat /var/run/rmsrbd.pid`");
			system("rm /var/run/rmsrbd.pid");	
			$alert_flag = "1";
		}
		if($type == "VDB")
		{
			//Delete VMB
			system("rm /etc/init.d/S98rmsvmd");		
			system("kill `cat /var/run/rmsvmd.pid`");
			system("rm /var/run/rmsvmd.pid");	
			$alert_flag = "1";
		}
		if($type == "EXTEMP")
		{
			//Delete EXTEMP
			exec("rm /etc/init.d/S90extemp");		
			exec("/etc/init.scripts/S90extemp stop");
			exec("rm /var/run/extemp.pid");	
			$alert_flag = "1";
		}
		if($type == "MODEM")
		{
			//Delete MODEM	
			$alert_flag = "1";
		}
		if($type == "EFOY")
		{
			//Delete EFOY
			exec("rm /etc/init.d/S82rmsefoyd");		
			exec("/etc/init.scripts/S82rmsefoyd stop");
			exec("rm /var/run/rmsefoyd.pid");	
			$alert_flag = "1";
		}
		if($type == "CUSTOM")
		{
			//Delete CUSTOM	
			$alert_flag = "1";
		}
	}	
}

// ADD/EDIT Device Button	was clicked
if(isset ($_GET['device_mgr']))
{
	$command = $_GET['device_mgr']; //add or edit	
	if($_GET['type'] == "CAMERA")
	{
		if($command == "edit")
		{
				$id = $_GET['mid']; //id
				$name = $_GET['name'];
				$device = $_GET['device'];
				$init = $_GET['init'];
				$baud = $_GET['baud'];
				$flowctl = $_GET['flowctl'];
				$enabled = $_GET['enabled'];				
				webcam_add_edit($command,$id,$name,$device,$init,$baud,$flowctl,$enabled); //$command will be add or edit, $mid is the database id.
				exit(0);
		}
		else
		{
				// Must be add
				webcam_add_edit("add","","","","","","","");
				exit(0);
		}
	}	
	if($_GET['type'] == "GPS")
	{	
		if($command == "edit")
		{
				$id = $_GET['mid']; //id
				$name = $_GET['name'];
				$device = $_GET['device'];
				$init = $_GET['init'];
				$baud = $_GET['baud'];
				$flowctl = $_GET['flowctl'];
				$enabled = $_GET['enabled'];				
				gps_add_edit($command,$id,$name,$device,$init,$baud,$flowctl,$enabled); //$command will be add or edit, $mid is the database id.
				exit(0);
		}
		else
		{
				// Must be add
				gps_add_edit("add","","","","","","","");
				exit(0);
		}
	}
	if($_GET['type'] == "GPRS")
	{	
		if($command == "edit")
		{
				$id = $_GET['mid']; //id
				$name = $_GET['name'];
				$device = $_GET['device'];
				$init = $_GET['init'];
				$baud = $_GET['baud'];
				$flowctl = $_GET['flowctl'];
				$enabled = $_GET['enabled'];
				$databits = $_GET['sdvar1'];
				$parity = $_GET['sdvar2'];
				$stopbits = $_GET['sdvar3'];
				gprs_add_edit($command,$id,$name,$device,$init,$baud,$flowctl,$enabled,$databits,$parity,$stopbits); //$command will be add or edit, $mid is the database id.
				exit(0);
		}
		else
		{
				// Must be add
				gprs_add_edit("add","","","","","","","","","","");
				exit(0);
		}
	}
	
	if($_GET['type'] == "MODEM")
	{	
		if($command == "edit")
		{
				$id = $_GET['mid']; //id
				$name = $_GET['name'];
				$device = $_GET['device'];
				$init = $_GET['init'];
				$baud = $_GET['baud'];
				$flowctl = $_GET['flowctl'];
				$enabled = $_GET['enabled'];
				$databits = $_GET['sdvar1'];
				$parity = $_GET['sdvar2'];
				$stopbits = $_GET['sdvar3'];
				modem_add_edit($command,$id,$name,$device,$init,$baud,$flowctl,$enabled,$databits,$parity,$stopbits); //$command will be add or edit, $mid is the database id.
				exit(0);
		}
		else
		{
				// Must be add
				modem_add_edit("add","","","","","","","","","","");
				exit(0);
		}
	}
	
	if($_GET['type'] == "RDB")
	{	
		if($command == "edit")
		{
				$id = $_GET['mid']; //id
				$name = $_GET['name'];
				$device = $_GET['device'];
				$init = $_GET['init'];
				$baud = $_GET['baud'];
				$flowctl = $_GET['flowctl'];
				$enabled = $_GET['enabled'];
				$databits = $_GET['sdvar1'];
				$parity = $_GET['sdvar2'];
				$stopbits = $_GET['sdvar3'];
				rdb_add_edit($command,$id,$name,$device,$init,$baud,$flowctl,$enabled,$databits,$parity,$stopbits); //$command will be add or edit, $mid is the database id.
				exit(0);
		}
		else
		{
				// Must be add
				rdb_add_edit("add","","","","","","","","","","");
				exit(0);
		}
	}
	
	if($_GET['type'] == "VDB")
	{	
		if($command == "edit")
		{
				$id = $_GET['mid']; //id
				$name = $_GET['name'];
				$device = $_GET['device'];
				$init = $_GET['init'];
				$baud = $_GET['baud'];
				$flowctl = $_GET['flowctl'];
				$enabled = $_GET['enabled'];
				$databits = $_GET['sdvar1'];
				$parity = $_GET['sdvar2'];
				$stopbits = $_GET['sdvar3'];
				vdb_add_edit($command,$id,$name,$device,$init,$baud,$flowctl,$enabled,$databits,$parity,$stopbits); //$command will be add or edit, $mid is the database id.
				exit(0);
		}
		else
		{
				// Must be add
				vdb_add_edit("add","","","","","","","","","","");
				exit(0);
		}
	}
	
	if($_GET['type'] == "EXTEMP")
	{	
		if($command == "edit")
		{
				$id = $_GET['mid']; //id
				$name = $_GET['name'];
				$device = $_GET['device'];
				$init = $_GET['init'];
				$baud = $_GET['baud'];
				$flowctl = $_GET['flowctl'];
				$enabled = $_GET['enabled'];
				$databits = $_GET['sdvar1'];
				$parity = $_GET['sdvar2'];
				$stopbits = $_GET['sdvar3'];
				extemp_add_edit($command,$id,$name,$device,$init,$baud,$flowctl,$enabled,$databits,$parity,$stopbits); //$command will be add or edit, $mid is the database id.
				exit(0);
		}
		else
		{
				// Must be add
				extemp_add_edit("add","","","","","","","","","","");
				exit(0);
		}
	}
	
	if($_GET['type'] == "EFOY")
	{
		if($command == "edit")
		{
				$id = $_GET['mid']; //id
				$name = $_GET['name'];
				$device = $_GET['device'];
				$init = $_GET['init'];
				$baud = $_GET['baud'];
				$flowctl = $_GET['flowctl'];
				$enabled = $_GET['enabled'];
				$sdvar1 = $_GET['sdvar1'];
				$sdvar2 = $_GET['sdvar2'];
				$sdvar3 = $_GET['sdvar3'];
				$sdvar4 = $_GET['sdvar4'];
				$sdvar5 = $_GET['sdvar5'];
				$sdvar6 = $_GET['sdvar6'];
				$sdvar7 = $_GET['sdvar7'];
				$sdvar8 = $_GET['sdvar8'];
				$sdvar9 = $_GET['sdvar9'];
				$sdvar10 = $_GET['sdvar10'];
				$sdvar11 = $_GET['sdvar11'];
				$sdvar12 = $_GET['sdvar12'];						
				efoy_add_edit($command,$id,$name,$device,$init,$baud,$flowctl,$enabled,$sdvar1,$sdvar2,$sdvar3,$sdvar4,$sdvar5,$sdvar6,$sdvar7,$sdvar8,$sdvar9,$sdvar10,$sdvar11,$sdvar12); //$command will be add or edit, $mid is the database id.
				exit(0);
		}
		else
		{
				// Must be add
				efoy_add_edit("add","","","","","","","","","","","","","25","","","","","","0");
				exit(0);
		}
	}	
		
	if($_GET['type'] == "CUSTOM")
	{	
		if($command == "edit")
		{
				$id = $_GET['mid']; //id
				$name = $_GET['name'];
				$device = $_GET['device'];
				$init = $_GET['init'];
				$baud = $_GET['baud'];
				$flowctl = $_GET['flowctl'];
				$enabled = $_GET['enabled'];
				$file_path = $_GET['sdvar1'];
				$icon_path = $_GET['sdvar2'];
				custom_add_edit($command,$id,$name,$device,$init,$baud,$flowctl,$enabled,$file_path,$icon_path); //$command will be add or edit, $mid is the database id.
				exit(0);
		}
		else
		{
				// Must be add
				custom_add_edit("add","","","","","","","","custom_device_template.php","images/custom16x16.gif");
				exit(0);
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

// Web Camera Save Button	was clicked
if(isset ($_POST['webcam_save_btn']))
{
	$camera_id = $_POST['camera_id'];
	$camera_command = $_POST['camera_command'];
	$camera_name = $_POST['camera_name'];
	if(isset($_POST['camera_enabled']))
	{
		$camera_enabled = "on";
	}
	else
	{
		$camera_enabled = "off";
	}
	$camera_device = "Dev Video";
	$camera_init = "Not used.";
	$camera_baud = "Not used.";
	$camera_flowctl = "Not used.";
	$camera_type = "CAMERA";
	$camera_var1 = "Not used.";
	$camera_var2 = "Not used.";
	$camera_var3 = "Not used.";
	$camera_var4 = "Not used.";
	$camera_var5 = "Not used.";
	
	if($camera_command == "add")
	{
		$sql = sprintf("INSERT INTO device_mgr VALUES (NULL, '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', ' ', ' ', ' ', ' ', ' ', ' ', ' ')", $camera_name, $camera_device, $camera_init, $camera_baud, $camera_flowctl, $camera_type, $camera_enabled, $camera_var1, $camera_var2, $camera_var3, $camera_var4, $camera_var5);
		$result  = $dbh->exec($sql);
		system("/etc/init.scripts/S44mjpg_streamer start > /dev/null");
		if ($camera_enabled == "on")
		{
			system("cp /etc/init.scripts/S44mjpg_streamer /etc/init.d/S44mjpg_streamer");
		}
		
		$text = "Device Web Camera Added!";
		$alert_flag = "2";
	}
	else
	{
		//Must be edit
		$sql = sprintf("UPDATE device_mgr SET name='%s', device='%s', init='%s', baud='%s', flowctl='%s', type='%s', enabled='%s', sdvar1='%s', sdvar2='%s', sdvar3='%s', sdvar4='%s', sdvar5='%s' WHERE id=%d", $camera_name, $camera_device, $camera_init, $camera_baud, $camera_flowctl, $camera_type, $camera_enabled, $camera_var1, $camera_var2, $camera_var3, $camera_var4,$camera_var5,$camera_id);
		$result  = $dbh->exec($sql);
		if($camera_enabled == "on")
		{
			system("cp /etc/init.scripts/S44mjpg_streamer /etc/init.d/S44mjpg_streamer");
		}
		else
		{
			system("rm -f /etc/init.d/S44mjpg_streamer");
		}
		$text = "Device Web Camera Updated!";
		$alert_flag = "2";
	}
}	

// GPS Save Button	was clicked
if(isset ($_POST['gps_save_btn']))
{
	$gps_id = $_POST['gps_id'];
	$gps_command = $_POST['gps_command'];
	$gps_name = $_POST['gps_name'];
	$gps_device = $_POST['gps_device'];
	if(isset($_POST['gps_enabled']))
	{
		$gps_enabled = "on";
	}
	else
	{
		$gps_enabled = "off";
	}
	$gps_init = "Not used.";
	$gps_baud = "Not used.";
	$gps_flowctl = "Not used.";
	$gps_type = "GPS";
	$gps_var1 = "Not used.";
	$gps_var2 = "Not used.";
	$gps_var3 = "Not used.";
	$gps_var4 = "Not used.";
	$gps_var5 = "Not used.";
	
	if($gps_command == "add")
	{
		$sql = sprintf("INSERT INTO device_mgr VALUES (NULL, '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', ' ', ' ', ' ', ' ', ' ', ' ', ' ')", $gps_name, $gps_device, $gps_init, $gps_baud, $gps_flowctl, $gps_type, $gps_enabled, $gps_var1, $gps_var2, $gps_var3, $gps_var4, $gps_var5);
		$result  = $dbh->exec($sql);
		system("/etc/init.scripts/S96gpsd start > /dev/null");
		if ($gps_enabled == "on")
		{
			system("cp /etc/init.scripts/S96gpsd /etc/init.d/S96gpsd");
		}
		$text = "Device GPS Added!";
		$alert_flag = "2";
	}
	else
	{
		//Must be edit
		$sql = sprintf("UPDATE device_mgr SET name='%s', device='%s', init='%s', baud='%s', flowctl='%s', type='%s', enabled='%s', sdvar1='%s', sdvar2='%s', sdvar3='%s', sdvar4='%s', sdvar5='%s' WHERE id=%d", $gps_name, $gps_device, $gps_init, $gps_baud, $gps_flowctl, $gps_type, $gps_enabled, $gps_var1, $gps_var2, $gps_var3, $gps_var4,$gps_var5,$gps_id);
		$result  = $dbh->exec($sql);
		if($gps_enabled == "on")
		{
			system("cp /etc/init.scripts/S96gpsd /etc/init.d/S96gpsd");
		}
		else
		{
			system("rm -f /etc/init.d/S96gpsd");
		}
		$text = "Device GPS Updated!";
		$alert_flag = "2";
	}
}	

// GPRS Save Button	was clicked
if(isset ($_POST['gprs_save_btn']))
{
	$gprs_id = $_POST['gprs_id'];
	$gprs_command = $_POST['gprs_command'];
	$gprs_name = $_POST['gprs_name'];
	$gprs_device = $_POST['gprs_device'];
	if(isset($_POST['gprs_enabled']))
	{
		$gprs_enabled = "on";
	}
	else
	{
		$gps_enabled = "off";
	}
	$gprs_init = "Not used.";
	$gprs_baud = $_POST['gprs_baud'];
	$gprs_flowctl = $_POST['gprs_flowctl'];
	$gprs_type = "GPRS";
	$gprs_var1 = $_POST['gprs_databits'];
	$gprs_var2 = $_POST['gprs_parity'];
	$gprs_var3 = $_POST['gprs_stopbits'];
	$gprs_var4 = "Not used.";
	$gprs_var5 = "Not used.";
	
	if($gprs_command == "add")
	{
		$sql = sprintf("INSERT INTO device_mgr VALUES (NULL, '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', ' ', ' ', ' ', ' ', ' ', ' ', ' ')", $gprs_name, $gprs_device, $gprs_init, $gprs_baud, $gprs_flowctl, $gprs_type, $gprs_enabled, $gprs_var1, $gprs_var2, $gprs_var3, $gprs_var4, $gprs_var5);
		$result  = $dbh->exec($sql);
		system("/etc/init.scripts/S89gprsd start > /dev/null");
		if ($gprs_enabled == "on")
		{
			system("cp /etc/init.scripts/S89gprsd /etc/init.d/S89gprsd");
		}
		$text = "Device GPRS Added!";
		$alert_flag = "2";
	}
	else
	{
		//Must be edit
		$sql = sprintf("UPDATE device_mgr SET name='%s', device='%s', init='%s', baud='%s', flowctl='%s', type='%s', enabled='%s', sdvar1='%s', sdvar2='%s', sdvar3='%s', sdvar4='%s', sdvar5='%s' WHERE id=%d", $gprs_name, $gprs_device, $gprs_init, $gprs_baud, $gprs_flowctl, $gprs_type, $gprs_enabled, $gprs_var1, $gprs_var2, $gprs_var3, $gprs_var4,$gprs_var5,$gprs_id);
		$result  = $dbh->exec($sql);
		if($gprs_enabled == "on")
		{
			system("cp /etc/init.scripts/S89gprsd /etc/init.d/S89gprsd");
		}
		else
		{
			system("rm -f /etc/init.d/S89gprsd");
		}
		$text = "Device GPRS Updated!";
		$alert_flag = "2";
	}
}	

// RDB Save Button	was clicked
if(isset ($_POST['rdb_save_btn']))
{
	$id = $_POST['id'];
	$rdb_id = $_POST['rdb_id'];
	$rdb_command = $_POST['rdb_command'];
	if($rdb_id == "0000")
	{
		$text = "No Relay Boards Found!";
		$alert_flag = 4;
		goto escape_hatch;
	}
	$rdb_name = "Relay Board";
	$rdb_device = "/dev/usb/hiddev";
	$rdb_init = sprintf("Product ID: %s",$rdb_id);
	$rdb_baud = "Not used.";
	$rdb_flowctl = "Not used.";
	$rdb_type = "RDB";
	$rdb_var1 = $rdb_id;
	$rdb_var2 = "Not used.";
	$rdb_var3 = "Not used.";
	$rdb_var4 = "Not used.";
	$rdb_var5 = "Not used.";
	
	if(isset($_POST['rdb_enabled']))
	{
		$rdb_enabled = "on";
	}
	else
	{
		$rdb_enabled = "off";
	}
	
	if($rdb_command == "add")
	{
		$sql = sprintf("INSERT INTO device_mgr VALUES (NULL, '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', ' ', ' ', ' ', ' ', ' ', ' ', ' ')", $rdb_name, $rdb_device, $rdb_init, $rdb_baud, $rdb_flowctl, $rdb_type, $rdb_enabled, $rdb_var1, $rdb_var2, $rdb_var3, $rdb_var4, $rdb_var5);
		$result  = $dbh->exec($sql);
		system("/etc/init.scripts/S97rmsrbd start > /dev/null");
		if ($rdb_enabled == "on")
		{
			system("cp /etc/init.scripts/S97rmsrbd /etc/init.d/S97rmsrbd");
		}
		$text = "Device RDB Added!";
		$alert_flag = "2";
	}
	else
	{
		//Must be edit
		$sql = sprintf("UPDATE device_mgr SET name='%s', device='%s', init='%s', baud='%s', flowctl='%s', type='%s', enabled='%s', sdvar1='%s', sdvar2='%s', sdvar3='%s', sdvar4='%s', sdvar5='%s' WHERE id=%d", $rdb_name, $rdb_device, $rdb_init, $rdb_baud, $rdb_flowctl, $rdb_type, $rdb_enabled, $rdb_var1, $rdb_var2, $rdb_var3, $rdb_var4,$rdb_var5,$id);
		$result  = $dbh->exec($sql);
		if($rdb_enabled == "on")
		{
			system("cp /etc/init.scripts/S97rmsrbd /etc/init.d/S97rmsrbd");
		}
		else
		{
			system("rm -f /etc/init.d/S97rmsrbd");
		}
		exec("kill -HUP `cat /var/run/rmsrbd.pid`");
		$text = "Device RDB Updated!";
		$alert_flag = "2";
	}
}	

// VDB Save Button	was clicked
if(isset ($_POST['vdb_save_btn']))
{
	$id = $_POST['id'];
	$vdb_id = $_POST['vdb_id'];
	$vdb_command = $_POST['vdb_command'];
	if($vdb_id == "0000")
	{
		$text = "No Voltmeter Boards Found!";
		$alert_flag = 4;
		goto escape_hatch;
	}
	$vdb_name = "Voltmeter Board";
	$vdb_device = "/dev/usb/hiddev";
	$vdb_init = sprintf("Product ID: %s",$vdb_id);
	$vdb_baud = "Not used.";
	$vdb_flowctl = "Not used.";
	$vdb_type = "VDB";
	$vdb_var1 = $vdb_id;
	$vdb_var2 = "Not used.";
	$vdb_var3 = "Not used.";
	$vdb_var4 = "Not used.";
	$vdb_var5 = "Not used.";
	
	if(isset($_POST['vdb_enabled']))
	{
		$vdb_enabled = "on";
	}
	else
	{
		$vdb_enabled = "off";
	}
	
	if($vdb_command == "add")
	{
		$sql = sprintf("INSERT INTO device_mgr VALUES (NULL, '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', ' ', ' ', ' ', ' ', ' ', ' ', ' ')", $vdb_name, $vdb_device, $vdb_init, $vdb_baud, $vdb_flowctl, $vdb_type, $vdb_enabled, $vdb_var1, $vdb_var2, $vdb_var3, $vdb_var4, $vdb_var5);
		$result  = $dbh->exec($sql);
		system("/etc/init.scripts/S98rmsvmd start > /dev/null");
		if ($vdb_enabled == "on")
		{
			system("cp /etc/init.scripts/S98rmsvmd /etc/init.d/S98rmsvmd");
		}
		$text = "Device VDB Added!";
		$alert_flag = "2";
	}
	else
	{
		//Must be edit
		$sql = sprintf("UPDATE device_mgr SET name='%s', device='%s', init='%s', baud='%s', flowctl='%s', type='%s', enabled='%s', sdvar1='%s', sdvar2='%s', sdvar3='%s', sdvar4='%s', sdvar5='%s' WHERE id=%d", $vdb_name, $vdb_device, $vdb_init, $vdb_baud, $vdb_flowctl, $vdb_type, $vdb_enabled, $vdb_var1, $vdb_var2, $vdb_var3, $vdb_var4,$vdb_var5,$id);
		$result  = $dbh->exec($sql);
		if($vdb_enabled == "on")
		{
			system("cp /etc/init.scripts/S98rmsvmd /etc/init.d/S98rmsvmd");
		}
		else
		{
			system("rm -f /etc/init.d/S98rmsvmd");
		}
		exec("kill -HUP `cat /var/run/rmsvmd.pid`");
		$text = "Device VDB Updated!";
		$alert_flag = "2";
	}
}	

// EXTEMP Save Button	was clicked
if(isset ($_POST['extemp_save_btn']))
{
	$id = $_POST['extemp_id'];
	$extemp_command = $_POST['extemp_command'];
	
	$extemp_name = $_POST['extemp_name'];
	//$extemp_name = "EXTEMP";
	$extemp_device = "/dev/USB";
	$extemp_init = "Not used.";
	$extemp_baud = "Not used.";
	$extemp_flowctl = "Not used.";
	$extemp_type = "EXTEMP";
	$extemp_var1 = "Not used.";
	$extemp_var2 = "Not used.";
	$extemp_var3 = "Not used.";
	$extemp_var4 = "Not used.";
	$extemp_var5 = "Not used.";
	
	if(isset($_POST['extemp_enabled']))
	{
		$extemp_enabled = "on";
	}
	else
	{
		$extemp_enabled = "off";
	}
	
	if($extemp_command == "add")
	{
		$sql = sprintf("INSERT INTO device_mgr VALUES (NULL, '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', ' ', ' ', ' ', ' ', ' ', ' ', ' ')", $extemp_name, $extemp_device, $extemp_init, $extemp_baud, $extemp_flowctl, $extemp_type, $extemp_enabled, $extemp_var1, $extemp_var2, $extemp_var3, $extemp_var4, $extemp_var5);
		$result  = $dbh->exec($sql);
		system("/etc/init.scripts/S90extemp start > /dev/null");
		if ($extemp_enabled == "on")
		{
			system("cp /etc/init.scripts/S90extemp /etc/init.d/S90extemp");
		}
		$text = "Device EXTEMP Added!";
		$alert_flag = "2";
	}
	else
	{
		//Must be edit
		$sql = sprintf("UPDATE device_mgr SET name='%s', device='%s', init='%s', baud='%s', flowctl='%s', type='%s', enabled='%s', sdvar1='%s', sdvar2='%s', sdvar3='%s', sdvar4='%s', sdvar5='%s' WHERE id=%d", $extemp_name, $extemp_device, $extemp_init, $extemp_baud, $extemp_flowctl, $extemp_type, $extemp_enabled, $extemp_var1, $extemp_var2, $extemp_var3, $extemp_var4,$extemp_var5,$id);
		$result  = $dbh->exec($sql);
		if($extemp_enabled == "on")
		{
			system("cp /etc/init.scripts/S90extemp /etc/init.d/S90extemp");
		}
		else
		{
			system("rm -f /etc/init.d/S90extemp");
		}
		exec("kill -HUP `cat /var/run/extemp.pid`");
		$text = "Device EXTEMP Updated!";
		$alert_flag = "2";
	}
}	

// MODEM Save Button	was clicked
if(isset ($_POST['modem_save_btn']))
{
	$modem_id = $_POST['modem_id'];
	$modem_command = $_POST['modem_command'];
	$modem_name = $_POST['modem_name'];
	$modem_device = $_POST['modem_device'];
	if(isset($_POST['modem_enabled']))
	{
		$modem_enabled = "on";
	}
	else
	{
		$modem_enabled = "off";
	}
	$modem_init = $_POST['modem_init'];
	$modem_baud = $_POST['modem_baud'];
	$modem_flowctl = $_POST['modem_flowctl'];
	$modem_type = "MODEM";
	$modem_var1 = $_POST['modem_databits'];
	$modem_var2 = $_POST['modem_parity'];
	$modem_var3 = $_POST['modem_stopbits'];
	$modem_var4 = "Not used.";
	$modem_var5 = "Not used.";
	
	if($modem_command == "add")
	{
		$sql = sprintf("INSERT INTO device_mgr VALUES (NULL, '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', ' ', ' ', ' ', ' ', ' ', ' ', ' ')", $modem_name, $modem_device, $modem_init, $modem_baud, $modem_flowctl, $modem_type, $modem_enabled, $modem_var1, $modem_var2, $modem_var3, $modem_var4, $modem_var5);
		$result  = $dbh->exec($sql);
		system("/etc/init.scripts/S89gprsd start > /dev/null");
		if ($modem_enabled == "on")
		{
			system("cp /etc/init.scripts/S89gprsd /etc/init.d/S89gprsd");
		}
		$text = "Device MODEM Added!";
		$alert_flag = "2";
	}
	else
	{
		//Must be edit
		$sql = sprintf("UPDATE device_mgr SET name='%s', device='%s', init='%s', baud='%s', flowctl='%s', type='%s', enabled='%s', sdvar1='%s', sdvar2='%s', sdvar3='%s', sdvar4='%s', sdvar5='%s' WHERE id=%d", $modem_name, $modem_device, $modem_init, $modem_baud, $modem_flowctl, $modem_type, $modem_enabled, $modem_var1, $modem_var2, $modem_var3, $modem_var4,$modem_var5,$modem_id);
		$result  = $dbh->exec($sql);
		if($modem_enabled == "on")
		{
			system("cp /etc/init.scripts/S89gprsd /etc/init.d/S89gprsd");
		}
		else
		{
			system("rm -f /etc/init.d/S89gprsd");
		}
		$text = "Device MODEM Updated!";
		$alert_flag = "2";
	}
}	

// Efoy Save Button	was clicked
if(isset ($_POST['efoy_save_btn']))
{
	$efoy_id = $_POST['efoy_id'];
	$efoy_command = $_POST['efoy_command'];
	$efoy_name = $_POST['efoy_name'];
	if(isset($_POST['efoy_enabled']))
	{
		$efoy_enabled = "CHECKED";
	}
	else
	{
		$efoy_enabled = "";
	}
	$efoy_device = $_POST['efoy_device'];
	$efoy_init = "Not used.";
	$efoy_baud = "Not used.";
	$efoy_type = "EFOY";
	$efoy_var1 = $_POST['efoy_var1'];
	
	$efoy_var2 = "";
	$efoy_var3 = "";
	$efoy_var4 = "";
	$efoy_var5 = "";
	$efoy_var6 = "";
	$efoy_var7 = "";
	$efoy_var8 = "";
	$efoy_var9 = "";
	$efoy_var10 = "";
	$efoy_var11 = "";
	$efoy_var12 = "";
	$efoy_flowctl = "";
	
	if(isset($_POST['from']))
	{
		$efoy_var2 = $_POST['from'];
	}
	
	if(isset($_POST['smtp']))
	{
		$efoy_var3 = $_POST['smtp'];
	}
	
	if(isset($_POST['to']))
	{
		$efoy_var4 = $_POST['to'];
	}
	
	if(isset($_POST['subject']))
	{
		$efoy_var5 = $_POST['subject'];
	}
	
	if(isset($_POST['smtp_port']))
	{
		$efoy_var6 = $_POST['smtp_port'];
	}
	
	if(isset($_POST['auth_check']))
	{
		$efoy_var7 = "CHECKED";
	}
	else
	{
		$efoy_var7 = "";
	}
	
	if(isset($_POST['username']))
	{
		$efoy_var8 = $_POST['username'];
	}
	
	if(isset($_POST['password']))
	{
		$efoy_var9 = $_POST['password'];
	}
	
	if(isset($_POST['auth_group']))
	{
		$auth_group = $_POST['auth_group'];
		if($auth_group == "starttls")
		{
			$sdvar10 = "";							//ssl
			$efoy_flowctl = "CHECKED";	//tls
		}
		else
		{
			$sdvar10 = "CHECKED"; //ssl
			$efoy_flowctl = "";  //tls
		}
	}
	
	if(isset($_POST['email_enabled']))
	{
		$efoy_var11 = "CHECKED";
	}
	
	
	if(isset($_POST['how_often']))
	{
		$efoy_var12 = $_POST['how_often'];
	}
	
	if($efoy_command == "add")
	{
		$sql = sprintf("INSERT INTO device_mgr VALUES (NULL, '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s')", $efoy_name, $efoy_device, $efoy_init, $efoy_baud, $efoy_flowctl, $efoy_type, $efoy_enabled, $efoy_var1, $efoy_var2, $efoy_var3, $efoy_var4, $efoy_var5, $efoy_var6, $efoy_var7, $efoy_var8, $efoy_var9, $efoy_var10, $efoy_var11, $efoy_var12);
		$result  = $dbh->exec($sql);
		system("/etc/init.scripts/S82rmsefoyd start > /dev/null");
		if ($efoy_enabled == "CHECKED")
		{
			system("cp /etc/init.scripts/S82rmsefoyd /etc/init.d/S82rmsefoyd");
		}
		
		$text = "Device Efoy Added!";
		$alert_flag = "2";
	}
	else
	{
		//Must be edit
		$sql = sprintf("UPDATE device_mgr SET name='%s', device='%s', init='%s', baud='%s', flowctl='%s', type='%s', enabled='%s', sdvar1='%s', sdvar2='%s', sdvar3='%s', sdvar4='%s', sdvar5='%s', sdvar6='%s', sdvar7='%s', sdvar8='%s', sdvar9='%s', sdvar10='%s', sdvar11='%s', sdvar12='%s' WHERE id=%d", $efoy_name, $efoy_device, $efoy_init, $efoy_baud, $efoy_flowctl, $efoy_type, $efoy_enabled, $efoy_var1, $efoy_var2, $efoy_var3, $efoy_var4, $efoy_var5, $efoy_var6, $efoy_var7, $efoy_var8, $efoy_var9, $efoy_var10, $efoy_var11, $efoy_var12, $efoy_id);
		$result  = $dbh->exec($sql);
		system("/etc/init.scripts/S82rmsefoyd restart > /dev/null");
		if($efoy_enabled == "CHECKED")
		{
			system("cp /etc/init.scripts/S82rmsefoyd /etc/init.d/S82rmsefoyd");
		}
		else
		{
			system("rm -f /etc/init.d/S82rmsefoyd");
		}
		$text = "Device Efoy Updated!";
		$alert_flag = "2";
	}
}	


// CUSTOM Save Button	was clicked
if(isset ($_POST['custom_save_btn']))
{
	$custom_id = $_POST['custom_id'];
	$custom_command = $_POST['custom_command'];
	$custom_name = $_POST['custom_name'];
	$custom_device = "Not used.";
	$file_path = $_POST['file_path'];
	$icon_path = $_POST['icon_path'];
	if(isset($_POST['custom_enabled']))
	{
		$custom_enabled = "on";
	}
	else
	{
		$custom_enabled = "off";
	}
	$custom_init = "Not used.";
	$custom_baud = "Not used.";
	$custom_flowctl = "Not used.";
	$custom_type = "CUSTOM";
	$custom_var1 = $file_path;
	$custom_var2 = $icon_path;
	$custom_var3 = "Not used.";
	$custom_var4 = "Not used.";
	$custom_var5 = "Not used.";
	
	if($custom_command == "add")
	{
		$sql = sprintf("INSERT INTO device_mgr VALUES (NULL, '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', ' ', ' ', ' ', ' ', ' ', ' ', ' ')", $custom_name, $custom_device, $custom_init, $custom_baud, $custom_flowctl, $custom_type, $custom_enabled, $custom_var1, $custom_var2, $custom_var3, $custom_var4, $custom_var5);
		$result  = $dbh->exec($sql);
		$text = "CUSTOM Device Added!";
		$alert_flag = "2";
	}
	else
	{
		//Must be edit
		$sql = sprintf("UPDATE device_mgr SET name='%s', device='%s', init='%s', baud='%s', flowctl='%s', type='%s', enabled='%s', sdvar1='%s', sdvar2='%s', sdvar3='%s', sdvar4='%s', sdvar5='%s' WHERE id=%d", $custom_name, $custom_device, $custom_init, $custom_baud, $custom_flowctl, $custom_type, $custom_enabled, $custom_var1, $custom_var2, $custom_var3, $custom_var4,$custom_var5,$custom_id);
		$result  = $dbh->exec($sql);
		$text = "CUSTOM Device Updated!";
		$alert_flag = "2";
	}
}	

escape_hatch:	
	$result  = $dbh->query("SELECT * FROM device_mgr WHERE type='VDB';");			
	foreach($result as $row)
		{
			$vdbflag = "1";
		}
		
	$result  = $dbh->query("SELECT * FROM device_mgr WHERE type='CAMERA';");			
	foreach($result as $row)
		{
			$camflag = "1";
		}
		
	$result  = $dbh->query("SELECT * FROM device_mgr WHERE type='RDB';");			
	foreach($result as $row)
		{
			$rdbflag = "1";
		}
	
	$result  = $dbh->query("SELECT * FROM device_mgr WHERE type='GPS';");			
	foreach($result as $row)
		{
			$gpsflag = "1";
		}
	
	$result  = $dbh->query("SELECT * FROM device_mgr WHERE type='GPRS';");			
	foreach($result as $row)
		{
			$gprsflag = "1";
		}
	
	$result  = $dbh->query("SELECT * FROM device_mgr WHERE type='EXTEMP';");			
	foreach($result as $row)
		{
			$extempflag = "1";
		}
		
	$result  = $dbh->query("SELECT * FROM device_mgr WHERE type='UNO';");			
	foreach($result as $row)
		{
			$unoflag = "1";
		}
	
	$result  = $dbh->query("SELECT * FROM device_mgr WHERE type='EFOY';");			
	foreach($result as $row)
		{
			$efoyflag = "1";
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
  		<div class="col-sm-12"><legend>Device Manager</legend></div>
  	</div>
  	<form name='DeviceManager' action='setup_device_manager.php' method='post' class="form-horizontal">  	
    	<fieldset>
  			<div class="row">
    			<div class="col-sm-12">
    		  	<div class="hpanel3">
    		  	  <div class="panel-body" style="text-align:center; background:#F1F3F6;border:none;">
    				  	<div class="table-responsive">
    				   		<table cellpadding="1" cellspacing="1" width="100%">
    				   			<tbody>
    				    			<tr>
    				    				<?php
    				    					if($gpsflag == "1")
    				    					{
    				    						echo '<td width="10%">';
    						    				echo '	<a href="#" onMouseOver="mouse_move(&#039;sd_gps_only_one&#039;);" onMouseOut="mouse_move();">';
    				  	    				echo '	<img src="images/gps32x32.gif" title="Only one USB GPS allowed!"><br><span>ADD GPS</span>';
    				  	    				echo '	</a>';
    						    				echo '</td>';
    				    					}
    				    					else
    				    					{
    				    						echo '<td width="10%">';
    						    				echo '	<a href="setup_device_manager.php?device_mgr=add&type=GPS" onMouseOver="mouse_move(&#039;sd_gps_add&#039;);" onMouseOut="mouse_move();">';
    				  	    				echo '	<img src="images/gps32x32.gif" title="ADD USB GPS"><br><span>ADD GPS</span>';
    				  	    				echo '	</a>';
    						    				echo '</td>';
    				    					}
    				    					
    		  	    					if($gprsflag == "1")
    				    					{
    				    						echo '<td width="10%">';
    						    				echo '	<a href="#" onMouseOver="mouse_move(&#039;sd_gprs_only_one&#039;);" onMouseOut="mouse_move();">';
    				  	    				echo '	<img src="images/gprs32x32.gif" title="Only one GPRS Modem allowed!"><br><span>ADD GPRS</span>';
    				  	    				echo '	</a>';
    						    				echo '</td>';
    				    					}
    				    					else
    				    					{
    				    						echo '<td width="10%">';
    						    				echo '	<a href="setup_device_manager.php?device_mgr=add&type=GPRS" onMouseOver="mouse_move(&#039;sd_gprs_add&#039;);" onMouseOut="mouse_move();">';
    				  	    				echo '	<img src="images/gprs32x32.gif" title="ADD GPRS Modem"><br><span>ADD GPRS</span>';
    				  	    				echo '	</a>';
    						    				echo '</td>';
    				    					}
    		  	    					
    		  	    					echo '<td width="10%">';
    						    			echo '	<a href="setup_device_manager.php?device_mgr=add&type=MODEM" onMouseOver="mouse_move(&#039;sd_modem_add&#039;);" onMouseOut="mouse_move();">';
    				  	    			echo '	<img src="images/modem32x32.gif" title="ADD Modem"><br><span>ADD Modem</span>';
    				  	    			echo '	</a>';
    						    			echo '</td>';
    		  	    					
    		  	    					if($rdbflag == "1")
    				    					{
    				    						echo '<td width="10%">';
    						    				echo '	<a href="#" onMouseOver="mouse_move(&#039;sd_rdb_done&#039;);" onMouseOut="mouse_move();">';
    				  	    				echo '	<img src="images/relay1-32x32.gif" title="Only one Relay Board allowed!"><br><span>ADD RB</span>';
    				  	    				echo '	</a>';
    						    				echo '</td>';
    				    					}
    				    					else
    				    					{
    				    						echo '<td width="10%">';
    						    				echo '	<a href="setup_device_manager.php?device_mgr=add&type=RDB" onMouseOver="mouse_move(&#039;sd_rdb_add&#039;);" onMouseOut="mouse_move();">';
    				  	    				echo '	<img src="images/relay1-32x32.gif" title="ADD Relay Board"><br><span>ADD RB</span>';
    				  	    				echo '	</a>';
    						    				echo '</td>';
    				    					}
    		  	    					
													if($vdbflag == "1")
    				    					{
    				    						echo '<td width="10%">';
    						    				echo '	<a href="#" onMouseOver="mouse_move(&#039;sd_vdb_done&#039;);" onMouseOut="mouse_move();">';
    				  	    				echo '	<img src="images/vdb32x32.gif" title="Only one Voltmeter Board allowed!"><br><span>ADD VB</span>';
    				  	    				echo '	</a>';
    						    				echo '</td>';
    				    					}
    				    					else
    				    					{
    				    						echo '<td width="10%">';
    						    				echo '	<a href="setup_device_manager.php?device_mgr=add&type=VDB" onMouseOver="mouse_move(&#039;sd_vdb_add&#039;);" onMouseOut="mouse_move();">';
    				  	    				echo '	<img src="images/vdb32x32.gif" title="ADD Voltmeter Board"><br><span>ADD VB</span>';
    				  	    				echo '	</a>';
    						    				echo '</td>';
    				    					}
    				    					
													echo '</tr><tr>';
													
													if($camflag == "1")
    				    					{
    				    						echo '<td width="10%" style="padding-top:20px">';
    						    				echo '	<a href="#" onMouseOver="mouse_move(&#039;sd_camera_only_one&#039;);" onMouseOut="mouse_move();">';
    				  	    				echo '	<img src="images/webcam32x32.gif" title="Only one USB Web Camera allowed!"><br><span>ADD Web Camera</span>';
    				  	    				echo '	</a>';
    						    				echo '</td>';
    				    					}
    				    					else
    				    					{
    				    						echo '<td width="10%" style="padding-top:20px">';
    						    				echo '	<a href="setup_device_manager.php?device_mgr=add&type=CAMERA" onMouseOver="mouse_move(&#039;b_camera&#039;);" onMouseOut="mouse_move();">';
    				  	    				echo '	<img src="images/webcam32x32.gif" title="ADD USB Web Camera"><br><span>ADD Web Camera</span>';
    				  	    				echo '	</a>';
    						    				echo '</td>';
    				    					}

  												if($extempflag == "1")
    				    					{
    				    						echo '<td width="10%" style="padding-top:20px">';
    						    				echo '	<a href="#" onMouseOver="mouse_move(&#039;sd_extemp_only_one&#039;);" onMouseOut="mouse_move();">';
    				  	    				echo '	<img src="images/extemp32x32.gif" title="Only one USB Temperature Sensor allowed!"><br><span>ADD Temp Sensor</span>';
    				  	    				echo '	</a>';
    						    				echo '</td>';
    				    					}
    				    					else
    				    					{
    				    						echo '<td width="10%" style="padding-top:20px">';
    						    				echo '	<a href="setup_device_manager.php?device_mgr=add&type=EXTEMP" onMouseOver="mouse_move(&#039;sd_extemp_add&#039;);" onMouseOut="mouse_move();">';
    				  	    				echo '	<img src="images/extemp32x32.gif" title="ADD External USB Temperature Sensor"><br><span>ADD Temp Sensor</span>';
    				  	    				echo '	</a>';
    						    				echo '</td>';
    				    					}
 
 													if($unoflag == "1")
    				    					{
    				    						echo '<td width="10%" style="padding-top:20px">';
    						    				echo '	<a href="#" onMouseOver="mouse_move(&#039;sd_uno_only_one&#039;);" onMouseOut="mouse_move();">';
    				  	    				echo '	<img src="images/arduino32x32.gif" title="Only one Arduino UNO I/O Extender allowed!"><br><span>ADD I/O Extender</span>';
    				  	    				echo '	</a>';
    						    				echo '</td>';
    				    					}
    				    					else
    				    					{
    				    						echo '<td width="10%" style="padding-top:20px">';
    						    				echo '	<a href="setup_device_manager.php?device_mgr=add&type=UNO" onMouseOver="mouse_move(&#039;sd_uno_add&#039;);" onMouseOut="mouse_move();">';
    				  	    				echo '	<img src="images/arduino32x32.gif" title="Add Arduino UNO I/O Extender"><br><span>ADD I/O Extender</span>';
    				  	    				echo '	</a>';
    						    				echo '</td>';
    				    					}
  						          	
  						          	if($efoyflag == "1")
    				    					{
    				    						echo '<td width="10%">';
    						    				echo '	<a href="#" onMouseOver="mouse_move(&#039;sd_efoy_only_one&#039;);" onMouseOut="mouse_move();">';
    				  	    				echo '	<img src="images/efoy.gif" title="Only one EFOY allowed!"><br><span>ADD Efoy Device</span>';
    				  	    				echo '	</a>';
    						    				echo '</td>';
    				    					}
    				    					else
    				    					{
    				    						echo '<td width="10%" style="padding-top:20px">';
    						    				echo '	<a href="setup_device_manager.php?device_mgr=add&type=EFOY" onMouseOver="mouse_move(&#039;sd_efoy_add&#039;);" onMouseOut="mouse_move();">';
    				  	    				echo '	<img src="images/efoy.gif" title="Add Efoy"><br><span>Add Efoy</span>';
    				  	    				echo '	</a>';
    						    				echo '</td>';
    				    					}
  						          	
    						    			echo '<td width="10%" style="padding-top:20px">';
    						    			echo '	<a href="setup_device_manager.php?device_mgr=add&type=CUSTOM" onMouseOver="mouse_move(&#039;sd_custom_add&#039;);" onMouseOut="mouse_move();">';
    				  	    			echo '	<img src="images/custom32x32.gif" title="Add Custom Device"><br><span>ADD Custom Device</span>';
    				  	    			echo '	</a>';
    						    			echo '</td>';
    						    	?>	
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
  				<div class="col-sm-12"><legend>Current Devices</legend></div>
  			</div>
  			
  			<div class="row">
    			<div class="col-sm-12">
    		  	<div class="hpanel3">
    		  	  <div class="panel-body" style="text-align:center; background:#F1F3F6;border:none;">
    				  	<div class="table-responsive">
    				   		<table cellpadding="1" cellspacing="1" width="70%" class="table table-striped">
    				   			<thead>
    				   				<tr>
    				   					<th width="4%" style="background:#ABBEEF; border: 1px solid white;">
    				   						<div style="text-align:center">Status</div>
    				   					</th>
    				   					<th width="1%" style="background:#D6DFF7; border: 1px solid white;">
    				   						<div style="text-align:center">ID</div>
    				   					</th>
    				   					<th width="10%" style="background:#D6DFF7; border: 1px solid white;">
    				   						<div style="text-align:center">Type</div>
    				   					</th>
    				   					<th width="25%" style="background:#D6DFF7; border: 1px solid white;">
    				   						<div style="text-align:left">Name</div>
    				   					</th>
    				   					<th width="25%" style="background:#D6DFF7; border: 1px solid white;">
    				   						<div style="text-align:left">Using Port</div>
    				   					</th>
    				   					<th width="25%" style="background:#D6DFF7; border: 1px solid white;">
    				   						<div style="text-align:left">Init String</div>
    				   					</th>
    				   					<th width="10%" style="background:#D6DFF7; border: 1px solid white;">
    				   						<div style="text-align:left">Actions</div>
    				   					</th>
    				   				</tr>
    				   			</thead>
    				   			<tbody>
    				   				<?php
    				   					$dbh = new PDO('sqlite:/etc/rms100.db');
												$result  = $dbh->query("SELECT * FROM device_mgr ORDER BY id;");			
												foreach($result as $row)
													{
														$id = $row['id'];
														$name = $row['name'];
														$device = $row['device'];
														$init = $row['init'];
														$baud = $row['baud'];
														$flowctl = $row['flowctl'];
														$type = $row['type'];
														$enabled = $row['enabled'];
														$sdvar1 = $row['sdvar1'];
														$sdvar2 = $row['sdvar2'];
														$sdvar3 = $row['sdvar3'];
														$sdvar4 = $row['sdvar4'];
														$sdvar5 = $row['sdvar5'];
														$sdvar6 = $row['sdvar6'];
														$sdvar7 = $row['sdvar7'];
														$sdvar8 = $row['sdvar8'];
														$sdvar9 = $row['sdvar9'];
														$sdvar10 = $row['sdvar10'];
														$sdvar11 = $row['sdvar11'];
														$sdvar12 = $row['sdvar12'];

    				   							echo "<tr>";
    				   							if($type == "VDB")
    				   								{
    				   									if(file_exists("/var/run/rmsvmd.pid"))
    				   									{
    				   										$sdhtml = "on";
    				   									}
    				   									else
    				   									{
    				   										$sdhtml = "off";
    				   									}
    				   								}
    				   							else if($type == "RDB")
    				   								{
    				   									if(file_exists("/var/run/rmsrbd.pid"))
    				   									{
    				   										$sdhtml = "on";
    				   									}
    				   									else
    				   									{
    				   										$sdhtml = "off";
    				   									}
    				   								}
    				   							else if($type == "EXTEMP")
    				   								{
    				   									if(file_exists("/var/run/extemp.pid"))
    				   									{
    				   										$sdhtml = "on";
    				   									}
    				   									else
    				   									{
    				   										$sdhtml = "off";
    				   									}
    				   								}	
    				   							else if($type == "UNO")
    				   								{
    				   									if(file_exists("/var/run/rmsunod.pid"))
    				   									{
    				   										$sdhtml = "on";
    				   									}
    				   									else
    				   									{
    				   										$sdhtml = "off";
    				   									}
    				   								}
    				   							else if($type == "EFOY")
    				   								{
    				   									if(file_exists("/var/run/rmsefoyd.pid"))
    				   									{
    				   										$sdhtml = "on";
    				   									}
    				   									else
    				   									{
    				   										$sdhtml = "off";
    				   									}
    				   								}	
    				   							else
															{
																$sdhtml = $enabled;
															}
    				   							
    				   							//Status
    				   							$html = sprintf("<td><img src='images/serv%s.gif' width='16' height='16'</td>",$sdhtml);
    				   							echo $html;
    				   							//ID
    				   							$html = sprintf("<td>%s</td>", $id);
    				   							echo $html;
    				   							//Type
    				   							$html = sprintf("<td>%s</td>", $type);
    				   							echo $html;
    				   							//Name (link to edit device)
														echo "<td style='text-align:left'><a href='setup_device_manager.php?device_mgr=edit";
														echo "&amp;mid=" . $id;
														echo "&amp;name=" . $name;
														echo "&amp;device=" . $device; 
														echo "&amp;init=" . $init;
														echo "&amp;baud=" . $baud;
														echo "&amp;flowctl=" . $flowctl;
														echo "&amp;type=" . $type;
														echo "&amp;enabled=" . $enabled;
														echo "&amp;sdvar1=" . $sdvar1;
														echo "&amp;sdvar2=" . $sdvar2;
														echo "&amp;sdvar3=" . $sdvar3;
														echo "&amp;sdvar4=" . $sdvar4;
														echo "&amp;sdvar5=" . $sdvar5;
														echo "&amp;sdvar6=" . $sdvar6;
														echo "&amp;sdvar7=" . $sdvar7;
														echo "&amp;sdvar8=" . $sdvar8;
														echo "&amp;sdvar9=" . $sdvar9;
														echo "&amp;sdvar10=" . $sdvar10;
														echo "&amp;sdvar11=" . $sdvar11;
														echo "&amp;sdvar12=" . $sdvar12;
														
														echo "'><u>" . $name . "</u></a></td>";
    				   							
														// Device and INIT String
														echo "<td style='text-align:left'>" . $device . "</td><td>" . $init . "</td>";
													
														
														if($type == "MODEM")
															{
																$sdhtml = "modem";
															}
														if($type == "GPS")
															{
																$sdhtml = "gps";
															}
														if($type == "GPRS")
															{
																$sdhtml = "gprs";
															}	
														if($type == "CAMERA")
															{
																$sdhtml = "camera";
															}
														if($type == "VDB")
															{
																$sdhtml = "vdb";
															}	
														if($type == "EXTEMP")
															{
																$sdhtml = "extemp";
															}
														if($type == "UNO")
															{
																$sdhtml = "uno";
															}					
														if($type == "EFOY")
															{
																$sdhtml = "efoy";
															}
														if($type == "CUSTOM")
															{
																$sdhtml = "custom";
															}
														
														// Delete Icon
														$html = sprintf("<td style='text-align:left'><a href='setup_device_manager.php?confirm=delete&id=%s&name=%s&device=%s&init=%s&baud=%s&flowctl=%s&type=%s&enabled=%s&sdvar1=%s&sdvar2=%s&sdvar3=%s&sdvar4=%s&sdvar5=%s'", $id, $name, $device, $init, $baud, $flowctl, $type, $enabled, $sdvar1, $sdvar2, $sdvar3, $sdvar4, $sdvar5); 
														//$html = sprintf("<td style='text-align:left'><a href='#'"); 
														echo $html;
														echo "onMouseOver ='mouse_move(&#039;" . $sdhtml . "_mgr_delete&#039;);' ";
														echo "onMouseOut='mouse_move();'>";
														echo "<img src='images/off.gif' width='16' height='16' name='" . $id . "' alt='DELETE'></a>";
														echo "</td>";
    				   							echo "</tr>";
    				   						}
    				   					
    				   				?>
    				   			</tbody>
  								</table>
  							</div> <!-- END TABLE RESPONSIVE -->
  							<br>
  							<div style="text-align:left">
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



<?php 
if($alert_flag == "1")
{
echo"<script>";
echo"swal({";
echo"  title:'Success!',";
echo"  text: 'Device Deleted from Database!',";
echo"  type: 'success',";
echo"  showConfirmButton: false,";
echo"  timer: 2500";
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
echo"  timer: 2500";
echo"});";
echo"</script>";
}




if($alert_flag == "3")
{
echo"<script>";
echo"	swal({";
echo"		title: 'Delete Device ID# " . $confirm_id . "<br><span style=\'color:#F8BB86\'>" . $confirm_name . "</span><br>Are you sure?',";
echo"		type: 'warning',";
echo"		showCancelButton: true,";
echo"		html: true,";
echo"		confirmButtonColor: '#DD6B55',";
echo"		confirmButtonText: 'Yes, delete it!',";
echo"		closeOnConfirm: false";
echo"	},";
echo"	function(){";
echo"		window.location.href = 'setup_device_manager.php?device=delete&id=" . $confirm_id . "&type=" . $confirm_type . "';";
echo"	});";
echo"</script>";
}

if($alert_flag == "4")
{
echo"<script>";
echo"swal({";
echo"  title:'Danger!',";
echo"  text: '" . $text . "',";
echo"  type: 'danger',";
echo"  showConfirmButton: false,";
echo"  timer: 2500";
echo"});";
echo"</script>";
}


?>


</body>
</html> 


<?php










function webcam_add_edit($command,$id,$name,$device,$init,$baud,$flowctl,$enabled)
{
	if($command == "edit")
	{
		$header = "Edit USB Web Camera";
	}
	else
	{
		$header = "ADD new USB Web Camera";
	}
	
	if($enabled == "off")
	{
		$sdhtml = "";
	}
	else
	{
		$sdhtml = "checked";
	}
	setup_top_header();
	start_header();
	left_nav("setup");
	echo "<script language='javascript' type='text/javascript'>";
	echo "SetContext('setup');";
	echo "</script>";
	echo "<!-- Main Wrapper -->";
	echo "<div id='wrapper'>";
	echo "	<div class='content animate-panel' data-effect='fadeInUp'>";
	echo "  	<!-- INFO BLOCK START -->";
	echo "  	<div class='row'>";
	echo "    	<div class='col-sm-12'>";
	echo "      	<div class='hpanel4'>";
	echo "      		<div class='panel-body' style='max-width:500px'>";
	echo "      	  	<form name='WebCamera' action='setup_device_manager.php' method='post' class='form-horizontal'>";  	
	echo "      	    	<fieldset>";
	echo "      	    		<legend><img src='images/webcam32x32.gif'> " . $header . "</legend>";
	echo "      	    		<div class='form-group'><label class='col-sm-3 control-label'>Camera Name:</label>";
	echo "              		<div class='col-sm-8' style='min-width:280px; max-width:280px'>";
	echo "              			<input type='text' class='form-control' name='camera_name' value='" . $name . "' required />";
	echo "              		</div>";
	echo "              	</div>	   "; 	    									
	echo "								<div class='form-group'><label class='col-sm-3 control-label'>Start on Boot:</label>";
	echo "              		<div class='col-sm-8'>";
	echo "              			<div class='checkbox checkbox-success'><input id='camera_enabled' type='checkbox' name='camera_enabled' " . $sdhtml . " /><label for='camera_enabled'></label></div>";
	echo "              		</div>";
	echo "              	</div>";         	
	echo "              	<div class='form-group'>";
	echo "              		<div class='col-sm-8 col-sm-offset-3'>";
	echo "              	  	<button name='webcam_save_btn' class='btn btn-success' type='submit' onMouseOver='mouse_move(&#039;b_apply&#039;);' onMouseOut='mouse_move();'><i class='fa fa-check' ></i> Save</button>";
	echo "              	  	<button name='webcam_cancel_btn' class='btn btn-primary' type='submit' onMouseOver='mouse_move(&#039;b_cancel&#039;);' onMouseOut='mouse_move();' formnovalidate><i class='fa fa-times'></i> Cancel</button>";
	echo "              	  </div>";
	echo "              	</div>";
	echo " 								<input type='hidden' name='camera_id' value='" . $id . "'>";
	echo " 								<input type='hidden' name='camera_command' value='" . $command . "'>";
	echo " 								<input type='hidden' name='camera_device' value='" . $device . "'>";
	echo " 								<input type='hidden' name='camera_init' value='" . $init . "'>";
	echo " 								<input type='hidden' name='camera_baud' value='" . $baud . "'>";
	echo " 								<input type='hidden' name='camera_flowctl' value='" . $flowctl . "'>";
	echo "              </fieldset>";  
	echo "						</form>";
	echo "      		</div> <!-- END PANEL BODY -->"; 
	echo "      	</div> <!-- END PANEL WRAPPER -->"; 
	echo "      </div>  <!-- END COL -->"; 
	echo "    </div> <!-- END ROW -->";
	echo "  </div> <!-- END CONTENT -->";    
	echo "</div> <!-- END Main Wrapper -->";
	echo "</body>";
	echo "</html>";
}


function gps_add_edit($command,$id,$name,$device,$init,$baud,$flowctl,$enabled)
{
	if($command == "edit")
	{
		$header = "Edit GPS Settings";
	}
	else
	{
		$header = "ADD new GPS";
	}
	
	if($enabled == "off")
	{
		$sdhtml = "";
	}
	else
	{
		$sdhtml = "checked";
	}
	setup_top_header();
	start_header();
	left_nav("setup");
	echo "<script language='javascript' type='text/javascript'>";
	echo "SetContext('setup');";
	echo "</script>";
	echo "<!-- Main Wrapper -->";
	echo "<div id='wrapper'>";
	echo "	<div class='content animate-panel' data-effect='fadeInUp'>";
	echo "  	<!-- INFO BLOCK START -->";
	echo "  	<div class='row'>";
	echo "    	<div class='col-sm-12'>";
	echo "      	<div class='hpanel4'>";
	echo "      		<div class='panel-body' style='max-width:500px'>";
	echo "      	  	<form name='GPS' action='setup_device_manager.php' method='post' class='form-horizontal'>";  	
	echo "      	    	<fieldset>";
	echo "      	    		<legend><img src='images/gps32x32.gif'> " . $header . "</legend>";
	echo "      	    		<div class='form-group'><label class='col-sm-3 control-label'>GPS Name:</label>";
	echo "              		<div class='col-sm-4' style='max-width:250px; min-width:200px'>";
	echo "              			<input type='text' class='form-control' name='gps_name' value='" . $name . "' required />";
	echo "              		</div>";
	echo "              	</div>"; 	 
	echo "      	    		<div class='form-group'><label class='col-sm-3 control-label'>Choose Device:</label>";
	echo "              		<div class='col-sm-4' style='max-width:200px; min-width:200px'>";
	echo "              			<select class='form-control m-b' size='0' name='gps_device'>";
	
															$dh  = opendir("/dev");
															while (false !== ($filename = readdir($dh))) 
															{
    														$pos = strpos($filename, "ttyU");
    														if ($pos !== false) 
    														{
  echo "													<option value='" . $filename . "'>" . $filename . "</option>";
    														}
															}
	
	echo "											<option value='ttyS1'>ttyS1</option>";
	echo "              			</select>";
	echo "              		</div>";
	echo "              	</div>";    									
	echo "								<div class='form-group'><label class='col-sm-3 control-label'>Start on Boot:</label>";
	echo "              		<div class='col-sm-4' style='max-width:200px; min-width:200px'>";
	echo "										<div class='checkbox checkbox-success'><input id='gps_enabled' type='checkbox' name='gps_enabled' " . $sdhtml . " /><label for='gps_enabled'></label></div>";
	echo "              		</div>";
	echo "              	</div>";         	
	echo "              	<div class='form-group'>";
	echo "              		<div class='col-sm-8 col-sm-offset-3'>";
	echo "              	  	<button name='gps_save_btn' class='btn btn-success' type='submit' onMouseOver='mouse_move(&#039;b_apply&#039;);' onMouseOut='mouse_move();'><i class='fa fa-check' ></i> Save</button>";
	echo "              	  	<button name='gps_cancel_btn' class='btn btn-primary' type='submit' onMouseOver='mouse_move(&#039;b_cancel&#039;);' onMouseOut='mouse_move();' formnovalidate><i class='fa fa-times'></i> Cancel</button>";
	echo "              	  </div>";
	echo "              	</div>";
	echo " 								<input type='hidden' name='gps_id' value='" . $id . "'>";
	echo " 								<input type='hidden' name='gps_command' value='" . $command . "'>";
	echo " 								<input type='hidden' name='gps_init' value='" . $init . "'>";
	echo " 								<input type='hidden' name='gps_baud' value='" . $baud . "'>";
	echo " 								<input type='hidden' name='gps_flowctl' value='" . $flowctl . "'>";
	echo "              </fieldset>";  
	echo "						</form>";
	echo "      		</div> <!-- END PANEL BODY -->"; 
	echo "      	</div> <!-- END PANEL WRAPPER -->"; 
	echo "      </div>  <!-- END COL -->"; 
	echo "    </div> <!-- END ROW -->";
	echo "  </div> <!-- END CONTENT -->";    
	echo "</div> <!-- END Main Wrapper -->";
	echo "</body>";
	echo "</html>";
}

function gprs_add_edit($command,$id,$name,$device,$init,$baud,$flowctl,$enabled,$databits,$parity,$stopbits)
{
	if($command == "edit")
	{
		$header = "Edit GPRS Modem Settings";
	}
	else
	{
		$header = "ADD new GPRS Modem";
	}
	
	if($enabled == "off")
	{
		$sdhtml = "";
	}
	else
	{
		$sdhtml = "checked";
	}
	setup_top_header();
	start_header();
	left_nav("setup");
	echo "<script language='javascript' type='text/javascript'>";
	echo "SetContext('setup');";
	echo "</script>";
	echo "<!-- Main Wrapper -->";
	echo "<div id='wrapper'>";
	echo "	<div class='content animate-panel' data-effect='fadeInUp'>";
	echo "  	<!-- INFO BLOCK START -->";
	echo "  	<div class='row'>";
	echo "    	<div class='col-sm-12'>";
	echo "      	<div class='hpanel4'>";
	echo "      		<div class='panel-body' style='max-width:500px'>";
	echo "      	  	<form name='GPRS' action='setup_device_manager.php' method='post' class='form-horizontal'>";  	
	echo "      	    	<fieldset>";
	echo "      	    		<legend><img src='images/gprs32x32.gif'> " . $header . "</legend>";
	echo "      	    		<div class='form-group'><label class='col-sm-3 control-label'>GPRS Name:</label>";
	echo "              		<div class='col-sm-4' style='min-width:250px; max-width:250px'>";
	echo "              			<input type='text' class='form-control' name='gprs_name' value='" . $name . "' required />";
	echo "              		</div>";
	echo "              	</div>"; 	 
	echo "      	    		<div class='form-group'><label class='col-sm-3 control-label'>Device:</label>";
	echo "              		<div class='col-sm-3' style='min-width:150px; max-width:150px'>";
	echo "              			<select class='form-control m-b' size='0' name='gprs_device'>";
	
															$dh  = opendir("/dev");
															while (false !== ($filename = readdir($dh))) 
															{
    														$pos = strpos($filename, "ttyU");
    														if ($pos !== false) 
    														{
  echo "													<option value='" . $filename . "'>" . $filename . "</option>";
    														}
															}
	
	echo "											<option value='ttyS1'>ttyS1</option>";
	echo "              			</select>";
	echo "              		</div>";
	echo "              	</div>";    									
	echo "      	    		<div class='form-group'><label class='col-sm-3 control-label'>Baud:</label>";
	echo "              		<div class='col-sm-4' style='min-width:150px; max-width:150px'>";
	echo "              			<select class='form-control m-b' size='0' name='gprs_baud'>";
															$array = array("100","300","600","1200","2400","4800","9600","14400","19200","38400","57600","115200","380400");
															for($xsd=0;$xsd<13;$xsd++)
															{
																if($baud == $array[$xsd])
																{
	echo "													<option value='" . $array[$xsd] . "' selected>" . $array[$xsd] . "</option>";																		
																}
																else
																{
	echo "													<option value='" . $array[$xsd] . "'>" . $array[$xsd] . "</option>";																		
																}															
															}
	echo "              			</select>";
	echo "              		</div>";
	echo "              	</div>";    															
	echo "      	    		<div class='form-group'><label class='col-sm-3 control-label'>Data Bits:</label>";
	echo "              		<div class='col-sm-4' style='min-width:150px; max-width:150px'>";
	echo "              			<select class='form-control m-b' size='0' name='gprs_databits'>";
															if($databits == "7")
															{
	echo "												<option value='7' selected>7</option>";															
															}
															else
															{
	echo "												<option value='7'>7</option>";																
															}
															if($databits == "8")
															{
	echo "												<option value='8' selected>8</option>";															
															}
															else
															{
	echo "												<option value='8'>8</option>";																
															}
	echo "              			</select>";
	echo "              		</div>";
	echo "              	</div>"; 
	echo "      	    		<div class='form-group'><label class='col-sm-3 control-label'>Parity:</label>";
	echo "              		<div class='col-sm-4' style='min-width:150px; max-width:150px'>";
	echo "              			<select class='form-control m-b' size='0' name='gprs_parity'>";
															if($parity == "none")
															{
	echo "												<option value='none' selected>None</option>";															
															}
															else
															{
	echo "												<option value='none'>None</option>";																
															}
															if($parity == "odd")
															{
	echo "												<option value='odd' selected>Odd</option>";															
															}
															else
															{
	echo "												<option value='odd'>Odd</option>";																
															}
																if($parity == "even")
															{
	echo "												<option value='even' selected>Even</option>";															
															}
															else
															{
	echo "												<option value='even'>Even</option>";																
															}														
	echo "              			</select>";
	echo "              		</div>";
	echo "              	</div>"; 															
	echo "      	    		<div class='form-group'><label class='col-sm-3 control-label'>Stop Bits:</label>";
	echo "              		<div class='col-sm-4' style='min-width:150px; max-width:150px'>";
	echo "              			<select class='form-control m-b' size='0' name='gprs_stopbits'>";
															if($stopbits == "0")
															{
	echo "												<option value='0' selected>0</option>";															
															}
															else
															{
	echo "												<option value='0'>0</option>";																
															}
															if($stopbits == "1")
															{
	echo "												<option value='1' selected>1</option>";															
															}
															else
															{
	echo "												<option value='1'>1</option>";																
															}
	echo "              			</select>";
	echo "              		</div>";
	echo "              	</div>"; 
	echo "      	    		<div class='form-group'><label class='col-sm-3 control-label'>Flow Control:</label>";
	echo "              		<div class='col-sm-4' style='min-width:150px; max-width:150px'>";
	echo "              			<select class='form-control m-b' size='0' name='gprs_flowctl'>";
															if($flowctl == "none")
															{
	echo "												<option value='none' selected>None</option>";															
															}
															else
															{
	echo "												<option value='none'>None</option>";																
															}
															if($flowctl == "xon")
															{
	echo "												<option value='xon' selected>XON/XOFF</option>";															
															}
															else
															{
	echo "												<option value='xon'>XON/XOFF</option>";																
															}
															if($flowctl == "rts")
															{
	echo "												<option value='rts' selected>RTSCTS</option>";															
															}
															else
															{
	echo "												<option value='rts'>RTSCTS</option>";																
															}														
	echo "              			</select>";
	echo "              		</div>";
	echo "              	</div>";
	echo "								<div class='form-group'><label class='col-sm-3 control-label'>Start on Boot:</label>";
	echo "              		<div class='col-sm-8'>";
	echo "										<div class='checkbox checkbox-success'><input id='gprs_enabled' type='checkbox' name='gprs_enabled' " . $sdhtml . " /><label for='gprs_enabled'></label></div>";
	echo "              		</div>";
	echo "              	</div>";         	
	echo "              	<div class='form-group'>";
	echo "              		<div class='col-sm-8 col-sm-offset-3'>";
	echo "              	  	<button name='gprs_save_btn' class='btn btn-success' type='submit' onMouseOver='mouse_move(&#039;b_apply&#039;);' onMouseOut='mouse_move();'><i class='fa fa-check' ></i> Save</button>";
	echo "              	  	<button name='gprs_cancel_btn' class='btn btn-primary' type='submit' onMouseOver='mouse_move(&#039;b_cancel&#039;);' onMouseOut='mouse_move();' formnovalidate><i class='fa fa-times'></i> Cancel</button>";
	echo "              	  </div>";
	echo "              	</div>";
	echo " 								<input type='hidden' name='gprs_id' value='" . $id . "'>";
	echo " 								<input type='hidden' name='gprs_command' value='" . $command . "'>";
	echo " 								<input type='hidden' name='gprs_init' value='" . $init . "'>";
	echo "              </fieldset>";  
	echo "						</form>";
	echo "      		</div> <!-- END PANEL BODY -->"; 
	echo "      	</div> <!-- END PANEL WRAPPER -->"; 
	echo "      </div>  <!-- END COL -->"; 
	echo "    </div> <!-- END ROW -->";
	echo "  </div> <!-- END CONTENT -->";    
	echo "</div> <!-- END Main Wrapper -->";
	echo "</body>";
	echo "</html>";
}


function modem_add_edit($command,$id,$name,$device,$init,$baud,$flowctl,$enabled,$databits,$parity,$stopbits)
{
	if($command == "edit")
	{
		$header = "Edit MODEM Settings";
	}
	else
	{
		$header = "ADD new MODEM";
	}
	
	if($enabled == "off")
	{
		$sdhtml = "";
	}
	else
	{
		$sdhtml = "checked";
	}
	setup_top_header();
	start_header();
	left_nav("setup");
	echo "<script language='javascript' type='text/javascript'>";
	echo "SetContext('setup');";
	echo "</script>";
	echo "<!-- Main Wrapper -->";
	echo "<div id='wrapper'>";
	echo "	<div class='content animate-panel' data-effect='fadeInUp'>";
	echo "  	<!-- INFO BLOCK START -->";
	echo "  	<div class='row'>";
	echo "    	<div class='col-sm-12'>";
	echo "      	<div class='hpanel4'>";
	echo "      		<div class='panel-body' style='max-width:500px'>";
	echo "      	  	<form name='MODEM' action='setup_device_manager.php' method='post' class='form-horizontal'>";  	
	echo "      	    	<fieldset>";
	echo "      	    		<legend><img src='images/gprs32x32.gif'> " . $header . "</legend>";
	echo "      	    		<div class='form-group'><label class='col-sm-3 control-label'>Name:</label>";
	echo "              		<div class='col-sm-8' style='min-width:200px; max-width:200px'>";
	echo "              			<input type='text' class='form-control' name='modem_name' value='" . $name . "' required />";
	echo "              		</div>";
	echo "              	</div>"; 	 
	echo "      	    		<div class='form-group'><label class='col-sm-3 control-label'>Device:</label>";
	echo "              		<div class='col-sm-3' style='min-width:150px; max-width:150px'>";
	echo "              			<select class='form-control m-b' size='0' name='modem_device'>";
	
															$dh  = opendir("/dev");
															while (false !== ($filename = readdir($dh))) 
															{
    														$pos = strpos($filename, "ttyU");
    														if ($pos !== false) 
    														{
  echo "													<option value='" . $filename . "'>" . $filename . "</option>";
    														}
															}
	
	echo "											<option value='ttyS1'>ttyS1</option>";
	echo "              			</select>";
	echo "              		</div>";
	echo "              	</div>";    									
	echo "      	    		<div class='form-group'><label class='col-sm-3 control-label'>Baud:</label>";
	echo "              		<div class='col-sm-3' style='min-width:150px; max-width:150px'>";
	echo "              			<select class='form-control m-b' size='0' name='modem_baud'>";
															$array = array("100","300","600","1200","2400","4800","9600","14400","19200","38400","57600","115200","380400");
															for($xsd=0;$xsd<13;$xsd++)
															{
																if($baud == $array[$xsd])
																{
	echo "													<option value='" . $array[$xsd] . "' selected>" . $array[$xsd] . "</option>";																		
																}
																else
																{
	echo "													<option value='" . $array[$xsd] . "'>" . $array[$xsd] . "</option>";																		
																}															
															}
	echo "              			</select>";
	echo "              		</div>";
	echo "              	</div>";    															
	echo "      	    		<div class='form-group'><label class='col-sm-3 control-label'>Flow Control:</label>";
	echo "              		<div class='col-sm-3' style='min-width:150px; max-width:150px'>";
	echo "              			<select class='form-control m-b' size='0' name='modem_flowctl'>";
															if($flowctl == "none")
															{
	echo "												<option value='none' selected>None</option>";															
															}
															else
															{
	echo "												<option value='none'>None</option>";																
															}
															if($flowctl == "xon")
															{
	echo "												<option value='xon' selected>XON/XOFF</option>";															
															}
															else
															{
	echo "												<option value='xon'>XON/XOFF</option>";																
															}
															if($flowctl == "rts")
															{
	echo "												<option value='rts' selected>RTSCTS</option>";															
															}
															else
															{
	echo "												<option value='rts'>RTSCTS</option>";																
															}														
	echo "              			</select>";
	echo "              		</div>";
	echo "              	</div>";
	echo "      	    		<div class='form-group'><label class='col-sm-3 control-label'>Init String:</label>";
	echo "              		<div class='col-sm-8' style='min-width:200px; max-width:200px'>";
	echo "              			<input type='text' class='form-control' name='modem_init' value='" . $init . "' />";
	echo "              		</div>";
	echo "              	</div>";
	echo "								<div class='form-group'><label class='col-sm-3 control-label'>Start on Boot:</label>";
	echo "              		<div class='col-sm-8' style='min-width:150px; max-width:150px'>";
	echo "										<div class='checkbox checkbox-success'><input id='modem_enabled' type='checkbox' name='modem_enabled' " . $sdhtml . " /><label for='modem_enabled'></label></div>";
	echo "              		</div>";
	echo "              	</div>";         	
	echo "              	<div class='form-group'>";
	echo "              	<div class='col-sm-3'>";
	echo "              	</div>";
	echo "              		<div class='col-sm-8' style='min-width:300px; max-width:300px'>";
	echo "              	  	<button name='modem_save_btn' class='btn btn-success' type='submit' onMouseOver='mouse_move(&#039;b_apply&#039;);' onMouseOut='mouse_move();'><i class='fa fa-check' ></i> Save</button>";
	echo "              	  	<button name='modem_cancel_btn' class='btn btn-primary' type='submit' onMouseOver='mouse_move(&#039;b_cancel&#039;);' onMouseOut='mouse_move();' formnovalidate><i class='fa fa-times'></i> Cancel</button>";
	echo "              	  </div>";
	echo "              	</div>";
	echo " 								<input type='hidden' name='modem_id' value='" . $id . "'>";
	echo " 								<input type='hidden' name='modem_command' value='" . $command . "'>";
	echo " 								<input type='hidden' name='modem_databits' value='" . $databits . "'>";
	echo " 								<input type='hidden' name='modem_parity' value='" . $parity . "'>";
	echo " 								<input type='hidden' name='modem_stopbits' value='" . $stopbits . "'>";	
	echo "              </fieldset>";  
	echo "						</form>";
	echo "      		</div> <!-- END PANEL BODY -->"; 
	echo "      	</div> <!-- END PANEL WRAPPER -->"; 
	echo "      </div>  <!-- END COL -->"; 
	echo "    </div> <!-- END ROW -->";
	echo "  </div> <!-- END CONTENT -->";    
	echo "</div> <!-- END Main Wrapper -->";
	echo "</body>";
	echo "</html>";
}

function rdb_add_edit($command,$id,$name,$device,$init,$baud,$flowctl,$enabled)
{
	if($command == "edit")
	{
		$header = "Edit RDB Settings";
	}
	else
	{
		$header = "ADD new USB Relay Board";
	}
	
	if($enabled == "off")
	{
		$sdhtml = "";
	}
	else
	{
		$sdhtml = "checked";
	}
	
	$rdb_id = trim(shell_exec('rmsrbd_id pid'));
	
	setup_top_header();
	start_header();
	left_nav("setup");
	echo "<script language='javascript' type='text/javascript'>";
	echo "SetContext('setup');";
	echo "</script>";
	echo "<!-- Main Wrapper -->";
	echo "<div id='wrapper'>";
	echo "	<div class='content animate-panel' data-effect='fadeInUp'>";
	echo "  	<!-- INFO BLOCK START -->";
	echo "  	<div class='row'>";
	echo "    	<div class='col-sm-12'>";
	echo "      	<div class='hpanel4'>";
	echo "      		<div class='panel-body' style='max-width:500px'>";
	echo "      	  	<form name='RDB' action='setup_device_manager.php' method='post' class='form-horizontal'>";  	
	echo "      	    	<fieldset>";
	echo "      	    		<legend><img src='images/relay1-32x32.gif'> " . $header . "</legend>";
	echo "      	    		<div class='form-group'><label class='col-sm-3 control-label'>RDB ID Found:</label>";
													if($rdb_id == "0000")
													{
														echo "<div class='col-sm-6' style='min-width:250px; max-width:250px'>";
														echo "	<input type='text' class='form-control' name='rdb_id' value='No USB Relay Boards Found' disabled />";
														echo "</div>";
													}
													else
													{
														echo "<div class='col-sm-4'>";
														echo "	<input type='text' class='form-control' name='rdb_id' value='" . $rdb_id . "' disabled />";
														echo "</div>";
													}
													
	echo "              	</div>"; 	 
	      	    											
	echo "								<div class='form-group'><label class='col-sm-3 control-label'>Start on Boot:</label>";
	echo "              		<div class='col-sm-8'>";
	echo "										<div class='checkbox checkbox-success'><input id='rdb_enabled' type='checkbox' name='rdb_enabled' " . $sdhtml . " /><label for='rdb_enabled'></label></div>";
	echo "              		</div>";
	echo "              	</div>";         	
	echo "              	<div class='form-group'>";
	echo "              		<div class='col-sm-8 col-sm-offset-3'>";
													if($rdb_id == "0000")
													{
														echo "<button name='rdb_save_btn' class='btn btn-success' type='submit' onMouseOver='mouse_move(&#039;b_apply&#039;);' onMouseOut='mouse_move();' disabled><i class='fa fa-check' ></i> Save</button>";
													}
													else
													{
														echo "<button name='rdb_save_btn' class='btn btn-success' type='submit' onMouseOver='mouse_move(&#039;b_apply&#039;);' onMouseOut='mouse_move();'><i class='fa fa-check' ></i> Save</button>";
													}
	echo "              	  	<button name='rdb_cancel_btn' class='btn btn-primary' type='submit' onMouseOver='mouse_move(&#039;b_cancel&#039;);' onMouseOut='mouse_move();' formnovalidate><i class='fa fa-times'></i> Cancel</button>";
	echo "              	  </div>";
	echo "              	</div>";
	echo " 								<input type='hidden' name='id' value='" . $id . "'>";
	echo " 								<input type='hidden' name='rdb_id' value='" . $rdb_id . "'>";	
	echo " 								<input type='hidden' name='rdb_command' value='" . $command . "'>";
	echo "              </fieldset>";  
	echo "						</form>";
	echo "      		</div> <!-- END PANEL BODY -->"; 
	echo "      	</div> <!-- END PANEL WRAPPER -->"; 
	echo "      </div>  <!-- END COL -->"; 
	echo "    </div> <!-- END ROW -->";
	echo "  </div> <!-- END CONTENT -->";    
	echo "</div> <!-- END Main Wrapper -->";
	echo "</body>";
	echo "</html>";
}

function vdb_add_edit($command,$id,$name,$device,$init,$baud,$flowctl,$enabled)
{
	if($command == "edit")
	{
		$header = "Edit VDB Settings";
	}
	else
	{
		$header = "ADD new USB Voltmeter Board";
	}
	
	if($enabled == "off")
	{
		$sdhtml = "";
	}
	else
	{
		$sdhtml = "checked";
	}
	
	$vdb_id = trim(shell_exec('rmsvdb_id pid'));
	
	setup_top_header();
	start_header();
	left_nav("setup");
	echo "<script language='javascript' type='text/javascript'>";
	echo "SetContext('setup');";
	echo "</script>";
	echo "<!-- Main Wrapper -->";
	echo "<div id='wrapper'>";
	echo "	<div class='content animate-panel' data-effect='fadeInUp'>";
	echo "  	<!-- INFO BLOCK START -->";
	echo "  	<div class='row'>";
	echo "    	<div class='col-sm-12'>";
	echo "      	<div class='hpanel4' style='max-width:500px'>";
	echo "      		<div class='panel-body'>";
	echo "      	  	<form name='VDB' action='setup_device_manager.php' method='post' class='form-horizontal'>";  	
	echo "      	    	<fieldset>";
	echo "      	    		<legend><img src='images/relay1-32x32.gif'> " . $header . "</legend>";
	echo "      	    		<div class='form-group'><label class='col-sm-3 control-label'>VDB ID Found:</label>";
													if($vdb_id == "0000")
													{
														echo "<div class='col-sm-6' style='min-width:270px; max-width:270px'>";
														echo "	<input type='text' class='form-control' name='vdb_id' value='No USB Voltmeter Boards Found' disabled />";
														echo "</div>";
													}
													else
													{
														echo "<div class='col-sm-4'>";
														echo "	<input type='text' class='form-control' name='vdb_id' value='" . $vdb_id . "' disabled />";
														echo "</div>";
													}
													
	echo "              	</div>"; 	 
	      	    											
	echo "								<div class='form-group'><label class='col-sm-3 control-label'>Start on Boot:</label>";
	echo "              		<div class='col-sm-8'>";
	echo "										<div class='checkbox checkbox-success'><input id='vdb_enabled' type='checkbox' name='vdb_enabled' " . $sdhtml . " /><label for='vdb_enabled'></label></div>";
	echo "              		</div>";
	echo "              	</div>";         	
	echo "              	<div class='form-group'>";
	echo "              		<div class='col-sm-8 col-sm-offset-3'>";
													if($vdb_id == "0000")
													{
														echo "<button name='vdb_save_btn' class='btn btn-success' type='submit' onMouseOver='mouse_move(&#039;b_apply&#039;);' onMouseOut='mouse_move();' disabled><i class='fa fa-check' ></i> Save</button>";
													}
													else
													{
														echo "<button name='vdb_save_btn' class='btn btn-success' type='submit' onMouseOver='mouse_move(&#039;b_apply&#039;);' onMouseOut='mouse_move();'><i class='fa fa-check' ></i> Save</button>";
													}
	echo "              	  	<button name='vdb_cancel_btn' class='btn btn-primary' type='submit' onMouseOver='mouse_move(&#039;b_cancel&#039;);' onMouseOut='mouse_move();' formnovalidate><i class='fa fa-times'></i> Cancel</button>";
	echo "              	  </div>";
	echo "              	</div>";
	echo " 								<input type='hidden' name='id' value='" . $id . "'>";
	echo " 								<input type='hidden' name='vdb_id' value='" . $vdb_id . "'>";	
	echo " 								<input type='hidden' name='vdb_command' value='" . $command . "'>";
	echo "              </fieldset>";  
	echo "						</form>";
	echo "      		</div> <!-- END PANEL BODY -->"; 
	echo "      	</div> <!-- END PANEL WRAPPER -->"; 
	echo "      </div>  <!-- END COL -->"; 
	echo "    </div> <!-- END ROW -->";
	echo "  </div> <!-- END CONTENT -->";    
	echo "</div> <!-- END Main Wrapper -->";
	echo "</body>";
	echo "</html>";
}

function extemp_add_edit($command,$id,$name,$device,$init,$baud,$flowctl,$enabled,$file_path,$icon_path)
{
	if($command == "edit")
	{
		$header = "Edit USB External Temperature Settings";
	}
	else
	{
		$header = "ADD new USB Temperature Sensor";
	}
	
	if($enabled == "off")
	{
		$sdhtml = "";
	}
	else
	{
		$sdhtml = "checked";
	}
	setup_top_header();
	start_header();
	left_nav("setup");
	echo "<script language='javascript' type='text/javascript'>";
	echo "SetContext('setup');";
	echo "</script>";
	echo "<!-- Main Wrapper -->";
	echo "<div id='wrapper'>";
	echo "	<div class='content animate-panel' data-effect='fadeInUp'>";
	echo "  	<!-- INFO BLOCK START -->";
	echo "  	<div class='row'>";
	echo "    	<div class='col-sm-12'>";
	echo "      	<div class='hpanel4'>";
	echo "      		<div class='panel-body' style='max-width:500px'>";
	echo "      	  	<form name='EXTEMP' action='setup_device_manager.php' method='post' class='form-horizontal'>";  	
	echo "      	    	<fieldset>";
	echo "      	    		<legend><img src='images/extemp32x32.gif'> " . $header . "</legend>";
	echo "      	    		<div class='form-group'><label class='col-sm-3 control-label'>Name:</label>";
	echo "              		<div class='col-sm-8' style='min-width:250px; max-width:250px'>";
	echo "              			<input type='text' class='form-control' name='extemp_name' value='" . $name . "' required />";
	echo "              		</div>";
	echo "              	</div>";
	echo "								<div class='form-group'><label class='col-sm-3 control-label'>Start on Boot:</label>";
	echo "              		<div class='col-sm-8'>";
	echo "										<div class='checkbox checkbox-success'><input id='extemp_enabled' type='checkbox' name='extemp_enabled' " . $sdhtml . " /><label for='extemp_enabled'></label></div>";
	echo "              		</div>";
	echo "              	</div>";
	echo "              		<div class='col-sm-8 col-sm-offset-3'>";												
	echo "										<button name='extemp_save_btn' class='btn btn-success' type='submit' onMouseOver='mouse_move(&#039;b_apply&#039;);' onMouseOut='mouse_move();'><i class='fa fa-check' ></i> Save</button>";
	echo "              	  	<button name='extemp_cancel_btn' class='btn btn-primary' type='submit' onMouseOver='mouse_move(&#039;b_cancel&#039;);' onMouseOut='mouse_move();' formnovalidate><i class='fa fa-times'></i> Cancel</button>";
	echo "              	  </div>";
	echo "              	</div>";
	echo " 								<input type='hidden' name='extemp_id' value='" . $id . "'>";
	echo " 								<input type='hidden' name='extemp_command' value='" . $command . "'>";
	echo " 								<input type='hidden' name='extemp_enabled' value='" . $enabled . "'>";	
	echo "              </fieldset>";  
	echo "						</form>";
	echo "      		</div> <!-- END PANEL BODY -->"; 
	echo "      	</div> <!-- END PANEL WRAPPER -->"; 
	echo "      </div>  <!-- END COL -->"; 
	echo "    </div> <!-- END ROW -->";
	echo "  </div> <!-- END CONTENT -->";    
	echo "</div> <!-- END Main Wrapper -->";
	echo "</body>";
	echo "</html>";
}


function efoy_add_edit($command,$id,$name,$device,$init,$baud,$flowctl,$enabled,$sdvar1,$sdvar2,$sdvar3,$sdvar4,$sdvar5,$sdvar6,$sdvar7,$sdvar8,$sdvar9,$sdvar10,$sdvar11,$sdvar12)
{
	if($command == "edit")
	{
		$header = "Edit Efoy Device Settings";
	}
	else
	{
		$header = "Add Efoy Device";
	}
	
	
	$from = $sdvar2;
	$smtp = $sdvar3;
	$to = $sdvar4;
	$subject = $sdvar5;
	$smtp_port = $sdvar6;
	$auth_check = $sdvar7;
	$username = $sdvar8;
	$password = $sdvar9;
	$ssl = $sdvar10;
	$tls = $flowctl;
	$email_enabled = $sdvar11;
	$how_often = $sdvar12;
	
	setup_top_header();
	start_header();
	left_nav("setup");
	echo "<link rel='stylesheet' href='css/jquery.bootstrap-touchspin.min.css' />\n";
	echo "<script src='javascript/jquery.bootstrap-touchspin.min.js'></script>\n";
	echo "<script language='javascript' type='text/javascript'>";
	echo "SetContext('setup');";
	echo "</script>";
	echo "<!-- Main Wrapper -->";
	echo "<div id='wrapper'>";
	echo "	<div class='content animate-panel' data-effect='fadeInUp'>";
	echo "  	<!-- INFO BLOCK START -->";
	echo "  	<div class='row'>";
	echo "    	<div class='col-sm-12'>";
	echo "      	<div class='hpanel4'>";
	echo "      		<div class='panel-body' style='max-width:600px; padding-bottom:25px'>";
	
	if($email_enabled == "CHECKED")
	{
		echo "      	  	<form name='EFOY' id='EFOY' action='setup_device_manager.php' method='post' class='form-horizontal'>";  		 	
	}
	else
	{
		echo "      	  	<form name='EFOY' id='EFOY' action='setup_device_manager.php' method='post' class='form-horizontal' novalidate>"; 
	}

	echo "      	    	<fieldset>";
	echo "      	    		<legend><img src='images/efoy.gif'> " . $header . "</legend>";	
	echo "      	    		<div class='form-group'><label class='col-sm-3 control-label' style='text-align:left; max-width:140px; min-width:140px;'>Com Port:</label>";
	echo "              		<div class='col-sm-12 input-group' style='min-width:150px; max-width:150px'>";
	echo "              			<select class='form-control m-b' size='0' name='efoy_device'>";
															$dh  = opendir("/dev");
															while (false !== ($filename = readdir($dh))) 
															{
    														$pos = strpos($filename, "ttyU");
    														if ($pos !== false) 
    														{
    															if($device == $filename)
    															{
    																echo "<option value='" . $filename . "' selected>" . $filename . "</option>";
    															}
  																else
  																{
  																	echo "<option value='" . $filename . "'>" . $filename . "</option>";
  																}
    														}
															}
															if($device == "/dev/ttyS0")
    															{
    																echo "<option value='/dev/ttyS0' selected>ttyS0</option>";
    															}
  																else
  																{
  																	echo "<option value='/dev/ttyS0'>ttyS0</option>";
  																}
	
	echo "              			</select>";
	echo "              		</div>";
	echo "              	</div>";
	
	echo "      	    		<div class='form-group'><label class='col-sm-3 control-label' style='text-align:left; max-width:140px; min-width:140px;'>Efoy Voltage:</label>";
	echo "              		<div class='col-sm-12 input-group' style='min-width:150px; max-width:150px'>";
	echo "              			<select class='form-control m-b' size='0' name='efoy_var1'>";
														if($sdvar1 == "12v")
    															{
    																echo "<option value='12v' selected>12v</option>";
    															}
  																else
  																{
  																	echo "<option value='12v'>12v</option>";
  																}
  													if($sdvar1 == "24v")
    															{
    																echo "<option value='24v' selected>24v</option>";
    															}
  																else
  																{
  																	echo "<option value='24v'>24v</option>";
  																}			
	echo "              			</select>";
	echo "              		</div>";
	echo "              	</div>";    						
	    						
	echo "								<div class='form-group'><label class='col-sm-3 control-label' style='text-align:left; max-width:140px; min-width:140px;'>Start on Boot:</label>";
	echo "              		<div class='col-sm-12 input-group' style='min-width:150px; max-width:150px'>";
	echo "										<div class='checkbox checkbox-success'><input id='efoy_enabled' type='checkbox' name='efoy_enabled' " . $enabled . " /><label for='efoy_enabled'></label></div>";
	echo "              		</div>";
	echo "              	</div>\n";
	
	echo "								<legend></legend>\n";
	   						
	echo "								<div class='form-group'><label class='col-sm-3 control-label' style='text-align:left; max-width:140px; min-width:140px'></label>\n";
  echo "	            		<div class='col-sm-12 checkbox checkbox-success' style='max-width:250px; min-width:250px'>\n";
  echo "	            			<input type='checkbox' onclick='using_email();' id='email_enabled' name='email_enabled' " . $email_enabled . " />\n";
  echo "	  		            <label for='email_enabled'><strong>Sending Email Enabled?</strong></label>\n";
  echo "	            		</div>\n";
  echo "	            	</div>\n";
	
	echo "      	    		<div class='form-group'><label class='col-sm-3 control-label' style='text-align:left; max-width:140px; min-width:140px;'>Send Every:</label>";
	echo "              		<div class='col-sm-12 input-group' style='min-width:150px; max-width:150px'>";
	echo "              			<select class='form-control m-b' size='0' name='how_often'>";
	echo "											<option value='0'>One Shot</option>";
															$ii=1;	if($how_often==$ii) {$chan=sprintf("selected");} else {$chan=sprintf(" ");} echo"<option ".$chan." value=".$ii.">".$ii." Second</option>";	
															for($ii=2; $ii<60; $ii++)	{	if($how_often==$ii) {$chan = "selected";} else {$chan = " ";} echo"<option " . $chan . " value='" . $ii . "'>" . $ii . " Seconds</option>";	}
															$ii=1; if($how_often==($ii*60)) {$chan = "selected";} else {$chan = " ";} echo "<option " . $chan . " value='" . $ii*60 . "'>" . $ii . " Minute</option>";
															for($ii=2; $ii<60; $ii++)	{	if($how_often==($ii*60)) {$chan = "selected";} else {$chan = " ";} echo "<option " . $chan . " value='" . $ii*60 . "'>" . $ii . " Minutes</option>";	}
															$ii=1;	if($how_often==($ii*60*60)) {$chan = "selected";} else {$chan = " ";} echo "<option " . $chan . " value='" . $ii*60*60 . "'>" . $ii . " Hour</option>";
															for($ii=2; $ii<25; $ii++)	{ if($how_often==($ii*60*60)) {$chan = "selected";} else {$chan = " ";} echo "<option " . $chan . " value='" . $ii*60*60 . "'>" . $ii . " Hours</option>";	}
	echo "              			</select>";
	echo "              		</div>";
	echo "              	</div>";
	
	echo "								<div class='form-group'><label class='col-sm-3 control-label' style='text-align:left; max-width:140px; min-width:140px'>From:</label>\n";
  echo "	            		<div class='col-sm-12 input-group' style='max-width:350px'>\n";
  echo "	            			<span class='input-group-addon'><i class='fa fa-envelope' style='max-width:16px; min-width:16px'></i></span>\n";
  echo "	            			<input type='text' class='form-control' name='from' value='" . $from . "' required/>\n";
  echo "	            		</div>\n";
  echo "	            	</div>\n";
	
	echo "								<div class='form-group'><label class='col-sm-3 control-label' style='text-align:left; max-width:140px; min-width:140px'>SMTP Server:</label>\n";
  echo "	            		<div class='col-sm-12 input-group' style='max-width:350px'>\n";
  echo "	            			<span class='input-group-addon'><i class='fa fa-desktop' style='max-width:16px; min-width:16px'></i></span>\n";
  echo "	            			<input type='text' class='form-control' name='smtp' value='" . $smtp . "' required/>\n";
  echo "	            		</div>\n";
  echo "	            	</div>\n";
	
	echo "								<div class='form-group'><label class='col-sm-3 control-label' style='text-align:left; max-width:140px; min-width:140px'>To:</label>\n";
  echo "	            		<div class='col-sm-12 input-group' style='max-width:350px'>\n";
  echo "	            			<span class='input-group-addon'><i class='fa fa-envelope' style='max-width:16px; min-width:16px'></i></span>\n";
  echo "	            			<input type='text' class='form-control' name='to' value='" . $to . "' required/>\n";
  echo "	            		</div>\n";
  echo "	            	</div>\n";
	
	echo "								<div class='form-group'><label class='col-sm-3 control-label' style='text-align:left; max-width:140px; min-width:140px'>Subject:</label>\n";
  echo "	            		<div class='col-sm-12 input-group' style='max-width:350px'>\n";
  echo "	            			<span class='input-group-addon'><i class='fa fa-question-circle' style='max-width:16px; min-width:16px'></i></span>\n";
  echo "	            			<input type='text' class='form-control' name='subject' value='" . $subject . "' required/>\n";
  echo "	            		</div>\n";
  echo "	            	</div>\n";
	
	echo "								<div class='form-group'><label class='col-sm-3 control-label' style='text-align:left; max-width:125px; min-width:125px'>SMTP Port</label>\n";
  echo "	            		<div class='col-sm-12' style='max-width:180px' onMouseOver='mouse_move(\"sd_timers_info\");' onMouseOut='mouse_move();'>\n";
  echo "	            			<input id='smtp_port' type='text' name='smtp_port' style='text-align:center' value='" . $smtp_port . "'>\n";
  echo "	            		</div>\n";
  echo "	            	</div>\n";
	
	echo "								<div class='form-group'><label class='col-sm-3 control-label' style='text-align:left; max-width:140px; min-width:140px'></label>\n";
  echo "	            		<div class='col-sm-12 checkbox checkbox-success' style='max-width:250px; min-width:250px'>\n";
  echo "	            			<input type='checkbox' onclick='validate();' id='auth_check' name='auth_check'" . $auth_check . " />\n";
  echo "	  		            <label for='auth_check'><strong>Use Authorization?</strong></label>\n";
  echo "	            		</div>\n";
  echo "	            	</div>\n";
	
	echo "								<div class='form-group'><label class='col-sm-3 control-label' style='text-align:left; max-width:140px; min-width:140px'>Username:</label>\n";
  echo "            			<div class='col-sm-12 input-group' style='max-width:350px'>\n";
  echo "            				<span class='input-group-addon'><i class='fa fa-user' style='max-width:16px; min-width:16px'></i></span>\n";
              							if($auth_check == "CHECKED")
              							{
              								echo "<input type='text' class='form-control' id='username' name='username' value='".$username."' required/>\n";
              							}
              							else
              							{
              								echo "<input type='text' class='form-control' id='username'name='username' value='".$username."' disabled/>\n";
              							}
  echo "            		</div>\n";
  echo "            	</div>\n";
								
	echo "							<div class='form-group'><label class='col-sm-3 control-label' style='text-align:left; max-width:140px; min-width:140px'>Password:</label>\n";
  echo "            		<div class='col-sm-12 input-group' style='max-width:350px'>\n";
  echo "            			<span class='input-group-addon'><i class='fa fa-sign-in' style='max-width:16px; min-width:16px'></i></span>\n";
              							if($auth_check == "CHECKED")
              							{
              								echo "<input type='password' class='form-control' id='password' name='password' value='".$password."' required/>\n";
              							}
              							else
              							{
              								echo "<input type='password' class='form-control' id='password' name='password' value='".$password."' disabled />\n";
              							}
  echo "            		</div>\n";
  echo "            	</div>\n";
								
	echo "							<div class='form-group'><label class='col-sm-3 control-label' style='text-align:left; max-width:140px; min-width:140px'></label>\n";
  echo "            		<div class='col-sm-12 radio radio-success' style='max-width:150px; min-width:150px'>\n";
    				  						if($auth_check == "CHECKED")
    				  						{
    				  							echo "<input type='radio' id='auth_group' name='auth_group' value='starttls' ".$tls."/>\n";
    				  						}
    				  						else
    				  						{
    				  							echo "<input type='radio' id='auth_group' name='auth_group' value='starttls' CHECKED disabled/>\n";
    				  						}
  echo "                	<label for='auth_group'><strong>Use StartTLS ?</strong></label>\n";
  echo "               </div>\n";
  echo "            	</div>\n";
								
	echo "							<div class='form-group'><label class='col-sm-3 control-label' style='text-align:left; max-width:140px; min-width:140px'></label>\n";
  echo "            		<div class='col-sm-12 radio radio-success' style='max-width:150px; min-width:150px'>\n";
    				  						if($auth_check == "CHECKED")
    				  						{
    				  							echo "<input type='radio' id='auth_group' name='auth_group' value='ssl' ".$ssl."/>\n";
    				  						}
    				  						else
    				  						{
    				  							echo "<input type='radio' id='auth_group' name='auth_group' value='ssl' ".$ssl." disabled/>\n";
    				  						}
  echo "                  <label for='auth_group'><strong>Use SSL ?</strong></label>\n";
  echo "                </div>\n";
  echo "            	</div>\n";
	
	echo "              	<div class='col-sm-8'>";												
	echo "									<button name='efoy_save_btn' class='btn btn-success' type='submit' onMouseOver='mouse_move(&#039;b_apply&#039;);' onMouseOut='mouse_move();'><i class='fa fa-check' ></i> Save</button>";
	echo "              	  <button name='efoy_cancel_btn' class='btn btn-primary' type='submit' onMouseOver='mouse_move(&#039;b_cancel&#039;);' onMouseOut='mouse_move();' formnovalidate><i class='fa fa-times'></i> Cancel</button>";
	echo "              	</div>";
	echo " 								<input type='hidden' name='efoy_id' value='" . $id . "'>";
	echo " 								<input type='hidden' name='efoy_name' value='EFOY'>";
	echo " 								<input type='hidden' name='efoy_command' value='" . $command . "'>";
	echo "              </fieldset>";  
	echo "						</form>";
	echo "      		</div> <!-- END PANEL BODY -->"; 
	echo "      	</div> <!-- END PANEL WRAPPER -->"; 
	echo "      </div>  <!-- END COL -->"; 
	echo "    </div> <!-- END ROW -->";
	echo "  </div> <!-- END CONTENT -->";    
	echo "</div> <!-- END Main Wrapper -->";
	
	echo "<script>\n";
	echo "function validate()\n"; 
	echo "{\n";
  echo "	if (document.getElementById('auth_check').checked)\n"; 
  echo "	  {\n";
  echo "			$('#username').attr('required',true);\n";
  echo "      $('#password').attr('required',true);\n";
  echo "      $('#username').removeAttr('disabled');\n";
  echo "      $('#password').removeAttr('disabled');\n";
  echo "      $('input:radio').removeAttr('disabled');\n";           
  echo "    }\n";
  echo "  else\n"; 
  echo "    {\n";
  echo "          $('#username').attr('required',false);\n";
  echo "          $('#password').attr('required',false);\n";
  echo "          $('#username').attr('disabled', true);\n";
  echo "          $('#password').attr('disabled', true);\n";
  echo "          $('input:radio').attr('disabled', true);\n";
  echo "      }\n";
	echo "}\n";
	echo "</script>\n";
	
	echo "<script>\n";
	echo "function using_email()\n"; 
	echo "{\n";
  echo "	if (document.getElementById('email_enabled').checked)\n"; 
  echo "	  {\n";
  echo "			document.getElementById('EFOY').noValidate = false;\n";       
  echo "    }\n";
  echo "  else\n"; 
  echo "    {\n";
  echo "			document.getElementById('EFOY').noValidate = true;\n";
  echo "    }\n";
	echo "}\n";
	echo "</script>\n";
	
	echo "<script>\n";
	echo "$(function(){\n";
	echo "    $('#smtp_port').TouchSpin({\n";
	echo "        min: 1,\n";
	echo "        max: 65536,\n";
	echo "        step: 1,\n";
	echo "        decimals: 0,\n";
	echo "        boostat: 5,\n";
	echo "        maxboostedstep: 10,\n";
	echo "    });\n";
	echo "});\n";
	echo "</script>\n";

	
	
	
	echo "</body>";
	echo "</html>";
}


function custom_add_edit($command,$id,$name,$device,$init,$baud,$flowctl,$enabled,$file_path,$icon_path)
{
	if($command == "edit")
	{
		$header = "Edit CUSTOM Device Settings";
	}
	else
	{
		$header = "ADD new CUSTOM Device";
	}
	
	if($enabled == "off")
	{
		$sdhtml = "";
	}
	else
	{
		$sdhtml = "checked";
	}
	setup_top_header();
	start_header();
	left_nav("setup");
	echo "<script language='javascript' type='text/javascript'>";
	echo "SetContext('setup');";
	echo "</script>";
	echo "<!-- Main Wrapper -->";
	echo "<div id='wrapper'>";
	echo "	<div class='content animate-panel' data-effect='fadeInUp'>";
	echo "  	<!-- INFO BLOCK START -->";
	echo "  	<div class='row'>";
	echo "    	<div class='col-sm-12'>";
	echo "      	<div class='hpanel4'>";
	echo "      		<div class='panel-body' style='max-width:600px;'>";
	echo "      	  	<form name='CUSTOM' action='setup_device_manager.php' method='post' class='form-horizontal'>";  	
	echo "      	    	<fieldset>";
	echo "      	    		<legend><img src='images/custom32x32.gif'> " . $header . "</legend>";
	echo "      	    		<div class='form-group'><label class='col-sm-3 control-label'>Name:</label>";
	echo "              		<div class='col-sm-8' style='min-width:250; max-width:250px'>";
	echo "              			<input type='text' class='form-control' name='custom_name' value='" . $name . "' required />";
	echo "              		</div>";
	echo "              	</div>"; 	 
	
	echo "      	    		<div class='form-group'><label class='col-sm-3 control-label'>File Path:</label>";
	echo "              		<div class='col-sm-8' style='min-width:250; max-width:250px'>";
	echo "              			<input type='text' class='form-control' name='file_path' placeholder='custom/myfile.html' value='" . $file_path . "' />";
	echo "              		</div>";
	echo "              	</div>";
	echo "      	    		<div class='form-group'><label class='col-sm-3 control-label'>Icon Path:</label>";
	echo "              		<div class='col-sm-8' style='min-width:250; max-width:250px'>";
	echo "              			<input type='text' class='form-control' name='icon_path' placeholder='custom/my16x16icon.gif' value='" . $icon_path . "' />";
	echo "              		</div>";
	echo "              	</div>";         	
	echo "              	<div class='form-group'>";
	echo "              		<div class='col-sm-8 col-sm-offset-3'>";
	echo "              	  	<button name='custom_save_btn' class='btn btn-success' type='submit' onMouseOver='mouse_move(&#039;b_apply&#039;);' onMouseOut='mouse_move();'><i class='fa fa-check' ></i> Save</button>";
	echo "              	  	<button name='custom_cancel_btn' class='btn btn-primary' type='submit' onMouseOver='mouse_move(&#039;b_cancel&#039;);' onMouseOut='mouse_move();' formnovalidate><i class='fa fa-times'></i> Cancel</button>";
	echo "              	  </div>";
	echo "              	</div>";
	echo " 								<input type='hidden' name='custom_id' value='" . $id . "'>";
	echo " 								<input type='hidden' name='custom_command' value='" . $command . "'>";
	echo " 								<input type='hidden' name='custom_init' value='" . $init . "'>";
	echo " 								<input type='hidden' name='custom_baud' value='" . $baud . "'>";
	echo " 								<input type='hidden' name='custom_flowctl' value='" . $flowctl . "'>";
	echo " 								<input type='hidden' name='custom_device' value='" . $device . "'>";	
	echo " 								<input type='hidden' name='custom_enabled' value='" . $enabled . "'>";	
	echo "              </fieldset>";  
	echo "						</form>";
	echo "      		</div> <!-- END PANEL BODY -->"; 
	echo "      	</div> <!-- END PANEL WRAPPER -->"; 
	echo "      </div>  <!-- END COL -->"; 
	echo "    </div> <!-- END ROW -->";
	echo "  </div> <!-- END CONTENT -->";    
	echo "</div> <!-- END Main Wrapper -->";
	echo "</body>";
	echo "</html>";
}






?>




