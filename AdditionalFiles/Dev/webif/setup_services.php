<?php
	error_reporting(E_ALL);
	include "lib.php";
	$hostname = trim(file_get_contents("/etc/hostname"));
	$alert_flag = "0";
	$restart = "0";
	$hup = "0";
	
	$dbh = new PDO('sqlite:/etc/rms100.db');
	
	$result  = $dbh->query("SELECT * FROM display_options;");			
	foreach($result as $row)
	{
		$screen_animations = $row['screen_animations'];
	}
	
		
/////////////////////////////////////////////////////////////////
//                                                             //
//                    GET PROCESSING                           //
//                                                             //
/////////////////////////////////////////////////////////////////


	if(isset ($_GET['svc_mgr']))
	{
		$mode = $_GET['svc_mgr'];
		if($mode == "edit")
		{
			$id = $_GET['id'];
			$name = $_GET['name'];
			$desc = $_GET['desc'];
			$rc = $_GET['rc'];
			$rl = $_GET['rl'];
			$tsr = $_GET['tsr'];
			$msg = "";
			svc_mgr_deladdedit($mode,$id,$name,$desc,$rc,$rl,$tsr,$msg);
			exit(0);
 		}
		
		if($mode == "delete")
		{
			$id = $_GET['id'];
			$name = $_GET['name'];
			$desc = $_GET['desc'];
			$rc = $_GET['rc'];
			$rl = $_GET['rl'];
			$tsr = $_GET['tsr'];
			$msg = "";
			svc_mgr_deladdedit($mode,$id,$name,$desc,$rc,$rl,$tsr,$msg);
			exit(0);
 		}
		
		if(($mode == "stop") || ($mode == "start"))
		{
			$name = $_GET['name'];
			$command = sprintf("/etc/init.scripts/%s %s >> /tmp/svc_mgr 2>&1", $name, $mode);
			system($command);
			sleep(3);
			unlink("/tmp/svc_mgr");
 		}
		
		if($mode == "restart")
		{
			$restart = "1";
			$name = $_GET['name'];
			$desc = $_GET['desc'];
 		}
		
		if($mode == "hup")
		{
			$hup = "1";
			$pid = $_GET['pid'];
			$desc = $_GET['desc'];
 		}
		
		if($mode == "add_init")
		{
			$rc = $_GET['rc'];
			$command = sprintf("cp -p /etc/init.scripts/%s /etc/init.d/%s",$rc, $rc);
			system($command);
 		}
		
		if($mode == "del_init")
		{
			$rc = $_GET['rc'];
			$command = sprintf("rm -f /etc/init.d/%s",$rc);
			system($command);
 		}
	}
	
	// CONFIRM DELETE Button was clicked
	if(isset ($_GET['confirm']))
	{
		$svc_id = $_GET['id'];
		$sql = sprintf("DELETE from svc_mgr WHERE id='%s'", $svc_id);
		$result  = $dbh->exec($sql);
		$alert_flag = "1";
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

// ADD NEW SERVICE Button was clicked from main form
if(isset ($_POST['add_new_service_btn']))
	{
		svc_mgr_deladdedit("add", "0", "", "", "", "", "", "");
		exit(0);
	}	

// SERVICE ADD Button was clicked
if(isset ($_POST['service_add_btn']))
	{
		$svc_name = $_POST['name'];
		$svc_desc = $_POST['desc'];
		$svc_rc = $_POST['rc'];
		$svc_rl = $_POST['rl'];
		if(isset ($_POST['tsr']))
		{
			$svc_tsr = "1";
		}
		else
		{
			$svc_tsr = "0";
		}
		
		$sql = sprintf("INSERT INTO svc_mgr VALUES (NULL, '%s', '%s', '%s', '%s', '%s')", $svc_name, $svc_desc, $svc_rc, $svc_rl, $svc_tsr);
		$result  = $dbh->exec($sql);
		
		$text = "Service " . $svc_name . " added to database.";
		$alert_flag = "2";
	}		

// SERVICE EDIT Button was clicked
if(isset ($_POST['service_edit_btn']))
	{
		$svc_id = $_POST['id'];
		$svc_name = $_POST['name'];
		$svc_desc = $_POST['desc'];
		$svc_rc = $_POST['rc'];
		$svc_rl = $_POST['rl'];
		if(isset ($_POST['tsr']))
		{
			$svc_tsr = "1";
		}
		else
		{
			$svc_tsr = "0";
		}
		
		$sql = sprintf("UPDATE svc_mgr SET name='%s', desc='%s', rc='%s', rl='%s', tsr='%s' WHERE id='%s'", $svc_name, $svc_desc, $svc_rc, $svc_rl, $svc_tsr, $svc_id);
		$result  = $dbh->exec($sql);
		
		$text = "Service " . $svc_name . "successfully changed.";
		$alert_flag = "2";
	}	

// SERVICE DELETE Button was clicked
if(isset ($_POST['service_delete_btn']))
	{
		$id = $_POST['id'];
		$name = $_POST['name'];
		$desc = $_POST['desc'];
		$rc = $_POST['rc'];
		$rl = $_POST['rl'];
		$tsr = $_POST['tsr'];
		$msg = "";
		$alert_flag = "3";
		svc_mgr_deladdedit("delete",$id,$name,$desc,$rc,$rl,$tsr,$msg);
		exit(0);
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
    <link rel="stylesheet" href="css/sweetalert.css" />
    <link rel="stylesheet" href="css/ethertek.css">
    
    <!-- Java Scripts -->
		<script src="javascript/jquery.min.js"></script>
		<script src="javascript/bootstrap.min.js"></script>
		<script src="javascript/sweetalert.min.js"></script>
		<script src="javascript/conhelp.js"></script>
		<script src="javascript/ethertek.js"></script>
		<script language="javascript" type="text/javascript">
			SetContext('setup_svc_mgr');
		</script>
		
</head>
<body class="fixed-navbar fixed-sidebar">
	<?php
	if($restart == "1")
	{
		echo "<div class='splash'> <div class='solid-line'></div><div class='splash-title'><h1>Restarting " . $desc . "... Please Wait...</h1><div class='spinner'> <div class='rect1'></div> <div class='rect2'></div> <div class='rect3'></div> <div class='rect4'></div> <div class='rect5'></div> </div> </div> </div>";
	}
	
	else if($hup == "1")
	{
		echo "<div class='splash'> <div class='solid-line'></div><div class='splash-title'><h1>Sending HUP signal to " . $desc . ". Please Wait...</h1><div class='spinner'> <div class='rect1'></div> <div class='rect2'></div> <div class='rect3'></div> <div class='rect4'></div> <div class='rect5'></div> </div> </div> </div>";
	}
	
	else
	{
		echo "<div class='splash'> <div class='solid-line'></div><div class='splash-title'><h1>Loading... Please Wait...</h1><div class='spinner'> <div class='rect1'></div> <div class='rect2'></div> <div class='rect3'></div> <div class='rect4'></div> <div class='rect5'></div> </div> </div> </div>";
	}
	?>


<!--[if lt IE 7]>
<p class="alert alert-danger">You are using an <strong>old</strong> web browser. Please upgrade your browser to improve your experience.</p>
<![endif]-->

<?php start_header(); ?>

<?php left_nav("setup"); ?>
<script language="javascript" type="text/javascript">
	SetContext('setup_svc_mgr');
</script>
<!-- Main Wrapper -->

<?php
	if($restart == "1")
	{
		if($name == "S41lighttpd" || $name == "S42lighttpd-ssl")
		{
			$command = sprintf("/etc/init.scripts/%s reload >> /tmp/svc_mgr 2>&1", $name);
			system($command);
		}
		else
		{
			$command = sprintf("/etc/init.scripts/%s restart >> /tmp/svc_mgr 2>&1", $name);
			system($command);
		}
		sleep(3);
		unlink("/tmp/svc_mgr");
		$text = "Service <b>" . $desc . "</b> restarted.";
		$alert_flag = "2";
	}
	
	if($hup == "1")
	{
		$command = sprintf("kill -HUP %s", $pid);
		system($command);
		$text = "HUP Signal sent to the <b>" . $desc . "</b>.";
		$alert_flag = "2";
	}
?>


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
  		<div class="col-sm-12"><legend><img src='images/btn_restart-services_bg.gif'> Services</legend></div>
  	</div>
  	<form name='ServiceManager' action='setup_services.php' method='post' class="form-horizontal">  	
    	<fieldset>
  			<div class="row">
    			<div class="col-sm-12">
    		  	<div class="hpanel3">
    		  	  <div class="panel-body" style="text-align:center; background:#F1F3F6;border:none;">
    				  	<div class="table-responsive">
    				   		<table width="100%" class="table table-striped table-condensed table-hover">
    				   			<thead>
    				   				<tr>
    				   					<th width="4%" style="background:#ABBEEF; border: 1px solid white;">
    				   						<div style="text-align:center">Status</div>
    				   					</th>
    				   					<th width="6%" style="background:#D6DFF7; border: 1px solid white;">
    				   						<div style="text-align:center">PID</div>
    				   					</th>
    				   					<th width="30%" style="background:#D6DFF7; border: 1px solid white;">
    				   						<div>Name</div>
    				   					</th>
    				   					<th width="40%" style="background:#D6DFF7; border: 1px solid white;">
    				   						<div style="text-align:left">Description</div>
    				   					</th>
    				   					<th width="8%" style="background:#D6DFF7; border: 1px solid white;">
    				   						<div style="text-align:center">Actions</div>
    				   					</th>
    				   					<th width="5%" style="background:#D6DFF7; border: 1px solid white;">
    				   						<div style="text-align:center">Delete</div>
    				   					</th>
    				   					<th width="7%" style="background:#D6DFF7; border: 1px solid white;">
    				   						<div style="text-align:center">Start On Boot</div>
    				   					</th>
    				   				</tr>
    				   			</thead>
    				   			<tbody>
    				   				<?php 
    				   					$dbh = new PDO('sqlite:/etc/rms100.db');
    				   					$result  = $dbh->query("SELECT * FROM svc_mgr;");			
												foreach($result as $row)
												{
													$onboot = "0";
													$pid = "0";
													$ball = "none";
													$psid = $row['id'];
													$psname = $row['name'];
													$psdesc = $row['desc'];
													$psrc = $row['rc'];
													$psrl = $row['rl'];
													$pstsr = $row['tsr'];
													
													$command = sprintf("pidof %s > /tmp/mypid",$psname);
													system($command);
													if (filesize('/tmp/mypid') == 0)
													{
    												$pid = "0";
    												$ball = "none";
													}
													else
													{
														$mypid = file_get_contents("/tmp/mypid");
														$pid = $mypid;
														$ball = "warn";
													}
													unlink("/tmp/mypid");
													
													$tmp_file = sprintf("/etc/init.d/%s", $psrc);
													if(file_exists($tmp_file))
													{
														if($pid == "0")
														{
															$onboot = "1";
															$ball = "off";
														}
														else
														{
															$onboot = "1";
															$ball = "on";
														}
														
													}
													else
													{
														$onboot = "0";
														if($pid == "0")
														{
															$ball = "none";
														}
														else
														{
															$ball = "warn";
														}
													}
													
													
													if($pstsr == "0"){ $ball = "none"; }
													
													
													
													echo "<tr>";
													$html = sprintf("<td><div style='text-align:center' onMouseOver = \"mouse_move('svc_mgr_%s');\" onMouseOut='mouse_move();'>",$ball);
													echo $html;
													
													
													
													
													
													$html = sprintf("<img src='images/serv%s.gif' width='16' height='16'></div></td><td><div>%s</div></td>", $ball, $pid);
													echo $html;
													echo "<td><div style='text-align:left'>";
													$html = sprintf("<a href='setup_services.php?svc_mgr=edit&id=%s&name=%s&desc=%s&rc=%s&rl=%s&tsr=%s'><u class='dotted'>%s</u></a></div></td>", $psid, $psname, $psdesc, $psrc, $psrl, $pstsr, $psname);
													echo $html;
													echo "<td><div style='text-align:left'>" . $psdesc . "</div></td><td><div style='text-align:left'>";
													
													
													if($pid == "0")
													{
														echo "<a href='setup_services.php?svc_mgr=start&name=" . $psrc . "'>";
														echo "<img src='images/on.gif' width='16' onMouseOver = \"mouse_move('svc_mgr_start');\" onMouseOut='mouse_move();' height='16' title='START Service'></a>";
													}
													else
													{
														echo "<a href='setup_services.php?svc_mgr=stop&name=" . $psrc . "'>";
														echo "<img src='images/stop.gif' width='16' onMouseOver = \"mouse_move('svc_mgr_stop');\" onMouseOut='mouse_move();' height='16' title='STOP Service'></a>";
													}
													
													
													
													if(($pstsr == "1") && ($pid !== "0"))
													{
														
														echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href='setup_services.php?svc_mgr=restart&name=" . $psrc . "&desc=" . $psdesc . "'>";
														echo "<img src='images/restart.gif' width='16' height='16' onMouseOver = \"mouse_move('svc_mgr_restart');\" onMouseOut='mouse_move();' title='RESTART Service'></a>";
														
														
														
														echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href='setup_services.php?svc_mgr=hup&pid=" . $pid . "&desc=" . $psdesc . "'>";
														echo "<img src='images/preferences.gif' width='16' height='16' onMouseOver = \"mouse_move('svc_mgr_hup');\" onMouseOut='mouse_move();' title='HUP Signal'></a>";
													}	
									
													
													echo "</div></td>";
													
													echo "<td><div style='text-align:center'>";
													$html = sprintf("<a href='setup_services.php?svc_mgr=delete&id=%s&name=%s&desc=%s&rc=%s&rl=%s&tsr=%s'>", $psid, $psname, $psdesc, $psrc, $psrl, $pstsr);
													echo $html;
													echo "<img src='images/off.gif' width='16' height='16' onMouseOver = \"mouse_move('svc_mgr_del');\" onMouseOut='mouse_move();' title='Delete Service from list'></a>";
													echo "</div></td>";
													
													echo "<td><div style='text-align:center'>";
													if($onboot == "1")
														{
															$html = sprintf("<a href='setup_services.php?svc_mgr=del_init&rl=%s&rc=%s'>", $psrl, $psrc);
															echo $html;
															echo "<img src='images/ok.gif' width='16' height='16' onMouseOver = \"mouse_move('svc_mgr_del_init');\" onMouseOut='mouse_move();' title='Will start on bootup.'></a>";
														}
													else
														{
														$html = sprintf("<a href='setup_services.php?svc_mgr=add_init&rl=%s&rc=%s'>", $psrl, $psrc);
															echo $html;
															echo "<img src='images/servnone.gif' width='16' height='16' onMouseOver = \"mouse_move('svc_mgr_add_init');\" onMouseOut='mouse_move();' title='Will NOT start on bootup.'></a>";
														}
													echo "</div></td>";
													echo "</tr>";
												}
    				   				?>
    				   			</tbody>
  								</table>      	    
    		  	  	</div> <!-- END TABLE RESPONSIVE -->
    		  	  	
    		  	  	<div class="form-group">
              		<div class="col-sm-12">
              	  	<button name="refresh_btn" class="btn btn-success " type="submit" onMouseOver="mouse_move(&#039;svc_mgr_refresh&#039;);" onMouseOut="mouse_move();"><i class="fa fa-check"></i> Refresh</button>
              	  	<button name="cancel_btn" class="btn btn-primary " type="submit" onMouseOver="mouse_move(&#039;svc_mgr_cancel&#039;);" onMouseOut="mouse_move();"><i class="fa fa-times"></i> Cancel</button>
              	  	<button name="add_new_service_btn" class="btn btn-warning" type="submit" onMouseOver="mouse_move(&#039;svc_mgr_add&#039;);" onMouseOut="mouse_move();" formnovalidate><i class="fa fa-plus"></i> Add Service</button>
              	  </div>
              	</div>
    		  	  	
    		  	  	
    		  	  	
    		  	  	
    		  	  	
    		  	  	
    		  	  	
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
echo"  text: 'Service Deleted from Database!',";
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

echo "</body>";
echo "</html>";





function svc_mgr_deladdedit($mode, $id, $name, $desc, $rc, $rl, $tsr, $msg)
{
	global $alert_flag; //, $mode, $id, $name, $desc, $rc, $rl, $tsr, $msg;
	if($mode == "add")
		{
			$header = " ADD new service";
			$itype = "required";
		}
	else if($mode == "delete")
		{
			$header = " DELETE existing service";
			$itype = "disabled";
		}
	else
		{
			$header = " EDIT existing service";
			$itype = "required";
		}
	
	if($tsr == "1")
		{
			$state = "checked";
		}
	else
		{
			$state = "";
		}	
	
	setup_top_header();
	start_header();
	left_nav("setup");
	echo "<script language='javascript' type='text/javascript'>";
	echo "SetContext('setup');";
	echo "</script>";
	echo "<!-- Main Wrapper -->";
	echo "<div id='wrapper'>";
	echo "	<div class='content animate-panel' data-effect='fadeInUp'>";
	echo "  	<!-- INFO BLOCK START -->";
	echo "  	<div class='row'>";
	echo "    	<div class='col-md-4'>";
	echo "      	<div class='hpanel4'>";
	echo "      		<div class='panel-body'>";
	echo "      	  	<form name='Service' action='setup_services.php' method='post' class='form-horizontal'>";  	
	echo "      	    	<fieldset>";
	echo "      	    		<legend><img src='images/btn_restart-services_bg.gif'> " . $header . "</legend>";
	echo "      	    		<div class='form-group'><label class='col-sm-4 control-label'>Service Name:</label>";
	echo "              		<div class='col-sm-8'>";
	echo "              			<input type='text' class='form-control' name='name' value='" . $name . "' " . $itype . " />";
	echo "              		</div>";
	echo "              	</div>"; 	    									
	echo "								<div class='form-group'><label class='col-sm-4 control-label'>Description:</label>";
	echo "              		<div class='col-sm-8'>";
	echo "              			<input type='text' class='form-control' name='desc' value='" . $desc . "' " . $itype . " />";
	echo "              		</div>";
	echo "              	</div>";         	
	echo "								<div class='form-group'><label class='col-sm-4 control-label'>Init Script:</label>";
	echo "              		<div class='col-sm-8'>";
	echo "              			<input type='text' class='form-control' name='rc' value='" . $rc . "' " . $itype . " />";
	echo "              		</div>";
	echo "              	</div>"; 
	echo "								<div class='form-group'><label class='col-sm-4 control-label'>Link Number:</label>";
	echo "              		<div class='col-sm-3'>";
	echo "              			<input type='text' class='form-control' name='rl' value='" . $rl . "' " . $itype . " />";
	echo "              		</div>";
	echo "              	</div>"; 
	echo "								<div class='form-group'><label class='col-sm-4 control-label'>Service is a Daemon:</label>";
	echo "              		<div class='col-sm-8'>";
														if($itype == "disabled")
														{
	echo "											<div class='checkbox checkbox-success'>";
  echo "       				 					<input id='tsr' type='checkbox' name='tsr' ".$state." disabled/>";
  echo "               					<label for='tsr'></label>";     
  echo "             					</div>";
														}
														else
														{
	echo "											<div class='checkbox checkbox-success'>";
  echo "       				 					<input id='tsr' type='checkbox' name='tsr' ".$state." />";
  echo "               					<label for='tsr'></label>";     
  echo "             					</div>";														
														}
									
	echo "              		</div>";
	echo "              	</div>";	
	echo "              	<div class='form-group'>";
	echo "              		<div class='col-sm-8 col-sm-offset-3'>";
														if($mode == "delete")
														{
	echo "              	  		<button name='service_delete_btn' class='btn btn-danger' type='submit' onMouseOver='mouse_move(&#039;svc_mgr_del&#039;);' onMouseOut='mouse_move();'><i class='fa fa-times' ></i> Delete Service</button>";														
	echo " 											<input type='hidden' name='name' value='" . $name . "'>";
	echo " 											<input type='hidden' name='desc' value='" . $desc . "'>";
	echo " 											<input type='hidden' name='rc' value='" . $rc . "'>";
	echo " 											<input type='hidden' name='rl' value='" . $rl . "'>";
	echo " 											<input type='hidden' name='tsr' value='" . $tsr . "'>";
														}
														else if($mode == "add")
														{
	echo "              	  		<button name='service_add_btn' class='btn btn-success' type='submit' onMouseOver='mouse_move(&#039;svc_mgr_add&#039;);' onMouseOut='mouse_move();'><i class='fa fa-check' ></i> Add New Service</button>";														
														}													
														else //edit
														{
	echo "              	  		<button name='service_edit_btn' class='btn btn-success' type='submit' onMouseOver='mouse_move(&#039;b_apply&#039;);' onMouseOut='mouse_move();'><i class='fa fa-check' ></i> Save</button>";														
														}	
	echo "              	  	<button name='service_cancel_btn' class='btn btn-primary' type='submit' onMouseOver='mouse_move(&#039;b_cancel&#039;);' onMouseOut='mouse_move();' formnovalidate><i class='fa fa-times'></i> Cancel</button>";
	echo "              	  </div>";
	echo "              	</div>";
	echo " 								<input type='hidden' name='id' value='" . $id . "'>";
	echo " 								<input type='hidden' name='mode' value='" . $mode . "'>";
	echo "              </fieldset>";  
	echo "						</form>";
	echo "      		</div> <!-- END PANEL BODY -->"; 
	echo "      	</div> <!-- END PANEL WRAPPER -->"; 
	echo "      </div>  <!-- END COL -->"; 
	echo "    </div> <!-- END ROW -->";
	echo "  </div> <!-- END CONTENT -->";    
	echo "</div> <!-- END Main Wrapper -->";
	
	if($alert_flag == "3")
	{
		echo"<script>";
		echo"	swal({";
		echo"		title: 'Delete Service ID# " . $id . "<br><span style=\'color:#F8BB86\'>" . $name . "</span><br>Are you really sure?',";
		echo"		type: 'warning',";
		echo"		showCancelButton: true,";
		echo"		html: true,";
		echo"		confirmButtonColor: '#DD6B55',";
		echo"		confirmButtonText: 'Yes, delete it!',";
		echo"		closeOnConfirm: false";
		echo"	},";
		echo"	function(){";
		echo"		window.location.href = 'setup_services.php?confirm=delete&id=" . $id . "';";
		echo"	});";
		echo"</script>";
	}
	
	echo "</body>";
	echo "</html>";
}









 
















