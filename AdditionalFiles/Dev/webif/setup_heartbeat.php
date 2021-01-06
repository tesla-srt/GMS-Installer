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
	$hb = "";
	$cp = "";
	
	
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
	header("Location: setup.php");
}

	
// OK Button	was clicked
if(isset ($_POST['save_btn']))
{	
	if(isset ($_POST['hb']))
	{
		$result  = $dbh->exec("UPDATE heart_beat_led SET led_value='on';"); 
		system("setgpiobits heartbeat on");
	}
	else
	{
		$result  = $dbh->exec("UPDATE heart_beat_led SET led_value='off';"); 
		system("setgpiobits heartbeat off");
	}
	
	if(isset ($_POST['cp']))
	{
		exec("sed -i '/ttyS0/s/^#//' /etc/inittab");//(uncomment, console on)
		exec('dmesg -n 4');
		exec('kill -HUP 1');
	}
	else
	{
		$line = exec("sed -n '/ttyS0/p' /etc/inittab");
		$pos = strpos($line,"#");
		if ($pos === false) //# was not found
		{
			exec("sed -i '/ttyS0/s/^/#/' /etc/inittab"); //(add comment, console off)
			exec('dmesg -n 1');
		}
		
		exec('kill -HUP 1');
	}
	
	
	$alert_flag = "1";
}
	

$result  = $dbh->query('SELECT * FROM heart_beat_led');
foreach($result as $row)
{
	$hb = $row['led_value'];
}

$line = exec("sed -n '/ttyS0/p' /etc/inittab");
$pos = strpos($line,"#");
if ($pos === false) //# was not found
{
	$cp="on";
}
else
{
	$cp="off";
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
			SetContext('general');
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
	SetContext('heartbeat');
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
      		<div class="panel-body" style="max-width:500px">
      	  	<form name='Heartbeat' action='setup_heartbeat.php' method='post' class="form-horizontal">  	
      	    	<fieldset>
      	    		<legend><img src="images/comport.jpg"> Console Port Setup (ttyS0)</legend> 
      	    			<?php
              			echo "<div class='checkbox checkbox-success'>";
              			if($cp == "on")
              			{
              				echo "	<input id='cp' type='checkbox' name='cp' checked />";		
              			}
              			else
              			{
              				echo "	<input id='cp' type='checkbox' name='cp' />";		
              			}
  									echo "  <label for='cp'> Enable Console Shell? (Uncheck for regular Com Port)</label>";     
  									echo "</div>";
              		?>
      	    		
      	    		
      	    		<br><br><br>
      	    		<legend><img src="images/heart.gif"> Heartbeat Led Setup</legend> 
      	    			<?php
              			echo "<div class='checkbox checkbox-success'>";
              			if($hb == "on")
              			{
              				echo "	<input id='hb' type='checkbox' name='hb' checked />";		
              			}
              			else
              			{
              				echo "	<input id='hb' type='checkbox' name='hb' />";		
              			}
  									echo "  <label for='hb'> Enable Heartbeat Led?</label>";     
  									echo "</div>";
              		?>
              		<br>
              		<div class="form-group">
              			<div class="col-sm-12">
              	  		<button name="save_btn" class="btn btn-success" type="submit" onMouseOver="mouse_move(&#039;b_apply&#039;);" onMouseOut="mouse_move();"><i class="fa fa-check" ></i> Save</button>
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
echo"  text: 'Settings Saved!',";
echo"  type: 'success',";
echo"  showConfirmButton: false,";
echo"  timer: 2000";
echo"});";
echo"</script>";
}

function replace_string_in_file($filename, $string_to_replace, $replace_with)
{
    $content=file_get_contents($filename);
    $content_chunks=explode($string_to_replace, $content);
    $content=implode($replace_with, $content_chunks);
    file_put_contents($filename, $content);
}



?>
</body>
</html> 
