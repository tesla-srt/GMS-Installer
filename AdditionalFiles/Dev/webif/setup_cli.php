<?php
	error_reporting(E_ALL);
	include "lib.php";
	$hostname = trim(file_get_contents("/etc/hostname"));
	$alert_flag = "0";
	$command_output = "";
	
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

// EXE Button	was clicked
if(isset ($_POST['exe_btn']))
{
	$command = $_POST['command'];
	$command = $command . " > /tmp/cli.txt";
	system($command);
	$command_output = file_get_contents("/tmp/cli.txt");
	unlink("/tmp/cli.txt");
}

// Cancel Button	was clicked
if(isset ($_POST['cancel']))
{
	header("Location: setup.php");
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
			SetContext('setup');
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
      		<div class="panel-body" style="max-width:700px">
      	  	<form name='Cli' action='setup_cli.php' method='post' class="form-horizontal">  	
      	    	<fieldset>
      	    		<legend><img src="images/clianim.gif"> Command Line Interface</legend> 
      	    		<div class="form-group"><label class="col-sm-2 control-label" style="text-align:left; max-width:240px; min-width:240px">Enter Single Command:</label>
              		<div class="col-sm-12" style="max-width:550px">
              			<input type="text" class="form-control" name="command" />
              		</div>
              	</div>
              	
              	<div class="row">
              		<div class="col-sm-10">
              			<button name="exe_btn" class="btn btn-success" type="submit"><i class="fa fa-bomb" ></i> Execute Command</button>
              		</div>
              	</div>
              	
              	<div class="row">
              		<?php
              			if(strlen($command_output) > 0)
              			{
              				echo '<br>';
              				echo '<pre>';
              				echo $command_output;
              				echo '</pre>';
              			}
              		?>
              	</div>
              	
              	<br>
              	<legend></legend>
              	
              	<div class="form-group">
              		<label class="col-sm-3 control-label" style="text-align:left; max-width:280px; min-width:280px">Linux Command Shell Interface:</label>
              	</div>
              	
              	<div class="row">
              		<div class="col-sm-12">
										<iframe allowtransparency='true' style='background: #FFFFFF;' height='450' width='100%' src='siab.cgi'></iframe>
									</div>
								</div>
								
									<br>
								<button name="cancel" class="btn btn-primary" type="submit"><i class="fa fa-times" ></i> Cancel</button>
								<br>
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
?>
</body>
</html> 
