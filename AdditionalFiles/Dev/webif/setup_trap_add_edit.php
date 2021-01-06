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
$type = "SNMP";
$snmp_version = "v1";
$trap_name = "";
$trap_desc = "";
$trap_ip = "";
$trap_user = "";
$trap_pass = "";
$trap_num = "";
$trap_message = "";
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
		$title = "Edit SNMP Trap ID# ".$id;
		$sd_query = sprintf("SELECT * FROM alerts WHERE id='%s';",$id);
		$result  = $dbh->query($sd_query);
		foreach($result as $row)
		{
			$trap_name = $row['name'];
			$trap_desc = $row['desc'];
			$trap_ip = $row['v1'];
			$trap_user = $row['v2'];
			$trap_pass = $row['v3'];
			$trap_num = $row['v4'];
			$trap_message = $row['v5'];
			$snmp_version = $row['v6'];
		}
	}
	else
	{
		$title = "ADD New SNMP Trap";
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

// Radio buttons clicked
if(isset($_POST['snmpver']))
{
	$snmp_version = $_POST['snmpver'];
	$trap_name = $_POST['trap_name'];
	$trap_desc = $_POST['trap_desc'];
	$trap_ip = $_POST['trap_ip'];
	if(isset($_POST['trap_user']))
		{
			$trap_user = $_POST['trap_user'];
		}
	else
		{
			$trap_user = "";
		}	
	$trap_pass = $_POST['trap_pass'];
	if(isset($_POST['trap_num']))
		{
			$trap_num = $_POST['trap_num'];
		}
	else
		{
			$trap_num = "";
		}
	$trap_message = $_POST['trap_message'];
	if(isset($_POST['id']))
		{
			$id = $_POST['id'];
			if($id == "")
			{
				$action = "add";
			}
			else
			{
				$action = "edit";
			}
			
		}
	else
		{
			$id = "";
			$action = "add";
		}	
}

//Cancel button clicked
if(isset($_POST['cancel_btn']))
{
	header("Location: setup_notifications.php");
}


// Save SNMP Trap Button was clicked
	if(isset($_POST['save_btn']))
	{
		$snmp_version = $_POST['snmpver'];
		$trap_name = $_POST['trap_name'];
		$trap_desc = $_POST['trap_desc'];
		$trap_ip = $_POST['trap_ip'];
		$action = $_POST['action'];
		$id = $_POST['id'];
		
		if(isset($_POST['trap_user']))
			{
				$trap_user = $_POST['trap_user'];
			}
		else
			{
				$trap_user = "";
			}	
		$trap_pass = $_POST['trap_pass'];
		if(isset($_POST['trap_num']))
			{
				$trap_num = $_POST['trap_num'];
			}
		else
			{
				$trap_num = "";
			}
		$trap_message = $_POST['trap_message'];
		if (filter_var($trap_ip, FILTER_VALIDATE_IP))
			{
				if($action == "add")
				{
					$sd_query = sprintf("INSERT INTO alerts (id, type, name, desc, v1, v2, v3, v4, v5, v6, v7, v8, v9, v10, port) VALUES (NULL, '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s');",$type,$trap_name,$trap_desc,$trap_ip,$trap_user,$trap_pass,$trap_num,$trap_message,$snmp_version,"","","","","");
					$result  = $dbh->exec($sd_query);
					header("Location: setup_notifications.php?alert_flag=1");
				}
				else
				{
					$sd_query = sprintf("UPDATE alerts SET name='%s',desc='%s',v1='%s',v2='%s',v3='%s',v4='%s',v5='%s',v6='%s',v7='',v8='',v9='',v10='',port='' WHERE id='%s';",$trap_name,$trap_desc,$trap_ip,$trap_user,$trap_pass,$trap_num,$trap_message,$snmp_version,$id);
					$result  = $dbh->exec($sd_query);
					header("Location: setup_notifications.php?alert_flag=1");
				}	
			}
		else
			{
				$text = "Invalid IP Address!";
				$alert_flag = "2";
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
      	  	<form name='trapform' action='setup_trap_add_edit.php' method='post' class="form-horizontal"">  	
      	    	<fieldset>
      	    		<legend><img src="images/snmp_add.gif"> <?php echo $title; ?></legend>  	
  									
  									<?php
  										if($snmp_version == "v1")
  										{
  											$v1_check = "checked";
  										}
  										else
  										{
  											$v1_check = " ";
  										}
  										echo '<div class="form-group">';
  										echo '	<div class="col-sm-3" style="max-width:120px; min-width:120px">';
              				echo '		<div class="radio radio-success">';
              				echo '			<input type="radio" id="v1" name="snmpver" value="v1"  onclick="javascript: submit();" '.$v1_check.' />';
    		            	echo '			<label for="v1">SNMP V1 &nbsp;&nbsp;&nbsp;&nbsp;</label>';
    		            	echo '		</div>';
              				echo '	</div>';
  										
  										
  										if($snmp_version == "v2")
  										{
  											$v2_check = "checked";
  										}
  										else
  										{
  											$v2_check = " ";
  										}
  										echo '	<div class="col-sm-3" style="max-width:120px; min-width:120px">';
              				echo '		<div class="radio radio-success">';
              				echo '			<input type="radio" id="v2" name="snmpver" value="v2"  onclick="javascript: submit();" '.$v2_check.' />';
    		            	echo '			<label for="v2">SNMP V2 &nbsp;&nbsp;&nbsp;&nbsp;</label>';
    		            	echo '		</div>';
              				echo '	</div>';
  										
  										
  										
  										if($snmp_version == "v3")
  										{
  											$v3_check = "checked";
  										}
  										else
  										{
  											$v3_check = " ";
  										}
  										echo '	<div class="col-sm-3" style="max-width:120px; min-width:120px">';
              				echo '		<div class="radio radio-success">';
              				echo '			<input type="radio" id="v3" name="snmpver" value="v3"  onclick="javascript: submit();" '.$v3_check.' />';
    		            	echo '			<label for="v3">SNMP V3 &nbsp;&nbsp;&nbsp;&nbsp;</label>';
    		            	echo '		</div>';
              				echo '	</div>';
              				echo '</div>';
  									?>
    					

    								<div class="form-group"><label class="col-sm-3 control-label" style="text-align:left; max-width:110px; min-width:110px">Trap Name:</label>
              				<div class="col-sm-12" style="max-width:450px">
              					<input type="text" class="form-control" name='trap_name' value='<?php echo $trap_name; ?>' placeholder="Choose a Name for this SNMP Trap" required/>
              				</div>
              			</div>
    								
    								<div class="form-group"><label class="col-sm-3 control-label" style="text-align:left; max-width:110px; min-width:110px">Description:</label>
              				<div class="col-sm-12" style="max-width:450px">
              					<input type="text" class="form-control" name='trap_desc' value='<?php echo $trap_desc; ?>' placeholder="Describe this SNMP Trap" required/>
              				</div>
              			</div>
    								
    								<div class="form-group"><label class="col-sm-2 control-label" style="text-align:left; max-width:110px; min-width:110px">Server IP:</label>
              				<div class="col-sm-12" style="max-width:450px">
              					<input type="text" class="form-control" name='trap_ip' value='<?php echo $trap_ip; ?>' placeholder="Enter the SNMP Trap Server IP Address" required/>
              				</div>
              			</div>
    								
    								<?php
  										if($snmp_version == "v3")
  										{
  											echo '<div class="form-group"><label class="col-sm-2 control-label" style="text-align:left; max-width:110px; min-width:110px">Username:</label>';
              					echo '	<div class="col-sm-12" style="max-width:450px">';
              					echo '		<input type="text" class="form-control" name="trap_user" value="'.$trap_user.'" placeholder="Enter the SNMPv3 Trap Server Username" required/>';
              					echo '	</div>';
              					echo '</div>';
  										}
  									?>
    								
    								<div class="form-group"><label class="col-sm-2 control-label" style="text-align:left; max-width:110px; min-width:110px">Password:</label>
              				<div class="col-sm-12" style="max-width:450px">
              					<input type="password" class="form-control" name='trap_pass' value='<?php echo $trap_pass; ?>' placeholder="Enter the SNMP Trap Server Password" required/>
              				</div>
              			</div>
              			
    								<?php
  										if($snmp_version == "v1")
  										{
  											echo '<div class="form-group"><label class="col-sm-2 control-label" style="text-align:left; max-width:110px; min-width:110px">Trap #</label>';
              					echo '	<div class="col-sm-12" style="max-width:450px">';
              					echo '		<input type="text" class="form-control" name="trap_num" value="'.$trap_num.'" placeholder="Choose a Number that means something to you" required/>';
              					echo '	</div>';
              					echo '</div>';
  										}
  									?>
    								
    								<div class="form-group"><label class="col-sm-2 control-label" style="text-align:left; max-width:110px; min-width:110px">Message:</label>
              				<div class="col-sm-12" style="max-width:450px">
              					<textarea cols="40" rows="5" maxlength="511" class="form-control" name='trap_message' placeholder="Enter a message to send to the SNMP Trap Server" required/><? echo $trap_message; ?></textarea>
              				</div>
              			</div>
    								
  									<br />
										
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




