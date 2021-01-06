<?php
	error_reporting(E_ALL);
	include "lib.php";
	$hostname = trim(file_get_contents("/etc/hostname"));
	$alert_flag = "0";
	$id = "0";
	$all = "";
	$sel = "";
	
	$dbh = new PDO('sqlite:/etc/rms100.db');
	
	$result  = $dbh->query("SELECT * FROM display_options;");			
	foreach($result as $row)
	{
		$screen_animations = $row['screen_animations'];
	}
	
//	$pageWasRefreshed = isset($_SERVER['HTTP_CACHE_CONTROL']) && $_SERVER['HTTP_CACHE_CONTROL'] === 'max-age=0';
//	if($pageWasRefreshed ) 
//	{
//   	//page was refreshed;
//   	goto escape_hatch;
//	}
	
		
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
		$success = $_GET['success'];
		if($success = "yes")
		{
			$myid = $_GET['id'];
			$text = "Firewall Rule # " . $myid . " Updated";		   						
			$alert_flag = "2";
		}	
	}
	else if($action == "add")
	{
		$success = $_GET['success'];
		if($success = "yes")
		{
			$text = "New Firewall Rule Added";		   						
			$alert_flag = "2";
		}	
	}
	else if($action == "delete")
	{
		$id = $_GET['id'];
		$alert_flag = "5";
	}
	else if($action == "enable")
	{
		$id = $_GET['id'];
		$flag = $_GET['flag'];
		if($flag == "on")
		{
			$flag = "off";
			$query = sprintf("UPDATE firewall SET enabled='%s' WHERE id=%d", $flag, $id);
			$result  = $dbh->exec($query);
			$text = "Firewall Rule #".$id." Disabled";
			$alert_flag = "2";
		}
		else
		{
			$flag = "on";
			$query = sprintf("UPDATE firewall SET enabled='%s' WHERE id=%d", $flag, $id);
			$result  = $dbh->exec($query);
			$text = "Firewall Rule #".$id." Enabled";
			$alert_flag = "2";
		}
		$query = "SELECT * FROM firewall_control";
		$result  = $dbh->query($query);
		foreach($result as $row)
		{
			$is_enabled = $row['enabled'];
		}
		if($is_enabled == "SEL")
		{
			fw_apply();
		}	
	} 
	goto escape_hatch;
}




	


