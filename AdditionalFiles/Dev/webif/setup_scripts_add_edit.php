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
	$name = "";
	$notes = "";
	$commands = "";
	$script_cmds = "";
	
/////////////////////////////////////////////////////////////////
//                                                             //
//                    GET PROCESSING                           //
//                                                             //
/////////////////////////////////////////////////////////////////
	
if(isset ($_GET['action']))
{
	$action = $_GET['action'];
	$type =  $_GET['type'];
	
	
	if($type == "RELAY")
	{
		$context = "relayscripts";
		$image = "script.gif";
		$mytype = "relay";
		if($action == "edit") 
		{
			$id =  $_GET['id'];
			$title = "Edit Relay Script ".$id;
			$query = sprintf("SELECT * FROM scripts WHERE id='%s';", $id);
			$result  = $dbh->query($query);
			foreach($result as $row)
			{
				$name = $row['name'];
				$notes = $row['description'];
				$commands = $row['commands'];
			}
		}
		else if($action == "add")
		{
			$title = "Add New Relay Script";
		}
	}
	else
	{
		//IO Scripts
		$context = "ioscripts";
		$image = "ioscripts.gif";
		$mytype = "io";
		if($action == "edit") 
		{
			$id =  $_GET['id'];
			$title = "Edit IO Script ".$id;
			$query = sprintf("SELECT * FROM scripts WHERE id='%s';", $id);
			$result  = $dbh->query($query);
			foreach($result as $row)
			{
				$name = $row['name'];
				$notes = $row['description'];
				$commands = $row['commands'];
			}
		}
		else if($action == "add")
		{
			$title = "Add New IO Script";
		}
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
	header("Location: setup_scripts.php");
}

	
// OK Button	was clicked
if(isset ($_POST['save_btn']))
{	
	$id = $_POST['id'];
	$mytype = $_POST['mytype'];
	$name = $_POST['name'];
	$notes = $_POST['notes'];
	$image = $_POST['image'];
	$title = $_POST['title'];
	$context = $_POST['context'];
	$action = $_POST['action'];
	
	if(isset($_POST['script_cmds']))
	{
		foreach ($_POST['script_cmds'] as $script_cmds_Box)
		{
    	$commands = $commands . $script_cmds_Box;
  	}
  	if($action == "add")
  	{
  		$query = sprintf("INSERT INTO scripts VALUES (NULL, '%s', '%s', '%s', '%s')",$mytype, $name, $notes, $commands);
  		$result  = $dbh->exec($query);
  	}
  	else if($action == "edit")
  	{
  		$query = sprintf("UPDATE scripts SET type='%s', name='%s', description='%s', commands='%s' WHERE id=%s;",$mytype, $name, $notes, $commands, $id);
  		$result  = $dbh->exec($query);
  	}
		//echo $query;
		
		restart_some_services();
		//$alert_flag = "1";
		header("Location: setup_scripts.php?alert_flag=1");
	}
	else
	{
		$text = "No Commands Selected!";
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
    <link rel="stylesheet" href="css/sweetalert.css" />
    <link rel="stylesheet" href="css/ethertek.css">
    
    <!-- Java Scripts -->
		<script src="javascript/jquery.min.js"></script>
		<script src="javascript/bootstrap.min.js"></script>
		<script src="javascript/sweetalert.min.js"></script>
		<script src="javascript/conhelp.js"></script>
		<script src="javascript/ethertek.js"></script>
		<script language="javascript" type="text/javascript">
			<?php
				echo "SetContext('".$context."');";
			?>

		</script>
		<script>
   	
   	$().ready(function() {  
   	 // scripts	
     $('#scripts_addedit_additem').click(function() {  
        return !$('#avail_cmds option:selected').clone().appendTo('#script_cmds');  
     });  
     $('#scripts_addedit_delitem').click(function() {  
        return !$('#script_cmds option:selected').remove();   
     });
 		});
   	
   	function selectAllOptions(selStr)
			{
  			var selObj = document.getElementById(selStr);
  			for (var i=0; i<selObj.options.length; i++)
  				{
    				selObj.options[i].selected = true;
  				}
			}
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
	<?php
		echo "SetContext('".$context."');";
	?>
</script>
<!-- Main Wrapper -->
<div id="wrapper">
	<div class="content animate-panel" data-effect="fadeInUp">
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
      		<div class="panel-body" style="max-width:700px">
      	  	<form name='RelayIOScripts' action='setup_scripts_add_edit.php' method='post' class="form-horizontal" onsubmit="selectAllOptions('script_cmds');">  	
      	    	<fieldset>
      	    		<legend><img src="images/<?php echo $image; ?>"> <?php echo $title; ?></legend> 
      	    		<div class="form-group"><label class="col-sm-2 control-label" style="text-align:left; max-width:140px; min-width:140px">Script Name:</label>
              		<div class="col-sm-12" style="max-width:360px">
              			<div class="input-group">
              				<span class="input-group-addon"><i class="fa fa-info" style="max-width:16px; min-width:16px"></i></span>
              				<input type="text" class="form-control" name='name' value='<?php echo $name; ?>' required/>
              			</div>
              		</div>
              	</div>	    	    	
								
								<div class="form-group"><label class="col-sm-2 control-label" style="text-align:left; max-width:140px; min-width:140px">Script Notes:</label>
              		<div class="col-sm-12" style="max-width:360px">
              			<div class="input-group">
              				<span class="input-group-addon"><i class="fa fa-comment" style="max-width:16px; min-width:16px"></i></span>
              				<textarea  rows="6" cols="50" class="form-control" name='notes' required><?php echo $notes; ?></textarea>
              			</div>
              		</div>
              	</div>
								
								<legend>Script Commands</legend>
								<div class='table-responsive'>
									<table width='100%' class='table table-condensed' style='margin:auto;'>
    				   			<thead>
    				   				<tr>
    				   					<th width='48%' style='background:#D6DFF7; border: 1px solid white;'>
    				   						<div style='text-align:center'>Available Commands</div>
    				   					</th>
    				   					<th width='4%' style='background:#FFFFFF; border: 1px solid white;'>
    				   						<div style='text-align:center'></div>
    				   					</th>
    				   					<th width='48%' style='background:#D6DFF7; border: 1px solid white;'>
    				   						<div style='text-align:center'>Selected Commands</div>
    				   					</th>
											</tr>
										</thead>
										<tbody>
											<tr>
												<td>
													<select multiple size='10' class='form-control input-sm' style='max-width:100%; height:155px;' id='avail_cmds' name='avail_cmds[]'>
														<?php
															$query = sprintf("SELECT * FROM %s_script_cmds;", $mytype);
															$result  = $dbh->query($query);
															foreach($result as $row)
															{
																$command = $row['command'];
																$actual = $row['actual'];
																$name = $row['name'];
																$sep = $row['sep'];
																$state = $row['state'];
																
																$query = sprintf("<option value='%s'>%s%s%s%s</option>",$command,$actual,$name,$sep,$state);
																echo $query;
															}
														?>
													</select>
													<br>
														<p style='text-align:center;'>
															<input id='scripts_addedit_additem' type='button' class='btn btn-success' name='scripts_addedit_additem' value='ADD' onMouseOver='mouse_move("sd_cmd_add");' onMouseOut='mouse_move();'>
														</p>
												</td>
												<td style="text-align:center; vertical-align:middle; color:green;">
													<i class='fa fa-arrow-left'></i><br><i class='fa fa-arrow-right'></i><br><br><br><br>
												</td>		
												<td>
													<select multiple class='form-control input-sm' style='max-width:100%; height:155px;' id='script_cmds' name='script_cmds[]'>
													<?php
														if(strlen($commands)!==0)
														{
															$buf = str_split($commands, 2);
															$count = count($buf);
															for($ii=0; $ii < $count; $ii++)
															{
																echo"<option value='".$buf[$ii]."'>";
																$query = sprintf("SELECT * FROM %s_script_cmds where command = '%s';", $mytype,$buf[$ii]);
																$result  = $dbh->query($query);
																foreach($result as $row)
																{
																	$actual = $row['actual'];
																	$name = $row['name'];
																	$sep = $row['sep'];
																	$state = $row['state'];
																}
																$query = sprintf("%s%s%s%s",$actual,$name,$sep,$state);
																echo $query."</option>";
															}
														}
													?>
													</select>
													<br>
														<p style='text-align:center;'>
															<input id='scripts_addedit_delitem' type='button' class='btn btn-success' name='scripts_addedit_delitem' value='Remove' onMouseOver='mouse_move("sd_cmd_del");' onMouseOut='mouse_move();'>
														</p>
												</td>
											</tr>
										</tbody>
									</table>
									<hr>
								</div>
													
              	<div class="form-group">
              		<div class="col-sm-12">
              			<input type="hidden" name="id" value="<?php echo $id; ?>">
              			<input type="hidden" name="mytype" value="<?php echo $mytype; ?>">
              			<input type="hidden" name="image" value="<?php echo $image; ?>">
              			<input type="hidden" name="title" value="<?php echo $title; ?>">
              			<input type="hidden" name="context" value="<?php echo $context; ?>">
              			<input type="hidden" name="action" value="<?php echo $action; ?>">
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
