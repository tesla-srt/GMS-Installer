<?php
	error_reporting(E_ALL);
	include "lib.php";
	$hostname = trim(file_get_contents("/etc/hostname"));
	$alert_flag = "0";
	$id = "0";
	$myid = "";
	$confirmation = "";
	$dbh = new PDO('sqlite:/etc/rms100.db');
	
	$result  = $dbh->query("SELECT * FROM display_options;");			
	foreach($result as $row)
	{
		$screen_animations = $row['screen_animations'];
	}
	
	$query = "SELECT * FROM alertconf";
	$result  = $dbh->query($query);
	foreach($result as $row)
	{
		$confirmation = $row['alert_confirmation'];	
	}
	if($confirmation == "1")
	{
		$confirmation = "checked";
	}
	else
	{
		$confirmation = " ";
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
	
	if(isset ($_GET['alert_flag']))
	{
		$alert_flag = "1";
	}
	
	if(isset ($_GET['action']))
	{
		$action = $_GET['action'];
		$myid = $_GET['id'];
		$id = $myid;
		if($action == "run")
		{
			if($confirmation == "checked")
			{
				$alert_flag = "3";
			}
			else
			{
				$command = "rmsalert ".$myid." > /dev/null 2>&1 &";
				exec($command);
			}
		}
		else if($action == "delete")
		{
			$alert_flag = "4";
		}
	}

if(isset ($_GET['confirm']))
	{
		$confirm = $_GET['confirm'];
		$id = $_GET['id'];
		if($confirm == "run")
		{
			$command = "rmsalert ".$id." > /dev/null 2>&1 &";
			exec($command);
			$text = "Alert ID #".$id." has been executed!";
			$alert_flag = "2";
		}
		else if($confirm == "delete")
		{
			$query = sprintf("DELETE FROM alerts WHERE id='%s';",$id);
			$result = $dbh->exec($query);
			z_seek_destroy_alert_refs($id);
			restart_some_services();
			$text = "Alert ID #".$id." has been Deleted!";
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

// Confirmation Button	was clicked
if(isset ($_POST['confirmation_btn']))
{
	if(isset($_POST['action_check']))
	{
		$query = "UPDATE alertconf SET alert_confirmation='1';";
		$result = $dbh->exec($query); 
	}
	else
	{
		$query = "UPDATE alertconf SET alert_confirmation='0';";
		$result = $dbh->exec($query);
	}
	
	$query = "SELECT * FROM alertconf";
	$result  = $dbh->query($query);
	foreach($result as $row)
	{
		$confirmation = $row['alert_confirmation'];	
	}
	if($confirmation == "1")
	{
		$confirmation = "checked";
	}
	else
	{
		$confirmation = " ";
	}

}


escape_hatch:



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
			echo '<div class="content animate-panel" data-effect="fadeInRightBig">';
		}
		else
		{
			echo '<div class="content">';
		}
	?>
		<form name='Scripts' action='setup_notifications.php' method='post' class="form-horizontal">  	
    	<fieldset>
  			<!-- INFO BLOCK START -->
  			<div class="row">
  				<div class="col-sm-12"><legend>Alerts Interface</legend></div>
  			</div>
  			<div class="row">
  				<div class="col-sm-6">
  					<div class="table-responsive">
    		  	 	<table cellpadding="1" cellspacing="1" width="100%">
    		  	 		<tbody>
    		  	 			<tr> 
    		  	  			<td width="25%" style="text-align:center">
    		  	  				<a href="setup_email_add_edit.php?action=add" onMouseOver="mouse_move('sd_email_alert_add');" onMouseOut='mouse_move();'>
      	  	  				<img src="images/btn_email_add_bg.gif" title='Add New Email Alert'><br><span><b>Add Email Alert</b></span>
      	  	  				</a>
    		  	  			</td>
          	      	
    		  	  			<td width="25%" style="text-align:center">
    		  	  				<a href="setup_report_add_edit.php?action=add" onMouseOver="mouse_move('sd_email_alert_add_report');" onMouseOut='mouse_move();'>
      	  	  				<img src='images/btn_email_add_report.gif' title='Add New Email Report'><br><span><b>Add Email Report</b></span>
      	  	  				</a>
    		  	  			</td>
    		  	  			
    		  	  			<td width="25%" style="text-align:center">
    		  	  				<a href="setup_trap_add_edit.php?action=add" onMouseOver="mouse_move('sd_add_snmp_trap');" onMouseOut='mouse_move();'>
      	  	  				<img src='images/snmp_add.gif' title='Add New SNMP Trap'><br><span><b>Add SNMP Trap</b></span>
      	  	  				</a>
    		  	  			</td>
    		  	  			
    		  	  			<!--<td width="25%" style="text-align:center">
    		  	  				<a href="setup_sms_add_edit.php?action=add" onMouseOver="mouse_move('sd_sms_alert_add');" onMouseOut='mouse_move();'>
      	  	  				<img src='images/cellphone_add.gif' title='Add New SMS Alert'><br><span><b>Add SMS Alert</b></span>
      	  	  				</a>
    		  	  			</td>-->
    		  	  			
    		  	 			</tr>
    		  	 		</tbody>
    		  	 	</table>
    		  	</div>
    		  </div>		
  			</div>
				<div class="row">
  				<div class="col-sm-12">
  					<br>
  					<br>
  					<legend>Alert Options</legend>
  				</div>
  			</div>
  			
  			<div class="row">
  				<div class="col-sm-12">
  					<div class="form-group">
    		      	<div class="col-sm-2" style="min-width:250px">
    		        	<div class='checkbox checkbox-success'>
    		          	<input type='checkbox' id='action_check' name='action_check' <?php echo $confirmation; ?> />
    		            <label for='action_check'>Alert Action Confirmation?</label>
    		          </div>
    		       </div>
    		    </div>
    		  </div>
    		  <div class="col-sm-12">
    		  	<button name="confirmation_btn" class="btn btn-success" type="submit" onMouseOver="mouse_move(&#039;b_apply&#039;);" onMouseOut="mouse_move();"> Apply</button>	
  				</div>
  			</div>
				<br>

  			<div class="row">
    			<div class="col-sm-12">
    		  	<div class="hpanel3">
    		  	  <div class="panel-body" style="text-align:center; background:#F1F3F6;border:none;">
    				  	<div class="table-responsive">
    				   		<table width="100%" class="table table-striped table-condensed table-hover">
    				   			<thead>
    				   				<tr>
    				   					<th width="2%" style="background:#ABBEEF; border: 1px solid white;">
    				   						<div style="text-align:center">ID</div>
    				   					</th>
    				   					<th width="8%" style="background:#D6DFF7; border: 1px solid white;">
    				   						<div style="text-align:center">Type</div>
    				   					</th>
    				   					<th width="30%" style="background:#D6DFF7; border: 1px solid white;">
    				   						<div style="text-align:left">Name</div>
    				   					</th>
    				   					<th width="50%" style="background:#D6DFF7; border: 1px solid white;">
    				   						<div style="text-align:left">Description</div>
    				   					</th>
    				   					<th width="10%" style="background:#D6DFF7; border: 1px solid white;">
    				   						<div style="text-align:left">Actions</div>
    				   					</th>
    				   				</tr>
    				   			</thead>
    				   			<tbody>
    				   				<?php
    				   					
    				   					$dbh = new PDO('sqlite:/etc/rms100.db');
												$query = "SELECT * FROM alerts ORDER BY id";
												$result  = $dbh->query($query);
												foreach($result as $row)
												{
													$id = $row['id'];
													$type = $row['type'];
													$name = $row['name'];
													$description = $row['desc'];
													
													echo "<tr>";
													echo "	<td>";
													echo 			$id;
													echo "	</td>";
													echo "	<td>";
													echo 			$type;
													echo "	</td>";
													echo "	<td style='text-align:left'>";
													if($type == "EMAIL")
													{
														echo "			<a href='setup_email_add_edit.php?action=edit&type=".$type."&id=".$id."'><u class='dotted'>".$name."</u></a>";
													}
													else if($type == "SNMP")
													{
														echo "			<a href='setup_trap_add_edit.php?action=edit&type=".$type."&id=".$id."'><u class='dotted'>".$name."</u></a>";
													}
													else if($type == "REPORT")
													{
														echo "			<a href='setup_report_add_edit.php?action=edit&type=".$type."&id=".$id."'><u class='dotted'>".$name."</u></a>";
													}
													else
													{
														echo "			<a href='setup_sms_add_edit.php?action=edit&type=".$type."&id=".$id."'><u class='dotted'>".$name."</u></a>";
													}
													echo "	</td>";
													echo "	<td style='text-align:left'>";
													echo 			$description;
													echo "	</td>";
													echo "	<td style='text-align:left'>";
													echo " 		<a href='setup_notifications.php?action=run&id=".$id."' onMouseOver ='mouse_move(\"sd_alert_exec\");'	onMouseOut='mouse_move();'>";
													echo "		<img src='images/on.gif' width='16' height='16' title='EXECUTE'></a>";
													echo "		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
													echo " 		<a href='setup_notifications.php?action=delete&id=".$id."' onMouseOver ='mouse_move(\"sd_alert_delete\");'	onMouseOut='mouse_move();'>";
													echo "		<img  src='images/off.gif' width='16' height='16' title='DELETE'></a>";
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
echo"  text: 'Settings Saved!',";
echo"  type: 'success',";
echo"  showConfirmButton: false,";
echo"  timer: 1500";
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
echo"  timer: 1000";
echo"});";
echo"</script>";
}

if($alert_flag == "3")
{
	echo"<script>";
	echo"	swal({";
	echo"		title: 'Execute Alert ID# " . $myid . "<br>Are you really sure?',";
	echo"		type: 'warning',";
	echo"		showCancelButton: true,";
	echo"		html: true,";
	echo"		confirmButtonColor: '#DD6B55',";
	echo"		confirmButtonText: 'Yes, run it!',";
	echo"		closeOnConfirm: false";
	echo"	},";
	echo"	function(){";
	echo"		window.location.href = 'setup_notifications.php?confirm=run&id=" . $myid . "';";
	echo"	});";
	echo"</script>";
}

if($alert_flag == "4")
{
	echo"<script>";
	echo"	swal({";
	echo"		title: 'Delete Alert ID# " . $myid . "<br>Are you really sure?',";
	echo"		type: 'warning',";
	echo"		showCancelButton: true,";
	echo"		html: true,";
	echo"		confirmButtonColor: '#DD6B55',";
	echo"		confirmButtonText: 'Yes, delete it!',";
	echo"		closeOnConfirm: false";
	echo"	},";
	echo"	function(){";
	echo"		window.location.href = 'setup_notifications.php?confirm=delete&id=" . $myid . "';";
	echo"	});";
	echo"</script>";
}

echo "</body>";
echo "</html>";


/////////////////////////////////////////////////////////////
//                                                         //
//             SEEK & DESTROY SCRIPTS                      //
//                                                         //
/////////////////////////////////////////////////////////////
function z_seek_destroy_alert_refs($del_val)
{
	$db1 = new PDO('sqlite:/etc/rms100.db');
	
	
	//3 voltmeters to check - HI_alert_cmds
	$commands = "";
	for($cnt1=1;$cnt1<4;$cnt1++)
	{
		$save_flag = 0;
		$sd_query = sprintf("SELECT * FROM voltmeters WHERE id='%d';", $cnt1);
		$result  = $db1->query($sd_query);
		foreach($result as $row)
		{
			$commands = $row['HI_alert_cmds'];	
			$mylen = strlen($commands);
			if($mylen !== 0)
			{
				$array = explode(".",$commands);
				$count = count($array);
				$count = $count - 1;
				for ($key = 0; $key < $count; $key++) 
				{
   				if($array[$key] == $del_val)
   				{
   					unset($array[$key]);
   					$save_flag = 1;
   				}
				}
				$count = count($array);
				if($count == 1)
				{
					if(empty($array))
					{
						$commands = "";
					}
					else
					{
						$commands = implode(".",$array);
					}
				}
				else
				{
					$commands = implode(".",$array);
				}
			}	
		}
		if($save_flag == 1)
		{
			$query = sprintf("UPDATE voltmeters SET HI_alert_cmds='%s' WHERE id='%s';", $commands,$cnt1);
			$result1  = $db1->exec($query);
		}
		
	}	

	//3 voltmeters to check - HI_N_alert_cmds
	$commands = "";
	for($cnt1=1;$cnt1<4;$cnt1++)
	{
		$save_flag = 0;
		$sd_query = sprintf("SELECT * FROM voltmeters WHERE id='%d';", $cnt1);
		$result  = $db1->query($sd_query);
		foreach($result as $row)
		{
			$commands = $row['HI_N_alert_cmds'];	
			$mylen = strlen($commands);
			if($mylen !== 0)
			{
				$array = explode(".",$commands);
				$count = count($array);
				$count = $count - 1;
				for ($key = 0; $key < $count; $key++) 
				{
   				if($array[$key] == $del_val)
   				{
   					unset($array[$key]);
   					$save_flag = 1;
   				}
				}
				$count = count($array);
				if($count == 1)
				{
					if(empty($array))
					{
						$commands = "";
					}
					else
					{
						$commands = implode(".",$array);
					}
				}
				else
				{
					$commands = implode(".",$array);
				}
			}	
		}
		if($save_flag == 1)
		{
			$query = sprintf("UPDATE voltmeters SET HI_N_alert_cmds='%s' WHERE id='%s';", $commands,$cnt1);
			$result1  = $db1->exec($query);
		}
		
	}	
		
	//3 voltmeters to check - LO_alert_cmds
	$commands = "";
	for($cnt1=1;$cnt1<4;$cnt1++)
	{
		$save_flag = 0;
		$sd_query = sprintf("SELECT * FROM voltmeters WHERE id='%d';", $cnt1);
		$result  = $db1->query($sd_query);
		foreach($result as $row)
		{
			$commands = $row['LO_alert_cmds'];	
			$mylen = strlen($commands);
			if($mylen !== 0)
			{
				$array = explode(".",$commands);
				$count = count($array);
				$count = $count - 1;
				for ($key = 0; $key < $count; $key++) 
				{
   				if($array[$key] == $del_val)
   				{
   					unset($array[$key]);
   					$save_flag = 1;
   				}
				}
				$count = count($array);
				if($count == 1)
				{
					if(empty($array))
					{
						$commands = "";
					}
					else
					{
						$commands = implode(".",$array);
					}
				}
				else
				{
					$commands = implode(".",$array);
				}
			}	
		}
		if($save_flag == 1)
		{
			$query = sprintf("UPDATE voltmeters SET LO_alert_cmds='%s' WHERE id='%s';", $commands,$cnt1);
			$result1  = $db1->exec($query);
		}
		
	}	
		
	//3 voltmeters to check - LO_N_alert_cmds
	$commands = "";
	for($cnt1=1;$cnt1<4;$cnt1++)
	{
		$save_flag = 0;
		$sd_query = sprintf("SELECT * FROM voltmeters WHERE id='%d';", $cnt1);
		$result  = $db1->query($sd_query);
		foreach($result as $row)
		{
			$commands = $row['LO_N_alert_cmds'];	
			$mylen = strlen($commands);
			if($mylen !== 0)
			{
				$array = explode(".",$commands);
				$count = count($array);
				$count = $count - 1;
				for ($key = 0; $key < $count; $key++) 
				{
   				if($array[$key] == $del_val)
   				{
   					unset($array[$key]);
   					$save_flag = 1;
   				}
				}
				$count = count($array);
				if($count == 1)
				{
					if(empty($array))
					{
						$commands = "";
					}
					else
					{
						$commands = implode(".",$array);
					}
				}
				else
				{
					$commands = implode(".",$array);
				}
			}	
		}
		if($save_flag == 1)
		{
			$query = sprintf("UPDATE voltmeters SET LO_N_alert_cmds='%s' WHERE id='%s';", $commands,$cnt1);
			$result1  = $db1->exec($query);
		}
		
	}	
	
	// Temperature to check	 - HI_alert_cmds
	$commands = "";
	$sd_query = sprintf("SELECT * FROM temperature", $cnt1);
	$result  = $db1->query($sd_query);
	foreach($result as $row)
	{
		$save_flag = 0;
		$commands = $row['HI_alert_cmds'];	
		$mylen = strlen($commands);
		if($mylen !== 0)
		{
			$array = explode(".",$commands);
			$count = count($array);
			$count = $count - 1;
			for ($key = 0; $key < $count; $key++) 
			{
  			if($array[$key] == $del_val)
  			{
  				unset($array[$key]);
  				$save_flag = 1;
  			}
			}
			$count = count($array);
			if($count == 1)
			{
				if(empty($array))
				{
					$commands = "";
				}
				else
				{
					$commands = implode(".",$array);
				}
			}
			else
			{
				$commands = implode(".",$array);
			}
		}	
	}
	if($save_flag == 1)
	{
		$query = sprintf("UPDATE temperature SET HI_alert_cmds='%s';", $commands);
		$result1  = $db1->exec($query);
	}
	
		
	// Temperature to check	 - HI_N_alert_cmds
	$commands = "";
	$sd_query = sprintf("SELECT * FROM temperature", $cnt1);
	$result  = $db1->query($sd_query);
	foreach($result as $row)
	{
		$save_flag = 0;
		$commands = $row['HI_N_alert_cmds'];	
		$mylen = strlen($commands);
		if($mylen !== 0)
		{
			$array = explode(".",$commands);
			$count = count($array);
			$count = $count - 1;
			for ($key = 0; $key < $count; $key++) 
			{
  			if($array[$key] == $del_val)
  			{
  				unset($array[$key]);
  				$save_flag = 1;
  			}
			}
			$count = count($array);
			if($count == 1)
			{
				if(empty($array))
				{
					$commands = "";
				}
				else
				{
					$commands = implode(".",$array);
				}
			}
			else
			{
				$commands = implode(".",$array);
			}
		}	
	}
	if($save_flag == 1)
	{
		$query = sprintf("UPDATE temperature SET HI_N_alert_cmds='%s';", $commands);
		$result1  = $db1->exec($query);
	}
	
	
	// Temperature to check	 - LO_alert_cmds
	$commands = "";
	$sd_query = sprintf("SELECT * FROM temperature", $cnt1);
	$result  = $db1->query($sd_query);
	foreach($result as $row)
	{
		$save_flag = 0;
		$commands = $row['LO_alert_cmds'];	
		$mylen = strlen($commands);
		if($mylen !== 0)
		{
			$array = explode(".",$commands);
			$count = count($array);
			$count = $count - 1;
			for ($key = 0; $key < $count; $key++) 
			{
  			if($array[$key] == $del_val)
  			{
  				unset($array[$key]);
  				$save_flag = 1;
  			}
			}
			$count = count($array);
			if($count == 1)
			{
				if(empty($array))
				{
					$commands = "";
				}
				else
				{
					$commands = implode(".",$array);
				}
			}
			else
			{
				$commands = implode(".",$array);
			}
		}	
	}
	if($save_flag == 1)
	{
		$query = sprintf("UPDATE temperature SET LO_alert_cmds='%s';", $commands);
		$result1  = $db1->exec($query);
	}
	
	
	// Temperature to check	 - LO_N_alert_cmds
	$commands = "";
	$sd_query = sprintf("SELECT * FROM temperature", $cnt1);
	$result  = $db1->query($sd_query);
	foreach($result as $row)
	{
		$save_flag = 0;
		$commands = $row['LO_N_alert_cmds'];	
		$mylen = strlen($commands);
		if($mylen !== 0)
		{
			$array = explode(".",$commands);
			$count = count($array);
			$count = $count - 1;
			for ($key = 0; $key < $count; $key++) 
			{
  			if($array[$key] == $del_val)
  			{
  				unset($array[$key]);
  				$save_flag = 1;
  			}
			}
			$count = count($array);
			if($count == 1)
			{
				if(empty($array))
				{
					$commands = "";
				}
				else
				{
					$commands = implode(".",$array);
				}
			}
			else
			{
				$commands = implode(".",$array);
			}
		}	
	}
	if($save_flag == 1)
	{
		$query = sprintf("UPDATE temperature SET LO_N_alert_cmds='%s';", $commands);
		$result1  = $db1->exec($query);
	}
	
	//5 alarms to check - HI_alert_cmds
	$commands = "";
	for($cnt1=1;$cnt1<6;$cnt1++)
	{
		$save_flag = 0;
		$sd_query = sprintf("SELECT * FROM io WHERE id='%d' AND type='alarm';", $cnt1);
		$result  = $db1->query($sd_query);
		foreach($result as $row)
		{
			$commands = $row['HI_alert_cmds'];	
			$mylen = strlen($commands);
			if($mylen !== 0)
			{
				$array = explode(".",$commands);
				$count = count($array);
				$count = $count - 1;
				for ($key = 0; $key < $count; $key++) 
				{
   				if($array[$key] == $del_val)
   				{
   					unset($array[$key]);
   					$save_flag = 1;
   				}
				}
				$count = count($array);
				if($count == 1)
				{
					if(empty($array))
					{
						$commands = "";
					}
					else
					{
						$commands = implode(".",$array);
					}
				}
				else
				{
					$commands = implode(".",$array);
				}
			}	
		}
		if($save_flag == 1)
		{
			$query = sprintf("UPDATE io SET HI_alert_cmds='%s' WHERE id='%s' AND type='alarm';", $commands,$cnt1);
			$result1  = $db1->exec($query);
		}
		
	}
	
	//5 alarms to check - LO_alert_cmds
	$commands = "";
	for($cnt1=1;$cnt1<6;$cnt1++)
	{
		$save_flag = 0;
		$sd_query = sprintf("SELECT * FROM io WHERE id='%d' AND type='alarm';", $cnt1);
		$result  = $db1->query($sd_query);
		foreach($result as $row)
		{
			$commands = $row['LO_alert_cmds'];	
			$mylen = strlen($commands);
			if($mylen !== 0)
			{
				$array = explode(".",$commands);
				$count = count($array);
				$count = $count - 1;
				for ($key = 0; $key < $count; $key++) 
				{
   				if($array[$key] == $del_val)
   				{
   					unset($array[$key]);
   					$save_flag = 1;
   				}
				}
				$count = count($array);
				if($count == 1)
				{
					if(empty($array))
					{
						$commands = "";
					}
					else
					{
						$commands = implode(".",$array);
					}
				}
				else
				{
					$commands = implode(".",$array);
				}
			}	
		}
		if($save_flag == 1)
		{
			$query = sprintf("UPDATE io SET LO_alert_cmds='%s' WHERE id='%s' AND type='alarm';", $commands,$cnt1);
			$result1  = $db1->exec($query);
		}
	}
	
	//4 gxio to check - HI_alert_cmds
	$commands = "";
	for($cnt1=1;$cnt1<5;$cnt1++)
	{
		$save_flag = 0;
		$sd_query = sprintf("SELECT * FROM io WHERE id='%d' AND type='gxio';", $cnt1);
		$result  = $db1->query($sd_query);
		foreach($result as $row)
		{
			$commands = $row['HI_alert_cmds'];	
			$mylen = strlen($commands);
			if($mylen !== 0)
			{
				$array = explode(".",$commands);
				$count = count($array);
				$count = $count - 1;
				for ($key = 0; $key < $count; $key++) 
				{
   				if($array[$key] == $del_val)
   				{
   					unset($array[$key]);
   					$save_flag = 1;
   				}
				}
				$count = count($array);
				if($count == 1)
				{
					if(empty($array))
					{
						$commands = "";
					}
					else
					{
						$commands = implode(".",$array);
					}
				}
				else
				{
					$commands = implode(".",$array);
				}
			}	
		}
		if($save_flag == 1)
		{
			$query = sprintf("UPDATE io SET HI_alert_cmds='%s' WHERE id='%s' AND type='gxio';", $commands,$cnt1);
			$result1  = $db1->exec($query);
		}
	}
	
	//4 gxio to check - LO_alert_cmds
	$commands = "";
	for($cnt1=1;$cnt1<5;$cnt1++)
	{
		$save_flag = 0;
		$sd_query = sprintf("SELECT * FROM io WHERE id='%d' AND type='gxio';", $cnt1);
		$result  = $db1->query($sd_query);
		foreach($result as $row)
		{
			$commands = $row['LO_alert_cmds'];	
			$mylen = strlen($commands);
			if($mylen !== 0)
			{
				$array = explode(".",$commands);
				$count = count($array);
				$count = $count - 1;
				for ($key = 0; $key < $count; $key++) 
				{
   				if($array[$key] == $del_val)
   				{
   					unset($array[$key]);
   					$save_flag = 1;
   				}
				}
				$count = count($array);
				if($count == 1)
				{
					if(empty($array))
					{
						$commands = "";
					}
					else
					{
						$commands = implode(".",$array);
					}
				}
				else
				{
					$commands = implode(".",$array);
				}
			}	
		}
		if($save_flag == 1)
		{
			$query = sprintf("UPDATE io SET LO_alert_cmds='%s' WHERE id='%s' AND type='gxio';", $commands,$cnt1);
			$result1  = $db1->exec($query);
		}
	}
	
	//1 button to check - HI_alert_cmds
	$commands = "";
	for($cnt1=1;$cnt1<2;$cnt1++)
	{
		$save_flag = 0;
		$sd_query = sprintf("SELECT * FROM io WHERE id='%d' AND type='btn';", $cnt1);
		$result  = $db1->query($sd_query);
		foreach($result as $row)
		{
			$commands = $row['HI_alert_cmds'];	
			$mylen = strlen($commands);
			if($mylen !== 0)
			{
				$array = explode(".",$commands);
				$count = count($array);
				$count = $count - 1;
				for ($key = 0; $key < $count; $key++) 
				{
   				if($array[$key] == $del_val)
   				{
   					unset($array[$key]);
   					$save_flag = 1;
   				}
				}
				$count = count($array);
				if($count == 1)
				{
					if(empty($array))
					{
						$commands = "";
					}
					else
					{
						$commands = implode(".",$array);
					}
				}
				else
				{
					$commands = implode(".",$array);
				}
			}	
		}
		if($save_flag == 1)
		{
			$query = sprintf("UPDATE io SET HI_alert_cmds='%s' WHERE id='%s' AND type='btn';", $commands,$cnt1);
			$result1  = $db1->exec($query);
		}
	}
	
	//1 button to check - LO_alert_cmds
	$commands = "";
	for($cnt1=1;$cnt1<2;$cnt1++)
	{
		$save_flag = 0;
		$sd_query = sprintf("SELECT * FROM io WHERE id='%d' AND type='btn';", $cnt1);
		$result  = $db1->query($sd_query);
		foreach($result as $row)
		{
			$commands = $row['LO_alert_cmds'];	
			$mylen = strlen($commands);
			if($mylen !== 0)
			{
				$array = explode(".",$commands);
				$count = count($array);
				$count = $count - 1;
				for ($key = 0; $key < $count; $key++) 
				{
   				if($array[$key] == $del_val)
   				{
   					unset($array[$key]);
   					$save_flag = 1;
   				}
				}
				$count = count($array);
				if($count == 1)
				{
					if(empty($array))
					{
						$commands = "";
					}
					else
					{
						$commands = implode(".",$array);
					}
				}
				else
				{
					$commands = implode(".",$array);
				}
			}	
		}
		if($save_flag == 1)
		{
			$query = sprintf("UPDATE io SET LO_alert_cmds='%s' WHERE id='%s' AND type='btn';", $commands,$cnt1);
			$result1  = $db1->exec($query);
		}
	}
	
	//15 ping targets to check - HI_alert_cmds
	$commands = "";
	for($cnt1=1;$cnt1<16;$cnt1++)
	{
		$save_flag = 0;
		$sd_query = sprintf("SELECT * FROM ping_targets WHERE id='%d';", $cnt1);
		$result  = $db1->query($sd_query);
		foreach($result as $row)
		{
			$commands = $row['HI_alert_cmds'];	
			$mylen = strlen($commands);
			if($mylen !== 0)
			{
				$array = explode(".",$commands);
				$count = count($array);
				$count = $count - 1;
				for ($key = 0; $key < $count; $key++) 
				{
   				if($array[$key] == $del_val)
   				{
   					unset($array[$key]);
   					$save_flag = 1;
   				}
				}
				$count = count($array);
				if($count == 1)
				{
					if(empty($array))
					{
						$commands = "";
					}
					else
					{
						$commands = implode(".",$array);
					}
				}
				else
				{
					$commands = implode(".",$array);
				}
			}	
		}
		if($save_flag == 1)
		{
			$query = sprintf("UPDATE ping_targets SET HI_alert_cmds='%s' WHERE id='%s';", $commands,$cnt1);
			$result1  = $db1->exec($query);
		}
	}
	
	//15 ping targets to check - HI_N_alert_cmds
	$commands = "";
	for($cnt1=1;$cnt1<16;$cnt1++)
	{
		$save_flag = 0;
		$sd_query = sprintf("SELECT * FROM ping_targets WHERE id='%d';", $cnt1);
		$result  = $db1->query($sd_query);
		foreach($result as $row)
		{
			$commands = $row['HI_N_alert_cmds'];	
			$mylen = strlen($commands);
			if($mylen !== 0)
			{
				$array = explode(".",$commands);
				$count = count($array);
				$count = $count - 1;
				for ($key = 0; $key < $count; $key++) 
				{
   				if($array[$key] == $del_val)
   				{
   					unset($array[$key]);
   					$save_flag = 1;
   				}
				}
				$count = count($array);
				if($count == 1)
				{
					if(empty($array))
					{
						$commands = "";
					}
					else
					{
						$commands = implode(".",$array);
					}
				}
				else
				{
					$commands = implode(".",$array);
				}
			}	
		}
		if($save_flag == 1)
		{
			$query = sprintf("UPDATE ping_targets SET HI_N_alert_cmds='%s' WHERE id='%s';", $commands,$cnt1);
			$result1  = $db1->exec($query);
		}
	}
	
	//15 ping targets to check - LO_alert_cmds
	$commands = "";
	for($cnt1=1;$cnt1<16;$cnt1++)
	{
		$save_flag = 0;
		$sd_query = sprintf("SELECT * FROM ping_targets WHERE id='%d';", $cnt1);
		$result  = $db1->query($sd_query);
		foreach($result as $row)
		{
			$commands = $row['LO_alert_cmds'];	
			$mylen = strlen($commands);
			if($mylen !== 0)
			{
				$array = explode(".",$commands);
				$count = count($array);
				$count = $count - 1;
				for ($key = 0; $key < $count; $key++) 
				{
   				if($array[$key] == $del_val)
   				{
   					unset($array[$key]);
   					$save_flag = 1;
   				}
				}
				$count = count($array);
				if($count == 1)
				{
					if(empty($array))
					{
						$commands = "";
					}
					else
					{
						$commands = implode(".",$array);
					}
				}
				else
				{
					$commands = implode(".",$array);
				}
			}	
		}
		if($save_flag == 1)
		{
			$query = sprintf("UPDATE ping_targets SET LO_alert_cmds='%s' WHERE id='%s';", $commands,$cnt1);
			$result1  = $db1->exec($query);
		}
	}
	
	//15 ping targets to check - LO_N_alert_cmds
	$commands = "";
	for($cnt1=1;$cnt1<16;$cnt1++)
	{
		$save_flag = 0;
		$sd_query = sprintf("SELECT * FROM ping_targets WHERE id='%d';", $cnt1);
		$result  = $db1->query($sd_query);
		foreach($result as $row)
		{
			$commands = $row['LO_N_alert_cmds'];	
			$mylen = strlen($commands);
			if($mylen !== 0)
			{
				$array = explode(".",$commands);
				$count = count($array);
				$count = $count - 1;
				for ($key = 0; $key < $count; $key++) 
				{
   				if($array[$key] == $del_val)
   				{
   					unset($array[$key]);
   					$save_flag = 1;
   				}
				}
				$count = count($array);
				if($count == 1)
				{
					if(empty($array))
					{
						$commands = "";
					}
					else
					{
						$commands = implode(".",$array);
					}
				}
				else
				{
					$commands = implode(".",$array);
				}
			}	
		}
		if($save_flag == 1)
		{
			$query = sprintf("UPDATE ping_targets SET LO_N_alert_cmds='%s' WHERE id='%s';", $commands,$cnt1);
			$result1  = $db1->exec($query);
		}
	}
}










?>









 
















