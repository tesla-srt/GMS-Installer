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
	$type = "button";
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
		
/////////////////////////////////////////////////////////////////
//                                                             //
//                    GET PROCESSING                           //
//                                                             //
/////////////////////////////////////////////////////////////////


	
if(isset($_GET['btn']))
	{
			$id = $_GET['btn'];
			$query = sprintf("SELECT * FROM io WHERE id='%d' and type='btn'",$id);
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
				$header = "Edit Button #" . $id;
			}
			$query = sprintf("SELECT * FROM btn_trig_supress WHERE id='%d'",$id);
			$result  = $dbh->query($query);
			foreach($result as $row)
			{
				$suppressed = $row['supress'];
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
	$name = trim($_POST['name']);
	$notes = trim($_POST['notes']);
	if($name == "")
	{
		$text = "Enter a name for this button!";
		$alert_flag = "2";
		goto noSave;
	}
	
	if($notes == "")
	{
		$text = "Enter notes for this button!";
		$alert_flag = "2";
		goto noSave;
	}
	
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
	
	$hi_flap = $_POST['hi_flap'];
	$lo_flap = $_POST['lo_flap'];
	$RunHiIoFile = $_POST['RunHiIoFile'];
	$RunLowIoFile = $_POST['RunLowIoFile'];

	$query = sprintf("UPDATE io SET name='%s', notes='%s', en='%s', HI_alert_cmds='%s', HI_script_cmds='%s', LO_alert_cmds='%s', LO_script_cmds='%s', hi_flap='%s', lo_flap='%s', RunHiIoFile='%s', RunLowIoFile='%s' WHERE id='%d' AND type='btn';", $name, $notes, $en, $HI_alert_cmds, $HI_script_cmds, $LO_alert_cmds, $LO_script_cmds, $hi_flap, $lo_flap, $RunHiIoFile, $RunLowIoFile, $id);
	$result  = $dbh->exec($query); 

	$query = sprintf("UPDATE btn_trig_supress SET supress='%s' WHERE id='%d';", $suppressed, $id);
	$result  = $dbh->exec($query);
	
	restart_some_services();
	$alert_flag = "1";
	//header("Location: ios.php?action=edit&success=yes&id=".$id."&type=GPIO");
	
	noSave:    
}



if($id == "1")
{
	$btn_state = 			exec("readiobits c | grep 'Port C bit 11' | cut  -f3 | cut -d' ' -f3");
}

