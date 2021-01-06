<?php
	error_reporting(E_ALL);
	include "lib.php";
	
	if(empty($_GET) && empty($_POST)) 
	{ 
		/* no parameters passed*/
		echo "This web page must be accessed through the RMS web interface!";
		exit(0);
	}

	$hostname = trim(file_get_contents("/etc/hostname"));
	$header = "";
	$alert_flag = "0";
	$query = "";
	$name ="";
	$notes = "";
	
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


	
if(isset($_GET['relay']))
	{
			$relay_num = $_GET['relay'];
			$query = sprintf("SELECT * FROM relays WHERE id='%d';",$relay_num);
			$result  = $dbh->query($query);
			foreach($result as $row)
			{
				$name = $row['name'];
				$notes = $row['notes'];
				$nc_color = $row['nc_color'];
				$no_color = $row['no_color'];
				
				$header = "Edit Relay #" . $relay_num;
			}
			
			if($relay_num == "1")
			{
				$query = sprintf("SELECT * FROM relay_script_cmds WHERE command='00';");
				$result  = $dbh->query($query);
				foreach($result as $row)
				{
					$desc1 = $row['state'];
				}
				$query = sprintf("SELECT * FROM relay_script_cmds WHERE command='01';");
				$result  = $dbh->query($query);
				foreach($result as $row)
				{
					$desc2 = $row['state'];
				}
			}
			
			if($relay_num == "2")
			{
				$query = sprintf("SELECT * FROM relay_script_cmds WHERE command='02';");
				$result  = $dbh->query($query);
				foreach($result as $row)
				{
					$desc1 = $row['state'];
				}
				$query = sprintf("SELECT * FROM relay_script_cmds WHERE command='03';");
				$result  = $dbh->query($query);
				foreach($result as $row)
				{
					$desc2 = $row['state'];
				}
			}
			
			if($relay_num == "3")
			{
				$query = sprintf("SELECT * FROM relay_script_cmds WHERE command='04';");
				$result  = $dbh->query($query);
				foreach($result as $row)
				{
					$desc1 = $row['state'];
				}
				$query = sprintf("SELECT * FROM relay_script_cmds WHERE command='05';");
				$result  = $dbh->query($query);
				foreach($result as $row)
				{
					$desc2 = $row['state'];
				}
			}
			
			if($relay_num == "4")
			{
				$query = sprintf("SELECT * FROM relay_script_cmds WHERE command='06';");
				$result  = $dbh->query($query);
				foreach($result as $row)
				{
					$desc1 = $row['state'];
				}
				$query = sprintf("SELECT * FROM relay_script_cmds WHERE command='07';");
				$result  = $dbh->query($query);
				foreach($result as $row)
				{
					$desc2 = $row['state'];
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
	header("Location: relays.php");
}

// Save Button	was clicked
if(isset ($_POST['save_btn']))
{
	$relay_num = $_POST['relay_num'];
	$name = $_POST['name'];
	$notes = $_POST['notes'];
	$desc1 = $_POST['desc1'];
	$desc2 = $_POST['desc2'];
	
	$no_color = $_POST['no_color'];
	$nc_color = $_POST['nc_color'];
	//echo "Hi Icon: ".$hi_icon." Low Icon :".$lo_icon;
	if($no_color == $nc_color)
	{
		$text = "The Normally Open color cannot be the same as the Normally Closed color!";
		$alert_flag = "2";
		goto noSave;
	}
	if($desc1 == $desc2)
	{
		$text = "Names for the Normally Open State and the Normally Closed State must be different!";
		$alert_flag = "2";
		goto noSave;
	}
	
	$query = sprintf("UPDATE relays SET name='%s', notes='%s', nc_color='%s', no_color='%s' WHERE id='%d';",$name, $notes, $nc_color, $no_color, $relay_num);
	$result  = $dbh->exec($query); 
	
	if($relay_num == "1")
	{
		$query = sprintf("UPDATE relay_script_cmds SET name='%s', state='%s' WHERE command='00';", $name, $desc1);
		$result  = $dbh->exec($query);
		$query = sprintf("UPDATE relay_script_cmds SET name='%s', state='%s' WHERE command='01';", $name, $desc2);
		$result  = $dbh->exec($query);					
	}
	 
	if($relay_num == "2")
	{
		$query = sprintf("UPDATE relay_script_cmds SET name='%s', state='%s' WHERE command='02';", $name, $desc1);
		$result  = $dbh->exec($query);
		$query = sprintf("UPDATE relay_script_cmds SET name='%s', state='%s' WHERE command='03';", $name, $desc2);
		$result  = $dbh->exec($query);					
	}
	
	if($relay_num == "3")
	{
		$query = sprintf("UPDATE relay_script_cmds SET name='%s', state='%s' WHERE command='04';", $name, $desc1);
		$result  = $dbh->exec($query);
		$query = sprintf("UPDATE relay_script_cmds SET name='%s', state='%s' WHERE command='05';", $name, $desc2);
		$result  = $dbh->exec($query);					
	}
	
	if($relay_num == "4")
	{
		$query = sprintf("UPDATE relay_script_cmds SET name='%s', state='%s' WHERE command='06';", $name, $desc1);
		$result  = $dbh->exec($query);
		$query = sprintf("UPDATE relay_script_cmds SET name='%s', state='%s' WHERE command='07';", $name, $desc2);
		$result  = $dbh->exec($query);					
	}
	
	restart_some_services();
	header("Location: relays.php?action=edit&success=yes&id=".$relay_num);
	
	noSave:    
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
			SetContext('relaysetup');
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
	SetContext('relaysetup');
</script>
<!-- Main Wrapper -->


<div id="wrapper">
	
	<?php
		if($screen_animations == "CHECKED")
		{
			echo '<div class="content animate-panel" data-effect="fadeInUpBig">';
		}
		else
		{
			echo '<div class="content">';
		}
	?>
  	<!-- INFO BLOCK START -->
  	<form name='IOS' action='setup_relays.php' method='post' class='form-horizontal'>  	
    	<fieldset>
  			<div class="row">
    			<div class="col-md-12">
    		  	<div class="hpanel3">
    		  	  <div class="panel-body" style="text-align:left; background:#F1F3F6;border:none;">
    				  	<legend><img src="images/relay1-32x32.gif"> <?php echo $header; ?></legend> 

    				  	<div class="form-group"><label class="col-sm-1 control-label" style="text-align:left; min-width:120px; max-width:120px">Relay Name:</label>
              		<div class="col-sm-2" style="min-width:300px; max-width:300px">
              			<input type="text" class="form-control" name='name' value='<?php echo $name; ?>' required />
              		</div>
              	</div>
    				  	
    				  	<div class="form-group"><label class="col-sm-1 control-label" style="text-align:left;min-width:120px; max-width:120px">Relay Notes:</label>
              		<div class="col-sm-3" style="min-width:300px; max-width:300px">
              			<textarea  rows="3" cols="50" class="form-control" name='notes' required><?php echo $notes; ?></textarea>
              		</div>
              	</div>
    				  	
    				  	<div class="form-group"><label class="col-sm-1 control-label" style="text-align:left;min-width:120px; max-width:120px">Normally Closed Name:</label>
              		<div class="col-sm-2" style="min-width:275px; max-width:275px">
              			<input type="text" class="form-control" name='desc2' value='<?php echo $desc2; ?>' onMouseOver="mouse_move(&#039;relay_nc_name&#039;);" onMouseOut="mouse_move();" required />
              		</div>
              		<div class="col-sm-1" style="text-align:left; min-width:120px; max-width:120px">
              			<div class="radio radio-danger"  style="text-align:left">
              				<?php
    				  					if($nc_color == "RED")
    				  					{
    				  						echo '<input type="radio" id="nc_color" name="nc_color" value="RED" checked/>';
    				  					}
    				  					else
    				  					{
    				  						echo '<input type="radio" id="nc_color" name="nc_color" value="RED" />';
    				  					}
    				  				?>
                      <label for="nc_color">Red Text</label>
                    </div>
                  </div>
                  <div class="col-sm-2" style="text-align:left; min-width:130px; max-width:130px">
                    <div class="radio radio-success"  style="text-align:left">
                    	<?php
    				  					if($nc_color == "GREEN")
    				  					{
    				  						echo '<input type="radio" id="nc_color" name="nc_color" value="GREEN" checked/>';
    				  					}
    				  					else
    				  					{
    				  						echo '<input type="radio" id="nc_color" name="nc_color" value="GREEN" />';
    				  					}
    				  				?>
                    	
                      <label for="nc_color">Green Text</label>
                    </div>
                  </div>
              	</div>
    				  	
    				  	<div class="form-group"><label class="col-sm-1 control-label" style="text-align:left; min-width:120px; max-width:120px">Normally Open Name:</label>
              		<div class="col-sm-2" style="min-width:275px; max-width:275px">
              			<input type="text" class="form-control" name='desc1' value='<?php echo $desc1; ?>'  onMouseOver="mouse_move(&#039;relay_no_name&#039;);" onMouseOut="mouse_move();" required />
              		</div>
              		<div class="col-sm-1" style="text-align:left; min-width:120px; max-width:120px">
              			<div class="radio radio-danger"  style="text-align:left">
              				<?php
    				  					if($no_color == "RED")
    				  					{
    				  						echo '<input type="radio" id="no_color" name="no_color" value="RED" checked/>';
    				  					}
    				  					else
    				  					{
    				  						echo '<input type="radio" id="no_color" name="no_color" value="RED" />';
    				  					}
    				  				?>
                      <label for="lo_ball">Red Text</label>
                    </div>
                  </div>
                  <div class="col-sm-2" style="text-align:left; min-width:130px; max-width:130px">
                    <div class="radio radio-success"  style="text-align:left">
                    	<?php
    				  					if($no_color == "GREEN")
    				  					{
    				  						echo '<input type="radio" id="no_color" name="no_color" value="GREEN" checked/>';
    				  					}
    				  					else
    				  					{
    				  						echo '<input type="radio" id="no_color" name="no_color" value="GREEN" />';
    				  					}
    				  				?>
                      <label for="no_color">Green Text</label>
                    </div>
                  </div>
              	</div>
              	
    		  	  	<div class="form-group">
        					<div class="col-sm-2" style="min-width:250px; max-width:250px">
        						<input type="hidden" name="relay_num" value="<?php echo $relay_num; ?>">
        						<button name="save_btn" class="btn btn-success" type="submit" onMouseOver="mouse_move(&#039;b_apply&#039;);" onMouseOut="mouse_move();"><i class="fa fa-check" ></i> Save</button>
        						<button name="cancel_btn" class="btn btn-primary" type="submit" onMouseOver="mouse_move(&#039;b_cancel&#039;);" onMouseOut="mouse_move();" formnovalidate><i class="fa fa-times"></i> Cancel</button>
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

echo "</body>";
echo "</html>";

?>









 
















