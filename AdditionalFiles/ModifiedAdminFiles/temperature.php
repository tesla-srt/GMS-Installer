<?php
	error_reporting(E_ALL);
	include "lib.php";
	
//	if(empty($_GET) && empty($_POST)) 
//	{ 
//		/* no parameters passed*/
//		echo "This web page must be accessed through the RMS web interface!";
//		exit(0);
//	}

	$hostname = trim(file_get_contents("/etc/hostname"));
	$notes = "";
	$hi_t = "";
	$lo_t = "";
	$h_en = "";
	$l_en = "";
	$HI_alert_cmds = "";
	$HI_N_alert_cmds = "";
	$HI_script_cmds = "";
	$HI_N_script_cmds = "";
	$LO_alert_cmds = "";
	$LO_N_alert_cmds = "";
	$LO_script_cmds = "";
	$LO_N_script_cmds = "";
	$hi_flap = "";
	$lo_flap = "";
	$default_temp = "";
	$adj = "";
	$RunHiFile = "";
	$RunHiNFile = "";
	$RunLowFile = "";
	$RunLowNFile = "";
	$hi_t_min = "";
	$lo_t_max = "";
	$text = "";
	$alert_flag = "";
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


	

$query = sprintf("SELECT * FROM temperature;");
$result  = $dbh->query($query);
foreach($result as $row)
{
	$notes = $row['notes'];
	$hi_t = $row['hi_t'];
	$lo_t = $row['lo_t'];
	$h_en = $row['h_en'];
	$l_en = $row['l_en'];
	$HI_alert_cmds = $row['HI_alert_cmds'];
	$HI_N_alert_cmds = $row['HI_N_alert_cmds'];
	$HI_script_cmds = $row['HI_script_cmds'];
	$HI_N_script_cmds = $row['HI_N_script_cmds'];
	$LO_alert_cmds = $row['LO_alert_cmds'];
	$LO_N_alert_cmds = $row['LO_N_alert_cmds'];
	$LO_script_cmds = $row['LO_script_cmds'];
	$LO_N_script_cmds = $row['LO_N_script_cmds'];
	$hi_flap = $row['hi_flap'];
	$lo_flap = $row['lo_flap'];
	$default_temp = $row['default_temp'];
	$adj = $row['adj'];
	$RunHiFile = $row['RunHiFile'];
	$RunHiNFile = $row['RunHiNFile'];
	$RunLowFile = $row['RunLowFile'];
	$RunLowNFile = $row['RunLowNFile'];
	$hi_t_min = $row['hi_t_min'];
	$lo_t_max = $row['lo_t_max'];	
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
	header("Location: index.php");
}

// Reset Button	was clicked
if(isset ($_POST['reset_btn']))
{
	exec("/sbin/temperature-reset");
	$query = sprintf("SELECT * FROM temperature;");
	$result  = $dbh->query($query);
	foreach($result as $row)
	{
		$notes = $row['notes'];
		$hi_t = $row['hi_t'];
		$lo_t = $row['lo_t'];
		$h_en = $row['h_en'];
		$l_en = $row['l_en'];
		$HI_alert_cmds = $row['HI_alert_cmds'];
		$HI_N_alert_cmds = $row['HI_N_alert_cmds'];
		$HI_script_cmds = $row['HI_script_cmds'];
		$HI_N_script_cmds = $row['HI_N_script_cmds'];
		$LO_alert_cmds = $row['LO_alert_cmds'];
		$LO_N_alert_cmds = $row['LO_N_alert_cmds'];
		$LO_script_cmds = $row['LO_script_cmds'];
		$LO_N_script_cmds = $row['LO_N_script_cmds'];
		$hi_flap = $row['hi_flap'];
		$lo_flap = $row['lo_flap'];
		$default_temp = $row['default_temp'];
		$adj = $row['adj'];
		$RunHiFile = $row['RunHiFile'];
		$RunHiNFile = $row['RunHiNFile'];
		$RunLowFile = $row['RunLowFile'];
		$RunLowNFile = $row['RunLowNFile'];
		$hi_t_min = $row['hi_t_min'];
		$lo_t_max = $row['lo_t_max'];	
	}
	$alert_flag = "3";
}


