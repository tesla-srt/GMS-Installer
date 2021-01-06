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
	$query = "";
	$id = "0";
	$days = 0;
	$hours = 0;
	$minutes = 0;
	$seconds = 0;
	$sd_buf1 = "";
	$sd_buf2 = "";
	$sd1 = 0;
	
	
	$dbh = new PDO('sqlite:/etc/rms100.db');
	
		
/////////////////////////////////////////////////////////////////
//                                                             //
//                    GET PROCESSING                           //
//                                                             //
/////////////////////////////////////////////////////////////////


	
if(isset($_GET['timers']))
	{
		$action = $_GET['timers'];
		if($action == "edit")
		{
			$id = $_GET['id'];
			$query = sprintf("SELECT * FROM timers WHERE id=%d",$id);
			$result  = $dbh->query($query);
			foreach($result as $row)
			{
				$id = $row['id'];
				$name = $row['name'];
				$notes = $row['notes'];
				$cmds = $row['cmds'];
				$start_time = $row['start_time']; 
				$firstrun = $row['firstrun'];
				$shots = $row['shots'];
				
				if($start_time == 0)
				{
					$days = 0;
					$hours = 0;
					$minutes = 0;
					$seconds = 0;
				}
				else
				{
					$theTime = secondsToTime($start_time);
					$days = $theTime['d'];
					$hours = $theTime['h'];
					$minutes = $theTime['m'];
					$seconds = $theTime['s'];
				}
				
				if($shots == "0")
				{
					$shots = "one";
				}
				else
				{
					$shots = "con";
				}
				
				
				$header = "Edit Timer #" . $id;
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
	header("Location: setup_timers.php");
}

// Save Button	was clicked
if(isset ($_POST['save_btn']))
{
	$id = $_POST['id'];
	$shots = $_POST['shots'];
	$name = $_POST['name'];
	$notes = $_POST['notes'];
	$cmds = $_POST['cmds'];
	$days = $_POST['days'];
	$hours = $_POST['hours'];
	$minutes = $_POST['minutes'];
	$seconds = $_POST['seconds'];
	
	if($shots == "one")
	{
		$sd1 = 0;
	}
	else
	{
		$sd1 = 1;
	}
	
	$seconds = $seconds + 60*($minutes + 60*($hours + 24*$days));
	
	$query = sprintf("UPDATE timers SET name='%s',notes='%s',cmds='%s',start_time='%s',firstrun='1',shots='%s' where id = %s;",$name,$notes,$cmds,$seconds,$sd1,$id);
  $result  = $dbh->exec($query);    		 	 	
  system("kill -HUP `cat /var/run/rmstimerd.pid`");
	header("Location: setup_timers.php?action=edit&success=yes&id=".$id);
	
	
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
    <link rel="stylesheet" href="css/jquery.bootstrap-touchspin.min.css" />
    <link rel="stylesheet" href="css/awesome-bootstrap-checkbox.css" />
    <link rel="stylesheet" href="css/sweetalert.css" />
    <link rel="stylesheet" href="css/ethertek.css">
    
    <!-- Java Scripts -->
		<script src="javascript/jquery.min.js"></script>
		<script src="javascript/bootstrap.min.js"></script>
		<script src="javascript/jquery.bootstrap-touchspin.min.js"></script>
		<script src="javascript/sweetalert.min.js"></script>
		<script src="javascript/conhelp.js"></script>
		<script src="javascript/ethertek.js"></script>
		<script language="javascript" type="text/javascript">
			SetContext('timers');
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
	SetContext('timers');
</script>
<!-- Main Wrapper -->


<div id="wrapper">
	<div class="content animate-panel" data-effect="fadeInUpBig">
  	<!-- INFO BLOCK START -->
  	

  	<form name='Cron' action='setup_timers_edit.php' method='post' class="form-horizontal">  	
    	<fieldset>
  			<div class="row">
    			<div class="col-sm-12">
    		  	<div class="hpanel4">
    		  	  <div class="panel-body" style="max-width:540px">
    				  	<legend><img src="images/timers.gif"> <?php echo $header; ?></legend> 
      	    		

    				  	<div class="form-group">
    				  		<label class="col-sm-3 control-label" style="text-align:left; max-width:160px; min-width:160px">Count Down Time:</label>	
              	</div>
    				  	
    				  	<div class="form-group"><label class="col-sm-3 control-label" style="text-align:left; max-width:160px; min-width:160px">Days:</label>
              		<div class="col-sm-12" style="max-width:150px" onMouseOver="mouse_move('sd_timers_info');" onMouseOut="mouse_move();">
              			<input id="days" type="text" name="days" style="text-align:center" value="<?php echo $days; ?>">
              		</div>
              	</div>
    				  	
    				  	<div class="form-group"><label class="col-sm-3 control-label" style="text-align:left; max-width:160px; min-width:160px">Hours:</label>
              		<div class="col-sm-12" style="max-width:150px">
              			<input id="hours" type="text" name="hours" style="text-align:center" value="<?php echo $hours; ?>" onMouseOver="mouse_move('sd_timers_info');" onMouseOut="mouse_move();">
              		</div>
              	</div>
    				  	
    				  	<div class="form-group"><label class="col-sm-3 control-label" style="text-align:left; max-width:160px; min-width:160px">Minutes:</label>
              		<div class="col-sm-12" style="max-width:150px">
              			<input id="minutes" type="text" name="minutes" style="text-align:center" value="<?php echo $minutes; ?>" onMouseOver="mouse_move('sd_timers_info');" onMouseOut="mouse_move();">
              		</div>
              	</div>
    				  	
    				  	<div class="form-group"><label class="col-sm-3 control-label" style="text-align:left; max-width:160px; min-width:160px">Seconds:</label>
              		<div class="col-sm-12" style="max-width:150px">
              			<input id="seconds" type="text" name="seconds" style="text-align:center" value="<?php echo $seconds; ?>" onMouseOver="mouse_move('sd_timers_info');" onMouseOut="mouse_move();">
              		</div>
              	</div>
              	
              	<div class="form-group"><label class="col-sm-3 control-label" style="text-align:left; max-width:160px; min-width:160px">Timer Name:</label>
              		<div class="col-sm-12" style="max-width:350px">
              			<input class="form-control" type="text" name="name" value="<?php echo $name; ?>" required />
              		</div>
              	</div>
              	
              	<div class="form-group"><label class="col-sm-3 control-label" style="text-align:left; max-width:160px; min-width:160px">Description:</label>
              		<div class="col-sm-12" style="max-width:350px">
              			<input class="form-control" type="text" name="notes" value="<?php echo $notes; ?>" required />
              		</div>
              	</div>
              	
              	<div class="form-group"><label class="col-sm-3 control-label" style="text-align:left; max-width:160px; min-width:160px">Command to Run:</label>
              		<div class="col-sm-12" style="max-width:350px">
              			<input class="form-control" type="text" name="cmds" value="<?php echo $cmds; ?>" required />
              		</div>
              	</div>
              	<?php
              		if($shots == "one")
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
                	<input type="radio" name="shots" id="radio1" value="one" <?php echo $sd_buf1; ?> />
                  	<label for="radio1">
                    	Timer <?php echo $id; ?> is: &nbsp;&nbsp;&nbsp;&nbsp;One Shot Only 
                    </label>
                </div>
                
                <div class="radio radio-primary">
                	<input type="radio" name="shots" id="radio2" value="con" <?php echo $sd_buf2; ?> />
                		<label for="radio2">
                    	Timer <?php echo $id; ?> is: &nbsp;&nbsp;&nbsp;&nbsp;Continuous
                    </label>
                </div>
                
    				  	<br><br>
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

<script>
$(function(){

    $("#days").TouchSpin({
        min: 0,
        max: 31,
        step: 1,
        decimals: 0,
        boostat: 5,
        maxboostedstep: 10,
    });
    
    $("#hours").TouchSpin({
        min: 0,
        max: 23,
        step: 1,
        decimals: 0,
        boostat: 5,
        maxboostedstep: 10,
    });
    
    $("#minutes").TouchSpin({
        min: 0,
        max: 59,
        step: 1,
        decimals: 0,
        boostat: 5,
        maxboostedstep: 10,
    });
    
    $("#seconds").TouchSpin({
        min: 0,
        max: 59,
        step: 1,
        decimals: 0,
        boostat: 5,
        maxboostedstep: 10,
    });
});

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









 
















