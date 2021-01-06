<?php
	//error_reporting(E_ALL);
	include "lib.php";
	$hostname = trim(file_get_contents("/etc/hostname"));
	
	$dbh = new PDO('sqlite:/etc/rms100.db');
	$result  = $dbh->query("SELECT * FROM display_options;");			
	foreach($result as $row)
	{
		$screen_animations = $row['screen_animations'];
	}
	
	$sd_errmsg2 = "";
	$alert_flag = "0";
	$themonth = "00";
	$command = "";
	$ntp = "";
	
	
/////////////////////////////////////////////////////////////////
//                                                             //
//                    GET PROCESSING                           //
//                                                             //
/////////////////////////////////////////////////////////////////


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

	
// time_save_btn Button	was clicked
if(isset ($_POST['time_save_btn']))
{	
	
	$mydate = $_POST["date"];
	$mytime = $_POST["timepicker"];			
	
	$dateparts = explode("-", $mydate);
	$mymonth = $dateparts[0];
	if($mymonth == "January"){$themonth = "01";}
	if($mymonth == "February"){$themonth = "02";}
	if($mymonth == "March"){$themonth = "03";}
	if($mymonth == "April"){$themonth = "04";}
	if($mymonth == "May"){$themonth = "05";}
	if($mymonth == "June"){$themonth = "06";}
	if($mymonth == "July"){$themonth = "07";}
	if($mymonth == "August"){$themonth = "08";}
	if($mymonth == "September"){$themonth = "09";}
	if($mymonth == "October"){$themonth = "10";}
	if($mymonth == "November"){$themonth = "11";}
	if($mymonth == "December"){$themonth = "12";}
	
	$myday = $dateparts[1];
	$myyear = $dateparts[2];
	
	$timeparts1 = explode(" ", $mytime);
	$myhour = $timeparts1[0];
	if(strlen($myhour) == 1){$myhour = "0".$myhour;}
	$myminute = $timeparts1[2];
	if(strlen($myminute) == 1){$myminute = "0".$myminute;}
	$myseconds = $timeparts1[4];
	if(strlen($myseconds) == 1){$myseconds = "0".$myseconds;}
	$ampm = $timeparts1[5];
	if($ampm == "PM" && $myhour != "12"){$myhour = $myhour + 12;}
	
	$command = sprintf("date -s %s%s%s%s%s.%s > /dev/null",$themonth,$myday,$myhour,$myminute,$myyear,$myseconds);
	system($command);
	
	$sd_seconds = strftime("%S");
	$sd_minutes = strftime("%M");
	$sd_hours = strftime("%H");
	$sd_day = strftime("%d");
	$sd_weekday = strftime("%u");
	if( $sd_weekday == 7){$sd_weekday = 0;}
	$sd_day = strftime("%d");
	$sd_month = strftime("%m");
	$sd_year = strftime("%y");
	
	$command = sprintf("rmsrtc writetime %s %s %s %02d %02d %02d %s",$sd_seconds,$sd_minutes,$sd_hours,$sd_day,$sd_weekday,$sd_month,$sd_year);
	system($command);
	
	$alert_flag = "1";
}

// timezone_save_btn Button	was clicked
if(isset ($_POST['timezone_save_btn']))
{		
	$command = sprintf("echo \"%s\" > /etc/TZ", $_POST['timezone']);
	system($command);
	$command = sprintf("sed -i s/.*date\.timezone.*// /etc/php.ini");
	system($command);
	$command = sprintf("echo -n date.timezone = %s >> /etc/php.ini",$_POST['timezone']);
	system($command);
	exec("/etc/init.scripts/S41lighttpd reload");
	exec("/etc/init.scripts/S42lighttpd-ssl reload");
	system("killall php-cgi");
	$alert_flag = "2";
}

