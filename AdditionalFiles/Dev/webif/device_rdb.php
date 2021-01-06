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
	$hb_led_check = "";
	$rly1_led_check = "";
	$rly2_led_check = "";
	$rly3_led_check = "";
	$rly4_led_check = "";
	$rly5_led_check = "";
	
	if(!file_exists("/var/run/rmsrbd.pid"))
	{
		exec("/etc/init.scripts/S97rmsrbd start > /dev/null");
		sleep(1);
	}
	
	$rdb_pid = trim(shell_exec('rmsrbd_id pid'));
	$rdb_dev = trim(shell_exec('rmsrbd_id dev'));
	$rdb_leds =  trim(file_get_contents("/var/rmsdata/rdbrelayleds"));
	//RELAY ACTION CONFIRMATION
	$result  = $dbh->query("SELECT * FROM rdbrelayconf;");
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
			$command = "rmsrelay rdb ".$rdb_dev." ".$relay_num." ".$command;
			exec($command);
			sleep(1);
		}
	}




if(isset ($_GET['action']))
	{
		$action = $_GET['action'];
		$id = $_GET['id'];
		if($action == "run")
		{
			if($relay_confirmation == "checked")
			{
				$alert_flag = "3";
			}
			else
			{
				$command = "rmsscript ".$id.".";
				exec($command);
			}
		}
		
		else if($action == "delete")
		{
			$alert_flag = "4";
		}
		
		else if($action == "edit")
		{
			$success = $_GET['success'];
			if($success == "yes")
			{
				$text = "Relay #".$id." Edit Success!";
				$alert_flag = "2";
			}
		}
	}

