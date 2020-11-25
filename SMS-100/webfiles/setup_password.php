<?php
	//error_reporting(E_ALL);
	include_once "mattLib/Utils.php";
	$hostname = trim(file_get_contents("/etc/hostname"));
    $first_run_bool = isFirstRun();

	$alert_flag = "0";
	$web_pass1 = "";
	$web_pass2 = "";


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


	//Cancel button 1 clicked
	if(isset($_POST['cancel_btn']))
	{
		header("Location: index.php");
	}


	// Web Save Button was clicked
	if(isset($_POST['save_btn']))
	{
		$web_pass1 = $_POST['web_pass1'];
		$web_pass2 = $_POST['web_pass2'];	
		
		$mylen = strlen($web_pass1);
		if($mylen > 63)
		{
			$text = "Passwords must not be longer than 63 Characters!";
			$alert_flag = "2";
			goto nosave;
		}
		if($mylen == 0)
		{
			$text = "Passwords must not be zero length!";
			$alert_flag = "2";
			goto nosave;
		}
		
		$mylen = strlen($web_pass2);
		if($mylen > 63)
		{
			$text = "Passwords must not be longer than 63 Characters!";
			$alert_flag = "2";
			goto nosave;
		}
		if($mylen == 0)
		{
			$text = "Passwords must not be zero length!";
			$alert_flag = "2";
			goto nosave;
		}
		
		if($web_pass1 !== $web_pass2)
		{
			$text = "Passwords do not Match!";
			$alert_flag = "2";
			goto nosave;
		}

		// Save new web password only
		$command = sprintf("/data/custom/htpasswd.sh monitor monitor %s",$web_pass1);
		system($command);
		$alert_flag = "1";
		
			
			
	}

	nosave:

?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0 shrink-to-fit=no">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">

	<!-- Page title -->
	<title><?php echo $hostname; ?></title>
	<link rel="shortcut icon" type="image/ico" href="mattLib/images/favicon.ico?<?php echo rand(); ?>" />
	
    <script> function jsIsFirstRun() { return <?php echo $first_run_bool ?> }; </script>
    
	<link rel="stylesheet" href="mattLib/dependencies/bootstrap.min.css" />
	<link rel="stylesheet" href="mattLib/dependencies/sweetalert.css" />
	<link rel="stylesheet" href="mattLib/SolarRig.css">
	<link href="https://fonts.googleapis.com/css?family=Roboto+Condensed:400,700&display=swap" rel="stylesheet"> 
	
	<script src="mattLib/dependencies/jquery-3.4.1.min.js"></script>
	<script src="mattLib/dependencies/bootstrap.bundle.min.js"></script>
	<script src="mattLib/dependencies/sweetalert.min.js"></script>
</head>

<body class="bg-body" style="font-family: 'Roboto Condensed', sans-serif;">
	<div class="container-fluid">
		<form name='PassSetup' action='setup_password.php' method='post'>
			<div class="row justify-content-center">
				<div class="col-auto border rounded bg-label shadow mx-2 my-2 py-2">
					
					<legend>Change Password</legend>
					<div class="form-group">
						<label for="web_pass1" class="col-auto col-form-label">Type New Password:</label>
						<div class="col-sm-12">
							<input type="password" class="form-control input-sm" name='web_pass1' required />
						</div>
					</div>
					<div class="form-group">
						<label for="web_pass2" class="col-auto col-form-label">Confirm New Password:</label>
						<div class="col-sm-12">
							<input type="password" class="form-control input-sm" name='web_pass2' required />
						</div>
					</div>
					
					<button name="save_btn" class="btn btn-primary shadow" type="submit" >Save</button>
					<button name="cancel_btn" class="btn btn-secondary shadow" type="submit" formnovalidate>Cancel</button>
				</div>
			</div>
		</form>

		<!-- Start Sticky Footer -->
			<style>
				html {
					position: relative;
					min-height: 100%;
				}
				body {
					margin-bottom: 60px; /* Margin bottom by footer height */
				}
				.footer {
					position: absolute;
					bottom: 0;
					width: 95%;
				}
			</style>
			<footer class="footer text-center"><span><?php //printCopyright();?></span></footer>
		<!-- End Sticky Footer -->
	</div>


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
	echo"},";
    echo"function(){";
    echo"	if(jsIsFirstRun() == 1) { window.location.href = 'setup.php'; }";
    echo"   else { window.location.href = 'index.php'; }";
    echo"	});";
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