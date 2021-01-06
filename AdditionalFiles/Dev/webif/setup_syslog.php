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
	$ip = "";
	
/////////////////////////////////////////////////////////////////
//                                                             //
//                    GET PROCESSING                           //
//                                                             //
/////////////////////////////////////////////////////////////////
	
	
	$my_meth = $_SERVER['REQUEST_METHOD'];
	if($my_meth = "GET")
	{
		system("/sbin/rms-syslog.sh > /tmp/syslog");
		$f = fopen("/tmp/syslog", 'r');
		$line = trim(fgets($f));
		fclose($f);
		unlink("/tmp/syslog");
		if(strlen($line) !== 0)
		{
			$parts = explode("@",$line);
			$ip = $parts[1];
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

	
// OK Button	was clicked
if(isset ($_POST['save_btn']))
{	
	$oldip = $_POST['oldip'];
	$ip = $_POST['serverip'];
	
	if(strlen($oldip) == 0)
	{
		//ADD Mode
		
		if (filter_var($ip, FILTER_VALIDATE_IP) === false) 
		{
    	$text = $ip." is not a valid IP address!";
    	$alert_flag = "2";
		} 
		else 
		{
    	$myFile = "/etc/syslog.conf";
			$fh = fopen($myFile, 'a');
			$stringData = "*.* @".$ip."\n";
			fwrite($fh, $stringData);
			fclose($fh);
			$alert_flag = "1";
		}
	}
	else
	{
		//EDIT Mode
		
		if(strlen($ip) == 0)
		{
			//DELETE ip
			system("sed -i '/*.* /d' /etc/syslog.conf");
			system("kill -HUP `cat /var/run/syslogd.pid`");
			$alert_flag = "1";
		}
		else if (filter_var($ip, FILTER_VALIDATE_IP) === false) 
		{
    	$text = $ip." is not a valid IP address!";
    	$alert_flag = "2";
		} 
		else 
		{
    	system("sed -i '/*.* /d' /etc/syslog.conf");
    	$myFile = "/etc/syslog.conf";
			$fh = fopen($myFile, 'a');
			$stringData = "*.* ".$ip."\n";
			fwrite($fh, $stringData);
			fclose($fh);
			system("kill -HUP `cat /var/run/syslogd.pid`");
			$alert_flag = "1";
		}
	}
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
		<script src="javascript/bootstrap.min.js"></script>
		<script src="javascript/sweetalert.min.js"></script>
		<script src="javascript/conhelp.js"></script>
		<script src="javascript/ethertek.js"></script>
		<script language="javascript" type="text/javascript">
			SetContext('remotesyslog');
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
	SetContext('remotesyslog');
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
      		<div class="panel-body" style="max-width:400px">
      	  	<form name='Syslog' action='setup_syslog.php' method='post' class="form-horizontal">  	
      	    	<fieldset>
      	    		<legend><img src="images/remotesyslog.gif"> Remote System Log</legend> 
      	    		<div class="form-group"><label class="col-sm-4 control-label" style="text-align:left; max-width:140px; min-width:140px">Syslog Server IP:</label>
              		<div class="col-sm-12" style="max-width:200px">
              			<input type="text" class="form-control" name='serverip' value='<?php echo $ip; ?>' />
              		</div>
              	</div>	    	    	

              	<div class="row">
              		<div class="col-sm-12">
              			<input type="hidden" name="oldip" value="<?php echo $ip; ?>">
              	  	<button name="save_btn" class="btn btn-success" type="submit" onMouseOver="mouse_move(&#039;b_apply&#039;);" onMouseOut="mouse_move();"><i class="fa fa-check" ></i> Save</button>
              	  	<button name="cancel_btn" class="btn btn-primary" type="submit" onMouseOver="mouse_move(&#039;b_cancel&#039;);" onMouseOut="mouse_move();" formnovalidate><i class="fa fa-times"></i> Cancel</button>
              	  </div>
              	</div>  
              	<br>
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


?>
</body>
</html> 