// timeserver_save_btn Button	was clicked
if(isset ($_POST['timeserver_save_btn']))
{		
	$timeserver = $_POST["timeserver"];	 
	if(isset ($_POST['ntp']))
	{
		system("cp /etc/init.scripts/S49ntpclient /etc/init.d/S49ntpclient");
	}
	else
	{
		system("rm -f /etc/init.d/S49ntpclient");
	}
	$command = sprintf("sed -e 's/RSERVER=%c%c.*%c%c/RSERVER=%c%c%s%c%c/' -i /etc/init.scripts/S49ntpclient",92,34,92,34,92,34,$timeserver,92,34 );
	system($command);
	$command = sprintf("sed -e 's/RSERVER=%c%c.*%c%c/RSERVER=%c%c%s%c%c/' -i /etc/init.d/S49ntpclient",92,34,92,34,92,34,$timeserver,92,34 );
	system($command);
	$alert_flag = "3";
}

// timeserver_sync_btn Button	was clicked
if(isset ($_POST['timeserver_sync_btn']))
{		
	system("/etc/init.scripts/S49ntpclient sync > /dev/null");
	sleep(1);
	$sd2 = file_get_contents("/tmp/ntp.sd2");
	unlink("/tmp/ntp.sd1");
	unlink("/tmp/ntp.sd2");
	if(strlen($sd2)>1)
	{
		$alert_flag = "4"; //SYNC FAIL
	}
	else
	{
		$sd_seconds = strftime("%S");
		$sd_minutes = strftime("%M");
		$sd_hours = strftime("%H");
		$sd_day = strftime("%d");
		$sd_weekday = strftime("%u");
		if( $sd_weekday == 7){$sd_weekday = 0;}
		$sd_day = strftime("%d");
		$sd_month = strftime("%m");
		$sd_year = strftime("%y");
	
		$command = sprintf("rmsrtc writetime %s %s %s %02d %02d %02d %s",$sd_seconds,$sd_minutes,$sd_hours,$sd_day,$sd_weekday,$sd_month,$sd_year);
		system($command);
		$alert_flag = "5"; //SYNC SUCCESS
	}
}