// Apply Button	was clicked
if(isset ($_POST['apply_btn']))
{
	$adj = $_POST['adj'];
	$notes = $_POST['notes'];
	if(isset ($_POST['h_en']))
	{
		$h_en = "1";
	}
	else
	{
		$h_en = "0";
	}
	
	if(isset ($_POST['l_en']))
	{
		$l_en = "1";
	}
	else
	{
		$l_en = "0";
	}

	$HI_alert_cmds = "";
	if(isset($_POST['HI_alert_delcmd']))
	{
		foreach ($_POST['HI_alert_delcmd'] as $HI_alert_delcmdBox)
		{
    	$HI_alert_cmds = $HI_alert_cmds . $HI_alert_delcmdBox . ".";
  	} 
	}
//	echo "HI_alert_cmds " . $HI_alert_cmds . "\n";
	
	$HI_N_alert_cmds = "";
	if(isset($_POST['HI_N_alert_delcmd']))
	{
		foreach ($_POST['HI_N_alert_delcmd'] as $HI_N_alert_delcmdBox)
		{
    	$HI_N_alert_cmds = $HI_N_alert_cmds . $HI_N_alert_delcmdBox . ".";
  	} 
	}
//	echo "HI_N_alert_cmds " . $HI_N_alert_cmds . "\n";
	
	$LO_alert_cmds = "";
	if(isset($_POST['LO_alert_delcmd']))
	{
		foreach ($_POST['LO_alert_delcmd'] as $LO_alert_delcmdBox)
		{
    	$LO_alert_cmds = $LO_alert_cmds . $LO_alert_delcmdBox . ".";
  	} 
	}
//	echo "LO_alert_cmds " . $LO_alert_cmds . "\n";
	
	$LO_N_alert_cmds = "";
	if(isset($_POST['LO_N_alert_delcmd']))
	{
		foreach ($_POST['LO_N_alert_delcmd'] as $LO_N_alert_delcmdBox)
		{
    	$LO_N_alert_cmds = $LO_N_alert_cmds . $LO_N_alert_delcmdBox . ".";
  	} 
	}
//	echo "LO_N_alert_cmds " . $LO_N_alert_cmds . "\n";
	
	$HI_script_cmds = "";
	if(isset($_POST['HI_script_delcmd']))
	{
		foreach ($_POST['HI_script_delcmd'] as $HI_script_delcmdBox)
		{
    	$HI_script_cmds = $HI_script_cmds . $HI_script_delcmdBox . ".";
  	} 
	}
//	echo "HI_script_cmds " . $HI_script_cmds . "\n";
	
	$HI_N_script_cmds = "";
	if(isset($_POST['HI_N_script_delcmd']))
	{
		foreach ($_POST['HI_N_script_delcmd'] as $HI_N_script_delcmdBox)
		{
    	$HI_N_script_cmds = $HI_N_script_cmds . $HI_N_script_delcmdBox . ".";
  	} 
	}
//	echo "HI_N_script_cmds " . $HI_N_script_cmds . "\n";
	
	$LO_script_cmds = "";
	if(isset($_POST['LO_script_delcmd']))
	{
		foreach ($_POST['LO_script_delcmd'] as $LO_script_delcmdBox)
		{
    	$LO_script_cmds = $LO_script_cmds . $LO_script_delcmdBox . ".";
  	} 
	}
//	echo "LO_script_cmds " . $LO_script_cmds . "\n";
	
	$LO_N_script_cmds = "";
	if(isset($_POST['LO_N_script_delcmd']))
	{
		foreach ($_POST['LO_N_script_delcmd'] as $LO_N_script_delcmdBox)
		{
    	$LO_N_script_cmds = $LO_N_script_cmds . $LO_N_script_delcmdBox . ".";
  	} 
	}
//	echo "LO_N_script_cmds " . $LO_N_script_cmds . "\n";
	
	$hi_flap = $_POST['hi_flap'];
	$lo_flap = $_POST['lo_flap'];
	$RunHiFile = $_POST['RunHiFile'];
	$RunHiNFile = $_POST['RunHiNFile'];
	$RunLowFile = $_POST['RunLowFile'];
	$RunLowNFile = $_POST['RunLowNFile'];
	$RunLowNFile = $_POST['RunLowNFile'];
	$hi_t = $_POST['hi_t'];
	$hi_t_min = $_POST['hi_t_min'];
	$lo_t = $_POST['lo_t'];
	$lo_t_max = $_POST['lo_t_max'];
	$default_temp = $_POST['group1'];
	
	//echo "Hi Icon: ".$hi_icon." Low Icon :".$lo_icon;
	if($hi_t <= $hi_t_min)
	{
		$text = "The High Trigger Max Value must be Greater than the High Trigger Min Value!";
		$alert_flag = "2";
		goto noSave;
	}
	if($lo_t >= $lo_t_max)
	{
		$text = "The Low Trigger Max Value must be Lesser than the Low Trigger Min Value!";
		$alert_flag = "2";
		goto noSave;
	}

	$query = sprintf("UPDATE temperature SET notes='%s', hi_t='%2.2f', lo_t='%2.2f', h_en='%d', l_en='%d', HI_alert_cmds='%s', HI_N_alert_cmds='%s', HI_script_cmds='%s', HI_N_script_cmds='%s', LO_alert_cmds='%s', LO_N_alert_cmds='%s', LO_script_cmds='%s', LO_N_script_cmds='%s', hi_flap='%d', lo_flap='%d', default_temp='%s', adj='%s', RunHiFile='%s', RunHiNFile='%s', RunLowFile='%s', RunLowNFile='%s', hi_t_min='%2.4f', lo_t_max='%2.4f';", $notes, $hi_t, $lo_t, $h_en, $l_en, $HI_alert_cmds, $HI_N_alert_cmds, $HI_script_cmds, $HI_N_script_cmds, $LO_alert_cmds, $LO_N_alert_cmds, $LO_script_cmds, $LO_N_script_cmds, $hi_flap, $lo_flap, $default_temp, $adj, $RunHiFile, $RunHiNFile, $RunLowFile, $RunLowNFile, $hi_t_min, $lo_t_max);
	$result  = $dbh->exec($query); 
	restart_some_services();
	$alert_flag = "1";
	
	noSave:    
}



