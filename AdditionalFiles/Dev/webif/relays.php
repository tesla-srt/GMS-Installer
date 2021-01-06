<?php
	error_reporting(E_ALL);
	include "lib.php";
	$hostname = trim(file_get_contents("/etc/hostname"));
	$alert_flag = "0";
	
	
	
	
	
	$dbh = new PDO('sqlite:/etc/rms100.db');
	
	$result  = $dbh->query("SELECT * FROM display_options;");			
	foreach($result as $row)
	{
		$screen_animations = $row['screen_animations'];
	}
	
	//RELAY 1
	$result  = $dbh->query("SELECT * FROM relays WHERE id='1';");
	foreach($result as $row)
	{
		$rly1_name = $row['name'];
		$rly1_notes = $row['notes'];
		$rly1_notes = str_replace(array("\r\n", "\r", "\n"), "<br>", $rly1_notes);
		$rly1_nc_color = $row['nc_color'];
		$rly1_no_color = $row['no_color'];
	}
	
	//RELAY 2
	$result  = $dbh->query("SELECT * FROM relays WHERE id='2';");
	foreach($result as $row)
	{
		$rly2_name = $row['name'];
		$rly2_notes = $row['notes'];
		$rly2_notes = str_replace(array("\r\n", "\r", "\n"), "<br>", $rly2_notes);
		$rly2_nc_color = $row['nc_color'];
		$rly2_no_color = $row['no_color'];
	}
	
	
	
	$result  = $dbh->query("SELECT * FROM relay_script_cmds WHERE command='00';");
	foreach($result as $row)
	{
		$NO1 = $row['state'];
	}
	
	$result  = $dbh->query("SELECT * FROM relay_script_cmds WHERE command='01';");
	foreach($result as $row)
	{
		$NC1 = $row['state'];
	}
	
	$result  = $dbh->query("SELECT * FROM relay_script_cmds WHERE command='02';");
	foreach($result as $row)
	{
		$NO2 = $row['state'];
	}
	
	$result  = $dbh->query("SELECT * FROM relay_script_cmds WHERE command='03';");
	foreach($result as $row)
	{
		$NC2 = $row['state'];
	}
	
	//RELAY ACTION CONFIRMATION
	$result  = $dbh->query("SELECT * FROM relayconf;");
	foreach($result as $row)
	{
		$relay_confirmation = $row['confirmation'];
	}
	if($relay_confirmation == "1")
	{
		$relay_confirmation = "checked";
	}
	else
	{
		$relay_confirmation = "";
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

if(isset ($_GET['relay']))
	{
		$relay_num = $_GET['relay'];
		$command = $_GET['command'];
		
		if($relay_confirmation == "checked")
		{
			$alert_flag = "5";
		}
		else
		{
			$command = "rmsrelay ".$relay_num." ".$command;
			exec($command);
		}
	}




if(isset ($_GET['action']))
	{
		$action = $_GET['action'];
		$id = $_GET['id'];
		if($action == "run")
		{
			if($relay_confirmation == "checked")
			{
				$alert_flag = "3";
			}
			else
			{
				$command = "rmsscript ".$id.". > /dev/null 2>&1 &";
				exec($command);
			}
		}
		
		else if($action == "delete")
		{
			$alert_flag = "4";
		}
		
		else if($action == "edit")
		{
			$success = $_GET['success'];
			if($success == "yes")
			{
				$text = "Relay #".$id." Edit Success!";
				$alert_flag = "2";
			}
		}
	}

if(isset ($_GET['confirm']))
	{
		$confirm = $_GET['confirm'];
		$id = $_GET['id'];
		if($confirm == "run")
		{
			$command = "rmsscript ".$id.". > /dev/null 2>&1 &";
			exec($command);
			$text = "Script ID #".$id." has been executed!";
			$alert_flag = "2";
		}
		else if($confirm == "delete")
		{
			$query = sprintf("DELETE FROM scripts WHERE id='%s';",$id);
			$result = $dbh->exec($query);
			z_seek_destroy_script_refs($id);
			restart_some_services();
			$text = "Script ID #".$id." has been Deleted!";
			$alert_flag = "2";
		}
	}
	
	if(isset ($_GET['execute']))
	{
		$execute = $_GET['execute'];
		if($execute == "yes")
		{
			$rly = $_GET['relay'];
			$cmd = $_GET['command'];
			$sd = "rmsrelay ".$rly." ".$cmd;
			exec($sd);
			$text = "Relay Command Executed!";
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

	
// OK Button	was clicked
if(isset ($_POST['save_btn']))
{	
	
	$alert_flag = "1";
}
	
// Relay Action Confirmation Button	was clicked
if(isset ($_POST['confirmation_btn']))
{	
	if(isset ($_POST['action_confirmation']))
	{
		$result  = $dbh->exec("UPDATE relayconf SET confirmation='1';");
		$relay_confirmation = "checked";
	}
	else
	{
		$result  = $dbh->exec("UPDATE relayconf SET confirmation='0';");
		$relay_confirmation = " ";
	}
	$alert_flag = "1";
}
	
	
	
	
escape_hatch:	
$relay1 = trim(file_get_contents("/var/rmsdata/relay1"));
$relay2 = trim(file_get_contents("/var/rmsdata/relay2"));


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
			SetContext('relays');
		</script>
		
		<script language="javascript" type="text/javascript">
		function display_relays ()
		{
		        var myRandom = parseInt(Math.random()*999999999);
		        $.getJSON('sdserver.php?element=relays&rand=' + myRandom,
		            function(data)
		            {
										setTimeout (display_relays, 1000);
		                
		                  if (data.rly.r1 == 1)
		                  {
		                  	$('#relay1').replaceWith("<div id='relay1'><a href='relays.php?relay=1&command=no'><img src='images/sdnc.gif' onMouseOver='mouse_move(&#039;b_relays_relaycontrol_on&#039;);'	onMouseOut='mouse_move();'></a></div>");
		                  	$('#r1N').replaceWith("<div id='r1N'><span style='color:" + data.rly.r1nc_color + "'>" + data.rly.r1NC + "</span></div>");
		                  }
		                  else
		                  {
		                    $('#relay1').replaceWith("<div id='relay1'><a href='relays.php?relay=1&command=nc'><img src='images/sdno.gif' onMouseOver='mouse_move(&#039;b_relays_relaycontrol_off&#039;);'	onMouseOut='mouse_move();'></a></div>");
		                  	$('#r1N').replaceWith("<div id='r1N'><span style='color:" + data.rly.r1no_color + "'>" + data.rly.r1NO + "</span></div>");
		                  }
											
											if (data.rly.r2 == 1)
		                  {
		                  	$('#relay2').replaceWith("<div id='relay2'><a href='relays.php?relay=2&command=no'><img src='images/sdnc.gif' onMouseOver='mouse_move(&#039;b_relays_relaycontrol_on&#039;);'	onMouseOut='mouse_move();'></a></div>");
		                  	$('#r2N').replaceWith("<div id='r2N'><span style='color:" + data.rly.r2nc_color + "'>" + data.rly.r2NC + "</span></div>");
		                  }
		                  else
		                  {
		                    $('#relay2').replaceWith("<div id='relay2'><a href='relays.php?relay=2&command=nc'><img src='images/sdno.gif' onMouseOver='mouse_move(&#039;b_relays_relaycontrol_off&#039;);'	onMouseOut='mouse_move();'></a></div>");
		                  	$('#r2N').replaceWith("<div id='r2N'><span style='color:" + data.rly.r2no_color + "'>" + data.rly.r2NO + "</span></div>");
		                  }
		            }
		        );
		}

	
		display_relays ();
		</script>
		
		
		
</head>
<body class="fixed-navbar fixed-sidebar">
<div class='splash'> <div class='solid-line'></div><div class='splash-title'><h1>Loading... Please Wait...</h1><div class='spinner'> <div class='rect1'></div> <div class='rect2'></div> <div class='rect3'></div> <div class='rect4'></div> <div class='rect5'></div> </div> </div> </div>

<!--[if lt IE 7]>
<p class="alert alert-danger">You are using an <strong>old</strong> web browser. Please upgrade your browser to improve your experience.</p>
<![endif]-->

<?php start_header(); ?>

<?php left_nav("relays"); ?>
<script language="javascript" type="text/javascript">
	SetContext('relays');
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
      		<div class="panel-body">
      	  	<form name='Relays' action='relays.php' method='post' class="form-horizontal">  	
      	    	<fieldset>
      	    		<legend><img src="images/relay1-32x32.gif"> Relays</legend>
              	
              	<div class="form-group">
              		<div class="col-sm-4">
              			<a href="setup_scripts_add_edit.php?action=add&type=RELAY" onMouseOver="mouse_move('sd_addrelayscript');" onMouseOut='mouse_move();'>
      	  	  				<img src="images/script.gif" title='Add New Relay Script'><br><span><b>Add New Relay Script</b></span>
      	  	  				</a>
              		</div>	
              	</div>
              	<hr>
              	<div class="form-group">
              		<div class="col-sm-2" style="min-width:250px; max-width:300px">
              			<div class="checkbox checkbox-success">
              				<input type="checkbox" id="action_confirmation" name="action_confirmation" <?php echo $relay_confirmation; ?> />
    		        			<label for="action_confirmation">Relay Action Confirmation?</label>
              			</div>
              		</div>
              	</div>
              	
              	<div class="form-group">	
              	  <div class="col-sm-4">
              				<button name="confirmation_btn" class="btn btn-success" type="submit" onMouseOver="mouse_move(&#039;b_apply&#039;);" onMouseOut="mouse_move();"><i class="fa fa-check" ></i> Apply</button>	
              		</div>
              	</div>
              	<div class="form-group"></div>	    	
								
								<legend>Relay Setup</legend> 
								<div class="table-responsive">
            			<table style="width:100%;">
              			<tbody>
              		  	<tr>
              		  		<td style="text-align:center;width:50%;"><a href="setup_relays.php?relay=1" onMouseOver="Tip('<?php echo $rly1_notes; ?>',TITLE,'Relay 1 - <?php echo $rly1_name; ?>',TITLEBGCOLOR,'#000000',BGCOLOR,'#ffff33'); mouse_move(&#039;b_relays_relaysetupa&#039;);" onMouseOut="UnTip();mouse_move();"><span class="an">RELAY 1</span></a></td>
              		  	  <td style="text-align:center;width:50%;"><a href="setup_relays.php?relay=2" onMouseOver="Tip('<?php echo $rly2_notes; ?>',TITLE,'Relay 2 - <?php echo $rly2_name; ?>',TITLEBGCOLOR,'#000000',BGCOLOR,'#ffff33'); mouse_move(&#039;b_relays_relaysetupa&#039;);" onMouseOut="UnTip();mouse_move();"><span class="an">RELAY 2</span></a></td>
              		  	</tr>
              		  	<tr>
              		  		<td style="text-align:center;width:50%;"><a href="setup_relays.php?relay=1" onMouseOver="Tip('<?php echo $rly1_notes; ?>',TITLE,'Relay 1 - <?php echo $rly1_name; ?>',TITLEBGCOLOR,'#000000',BGCOLOR,'#ffff33'); mouse_move(&#039;b_relays_relaysetupa&#039;);" onMouseOut="UnTip();mouse_move();"><div><img src='images/relay1-32x32.gif'></div></a></td>
              		  	  <td style="text-align:center;width:50%;"><a href="setup_relays.php?relay=2" onMouseOver="Tip('<?php echo $rly2_notes; ?>',TITLE,'Relay 2 - <?php echo $rly2_name; ?>',TITLEBGCOLOR,'#000000',BGCOLOR,'#ffff33'); mouse_move(&#039;b_relays_relaysetupa&#039;);" onMouseOut="UnTip();mouse_move();"><div><img src='images/relay1-32x32.gif'></div></a></td>
              		  	</tr>
              		  	<tr>
              		  		<td style="text-align:center;width:50%;"><a href="setup_relays.php?relay=1" onMouseOver="Tip('<?php echo $rly1_notes; ?>',TITLE,'Relay 1 - <?php echo $rly1_name; ?>',TITLEBGCOLOR,'#000000',BGCOLOR,'#ffff33'); mouse_move(&#039;b_relays_relaysetupa&#039;);" onMouseOut="UnTip();mouse_move();"><span style="color:blue"><?php echo $rly1_name; ?></span></a></td>
              		  	  <td style="text-align:center;width:50%;"><a href="setup_relays.php?relay=2" onMouseOver="Tip('<?php echo $rly2_notes; ?>',TITLE,'Relay 2 - <?php echo $rly2_name; ?>',TITLEBGCOLOR,'#000000',BGCOLOR,'#ffff33'); mouse_move(&#039;b_relays_relaysetupa&#039;);" onMouseOut="UnTip();mouse_move();"><span style="color:blue"><?php echo $rly2_name; ?></span></a></td>
              		  	</tr>
              		  </tbody>
              		</table>
								</div>
								
								<div class="form-group"></div>
								
								<legend>Relay Control</legend> 
								<div class="table-responsive">
            			<table style="width:100%;">
              			<thead>
              		  	<tr>
              		    	<th colspan="4" style="text-align:center; background-color:#D6DFF7;"><span style="color:black">Multi-Purpose Power Relays</span></th>
              		  	</tr>
              		  </thead>
              		  <tr>
              		  	<td style="text-align:center;width:50%;"><a href="relays.php?relay=1" onMouseOver="Tip('<?php echo $rly1_notes; ?>',TITLE,'Relay 1 - <?php echo $rly1_name; ?>',TITLEBGCOLOR,'#000000',BGCOLOR,'#ffff33');" onMouseOut="UnTip();"><span class="an">RELAY 1</span></a></td>
              		    <td style="text-align:center;width:50%;"><a href="relays.php?relay=2" onMouseOver="Tip('<?php echo $rly2_notes; ?>',TITLE,'Relay 2 - <?php echo $rly2_name; ?>',TITLEBGCOLOR,'#000000',BGCOLOR,'#ffff33');" onMouseOut="UnTip();"><span class="an">RELAY 2</span></a></td>
              		  </tr>
              		  <tr>
              		  	
              		  		<?php
              		  			echo "<td style='text-align:center;width:50%;'>";
              		  			if($relay1 == "0")
              		  			{
              		  				echo "<div id='relay1'><a href='relays.php?relay=1&command=no'><img src='images/sdnc.gif'></a></div>";
              		  			}
              		  			else
              		  			{
              		  				echo "<div id='relay1'><a href='relays.php?relay=1&command=nc'><img src='images/sdno.gif'></a></div>";
              		  			}
              		  			echo "</td>";
              		  			
              		  			echo "<td style='text-align:center;width:50%;'>";
              		  			if($relay2 == "0")
              		  			{
              		  				echo "<div id='relay2'><a href='relays.php?relay=2&command=no'><img src='images/sdnc.gif'></a></div>";
              		  			}
              		  			else
              		  			{
              		  				echo "<div id='relay2'><a href='relays.php?relay=2&command=nc'><img src='images/sdno.gif'></a></div>";
              		  			}
              		  			echo "</td>";
              		  		?>
              		  		
              		    
              		  </tr>
              		  <tr>
              		  	<td style="text-align:center;width:50%;"><a href="relays.php?relay=1" onMouseOver="Tip('<?php echo $rly1_notes; ?>',TITLE,'Relay 1 - <?php echo $rly1_name; ?>',TITLEBGCOLOR,'#000000',BGCOLOR,'#ffff33');" onMouseOut="UnTip();"><span style="color:blue"><?php echo $rly1_name; ?></span></a></td>
              		    <td style="text-align:center;width:50%;"><a href="relays.php?relay=2" onMouseOver="Tip('<?php echo $rly2_notes; ?>',TITLE,'Relay 2 - <?php echo $rly2_name; ?>',TITLEBGCOLOR,'#000000',BGCOLOR,'#ffff33');" onMouseOut="UnTip();"><span style="color:blue"><?php echo $rly2_name; ?></span></a></td>
              		  </tr>
              		  <tr>
              		  	<?php
              		  		echo "<td style='text-align:center;width:50%;'>";
              		  		if($relay1 == "0")
              		  		{
              		  			echo "<div id='r1N'><span style='color:" . $rly1_nc_color . "'>" . $NC1 . "</span></div>";
              		  		}
              		  		else
              		  		{
              		  			echo "<div id='r1N'><span style='color:" . $rly1_no_color . "'>" . $NO1 . "</span></div>";
              		  		}
              		  		echo "</td>";
              		  		
              		  		echo "<td style='text-align:center;width:50%;'>";
              		  		if($relay2 == "0")
              		  		{
              		  			echo "<div id='r2N'><span style='color:" . $rly2_nc_color . "'>" . $NC2 . "</span></div>";
              		  		}
              		  		else
              		  		{
              		  			echo "<div id='r2N'><span style='color:" . $rly2_no_color . "'>" . $NO2 . "</span></div>";
              		  		}
              		  		echo "</td>";
              		  	?>
              		  	</tr>
              		</table>
								</div>	
								
								<div class="form-group"></div>
								
								<legend>Relay Scripts</legend> 
								
								<div class="row">
    							<div class="col-sm-12">
    								<div class="table-responsive">
    									<table style="width:100%;" class="table table-striped table-condensed table-hover">
    										<thead>
    											<tr>
    												<th style="background:#ABBEEF; border: 1px solid white;width:2%;">
    													<div style="text-align:center">ID</div>
    												</th>
    												<th style="background:#D6DFF7; border: 1px solid white;width:8%;">
    													<div style="text-align:center">Type</div>
    												</th>
    												<th style="background:#D6DFF7; border: 1px solid white;width:30%;">
    													<div style="text-align:left">Name</div>
    												</th>
    												<th style="background:#D6DFF7; border: 1px solid white;width:50%;">
    													<div style="text-align:left">Description</div>
    												</th>
    												<th style="background:#D6DFF7; border: 1px solid white;width:10%;">
    													<div style="text-align:left">Actions</div>
    												</th>
    											</tr>
    										</thead>
    										<tbody>
    											<?php
    												
    												$dbh = new PDO('sqlite:/etc/rms100.db');
														$query = "SELECT * FROM scripts WHERE type='relay' ORDER BY id";
														$result  = $dbh->query($query);
														foreach($result as $row)
														{
															$sid = $row['id'];
															$type = "RELAY";
															$name = $row['name'];
															$description = $row['description'];
															$commands = $row['commands'];
															
															echo "<tr>";
															echo "	<td style='text-align:center'>";
															echo 			$sid;
															echo "	</td>";
															echo "	<td style='text-align:center'>";
															echo "		RELAY";
															echo "	</td>";
															echo "	<td style='text-align:left'>";
															echo "			<a href='setup_scripts_add_edit.php?action=edit&type=".$type."&id=".$sid."'><u class='dotted'>".$name."</u></a>";
															echo "	</td>";
															echo "	<td style='text-align:left'>";
															echo 			$description;
															echo "	</td>";
															echo "	<td style='text-align:left'>";
															echo " 		<a href='relays.php?action=run&id=".$sid."' onMouseOver ='mouse_move(\"b_relays_execrelaycript\");'	onMouseOut='mouse_move();'>";
															echo "		<img src='images/on.gif' width='16' height='16' title='EXECUTE SCRIPT'></a>";
															echo "		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
															echo " 		<a href='relays.php?action=delete&id=".$sid."' onMouseOver ='mouse_move(\"b_relays_deleterelaycript\");'	onMouseOut='mouse_move();'>";
															echo "		<img  src='images/off.gif' width='16' height='16' title='DELETE SCRIPT'></a>";
															echo "	</td>";
															echo "</tr>";
														}
    											?>
    										</tbody>
  										</table>      	    
    		  					</div> <!-- END TABLE RESPONSIVE -->		
    		  				</div> <!-- END COL-MD-12 --> 
    						</div> <!-- END ROW -->	
              </fieldset>  
						</form>
					</div> <!-- END PANEL BODY --> 
      	</div> <!-- END PANEL WRAPPER --> 
      </div>  <!-- END COL --> 
    </div> <!-- END ROW --> 
  </div> <!-- END CONTENT -->    
</div> <!-- END Main Wrapper -->
<script type="text/javascript" src="javascript/wz_tooltip.js"></script>
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
	echo"		title: 'Execute Script ID# " . $id . "<br>Are you really sure?',";
	echo"		type: 'warning',";
	echo"		showCancelButton: true,";
	echo"		html: true,";
	echo"		confirmButtonColor: '#DD6B55',";
	echo"		confirmButtonText: 'Yes, run it!',";
	echo"		closeOnConfirm: false";
	echo"	},";
	echo"	function(){";
	echo"		window.location.href = 'relays.php?confirm=run&id=" . $id . "';";
	echo"	});";
	echo"</script>";
}

if($alert_flag == "4")
{
	echo"<script>";
	echo"	swal({";
	echo"		title: 'Delete Script ID# " . $id . "<br>Are you really sure?',";
	echo"		type: 'warning',";
	echo"		showCancelButton: true,";
	echo"		html: true,";
	echo"		confirmButtonColor: '#DD6B55',";
	echo"		confirmButtonText: 'Yes, delete it!',";
	echo"		closeOnConfirm: false";
	echo"	},";
	echo"	function(){";
	echo"		window.location.href = 'relays.php?confirm=delete&id=" . $id . "';";
	echo"	});";
	echo"</script>";
}

if($alert_flag == "5")
{
	echo"<script>";
	echo"	swal({";
	echo"		title: 'Execute Relay Action<br>Are you really sure?',";
	echo"		type: 'warning',";
	echo"		showCancelButton: true,";
	echo"		html: true,";
	echo"		confirmButtonColor: '#DD6B55',";
	echo"		confirmButtonText: 'Yes, execute!',";
	echo"		closeOnConfirm: false";
	echo"	},";
	echo"	function(){";
	echo"		window.location.href = 'relays.php?execute=yes&relay=" . $relay_num . "&command=".$command."';";
	echo"	});";
	echo"</script>";
}


?>
</body>
</html> 


<?php


/////////////////////////////////////////////////////////////
//                                                         //
//             SEEK & DESTROY SCRIPTS                      //
//                                                         //
/////////////////////////////////////////////////////////////
function z_seek_destroy_script_refs($del_val)
{
	$db1 = new PDO('sqlite:/etc/rms100.db');
	
	
	//3 voltmeters to check - HI_script_cmds
	$commands = "";
	for($cnt1=1;$cnt1<4;$cnt1++)
	{
		$save_flag = 0;
		$sd_query = sprintf("SELECT * FROM voltmeters WHERE id='%d';", $cnt1);
		$result  = $db1->query($sd_query);
		foreach($result as $row)
		{
			$commands = $row['HI_script_cmds'];	
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
			$query = sprintf("UPDATE voltmeters SET HI_script_cmds='%s' WHERE id='%s';", $commands,$cnt1);
			$result1  = $db1->exec($query);
		}
		
	}	

	//3 voltmeters to check - HI_N_script_cmds
	$commands = "";
	for($cnt1=1;$cnt1<4;$cnt1++)
	{
		$save_flag = 0;
		$sd_query = sprintf("SELECT * FROM voltmeters WHERE id='%d';", $cnt1);
		$result  = $db1->query($sd_query);
		foreach($result as $row)
		{
			$commands = $row['HI_N_script_cmds'];	
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
			$query = sprintf("UPDATE voltmeters SET HI_N_script_cmds='%s' WHERE id='%s';", $commands,$cnt1);
			$result1  = $db1->exec($query);
		}
		
	}	
		
	//3 voltmeters to check - LO_script_cmds
	$commands = "";
	for($cnt1=1;$cnt1<4;$cnt1++)
	{
		$save_flag = 0;
		$sd_query = sprintf("SELECT * FROM voltmeters WHERE id='%d';", $cnt1);
		$result  = $db1->query($sd_query);
		foreach($result as $row)
		{
			$commands = $row['LO_script_cmds'];	
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
			$query = sprintf("UPDATE voltmeters SET LO_script_cmds='%s' WHERE id='%s';", $commands,$cnt1);
			$result1  = $db1->exec($query);
		}
		
	}	
		
	//3 voltmeters to check - LO_N_script_cmds
	$commands = "";
	for($cnt1=1;$cnt1<4;$cnt1++)
	{
		$save_flag = 0;
		$sd_query = sprintf("SELECT * FROM voltmeters WHERE id='%d';", $cnt1);
		$result  = $db1->query($sd_query);
		foreach($result as $row)
		{
			$commands = $row['LO_N_script_cmds'];	
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
			$query = sprintf("UPDATE voltmeters SET LO_N_script_cmds='%s' WHERE id='%s';", $commands,$cnt1);
			$result1  = $db1->exec($query);
		}
		
	}	
	
	// Temperature to check	 - HI_script_cmds
	$commands = "";
	$sd_query = sprintf("SELECT * FROM temperature", $cnt1);
	$result  = $db1->query($sd_query);
	foreach($result as $row)
	{
		$save_flag = 0;
		$commands = $row['HI_script_cmds'];	
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
		$query = sprintf("UPDATE temperature SET HI_script_cmds='%s';", $commands);
		$result1  = $db1->exec($query);
	}
	
		
	// Temperature to check	 - HI_N_script_cmds
	$commands = "";
	$sd_query = sprintf("SELECT * FROM temperature", $cnt1);
	$result  = $db1->query($sd_query);
	foreach($result as $row)
	{
		$save_flag = 0;
		$commands = $row['HI_N_script_cmds'];	
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
		$query = sprintf("UPDATE temperature SET HI_N_script_cmds='%s';", $commands);
		$result1  = $db1->exec($query);
	}
	
	
	// Temperature to check	 - LO_script_cmds
	$commands = "";
	$sd_query = sprintf("SELECT * FROM temperature", $cnt1);
	$result  = $db1->query($sd_query);
	foreach($result as $row)
	{
		$save_flag = 0;
		$commands = $row['LO_script_cmds'];	
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
		$query = sprintf("UPDATE temperature SET LO_script_cmds='%s';", $commands);
		$result1  = $db1->exec($query);
	}
	
	
	// Temperature to check	 - LO_N_script_cmds
	$commands = "";
	$sd_query = sprintf("SELECT * FROM temperature", $cnt1);
	$result  = $db1->query($sd_query);
	foreach($result as $row)
	{
		$save_flag = 0;
		$commands = $row['LO_N_script_cmds'];	
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
		$query = sprintf("UPDATE temperature SET LO_N_script_cmds='%s';", $commands);
		$result1  = $db1->exec($query);
	}
	
	//5 alarms to check - HI_script_cmds
	$commands = "";
	for($cnt1=1;$cnt1<6;$cnt1++)
	{
		$save_flag = 0;
		$sd_query = sprintf("SELECT * FROM io WHERE id='%d' AND type='alarm';", $cnt1);
		$result  = $db1->query($sd_query);
		foreach($result as $row)
		{
			$commands = $row['HI_script_cmds'];	
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
			$query = sprintf("UPDATE io SET HI_script_cmds='%s' WHERE id='%s' AND type='alarm';", $commands,$cnt1);
			$result1  = $db1->exec($query);
		}
		
	}
	
	//5 alarms to check - LO_script_cmds
	$commands = "";
	for($cnt1=1;$cnt1<6;$cnt1++)
	{
		$save_flag = 0;
		$sd_query = sprintf("SELECT * FROM io WHERE id='%d' AND type='alarm';", $cnt1);
		$result  = $db1->query($sd_query);
		foreach($result as $row)
		{
			$commands = $row['LO_script_cmds'];	
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
			$query = sprintf("UPDATE io SET LO_script_cmds='%s' WHERE id='%s' AND type='alarm';", $commands,$cnt1);
			$result1  = $db1->exec($query);
		}
	}
	
	//4 gxio to check - HI_script_cmds
	$commands = "";
	for($cnt1=1;$cnt1<5;$cnt1++)
	{
		$save_flag = 0;
		$sd_query = sprintf("SELECT * FROM io WHERE id='%d' AND type='gxio';", $cnt1);
		$result  = $db1->query($sd_query);
		foreach($result as $row)
		{
			$commands = $row['HI_script_cmds'];	
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
			$query = sprintf("UPDATE io SET HI_script_cmds='%s' WHERE id='%s' AND type='gxio';", $commands,$cnt1);
			$result1  = $db1->exec($query);
		}
	}
	
	//4 gxio to check - LO_script_cmds
	$commands = "";
	for($cnt1=1;$cnt1<5;$cnt1++)
	{
		$save_flag = 0;
		$sd_query = sprintf("SELECT * FROM io WHERE id='%d' AND type='gxio';", $cnt1);
		$result  = $db1->query($sd_query);
		foreach($result as $row)
		{
			$commands = $row['LO_script_cmds'];	
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
			$query = sprintf("UPDATE io SET LO_script_cmds='%s' WHERE id='%s' AND type='gxio';", $commands,$cnt1);
			$result1  = $db1->exec($query);
		}
	}
	
	//1 button to check - HI_script_cmds
	$commands = "";
	for($cnt1=1;$cnt1<2;$cnt1++)
	{
		$save_flag = 0;
		$sd_query = sprintf("SELECT * FROM io WHERE id='%d' AND type='btn';", $cnt1);
		$result  = $db1->query($sd_query);
		foreach($result as $row)
		{
			$commands = $row['HI_script_cmds'];	
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
			$query = sprintf("UPDATE io SET HI_script_cmds='%s' WHERE id='%s' AND type='btn';", $commands,$cnt1);
			$result1  = $db1->exec($query);
		}
	}
	
	//1 button to check - LO_script_cmds
	$commands = "";
	for($cnt1=1;$cnt1<2;$cnt1++)
	{
		$save_flag = 0;
		$sd_query = sprintf("SELECT * FROM io WHERE id='%d' AND type='btn';", $cnt1);
		$result  = $db1->query($sd_query);
		foreach($result as $row)
		{
			$commands = $row['LO_script_cmds'];	
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
			$query = sprintf("UPDATE io SET LO_script_cmds='%s' WHERE id='%s' AND type='btn';", $commands,$cnt1);
			$result1  = $db1->exec($query);
		}
	}
	
	//15 ping targets to check - HI_script_cmds
	$commands = "";
	for($cnt1=1;$cnt1<16;$cnt1++)
	{
		$save_flag = 0;
		$sd_query = sprintf("SELECT * FROM ping_targets WHERE id='%d';", $cnt1);
		$result  = $db1->query($sd_query);
		foreach($result as $row)
		{
			$commands = $row['HI_script_cmds'];	
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
			$query = sprintf("UPDATE ping_targets SET HI_script_cmds='%s' WHERE id='%s';", $commands,$cnt1);
			$result1  = $db1->exec($query);
		}
	}
	
	//15 ping targets to check - HI_N_script_cmds
	$commands = "";
	for($cnt1=1;$cnt1<16;$cnt1++)
	{
		$save_flag = 0;
		$sd_query = sprintf("SELECT * FROM ping_targets WHERE id='%d';", $cnt1);
		$result  = $db1->query($sd_query);
		foreach($result as $row)
		{
			$commands = $row['HI_N_script_cmds'];	
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
			$query = sprintf("UPDATE ping_targets SET HI_N_script_cmds='%s' WHERE id='%s';", $commands,$cnt1);
			$result1  = $db1->exec($query);
		}
	}
	
	//15 ping targets to check - LO_script_cmds
	$commands = "";
	for($cnt1=1;$cnt1<16;$cnt1++)
	{
		$save_flag = 0;
		$sd_query = sprintf("SELECT * FROM ping_targets WHERE id='%d';", $cnt1);
		$result  = $db1->query($sd_query);
		foreach($result as $row)
		{
			$commands = $row['LO_script_cmds'];	
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
			$query = sprintf("UPDATE ping_targets SET LO_script_cmds='%s' WHERE id='%s';", $commands,$cnt1);
			$result1  = $db1->exec($query);
		}
	}
	
	//15 ping targets to check - LO_N_script_cmds
	$commands = "";
	for($cnt1=1;$cnt1<16;$cnt1++)
	{
		$save_flag = 0;
		$sd_query = sprintf("SELECT * FROM ping_targets WHERE id='%d';", $cnt1);
		$result  = $db1->query($sd_query);
		foreach($result as $row)
		{
			$commands = $row['LO_N_script_cmds'];	
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
			$query = sprintf("UPDATE ping_targets SET LO_N_script_cmds='%s' WHERE id='%s';", $commands,$cnt1);
			$result1  = $db1->exec($query);
		}
	}
}



?>