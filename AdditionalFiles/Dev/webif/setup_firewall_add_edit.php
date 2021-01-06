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


$action = "add";
$id = "";
$ip = "";
$desc = "";
$enabled = " ";

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
		$title = "Edit Firewall Rule# ".$id;
		$sd_query = sprintf("SELECT * FROM firewall WHERE id='%s';",$id);
		$result  = $dbh->query($sd_query);
		foreach($result as $row)
		{
			$ip = $row['ip'];
			$desc = $row['desc'];
			$enabled = $row['enabled'];
			if($enabled == "on")
			{
				$enabled = "checked";
			}
			else
			{
				$enabled = " ";
			}
		}
	}
	else
	{
		$title = "ADD New Firewall Rule";
		$enabled = " ";
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
	header("Location: setup_firewall.php");
}


// Save Firewall Rule Button was clicked
if(isset($_POST['save_btn']))
{
	$id = $_POST['id'];
	$ip = $_POST['ip'];
	$desc = $_POST['desc'];
	
	if (filter_var($ip, FILTER_VALIDATE_IP) === false) 
	{
    $text = $ip." is Not a Valid IP Address!";
    $alert_flag = "2";
    goto nosave;
	} 
	
	$result  = $dbh->query("SELECT * FROM firewall");
	foreach($result as $row)
	{
		$test_ip = $row['ip'];
		if($test_ip == $ip)
		{
			$text = $ip." already exists, why add it twice?!";
    	$alert_flag = "2";
    	goto nosave;
		}	
	}
	
	$action = $_POST['action'];
	if(isset($_POST['enabled']))
	{
		$enabled = "on";
		
	}
	else
	{
		$enabled = "off";
	}
	if($action == "add")
	{
		$query = sprintf("INSERT INTO firewall VALUES (NULL, '%s', '%s', '%s', '%s')", $ip, $desc, $enabled," ");
		$result  = $dbh->exec($query);
		header("Location: setup_firewall.php?action=add&success=yes");
	}
	else if($action == "edit")
	{
		$query = sprintf("UPDATE firewall SET ip='%s', desc='%s', enabled='%s', date_time='%s' WHERE id=%d", $ip, $desc, $enabled, " ", $id);
		$result  = $dbh->exec($query);
		header("Location: setup_firewall.php?action=edit&success=yes&id=".$id);
	}
}

nosave:

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
			SetContext('firewall');
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
	SetContext('firewall');
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
      	  	<form name='firewall' action='setup_firewall_add_edit.php' method='post' class="form-horizontal"">  	
      	    	<fieldset>
      	    		<legend><img src="images/firewalladd.gif"> <?php echo $title; ?></legend>  	
  									
    						<div class="form-group"><label class="col-sm-4 control-label">Allowed IP Address:</label>
              		<div class="col-sm-8" style="max-width:170px">
              			<input type="text" class="form-control" name='ip' value='<?php echo $ip; ?>' required/>
              		</div>
              	</div>
    						
    						<div class="form-group"><label class="col-sm-4 control-label">Description:</label>
              		<div class="col-sm-8" style="max-width:300px">
              			<input type="text" class="form-control" name='desc' value='<?php echo $desc; ?>' required/>
              		</div>
              	</div>
    						
    						<div class="form-group"><label class="col-sm-4 control-label"></label>
              		<div class="col-sm-8">
    		        		<div class='checkbox checkbox-success'>
    		          		<input type='checkbox' id='enabled' name='enabled' <?php echo $enabled; ?> />
    		            	<label for='enabled'>Enable this Firewall Rule?</label>
    		         		</div>
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