$header = "Edit Button #" . $id;

				
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
		<script src="javascript/jquery-ui.min.js"></script>
		<script src="javascript/bootstrap.min.js"></script>
		<script src="javascript/sweetalert.min.js"></script>
		<script src="javascript/conhelp.js"></script>
		<script src="javascript/ethertek.js"></script>
		<script language="javascript" type="text/javascript">
			SetContext('ios');
		</script>
		
		<script language="javascript" type="text/javascript">
		function display_io ()
		{
			var myRandom = parseInt(Math.random()*999999999);
			$.getJSON('sdserver.php?element=ios&rand=' + myRandom,
			    function(data)
			    {
							setTimeout (display_io, 1000);
			        
			          if (data.ios.btn1 == 1)
			          {
			          	$('#btn1').replaceWith("<span id='btn1' style='color:green'>UP</span>");
			          }
			          else
			          {
			            $('#btn1').replaceWith("<span id='btn1' style='color:red'>DOWN</span>"); 
			          }
								
			    }
			);
		}

	
		display_io ();
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
  	<form name='IOS' action='setup_button.php' method='post' class="form-horizontal" onsubmit="selectAllOptions('HI_alert_delcmd');selectAllOptions('LO_alert_delcmd');selectAllOptions('HI_script_delcmd');selectAllOptions('LO_script_delcmd');">  	
    	<fieldset>
  			<div class="row">
    			<div class="col-sm-12">
    		  	<div class="hpanel3">
    		  	  <div class="panel-body" style="text-align:left; background:#F1F3F6;border:none;">
    				  	<legend><img src="images/micro_button1.gif"> <?php echo $header; ?></legend> 

    				  	<div class="form-group"><label class="col-sm-1 control-label" style="text-align:left; min-width:120px; max-width:120px">Button Name:</label>
              		<div class="col-sm-6" style="min-width:250px; max-width:350px">
              			<input type="text" class="form-control" name='name' value='<?php echo $name; ?>' required />
              		</div>
              	</div>
    				  	
    				  	<div class="form-group"><label class="col-sm-1 control-label" style="text-align:left; min-width:120px; max-width:120px">Button Notes:</label>
              		<div class="col-sm-3" style="min-width:350px; max-width:350px">
              			<textarea  rows="3" cols="60" class="form-control" name='notes' required><?php echo $notes; ?></textarea>
              		</div>
              	</div>
              	
              	<div class="form-group"><label class="col-sm-1 control-label" style="text-align:left; min-width:120px; max-width:120px"></label>
              		<div class="col-sm-3" style="min-width:350px; max-width:350px">
              			<div class="checkbox checkbox-danger" onMouseOver="mouse_move(&#039;sd_suppress&#039;);" onMouseOut="mouse_move();">
              				<input type="checkbox" id="supress" name="supress" <?php echo $suppressed; ?> />
    		        			<label for="suppress" onMouseOver="mouse_move(&#039;sd_suppress&#039;);" onMouseOut="mouse_move();">Suppress Trigger Actions on Boot?</label>
              			</div>
              		</div>
              	</div>
              	<?php
              	
              		if($btn_state == "1")
                  {
                  	$state = '<span id="btn'.$id.'" style="color:green">UP</span>';
                  }
                  else
                  {
                  	$state = '<span id="btn'.$id.'" style="color:red">DOWN</span>';
                  }
                  
                  echo '<p>';
        					echo '	<b>Button state: '. $state . '</b>';
        					echo '</p>';
        					echo '<div class="form-group">';
        					echo '	<div class="col-sm-12" style="min-width:250px; max-width:250px">';
        					echo '		<button name="save_btn" class="btn btn-success" type="submit" onMouseOver="mouse_move(&#039;b_apply&#039;);" onMouseOut="mouse_move();"><i class="fa fa-check" ></i> Save</button>';
        					echo '		<button name="cancel_btn" class="btn btn-primary" type="submit" onMouseOver="mouse_move(&#039;b_cancel&#039;);" onMouseOut="mouse_move();" formnovalidate><i class="fa fa-times"></i> Cancel</button>';
        					echo '	</div>';
        					echo '</div>';
        					
    				  		echo '<div class="table-responsive">';
    				   		echo '	<table width="100%" border="1" class="table table-striped table-condensed">';
    				   		echo '		<thead>';
    				   		echo '			<tr>';
    				   		echo '			<th width="10%" style="background:#D6DFF7;">';
    				   		echo '				<div style="text-align:center;color:black">Enabled</div>';
    				   		echo '			</th>';
    				   		echo '			<th width="40%" style="background:red">';
    				   		echo '				<div style="text-align:center;color:black">Button '.$id.' DOWN Trigger</div>';
    				   		echo '			</th>';
    				   		echo '			<th width="40%" style="background:#40FF40">';
    				   		echo '				<div style="text-align:center;color:black">Button '.$id.' UP Trigger</div>';
    				   		echo '			</th>';
    				   		echo '		</tr>';
    				   		echo '	</thead>';
    				   		echo '	<tbody>';
    				   		echo '		<tr>';
    				   		echo '			<td style="vertical-align:middle">';			
    				   		if($en == "1")
    				   		{
    				   			$active = "checked";
    				   		}
    				   		else
    				   		{
    				   			$active = " ";
    				   		}
    				   		echo '	<div class="checkbox checkbox-success"  style="text-align:center">';
                  echo '		<input type="checkbox" id="en" name="en" value="1" onMouseOver="mouse_move(&#039;sd_enabled&#039;);" onMouseOut="mouse_move();" '.$active.' />';
                  echo '	  	<label for="en"></label>';
                  echo '	 </div>';
    				   					
    				   		echo '		</td>';
    				   		echo '		<td>';
    				   		echo '			<div style="text-align:center;">';
    				   		echo '				<strong style="font-size: 15px;color:blue;">These events will fire when BUTTON '.$id.' is down.</strong>';
    				   		echo '			</div>';
    				   		
    				   		echo '			<div style="text-align:center; margin-top: 15px;">';
    				   		echo '				<strong style="font-size: 15px;">Execute the actions below every:</strong>';
    				   		echo '			</div>';
    				   		echo '			<div style="text-align:center;">';
    				   		echo '				<select class="form-control input-sm" style="max-width:105px; min-width:105px; display: block; margin: 0 auto;" name="hi_flap" >';
									echo '					<option value="0">One Shot</option>';
															
															$ii=1;	if($hi_flap==$ii) {$chan=sprintf("selected");} else {$chan=sprintf(" ");} echo"<option ".$chan." value=".$ii.">".$ii." Second</option>";	
															for($ii=2; $ii<60; $ii++)	{	if($hi_flap==$ii) {$chan = "selected";} else {$chan = " ";} echo"<option " . $chan . " value='" . $ii . "'>" . $ii . " Seconds</option>";	}
															$ii=1; if($hi_flap==($ii*60)) {$chan = "selected";} else {$chan = " ";} echo "<option " . $chan . " value='" . $ii*60 . "'>" . $ii . " Minute</option>";
															for($ii=2; $ii<60; $ii++)	{	if($hi_flap==($ii*60)) {$chan = "selected";} else {$chan = " ";} echo "<option " . $chan . " value='" . $ii*60 . "'>" . $ii . " Minutes</option>";	}
															$ii=1;	if($hi_flap==($ii*60*60)) {$chan = "selected";} else {$chan = " ";} echo "<option " . $chan . " value='" . $ii*60*60 . "'>" . $ii . " Hour</option>";
															for($ii=2; $ii<25; $ii++)	{ if($hi_flap==($ii*60*60)) {$chan = "selected";} else {$chan = " ";} echo "<option " . $chan . " value='" . $ii*60*60 . "'>" . $ii . " Hours</option>";	}
															
		    				  echo '				</select>';
		    				  echo '			</div>';			
												
									echo '				</td>';
									echo '				<td style="vertical-align:middle">';
									echo '					<div style="text-align:center;"><strong style="font-size: 15px;color:blue;">These events will fire when BUTTON '.$id.' is up.</strong></div>';	
									
									echo '					<div style="text-align:center; margin-top: 15px;">';
    				   		echo '						<strong style="font-size: 15px;">Execute the actions below every:</strong>';
    				   		echo '					</div>';
    				   		echo '					<div style="text-align:center;">';
    				   		echo '						<select class="form-control input-sm" style="max-width:105px; min-width:105px; display: block; margin: 0 auto;" name="lo_flap" >';
									echo '						<option value="0">One Shot</option>';
															
															$ii=1;	if($lo_flap==$ii) {$chan=sprintf("selected");} else {$chan=sprintf(" ");} echo"<option ".$chan." value=".$ii.">".$ii." Second</option>";	
															for($ii=2; $ii<60; $ii++)	{	if($lo_flap==$ii) {$chan = "selected";} else {$chan = " ";} echo"<option " . $chan . " value='" . $ii . "'>" . $ii . " Seconds</option>";	}
															$ii=1; if($lo_flap==($ii*60)) {$chan = "selected";} else {$chan = " ";} echo "<option " . $chan . " value='" . $ii*60 . "'>" . $ii . " Minute</option>";
															for($ii=2; $ii<60; $ii++)	{	if($lo_flap==($ii*60)) {$chan = "selected";} else {$chan = " ";} echo "<option " . $chan . " value='" . $ii*60 . "'>" . $ii . " Minutes</option>";	}
															$ii=1;	if($lo_flap==($ii*60*60)) {$chan = "selected";} else {$chan = " ";} echo "<option " . $chan . " value='" . $ii*60*60 . "'>" . $ii . " Hour</option>";
															for($ii=2; $ii<25; $ii++)	{ if($lo_flap==($ii*60*60)) {$chan = "selected";} else {$chan = " ";} echo "<option " . $chan . " value='" . $ii*60*60 . "'>" . $ii . " Hours</option>";	}
															
		    				  echo '					</select>';
		    				  echo '				</div>';
									
									echo '				</td>';
									echo '			</tr>';
										
									echo '			<tr>';
    				   		echo '				<td style="vertical-align:middle">';
    				   		echo '					<div style="text-align:center;"><a name="#hi1"></a><br><a href="setup_notifications.php"><strong><u class="dotted">Alerts</u></strong></a></div>';
    				   		echo '				</td>';
    				   		echo '				<td>';		
    				   		selectbox("HI", "alert", $HI_alert_cmds);
    				   		echo '				</td>';		
    				   		echo '				<td>';						
    				   		selectbox("LO", "alert", $LO_alert_cmds);
    				   		echo '				</td>';						
    				   		echo '			</tr>';
    				   			
    				   		echo '			<tr>';				
    				   		echo '				<td style="vertical-align:middle">';
    				   		echo '					<div style="text-align:center;"><a name="#lo1"></a><br><a href="setup_scripts.php?"><strong><u class="dotted">Scripts</u></strong></a></div>';
    				   		echo '				</td>';
    				   		echo '				<td>';		
    				   		selectbox("HI", "script", $HI_script_cmds);
    				   		echo '				</td>';			
    				   		echo '				<td>';						
    				   		selectbox("LO", "script", $LO_script_cmds);
    				   		echo '				</td>';									
    				   		echo '			</tr>';
    				   			
    				   		echo '			<tr>';
									echo '				<td style="vertical-align:middle">';
									echo '				<div style="text-align:center;"><a href="#hi3"></a><a href="setup_file_explorer.php"><strong><u class="dotted">File</u></strong></a></center></div>';
									echo '				</td>';
										
									echo '				<td>';
									echo '					<div><label class="col-sm-2 control-label">Execute File:</label>';
              		echo '						<div class="col-sm-8">';
              		echo '							<input type="text" class="form-control" name="RunHiIoFile" value="'.$RunHiIoFile.'" />';
              		echo '						</div>';
              		echo '					</div>';
									echo '				</td>';
										
									echo '				<td>';
									echo '					<div><label class="col-sm-2 control-label">Execute File:</label>';
              		echo '						<div class="col-sm-8">';
              		echo '							<input type="text" class="form-control" name="RunLowIoFile" value="'.$RunLowIoFile.'" />';
              		echo '						</div>';
              		echo '					</div>';
									echo '				</td>';
									echo '			</tr>';
    				   		echo '		</tbody>';
  								echo '	</table>';    	    
    		  	  		echo '</div> <!-- END TABLE RESPONSIVE -->';
              		
              		echo '<div class="form-group">';
        					echo '	<div class="col-sm-12">';
        					echo '		<input type="hidden" name="id" value="'.$id.'">';
        					echo '		<button name="save_btn" class="btn btn-success" type="submit" onMouseOver="mouse_move(&#039;b_apply&#039;);" onMouseOut="mouse_move();"><i class="fa fa-check" ></i> Save</button>';
        					echo '		<button name="cancel_btn" class="btn btn-primary" type="submit" onMouseOver="mouse_move(&#039;b_cancel&#039;);" onMouseOut="mouse_move();" formnovalidate><i class="fa fa-times"></i> Cancel</button>';
        					echo '	</div>';
        					echo '</div>';
    				  	?>
    				  	
    				  	
    		  	  	
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
echo"  text: 'Settings Saved!',";
echo"  type: 'success',";
echo"  showConfirmButton: false,";
echo"  timer: 2000";
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

echo "</body>";
echo "</html>";

?>
