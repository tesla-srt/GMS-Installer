<?php
	error_reporting(E_ALL);
	include "lib.php";
	$hostname = trim(file_get_contents("/etc/hostname"));
	
	$dbh = new PDO('sqlite:/etc/rms100.db');
	
	$alert_flag = "0";
	$info_block = "";
	$io_block = "";
	$relay_block = "";
	$vm_block = "";
	$alarm_block = "";
	$screen_animations = "";
	
/////////////////////////////////////////////////////////////////
//                                                             //
//                    GET PROCESSING                           //
//                                                             //
/////////////////////////////////////////////////////////////////

$result  = $dbh->query("SELECT * FROM display_options;");
foreach($result as $row)
{
	$info_block = $row['info_block'];
	$io_block = $row['io_block'];
	$relay_block = $row['relay_block'];
	$vm_block = $row['vm_block'];
	$alarm_block = $row['alarm_block'];
	$screen_animations = $row['screen_animations'];
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
	if(isset($_POST["info_block"]))
	{
		$info_block = "CHECKED";
	}
	else
	{
		$info_block = " ";
	}
	
	if(isset($_POST["io_block"]))
	{
		$io_block = "CHECKED";
	}
	else
	{
		$io_block = " ";
	}
	
	if(isset($_POST["relay_block"]))
	{
		$relay_block = "CHECKED";
	}
	else
	{
		$relay_block = " ";
	}
	
	if(isset($_POST["vm_block"]))
	{
		$vm_block = "CHECKED";
	}
	else
	{
		$vm_block = " ";
	}
	
	if(isset($_POST["alarm_block"]))
	{
		$alarm_block = "CHECKED";
	}
	else
	{
		$alarm_block = " ";
	}
	
	if(isset($_POST["screen_animations"]))
	{
		$screen_animations = "CHECKED";
	}
	else
	{
		$screen_animations = " ";
	}
	
	$query = sprintf("UPDATE display_options SET info_block='%s', io_block='%s', relay_block='%s', vm_block='%s', alarm_block='%s', screen_animations='%s'", $info_block, $io_block, $relay_block, $vm_block, $alarm_block, $screen_animations);
	$result  = $dbh->exec($query);
	$alert_flag = "1";
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
			SetContext('display');
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
	SetContext('display');
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
      	  	<form name='Display' action='setup_display.php' method='post' class="form-horizontal">  	
      	    	<fieldset>
      	    		<legend><img src="images/display.gif"> Display Setup</legend> 
      	    		
              	<div class="col-sm-12">
    		        	<div class='checkbox checkbox-success'>
    		        		<input type='checkbox' id='info_block' name='info_block' <?php echo $info_block; ?> />
    		          	<label for='info_block'> Display Top Information Block on Home Page?</label>
    		        	</div>
    		        </div>
    		        
    		        <div class="col-sm-12">
    		        	<div class='checkbox checkbox-success'>
    		        		<input type='checkbox' id='alarm_block' name='alarm_block' <?php echo $alarm_block; ?> />
    		          	<label for='alarm_block'> Display Alarm Information Block on Home Page?</label>
    		        	</div>
    		        </div>
    		      	
    		      	<div class="col-sm-12">
    		        	<div class='checkbox checkbox-success'>
    		        		<input type='checkbox' id='io_block' name='io_block' <?php echo $io_block; ?> />
    		          	<label for='io_block'> Display I/O Information Block on Home Page?</label>
    		        	</div>
    		        </div>
    		      	
    		      	<div class="col-sm-12">
    		        	<div class='checkbox checkbox-success'>
    		        		<input type='checkbox' id='relay_block' name='relay_block' <?php echo $relay_block; ?> />
    		          	<label for='relay_block'> Display Relay Information Block on Home Page?</label>
    		        	</div>
    		        </div>
    		      	
    		      	<div class="col-sm-12">
    		        	<div class='checkbox checkbox-success'>
    		        		<input type='checkbox' id='vm_block' name='vm_block' <?php echo $vm_block; ?> />
    		          	<label for='vm_block'> Display Voltmeter Information Block on Home Page?</label>
    		        	</div>
    		        </div>
					
								<div class="col-sm-12">
    		        	<div class="checkbox checkbox-success" onMouseOver="mouse_move('display_animations');" onMouseOut="mouse_move();">
    		        		<input type='checkbox' id='screen_animations' name='screen_animations' <?php echo $screen_animations; ?> />
    		          	<label for='screen_animations'> Display Screen Animations?</label>
    		        	</div>
    		        	<br>
    		        </div>
    		      	
              	<legend></legend>
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
?>
</body>
</html> 