if(isset ($_GET['confirm']))
{
	$action = $_GET['confirm'];
	if($action == "enable")
	{
		fw_apply();
		$result  = $dbh->exec("UPDATE firewall_control SET enabled='SEL' WHERE id=1");
		$text = "Firewall Enabled";				
		$alert_flag = "2";
	}
	else if($action == "disable")
	{
		exec("touch /tmp/fw.start");
		$fp = fopen('/tmp/fw.stop', 'w');
		fwrite($fp,"iptables -P INPUT ACCEPT\n");
		fwrite($fp,"iptables -P FORWARD ACCEPT\n");
		fwrite($fp,"iptables -P OUTPUT ACCEPT\n");
		fwrite($fp,"iptables -F\n");
  	fclose($fp);
  	system("sh /tmp/fw.stop");
		unlink("/tmp/fw.stop");
		$result  = $dbh->exec("UPDATE firewall_control SET enabled='ALL' WHERE id=1");
		$text = "Firewall Disabled";
		$alert_flag = "2";
	}

	else if($action == "delete")
	{
		$id = $_GET['id'];
		$query = sprintf("DELETE FROM firewall WHERE id=%d;",$id);
		$result  = $dbh->exec($query);
		$query = "SELECT * FROM firewall_control";
		$result  = $dbh->query($query);
		foreach($result as $row)
		{
			$is_enabled = $row['enabled'];
		}
		if($is_enabled == "SEL")
		{
			fw_apply();
		}	
		$text = "Firewall Rule #".$id." Deleted!";
		$alert_flag = "2";
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
	header("Location: setup.php");
}

if(isset ($_POST['apply_btn']))
{
	$status = $_POST['group1'];
	if($status == "ALL")
	{
		$alert_flag = "4";
	}
	else
	{
		$alert_flag = "3";
	}
}


escape_hatch:
$result  = $dbh->query("SELECT * FROM firewall_control;");
foreach($result as $row)
{
	$enabled = $row['enabled'];
}

if($enabled == "ALL")
{
	$all = "checked";
	$sel = " ";
	$sd_html = "<img src='images/fw_disabled.gif' title='DISABLED'><span style='color:red'>&nbsp;&nbsp;<b>Firewall Disabled !</b></span>";
}
else
{
	$all = " ";
	$sel = "checked";
	$sd_html = "<img src='images/fw_enabled.gif' title='ENABLED'><span style='color:green'>&nbsp;&nbsp;<b>Firewall Enabled !</b></span>";
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
			echo '<div class="content animate-panel" data-effect="fadeInRightBig">';
		}
		else
		{
			echo '<div class="content">';
		}
	?>
  	<!-- INFO BLOCK START -->
  	<div class="row">
  		<div class="col-sm-12"><legend>Firewall Rules</legend></div>
  	</div>
  	<div class="row">
  		<div class="col-sm-12" style="text-align:left"><a href="setup_firewall_add_edit.php?action=add" onMouseOver="mouse_move('sd_firewall_add');" onMouseOut='mouse_move();'><img src='images/firewalladd.gif' title='Add New Firewall Rule'></a></div>
  	</div>
  	<div class="row">
  		<div class="col-sm-12" style="text-align:left"><a href="setup_firewall_add_edit.php?action=add" onMouseOver="mouse_move('sd_firewall_add');" onMouseOut='mouse_move();'><h5><u class="dotted">Add New Firewall Rule</u></h5></a></div>
  	</div>
  	<br>
  	
		<legend></legend>
		
			
		
  	<form name='Firewall' action='setup_firewall.php' method='post' class="form-horizontal">  	
    	<fieldset>
  			<div class="row">
    			<div class="col-sm-12">
    		  	<div class="hpanel3">
    		  	  <div class="panel-body" style="text-align:left; background:#F1F3F6;border:none;">
    		  	  	<div class="col-sm-12">
    		  	  		<div class="form-group">
    		  	  			<?php echo $sd_html; ?>
    		  	  		</div>
    		  	  	</div>
              	<div class="col-sm-12">
              		<div class="form-group">
              			<div class="radio radio-danger">
              				<input id='active1' type='radio' value='ALL' name='group1' <?php echo $all; ?> />
                      <label for="active1">Allow ALL IPs to connect to the GMS-100 board.</label>     
                    </div>
                  </div>
                </div>
                   
                <div class="col-sm-12">
                	<div class="form-group">
                    <div class="radio radio-success">
              				<input id='active2' type='radio' value='SEL' name='group1' <?php echo $sel; ?>  />
                      <label for="active2">Allow only the IP's below to connect to the GMS-100 board.</label>     
                    </div>
                  </div>
              	</div>	
              	
        				<div class="col-sm-12">
        					<div class="form-group">
        						<button name="apply_btn" class="btn btn-success" type="submit" onMouseOver="mouse_move(&#039;b_cancel&#039;);" onMouseOut="mouse_move();" formnovalidate><i class="fa fa-check"></i> Apply</button>
        						<button name="cancel_btn" class="btn btn-primary" type="submit" onMouseOver="mouse_move(&#039;b_cancel&#039;);" onMouseOut="mouse_move();" formnovalidate><i class="fa fa-times"></i> Cancel</button>
        					</div>
        				</div>
        				<p style="color:red">
        					<strong>Warning:</strong> Improper settings could make you lose connectivity!!<br>
									<strong>Note:</strong> If you add Firewall Rules after the Firewall is enabled, you will have to hit APPLY again.
        				</p>
    				  	<legend></legend>
    				  	
    				  	
    				  	
    				  	<div class="table-responsive">
    				   		<table width="100%" class="table table-striped table-hover">
    				   			<thead>
    				   				<tr>
    				   					<th width="10%" style="background:#ABBEEF; border: 1px solid white;">
    				   						<div style="text-align:center">Enabled</div>
    				   					</th>
    				   					<th width="15%" style="background:#D6DFF7; border: 1px solid white;">
    				   						<div style="text-align:left">Allowed IP</div>
    				   					</th>
    				   					<th width="50%" style="background:#D6DFF7; border: 1px solid white;">
    				   						<div style="text-align:left">Description</div>
    				   					</th>
    				   					<th width="25%" style="background:#D6DFF7; border: 1px solid white;">
    				   						<div style="text-align:left">Actions</div>
    				   					</th>
    				   				</tr>
    				   			</thead>
    				   			<tbody>
    				   				<?php
												$result  = $dbh->query("SELECT * FROM firewall ORDER BY id;");
												foreach($result as $row)
												{
													$id = $row['id'];
													$ip = $row['ip'];
													$desc = $row['desc'];
													$enabled = $row['enabled'];
													
													echo "<tr>";
													echo "	<td style='text-align:center'>";
													echo "		<a href='setup_firewall.php?action=enable&id=".$id."&flag=".$enabled."'>";
													echo "		<img src='images/serv".$enabled.".gif' width='16' height='16' title='Enable or Disable IP'></a>";
													echo "	</td>";
													echo "	<td style='text-align:left'>";
													echo "		<span title='Edit IP'><a href='setup_firewall_add_edit.php?action=edit&id=".$id."'><u class='dotted'>".$ip."</u></a></span>";
													echo "	</td>";
													echo "	<td style='text-align:left'>";
													echo 			$desc;
													echo "	</td>";
													echo "	<td style='text-align:left'>";
													echo "		<a href='setup_firewall.php?action=delete&id=".$id."' onMouseOver ='mouse_move(\"sd_firewall_delete\");'	onMouseOut='mouse_move();'>";
													echo "		<img  src='images/off.gif' width='16' height='16' title='DELETE RULE'></a>";
													echo "	</td>";
													echo "</tr>";
												}	
													
													
												
    				   				?>
    				   			</tbody>
  								</table>      	    
    		  	  	</div> <!-- END TABLE RESPONSIVE -->
    		  	  	
    		  		</div> <!-- END PANEL BODY --> 
    		  	</div> <!-- END HPANEL3 --> 
    		  </div> <!-- END COL-MD-12 --> 
    		</div> <!-- END ROW -->
    	</fieldset>
    </form>			
  </div> <!-- END CONTENT -->    
</div> <!-- END Main Wrapper -->



<?php 
if($alert_flag == "1")
{
echo"<script>";
echo"swal({";
echo"  title:'Success!',";
echo"  text: 'IP Deleted!',";
echo"  type: 'success',";
echo"  showConfirmButton: false,";
echo"  timer: 2500";
echo"});";
echo"</script>";
}

if($alert_flag == "2")
{
echo"<script>";
echo"swal({";
echo"  title:'Success!',";
echo"  text: '" . $text . "',";
echo"  type: 'success',";
echo"  showConfirmButton: false,";
echo"	 html: true,";
echo"  timer: 2500";
echo"});";
echo"</script>";
}

if($alert_flag == "3")
{
	echo"<script>";
	echo"	swal({";
	echo"		title: 'Enable Firewall<br>Are you really sure?',";
	echo"		type: 'warning',";
	echo"		showCancelButton: true,";
	echo"		html: true,";
	echo"		confirmButtonColor: '#DD6B55',";
	echo"		confirmButtonText: 'Yes, enable it!',";
	echo"		closeOnConfirm: false";
	echo"	},";
	echo"	function(){";
	echo"		window.location.href = 'setup_firewall.php?confirm=enable';";
	echo"	});";
	echo"</script>";
}

if($alert_flag == "4")
{
	echo"<script>";
	echo"	swal({";
	echo"		title: 'Disable Firewall<br>Are you really sure?',";
	echo"		type: 'warning',";
	echo"		showCancelButton: true,";
	echo"		html: true,";
	echo"		confirmButtonColor: '#DD6B55',";
	echo"		confirmButtonText: 'Yes, turn it off!',";
	echo"		closeOnConfirm: false";
	echo"	},";
	echo"	function(){";
	echo"		window.location.href = 'setup_firewall.php?confirm=disable';";
	echo"	});";
	echo"</script>";
}

if($alert_flag == "5")
{
	echo"<script>";
	echo"	swal({";
	echo"		title: 'Delete Firewall Rule #".$id."<br>Are you really sure?',";
	echo"		type: 'warning',";
	echo"		showCancelButton: true,";
	echo"		html: true,";
	echo"		confirmButtonColor: '#DD6B55',";
	echo"		confirmButtonText: 'Yes, delete it!',";
	echo"		closeOnConfirm: false";
	echo"	},";
	echo"	function(){";
	echo"		window.location.href = 'setup_firewall.php?confirm=delete&id=".$id."';";
	echo"	});";
	echo"</script>";
}

echo "</body>";
echo "</html>";

function fw_apply()
{
	global $dbh;
	
	exec("touch /tmp/fw.start");
	$fp = fopen('/tmp/fw.start', 'w');
	
	fwrite($fp,"iptables -P FORWARD DROP\n");
	fwrite($fp,"iptables -F\n");
	fwrite($fp,"iptables -A INPUT -i lo -j ACCEPT\n");
	fwrite($fp,"iptables -A INPUT -m state --state RELATED,ESTABLISHED -j ACCEPT\n");
	
	$query = "SELECT * FROM firewall ORDER BY id";
	$result  = $dbh->query($query);
	foreach($result as $row)
	{
		$is_enabled = $row['enabled'];
		$the_ip = $row['ip'];
		if($is_enabled == "on")
		{
			$rule = sprintf("iptables -A INPUT -p all -s %s -j ACCEPT\n",$the_ip);
			fwrite($fp, $rule);
		}
	}
	fwrite($fp,"iptables -P INPUT DROP\n");
	fclose($fp);
	exec("sh /tmp/fw.start"); 
	//unlink("/tmp/fw.start");	
}


?>









 
















