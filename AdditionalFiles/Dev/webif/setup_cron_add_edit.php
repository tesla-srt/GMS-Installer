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
	$alert_flag = "0";
	$id = "0";
	$cron = "";
	$mins = "";
	$hours = "";
	$days = "";
	$months = "";
	$wdays = "";
	$cmds = "";
	$active_yes = "";
	$active_no = "";
	$myMins = "";
	$myHours = "";
	$myDays = "";
	$myMonths = "";
	$myWdays = "";
	$header = "";
	$cron_tab = "";
	
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


	
if(isset ($_GET['command']))
{
	$command = $_GET['command'];
	if($command == "add")
	{
		$header = "Add New Cron Job";
		$active_yes = " ";
    $active_no = "checked";
    $mins = $hours = $days = $months = $wdays = "*";
    $id = "0";
	}
	else if($command == "edit")
	{
		$cron = $_GET['cron'];
		$id = $_GET['id'];
		$header = "Edit Existing Cron Job # " . $id;
		$myFile = "/etc/crontabs/root";
		$lines = file($myFile); //file in to an array
    $rows = count($lines); //how many lines if file
    
    $myid = $id - 1;				   					
    list($mins, $hours, $days, $months, $wdays, $cmds) = explode(' ', $lines[$myid], 6);
    if($mins[0] == "#")
    {
    	$active_yes = " ";
    	$active_no = "checked";
    	$mins = substr($mins, 1); //remove #
    }
    else
    {
    	$active_yes = "checked";
    	$active_no = " ";
    }
	}
}




/////////////////////////////////////////////////////////////////
//                                                             //
//                    POST PROCESSING                          //
//                                                             //
/////////////////////////////////////////////////////////////////

// Strip illegal characters from $_POST data
//$input_arr = array();
//foreach ($_POST as $key => $input_arr)
//{
//  	$_POST[$key] = preg_replace("/[^a-zA-Z0-9\s!@#$%&*()_\-=+?.,:\/]/", "", $input_arr);
//}

// Cancel Button	was clicked
if(isset ($_POST['cancel_btn']))
{
	header("Location: setup_cron.php");
}