if(isset ($_GET['confirm']))
	{
		$confirm = $_GET['confirm'];
		$id = $_GET['id'];
		if($confirm == "run")
		{
			$command = "rmsscript ".$id.".";
			exec($command);
			$text = "Script ID #".$id." has been executed!";
			$alert_flag = "2";
		}
		else if($confirm == "delete")
		{
			$query = sprintf("DELETE FROM scripts WHERE id='%s';",$id);
			$result = $dbh->exec($query);
			z_seek_destroy_script_refs($id);
			restart_some_services();
			$text = "Script ID #".$id." has been Deleted!";
			$alert_flag = "2";
		}

	}
	
	if(isset ($_GET['execute']))
	{
		$execute = $_GET['execute'];
		if($execute == "yes")
		{
			$rly = $_GET['relay'];
			$cmd = $_GET['command'];
			$sd = "rmsrelay rdb ".$rdb_dev." ".$rly." ".$cmd;
			exec($sd);
			sleep(1);
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
	header("Location: setup.php");
}

// Led Button	was clicked
if(isset ($_POST['led_btn']))
{
	$leds = 0;
	if(isset ($_POST['led1'])){$leds = $leds | 1;}
	if(isset ($_POST['led2'])){$leds = $leds | 2;}
	if(isset ($_POST['led3'])){$leds = $leds | 4;}
	if(isset ($_POST['led4'])){$leds = $leds | 8;}
	if(isset ($_POST['led5'])){$leds = $leds | 16;}
	if(isset ($_POST['hb'])){$leds = $leds | 32;}
	$command = "rmsrbd_id write ".$leds;	
	exec($command);
	sleep(1);
	$text = "Settings Saved!";
	$alert_flag = "1";
}

	
// Relay Action Confirmation Button	was clicked
if(isset ($_POST['confirmation_btn']))
{	
	if(isset ($_POST['action_confirmation']))
	{
		$result  = $dbh->exec("UPDATE rdbrelayconf SET confirmation='1';");
		$relay_confirmation = "checked";
	}
	else
	{
		$result  = $dbh->exec("UPDATE rdbrelayconf SET confirmation='0';");
		$relay_confirmation = " ";
	}
	$text = "Settings Saved!";
	$alert_flag = "1";
}
	
	
	
	
escape_hatch:	
	$rdb_pid = trim(shell_exec('rmsrbd_id pid'));
	$rdb_dev = trim(shell_exec('rmsrbd_id dev'));
	$rdb_leds =  trim(file_get_contents("/var/rmsdata/rdbrelayleds"));
	
	$rly1_led = ($rdb_leds & 1);
	$rly2_led = ($rdb_leds & 2);
	$rly3_led = ($rdb_leds & 4);
	$rly4_led = ($rdb_leds & 8);
	$rly5_led = ($rdb_leds & 16);
	$hb_led = ($rdb_leds & 32);
	
	if($rly1_led == 1)
	{
		$rly1_led_check = "checked";
	}
	
	if($rly2_led == 2)
	{
		$rly2_led_check = "checked";
	}
	
	if($rly3_led == 4)
	{
		$rly3_led_check = "checked";
	}
	
	if($rly4_led == 8)
	{
		$rly4_led_check = "checked";
	}
	
	if($rly5_led == 16)
	{
		$rly5_led_check = "checked";
	}

	if($hb_led == 32)
	{
		$hb_led_check = "checked";
	}
	
	//RELAY 1
	$result  = $dbh->query("SELECT * FROM rdb WHERE id='1';");
	foreach($result as $row)
	{
		$rly1_name = $row['name'];
		$rly1_notes = $row['notes'];
	}
	
	//RELAY 2
	$result  = $dbh->query("SELECT * FROM rdb WHERE id='2';");
	foreach($result as $row)
	{
		$rly2_name = $row['name'];
		$rly2_notes = $row['notes'];
	}
	
	//RELAY 3
	$result  = $dbh->query("SELECT * FROM rdb WHERE id='3';");
	foreach($result as $row)
	{
		$rly3_name = $row['name'];
		$rly3_notes = $row['notes'];
	}
	
	//RELAY 4
	$result  = $dbh->query("SELECT * FROM rdb WHERE id='4';");
	foreach($result as $row)
	{
		$rly4_name = $row['name'];
		$rly4_notes = $row['notes'];
	}
	
	//RELAY 5
	$result  = $dbh->query("SELECT * FROM rdb WHERE id='5';");
	foreach($result as $row)
	{
		$rly5_name = $row['name'];
		$rly5_notes = $row['notes'];
	}
	
	$result  = $dbh->query("SELECT * FROM relay_script_cmds WHERE command='0Z';");
	foreach($result as $row)
	{
		$NO1 = $row['state'];
	}
	
	$result  = $dbh->query("SELECT * FROM relay_script_cmds WHERE command='10';");
	foreach($result as $row)
	{
		$NC1 = $row['state'];
	}
	
	$result  = $dbh->query("SELECT * FROM relay_script_cmds WHERE command='11';");
	foreach($result as $row)
	{
		$NO2 = $row['state'];
	}
	
	$result  = $dbh->query("SELECT * FROM relay_script_cmds WHERE command='12';");
	foreach($result as $row)
	{
		$NC2 = $row['state'];
	}
	
	$result  = $dbh->query("SELECT * FROM relay_script_cmds WHERE command='13';");
	foreach($result as $row)
	{
		$NO3 = $row['state'];
	}
	
	$result  = $dbh->query("SELECT * FROM relay_script_cmds WHERE command='14';");
	foreach($result as $row)
	{
		$NC3 = $row['state'];
	}
	
	$result  = $dbh->query("SELECT * FROM relay_script_cmds WHERE command='15';");
	foreach($result as $row)
	{
		$NO4 = $row['state'];
	}
	
	$result  = $dbh->query("SELECT * FROM relay_script_cmds WHERE command='16';");
	foreach($result as $row)
	{
		$NC4 = $row['state'];
	}
	
	$result  = $dbh->query("SELECT * FROM relay_script_cmds WHERE command='17';");
	foreach($result as $row)
	{
		$NO5 = $row['state'];
	}
	
	$result  = $dbh->query("SELECT * FROM relay_script_cmds WHERE command='18';");
	foreach($result as $row)
	{
		$NC5 = $row['state'];
	}
	
	
	$relay1 = trim(file_get_contents("/var/rmsdata/rdbrelay1"));
	$relay2 = trim(file_get_contents("/var/rmsdata/rdbrelay2"));
	$relay3 = trim(file_get_contents("/var/rmsdata/rdbrelay3"));
	$relay4 = trim(file_get_contents("/var/rmsdata/rdbrelay4"));
	$relay5 = trim(file_get_contents("/var/rmsdata/rdbrelay5"));

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
			SetContext('rdb');
		</script>
		
</head>
<body class="fixed-navbar fixed-sidebar">
<div class='splash'> <div class='solid-line'></div><div class='splash-title'><h1>Loading... Please Wait...</h1><div class='spinner'> <div class='rect1'></div> <div class='rect2'></div> <div class='rect3'></div> <div class='rect4'></div> <div class='rect5'></div> </div> </div> </div>

<!--[if lt IE 7]>
<p class="alert alert-danger">You are using an <strong>old</strong> web browser. Please upgrade your browser to improve your experience.</p>
<![endif]-->

<?php start_header(); ?>

<?php left_nav("rdb"); ?>
<script language="javascript" type="text/javascript">
	SetContext('rdb');
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
      		<div class="panel-body">
      	  	<form name='rdb' action='device_rdb.php' method='post' class="form-horizontal">  	
      	    	<fieldset>
      	    		<legend><img src="images/relay1-32x32.gif"> USB Relays</legend>
              	
              	<div class="form-group">
              		<div class="col-sm-2">
              			<div class="checkbox checkbox-success">
              				<input type="checkbox" id="action_confirmation" name="action_confirmation" <?php echo $relay_confirmation; ?> />
    		        			<label for="action_confirmation">Relay Action Confirmation?</label>
              			</div>
              		</div>
              		<div class="col-sm-2" style="text-align:center">
              			<a href="setup_scripts_add_edit.php?action=add&type=RELAY" onMouseOver="mouse_move('sd_addrelayscript');" onMouseOut='mouse_move();'>
      	  	  				<img src="images/script.gif" title='Add New Relay Script'><br><span><b>Add New Relay Script</b></span>
      	  	  				</a>
              		</div>
              	</div>
              	
              	<div class="form-group">	
              	  <div class="col-sm-4">
              				<button name="confirmation_btn" class="btn btn-success" type="submit" onMouseOver="mouse_move(&#039;b_apply&#039;);" onMouseOut="mouse_move();"><i class="fa fa-check" ></i> Apply</button>	
              		</div>
              	</div>
              	<div class="form-group"></div>	    	
								
								<legend>Relay Setup</legend> 
								<div class="table-responsive">
            			<table width="100%">
              			<tbody>
              		  	<tr>
              		  		<td style="text-align:center;"><a href="setup_rdbrelays.php?relay=1" onMouseOver="Tip('<?php echo $rly1_notes; ?>',TITLE,'Relay 1 - <?php echo $rly1_name; ?>',TITLEBGCOLOR,'#000000',BGCOLOR,'#ffff33'); mouse_move(&#039;rdb_relaysetup&#039;);" onMouseOut="UnTip();mouse_move();"><span class="an">RELAY 1</span></a></td>
              		  	  <td style="text-align:center;"><a href="setup_rdbrelays.php?relay=2" onMouseOver="Tip('<?php echo $rly2_notes; ?>',TITLE,'Relay 2 - <?php echo $rly2_name; ?>',TITLEBGCOLOR,'#000000',BGCOLOR,'#ffff33'); mouse_move(&#039;rdb_relaysetup&#039;);" onMouseOut="UnTip();mouse_move();"><span class="an">RELAY 2</span></a></td>
              		  	  <td style="text-align:center;"><a href="setup_rdbrelays.php?relay=3" onMouseOver="Tip('<?php echo $rly3_notes; ?>',TITLE,'Relay 3 - <?php echo $rly3_name; ?>',TITLEBGCOLOR,'#000000',BGCOLOR,'#ffff33'); mouse_move(&#039;rdb_relaysetup&#039;);" onMouseOut="UnTip();mouse_move();"><span class="an">RELAY 3</span></a></td>
              		  	  <td style="text-align:center;"><a href="setup_rdbrelays.php?relay=4" onMouseOver="Tip('<?php echo $rly4_notes; ?>',TITLE,'Relay 4 - <?php echo $rly4_name; ?>',TITLEBGCOLOR,'#000000',BGCOLOR,'#ffff33'); mouse_move(&#039;rdb_relaysetup&#039;);" onMouseOut="UnTip();mouse_move();"><span class="an">RELAY 4</span></a></td>
              		  		<td style="text-align:center;"><a href="setup_rdbrelays.php?relay=5" onMouseOver="Tip('<?php echo $rly5_notes; ?>',TITLE,'Relay 5 - <?php echo $rly5_name; ?>',TITLEBGCOLOR,'#000000',BGCOLOR,'#ffff33'); mouse_move(&#039;rdb_relaysetup&#039;);" onMouseOut="UnTip();mouse_move();"><span class="an">RELAY 5</span></a></td>
              		  	</tr>
              		  	<tr>
              		  		<td style="text-align:center;"><a href="setup_rdbrelays.php?relay=1" onMouseOver="Tip('<?php echo $rly1_notes; ?>',TITLE,'Relay 1 - <?php echo $rly1_name; ?>',TITLEBGCOLOR,'#000000',BGCOLOR,'#ffff33'); mouse_move(&#039;rdb_relaysetup&#039;);" onMouseOut="UnTip();mouse_move();"><div><img src='images/relay1-32x32.gif'></div></a></td>
              		  	  <td style="text-align:center;"><a href="setup_rdbrelays.php?relay=2" onMouseOver="Tip('<?php echo $rly2_notes; ?>',TITLE,'Relay 2 - <?php echo $rly2_name; ?>',TITLEBGCOLOR,'#000000',BGCOLOR,'#ffff33'); mouse_move(&#039;rdb_relaysetup&#039;);" onMouseOut="UnTip();mouse_move();"><div><img src='images/relay1-32x32.gif'></div></a></td>
              		  	  <td style="text-align:center;"><a href="setup_rdbrelays.php?relay=3" onMouseOver="Tip('<?php echo $rly3_notes; ?>',TITLE,'Relay 3 - <?php echo $rly3_name; ?>',TITLEBGCOLOR,'#000000',BGCOLOR,'#ffff33'); mouse_move(&#039;rdb_relaysetup&#039;);" onMouseOut="UnTip();mouse_move();"><div><img src='images/relay1-32x32.gif'></div></a></td>
              		  	  <td style="text-align:center;"><a href="setup_rdbrelays.php?relay=4" onMouseOver="Tip('<?php echo $rly4_notes; ?>',TITLE,'Relay 4 - <?php echo $rly4_name; ?>',TITLEBGCOLOR,'#000000',BGCOLOR,'#ffff33'); mouse_move(&#039;rdb_relaysetup&#039;);" onMouseOut="UnTip();mouse_move();"><div><img src='images/relay1-32x32.gif'></div></a></td>
              		  	  <td style="text-align:center;"><a href="setup_rdbrelays.php?relay=5" onMouseOver="Tip('<?php echo $rly5_notes; ?>',TITLE,'Relay 5 - <?php echo $rly5_name; ?>',TITLEBGCOLOR,'#000000',BGCOLOR,'#ffff33'); mouse_move(&#039;rdb_relaysetup&#039;);" onMouseOut="UnTip();mouse_move();"><div><img src='images/relay1-32x32.gif'></div></a></td>
              		  	</tr>
              		  	<tr>
              		  		<td style="text-align:center;"><a href="setup_rdbrelays.php?relay=1" onMouseOver="Tip('<?php echo $rly1_notes; ?>',TITLE,'Relay 1 - <?php echo $rly1_name; ?>',TITLEBGCOLOR,'#000000',BGCOLOR,'#ffff33'); mouse_move(&#039;rdb_relaysetup&#039;);" onMouseOut="UnTip();mouse_move();"><span style="color:blue"><?php echo $rly1_name; ?></span></a></td>
              		  	  <td style="text-align:center;"><a href="setup_rdbrelays.php?relay=2" onMouseOver="Tip('<?php echo $rly2_notes; ?>',TITLE,'Relay 2 - <?php echo $rly2_name; ?>',TITLEBGCOLOR,'#000000',BGCOLOR,'#ffff33'); mouse_move(&#039;rdb_relaysetup&#039;);" onMouseOut="UnTip();mouse_move();"><span style="color:blue"><?php echo $rly2_name; ?></span></a></td>
              		  	  <td style="text-align:center;"><a href="setup_rdbrelays.php?relay=3" onMouseOver="Tip('<?php echo $rly3_notes; ?>',TITLE,'Relay 3 - <?php echo $rly3_name; ?>',TITLEBGCOLOR,'#000000',BGCOLOR,'#ffff33'); mouse_move(&#039;rdb_relaysetup&#039;);" onMouseOut="UnTip();mouse_move();"><span style="color:blue"><?php echo $rly3_name; ?></span></a></td>
              		  	  <td style="text-align:center;"><a href="setup_rdbrelays.php?relay=4" onMouseOver="Tip('<?php echo $rly4_notes; ?>',TITLE,'Relay 4 - <?php echo $rly4_name; ?>',TITLEBGCOLOR,'#000000',BGCOLOR,'#ffff33'); mouse_move(&#039;rdb_relaysetup&#039;);" onMouseOut="UnTip();mouse_move();"><span style="color:blue"><?php echo $rly4_name; ?></span></a></td>
              		  	  <td style="text-align:center;"><a href="setup_rdbrelays.php?relay=5" onMouseOver="Tip('<?php echo $rly5_notes; ?>',TITLE,'Relay 5 - <?php echo $rly5_name; ?>',TITLEBGCOLOR,'#000000',BGCOLOR,'#ffff33'); mouse_move(&#039;rdb_relaysetup&#039;);" onMouseOut="UnTip();mouse_move();"><span style="color:blue"><?php echo $rly5_name; ?></span></a></td>
              		  	</tr>
              		  </tbody>
              		</table>
								</div>
								
								<div class="form-group"></div>
								
								<legend>Relay Control</legend> 
								<div class="table-responsive">
            			<table width="100%">
              			<thead>
              		  	<tr>
              		    	<th colspan="5" style="text-align:center; background-color:#D6DFF7;"><span style="color:black">Multi-Purpose Power Relays</span></th>
              		  	</tr>
              		  </thead>
              		  <tr>
              		  	<td style="text-align:center;"><a href="relays.php?relay=1" onMouseOver="Tip('<?php echo $rly1_notes; ?>',TITLE,'Relay 1 - <?php echo $rly1_name; ?>',TITLEBGCOLOR,'#000000',BGCOLOR,'#ffff33');" onMouseOut="UnTip();"><span class="an">RELAY 1</span></a></td>
              		    <td style="text-align:center;"><a href="relays.php?relay=2" onMouseOver="Tip('<?php echo $rly2_notes; ?>',TITLE,'Relay 2 - <?php echo $rly2_name; ?>',TITLEBGCOLOR,'#000000',BGCOLOR,'#ffff33');" onMouseOut="UnTip();"><span class="an">RELAY 2</span></a></td>
              		    <td style="text-align:center;"><a href="relays.php?relay=3" onMouseOver="Tip('<?php echo $rly3_notes; ?>',TITLE,'Relay 3 - <?php echo $rly3_name; ?>',TITLEBGCOLOR,'#000000',BGCOLOR,'#ffff33');" onMouseOut="UnTip();"><span class="an">RELAY 3</span></a></td>
              		    <td style="text-align:center;"><a href="relays.php?relay=4" onMouseOver="Tip('<?php echo $rly4_notes; ?>',TITLE,'Relay 4 - <?php echo $rly4_name; ?>',TITLEBGCOLOR,'#000000',BGCOLOR,'#ffff33');" onMouseOut="UnTip();"><span class="an">RELAY 4</span></a></td>
              		    <td style="text-align:center;"><a href="relays.php?relay=5" onMouseOver="Tip('<?php echo $rly5_notes; ?>',TITLE,'Relay 5 - <?php echo $rly5_name; ?>',TITLEBGCOLOR,'#000000',BGCOLOR,'#ffff33');" onMouseOut="UnTip();"><span class="an">RELAY 5</span></a></td>
              		  </tr>
              		  <tr>
              		  	
              		  		<?php
              		  			
              		  			echo "<td style='text-align:center;'>";
              		  			if($relay1 == "0")
              		  			{
              		  				echo "<div id='relay1'><a href='device_rdb.php?relay=1&command=on'><img src='images/sdnc.gif'></a></div>";
              		  			}
              		  			else
              		  			{
              		  				echo "<div id='relay1'><a href='device_rdb.php?relay=1&command=off'><img src='images/sdno.gif'></a></div>";
              		  			}
              		  			echo "</td>";
              		  			
              		  			echo "<td style='text-align:center;'>";
              		  			if($relay2 == "0")
              		  			{
              		  				echo "<div id='relay2'><a href='device_rdb.php?relay=2&command=on'><img src='images/sdnc.gif'></a></div>";
              		  			}
              		  			else
              		  			{
              		  				echo "<div id='relay2'><a href='device_rdb.php?relay=2&command=off'><img src='images/sdno.gif'></a></div>";
              		  			}
              		  			echo "</td>";
              		  			
              		  			echo "<td style='text-align:center;'>";
              		  			if($relay3 == "0")
              		  			{
              		  				echo "<div id='relay3'><a href='device_rdb.php?relay=3&command=on'><img src='images/sdnc.gif'></a></div>";
              		  			}
              		  			else
              		  			{
              		  				echo "<div id='relay3'><a href='device_rdb.php?relay=3&command=off'><img src='images/sdno.gif'></a></div>";
              		  			}
              		  			echo "</td>";
              		  			
              		  			echo "<td style='text-align:center;'>";
              		  			if($relay4 == "0")
              		  			{
              		  				echo "<div id='relay4'><a href='device_rdb.php?relay=4&command=on'><img src='images/sdnc.gif'></a></div>";
              		  			}
              		  			else
              		  			{
              		  				echo "<div id='relay4'><a href='device_rdb.php?relay=4&command=off'><img src='images/sdno.gif'></a></div>";
              		  			}
              		  			echo "</td>";
              		  			
              		  			echo "<td style='text-align:center;'>";
              		  			if($relay5 == "0")
              		  			{
              		  				echo "<div id='relay5'><a href='device_rdb.php?relay=5&command=on'><img src='images/sdnc.gif'></a></div>";
              		  			}
              		  			else
              		  			{
              		  				echo "<div id='relay5'><a href='device_rdb.php?relay=5&command=off'><img src='images/sdno.gif'></a></div>";
              		  			}
              		  			echo "</td>";
              		  		?>
              		  		
              		    
              		  </tr>
              		  <tr>
              		  	<td style="text-align:center;"><a href="device_rdb.php?relay=1" onMouseOver="Tip('<?php echo $rly1_notes; ?>',TITLE,'Relay 1 - <?php echo $rly1_name; ?>',TITLEBGCOLOR,'#000000',BGCOLOR,'#ffff33');" onMouseOut="UnTip();"><span style="color:blue"><?php echo $rly1_name; ?></span></a></td>
              		    <td style="text-align:center;"><a href="device_rdb.php?relay=2" onMouseOver="Tip('<?php echo $rly2_notes; ?>',TITLE,'Relay 2 - <?php echo $rly2_name; ?>',TITLEBGCOLOR,'#000000',BGCOLOR,'#ffff33');" onMouseOut="UnTip();"><span style="color:blue"><?php echo $rly2_name; ?></span></a></td>
              		    <td style="text-align:center;"><a href="device_rdb.php?relay=3" onMouseOver="Tip('<?php echo $rly3_notes; ?>',TITLE,'Relay 3 - <?php echo $rly3_name; ?>',TITLEBGCOLOR,'#000000',BGCOLOR,'#ffff33');" onMouseOut="UnTip();"><span style="color:blue"><?php echo $rly3_name; ?></span></a></td>
              		    <td style="text-align:center;"><a href="device_rdb.php?relay=4" onMouseOver="Tip('<?php echo $rly4_notes; ?>',TITLE,'Relay 4 - <?php echo $rly4_name; ?>',TITLEBGCOLOR,'#000000',BGCOLOR,'#ffff33');" onMouseOut="UnTip();"><span style="color:blue"><?php echo $rly4_name; ?></span></a></td>
              		    <td style="text-align:center;"><a href="device_rdb.php?relay=5" onMouseOver="Tip('<?php echo $rly5_notes; ?>',TITLE,'Relay 5 - <?php echo $rly5_name; ?>',TITLEBGCOLOR,'#000000',BGCOLOR,'#ffff33');" onMouseOut="UnTip();"><span style="color:blue"><?php echo $rly5_name; ?></span></a></td>
              		  </tr>
              		  <tr>
              		  	<?php
              		  		echo "<td style='text-align:center;'>";
              		  		if($relay1 == "0")
              		  		{
              		  			echo "<div id='r1N'><span style='color:blue'>" . $NC1 . "</span></div>";
              		  		}
              		  		else
              		  		{
              		  			echo "<div id='r1N'><span style='color:red'>" . $NO1 . "</span></div>";
              		  		}
              		  		echo "</td>";
              		  		
              		  		echo "<td style='text-align:center;'>";
              		  		if($relay2 == "0")
              		  		{
              		  			echo "<div id='r2N'><span style='color:blue'>" . $NC2 . "</span></div>";
              		  		}
              		  		else
              		  		{
              		  			echo "<div id='r2N'><span style='color:red'>" . $NO2 . "</span></div>";
              		  		}
              		  		echo "</td>";
              		  		
              		  		echo "<td style='text-align:center;'>";
              		  		if($relay3 == "0")
              		  		{
              		  			echo "<div id='r3N'><span style='color:blue'>" . $NC3 . "</span></div>";
              		  		}
              		  		else
              		  		{
              		  			echo "<div id='r3N'><span style='color:red'>" . $NO3 . "</span></div>";
              		  		}
              		  		echo "</td>";
              		  		
              		  		echo "<td style='text-align:center;'>";
              		  		if($relay4 == "0")
              		  		{
              		  			echo "<div id='r4N'><span style='color:blue'>" . $NC4 . "</span></div>";
              		  		}
              		  		else
              		  		{
              		  			echo "<div id='r4N'><span style='color:red'>" . $NO4 . "</span></div>";
              		  		}
              		  		echo "</td>";
              		  		
              		  		echo "<td style='text-align:center;'>";
              		  		if($relay5 == "0")
              		  		{
              		  			echo "<div id='r4N'><span style='color:blue'>" . $NC5 . "</span></div>";
              		  		}
              		  		else
              		  		{
              		  			echo "<div id='r4N'><span style='color:red'>" . $NO5 . "</span></div>";
              		  		}
              		  		echo "</td>";
              		  	?>
              		  	</tr>
              		</table>
								</div>	
								
								<div class="form-group"></div>
								
								<legend>Enable RDB Relay Leds</legend>
								
								<div class="col-lg-12">
              			<div class="checkbox checkbox-success">
              				<div class="col-lg-1"><input type="checkbox" id="led1" name="led1" <? echo $rly1_led_check; ?>  /><label for="led1">Relay Led 1</label></div>
              				<div class="col-lg-1"><input type="checkbox" id="led2" name="led2" <? echo $rly2_led_check; ?>  /><label for="led2">Relay Led 2</label></div>
              				<div class="col-lg-1"><input type="checkbox" id="led3" name="led3" <? echo $rly3_led_check; ?>  /><label for="led3">Relay Led 3</label></div>
              				<div class="col-lg-1"><input type="checkbox" id="led4" name="led4" <? echo $rly4_led_check; ?>  /><label for="led4">Relay Led 4</label></div>
              				<div class="col-lg-1"><input type="checkbox" id="led5" name="led5" <? echo $rly5_led_check; ?>  /><label for="led5">Relay Led 5</label></div>
              				<div class="col-lg-1"><input type="checkbox" id="hb" name="hb" <? echo $hb_led_check; ?> /><label for="hb">Heartbeat Led</label></div>
              				
              			</div>
              			
              	</div>
              	<div class="form-group"></div>
              	<div class="col-lg-1"><button name="led_btn" class="btn btn-success" type="submit" onMouseOver="mouse_move(&#039;sd_rdbled_set&#039;);" onMouseOut="mouse_move();"><i class="fa fa-check" ></i> SET</button></div>
								<div class="form-group"></div>
								
								
								<legend>Relay Scripts</legend> 
								
								<div class="row">
    							<div class="col-md-12">
    								<div class="table-responsive">
    									<table width="100%" class="table table-striped table-condensed table-hover">
    										<thead>
    											<tr>
    												<th width="2%" style="background:#ABBEEF; border: 1px solid white;">
    													<div style="text-align:center">ID</div>
    												</th>
    												<th width="8%" style="background:#D6DFF7; border: 1px solid white;">
    													<div style="text-align:center">Type</div>
    												</th>
    												<th width="30%" style="background:#D6DFF7; border: 1px solid white;">
    													<div style="text-align:left">Name</div>
    												</th>
    												<th width="50%" style="background:#D6DFF7; border: 1px solid white;">
    													<div style="text-align:left">Description</div>
    												</th>
    												<th width="10%" style="background:#D6DFF7; border: 1px solid white;">
    													<div style="text-align:left">Actions</div>
    												</th>
    											</tr>
    										</thead>
    										<tbody>
    											<?php
    												
    												$dbh = new PDO('sqlite:/etc/rms100.db');
														$query = "SELECT * FROM scripts WHERE type='relay' ORDER BY id";
														$result  = $dbh->query($query);
														foreach($result as $row)
														{
															$id = $row['id'];
															$type = "RELAY";
															$name = $row['name'];
															$description = $row['description'];
															$commands = $row['commands'];
															
															echo "<tr>";
															echo "	<td style='text-align:center'>";
															echo 			$id;
															echo "	</td>";
															echo "	<td style='text-align:center'>";
															echo "		RELAY";
															echo "	</td>";
															echo "	<td style='text-align:left'>";
															echo "			<a href='setup_scripts_add_edit.php?action=edit&type=".$type."&id=".$id."'><u class='dotted'>".$name."</u></a>";
															echo "	</td>";
															echo "	<td style='text-align:left'>";
															echo 			$description;
															echo "	</td>";
															echo "	<td style='text-align:left'>";
															echo " 		<a href='device_rdb.php?action=run&id=".$id."' onMouseOver ='mouse_move(\"b_relays_execrelaycript\");'	onMouseOut='mouse_move();'>";
															echo "		<img src='images/on.gif' width='16' height='16' title='EXECUTE SCRIPT'></a>";
															echo "		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
															echo " 		<a href='device_rdb.php?action=delete&id=".$id."' onMouseOver ='mouse_move(\"b_relays_deleterelaycript\");'	onMouseOut='mouse_move();'>";
															echo "		<img  src='images/off.gif' width='16' height='16' title='DELETE SCRIPT'></a>";
															echo "	</td>";
															echo "</tr>";
														}
    											?>
    										</tbody>
  										</table>      	    
    		  					</div> <!-- END TABLE RESPONSIVE -->		
    		  				</div> <!-- END COL-MD-12 --> 
    						</div> <!-- END ROW -->	
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
echo"  timer: 1500";
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

if($alert_flag == "3")
{
	echo"<script>";
	echo"	swal({";
	echo"		title: 'Execute Script ID# " . $id . "<br>Are you really sure?',";
	echo"		type: 'warning',";
	echo"		showCancelButton: true,";
	echo"		html: true,";
	echo"		confirmButtonColor: '#DD6B55',";
	echo"		confirmButtonText: 'Yes, run it!',";
	echo"		closeOnConfirm: false";
	echo"	},";
	echo"	function(){";
	echo"		window.location.href = 'device_rdb.php?confirm=run&id=" . $id . "';";
	echo"	});";
	echo"</script>";
}

if($alert_flag == "4")
{
	echo"<script>";
	echo"	swal({";
	echo"		title: 'Delete Script ID# " . $id . "<br>Are you really sure?',";
	echo"		type: 'warning',";
	echo"		showCancelButton: true,";
	echo"		html: true,";
	echo"		confirmButtonColor: '#DD6B55',";
	echo"		confirmButtonText: 'Yes, delete it!',";
	echo"		closeOnConfirm: false";
	echo"	},";
	echo"	function(){";
	echo"		window.location.href = 'device_rdb.php?confirm=delete&id=" . $id . "';";
	echo"	});";
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
	echo"		window.location.href = 'device_rdb.php?execute=yes&relay=" . $relay_num . "&command=".$command."';";
	echo"	});";
	echo"</script>";
}


?>
</body>
</html> 


<?php


/////////////////////////////////////////////////////////////
//                                                         //
//             SEEK & DESTROY SCRIPTS                      //
//                                                         //
/////////////////////////////////////////////////////////////
function z_seek_destroy_script_refs($del_val)
{
	$db1 = new PDO('sqlite:/etc/rms100.db');
	
	
	//3 voltmeters to check - HI_script_cmds
	$commands = "";
	for($cnt1=1;$cnt1<4;$cnt1++)
	{
		$save_flag = 0;
		$sd_query = sprintf("SELECT * FROM voltmeters WHERE id='%d';", $cnt1);
		$result  = $db1->query($sd_query);
		foreach($result as $row)
		{
			$commands = $row['HI_script_cmds'];	
			$mylen = strlen($commands);
			if($mylen !== 0)
			{
				$array = explode(".",$commands);
				$count = count($array);
				$count = $count - 1;
				for ($key = 0; $key < $count; $key++) 
				{
   				if($array[$key] == $del_val)
   				{
   					unset($array[$key]);
   					$save_flag = 1;
   				}
				}
				$count = count($array);
				if($count == 1)
				{
					if(empty($array))
					{
						$commands = "";
					}
					else
					{
						$commands = implode(".",$array);
					}
				}
				else
				{
					$commands = implode(".",$array);
				}
			}	
		}
		if($save_flag == 1)
		{
			$query = sprintf("UPDATE voltmeters SET HI_script_cmds='%s' WHERE id='%s';", $commands,$cnt1);
			$result1  = $db1->exec($query);
		}
		
	}	

	//3 voltmeters to check - HI_N_script_cmds
	$commands = "";
	for($cnt1=1;$cnt1<4;$cnt1++)
	{
		$save_flag = 0;
		$sd_query = sprintf("SELECT * FROM voltmeters WHERE id='%d';", $cnt1);
		$result  = $db1->query($sd_query);
		foreach($result as $row)
		{
			$commands = $row['HI_N_script_cmds'];	
			$mylen = strlen($commands);
			if($mylen !== 0)
			{
				$array = explode(".",$commands);
				$count = count($array);
				$count = $count - 1;
				for ($key = 0; $key < $count; $key++) 
				{
   				if($array[$key] == $del_val)
   				{
   					unset($array[$key]);
   					$save_flag = 1;
   				}
				}
				$count = count($array);
				if($count == 1)
				{
					if(empty($array))
					{
						$commands = "";
					}
					else
					{
						$commands = implode(".",$array);
					}
				}
				else
				{
					$commands = implode(".",$array);
				}
			}	
		}
		if($save_flag == 1)
		{
			$query = sprintf("UPDATE voltmeters SET HI_N_script_cmds='%s' WHERE id='%s';", $commands,$cnt1);
			$result1  = $db1->exec($query);
		}
		
	}	
		
	//3 voltmeters to check - LO_script_cmds
	$commands = "";
	for($cnt1=1;$cnt1<4;$cnt1++)
	{
		$save_flag = 0;
		$sd_query = sprintf("SELECT * FROM voltmeters WHERE id='%d';", $cnt1);
		$result  = $db1->query($sd_query);
		foreach($result as $row)
		{
			$commands = $row['LO_script_cmds'];	
			$mylen = strlen($commands);
			if($mylen !== 0)
			{
				$array = explode(".",$commands);
				$count = count($array);
				$count = $count - 1;
				for ($key = 0; $key < $count; $key++) 
				{
   				if($array[$key] == $del_val)
   				{
   					unset($array[$key]);
   					$save_flag = 1;
   				}
				}
				$count = count($array);
				if($count == 1)
				{
					if(empty($array))
					{
						$commands = "";
					}
					else
					{
						$commands = implode(".",$array);
					}
				}
				else
				{
					$commands = implode(".",$array);
				}
			}	
		}
		if($save_flag == 1)
		{
			$query = sprintf("UPDATE voltmeters SET LO_script_cmds='%s' WHERE id='%s';", $commands,$cnt1);
			$result1  = $db1->exec($query);
		}
		
	}	
		
	//3 voltmeters to check - LO_N_script_cmds
	$commands = "";
	for($cnt1=1;$cnt1<4;$cnt1++)
	{
		$save_flag = 0;
		$sd_query = sprintf("SELECT * FROM voltmeters WHERE id='%d';", $cnt1);
		$result  = $db1->query($sd_query);
		foreach($result as $row)
		{
			$commands = $row['LO_N_script_cmds'];	
			$mylen = strlen($commands);
			if($mylen !== 0)
			{
				$array = explode(".",$commands);
				$count = count($array);
				$count = $count - 1;
				for ($key = 0; $key < $count; $key++) 
				{
   				if($array[$key] == $del_val)
   				{
   					unset($array[$key]);
   					$save_flag = 1;
   				}
				}
				$count = count($array);
				if($count == 1)
				{
					if(empty($array))
					{
						$commands = "";
					}
					else
					{
						$commands = implode(".",$array);
					}
				}
				else
				{
					$commands = implode(".",$array);
				}
			}	
		}
		if($save_flag == 1)
		{
			$query = sprintf("UPDATE voltmeters SET LO_N_script_cmds='%s' WHERE id='%s';", $commands,$cnt1);
			$result1  = $db1->exec($query);
		}
		
	}	
	
	// Temperature to check	 - HI_script_cmds
	$commands = "";
	$sd_query = sprintf("SELECT * FROM temperature", $cnt1);
	$result  = $db1->query($sd_query);
	foreach($result as $row)
	{
		$save_flag = 0;
		$commands = $row['HI_script_cmds'];	
		$mylen = strlen($commands);
		if($mylen !== 0)
		{
			$array = explode(".",$commands);
			$count = count($array);
			$count = $count - 1;
			for ($key = 0; $key < $count; $key++) 
			{
  			if($array[$key] == $del_val)
  			{
  				unset($array[$key]);
  				$save_flag = 1;
  			}
			}
			$count = count($array);
			if($count == 1)
			{
				if(empty($array))
				{
					$commands = "";
				}
				else
				{
					$commands = implode(".",$array);
				}
			}
			else
			{
				$commands = implode(".",$array);
			}
		}	
	}
	if($save_flag == 1)
	{
		$query = sprintf("UPDATE temperature SET HI_script_cmds='%s';", $commands);
		$result1  = $db1->exec($query);
	}
	
		
	// Temperature to check	 - HI_N_script_cmds
	$commands = "";
	$sd_query = sprintf("SELECT * FROM temperature", $cnt1);
	$result  = $db1->query($sd_query);
	foreach($result as $row)
	{
		$save_flag = 0;
		$commands = $row['HI_N_script_cmds'];	
		$mylen = strlen($commands);
		if($mylen !== 0)
		{
			$array = explode(".",$commands);
			$count = count($array);
			$count = $count - 1;
			for ($key = 0; $key < $count; $key++) 
			{
  			if($array[$key] == $del_val)
  			{
  				unset($array[$key]);
  				$save_flag = 1;
  			}
			}
			$count = count($array);
			if($count == 1)
			{
				if(empty($array))
				{
					$commands = "";
				}
				else
				{
					$commands = implode(".",$array);
				}
			}
			else
			{
				$commands = implode(".",$array);
			}
		}	
	}
	if($save_flag == 1)
	{
		$query = sprintf("UPDATE temperature SET HI_N_script_cmds='%s';", $commands);
		$result1  = $db1->exec($query);
	}
	
	
	// Temperature to check	 - LO_script_cmds
	$commands = "";
	$sd_query = sprintf("SELECT * FROM temperature", $cnt1);
	$result  = $db1->query($sd_query);
	foreach($result as $row)
	{
		$save_flag = 0;
		$commands = $row['LO_script_cmds'];	
		$mylen = strlen($commands);
		if($mylen !== 0)
		{
			$array = explode(".",$commands);
			$count = count($array);
			$count = $count - 1;
			for ($key = 0; $key < $count; $key++) 
			{
  			if($array[$key] == $del_val)
  			{
  				unset($array[$key]);
  				$save_flag = 1;
  			}
			}
			$count = count($array);
			if($count == 1)
			{
				if(empty($array))
				{
					$commands = "";
				}
				else
				{
					$commands = implode(".",$array);
				}
			}
			else
			{
				$commands = implode(".",$array);
			}
		}	
	}
	if($save_flag == 1)
	{
		$query = sprintf("UPDATE temperature SET LO_script_cmds='%s';", $commands);
		$result1  = $db1->exec($query);
	}
	
	
	// Temperature to check	 - LO_N_script_cmds
	$commands = "";
	$sd_query = sprintf("SELECT * FROM temperature", $cnt1);
	$result  = $db1->query($sd_query);
	foreach($result as $row)
	{
		$save_flag = 0;
		$commands = $row['LO_N_script_cmds'];	
		$mylen = strlen($commands);
		if($mylen !== 0)
		{
			$array = explode(".",$commands);
			$count = count($array);
			$count = $count - 1;
			for ($key = 0; $key < $count; $key++) 
			{
  			if($array[$key] == $del_val)
  			{
  				unset($array[$key]);
  				$save_flag = 1;
  			}
			}
			$count = count($array);
			if($count == 1)
			{
				if(empty($array))
				{
					$commands = "";
				}
				else
				{
					$commands = implode(".",$array);
				}
			}
			else
			{
				$commands = implode(".",$array);
			}
		}	
	}
	if($save_flag == 1)
	{
		$query = sprintf("UPDATE temperature SET LO_N_script_cmds='%s';", $commands);
		$result1  = $db1->exec($query);
	}
	
	//5 alarms to check - HI_script_cmds
	$commands = "";
	for($cnt1=1;$cnt1<6;$cnt1++)
	{
		$save_flag = 0;
		$sd_query = sprintf("SELECT * FROM io WHERE id='%d' AND type='alarm';", $cnt1);
		$result  = $db1->query($sd_query);
		foreach($result as $row)
		{
			$commands = $row['HI_script_cmds'];	
			$mylen = strlen($commands);
			if($mylen !== 0)
			{
				$array = explode(".",$commands);
				$count = count($array);
				$count = $count - 1;
				for ($key = 0; $key < $count; $key++) 
				{
   				if($array[$key] == $del_val)
   				{
   					unset($array[$key]);
   					$save_flag = 1;
   				}
				}
				$count = count($array);
				if($count == 1)
				{
					if(empty($array))
					{
						$commands = "";
					}
					else
					{
						$commands = implode(".",$array);
					}
				}
				else
				{
					$commands = implode(".",$array);
				}
			}	
		}
		if($save_flag == 1)
		{
			$query = sprintf("UPDATE io SET HI_script_cmds='%s' WHERE id='%s' AND type='alarm';", $commands,$cnt1);
			$result1  = $db1->exec($query);
		}
		
	}
	
	//5 alarms to check - LO_script_cmds
	$commands = "";
	for($cnt1=1;$cnt1<6;$cnt1++)
	{
		$save_flag = 0;
		$sd_query = sprintf("SELECT * FROM io WHERE id='%d' AND type='alarm';", $cnt1);
		$result  = $db1->query($sd_query);
		foreach($result as $row)
		{
			$commands = $row['LO_script_cmds'];	
			$mylen = strlen($commands);
			if($mylen !== 0)
			{
				$array = explode(".",$commands);
				$count = count($array);
				$count = $count - 1;
				for ($key = 0; $key < $count; $key++) 
				{
   				if($array[$key] == $del_val)
   				{
   					unset($array[$key]);
   					$save_flag = 1;
   				}
				}
				$count = count($array);
				if($count == 1)
				{
					if(empty($array))
					{
						$commands = "";
					}
					else
					{
						$commands = implode(".",$array);
					}
				}
				else
				{
					$commands = implode(".",$array);
				}
			}	
		}
		if($save_flag == 1)
		{
			$query = sprintf("UPDATE io SET LO_script_cmds='%s' WHERE id='%s' AND type='alarm';", $commands,$cnt1);
			$result1  = $db1->exec($query);
		}
	}
	
	//4 gxio to check - HI_script_cmds
	$commands = "";
	for($cnt1=1;$cnt1<5;$cnt1++)
	{
		$save_flag = 0;
		$sd_query = sprintf("SELECT * FROM io WHERE id='%d' AND type='gxio';", $cnt1);
		$result  = $db1->query($sd_query);
		foreach($result as $row)
		{
			$commands = $row['HI_script_cmds'];	
			$mylen = strlen($commands);
			if($mylen !== 0)
			{
				$array = explode(".",$commands);
				$count = count($array);
				$count = $count - 1;
				for ($key = 0; $key < $count; $key++) 
				{
   				if($array[$key] == $del_val)
   				{
   					unset($array[$key]);
   					$save_flag = 1;
   				}
				}
				$count = count($array);
				if($count == 1)
				{
					if(empty($array))
					{
						$commands = "";
					}
					else
					{
						$commands = implode(".",$array);
					}
				}
				else
				{
					$commands = implode(".",$array);
				}
			}	
		}
		if($save_flag == 1)
		{
			$query = sprintf("UPDATE io SET HI_script_cmds='%s' WHERE id='%s' AND type='gxio';", $commands,$cnt1);
			$result1  = $db1->exec($query);
		}
	}
	
	//4 gxio to check - LO_script_cmds
	$commands = "";
	for($cnt1=1;$cnt1<5;$cnt1++)
	{
		$save_flag = 0;
		$sd_query = sprintf("SELECT * FROM io WHERE id='%d' AND type='gxio';", $cnt1);
		$result  = $db1->query($sd_query);
		foreach($result as $row)
		{
			$commands = $row['LO_script_cmds'];	
			$mylen = strlen($commands);
			if($mylen !== 0)
			{
				$array = explode(".",$commands);
				$count = count($array);
				$count = $count - 1;
				for ($key = 0; $key < $count; $key++) 
				{
   				if($array[$key] == $del_val)
   				{
   					unset($array[$key]);
   					$save_flag = 1;
   				}
				}
				$count = count($array);
				if($count == 1)
				{
					if(empty($array))
					{
						$commands = "";
					}
					else
					{
						$commands = implode(".",$array);
					}
				}
				else
				{
					$commands = implode(".",$array);
				}
			}	
		}
		if($save_flag == 1)
		{
			$query = sprintf("UPDATE io SET LO_script_cmds='%s' WHERE id='%s' AND type='gxio';", $commands,$cnt1);
			$result1  = $db1->exec($query);
		}
	}
	
	//1 button to check - HI_script_cmds
	$commands = "";
	for($cnt1=1;$cnt1<2;$cnt1++)
	{
		$save_flag = 0;
		$sd_query = sprintf("SELECT * FROM io WHERE id='%d' AND type='btn';", $cnt1);
		$result  = $db1->query($sd_query);
		foreach($result as $row)
		{
			$commands = $row['HI_script_cmds'];	
			$mylen = strlen($commands);
			if($mylen !== 0)
			{
				$array = explode(".",$commands);
				$count = count($array);
				$count = $count - 1;
				for ($key = 0; $key < $count; $key++) 
				{
   				if($array[$key] == $del_val)
   				{
   					unset($array[$key]);
   					$save_flag = 1;
   				}
				}
				$count = count($array);
				if($count == 1)
				{
					if(empty($array))
					{
						$commands = "";
					}
					else
					{
						$commands = implode(".",$array);
					}
				}
				else
				{
					$commands = implode(".",$array);
				}
			}	
		}
		if($save_flag == 1)
		{
			$query = sprintf("UPDATE io SET HI_script_cmds='%s' WHERE id='%s' AND type='btn';", $commands,$cnt1);
			$result1  = $db1->exec($query);
		}
	}
	
	//1 button to check - LO_script_cmds
	$commands = "";
	for($cnt1=1;$cnt1<2;$cnt1++)
	{
		$save_flag = 0;
		$sd_query = sprintf("SELECT * FROM io WHERE id='%d' AND type='btn';", $cnt1);
		$result  = $db1->query($sd_query);
		foreach($result as $row)
		{
			$commands = $row['LO_script_cmds'];	
			$mylen = strlen($commands);
			if($mylen !== 0)
			{
				$array = explode(".",$commands);
				$count = count($array);
				$count = $count - 1;
				for ($key = 0; $key < $count; $key++) 
				{
   				if($array[$key] == $del_val)
   				{
   					unset($array[$key]);
   					$save_flag = 1;
   				}
				}
				$count = count($array);
				if($count == 1)
				{
					if(empty($array))
					{
						$commands = "";
					}
					else
					{
						$commands = implode(".",$array);
					}
				}
				else
				{
					$commands = implode(".",$array);
				}
			}	
		}
		if($save_flag == 1)
		{
			$query = sprintf("UPDATE io SET LO_script_cmds='%s' WHERE id='%s' AND type='btn';", $commands,$cnt1);
			$result1  = $db1->exec($query);
		}
	}
	
	//15 ping targets to check - HI_script_cmds
	$commands = "";
	for($cnt1=1;$cnt1<16;$cnt1++)
	{
		$save_flag = 0;
		$sd_query = sprintf("SELECT * FROM ping_targets WHERE id='%d';", $cnt1);
		$result  = $db1->query($sd_query);
		foreach($result as $row)
		{
			$commands = $row['HI_script_cmds'];	
			$mylen = strlen($commands);
			if($mylen !== 0)
			{
				$array = explode(".",$commands);
				$count = count($array);
				$count = $count - 1;
				for ($key = 0; $key < $count; $key++) 
				{
   				if($array[$key] == $del_val)
   				{
   					unset($array[$key]);
   					$save_flag = 1;
   				}
				}
				$count = count($array);
				if($count == 1)
				{
					if(empty($array))
					{
						$commands = "";
					}
					else
					{
						$commands = implode(".",$array);
					}
				}
				else
				{
					$commands = implode(".",$array);
				}
			}	
		}
		if($save_flag == 1)
		{
			$query = sprintf("UPDATE ping_targets SET HI_script_cmds='%s' WHERE id='%s';", $commands,$cnt1);
			$result1  = $db1->exec($query);
		}
	}
	
	//15 ping targets to check - HI_N_script_cmds
	$commands = "";
	for($cnt1=1;$cnt1<16;$cnt1++)
	{
		$save_flag = 0;
		$sd_query = sprintf("SELECT * FROM ping_targets WHERE id='%d';", $cnt1);
		$result  = $db1->query($sd_query);
		foreach($result as $row)
		{
			$commands = $row['HI_N_script_cmds'];	
			$mylen = strlen($commands);
			if($mylen !== 0)
			{
				$array = explode(".",$commands);
				$count = count($array);
				$count = $count - 1;
				for ($key = 0; $key < $count; $key++) 
				{
   				if($array[$key] == $del_val)
   				{
   					unset($array[$key]);
   					$save_flag = 1;
   				}
				}
				$count = count($array);
				if($count == 1)
				{
					if(empty($array))
					{
						$commands = "";
					}
					else
					{
						$commands = implode(".",$array);
					}
				}
				else
				{
					$commands = implode(".",$array);
				}
			}	
		}
		if($save_flag == 1)
		{
			$query = sprintf("UPDATE ping_targets SET HI_N_script_cmds='%s' WHERE id='%s';", $commands,$cnt1);
			$result1  = $db1->exec($query);
		}
	}
	
	//15 ping targets to check - LO_script_cmds
	$commands = "";
	for($cnt1=1;$cnt1<16;$cnt1++)
	{
		$save_flag = 0;
		$sd_query = sprintf("SELECT * FROM ping_targets WHERE id='%d';", $cnt1);
		$result  = $db1->query($sd_query);
		foreach($result as $row)
		{
			$commands = $row['LO_script_cmds'];	
			$mylen = strlen($commands);
			if($mylen !== 0)
			{
				$array = explode(".",$commands);
				$count = count($array);
				$count = $count - 1;
				for ($key = 0; $key < $count; $key++) 
				{
   				if($array[$key] == $del_val)
   				{
   					unset($array[$key]);
   					$save_flag = 1;
   				}
				}
				$count = count($array);
				if($count == 1)
				{
					if(empty($array))
					{
						$commands = "";
					}
					else
					{
						$commands = implode(".",$array);
					}
				}
				else
				{
					$commands = implode(".",$array);
				}
			}	
		}
		if($save_flag == 1)
		{
			$query = sprintf("UPDATE ping_targets SET LO_script_cmds='%s' WHERE id='%s';", $commands,$cnt1);
			$result1  = $db1->exec($query);
		}
	}
	
	//15 ping targets to check - LO_N_script_cmds
	$commands = "";
	for($cnt1=1;$cnt1<16;$cnt1++)
	{
		$save_flag = 0;
		$sd_query = sprintf("SELECT * FROM ping_targets WHERE id='%d';", $cnt1);
		$result  = $db1->query($sd_query);
		foreach($result as $row)
		{
			$commands = $row['LO_N_script_cmds'];	
			$mylen = strlen($commands);
			if($mylen !== 0)
			{
				$array = explode(".",$commands);
				$count = count($array);
				$count = $count - 1;
				for ($key = 0; $key < $count; $key++) 
				{
   				if($array[$key] == $del_val)
   				{
   					unset($array[$key]);
   					$save_flag = 1;
   				}
				}
				$count = count($array);
				if($count == 1)
				{
					if(empty($array))
					{
						$commands = "";
					}
					else
					{
						$commands = implode(".",$array);
					}
				}
				else
				{
					$commands = implode(".",$array);
				}
			}	
		}
		if($save_flag == 1)
		{
			$query = sprintf("UPDATE ping_targets SET LO_N_script_cmds='%s' WHERE id='%s';", $commands,$cnt1);
			$result1  = $db1->exec($query);
		}
	}
}



?>