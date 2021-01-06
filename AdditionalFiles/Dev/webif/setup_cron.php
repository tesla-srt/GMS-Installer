<?php
	error_reporting(E_ALL);
	include "lib.php";
	$hostname = trim(file_get_contents("/etc/hostname"));
	$alert_flag = "0";
	$id = "0";
	
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
			$text = "Cron Job # " . $myid . " Updated";		   						
			$alert_flag = "2";
		}	
	}
	else if($action == "add")
	{
		$success = $_GET['success'];
		if($success = "yes")
		{
			$text = "New Cron Job Added";		   						
			$alert_flag = "2";
		}	
	}
}



if(isset ($_GET['command']))
{
	$command = $_GET['command'];
	if($command == "set")
	{
		$state = $_GET['cron'];
		$id = $_GET['id'];
		if($state == "off")
		{
			//Enable Cron Job
  		$filename = "/etc/crontabs/root";
    	$file_content = file($filename);
    	$cron_command = $file_content[$id-1];
    	$cron_command = ltrim($cron_command,"#");
    	$fp = fopen($filename, "w+"); 
    	$file_content[$id-1] = $cron_command;
    	fwrite($fp, implode($file_content, ''));
    	fclose($fp);
    	system("/etc/init.scripts/S93crond restart  2>/dev/null 1>/dev/null");
    	$text = "Cron Job Enabled";		   						
			$alert_flag = "2";
		}
		else
		{
			//Disable Cron Job
  		$filename = "/etc/crontabs/root";
    	$file_content = file($filename);
    	$cron_command = "#".$file_content[$id-1];
    	$fp = fopen($filename, "w+"); 
    	$file_content[$id-1] = $cron_command;
    	fwrite($fp, implode($file_content, ''));
    	fclose($fp);
    	system("/etc/init.scripts/S93crond restart  2>/dev/null 1>/dev/null");
    	$text = "Cron Job Disabled";		   						
			$alert_flag = "2";
		}
		
	}
	
}
	
if(isset ($_GET['cron']))
{
	$action = $_GET['cron'];
	$id = $_GET['id'];
	if($action == "run")
	{
		$myFile = "/etc/crontabs/root";
		$lines = file($myFile); //file in to an array
		$id = $id - 1;
    list($mins, $hours, $days, $months, $weekdays, $command) = explode(' ', $lines[$id], 6);
    $command = $command . " > /dev/null 2>&1";
    exec($command);
    $text = "Cron Command Executed";		   						
		$alert_flag = "2";
	}
	
	if($action == "delete")
	{
		$id = $_GET['id'];
		$alert_flag = "3";
	}
}

