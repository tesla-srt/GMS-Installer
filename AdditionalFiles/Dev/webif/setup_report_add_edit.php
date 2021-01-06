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
	$id = "";
	$name = "";
	$body = "";
	$desc = "";
	$from = "";
	$smtp = "";
	$to = "";
	$subject = "";
	$port = "25";
	$auth_check = " ";
	$username = "";
	$password = "";
	$ssl = " ";
	$tls = " ";
	
/////////////////////////////////////////////////////////////////
//                                                             //
//                    GET PROCESSING                           //
//                                                             //
/////////////////////////////////////////////////////////////////
	
if(isset ($_GET['action']))
{
	$action = $_GET['action'];
	if($action == "edit")
	{
		$type = $_GET['type'];
		$id = $_GET['id'];
		$title = "Edit Email Report - ID# ".$id;
		$query = sprintf("SELECT * FROM alerts WHERE id='%s';", $id);
		$result  = $dbh->query($query);
		foreach($result as $row)
		{
			$name = $row['name'];
			$type = $row['type'];
			$desc = $row['desc'];
			$to = $row['v1'];
			$subject = $row['v2'];
			$smtp = $row['v3'];
			$body = $row['v4'];
			$from = $row['v5'];
			$auth_check = $row['v6'];
			$username = $row['v7'];
			$password = $row['v8'];
			$ssl = $row['v9'];
			$tls = $row['v10'];
			$port = $row['port'];
			
		}
	}
	else
	{
		$title = "Add New Email Report";
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
	header("Location: setup_notifications.php");
}

	
// OK Button	was clicked
if(isset ($_POST['save_btn']))
{	
	$type = "REPORT";
	$id = $_POST['id'];
	$action = $_POST['action'];
	$name = $_POST['name'];
	$desc = $_POST['desc'];
	$from = $_POST['from'];
	$smtp = $_POST['smtp'];
	$to = $_POST['to'];
	$subject = $_POST['subject'];
	$body = $_POST['body'];
	$port = $_POST['port'];
	if(isset($_POST['auth_check']))
	{
		$auth_check = "CHECKED";
		$username = $_POST['username'];
		$password = $_POST['password'];
	}
	else
	{
		$auth_check = " ";
		$username = "";
		$password = "";
	}
	
	
	if(isset($_POST['auth_group']))
	{
		$auth_group = $_POST['auth_group'];
		if($auth_group == "starttls")
		{
			$ssl = " ";
			$tls = "CHECKED";
		}
		else
		{
			$ssl = "CHECKED";
			$tls = " ";
		}
	}
	
	if($action == "add")
	{
		$query = sprintf("INSERT INTO alerts VALUES (NULL, '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s');", $type, $name, $desc, $to, $subject, $smtp, $body, $from, $auth_check, $username, $password, $ssl, $tls, $port);
		$result  = $dbh->exec($query);
		header("Location: setup_notifications.php?alert_flag=1");
	}
	
	if($action == "edit")
	{
		$query = sprintf("UPDATE alerts SET name='%s', desc='%s', v1='%s', v2='%s', v3='%s', v4='%s', v5='%s', v6='%s', v7='%s', v8='%s', v9='%s', v10='%s', port='%s' WHERE id='%d';",$name, $desc, $to, $subject, $smtp, $body, $from, $auth_check, $username, $password, $ssl, $tls, $port, $id);
		//echo $query;
		$result  = $dbh->exec($query);
		header("Location: setup_notifications.php?alert_flag=1");
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
    <link rel="stylesheet" href="css/jquery.bootstrap-touchspin.min.css" />
    <link rel="stylesheet" href="css/awesome-bootstrap-checkbox.css" />
    <link rel="stylesheet" href="css/sweetalert.css" />
    <link rel="stylesheet" href="css/ethertek.css">
    
    <!-- Java Scripts -->
		<script src="javascript/jquery.min.js"></script>
		<script src="javascript/bootstrap.min.js"></script>
		<script src="javascript/jquery.bootstrap-touchspin.min.js"></script>
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
      	  	<form name='Reports' action='setup_report_add_edit.php' method='post' class="form-horizontal"">  	
      	    	<fieldset>
      	    		<legend><img src="images/btn_email_add_bg.gif"> <?php echo $title; ?></legend> 
      	    		
      	    		<div class="form-group"><label class="col-sm-3 control-label" style="text-align:left; max-width:140px; min-width:140px">Report Name:</label>
              		<div class="col-sm-12 input-group" style="max-width:350px">
              			<span class="input-group-addon"><i class="fa fa-info" style="max-width:16px; min-width:16px"></i></span>
              			<input type="text" class="form-control" name='name' value='<?php echo $name; ?>' required/>
              		</div>
              	</div>	    	    	
								
								<div class="form-group"><label class="col-sm-3 control-label" style="text-align:left; max-width:140px; min-width:140px">Description:</label>
              		<div class="col-sm-12 input-group" style="max-width:350px">
              			<span class="input-group-addon"><i class="fa fa-question" style="max-width:16px; min-width:16px"></i></span>
              			<input type="text" class="form-control" name='desc' value='<?php echo $desc; ?>' required/>
              		</div>
              	</div>
								
								<div class="form-group"><label class="col-sm-3 control-label" style="text-align:left; max-width:140px; min-width:140px">From:</label>
              		<div class="col-sm-12 input-group" style="max-width:350px">
              			<span class="input-group-addon"><i class="fa fa-envelope" style="max-width:16px; min-width:16px"></i></span>
              			<input type="text" class="form-control" name='from' value='<?php echo $from; ?>' required/>
              		</div>
              	</div>
								
								<div class="form-group"><label class="col-sm-3 control-label" style="text-align:left; max-width:140px; min-width:140px">SMTP Server:</label>
              		<div class="col-sm-12 input-group" style="max-width:350px">
              			<span class="input-group-addon"><i class="fa fa-desktop" style="max-width:16px; min-width:16px"></i></span>
              			<input type="text" class="form-control" name='smtp' value='<?php echo $smtp; ?>' required/>
              		</div>
              	</div>
								
								<div class="form-group"><label class="col-sm-3 control-label" style="text-align:left; max-width:140px; min-width:140px">To:</label>
              		<div class="col-sm-12 input-group" style="max-width:350px">
              			<span class="input-group-addon"><i class="fa fa-envelope" style="max-width:16px; min-width:16px"></i></span>
              			<input type="text" class="form-control" name='to' value='<?php echo $to; ?>' required/>
              		</div>
              	</div>
								
								<div class="form-group"><label class="col-sm-3 control-label" style="text-align:left; max-width:140px; min-width:140px">Subject:</label>
              		<div class="col-sm-12 input-group" style="max-width:350px">
              			<span class="input-group-addon"><i class="fa fa-question-circle" style="max-width:16px; min-width:16px"></i></span>
              			<input type="text" class="form-control" name='subject' value='<?php echo $subject; ?>' required/>
              		</div>
              	</div>
								
								<div class="form-group"><label class="col-sm-3 control-label" style="text-align:left; max-width:140px; min-width:140px">Email Body:</label>
              		<div class="col-sm-12 input-group" style="max-width:350px">
              			<span class="input-group-addon"><i class="fa fa-comment" style="max-width:16px; min-width:16px"></i></span>
              			<textarea  rows="2" cols="50" class="form-control" name='body' required><?php echo $body; ?></textarea>
              		</div>
              	</div>
								
								<legend> Email Server Settings</legend> 
								
								<div class="form-group"><label class="col-sm-3 control-label" style="text-align:left; max-width:125px; min-width:125px">SMTP Port:</label>
              		<div class="col-sm-12" style="max-width:180px" onMouseOver="mouse_move('sd_timers_info');" onMouseOut="mouse_move();">
              			<input id="port" type="text" name="port" style="text-align:center" value="<?php echo $port; ?>">
              		</div>
              	</div>
								
								<div class="form-group"><label class="col-sm-3 control-label" style="text-align:left; max-width:140px; min-width:140px"></label>
              		<div class="col-sm-7 checkbox checkbox-success">
              			<input type='checkbox' onclick='validate();' id='auth_check' name='auth_check' <?php echo $auth_check; ?> />
    		            <label for='auth_check'><strong>Use Authorization ?</strong></label>
              		</div>
              	</div>
								
								<div class="form-group"><label class="col-sm-3 control-label" style="text-align:left; max-width:140px; min-width:140px">Username:</label>
              		<div class="col-sm-12 input-group" style="max-width:350px">
              				<span class="input-group-addon"><i class="fa fa-user" style="max-width:16px; min-width:16px"></i></span>
              				<?php
              					if($auth_check == "CHECKED")
              					{
              						echo "<input type='text' class='form-control' id='username' name='username' value='".$username."' required/>";
              					}
              					else
              					{
              						echo "<input type='text' class='form-control' id='username'name='username' value='".$username."' disabled/>";
              					}
              				?>
              		</div>
              	</div>
								
								<div class="form-group"><label class="col-sm-3 control-label" style="text-align:left; max-width:140px; min-width:140px">Password:</label>
              		<div class="col-sm-12 input-group" style="max-width:350px">
              				<span class="input-group-addon"><i class="fa fa-sign-in" style="max-width:16px; min-width:16px"></i></span>
              				<?php
              					if($auth_check == "CHECKED")
              					{
              						echo "<input type='password' class='form-control' id='password' name='password' value='".$password."' required/>";
              					}
              					else
              					{
              						echo "<input type='password' class='form-control' id='password' name='password' value='".$password."' disabled />";
              					}
              				?>
              		</div>
              	</div>
								
								<div class="form-group"><label class="col-sm-3 control-label" style="text-align:left; max-width:140px; min-width:140px"></label>
              		<div class="col-sm-7 radio radio-success">
              				<?php
    				  					if($auth_check == "CHECKED")
    				  					{
    				  						echo '<input type="radio" id="auth_group" name="auth_group" value="starttls" '.$tls.'/>';
    				  					}
    				  					else
    				  					{
    				  						echo '<input type="radio" id="auth_group" name="auth_group" value="starttls" CHECKED disabled/>';
    				  					}
    				  				?>
                      <label for="auth_group"><strong>Use StartTLS ?</strong></label>
                    </div>
              	</div>
								
								<div class="form-group"><label class="col-sm-3 control-label" style="text-align:left; max-width:140px; min-width:140px"></label>
              		<div class="col-sm-7 radio radio-success">
              				<?php
    				  					if($auth_check == "CHECKED")
    				  					{
    				  						echo '<input type="radio" id="auth_group" name="auth_group" value="ssl" '.$ssl.'/>';
    				  					}
    				  					else
    				  					{
    				  						echo '<input type="radio" id="auth_group" name="auth_group" value="ssl" '.$ssl.' disabled/>';
    				  					}
    				  				?>
                      <label for="auth_group"><strong>Use SSL ?</strong></label>
                    </div>
              	</div>
														
              	<div class="form-group">
              		<div class="col-sm-12">
              		<input type="hidden" name="action" value="<?php echo $action; ?>">
              		<input type="hidden" name="id" value="<?php echo $id; ?>">
              		<input type="hidden" name="type" value="<?php echo $type; ?>">
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

<script>
	
function validate() 
{
        if (document.getElementById('auth_check').checked) 
        {
            $("#username").attr('required',true);
            $("#password").attr('required',true);
            $('#username').removeAttr('disabled');
            $('#password').removeAttr('disabled');
            $('input:radio').removeAttr('disabled');
            
        } 
        else 
        {
            $("#username").attr('required',false);
            $("#password").attr('required',false);
            $('#username').attr('disabled', true);
            $('#password').attr('disabled', true);
            $('input:radio').attr('disabled', true);
        }
}
</script>	
	
	
<script>	
$(function(){
    $("#port").TouchSpin({
        min: 1,
        max: 65536,
        step: 1,
        decimals: 0,
        boostat: 5,
        maxboostedstep: 10,
    });
});

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