$tempc = trim(file_get_contents("/var/rmsdata/tempc"));
$tempf = trim(file_get_contents("/var/rmsdata/tempf"));

				
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
			SetContext('temperature');
		</script>
		
		
	
		
		
		<script>
   	
   	$().ready(function() {  
   	 // alerts	
     $('#add_HI_alert').click(function() {  
        return !$('#HI_alert_addcmd option:selected').clone().appendTo('#HI_alert_delcmd');  
     });  
     $('#remove_HI_alert').click(function() {  
        return !$('#HI_alert_delcmd option:selected').remove();   
     });
     
     $('#add_HI_N_alert').click(function() {  
        return !$('#HI_N_alert_addcmd option:selected').clone().appendTo('#HI_N_alert_delcmd');  
     });  
     $('#remove_HI_N_alert').click(function() {  
        return !$('#HI_N_alert_delcmd option:selected').remove();   
     });
     
     $('#add_LO_alert').click(function() {  
        return !$('#LO_alert_addcmd option:selected').clone().appendTo('#LO_alert_delcmd');  
     });  
     $('#remove_LO_alert').click(function() {  
        return !$('#LO_alert_delcmd option:selected').remove();   
     });
     
     $('#add_LO_N_alert').click(function() {  
        return !$('#LO_N_alert_addcmd option:selected').clone().appendTo('#LO_N_alert_delcmd');  
     });  
     $('#remove_LO_N_alert').click(function() {  
        return !$('#LO_N_alert_delcmd option:selected').remove();   
     });
     
     //scripts
     $('#add_HI_script').click(function() {  
        return !$('#HI_script_addcmd option:selected').clone().appendTo('#HI_script_delcmd');  
     });  
     $('#remove_HI_script').click(function() {  
        return !$('#HI_script_delcmd option:selected').remove();   
     });
     
     $('#add_HI_N_script').click(function() {  
        return !$('#HI_N_script_addcmd option:selected').clone().appendTo('#HI_N_script_delcmd');  
     });  
     $('#remove_HI_N_script').click(function() {  
        return !$('#HI_N_script_delcmd option:selected').remove();   
     });
     
     $('#add_LO_script').click(function() {  
        return !$('#LO_script_addcmd option:selected').clone().appendTo('#LO_script_delcmd');  
     });  
     $('#remove_LO_script').click(function() {  
        return !$('#LO_script_delcmd option:selected').remove();   
     });
     
     $('#add_LO_N_script').click(function() {  
        return !$('#LO_N_script_addcmd option:selected').clone().appendTo('#LO_N_script_delcmd');  
     });  
     $('#remove_LO_N_script').click(function() {  
        return !$('#LO_N_script_delcmd option:selected').remove();   
     });

 		});
   	
   	function selectAllOptions(selStr)
			{
  			var selObj = document.getElementById(selStr);
  			for (var i=0; i<selObj.options.length; i++)
  				{
    				selObj.options[i].selected = true;
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

<?php left_nav("temperature"); ?>
<script language="javascript" type="text/javascript">
	SetContext('temperature');
</script>
<!-- Main Wrapper -->

<form name='TEMP' action='temperature.php' method='post' class="form-horizontal" onsubmit="selectAllOptions('HI_alert_delcmd');selectAllOptions('HI_N_alert_delcmd');selectAllOptions('LO_alert_delcmd');selectAllOptions('LO_N_alert_delcmd');selectAllOptions('HI_script_delcmd');selectAllOptions('HI_N_script_delcmd');selectAllOptions('LO_script_delcmd');selectAllOptions('LO_N_script_delcmd');">  	
	<fieldset>
		<div id="wrapper">
			<?php
				if($screen_animations == "CHECKED")
				{
					echo '<div class="content animate-panel">';
				}
				else
				{
					echo '<div class="content">';
				}
			?>
		  	<!-- INFO BLOCK START -->
		  	
		  			<div class="row">
		    			<div class="col-sm-12">
		    		  	<div class="hpanel3">
		    		  	  <div class="panel-body" style="text-align:left; background:#F1F3F6;border:none;">
		    				  	<legend>Temperature Sensor</legend> 
		
		    				  	<div class="form-group"><label class="col-sm-1 control-label" style="text-align:left; min-width:120px; max-width:120px">Celsius:</label>
		              		<div class="col-sm-2">
		              			<label class="col-sm-1 control-label" style="text-align:left">
		              				<div id="tmpc" style="color:red"><?php echo $tempc; ?></div> 
		              				</label>
		              		</div>
		              	</div>
		    				  	
		    				  	<div class="form-group"><label class="col-sm-1 control-label" style="text-align:left; min-width:120px; max-width:120px">Fahrenheit:</label>
		              		<div class="col-sm-2">
		              			<label class="col-sm-1 control-label" style="text-align:left">
		              				<div id="tmpf" style="color:red"><?php echo $tempf; ?></div> 
		              				</label>
		              		</div>
		              	</div>
		    				  	
		    				  	<div class="form-group"><label class="col-sm-1 control-label" style="text-align:left; min-width:130px; max-width:130px">Adjustment:</label>
		              		<div class="col-sm-1" style="min-width:90px; max-width:90px">
		              			<input type="text" class="form-control" name='adj' value='<?php echo $adj; ?>' onMouseOver="mouse_move(&#039;temperature_adj&#039;);" onMouseOut="mouse_move();" required />
		              		</div>
		              	</div>
		    				  	
		    				  	<div class="form-group"><label class="col-sm-1 control-label" style="text-align:left; min-width:130px; max-width:130px">Notes:</label>
		              		<div class="col-sm-3" style="min-width:368px; max-width:368px">
		              			<textarea  rows="3" cols="48" class="form-control" name='notes' required><?php echo $notes; ?></textarea>
		              		</div>
		              	</div>
		              	
		              	<div class="form-group"><label class="col-sm-1 control-label" style="text-align:left; min-width:130px">Temperature Graph showing Fahrenheit and Celsius over a one hour period. (Click the Graph for more views.)</label>
		              		<div class="col-sm-3">
		    				  			<?php
		    				  				if(file_exists("rrd/tmp/temp-hour.png"))
		    				  				{
		    				  					echo "<a href='rms-graph.php?action=viewgraph&amp;g=temp'><img src='rrd/tmp/temp-hour.png' height='145'></a>";
		    				  				}
		    				  				else
		    				  				{
		    				  					echo "<img src='images/no-rrd-temperature.jpg' height='145'>";
		    				  				}
		    				  				?>
		    				  		</div>
		              	</div>
		    				  	<div class="form-group">	
		    				  		<div class="col-sm-4" style="text-align:left; max-width:225px">
		              			<div class="radio radio-success" style="text-align:left">
		              				<?php
		    				  					if($default_temp == "C")
		    				  						{
		    				  							echo '<input type="radio" id="tempc" name="group1" value="C" checked/>';
		    				  						}
		    				  						else
		    				  						{
		    				  							echo '<input type="radio" id="tempc" name="group1" value="C" />';
		    				  						}
		    				  					?>
		              	        <label for="tempc">Triggers default to Celsius</label>
		              	    </div>
		              	  </div>
		    				  		
		    				  		<div class="col-sm-5" style="text-align:left; max-width:270px">
		              			<div class="radio radio-danger" style="text-align:left">
		              				<?php
		    				  					if($default_temp == "F")
		    				  						{
		    				  							echo '<input type="radio" id="tempf" name="group1" value="F" checked/>';
		    				  						}
		    				  						else
		    				  						{
		    				  							echo '<input type="radio" id="tempf" name="group1" value="F" />';
		    				  						}
		    				  					?>
		              	        <label for="tempf">Triggers default to Fahrenheit</label>
		              	    </div>
		              	 	</div>
		    				  	</div>	
		    				  	
		    				  	<div class="form-group">
		        					<div class="col-sm-6" style="max-width:480px">
		        						<button name="apply_btn" class="btn btn-success" type="submit" onMouseOver="mouse_move(&#039;b_apply&#039;);" onMouseOut="mouse_move();"><i class="fa fa-check" ></i> Apply</button>
		        						<button name="cancel_btn" class="btn btn-primary" type="submit" onMouseOver="mouse_move(&#039;b_cancel&#039;);" onMouseOut="mouse_move();" formnovalidate><i class="fa fa-times"></i> Cancel</button>
		        						<button name="reset_btn" class="btn btn-danger" type="submit" onMouseOver="mouse_move(&#039;temperature_reset&#039;);" onMouseOut="mouse_move();" formnovalidate><i class="fa fa-exclamation"></i> Reset</button>
		        					</div>
		        				</div>
		    				  </div>
								</div>
							</div>
						</div>
		    		<div class="row">
							<div class="col-sm-12">
								<div class="hpanel3">
		    		  	  <div class="panel-body" style="background:#F1F3F6;">	
		    				  	
		    				  	<div class="table-responsive">
		    				   		<table width="100%" border="1" class="table table-striped table-condensed">
		    				   			<thead>
		    				   				<tr>
		    				   					<th width="10%" style="background:#D6DFF7;">
		    				   						<div style="text-align:center;color:black">Enabled</div>
		    				   					</th>
		    				   					<th width="40%" style="background:red">
		    				   						<div style="text-align:center;color:black">Temperature High Trigger Range</div>
		    				   					</th>
		    				   					<th width="40%" style="background:#40FF40">
		    				   						<div style="text-align:center;color:black">Temperature High Normal Trigger</div>
		    				   					</th>
		    				   				</tr>
		    				   			</thead>
		    				   			<tbody>
		    				   				<tr>
		    				   					<td style="vertical-align:middle">
		    				   						<?php
		    				   							if($h_en == "1")
		    				   							{
		    				   								$active = "checked";
		    				   							}
		    				   							else
		    				   							{
		    				   								$active = " ";
		    				   							}
		    				   						?>
		    				   						
		    				   							<div class="checkbox checkbox-success"  style="text-align:center">
		                            	<input type="checkbox" id="h_en" name="h_en" value="1" <?php echo $active; ?>> />
		                              <label for="h_en"></label>
		                            </div>	
		    				   					</td>
		    				   					<?php
		    				   						if($default_temp == "F")
		    				  						{
		    				  							$the_temp = "Fahrenheit";
		    				  						}
		    				  						else
		    				  						{
		    				  							$the_temp = "Celsius";
		    				  						}
		    				  					?>
		    				   					
		    				   					<td>
		    				   						<div style="text-align:center;">
		    				   							<strong style="font-size: 15px;color:blue;"><?php echo $the_temp; ?></strong>
		    				   						</div>
		    				   						<div class="table-responsive">
		    				   							<table class="table table-condensed table-hover">
																	<tr>
																		<td style="text-align:right;vertical-align:middle">
																			<strong style="font-size: 15px;">High trigger value (max):</strong>
		    				   									</td>	
		    				   									<td>
		              										<input style="max-width:70%; min-width:70%" type="text" class="form-control" name='hi_t' value='<?php $hi_t = sprintf("%.4f",$hi_t); echo $hi_t; ?>' onMouseOver="mouse_move(&#039;thimax&#039;);" onMouseOut="mouse_move();" required />		
		    				   									</td>
		    				   								</tr>
		    				   								<tr>
																		<td style="text-align:right;vertical-align:middle">
																			<strong style="font-size: 15px;">High trigger value (min):</strong>
		    				   									</td>	
		    				   									<td>
		              										<input style="max-width:70%; min-width:70%" type="text" class="form-control" name='hi_t_min' value='<?php $hi_t_min = sprintf("%.4f",$hi_t_min); echo $hi_t_min; ?>' onMouseOver="mouse_move(&#039;thimin&#039;);" onMouseOut="mouse_move();" required />		
		    				   									</td>
		    				   								</tr>
		    				   								<tr>
		    				   									<td style="text-align:right;vertical-align:middle">
		    				   										<strong style="font-size: 15px;">Execute the actions below every:</strong> 
																		</td>
																		<td>
																			<select class="form-control input-sm" style="max-width:70%; min-width:70%" name="hi_flap" >
																				<option value="0">One Shot</option>
																				<?php
																				$ii=1;	if($hi_flap==$ii) {$chan=sprintf("selected");} else {$chan=sprintf(" ");} echo"<option ".$chan." value=".$ii.">".$ii." Second</option>";	
																				for($ii=2; $ii<60; $ii++)	{	if($hi_flap==$ii) {$chan = "selected";} else {$chan = " ";} echo"<option " . $chan . " value='" . $ii . "'>" . $ii . " Seconds</option>";	}
																				$ii=1; if($hi_flap==($ii*60)) {$chan = "selected";} else {$chan = " ";} echo "<option " . $chan . " value='" . $ii*60 . "'>" . $ii . " Minute</option>";
																				for($ii=2; $ii<60; $ii++)	{	if($hi_flap==($ii*60)) {$chan = "selected";} else {$chan = " ";} echo "<option " . $chan . " value='" . $ii*60 . "'>" . $ii . " Minutes</option>";	}
																				$ii=1;	if($hi_flap==($ii*60*60)) {$chan = "selected";} else {$chan = " ";} echo "<option " . $chan . " value='" . $ii*60*60 . "'>" . $ii . " Hour</option>";
																				for($ii=2; $ii<25; $ii++)	{ if($hi_flap==($ii*60*60)) {$chan = "selected";} else {$chan = " ";} echo "<option " . $chan . " value='" . $ii*60*60 . "'>" . $ii . " Hours</option>";	}
																				?>
		    				   										</select>
		    				   									</td>
		    				   								</tr>
		    				   							</table>
		    				   						</div>
														</td>
														
														<td style="vertical-align:middle">
															<div style="text-align:center;"><strong style="font-size: 15px;color:black;">These events will fire when the Temperature drops below the high trigger minimum value.</strong></div>	
														</td>
													</tr>
													
													<tr>
		    				   					<td style="vertical-align:middle">
		    				   						<div style="text-align:center;"><a name="#hi1"></a><br><a href="setup_notifications.php"><strong><u class="dotted">Alerts</u></strong></a></div>
		    				   					</td>
		    				   					<td>		
		    				   						<?php selectbox("HI", "alert", $HI_alert_cmds); ?>	
		    				   					</td>				
		    				   					<td>						
		    				   						<?php selectbox("HI_N", "alert", $HI_N_alert_cmds); ?>
		    				   					</td>							
		    				   				</tr>
		    				   				
		    				   				<tr>				
		    				   					<td style="vertical-align:middle">
		    				   						<div style="text-align:center;"><a name="#hiN1"></a><br><a href="setup_scripts.php?"><strong><u class="dotted">Scripts</u></strong></a></div>
		    				   					</td>
		    				   					<td>		
		    				   						<?php selectbox("HI", "script", $HI_script_cmds); ?>	
		    				   					</td>				
		    				   					<td>						
		    				   						<?php selectbox("HI_N", "script", $HI_N_script_cmds); ?>
		    				   					</td>										
		    				   				</tr>
		    				   				
		    				   				<tr>
														<td style="vertical-align:middle">
														<div style="text-align:center;"><a href="#hi3"></a><a href="setup_file_explorer.php"><strong><u class="dotted">File</u></strong></a></center></div>
														</td>
													
														<td>
															<div><label class="col-sm-2 control-label">Execute File:</label>
		              							<div class="col-sm-8">
		              								<input type="text" class="form-control" name='RunHiFile' value='<?php echo $RunHiFile; ?>' />
		              							</div>
		              						</div>
														</td>
													
														<td>
															<div><label class="col-sm-2 control-label">Execute File:</label>
		              							<div class="col-sm-8">
		              								<input type="text" class="form-control" name='RunHiNFile' value='<?php echo $RunHiNFile; ?>' />
		              							</div>
		              						</div>
														</td>
													</tr>
		    				   			</tbody>
		  								</table>      	    
		    		  	  	</div> <!-- END TABLE RESPONSIVE -->
		    		  	  	
		    		  	  	
		    		  	  	<div class="table-responsive">
		    				   		<table width="100%" border="1" class="table table-striped table-condensed">
		    				   			<thead>
		    				   				<tr>
		    				   					<th width="10%" style="background:#D6DFF7;">
		    				   						<div style="text-align:center;color:black">Enabled</div>
		    				   					</th>
		    				   					<th width="40%" style="background:yellow">
		    				   						<div style="text-align:center;color:black">Temperature Low Trigger Range</div>
		    				   					</th>
		    				   					<th width="40%" style="background:#40FF40">
		    				   						<div style="text-align:center;color:black">Temperature Low Normal Trigger</div>
		    				   					</th>
		    				   				</tr>
		    				   			</thead>
		    				   			<tbody>
		    				   				<tr>
		    				   					<td style="vertical-align:middle">
		    				   						<?php
		    				   							if($l_en == "1")
		    				   							{
		    				   								$active = "checked";
		    				   							}
		    				   							else
		    				   							{
		    				   								$active = " ";
		    				   							}
		    				   						?>
		    				   						
		    				   					
		    				   							<div class="checkbox checkbox-success"  style="text-align:center">
		                            	<input type="checkbox" id="l_en" name="l_en" value="1" <?php echo $active; ?>> />
		                              <label for="l_en"></label>
		                            </div>	
		    				   					</td>
		    				   					<?php
		    				   						if($default_temp == "F")
		    				  						{
		    				  							$the_temp = "Fahrenheit";
		    				  						}
		    				  						else
		    				  						{
		    				  							$the_temp = "Celsius";
		    				  						}
		    				  					?>
		    				   					
		    				   					<td>
		    				   						<div style="text-align:center;">
		    				   							<strong style="font-size: 15px;color:blue;"><?php echo $the_temp; ?></strong>
		    				   						</div>
		    				   						<div class="table-responsive">
		    				   							<table class="table table-condensed table-hover">
																	<tr>
																		<td style="text-align:right;vertical-align:middle">
																			<strong style="font-size: 15px;">Low trigger value (min):</strong>
		    				   									</td>	
		    				   									<td>
		              										<input style="max-width:70%; min-width:70%" type="text" class="form-control" name='lo_t_max' value='<?php $lo_t_max = sprintf("%.4f",$lo_t_max); echo $lo_t_max; ?>' onMouseOver="mouse_move(&#039;tlomin&#039;);" onMouseOut="mouse_move();" required />		
		    				   									</td>
		    				   								</tr>
		    				   								<tr>
																		<td style="text-align:right;vertical-align:middle">
																			<strong style="font-size: 15px;">Low trigger value (max):</strong>
		    				   									</td>	
		    				   									<td>
		              										<input style="max-width:70%; min-width:70%" type="text" class="form-control" name='lo_t' value='<?php $lo_t = sprintf("%.4f",$lo_t); echo $lo_t; ?>' onMouseOver="mouse_move(&#039;tlomax&#039;);" onMouseOut="mouse_move();" required />		
		              										
		    				   									</td>
		    				   								</tr>
		    				   								<tr>
		    				   									<td style="text-align:right;vertical-align:middle">
		    				   										<strong style="font-size: 15px;">Execute the actions below every:</strong> 
																		</td>
																		<td>
																			<select class="form-control input-sm" style="max-width:70%; min-width:70%" name="lo_flap" >
																				<option value="0">One Shot</option>
																				<?php
																				$ii=1;	if($lo_flap==$ii) {$chan=sprintf("selected");} else {$chan=sprintf(" ");} echo"<option ".$chan." value=".$ii.">".$ii." Second</option>";	
																				for($ii=2; $ii<60; $ii++)	{	if($lo_flap==$ii) {$chan = "selected";} else {$chan = " ";} echo"<option " . $chan . " value='" . $ii . "'>" . $ii . " Seconds</option>";	}
																				$ii=1; if($lo_flap==($ii*60)) {$chan = "selected";} else {$chan = " ";} echo "<option " . $chan . " value='" . $ii*60 . "'>" . $ii . " Minute</option>";
																				for($ii=2; $ii<60; $ii++)	{	if($lo_flap==($ii*60)) {$chan = "selected";} else {$chan = " ";} echo "<option " . $chan . " value='" . $ii*60 . "'>" . $ii . " Minutes</option>";	}
																				$ii=1;	if($lo_flap==($ii*60*60)) {$chan = "selected";} else {$chan = " ";} echo "<option " . $chan . " value='" . $ii*60*60 . "'>" . $ii . " Hour</option>";
																				for($ii=2; $ii<25; $ii++)	{ if($lo_flap==($ii*60*60)) {$chan = "selected";} else {$chan = " ";} echo "<option " . $chan . " value='" . $ii*60*60 . "'>" . $ii . " Hours</option>";	}
																				?>
		    				   										</select>
		    				   									</td>
		    				   								</tr>
		    				   							</table>
		    				   						</div>
														</td>
														
														<td style="vertical-align:middle">
															<div style="text-align:center;"><strong style="font-size: 15px;color:black;">These events will fire when the Temperature rises above the low trigger minimum value.</strong></div>	
														</td>
													</tr>
													
													<tr>
		    				   					<td style="vertical-align:middle">
		    				   						<div style="text-align:center;"><a name="#lo1"></a><br><a href="setup_notifications.php"><strong><u class="dotted">Alerts</u></strong></a></div>
		    				   					</td>
		    				   					<td>		
		    				   						<?php selectbox("LO", "alert", $LO_alert_cmds); ?>	
		    				   					</td>				
		    				   					<td>						
		    				   						<?php selectbox("LO_N", "alert", $LO_N_alert_cmds); ?>
		    				   					</td>							
		    				   				</tr>
		    				   				
		    				   				<tr>				
		    				   					<td style="vertical-align:middle">
		    				   						<div style="text-align:center;"><a name="#loN1"></a><br><a href="setup_scripts.php?"><strong><u class="dotted">Scripts</u></strong></a></div>
		    				   					</td>
		    				   					<td>		
		    				   						<?php selectbox("LO", "script", $LO_script_cmds); ?>	
		    				   					</td>				
		    				   					<td>						
		    				   						<?php selectbox("LO_N", "script", $LO_N_script_cmds); ?>
		    				   					</td>										
		    				   				</tr>
		    				   				
		    				   				<tr>
														<td style="vertical-align:middle">
														<div style="text-align:center;"><a href="#lo3"></a><a href="setup_file_explorer.php"><strong><u class="dotted">File</u></strong></a></center></div>
														</td>
													
														<td>
															<div><label class="col-sm-2 control-label">Execute File:</label>
		              							<div class="col-sm-8">
		              								<input type="text" class="form-control" name='RunLowFile' value='<?php echo $RunLowFile; ?>' />
		              							</div>
		              						</div>
														</td>
													
														<td>
															<div><label class="col-sm-2 control-label">Execute File:</label>
		              							<div class="col-sm-8">
		              								<input type="text" class="form-control" name='RunLowNFile' value='<?php echo $RunLowNFile; ?>' />
		              							</div>
		              						</div>
														</td>
													</tr>
		    				   			</tbody>
		  								</table>      	    
		    		  	  	</div> <!-- END TABLE RESPONSIVE -->
		    		  	  	
		    		  	  	<div class="form-group">
		        					<div class="col-sm-2" style="max-width:300px; min-width:300px">
		        						<button name="apply_btn" class="btn btn-success" type="submit" onMouseOver="mouse_move(&#039;b_apply&#039;);" onMouseOut="mouse_move();"><i class="fa fa-check" ></i> Apply</button>
		        						<button name="cancel_btn" class="btn btn-primary" type="submit" onMouseOver="mouse_move(&#039;b_cancel&#039;);" onMouseOut="mouse_move();" formnovalidate><i class="fa fa-times"></i> Cancel</button>
		        					</div>
		        				</div>
		    		  		</div> <!-- END PANEL BODY --> 
		    		  	</div> <!-- END HPANEL3 --> 
		    		  </div> <!-- END COL-MD-12 --> 
		    		</div> <!-- END ROW --> 			
		  </div> <!-- END CONTENT -->    
		</div> <!-- END Main Wrapper -->
	</fieldset>
</form>	
<script language="javascript" type="text/javascript">
	function display_temps()
	{
			var myRandom = parseInt(Math.random()*999999999);
     	$.getJSON('sdserver.php?element=tempall&rand=' + myRandom,
     	function(data)
     	{
     		$.each (data.tmp, function (k, v) { $('#' + k).text (v); });
				setTimeout (display_temps, 1000);
		 	}
		);
	}

	display_temps();
</script>




<?php 

if($alert_flag == "1")
{
echo"<script>";
echo"swal({";
echo"  title:'Success!',";
echo"  text: 'Settings Saved',";
echo"  type: 'success',";
echo"  showConfirmButton: false,";
echo"	 html: true,";
echo"  timer: 2500";
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

if($alert_flag == "3")
{
echo"<script>";
echo"swal({";
echo"  title:'Success!',";
echo"  text: 'LM75 Temperature Chip Reset',";
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









 
