if(isset ($_GET['confirm']))
{
	$action = $_GET['confirm'];
	$id = $_GET['id'];
	if($action == "delete")
	{
		$command = sprintf("cat /etc/crontabs/root |sed -e '%dd' -i /etc/crontabs/root", $id);
		system($command);
		system("/etc/init.scripts/S93crond restart > /dev/null");	   						
		$alert_flag = "1";
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
			SetContext('cron');
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
	SetContext('cron');
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
  		<div class="col-sm-12"><legend>Cron Scheduler Interface</legend></div>
  	</div>
  	<div class="row">
  		<div class="col-sm-2"><a href="setup_cron_add_edit.php?command=add" onMouseOver="mouse_move('sd_cron_tab_add');" onMouseOut='mouse_move();'><img src='images/schedule_add.gif' title='Add New Cron Job'><h5>Add New Cron Job</h5></a></div>
  	</div>

  	<form name='Cron' action='setup_cron.php' method='post' class="form-horizontal">  	
    	<fieldset>
  			<div class="row">
    			<div class="col-sm-12">
    		  	<div class="hpanel3">
    		  	  <div class="panel-body" style="text-align:center; background:#F1F3F6;border:none;">
    				  	<div class="table-responsive">
    				   		<table width="100%" class="table table-striped table-condensed table-hover">
    				   			<thead>
    				   				<tr>
    				   					<th width="5%" style="background:#ABBEEF; border: 1px solid white;">
    				   						<div style="text-align:center">Status</div>
    				   					</th>
    				   					<th width="5%" style="background:#D6DFF7; border: 1px solid white;">
    				   						<div style="text-align:center">ID</div>
    				   					</th>
    				   					<th width="50%" style="background:#D6DFF7; border: 1px solid white;">
    				   						<div>Commands</div>
    				   					</th>
    				   					<th width="40%" style="background:#D6DFF7; border: 1px solid white;">
    				   						<div style="text-align:left">Actions</div>
    				   					</th>
    				   				</tr>
    				   			</thead>
    				   			<tbody>
    				   				<?php
    				   					$myFile = "/etc/crontabs/root";
												$lines = file($myFile); //file in to an array
    				   					$rows = count($lines); //how many lines if file
    				   					for($ii=0;$ii < $rows; $ii++)
    				   					{
    				   						list($mins, $hours, $days, $months, $weekdays, $command) = explode(' ', $lines[$ii], 6);
    				   						$id = $ii + 1;
    				   						echo "<tr>";
    				   						echo "	<td>";
    				   						if($mins[0] == "#")
    				   						{
    				   							$cron_active = "off";
    				   							$title = "Enabled Cron Job #".$id;
    				   						}
    				   						else
    				   						{
    				   							$cron_active = "on";
    				   							$title = "Disable Cron Job #".$id;
    				   						}
    				   						echo "		<a href='setup_cron.php?command=set&cron=" . $cron_active . "&id=" . $id . "'><img src='images/serv" . $cron_active . ".gif' width='16' height='16' title='" . $title . "'></a>";
    				   						echo "	</td>";
    				   						echo "	<td>";
    				   						echo "		<a href='setup_cron_add_edit.php?command=edit&cron=" . $cron_active . "&id=" . $id . "' title='Edit Cron Job #" . $id . "'><u>" . $id . "</u></a>";
    				   						echo "	</td>";
    				   						echo "	<td><div style='text-align:left'>";
    				   						echo "		<a href='setup_cron_add_edit.php?command=edit&cron=" . $cron_active . "&id=" . $id . "' title='Edit Cron Job #" . $id . "'><u class='dotted'>" . $command . "</u></a>";
    				   						//echo $command;
    				   						echo "	</div></td>";
    				   						echo "	<td><div style='text-align:left'>";
    				   						echo "		<a href='setup_cron.php?cron=run&id=" . $id . "' onMouseOver ='mouse_move(\"sd_cron_tab_exec\");' onMouseOut='mouse_move();'><img  src='images/on.gif' width='16' height='16' title='EXECUTE Cron Job'></a>";
    				   						echo "		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
    				   						echo "		<a href='setup_cron.php?cron=delete&id=" . $id . "' onMouseOver='mouse_move(\"sd_cron_tab_delete\");'	onMouseOut='mouse_move();'><img src='images/off.gif' width='16' height='16' title='DELETE Cron Job'></a>";
    				   						echo "	</div></td>";
    				   						echo "</tr>";
    				   					}
    				   				?>
    				   			</tbody>
  								</table>      	    
    		  	  	</div> <!-- END TABLE RESPONSIVE -->
    		  	  	<div class="form-group">
        					<div class="col-sm-1">
        						<button name="cancel_btn" class="btn btn-primary" type="submit" onMouseOver="mouse_move(&#039;b_cancel&#039;);" onMouseOut="mouse_move();" formnovalidate><i class="fa fa-times"></i> Cancel</button>
        					</div>
        				</div>
    		  		</div> <!-- END PANEL BODY --> 
    		  	</div> <!-- END HPANEL3 --> 
    		  </div> <!-- END COL-sm-12 --> 
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
echo"  text: 'Cron Job Deleted!',";
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
	echo"		title: 'Delete Cron Job ID# " . $id . "<br>Are you really sure?',";
	echo"		type: 'warning',";
	echo"		showCancelButton: true,";
	echo"		html: true,";
	echo"		confirmButtonColor: '#DD6B55',";
	echo"		confirmButtonText: 'Yes, delete it!',";
	echo"		closeOnConfirm: false";
	echo"	},";
	echo"	function(){";
	echo"		window.location.href = 'setup_cron.php?confirm=delete&id=" . $id . "';";
	echo"	});";
	echo"</script>";
}

echo "</body>";
echo "</html>";

?>









 
















