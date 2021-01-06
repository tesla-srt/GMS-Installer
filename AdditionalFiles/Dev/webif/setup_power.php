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
	
	$title = "";
	$alert_flag = "0";
	
	
/////////////////////////////////////////////////////////////////
//                                                             //
//                    GET PROCESSING                           //
//                                                             //
/////////////////////////////////////////////////////////////////
if(isset ($_GET['confirm']))
{
	$action = $_GET['confirm'];
	if($action == "restart")
	{
		restart();
		exit();
	}
	else if($action == "shutdown")
	{
		shutdown();
		exit();
	}
}

if(isset($_GET["restart"]))
	{
		$alert_flag = "5";
	}
	
if(isset($_GET["shutdown"]))
	{
		$alert_flag = "6";
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

	
if(isset($_POST["restart"]))
	{
		$alert_flag = "5";
	}
	
	if(isset($_POST["shutdown"]))
	{
		$alert_flag = "6";
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
			SetContext('power');
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
	SetContext('power');
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
      		<div class="panel-body" style="max-width:335px">
      	  	<form name='Power' action='setup_power.php' method='post' class="form-horizontal"">  	
      	    	<fieldset>
      	    		
              	<legend><img src="images/btn_reboot_bg.gif"> Main Power Options</legend> 
              	
              	<div class="form-group">
              		<div class="col-sm-12">
              	  	<button name="restart" class="btn btn-warning" type="submit" onMouseOver="mouse_move(&#039;b_reboot&#039;);" onMouseOut="mouse_move();"><i class="fa fa-check" ></i> Restart</button>
              	  	<button name="shutdown" class="btn btn-danger" type="submit" onMouseOver="mouse_move(&#039;b_shutdown&#039;);" onMouseOut="mouse_move();"><i class="fa fa-times"></i> Shutdown</button>
              	  	<button name="cancel_btn" class="btn btn-primary" type="submit" onMouseOver="mouse_move(&#039;b_cancel&#039;);" onMouseOut="mouse_move();"><i class="fa fa-times"></i> Cancel</button>
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

if($alert_flag == "5")
{
	echo"<script>";
	echo"	swal({";
	echo"		title: 'Restart the GMS-100<br>Are you really sure?',";
	echo"		type: 'warning',";
	echo"		showCancelButton: true,";
	echo"		html: true,";
	echo"		confirmButtonColor: '#DD6B55',";
	echo"		confirmButtonText: 'Yes, restart!',";
	echo"		closeOnConfirm: false";
	echo"	},";
	echo"	function(){";
	echo"		window.location.href = 'setup_power.php?confirm=restart';";
	echo"	});";
	echo"</script>";
}

if($alert_flag == "6")
{
	echo"<script>";
	echo"	swal({";
	echo"		title: 'Power Off the GMS-100<br>Are you really sure?',";
	echo"		type: 'warning',";
	echo"		showCancelButton: true,";
	echo"		html: true,";
	echo"		confirmButtonColor: '#DD6B55',";
	echo"		confirmButtonText: 'Yes, Power Off!',";
	echo"		closeOnConfirm: false";
	echo"	},";
	echo"	function(){";
	echo"		window.location.href = 'setup_power.php?confirm=shutdown';";
	echo"	});";
	echo"</script>";
}




function restart()
{
	$hostname = trim(file_get_contents("/etc/hostname"));
	$command = "grep address /etc/network/interfaces | cut -f 2 -d ' ' > /tmp/sdip";
	exec($command);
	$url = trim(file_get_contents("/tmp/sdip"));
	unlink("/tmp/sdip");
	echo "<!DOCTYPE html>";
	echo "<html>";
	echo "<head>";
	echo "  <meta charset='utf-8'>";
	echo "  <meta name='viewport' content='width=device-width, initial-scale=1.0'>";
	echo "  <meta http-equiv='X-UA-Compatible' content='IE=edge'>";
	echo "  <!-- Page title -->";
	echo "  <title>". $hostname . "</title>";
	$myrand = rand();
	echo "  <link rel='shortcut icon' type='image/ico' href='rms100favicon.ico?".$myrand."' />\n";
	echo "  <!-- CSS -->";
	echo "  <link rel='stylesheet' href='css/fontawesome/css/font-awesome.css' />";
	echo "  <link rel='stylesheet' href='css/animate.css' />";
	echo "  <link rel='stylesheet' href='css/bootstrap.css' />";
	echo "  <link rel='stylesheet' href='css/sweetalert.css' />";
	echo "  <link rel='stylesheet' href='css/ethertek.css'>";
	echo "  <!-- Java Scripts -->";
	echo "	<script src='javascript/jquery.min.js'></script>";
	echo "	<script src='javascript/bootstrap.min.js'></script>";
	echo "	<script src='javascript/sweetalert.min.js'></script>";
	echo "	<script src='javascript/conhelp.js'></script>";
	echo "	<script src='javascript/ethertek.js'></script>";
	echo "	<script language='javascript' type='text/javascript'>";
	echo "		SetContext('setup');";
	echo "	</script>";
	echo "	<script language=\"JavaScript\">\n";
	echo "		function fun2() { top.window.location.replace('http://" . $url . "/index.php') }\n";
	echo "		function fun1() { window.setTimeout('fun2()', 45000); }\n";
	echo "	</script>";
	echo "</head>";
	echo "<body class='fixed-navbar fixed-sidebar'>";
	echo "	<div class='splash'> <div class='solid-line'></div><div class='splash-title'><h1>Loading... Please Wait...</h1><div class='spinner'> <div class='rect1'></div> <div class='rect2'></div> <div class='rect3'></div> <div class='rect4'></div> <div class='rect5'></div> </div> </div> </div>";
	echo "	<!--[if lt IE 7]>";
	echo "	<p class='alert alert-danger'>You are using an <strong>old</strong> web browser. Please upgrade your browser to improve your experience.</p>";
	echo "	<![endif]-->";
	start_header();
	left_nav("setup");
	echo "<!-- Main Wrapper -->\n";
	echo "<div id='wrapper'>\n";
	echo "	<div class='row'>\n";
	echo "		<div class='col-lg-8'>\n";
  echo "			<h3 style='text-align:left;'> Restarting...</h3>\n";
	echo "		</div>\n";
	echo "	</div>\n";
	echo "		<p>";
	echo "			<a target='_top' href='http://" . $url . "/index.php'><u class='dotted'> Click here if you are not automatically redirected in 45 seconds.</u></a>\n";
	echo "		</p>";
	echo "		<script language='JavaScript'> fun1(); </script>\n";
	echo "</div>\n";
	echo "</body>";
	echo "</html>";
	exec("/sbin/reboot > /dev/null 2>&1 &");
}

function shutdown()
{
	$hostname = trim(file_get_contents("/etc/hostname"));
	$mac = file_get_contents("/var/macaddress");
	echo "<!DOCTYPE html>";
	echo "<html>";
	echo "<head>";
  echo "  <meta charset='utf-8'>";
  echo "  <meta name='viewport' content='width=device-width, initial-scale=1.0'>";
  echo "  <meta http-equiv='X-UA-Compatible' content='IE=edge'>";
  echo "  <!-- Page title -->";
  echo "  <title>". $hostname . "</title>";
  $myrand = rand();
  echo "  <link rel='shortcut icon' type='image/ico' href='rms100favicon.ico?".$myrand."' />\n";
  echo "  <!-- CSS -->";
  echo "  <link rel='stylesheet' href='css/fontawesome/css/font-awesome.css' />";
  echo "  <link rel='stylesheet' href='css/animate.css' />";
  echo "  <link rel='stylesheet' href='css/bootstrap.css' />";
  echo "  <link rel='stylesheet' href='css/sweetalert.css' />";
  echo "  <link rel='stylesheet' href='css/ethertek.css'>";
  echo "  <!-- Java Scripts -->";
	echo "	<script src='javascript/jquery.min.js'></script>";
	echo "	<script src='javascript/bootstrap.min.js'></script>";
	echo "	<script src='javascript/sweetalert.min.js'></script>";
	echo "	<script src='javascript/conhelp.js'></script>";
	echo "	<script src='javascript/ethertek.js'></script>";
	echo "	<script language='javascript' type='text/javascript'>";
	echo "		SetContext('setup');";
	echo "	</script>";
	echo "</head>";
	echo "<body class='fixed-navbar fixed-sidebar'>";
	echo "	<div class='splash'> <div class='solid-line'></div><div class='splash-title'><h1>Loading... Please Wait...</h1><div class='spinner'> <div class='rect1'></div> <div class='rect2'></div> <div class='rect3'></div> <div class='rect4'></div> <div class='rect5'></div> </div> </div> </div>";
	echo "	<!--[if lt IE 7]>";
	echo "	<p class='alert alert-danger'>You are using an <strong>old</strong> web browser. Please upgrade your browser to improve your experience.</p>";
	echo "	<![endif]-->";
	start_header();
	left_nav("setup");
	echo "<!-- Main Wrapper -->\n";
	echo "<div id='wrapper'>\n";
	echo "	<div class='row'>\n";
	echo "		<div class='col-lg-8'>\n";
  echo "			<h3 style='text-align:left;'> Shutdown in progress...</h3>\n";
	echo "		</div>\n";
	echo "	</div>\n";
	echo "	<br><br><H3> It is now safe to pull the <span style='color:green'>green</span> power plug.</H1>";
	echo "	<br><br>";
	echo 			$mac;
	echo "	<br><br>";
	echo "</div>\n";
	echo "</body>";
	echo "</html>";
	exec("/sbin/poweroff > /dev/null 2>&1 &");
}



?>
</body>
</html> 
