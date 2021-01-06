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
$web_pass1 = "";
$web_pass2 = "";
$shell_pass1 = "";
$shell_pass2 = "";


/////////////////////////////////////////////////////////////////
//                                                             //
//                    GET PROCESSING                           //
//                                                             //
/////////////////////////////////////////////////////////////////
	
//$pageWasRefreshed = isset($_SERVER['HTTP_CACHE_CONTROL']) && $_SERVER['HTTP_CACHE_CONTROL'] === 'max-age=0';
//	if($pageWasRefreshed ) 
//	{
//   	//page was refreshed;
//   	goto nosave;
//	}

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
if(isset($_POST['cancel_btn1']))
{
	header("Location: setup.php");
}

//Cancel button 2 clicked
if(isset($_POST['cancel_btn2']))
{
	header("Location: setup.php");
}



// Web Save Button was clicked
if(isset($_POST['save_btn1']))
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
	
	if(isset($_POST['pass_check']))
	{
		// Make Web and Shell Passwords the same
		
		//First save Shell Password
		$command = sprintf("passwd -p %s %s > /dev/null",$web_pass1,$web_pass2);
		system($command);
		//Now save Web Pasword
		$command = sprintf("/bin/htpasswd.sh root root %s",$web_pass1);
		system($command);
		$alert_flag = "1";
	}
	else
	{
		// Save new web password only
		$command = sprintf("/bin/htpasswd.sh root root %s",$web_pass1);
		system($command);
		$alert_flag = "1";
	}
		
		
}



// Shell Save Button was clicked
if(isset($_POST['save_btn2']))
{
	$shell_pass1 = $_POST['shell_pass1'];
	$shell_pass2 = $_POST['shell_pass2'];
	
	$mylen = strlen($shell_pass1);
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
	
	$mylen = strlen($shell_pass2);
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
	
	if($shell_pass1 !== $shell_pass2)
	{
		$text = "Passwords do not Match!";
		$alert_flag = "2";
		goto nosave;
	}	
	
	//Save Shell Password
	$command = sprintf("passwd -p %s %s > /dev/null",$shell_pass1,$shell_pass1);
	system($command);
	$alert_flag = "1";
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
		<script src="javascript/bootstrap.min.js"></script>
		<script src="javascript/sweetalert.min.js"></script>
		<script src="javascript/conhelp.js"></script>
		<script src="javascript/ethertek.js"></script>
		<script language="javascript" type="text/javascript">
			SetContext('password');
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
	SetContext('password');
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
  	
  	<form name='pwdform' action='setup_password.php' method='post' class="form-horizontal"">
  		<fieldset>
  			<div class="row">
    			<div class="col-sm-12">
    		  	<div class="hpanel4" style="max-width:600px">
    		  		<div class="panel-body">
    		  	  	  	
    		  	  	<legend><img src="images/web_interface.gif"> Web Interface Password</legend>  	
  									
    						<div class="form-group"><label class="col-sm-4 control-label" style="color:blue; min-width:220px">Type New Web Password:</label>
    		      		<div class="col-sm-8" style="max-width:350px">
    		      			<input type="password" class="form-control" name='web_pass1' value='<?php echo $web_pass1; ?>' />
    		      		</div>
    		      	</div>
    						
    						<div class="form-group"><label class="col-sm-4 control-label" style="color:green; min-width:220px">Confirm New Web Password:</label>
    		      		<div class="col-sm-8" style="max-width:350px">
    		      			<input type="password" class="form-control" name='web_pass2' value='<?php echo $web_pass2; ?>' />
    		      		</div>
    		      	</div>
    						
    						<div class="form-group"><label class="col-sm-2 control-label"></label>
    		      		<div class="col-sm-12">
    				  			<div class='checkbox checkbox-warning'>
    				  	  		<input type='checkbox' id='pass_check' name='pass_check' checked />
    				  	    	<label for='pass_check' style="color:#EC971F">Make the Web Pasword and the Shell Password the same?</label>
    				  	 		</div>
    				  	 	</div>
    				  	</div>
    						
    						<div class="form-group">
    		      		<div class="col-sm-12">
    		      			<button name="save_btn1" class="btn btn-success" type="submit" onMouseOver="mouse_move(&#039;b_apply&#039;);" onMouseOut="mouse_move();"><i class="fa fa-check" ></i> Save</button>
    		      			<button name="cancel_btn1" class="btn btn-primary" type="submit" onMouseOver="mouse_move(&#039;b_cancel&#039;);" onMouseOut="mouse_move();" formnovalidate><i class="fa fa-times"></i> Cancel</button>
    		      		</div>
    		      	</div>
    								
    					</div> <!-- END PANEL BODY --> 
    		  	</div> <!-- END PANEL WRAPPER --> 
    		  </div>  <!-- END COL --> 
    		</div> <!-- END ROW -->
    		<br>		
    		<div class="row">
    			<div class="col-sm-12">
    		  	<div class="hpanel4">
    		  		<div class="panel-body" style="max-width:600px">				
    						<legend><img src="images/shell_interface.gif"> Shell Interface Password</legend> 
    						
    						<div class="form-group"><label class="col-sm-4 control-label" style="color:blue; min-width:220px">Type New Shell Password:</label>
    		      		<div class="col-sm-8" style="max-width:350px">
    		      			<input type="password" class="form-control" name='shell_pass1' />
    		      		</div>
    		      	</div>
    						
    						<div class="form-group"><label class="col-sm-4 control-label" style="color:green; min-width:220px">Confirm New Shell Password:</label>
    		      		<div class="col-sm-8" style="max-width:350px">
    		      			<input type="password" class="form-control" name='shell_pass2' />
    		      		</div>
    		      	</div>
								
								<br>
								
								<div class="form-group">
    		      		<div class="col-sm-12">
    		      			<button name="save_btn2" class="btn btn-success" type="submit" onMouseOver="mouse_move(&#039;b_apply&#039;);" onMouseOut="mouse_move();"><i class="fa fa-check" ></i> Save</button>
    		      			<button name="cancel_btn2" class="btn btn-primary" type="submit" onMouseOver="mouse_move(&#039;b_cancel&#039;);" onMouseOut="mouse_move();" formnovalidate><i class="fa fa-times"></i> Cancel</button>
    		      		</div>
    		      	</div>		
  								 
    		  		</div> <!-- END PANEL BODY --> 
    		  	</div> <!-- END PANEL WRAPPER --> 
    		  </div>  <!-- END COL --> 
    		</div> <!-- END ROW -->
    	</fieldset>
    </form>	 
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




