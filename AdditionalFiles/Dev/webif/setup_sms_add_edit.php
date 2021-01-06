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
$type = "SMS";
$desc = "";
$phone = "";

$action = "add";
$id = "";

/////////////////////////////////////////////////////////////////
//                                                             //
//                    GET PROCESSING                           //
//                                                             //
/////////////////////////////////////////////////////////////////
	
if(isset($_GET['action']))
{
	$action = $_GET['action'];
	if($action == "edit")
	{
		$id = $_GET['id'];
		$title = "Edit SMS Message ID# ".$id;
		$sd_query = sprintf("SELECT * FROM alerts WHERE id='%s';",$id);
		$result  = $dbh->query($sd_query);
		foreach($result as $row)
		{
			$name = $row['name'];
			$desc = $row['desc'];
			$ip = $row['v1'];
			$user = $row['v2'];
			$pass = $row['v3'];
			$num = $row['v4'];
			$message = $row['v5'];
		}
	}
	else
	{
		$title = "ADD New SMS Message";
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


//Cancel button clicked
if(isset($_POST['cancel_btn']))
{
	header("Location: setup_notifications.php");
}


// Save SMS Message Button was clicked
	if(isset($_POST['save_btn']))
	{
		$name = $_POST['trap_name'];
		$desc = $_POST['trap_desc'];
		$action = $_POST['action'];
		$id = $_POST['id'];
		
		
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
			SetContext('notification');
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
	SetContext('notification');
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
      		<div class="panel-body" style="max-width:600px">
      	  	<form name='smsform' action='setup_sms_add_edit.php' method='post' class="form-horizontal"">  	
      	    	<fieldset>
      	    		<legend><img src="images/cellphone_add.gif"> <?php echo $title; ?></legend>  	
  									
    						<div class="form-group"><label class="col-sm-3 control-label" style="text-align:left; max-width:140px; min-width:140px">SMS Name:</label>
              		<div class="col-sm-12" style="max-width:400px">
              			<input type="text" class="form-control" name='desc' value='<?php echo $desc; ?>' placeholder="Describe this SMS Message" required/>
              		</div>
              	</div>
    						
    						<div class="form-group"><label class="col-sm-3 control-label" style="text-align:left; max-width:140px; min-width:140px">Phone #</label>
              		<div class="col-sm-12" style="max-width:400px">
              			<input type="text" class="form-control" name='phone' value='<?php echo $phone; ?>' placeholder="Enter the phone number to receive this SMS Message" required/>
              		</div>
              	</div>
    						
    						<div class="form-group"><label class="col-sm-3 control-label" style="text-align:left; max-width:140px; min-width:140px">GPRS Modem</label>
              		<div class="col-sm-12" style="max-width:400px">
   	          			<select size="0" name="sms_device" class="form-control">
   	          				<?php
   	          					$sd_query = sprintf("SELECT * FROM device_mgr WHERE type='GPRS'");
												$result  = $dbh->query($sd_query);
												foreach($result as $row)
												{
													$id = $row['id'];
													$name = $row['name'];
													$html = sprintf("<option value='%s'>%s</option>\n", $id, $name);	
												}
              				?>
              			</select>
              		</div>
              	</div>
    						
    						<div class="form-group"><label class="col-sm-3 control-label" style="text-align:left; max-width:140px; min-width:140px"></label>
              		<div class="col-sm-12" style="max-width:400px">
    		        		<div class='checkbox checkbox-success'>
    		          		<input type='checkbox' id='msg_check' name='msg_check' />
    		            	<label for='msg_check'>Send system message instead of custom message?</label>
    		         		</div>
    		         	</div>
    		      	</div>
    						
    						<div class="form-group"><label class="col-sm-3 control-label" style="text-align:left; max-width:140px; min-width:140px">Custom Message:</label>
              		<div class="col-sm-12" style="max-width:400px">
              			<input type="text" class="form-control" name='custom' placeholder="Enter a custom SMS Message (150 chars max)" required/>
              		</div>
              	</div>
    						
								
								<div class="form-group">
              		<div class="col-sm-12">
              			<input type="hidden" name="action" value="<?php echo $action; ?>">
              			<input type="hidden" name="id" value="<?php echo $id; ?>">
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

</script>
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