// Save Button	was clicked
if(isset ($_POST['save_btn']))
{
	$id = $_POST['id'];
	$active = $_POST['active'];
	$cmds =  $_POST['cmds'];
	$all_mins = $_POST['all_mins'];
	$all_hours = $_POST['all_hours'];
	$all_days = $_POST['all_days'];
	$all_months = $_POST['all_months'];
	$all_weekdays = $_POST['all_weekdays'];
	
	if($all_mins == "1")
	{ 
		$mins = "*"; 
	}
	else if(isset($_POST['mins']))
	{
		foreach ($_POST['mins'] as $minsBox)
		{
    	$myMins = $myMins . $minsBox . ",";
  	} 
  	$mins = rtrim($myMins, ",");
	}
	
	if($all_hours == "1")
	{ 
		$hours = "*"; 
	}
	else if(isset($_POST['hours']))
	{
		foreach ($_POST['hours'] as $hoursBox)
		{
    	$myHours = $myHours . $hoursBox . ",";
  	} 
  	$hours = rtrim($myHours, ",");
	}
	
	if($all_days == "1")
	{ 
		$days = "*"; 
	}
  else if(isset($_POST['days']))
	{
		foreach ($_POST['days'] as $daysBox)
		{
    	$myDays = $myDays . $daysBox . ",";
  	} 
  	$days = rtrim($myDays, ",");
	}
  
  if($all_months == "1")
	{ 
		$months = "*"; 
	}
  else if(isset($_POST['months']))
	{
		foreach ($_POST['months'] as $monthsBox)
		{
    	$myMonths = $myMonths . $monthsBox . ",";
  	} 
  	$months = rtrim($myMonths, ",");
	}
  
  if($all_weekdays == "1")
	{ 
		$wdays = "*"; 
	}
  else if(isset($_POST['weekdays']))
	{
		foreach ($_POST['weekdays'] as $wdaysBox)
		{
    	$myWdays = $myWdays . $wdaysBox . ",";
  	} 
  	$wdays = rtrim($myWdays, ",");
	}
  
  if($active == 1)
  {
  	// Active Cron Job
  	$cron_tab = "";
  	$active_yes = "checked";
    $active_no = " ";
  }
  else
  {
  	// Inactive Cron Job
  	$cron_tab = "#";
  	$active_yes = " ";
    $active_no = "checked";
  }
  
  if(strlen($cmds) == 0){ $text = "Commands box must not be empty!"; $alert_flag = "2"; goto noSave;}
  if(strlen($mins) == 0 && $all_mins == 0){ $text = "Select some Minutes or ALL!"; $alert_flag = "2"; goto noSave;}
  if(strlen($hours) == 0 && $all_hours == 0){ $text = "Select some Hours or ALL!"; $alert_flag = "2"; goto noSave;}
  if(strlen($days) == 0 && $all_days == 0){ $text = "Select some Days or ALL!"; $alert_flag = "2"; goto noSave;}
  if(strlen($months) == 0 && $all_months == 0){ $text = "Select some Months or ALL!"; $alert_flag = "2"; goto noSave;}
  if(strlen($wdays) == 0 && $all_weekdays == 0){ $text = "Select some Weekdays or ALL!"; $alert_flag = "2"; goto noSave;}
  
  
  $cron_tab = $cron_tab . $mins ." ". $hours ." ". $days ." ". $months ." ". $wdays ." ". $cmds;
  $cron_tab = rtrim($cron_tab,"\n\r");
  
  if($id == 0)
  {
  	//Add New Cron Tab to /etc/crontabs/root
  	$command = sprintf("echo '%s' >> /etc/crontabs/root", $cron_tab);
		system($command);
		system("/etc/init.scripts/S93crond restart  2>/dev/null 1>/dev/null");
		header("Location: setup_cron.php?action=add&success=yes&id=".$id);
  }
  else
  {
  	//Edit existing Cron Tab
  	$cron_tab = $cron_tab . "\n";
  	$filename = "/etc/crontabs/root";
    $file_content = file($filename);
    $fp = fopen($filename, "w+"); 
    $file_content[$id-1] = $cron_tab;
    fwrite($fp, implode($file_content, ''));
    fclose($fp);
    system("/etc/init.scripts/S93crond restart  2>/dev/null 1>/dev/null");
    header("Location: setup_cron.php?action=edit&success=yes&id=".$id);
  }
  
//  echo "Cron Tab: ".$cron_tab."<br>";
//  echo "ID: ".$id."<br>";
//  echo "Mins: ".$mins."<br>";  
//  echo "Hours: ".$hours."<br>"; 
//  echo "Days: ".$days."<br>";  
//  echo "Months: ".$months."<br>";  
//  echo "Wdays: ".$wdays."<br>";  
//  echo "CMDS: ".$cmds."<br>";
//	echo "Active: ".$active."<br>";
	
	
	
	
	noSave:
	
	//header("Location: setup_cron.php");
	    
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
		<script src="javascript/jquery-ui.min.js"></script>
		<script src="javascript/bootstrap.min.js"></script>
		<script src="javascript/sweetalert.min.js"></script>
		<script src="javascript/conhelp.js"></script>
		<script src="javascript/ethertek.js"></script>
		<script language="javascript" type="text/javascript">
			SetContext('cron');
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
	SetContext('cron');
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
  	

  	<form name='Cron' action='setup_cron_add_edit.php' method='post' class="form-horizontal">  	
    	<fieldset>
  			<div class="row">
    			<div class="col-sm-12">
    		  	<div class="hpanel3">
    		  	  <div class="panel-body" style="text-align:left; background:#F1F3F6;border:none;">
    				  	<legend><img src="images/schedule_add.gif"> <?php echo $header; ?></legend> 
      	    		<div class="form-group">
              		<div class="col-sm-12">
              			<b>Commands to Execute:</b><input type="text" class="form-control" name="cmds" value="<?php echo $cmds; ?>" required />
              		</div>
              	</div>
    				  	
    				  	<div class="form-group">
              		<div class="col-sm-12">
              			<b>Active </b>
              			
              			<div class="radio radio-success">
              				 <input id='active1' type='radio' name='active' value='1' <?php echo $active_yes; ?> required />
                      <label for="active1">Yes</label>     
                    </div>
              			
              			<div class="radio radio-danger">
              				 <input id='active2' type='radio' name='active' value='0' <?php echo $active_no; ?> />
                      <label for="active2">No</label>     
                    </div>

              		</div>
              	</div>
    				  	
    				  	<hr style="border-top: 2px solid #C2C2C2;">
    				  	
    				  	<h5><b>Choose when to run the above command</b></h5>
    				  	
    				  	<div class="table-responsive">
    				   		<table width="100%" class="table table-striped table-condensed table-hover">
    				   			<thead>
    				   				<tr>
    				   					<th width="20%" style="background:#D6DFF7; border: 1px solid white;">
    				   						<div style="text-align:left">Minutes</div>
    				   					</th>
    				   					<th width="20%" style="background:#D6DFF7; border: 1px solid white;">
    				   						<div style="text-align:left">Hours</div>
    				   					</th>
    				   					<th width="20%" style="background:#D6DFF7; border: 1px solid white;">
    				   						<div>Days</div>
    				   					</th>
    				   					<th width="20%" style="background:#D6DFF7; border: 1px solid white;">
    				   						<div style="text-align:left">Months</div>
    				   					</th>
    				   					<th width="20%" style="background:#D6DFF7; border: 1px solid white;">
    				   						<div style="text-align:left">Weekdays</div>
    				   					</th>
    				   				</tr>
    				   			</thead>
    				   			<tbody>
    				   				<tr>
    				   					<td style="vertical-align: top;">
    				   						<?php 
    				   							if($mins == "*")
    				   							{
    				   								$sd_buf1 = "checked";
    				   								$sd_buf2 = " ";
    				   							}
    				   							else
    				   							{
    				   								$sd_buf1 = " ";
    				   								$sd_buf2 = "checked";
    				   							}
    				   						?>
    				   						
    				   						<div class="radio radio-success">
              				 			<input id='all_mins' type='radio' name='all_mins' value="1" <?php echo $sd_buf1; ?> required />
                      			<label for="all_mins">All</label>     
                    			</div>
              			
              						<div class="radio radio-primary">
              				 			<input id='all_mins' type='radio' name='all_mins' value="0" <?php echo $sd_buf2; ?> />
                      			<label for="all_mins">Selected</label>     
                    			</div>
    				   						
    				   						<table>
    				   							<tr>
    				   								<td style="vertical-align: top;">
    				   									<select multiple size="12" name="mins[]">
    				   										<?php
    				   											$j = 0;
    				   											if($mins[0] == "*")
    				   											{
    				   												$count = 0;
    				   											}
    				   											else
    				   											{
    				   												$bmins = explode(",", $mins);
    				   												$count = count($bmins);
    				   											}
    				   											
																		for($ii=0; $ii<60; $ii++)
																			{
																				$bsel = " ";
																				$btmp = $ii;
																				if($j < $count)		{		if($bmins[$j] == $btmp)	{ $bsel = "selected"; $j++; }		}
																				echo "<option value='" . $ii . "' " . $bsel . ">" . $ii;
																				if($ii==11 || $ii==23 || $ii==35 || $ii==47)	{ echo"</select></td><td valign='top'><select multiple size='12' name='mins[]'>";	}
																			}	
    				   										?>
    				   									</select>
    				   								</td>
    				   							</tr>
    				   						</table>
    				   						<?php 
    				   							if($hours == "*")
    				   							{
    				   								$sd_buf1 = "checked";
    				   								$sd_buf2 = " ";
    				   							}
    				   							else
    				   							{
    				   								$sd_buf1 = " ";
    				   								$sd_buf2 = "checked";
    				   							}
    				   						?>
    				   						<td style="vertical-align: top;">
    				   							
    				   							<div class="radio radio-success">
              				 				<input id='all_hours' type='radio' name='all_hours' value="1" <?php echo $sd_buf1; ?> required />
                      				<label for="all_hours">All</label>     
                    				</div>
              							<div class="radio radio-primary">
              				 				<input id='all_hours' type='radio' name='all_hours' value="0" <?php echo $sd_buf2; ?> />
                      				<label for="all_hours">Selected</label>     
                    				</div>
    				   							
    				   							<table>
    				   								<tr>
    				   									<td style="vertical-align: top;">
    				   										<select multiple size="12" name="hours[]">
    				   											<?php
    				   												$j=0;
    				   												if($hours[0] == "*")
    				   												{
    				   													$count = 0;
    				   												}
    				   												else
    				   												{
    				   													$bhours = explode(",", $hours);
    				   													$count = count($bhours);
    				   												}
                          	
																			for($ii=0; $ii<24; $ii++)
																				{
																					$bsel = " ";
																					$btmp = $ii;
																					if($j < $count)		{		if($bhours[$j] == $btmp)	{ $bsel = "selected"; $j++; }		}
																					echo "<option value='" . $ii . "' " . $bsel . ">" . $ii;
																					if($ii==11)	{ echo"</select></td><td valign='top'><select multiple size='12' name='hours[]'>";	}
																				}	
    				   											?>
    				   										</select>
    				   									</td>
    				   								</tr>
    				   							</table>
    				   						<?php 
    				   							if($days == "*")
    				   							{
    				   								$sd_buf1 = "checked";
    				   								$sd_buf2 = " ";
    				   							}
    				   							else
    				   							{
    				   								$sd_buf1 = " ";
    				   								$sd_buf2 = "checked";
    				   							}
    				   						?>
    				   					</td>	
    				   					<td style="vertical-align: top;">
    				   						
    				   						<div class="radio radio-success">
              				 			<input id='all_days' type='radio' name='all_days' value="1" <?php echo $sd_buf1; ?> required />
                      			<label for="all_days">All</label>     
                    			</div>
              						<div class="radio radio-primary">
              				 			<input id='all_days' type='radio' name='all_days' value="0" <?php echo $sd_buf2; ?> />
                      			<label for="all_days">Selected</label>     
                    			</div>
    				   						
    				   						<table>
    				   							<tr>
    				   								<td style="vertical-align: top;">
    				   									<select multiple size="12" name="days[]">
    				   										<?php
    				   											$j=0;
    				   											if($days[0] == "*")
    				   											{
    				   												$count = 0;
    				   											}
    				   											else
    				   											{
    				   												$bdays = explode(",", $days);
    				   												$count = count($bdays);
    				   											}

																		for($ii=1; $ii<32; $ii++)
																			{
																				$bsel = " ";
																				$btmp = $ii;
																				if($j < $count)		{		if($bdays[$j] == $btmp)	{ $bsel = "selected"; $j++; }		}
																				echo "<option value='" . $ii . "' " . $bsel . ">" . $ii;
																				if($ii==12 || $ii==24)	{ echo"</select></td><td valign='top'><select multiple size='12' name='days[]'>";	}
																			}	
    				   										?>
    				   									</select>
    				   								</td>
    				   							</tr>
    				   						</table>
    				   						
    				   						<?php 
    				   							if($months == "*")
    				   							{
    				   								$sd_buf1 = "checked";
    				   								$sd_buf2 = " ";
    				   							}
    				   							else
    				   							{
    				   								$sd_buf1 = " ";
    				   								$sd_buf2 = "checked";
    				   							}
    				   						?>
    				   					</td>	
    				   					<td style="vertical-align: top;">
    				   						
    				   						<div class="radio radio-success">
              				 			<input id='all_months' type='radio' name='all_months' value="1" <?php echo $sd_buf1; ?> required />
                      			<label for="all_months">All</label>     
                    			</div>
              						<div class="radio radio-primary">
              				 			<input id='all_months' type='radio' name='all_months' value="0" <?php echo $sd_buf2; ?> />
                      			<label for="all_months">Selected</label>     
                    			</div>
    				   						
    				   						<table>
    				   							<tr>
    				   								<td style="vertical-align: top;">
    				   									<select multiple size="12" name="months[]">
    				   										<?php
    				   											$j=0;
    				   											if($months[0] == "*")
    				   											{
    				   												$count = 0;
    				   											}
    				   											else
    				   											{
    				   												$bmonths = explode(",", $months);
    				   												$count = count($bmonths);
    				   											}
	
																		for($ii=1; $ii<13; $ii++)
																			{
																				$bsel = " ";
																				$btmp = $ii;
																				if($j < $count)		{		if($bmonths[$j] == $btmp)	{ $bsel = "selected"; $j++; }		}
																				if($ii==1)		{	echo"<option value='" . $ii . "' " . $bsel . ">January";	}
																				if($ii==2)		{	echo"<option value='" . $ii . "' " . $bsel . ">February";	}
																				if($ii==3)		{	echo"<option value='" . $ii . "' " . $bsel . ">March";	}
																				if($ii==4)		{	echo"<option value='" . $ii . "' " . $bsel . ">April";	}
																				if($ii==5)		{	echo"<option value='" . $ii . "' " . $bsel . ">May";	}
																				if($ii==6)		{	echo"<option value='" . $ii . "' " . $bsel . ">June";	}
																				if($ii==7)		{	echo"<option value='" . $ii . "' " . $bsel . ">July";	}
																				if($ii==8)		{	echo"<option value='" . $ii . "' " . $bsel . ">August";	}
																				if($ii==9)		{	echo"<option value='" . $ii . "' " . $bsel . ">September";	}
																				if($ii==10)	{	echo"<option value='" . $ii . "' " . $bsel . ">October";	}
																				if($ii==11)	{	echo"<option value='" . $ii . "' " . $bsel . ">November";	}
																				if($ii==12)	{	echo"<option value='" . $ii . "' " . $bsel . ">December";	}
																			}	
    				   										?>
    				   									</select>
    				   								</td>
    				   							</tr>
    				   						</table>
    				   						<?php 
    				   							if($wdays == "*")
    				   							{
    				   								$sd_buf1 = "checked";
    				   								$sd_buf2 = " ";
    				   							}
    				   							else
    				   							{
    				   								$sd_buf1 = " ";
    				   								$sd_buf2 = "checked";
    				   							}
    				   						?>
    				   						
    				   					</td>	
    				   					<td style="vertical-align: top;">
    				   						
    				   						<div class="radio radio-success">
              				 			<input id='all_weekdays' type='radio' name='all_weekdays' value="1" <?php echo $sd_buf1; ?> required />
                      			<label for="all_weekdays">All</label>     
                    			</div>
              						<div class="radio radio-primary">
              				 			<input id='all_weekdays' type='radio' name='all_weekdays' value="0" <?php echo $sd_buf2; ?> />
                      			<label for="all_weekdays">Selected</label>     
                    			</div>
    				   						
    				   						<table>
    				   							<tr>
    				   								<td style="vertical-align: top;">
    				   									<select multiple size="12" name="weekdays[]">
    				   										<?php
    				   											$j=0;
    				   											if($wdays[0] == "*")
    				   											{
    				   												$count = 0;
    				   											}
    				   											else
    				   											{
    				   												$bwdays = explode(",", $wdays);
    				   												$count = count($bwdays);
    				   											}

																		for($ii=0; $ii<7; $ii++)
																			{
																				$bsel = " ";
																				$btmp = $ii;
																				if($j < $count)		{		if($bwdays[$j] == $btmp)	{ $bsel = "selected"; $j++; }		}
																				if($ii==0)		{	echo"<option value='" . $ii . "' " . $bsel . ">Sunday";	}
																				if($ii==1)		{	echo"<option value='" . $ii . "' " . $bsel . ">Monday";	}
																				if($ii==2)		{	echo"<option value='" . $ii . "' " . $bsel . ">Tuesday";	}
																				if($ii==3)		{	echo"<option value='" . $ii . "' " . $bsel . ">Wednesday";	}
																				if($ii==4)		{	echo"<option value='" . $ii . "' " . $bsel . ">Thursday";	}
																				if($ii==5)		{	echo"<option value='" . $ii . "' " . $bsel . ">Friday";	}
																				if($ii==6)		{	echo"<option value='" . $ii . "' " . $bsel . ">Saturday";	}
																			}	
    				   										?>
    				   									</select>
    				   								</td>
    				   							</tr>
    				   						</table>
    				   					</td>
    				   				</tr>
    				   				<tr style="background:#D6DFF7;">
    				   					<td colspan="5">Note: Ctrl-click (or command-click on the Mac) to select and de-select minutes, hours, days, months and weekdays.
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
if($alert_flag == "1")
{
echo"<script>";
echo"swal({";
echo"  title:'Success!',";
echo"  text: 'Cron Job Deleted!',";
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
	echo"	swal({";
	echo"		title: 'Delete Cron Job ID# " . $id . "<br>Are you really sure?',";
	echo"		type: 'warning',";
	echo"		showCancelButton: true,";
	echo"		html: true,";
	echo"		confirmButtonColor: '#DD6B55',";
	echo"		confirmButtonText: 'Yes, delete it!',";
	echo"		closeOnConfirm: false";
	echo"	},";
	echo"	function(){";
	echo"		window.location.href = 'setup_cron.php?confirm=delete&id=" . $id . "';";
	echo"	});";
	echo"</script>";
}

if($alert_flag == "4")
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









 
