if(file_exists("/etc/init.d/S49ntpclient"))
{
	$ntp = "checked";
}
else
{
	$ntp = "";
}
$timezone = file_get_contents("/etc/TZ");	
$command = sprintf("cat /etc/init.scripts/S49ntpclient | grep RSERVER= | sed s/RSERVER=%c%c// | sed s/%c%c// >  /tmp/tmp-webif-date",92,34,92,34);
system($command);
$timeserver = file_get_contents("/tmp/tmp-webif-date");
unlink("/tmp/tmp-webif-date");



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
    <link rel="stylesheet" href="css/jquery-ui.min.css" />
    <link rel="stylesheet" href="css/animate.css" />
    <link rel="stylesheet" href="css/bootstrap.css" />
    <link rel="stylesheet" href="css/awesome-bootstrap-checkbox.css" />
    <link rel="stylesheet" href="css/sweetalert.css" />
    <link rel="stylesheet" href="css/wickedpicker.min.css" />
    <link rel="stylesheet" href="css/ethertek.css">
    
    <!-- Java Scripts -->
		<script src="javascript/jquery.min.js"></script>
		<script src="javascript/jquery-ui.min.js"></script>
		<script src="javascript/bootstrap.min.js"></script>
		<script src="javascript/sweetalert.min.js"></script>
		<script src="javascript/wickedpicker.min.js"></script>
		<script src="javascript/conhelp.js"></script>
		<script src="javascript/ethertek.js"></script>
		<script language="javascript" type="text/javascript">
			SetContext('setup');
			  $(function() {
    		$( "#datepicker" ).datepicker();
    		$( "#datepicker" ).datepicker( "option", "dateFormat", "MM-dd-yy");
    		$( "#datepicker" ).datepicker( "setDate",'<?php echo date("F-d-Y"); ?>' );  
    		$( "#anim" ).change(function() {
      	$( "#datepicker" ).datepicker( "option", "showAnim", $( this ).val() );
    	});
  	});
  </script>
	
	<script language="javascript" type="text/javascript">
		function display_vm ()
		{
		        var myRandom = parseInt(Math.random()*999999999);
		        $.getJSON('sdserver.php?element=vm1all&rand=' + myRandom,
		            function(data)
		            {
		                  setTimeout (display_vm, 1000);
											$('#time1').replaceWith("<input id='time1' type='text' class='form-control' value='" + data.vmall.vmp4 + "&nbsp;-&nbsp;" + data.vmall.vmp5 + "' disabled/>");
								}
				);
		}
				
		display_vm ();
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
			echo '<div class="content animate-panel" data-effect="fadeInUp">';
		}
		else
		{
			echo '<div class="content">';
		}
	?>
  	<!-- INFO BLOCK START -->
  	<div class="row">
    	<div class="col-sm-8">
      	<div class="hpanel4">
      		<div class="panel-body" style="max-width:600px">
      	  	<form name='SystemTimeSetup' action='setup_system_time.php' method='post' class="form-horizontal">  	
      	    	<fieldset>
      	    		<legend><img src="images/btn_system-time_bg.gif"> System Time Setup</legend> 
      	    		<div class="form-group"><label class="col-sm-3 control-label" style="text-align:left; max-width:110px; min-width:110px">Date/Time:</label>
              		<div class="col-sm-12" style="max-width:250px">
              			<input id="time1" type="text" = value='<?php echo date("M-d-Y - h:i:s A"); ?>' disabled/>
              		</div>
              	</div>	    	    	
								
								<div class="form-group has-feedback"><label class="col-sm-3 control-label" style="text-align:left; max-width:110px; min-width:110px">Set Date:</label>
              		<div class="col-sm-12" style="max-width:250px">
              			<input type="text" class="form-control" name="date" id="datepicker" style="cursor: pointer;" size="30" value='<?php echo date("M-d-Y"); ?>' required>
              			<i class="fa fa-calendar form-control-feedback" aria-hidden="true"></i>
              		</div>
              	</div>
	            	
								<div class="form-group has-feedback"><label class="col-sm-3 control-label" style="text-align:left; max-width:110px; min-width:110px">Set Time:</label>
              		<div class="col-sm-12" style="max-width:250px">
              			<input type="text" class="form-control timepicker" name="timepicker" id="timepicker" style="cursor: pointer;" size="30" value="<?php echo date("h:i:s A"); ?>" required>
              			<i class="fa fa-clock-o form-control-feedback" aria-hidden="true"></i>
              		</div>
              	</div>
              	
              	<script>
                	$(function() 
                	{
                    var options = {
        							showSeconds: true,
        							title: 'Set New System Time'
    								};

                    $('.timepicker').wickedpicker(options);
                	});
                	
            		</script>
              	
              	<div class="form-group">
									<div class="col-sm-3" style="text-align:left; max-width:110px; min-width:110px">
									</div>
              		<div class="col-sm-2">
              	  	<button name="time_save_btn" class="btn btn-success" type="submit" onMouseOver="mouse_move(&#039;b_apply&#039;);" onMouseOut="mouse_move();"><i class="fa fa-check"></i> Apply</button>
              	  </div>
              	</div>
              	
              	<legend>Time Zone Setup</legend>
              	
              	<div class="form-group"><label class="col-sm-3 control-label" style="text-align:left; max-width:110px; min-width:110px">Time Zone:</label>
              		<div class="col-sm-12" style="max-width:250px">
              			<input type="text" class="form-control" name="timezone" value='<?php echo $timezone; ?>' />
              		</div>
              	</div>
              	
              	<div class="form-group">
									<div class="col-sm-3" style="text-align:left; max-width:110px; min-width:110px">
									</div>
              		<div class="col-sm-2">
              	  	<button name="timezone_save_btn" class="btn btn-success" type="submit" onMouseOver="mouse_move(&#039;b_apply&#039;);" onMouseOut="mouse_move();"><i class="fa fa-check"></i> Apply</button>
              	  </div>
              	</div>
              	
              	<legend>Time Server Setup</legend>
              	
              	<div class="form-group"><label class="col-sm-3 control-label" style="text-align:left; max-width:110px; min-width:110px">Time Server:</label>
              		<div class="col-sm-12" style="max-width:250px">
              			<input type="text" class="form-control" name="timeserver" value='<?php echo $timeserver; ?>' />
              		</div>
              	</div>
              	
              	<div class="form-group"><label class="col-sm-3 control-label" style="text-align:left; max-width:110px; min-width:110px"></label>
              		<div class="col-sm-8">
              			<div class="checkbox checkbox-success">
              				 <input id='ntp' type='checkbox' name='ntp' <?php echo $ntp; ?> />
                      <label for="ntp">Sync with Time Server on boot?</label>     
                    </div>
              		</div>
              	</div>

              	<div class="form-group">
									<div class="col-sm-3" style="text-align:left; max-width:110px; min-width:110px">
									</div>
              		<div class="col-sm-9" style="min-width:325px">
              	  	<button name="timeserver_save_btn" class="btn btn-success " type="submit" onMouseOver="mouse_move(&#039;sd_save_time&#039;);" onMouseOut="mouse_move();"><i class="fa fa-check"></i> Save</button>
              	  	<button name="timeserver_sync_btn" class="btn btn-success " type="submit" onMouseOver="mouse_move(&#039;sd_sync_time&#039;);" onMouseOut="mouse_move();"><i class="fa fa-clock-o"></i> Sync Time</button>
              	  	<button name="cancel_btn" class="btn btn-primary" type="submit" onMouseOver="mouse_move(&#039;b_cancel&#039;);" onMouseOut="mouse_move();" formnovalidate><i class="fa fa-times"></i> Cancel</button>
              	  </div>
              	</div>
              	
              </fieldset>  
						</form>	
      		</div> <!-- END PANEL BODY --> 
      	</div> <!-- END PANEL WRAPPER --> 
      </div>  <!-- END COL --> 
    </div> <!-- END ROW --> 
  </div> <!-- END CONTENT -->    
