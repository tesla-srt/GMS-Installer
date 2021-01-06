<?php
	error_reporting(E_ALL);
	include "lib.php";
	$hostname = trim(file_get_contents("/etc/hostname"));
	
	$sd_errmsg = "";
	$sd_errmsg2 = "";
	$alert_flag = "0";
	
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
  	$_POST[$key] = preg_replace("/[^a-zA-Z0-9\s!@#$%^&*()_\-=+?.,:\/]/", "", $input_arr);
}

// Cancel Button	was clicked
if(isset ($_POST['cancel_btn']))
{
	header("Location: setup.php");
}

	
// OK Button	was clicked
if(isset ($_POST['save_btn']))
{	
	
	if(empty ($_POST['admin_companyname']))
		{
			//no Company Name
			$sd_errmsg2 = "<span style='color:red'>&nbsp;&nbsp;&nbsp;<i class='fa fa-asterisk' aria-hidden='true'> No Company Name!</i></span>";
			goto error_condition;
		}
	
	if(empty ($_POST['admin_phonenumber']))
		{
			//no Company Phone Number
			$sd_errmsg2 = "<span style='color:red'>&nbsp;&nbsp;&nbsp;<i class='fa fa-asterisk' aria-hidden='true'> No Phone Number!</i></span>";
			goto error_condition;
		}
	
	if(empty ($_POST['admin_companynotes']))
		{
			//no Company Notes
			$sd_errmsg2 = "<span style='color:red'>&nbsp;&nbsp;&nbsp;<i class='fa fa-asterisk' aria-hidden='true'> No Notes!</i></span>";
			goto error_condition;
		}
		
	$mycompanyname = $_POST["admin_companyname"];
	$mycompanyphonenumber = $_POST["admin_phonenumber"];			
	$mycompanynotes = $_POST["admin_companynotes"];
	
	$dbh = new PDO('sqlite:/etc/rms100.db');
	$result  = $dbh->exec("UPDATE admin_info SET company='" . $mycompanyname . "',phone='" . $mycompanyphonenumber . "',notes='" . $mycompanynotes . "'"); 
	$dbh = NULL;
	//$sd_errmsg2 = "<span style='color:green'>&nbsp;&nbsp;&nbsp;<i class='fa fa-asterisk' aria-hidden='true'> Settings Saved...</i></span>";
	$alert_flag = "1";
}
	

error_condition:
$dbh = new PDO('sqlite:/etc/rms100.db');
$result  = $dbh->query("SELECT * FROM admin_info WHERE id='1'");
foreach($result as $row)
{
	$admin_companyname = $row['company'];
	$admin_companyphonenumber = $row['phone'];
	$admin_companynotes = $row['notes'];
}
$result  = $dbh->query("SELECT * FROM display_options;");			
	foreach($result as $row)
	{
		$screen_animations = $row['screen_animations'];
	}
$dbh = NULL;

	
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
	SetContext('general');
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
      		<div class="panel-body" style="max-width:500px">
      	  	<form name='CompanySetup' action='setup_company.php' method='post' class="form-horizontal">  	
      	    	<fieldset>
      	    		<legend><img src="images/company_setup.gif"> Company Setup</legend> 
      	    		<div class="form-group"><label class="col-sm-3 control-label" style="text-align:left; max-width:140px; min-width:140px">Company Name:</label>
              		<div class="col-sm-12" style="max-width:270px">
              			<div class="input-group">
              				<span class="input-group-addon"><i class="fa fa-user" style="max-width:16px; min-width:16px"></i></span>
              				<input type="text" class="form-control" name="admin_companyname" maxlength="62" value="<?php echo $admin_companyname; ?>" required />
              			</div>
              		</div>
              	</div>	    	    	
								
								<div class="form-group"><label class="col-sm-3 control-label" style="text-align:left; max-width:140px; min-width:140px">Phone:</label>
              		<div class="col-sm-12" style="max-width:270px">
              			<div class="input-group">
              				<span class="input-group-addon"><i class="fa fa-phone" style="max-width:16px; min-width:16px"></i></span>
              				<input type="text" class="form-control" name="admin_phonenumber" maxlength="62" value="<?php echo $admin_companyphonenumber; ?>" required />
              			</div>
              		</div>
              	</div>
	            	
								<div class="form-group"><label class="col-sm-3 control-label" style="text-align:left; max-width:140px; min-width:140px">Notes:</label>
              		<div class="col-sm-12" style="max-width:270px">
              			<div class="input-group">
              				<span class="input-group-addon"><i class="fa fa-comment" style="max-width:16px; min-width:16px"></i></span>
              				<textarea class="form-control" rows="5" maxlength="510" name="admin_companynotes" required><?php echo $admin_companynotes; ?></textarea>
              			</div>
              		</div>
              	</div>
              	
              	<div class="form-group">
									
									<div class="col-sm-3" style="text-align:left; max-width:126px; min-width:126px">
									</div>
              		<div class="col-sm-8" style="text-align:left; max-width:270px">
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
