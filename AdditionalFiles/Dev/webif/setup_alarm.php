<?php
	error_reporting(E_ALL);
	ob_start('ob_gzhandler');
	include "lib.php";
	
	if(empty($_GET) && empty($_POST)) 
	{ 
		/* no parameters passed*/
		echo "This web page must be accessed through the RMS web interface!";
		exit(0);
	}

	$hostname = trim(file_get_contents("/etc/hostname"));
	$header = "";
	$alert_flag = "0";
	$query = "";
	$id = "0";
	$type = "alarm";
	$notes = "";
	$en = "";
	$HI_alert_cmds = "";
	$LO_alert_cmds = ""; 
	$HI_script_cmds = "";
	$LO_script_cmds = "";
	$hi_flap = "";
	$lo_flap = "";
	$dos = "";
	$RunHiIoFile = "";
	$RunLowIoFile = "";
	$iodir = "";
	$iostate = "";
	$pullup = "";
	$glitch = "";
	$text = "";
	$dbh = new PDO('sqlite:/etc/rms100.db');
	$result  = $dbh->query("SELECT * FROM display_options;");			
	foreach($result as $row)
	{
		$screen_animations = $row['screen_animations'];
	}
	
	$result  = $dbh->query("SELECT * FROM throttle;");			
	foreach($result as $row)
	{
		$dt = $row['delay'];
	}
		
