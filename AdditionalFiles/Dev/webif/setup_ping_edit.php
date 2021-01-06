<?php
	error_reporting(E_ALL);
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
	$ip = "";
	$notes = "";
	$en = "";
	$HI_alert_cmds = "";
	$LO_alert_cmds = ""; 
	$HI_script_cmds = "";
	$LO_script_cmds = "";
	$hi_flap = "";
	$RunUpPingFile = "";
	$RunDownPingFile = "";
	$howmanytimes = "";
	$pausetime = "";
	$processtime = "";
	$active = "";
	$chan = "";
	$text = "";
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


	
if(isset($_GET['pingtarget']))
	{
		$action = $_GET['pingtarget'];
		if($action == "edit")
		{
			$id = $_GET['sid'];
			$query = sprintf("SELECT * FROM ping_targets WHERE id=%d",$id);
			$result  = $dbh->query($query);
			foreach($result as $row)
			{
				$id = $row['id'];
				$ip = $row['ip'];
				$notes = $row['notes'];
				$en = $row['en'];
				$HI_alert_cmds = $row['HI_alert_cmds'];
				$LO_alert_cmds = $row['LO_alert_cmds']; 
				$HI_script_cmds = $row['HI_script_cmds'];
				$LO_script_cmds = $row['LO_script_cmds'];
				$hi_flap = $row['hi_flap'];
				$lo_flap = $row['lo_flap'];
				$hi_flag = $row['hi_flag'];
				$lo_flag = $row['lo_flag'];
				$hi_flap_time = $row['hi_flap_time'];
				$lo_flap_time = $row['lo_flap_time'];
				$RunUpPingFile = $row['RunUpPingFile'];
				$RunDownPingFile = $row['RunDownPingFile'];
				$howmanytimes = $row['howmanytimes'];
				$pausetime = $row['pausetime'];
				$processtime = $row['processtime'];
				$header = "Edit Ping Target #" . $id;
			}
		}
		else
		{
			echo "This web page must be accessed through the RMS web interface!";
			exit(0);
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
	header("Location: setup_ping.php");
}

// Save Button	was clicked
if(isset ($_POST['save_btn']))
{
	$id = $_POST['id'];
	$ip = $_POST['ip'];
	$notes = $_POST['notes'];
	if(isset ($_POST['en']))
	{
		$en = "on";
	}
	else
	{
		$en = "off";
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
	
	$hi_flap = $_POST['hi_flap'];
	$RunUpPingFile = $_POST['RunUpPingFile'];
	$RunDownPingFile = $_POST['RunDownPingFile'];
	$howmanytimes = $_POST['howmanytimes'];
	$pausetime = $_POST['pausetime'];
	$processtime = $_POST['processtime'];
	$header = "Edit Ping Target #" . $id;
	
	if (filter_var($ip, FILTER_VALIDATE_IP) === false) 
	{
    $text = $ip." is NOT a valid IP address!";
    $alert_flag = "2";
    goto noSave;
	} 
	
	$query = sprintf("UPDATE ping_targets SET ip='%s', notes='%s', en='%s', HI_alert_cmds='%s', LO_alert_cmds='%s', HI_script_cmds='%s', LO_script_cmds='%s', hi_flap='%s', RunUpPingFile='%s', RunDownPingFile='%s', howmanytimes='%s', pausetime='%s', processtime='%s' WHERE id=%s;",$ip, $notes, $en, $HI_alert_cmds,$LO_alert_cmds,$HI_script_cmds,$LO_script_cmds,$hi_flap,$RunUpPingFile,$RunDownPingFile, $howmanytimes, $pausetime, $processtime, $id);
	
	//echo $query;
	$result  = $dbh->exec($query); 
	system("kill -HUP `cat /var/run/rmspingd.pid`");
	header("Location: setup_ping.php?action=edit&success=yes&id=".$id);
	
	noSave:    
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
			SetContext('ping');
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

<?php left_nav("setup"); ?>
<script language="javascript" type="text/javascript">
	SetContext('ping');
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
  	<form name='Ping' action='setup_ping_edit.php' method='post' class="form-horizontal" onsubmit="selectAllOptions('HI_alert_delcmd');selectAllOptions('LO_alert_delcmd');selectAllOptions('HI_script_delcmd');selectAllOptions('LO_script_delcmd');">  	
    	<fieldset>
  			<div class="row">
    			<div class="col-sm-12">
    		  	<div class="hpanel3">
    		  	  <div class="panel-body" style="text-align:left; background:#F1F3F6;border:none;">
    				  	<legend><img src="images/ping2.gif"> <?php echo $header; ?></legend> 

    				  	<div class="form-group"><label class="col-sm-1 control-label" style="text-align:left; max-width:140px; min-width:140px">Ping Target:</label>
              		<div class="col-sm-12" style="max-width:160px">
              			<input type="text" class="form-control" name='ip' value='<?php echo $ip; ?>' required />
              		</div>
              	</div>
    				  	
    				  	<div class="form-group"><label class="col-sm-1 control-label" style="text-align:left; max-width:140px; min-width:140px">Description:</label>
              		<div class="col-sm-12" style="max-width:300px">
              			<input type="text" class="form-control" name='notes' value='<?php echo $notes; ?>' required />
              		</div>
              	</div>
    				  	
    				  	<div class="table-responsive">
    				   		<table width="100%" border="1" class="table table-striped table-condensed">
    				   			<thead>
    				   				<tr>
    				   					<th width="10%" style="background:#D6DFF7;">
    				   						<div style="text-align:center;color:black">Enabled</div>
    				   					</th>
    				   					<th width="40%" style="background:#FF4040;">
    				   						<div style="text-align:center;color:black">Ping Target Dead Trigger</div>
    				   					</th>
    				   					<th width="40%" style="background:#40FF40;">
    				   						<div style="text-align:center;color:black">Ping Target Alive Trigger</div>
    				   					</th>
    				   				</tr>
    				   			</thead>
    				   			<tbody>
    				   				<tr>
    				   					<td style="vertical-align:middle">
    				   						<?php
    				   							if($en == "on")
    				   							{
    				   								$active = "checked";
    				   							}
    				   							else
    				   							{
    				   								$active = " ";
    				   							}
    				   						?>
    				   						
    				   					
    				   							<div class="checkbox checkbox-success"  style="text-align:center">
                            	<input type="checkbox" id="en" name="en" value="1" <?php echo $active; ?>> />
                              <label for="en"></label>
                            </div>
    				   						
    				   					</td>
    				   					<td>
    				   						<div style="text-align:center;">
    				   							<strong style="font-size: 15px;color:blue;">These events will fire when the Ping Target is unreachable.</strong>
    				   						</div>
    				   						<div class="table-responsive">
    				   							<table class="table table-condensed table-hover">
															<tr>
																<td>
    				   										&nbsp;<strong style="font-size: 15px;">Consecutive timeouts before any action is taken:</strong>
    				   										<select class="form-control input-sm" style="min-width:120px;max-width:120px;" name="hi_flap">
    				   										<?php
    				   											for($ii=3; $ii<60; $ii++)	{	if($hi_flap==$ii) {$chan = "selected";} else {$chan = " ";} 
    				   											echo "<option " , $chan . " value='" . $ii . "'>" . $ii . " Timeouts</option>";	}
																		for($ii=1; $ii<60; $ii++)	{	if($hi_flap==($ii*60)) 		{$chan = "selected";} else {$chan = " ";} echo"<option " . $chan . " value='" . $ii*60 . "'>" . $ii . " Minute Timeout</option>";	}
																		for($ii=1; $ii<25; $ii++)	{ if($hi_flap==($ii*60*60)) {$chan = "selected";} else {$chan = " ";} echo"<option " . $chan . " value='" . $ii*60*60 . "'>" . $ii . " Hour Timeout</option>";	}
    				   										?>
    				   										</select>
    				   										
    				   									</td>
    				   								</tr>
    				   								<tr>
    				   									<td>
    				   										<div class="radio radio-success">
              				 							<input id="howmanytimes" type="radio" name="howmanytimes" value="0"
              				 							<?php
    				   												if($howmanytimes == 0) echo" checked";
    				   											?>
    				   											/>
                      							<label for="howmanytimes"></label>     
                    							</div>
    				   										 <strong style="font-size: 15px;">Do the actions below (Alerts, Scripts, or Execute File) only one time.</strong>
																</td>
															</tr>
															<tr>
																<td>
																	<div class="radio radio-primary">
              				 							<input id="howmanytimes" type="radio" name="howmanytimes" value="1"
              				 							<?php
    				   												if($howmanytimes == 1) echo" checked";
    				   											?>
    				   											/>
                      							<label for="howmanytimes"></label>     
                    							</div>
                    							 <strong style="font-size: 15px;">Do the actions below, then wait for</strong> 
																	<select class="form-control input-sm" style="min-width:120px;max-width:120px;" name="pausetime" >
																		<option value="1">1 Second</option>
																		<?php
																		for($ii=2; $ii<60; $ii++)	{	if($pausetime==$ii) {$chan = "selected";} else {$chan = " ";} echo"<option " . $chan . " value='" . $ii . "'>" . $ii . " Seconds</option>";	}
																		$ii=1; if($pausetime==($ii*60)) {$chan = "selected";} else {$chan = " ";} echo "<option " . $chan . " value='" . $ii*60 . "'>" . $ii . " Minute</option>";
																		for($ii=2; $ii<60; $ii++)	{	if($pausetime==($ii*60)) {$chan = "selected";} else {$chan = " ";} echo "<option " . $chan . " value='" . $ii*60 . "'>" . $ii . " Minutes</option>";	}
																		$ii=1;	if($pausetime==($ii*60*60)) {$chan = "selected";} else {$chan = " ";} echo "<option " . $chan . " value='" . $ii*60*60 . "'>" . $ii . " Hour</option>";
																		for($ii=2; $ii<25; $ii++)	{ if($pausetime==($ii*60*60)) {$chan = "selected";} else {$chan = " ";} echo "<option " . $chan . " value='" . $ii*60*60 . "'>" . $ii . " Hours</option>";	}
																		?>
    				   										</select><br>
																	&nbsp;<strong style="font-size: 15px;">After that, reload the ping timeout counter and start pinging again.
																	&nbsp;Repeat the entire process</strong>
    				   										<select class="form-control input-sm" style="min-width:120px;max-width:120px;" name='processtime'>
    				   										<?php
    				   											if($processtime==0)
																		{
																			echo "<option selected value='0'>Forever</option>";
																		}
																		else
																		{
																			echo "<option value='0'>Forever</option>";
																		}
																		
																		if($processtime==1)
																		{
																			echo "<option selected value='1'>1 Time</option>";
																		}
																		else
																		{
																			echo "<option value='1'>1 Time</option>";
																		}
    				   											for($ii=2; $ii<1001; $ii++)	{	if($processtime==$ii) {$chan = "selected";} else {$chan = " ";} echo "<option " . $chan . " value='" . $ii . "'>" . $ii . " Times</option>";	}
    				   										?>
    				   										
    				   										</select>
																</td>
															</tr>
														</table>
													</div>
												</td>
												<td style="vertical-align:middle">
													<div style="text-align:center;"><strong style="font-size: 15px;color:blue;">These events will fire when the Ping Target is reachable again.</strong></div>	
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
												<div style="text-align:center;"><a href="#hi3"></a><a href="fe.php"><strong><u class="dotted">File</u></strong></a></center></div>
												</td>
											
												<td>
													<div><label class="col-sm-2 control-label">Execute File:</label>
              							<div class="col-sm-8">
              								<input type="text" class="form-control" name='RunUpPingFile' value='<?php echo $RunUpPingFile; ?>' />
              							</div>
              						</div>
												</td>
											
												<td>
													<div><label class="col-sm-2 control-label">Execute File:</label>
              							<div class="col-sm-8">
              								<input type="text" class="form-control" name='RunDownPingFile' value='<?php echo $RunDownPingFile; ?>' />
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









 
