</div> <!-- END Main Wrapper -->
<?php 
if($alert_flag == "1")
{
echo"<script>";
echo"swal({";
echo"  title:'Success!',";
//echo"  text: '" . $command1 . "',";
echo"  text: 'New Time Settings Saved!',";
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
//echo"  text: '" . $command1 . "',";
echo"  text: 'New Time Zone Saved!',";
echo"  type: 'success',";
echo"  showConfirmButton: false,";
echo"  timer: 2500";
echo"});";
echo"</script>";
}

if($alert_flag == "3")
{
echo"<script>";
echo"swal({";
echo"  title:'Success!',";
//echo"  text: '" . $command1 . "',";
echo"  text: 'New Time Server Saved!',";
echo"  type: 'success',";
echo"  showConfirmButton: false,";
echo"  timer: 2500";
echo"});";
echo"</script>";
}

if($alert_flag == "4")
{
echo"<script>";
echo"swal({";
echo"  title:'Sync FAIL!',";
//echo"  text: '" . $command1 . "',";
echo"  text: 'Sync with Time Server Failed!',";
echo"  type: 'error',";
echo"  showConfirmButton: false,";
echo"  timer: 2500";
echo"});";
echo"</script>";
}

if($alert_flag == "5")
{
echo"<script>";
echo"swal({";
echo"  title:'Success!',";
//echo"  text: '" . $command1 . "',";
echo"  text: 'RMS-100 Synced with Time Server!',";
echo"  type: 'success',";
echo"  showConfirmButton: false,";
echo"  timer: 2500";
echo"});";
echo"</script>";
}

?>
</body>
</html> 