/////////////////////////////////////////////////////////////////
//                                                             //
//                    GET PROCESSING                           //
//                                                             //
/////////////////////////////////////////////////////////////////


	
if(isset($_GET['alarm']))
{
	$id = $_GET['alarm'];
	$query = sprintf("SELECT * FROM io WHERE id='%d' and type='alarm'",$id);
	$result  = $dbh->query($query);
	foreach($result as $row)
	{
		$name = $row['name'];
		$notes = $row['notes'];
		$en = $row['en'];
		$HI_alert_cmds = $row['HI_alert_cmds'];
		$LO_alert_cmds = $row['LO_alert_cmds']; 
		$HI_script_cmds = $row['HI_script_cmds'];
		$LO_script_cmds = $row['LO_script_cmds'];
		$hi_flap = $row['hi_flap'];
		$lo_flap = $row['lo_flap'];
		$dos = $row['dos'];
		$RunHiIoFile = $row['RunHiIoFile'];
		$RunLowIoFile = $row['RunLowIoFile'];
		$iodir = $row['iodir'];
		$iostate = $row['iostate'];
		$pullup = $row['pullup'];
		$glitch = $row['glitch'];
		$header = "Edit Alarm #" . $id;
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
	header("Location: ios.php");
}

// Save Button	was clicked
if(isset ($_POST['save_btn']))
{
	$id = $_POST['id'];
	$name = $_POST['name'];
	$notes = $_POST['notes'];
	if(isset ($_POST['en']))
	{
		$en = "1";
	}
	else
	{
		$en = "0";
	}
	
	if(isset ($_POST['supress']))
	{
		$suppressed = "CHECKED";
	}
	else
	{
		$suppressed = "UNCHECKED";
	}
	
	
	if(isset($_POST['HI_alert_delcmd']))
	{
		foreach ($_POST['HI_alert_delcmd'] as $HI_alert_delcmdBox)
		{
    	$HI_alert_cmds = $HI_alert_cmds . $HI_alert_delcmdBox . ".";
  	} 
	}
	//echo "HI_alert_cmds " . $HI_alert_cmds . "\n";
	
	if(isset($_POST['LO_alert_delcmd']))
	{
		foreach ($_POST['LO_alert_delcmd'] as $LO_alert_delcmdBox)
		{
    	$LO_alert_cmds = $LO_alert_cmds . $LO_alert_delcmdBox . ".";
  	} 
	}
	//echo "LO_alert_cmds " . $LO_alert_cmds . "\n";
	
	if(isset($_POST['HI_script_delcmd']))
	{
		foreach ($_POST['HI_script_delcmd'] as $HI_script_delcmdBox)
		{
    	$HI_script_cmds = $HI_script_cmds . $HI_script_delcmdBox . ".";
  	} 
	}
	//echo "HI_script_cmds " . $HI_script_cmds . "\n";
	
	if(isset($_POST['LO_script_delcmd']))
	{
		foreach ($_POST['LO_script_delcmd'] as $LO_script_delcmdBox)
		{
    	$LO_script_cmds = $LO_script_cmds . $LO_script_delcmdBox . ".";
  	} 
	}
	//echo "LO_script_cmds " . $LO_script_cmds . "\n";
	
	//$hi_flap = $_POST['hi_flap'];
	//$lo_flap = $_POST['lo_flap'];
	//$dos = $_POST['dos'];
	$RunHiIoFile = $_POST['RunHiIoFile'];
	$RunLowIoFile = $_POST['RunLowIoFile'];
	$hi_state_name = $_POST['hi_state_name'];
	$lo_state_name = $_POST['lo_state_name'];
	$hi_icon = $_POST['hi_ball'];
	$lo_icon = $_POST['lo_ball'];
	$hi_flap = $_POST['hi_flap'];
	$lo_flap = $_POST['lo_flap'];
	//echo "Hi Icon: ".$hi_icon." Low Icon :".$lo_icon;
	if($hi_icon == $lo_icon)
	{
		$text = "The High State Icon cannot be the same as the Low State Icon!";
		$alert_flag = "2";
		goto noSave;
	}
	if($hi_state_name == $lo_state_name)
	{
		$text = "Names for High Input State and Low Input State must be different!";
		$alert_flag = "2";
		goto noSave;
	}
	
	
	//$iodir = $_POST['iodir'];
	//$iostate = $_POST['iostate'];
	//$pullup = $_POST['pullup'];
	//$glitch = $_POST['glitch'];
	
	
	$query = sprintf("UPDATE alarm_options SET hi_state_name='%s', lo_state_name='%s', hi_icon='%s', lo_icon='%s' WHERE id='%d';",$hi_state_name, $lo_state_name, $hi_icon, $lo_icon, $id);
	$result  = $dbh->exec($query); 
	
	$query = sprintf("UPDATE io SET name='%s', notes='%s', en='%d', HI_alert_cmds='%s', HI_script_cmds='%s', LO_alert_cmds='%s', LO_script_cmds='%s', hi_flap='%s', lo_flap='%s', RunHiIoFile='%s', RunLowIoFile='%s' WHERE id='%d' AND type='alarm';",$name, $notes, $en, $HI_alert_cmds, $HI_script_cmds, $LO_alert_cmds, $LO_script_cmds, $hi_flap, $lo_flap, $RunHiIoFile, $RunLowIoFile, $id);
	$result  = $dbh->exec($query); 
	
	$query = sprintf("UPDATE alarm_trig_supress SET supress='%s' WHERE id='%d';", $suppressed, $id);
	$result  = $dbh->exec($query); 
	restart_some_services();
	header("Location: ios.php?action=edit&success=yes&id=".$id."&type=Alarm");
	
	noSave:    
}

$query = sprintf("SELECT * FROM alarm_options WHERE id = '%d'", $id);
//echo $query;
$result  = $dbh->query($query);
foreach($result as $row)
{	
	$hi_state_name = $row['hi_state_name']; 					
	$lo_state_name = $row['lo_state_name'];
	$hi_icon = $row['hi_icon'];
	$lo_icon = $row['lo_icon'];
}

$query = sprintf("SELECT * FROM alarm_trig_supress WHERE id = '%d'", $id);
$result  = $dbh->query($query);
foreach($result as $row)
{	
	$supress = $row['supress']; 					
}

if($id == "1"){$state = "a1state";}
if($id == "2"){$state = "a2state";}
if($id == "3"){$state = "a3state";}
if($id == "4"){$state = "a4state";}
if($id == "5"){$state = "a5state";}
				
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
			SetContext('ios');
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
     
     $('#add_LO_alert').click(function() {  
        return !$('#LO_alert_addcmd option:selected').clone().appendTo('#LO_alert_delcmd');  
     });  
     $('#remove_LO_alert').click(function() {  
        return !$('#LO_alert_delcmd option:selected').remove();   
     });
     //scripts
     $('#add_HI_script').click(function() {  
        return !$('#HI_script_addcmd option:selected').clone().appendTo('#HI_script_delcmd');  
     });  
     $('#remove_HI_script').click(function() {  
        return !$('#HI_script_delcmd option:selected').remove();   
     });
     
     $('#add_LO_script').click(function() {  
        return !$('#LO_script_addcmd option:selected').clone().appendTo('#LO_script_delcmd');  
     });  
     $('#remove_LO_script').click(function() {  
        return !$('#LO_script_delcmd option:selected').remove();   
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

<?php left_nav("ios"); ?>
<script language="javascript" type="text/javascript">
	SetContext('ios');
</script>
<!-- Main Wrapper -->


<div id="wrapper">
	
	<?php
		if($screen_animations == "CHECKED")
		{
			echo '<div class="content animate-panel" data-effect="fadeInUpBig">';
		}
		else
		{
			echo '<div class="content">';
		}
	?>
  	<!-- INFO BLOCK START -->
  	<form name='IOS' action='setup_alarm.php' method='post' class="form-horizontal" onsubmit="selectAllOptions('HI_alert_delcmd');selectAllOptions('LO_alert_delcmd');selectAllOptions('HI_script_delcmd');selectAllOptions('LO_script_delcmd');">  	
    	<fieldset>
  			<div class="row">
    			<div class="col-sm-12">
    		  	<div class="hpanel3">
    		  	  <div class="panel-body" style="text-align:left; background:#F1F3F6;border:none;">
    				  	<legend><img src="images/ping2.gif"> <?php echo $header; ?></legend> 

    				  	<div class="form-group"><label class="col-sm-1 control-label" style="text-align:left; min-width:120px; max-width:120px">Alarm Name:</label>
              		<div class="col-sm-3" style="min-width:325px; max-width:325px">
              			<input type="text" class="form-control" name='name' value='<?php echo $name; ?>' required />
              		</div>
              	</div>
    				  	
    				  	<div class="form-group"><label class="col-sm-1 control-label" style="text-align:left; min-width:120px; max-width:120px">Alarm Notes:</label>
              		<div class="col-sm-3" style="min-width:325px; max-width:325px">
              			<textarea  rows="3" cols="50" class="form-control" name='notes' required><?php echo $notes; ?></textarea>
              		</div>
              	</div>
              	
              	<div class="form-group"><label class="col-sm-1 control-label" style="text-align:left; min-width:120px; max-width:120px"></label>
              		<div class="col-sm-4" style="min-width:325px; max-width:325px">
              			<div class="checkbox checkbox-danger" onMouseOver="mouse_move(&#039;sd_suppress&#039;);" onMouseOut="mouse_move();">
              				<input type="checkbox" id="supress" name="supress" <?php echo $supress; ?> />
    		            	<label for="suppress" onMouseOver="mouse_move(&#039;sd_suppress&#039;);" onMouseOut="mouse_move();">Suppress Trigger Actions on Boot or Save?</label>
              			</div>
              		</div>
              	</div>
    				  	
    				  	<div class="form-group"><label class="col-sm-1 control-label" style="text-align:left; min-width:120px; max-width:120px">High State:</label>
              		<div class="col-sm-2" style="text-align:left; min-width:325px; max-width:325px">
              			<input type="text" class="form-control" name='hi_state_name' value='<?php echo $hi_state_name; ?>' required />
              		</div>
              		<div class="col-sm-1" style="text-align:left; min-width:130px; max-width:130px">
              			<div class="radio radio-danger"  style="text-align:left">
              				<?php
    				  					if($hi_icon == "RED")
    				  					{
    				  						echo '<input type="radio" id="hi_ball" name="hi_ball" value="RED" checked/>';
    				  					}
    				  					else
    				  					{
    				  						echo '<input type="radio" id="hi_ball" name="hi_ball" value="RED" />';
    				  					}
    				  				?>
                      <label for="hi_ball">Red Icon</label>
                      <img src="images/red_att.gif">
                    </div>
                  </div>
                  <div class="col-sm-2" style="text-align:left; min-width:140px; max-width:140px">
                    <div class="radio radio-success"  style="text-align:left">
                    	<?php
    				  					if($hi_icon == "GREEN")
    				  					{
    				  						echo '<input type="radio" id="hi_ball" name="hi_ball" value="GREEN" checked/>';
    				  					}
    				  					else
    				  					{
    				  						echo '<input type="radio" id="hi_ball" name="hi_ball" value="GREEN" />';
    				  					}
    				  				?>
                    	
                      <label for="hi_ball">Green Icon</label>
                      <img src="images/ok.gif">
                    </div>
                  </div>
              	</div>
    				  	
    				  	
    				  	<div class="form-group"><label class="col-sm-1 control-label" style="text-align:left; min-width:120px; max-width:120px">Low State:</label>
              		<div class="col-sm-2" style="text-align:leftstyle; min-width:325px; max-width:325px">
              			<input type="text" class="form-control" name='lo_state_name' value='<?php echo $lo_state_name; ?>' required />
              		</div>
              		<div class="col-sm-1" style="text-align:left; min-width:130px; max-width:130px">
              			<div class="radio radio-danger"  style="text-align:left">
              				<?php
    				  					if($lo_icon == "RED")
    				  					{
    				  						echo '<input type="radio" id="lo_ball" name="lo_ball" value="RED" checked/>';
    				  					}
    				  					else
    				  					{
    				  						echo '<input type="radio" id="lo_ball" name="lo_ball" value="RED" />';
    				  					}
    				  				?>
                      <label for="lo_ball">Red Icon</label>
                      <img src="images/red_att.gif">
                    </div>
                  </div>
                  <div class="col-sm-2" style="text-align:left; min-width:140px; max-width:140px">
                    <div class="radio radio-success"  style="text-align:left">
                    	<?php
    				  					if($lo_icon == "GREEN")
    				  					{
    				  						echo '<input type="radio" id="lo_ball" name="lo_ball" value="GREEN" checked/>';
    				  					}
    				  					else
    				  					{
    				  						echo '<input type="radio" id="lo_ball" name="lo_ball" value="GREEN" />';
    				  					}
    				  				?>
                      <label for="lo_ball">Green Icon</label>
                      <img src="images/ok.gif">
                    </div>
                  </div>
              	</div>
    				  	
    				  	<div class="form-group"><label class="col-sm-1 control-label" style="text-align:left; min-width:120px; max-width:120px">Alarm State:</label>
              		<div class="col-sm-2">
              			<label class="col-sm-12 control-label" style="text-align:left">
              				<div id="<?php echo $state; ?>"></div> 
              				</label>
              		</div>
              	</div>
							</div>
						</div>
					</div>
				</div>		
    		<div class="row">
    			<div class="col-sm-12">
    		  	<div class="hpanel3">
    		  	  <div class="panel-body" style="text-align:left; background:#F1F3F6;border:none;">
    				  	<div class="table-responsive">
    				   		<table width="100%" border="1" class="table table-striped table-condensed">
    				   			<thead>
    				   				<tr>
    				   					<th width="10%" style="background:#D6DFF7;">
    				   						<div style="text-align:center;color:black">Enabled</div>
    				   					</th>
    				   					
    				   					<?php
    				   						if($hi_icon == "GREEN")
    				   						{
    				   							echo '<th width="40%" style="background:#40FF40">';
    				   						}
    				   						else
    				   						{
    				   							echo '<th width="40%" style="background:'.$hi_icon.'">';
    				   						}
    				   					?>
    				   						<div style="text-align:center;color:black">Alarm <?php echo $id; ?> <?php echo $hi_state_name; ?></div>
    				   					</th>
    				   					<?php
    				   						if($lo_icon == "GREEN")
    				   						{
    				   							echo '<th width="40%" style="background:#40FF40">';
    				   						}
    				   						else
    				   						{
    				   							echo '<th width="40%" style="background:'.$lo_icon.'">';
    				   						}
    				   					?>
    				   						<div style="text-align:center;color:black">Alarm <?php echo $id; ?> <?php echo $lo_state_name; ?></div>
    				   					</th>
    				   				</tr>
    				   			</thead>
    				   			<tbody>
    				   				<tr>
    				   					<td style="vertical-align:middle">
    				   						<?php
    				   							if($en == "1")
    				   							{
    				   								$active = "checked";
    				   							}
    				   							else
    				   							{
    				   								$active = " ";
    				   							}
    				   						?>
    				   						
    				   					
    				   							<div class="checkbox checkbox-success"  style="text-align:center">
                            	<input type="checkbox" id="en" name="en" value="1" onMouseOver="mouse_move(&#039;sd_enabled&#039;);" onMouseOut="mouse_move();" <?php echo $active; ?> />
                              <label for="en"></label>
                            </div>
    				   					</td>
    				   					
    				   					<td>
    				   						<div style="text-align:center;">
    				   							<strong style="font-size: 15px;color:blue;">These events will fire when Alarm <?php echo $id; ?> is <?php echo $hi_state_name; ?>.</strong>
    				   						</div>
    				   						<div style="text-align:center; margin-top: 15px;">
    				   							<strong style="font-size: 15px;">Execute the actions below every:</strong>
    				   						</div>
    				   						<div style="text-align:center;">
    				   							<select class="form-control input-sm" style="max-width:105px; min-width:105px; display: block; margin: 0 auto;" name="hi_flap" >
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
		    				   				</div>
												</td>
												
												<td style="vertical-align:middle">
													<div style="text-align:center;"><strong style="font-size: 15px;color:blue;">These events will fire when Alarm <?php echo $id; ?> is <?php echo $lo_state_name; ?>.</strong></div>	
													<div style="text-align:center; margin-top: 15px;">
    				   							<strong style="font-size: 15px;">Execute the actions below every:</strong>
    				   						</div>
    				   						<div style="text-align:center;">
    				   							<select class="form-control input-sm" style="max-width:105px; min-width:105px; display: block; margin: 0 auto;" name="lo_flap" >
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
		    				   				</div>
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
    				   						<?php selectbox("LO", "alert", $LO_alert_cmds); ?>
    				   					</td>							
    				   				</tr>
    				   				
    				   				<tr>				
    				   					<td style="vertical-align:middle">
    				   						<div style="text-align:center;"><a name="#lo1"></a><br><a href="setup_scripts.php?"><strong><u class="dotted">Scripts</u></strong></a></div>
    				   					</td>
    				   					<td>		
    				   						<?php selectbox("HI", "script", $HI_script_cmds); ?>	
    				   					</td>				
    				   					<td>						
    				   						<?php selectbox("LO", "script", $LO_script_cmds); ?>
    				   					</td>										
    				   				</tr>
    				   				
    				   				<tr>
												<td style="vertical-align:middle">
												<div style="text-align:center;"><a href="#hi3"></a><a href="setup_file_explorer.php"><strong><u class="dotted">File</u></strong></a></center></div>
												</td>
											
												<td>
													<div><label class="col-sm-2 control-label">Execute File:</label>
              							<div class="col-sm-8">
              								<input type="text" class="form-control" name='RunHiIoFile' value='<?php echo $RunHiIoFile; ?>' />
              							</div>
              						</div>
												</td>
											
												<td>
													<div><label class="col-sm-2 control-label">Execute File:</label>
              							<div class="col-sm-8">
              								<input type="text" class="form-control" name='RunLowIoFile' value='<?php echo $RunLowIoFile; ?>' />
              							</div>
              						</div>
												</td>
											</tr>
    				   			</tbody>
  								</table>      	    
    		  	  	</div> <!-- END TABLE RESPONSIVE -->
    		  	  	<div class="form-group">
        					<div class="col-sm-12">
        						<input type="hidden" name="id" value="<?php echo $id; ?>">
        						<button name="save_btn" class="btn btn-success" type="submit" onMouseOver="mouse_move(&#039;b_apply&#039;);" onMouseOut="mouse_move();"><i class="fa fa-check" ></i> Save</button>
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

<script language="javascript" type="text/javascript">
		function display_io ()
		{
		        var myRandom = parseInt(Math.random()*999999999);
		        $.getJSON('sdserver.php?element=alarmall&rand=' + myRandom,
		            function(data)
		            {
											<?php
		                  	echo"setTimeout (display_io," . $dt . ");";
		                  ?>
		                
		                  if (data.alarms.a1 == 0)
		                  {
		                  	$('#a1state').replaceWith("<div id='a1state' style='color:" + data.alarms.aLi1 + "'>" + data.alarms.a1lo + "</div>");
		                  }
		                  else
		                  {
		                    $('#a1state').replaceWith("<div id='a1state' style='color:" + data.alarms.aHi1 + "'>" + data.alarms.a1hi + "</div>"); 
		                  }
											
											if (data.alarms.a2 == 0)
		                  {
		                  	$('#a2state').replaceWith("<div id='a2state' style='color:" + data.alarms.aLi2 + "'>" + data.alarms.a2lo + "</div>");
		                  }
		                  else
		                  {
		                    $('#a2state').replaceWith("<div id='a2state' style='color:" + data.alarms.aHi2 + "'>" + data.alarms.a2hi + "</div>"); 
		                  }
		                  
		                  if (data.alarms.a3 == 0)
		                  {
		                  	$('#a3state').replaceWith("<div id='a3state' style='color:" + data.alarms.aLi3 + "'>" + data.alarms.a3lo + "</div>");
		                  }
		                  else
		                  {
		                    $('#a3state').replaceWith("<div id='a3state' style='color:" + data.alarms.aHi3 + "'>" + data.alarms.a3hi + "</div>"); 
		                  }
		                  
		                  if (data.alarms.a4 == 0)
		                  {
		                  	$('#a4state').replaceWith("<div id='a4state' style='color:" + data.alarms.aLi4 + "'>" + data.alarms.a4lo + "</div>");
		                  }
		                  else
		                  {
		                    $('#a4state').replaceWith("<div id='a4state' style='color:" + data.alarms.aHi4 + "'>" + data.alarms.a4hi + "</div>"); 
		                  }
		                    
		                  if (data.alarms.a5 == 0)
		                  {
		                  	$('#a5state').replaceWith("<div id='a5state' style='color:" + data.alarms.aLi5 + "'>" + data.alarms.a5lo + "</div>");
		                  }
		                  else
		                  {
		                    $('#a5state').replaceWith("<div id='a5state' style='color:" + data.alarms.aHi5 + "'>" + data.alarms.a5hi + "</div>"); 
		                  } 
		                   
		            }
		        );
		}

	
		display_io ();
		</script>




<?php 

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

echo "</body>";
echo "</html>";

?>
