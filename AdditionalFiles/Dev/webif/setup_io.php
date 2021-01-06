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
	$id = "0";
	$type = "io";
	$notes = "";
	$en = "";
	$HI_alert_cmds = "";
	$LO_alert_cmds = ""; 
	$HI_script_cmds = "";
	$LO_script_cmds = "";
	$hi_flap = "";
	$lo_flap = "";
	$dos = "";
	$RunHiIoFile = "";
	$RunLowIoFile = "";
	$iodir = "";
	$iostate = "";
	$pullup = "";
	$glitch = "";
	$text = "";
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


	
if(isset($_GET['io']))
	{
			$id = $_GET['io'];
			$query = sprintf("SELECT * FROM io WHERE id='%d' and type='gxio'",$id);
			$result  = $dbh->query($query);
			foreach($result as $row)
			{
				$name = $row['name'];
				$notes = $row['notes'];
				$en = $row['en'];
				$HI_alert_cmds = $row['HI_alert_cmds'];
				$LO_alert_cmds = $row['LO_alert_cmds']; 
				$HI_script_cmds = $row['HI_script_cmds'];
				$LO_script_cmds = $row['LO_script_cmds'];
				$hi_flap = $row['hi_flap'];
				$lo_flap = $row['lo_flap'];
				$dos = $row['dos'];
				$RunHiIoFile = $row['RunHiIoFile'];
				$RunLowIoFile = $row['RunLowIoFile'];
				$iodir = $row['iodir'];
				$iostate = $row['iostate'];
				$pullup = $row['pullup'];
				$glitch = $row['glitch'];
				$header = "Edit GPIO #" . $id;
			}
			$query = sprintf("SELECT * FROM input_trig_supress WHERE id='%d'",$id);
			$result  = $dbh->query($query);
			foreach($result as $row)
			{
				$suppressed = $row['supress'];
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
	header("Location: ios.php");
}

// change Button	was clicked
if(isset ($_POST['change']))
{
	$id = $_POST['id'];
	$name = $_POST['name'];
	$notes = $_POST['notes'];
	
	if(isset ($_POST['supress']))
	{
		$suppressed = "CHECKED";
	}
	else
	{
		$suppressed = "UNCHECKED";
	}
	
	$set_dir = $_POST['group1'];
	if($set_dir == "output")
	{
		$command = "setgpiobits ".$id." 1 direction";
		exec ($command);
	}
	else
	{
		$command = "setgpiobits ".$id." 0 direction";
		exec ($command);
	}
	$query = sprintf("UPDATE io SET name='%s', notes='%s' WHERE id='%d' AND type='gxio';",$name, $notes, $id);
	$result  = $dbh->exec($query); 
	$query = sprintf("UPDATE gxio_trig_supress SET supress='%s' WHERE id='%d';", $suppressed, $id);
	$result  = $dbh->exec($query);
}

 

// btn_state Button	was clicked
if(isset ($_POST['btn_state']))
{
	$id = $_POST['id'];
	$name = $_POST['name'];
	$notes = $_POST['notes'];
	
	if($id == "1")
	{
		$io_state = 			exec("readiobits a | grep 'Port A bit 00' | cut  -f3 | cut -d' ' -f3");
	}
	else if($id == "2")
	{
		$io_state = 			exec("readiobits a | grep 'Port A bit 01' | cut  -f3 | cut -d' ' -f3");
	}
	else if($id == "3")
	{
		$io_state = 			exec("readiobits a | grep 'Port A bit 02' | cut  -f3 | cut -d' ' -f3");
	}
	else if($id == "4")
	{
		$io_state = 			exec("readiobits a | grep 'Port A bit 03' | cut  -f3 | cut -d' ' -f3");
	}

	if($io_state == "0")
	{
		$iostate = "high";
		$command = "setgpiobits ".$id." 1 state";
		exec ($command);
	}
	else
	{
		$iostate = "low";
		$command = "setgpiobits ".$id." 0 state";
		exec ($command);
	}
	$query = sprintf("UPDATE io SET iostate='%s' WHERE id='%d' AND type='gxio';",$iostate, $id);
	$result  = $dbh->exec($query);
}



// Save Button	was clicked
if(isset ($_POST['save_btn']))
{
	$id = $_POST['id'];
	$name = $_POST['name'];
	$notes = $_POST['notes'];
	if(isset ($_POST['en']))
	{
		$en = "1";
	}
	else
	{
		$en = "0";
	}
	
	if(isset ($_POST['supress']))
	{
		$suppressed = "CHECKED";
	}
	else
	{
		$suppressed = "UNCHECKED";
	}
	
	if(isset ($_POST['pullup']))
	{
		$pullup = "on";
		$command = "setgpiobits ".$id." 1 pullup";
		exec($command); 
	}
	else
	{
		$pullup = "off";
		$command = "setgpiobits ".$id." 0 pullup";
		exec($command);
	}
	
	if(isset ($_POST['glitch']))
	{
		$deglitch = "on";
		$command = "setgpiobits ".$id." 1 glitch";
		exec($command);
	}
	else
	{
		$deglitch = "off";
		$command = "setgpiobits ".$id." 0 glitch";
		exec($command);
	}
	
	$set_dir = $_POST['group1'];
	if($set_dir == "output")
	{
		if($id == "1") {$iostate = exec("readiobits a | grep 'Port A bit 00' | cut  -f3 | cut -d' ' -f3");}
		if($id == "2") {$iostate = exec("readiobits a | grep 'Port A bit 01' | cut  -f3 | cut -d' ' -f3");}
		if($id == "3") {$iostate = exec("readiobits a | grep 'Port A bit 02' | cut  -f3 | cut -d' ' -f3");}
		if($id == "4") {$iostate = exec("readiobits a | grep 'Port A bit 03' | cut  -f3 | cut -d' ' -f3");}
		if($iostate = "0")
		{
			$iostate = "low";
		}
		else
		{
			$iostate = "high";
		}
		$command = "setgpiobits ".$id." 1 direction";
		exec ($command);
	}
	else
	{
		
		$iostate = "low";
		$command = "setgpiobits ".$id." 0 direction";
		exec ($command);
	}
	
	if(isset($_POST['HI_alert_delcmd']))
	{
		foreach ($_POST['HI_alert_delcmd'] as $HI_alert_delcmdBox)
		{
    	$HI_alert_cmds = $HI_alert_cmds . $HI_alert_delcmdBox . ".";
  	} 
	}
	//echo "HI_alert_cmds " . $HI_alert_cmds . "\n";
	
	if(isset($_POST['LO_alert_delcmd']))
	{
		foreach ($_POST['LO_alert_delcmd'] as $LO_alert_delcmdBox)
		{
    	$LO_alert_cmds = $LO_alert_cmds . $LO_alert_delcmdBox . ".";
  	} 
	}
	//echo "LO_alert_cmds " . $LO_alert_cmds . "\n";
	
	if(isset($_POST['HI_script_delcmd']))
	{
		foreach ($_POST['HI_script_delcmd'] as $HI_script_delcmdBox)
		{
    	$HI_script_cmds = $HI_script_cmds . $HI_script_delcmdBox . ".";
  	} 
	}
	//echo "HI_script_cmds " . $HI_script_cmds . "\n";
	
	if(isset($_POST['LO_script_delcmd']))
	{
		foreach ($_POST['LO_script_delcmd'] as $LO_script_delcmdBox)
		{
    	$LO_script_cmds = $LO_script_cmds . $LO_script_delcmdBox . ".";
  	} 
	}
	//echo "LO_script_cmds " . $LO_script_cmds . "\n";
	
	$hi_flap = $_POST['hi_flap'];
	$lo_flap = $_POST['lo_flap'];
	//$dos = $_POST['dos'];
	$RunHiIoFile = $_POST['RunHiIoFile'];
	$RunLowIoFile = $_POST['RunLowIoFile'];

	
	
	$query = sprintf("UPDATE io SET name='%s', notes='%s', en='%s', HI_alert_cmds='%s', HI_script_cmds='%s', LO_alert_cmds='%s', LO_script_cmds='%s', hi_flap='%s', lo_flap='%s', RunHiIoFile='%s', RunLowIoFile='%s', iodir='%s', iostate='%s', pullup='%s', glitch='%s' WHERE id='%d' AND type='gxio';",$name, $notes, $en, $HI_alert_cmds, $HI_script_cmds, $LO_alert_cmds, $LO_script_cmds, $hi_flap, $lo_flap, $RunHiIoFile, $RunLowIoFile, $set_dir, $iostate, $pullup, $deglitch, $id);
	$result  = $dbh->exec($query); 
	
	if($id == "1")
	{
		$query = sprintf("UPDATE io_script_cmds SET name='%s' WHERE command='00';", $name);
		$result  = $dbh->exec($query);
		$query = sprintf("UPDATE io_script_cmds SET name='%s' WHERE command='01';", $name);
		$result  = $dbh->exec($query);
	}
	if($id == "2")
	{
		$query = sprintf("UPDATE io_script_cmds SET name='%s' WHERE command='02';", $name);
		$result  = $dbh->exec($query);
		$query = sprintf("UPDATE io_script_cmds SET name='%s' WHERE command='03';", $name);
		$result  = $dbh->exec($query);
	}
	if($id == "3")
	{
		$query = sprintf("UPDATE io_script_cmds SET name='%s' WHERE command='04';", $name);
		$result  = $dbh->exec($query);
		$query = sprintf("UPDATE io_script_cmds SET name='%s' WHERE command='05';", $name);
		$result  = $dbh->exec($query);
	}
	if($id == "4")
	{
		$query = sprintf("UPDATE io_script_cmds SET name='%s' WHERE command='06';", $name);
		$result  = $dbh->exec($query);
		$query = sprintf("UPDATE io_script_cmds SET name='%s' WHERE command='07';", $name);
		$result  = $dbh->exec($query);
	}
	
	$query = sprintf("UPDATE gxio_trig_supress SET supress='%s' WHERE id='%d';", $suppressed, $id);
	$result  = $dbh->exec($query);
	
	restart_some_services();
	
	//header("Location: ios.php?action=edit&success=yes&id=".$id."&type=GPIO");
	
	noSave:    
}



if($id == "1")
{
	$data_direction = exec("readiobits a | grep 'Port A bit 00' | cut  -f2 | cut -d' ' -f3");
	$io_state = 			exec("readiobits a | grep 'Port A bit 00' | cut  -f3 | cut -d' ' -f3");
	$pullup 	= 			exec("readiobits a | grep 'Port A bit 00' | cut  -f4 | cut -d' ' -f3");
	$deglitch	= 			exec("readiobits a | grep 'Port A bit 00' | cut  -f5 | cut -d' ' -f4");
	$multi		= 			exec("readiobits a | grep 'Port A bit 00' | cut  -f6 | cut -d' ' -f3");
}
else if($id == "2")
{
	$data_direction = exec("readiobits a | grep 'Port A bit 01' | cut  -f2 | cut -d' ' -f3");
	$io_state = 			exec("readiobits a | grep 'Port A bit 01' | cut  -f3 | cut -d' ' -f3");
	$pullup 	= 			exec("readiobits a | grep 'Port A bit 01' | cut  -f4 | cut -d' ' -f3");
	$deglitch	= 			exec("readiobits a | grep 'Port A bit 01' | cut  -f5 | cut -d' ' -f4");
	$multi		= 			exec("readiobits a | grep 'Port A bit 01' | cut  -f6 | cut -d' ' -f3");
}
else if($id == "3")
{
	$data_direction = exec("readiobits a | grep 'Port A bit 02' | cut  -f2 | cut -d' ' -f3");
	$io_state = 			exec("readiobits a | grep 'Port A bit 02' | cut  -f3 | cut -d' ' -f3");
	$pullup 	= 			exec("readiobits a | grep 'Port A bit 02' | cut  -f4 | cut -d' ' -f3");
	$deglitch	= 			exec("readiobits a | grep 'Port A bit 02' | cut  -f5 | cut -d' ' -f4");
	$multi		= 			exec("readiobits a | grep 'Port A bit 02' | cut  -f6 | cut -d' ' -f3");
}
else if($id == "4")
{
	$data_direction = exec("readiobits a | grep 'Port A bit 03' | cut  -f2 | cut -d' ' -f3");
	$io_state = 			exec("readiobits a | grep 'Port A bit 03' | cut  -f3 | cut -d' ' -f3");
	$pullup 	= 			exec("readiobits a | grep 'Port A bit 03' | cut  -f4 | cut -d' ' -f3");
	$deglitch	= 			exec("readiobits a | grep 'Port A bit 03' | cut  -f5 | cut -d' ' -f4");
	$multi		= 			exec("readiobits a | grep 'Port A bit 03' | cut  -f6 | cut -d' ' -f3");
}

$header = "Edit GPIO #" . $id;
				
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
			SetContext('ios');
		</script>
		
		<script language="javascript" type="text/javascript">
		function display_io ()
		{
		        var myRandom = parseInt(Math.random()*999999999);
		        $.getJSON('sdserver.php?element=ios&rand=' + myRandom,
		            function(data)
		            {
										setTimeout (display_io, 1000);
		                
		                  if (data.ios.io1 == 0)
		                  {
		                  	$('#gpio1').replaceWith("<span id='gpio1' style='color:green'>LOW</span>");
		                  }
		                  else
		                  {
		                    $('#gpio1').replaceWith("<span id='gpio1' style='color:red'>HIGH</span>"); 
		                  }
											
											if (data.ios.io2 == 0)
		                  {
		                  	$('#gpio2').replaceWith("<span id='gpio2' style='color:green'>LOW</span>");
		                  }
		                  else
		                  {
		                    $('#gpio2').replaceWith("<span id='gpio2' style='color:red'>HIGH</span>"); 
		                  }
		                  
		                  if (data.ios.io3 == 0)
		                  {
		                  	$('#gpio3').replaceWith("<span id='gpio3' style='color:green'>LOW</span>");
		                  }
		                  else
		                  {
		                    $('#gpio3').replaceWith("<span id='gpio3' style='color:red'>HIGH</span>"); 
		                  }
		                  
		                  if (data.ios.io4 == 0)
		                  {
		                  	$('#gpio4').replaceWith("<span id='gpio4' style='color:green'>LOW</span>");
		                  }
		                  else
		                  {
		                    $('#gpio4').replaceWith("<span id='gpio4' style='color:red'>HIGH</span>"); 
		                  }
		            }
		        );
		}

	
		display_io ();
		</script>
	
		
		
		<script>
   	
   	$().ready(function() {  
   	 // alerts	
     $('#add_HI_alert').click(function() {  
        return !$('#HI_alert_addcmd option:selected').clone().appendTo('#HI_alert_delcmd');  
     });  
     $('#remove_HI_alert').click(function() {  
        return !$('#HI_alert_delcmd option:selected').remove();   
     });
     
     $('#add_LO_alert').click(function() {  
        return !$('#LO_alert_addcmd option:selected').clone().appendTo('#LO_alert_delcmd');  
     });  
     $('#remove_LO_alert').click(function() {  
        return !$('#LO_alert_delcmd option:selected').remove();   
     });
     //scripts
     $('#add_HI_script').click(function() {  
        return !$('#HI_script_addcmd option:selected').clone().appendTo('#HI_script_delcmd');  
     });  
     $('#remove_HI_script').click(function() {  
        return !$('#HI_script_delcmd option:selected').remove();   
     });
     
     $('#add_LO_script').click(function() {  
        return !$('#LO_script_addcmd option:selected').clone().appendTo('#LO_script_delcmd');  
     });  
     $('#remove_LO_script').click(function() {  
        return !$('#LO_script_delcmd option:selected').remove();   
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

<?php left_nav("ios"); ?>
<script language="javascript" type="text/javascript">
	SetContext('ios');
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
  	<form name='IOS' action='setup_io.php' method='post' class="form-horizontal" onsubmit="selectAllOptions('HI_alert_delcmd');selectAllOptions('LO_alert_delcmd');selectAllOptions('HI_script_delcmd');selectAllOptions('LO_script_delcmd');">  	
    	<fieldset>
  			<div class="row">
    			<div class="col-sm-12">
    		  	<div class="hpanel3">
    		  	  <div class="panel-body" style="text-align:left; background:#F1F3F6;border:none;">
    				  	<legend><img src="images/bigios.gif"> <?php echo $header; ?></legend> 

    				  	<div class="form-group"><label class="col-sm-1 control-label" style="text-align:left">GPIO Name:</label>
              		<div class="col-sm-4" style="text-align:left; min-width:200px; max-width:200px">
              			<input type="text" class="form-control" name='name' value='<?php echo $name; ?>' required />
              		</div>
              	</div>
    				  	
    				  	<div class="form-group"><label class="col-sm-1 control-label" style="text-align:left">GPIO Notes:</label>
              		<div class="col-sm-3" style="text-align:left; min-width:300px; max-width:300px">
              			<textarea  rows="3" cols="60" class="form-control" name='notes' required><?php echo $notes; ?></textarea>
              		</div>
              	</div>
              	
              	<?php
              		if($data_direction == "I")
              		{
              			echo '<div class="form-group"><label class="col-sm-1 control-label" style="text-align:left"></label>';
              			echo '	<div class="col-sm-12" style="text-align:left; min-width:350px; max-width:350px">';
              			echo '		<div class="checkbox checkbox-danger" onMouseOver="mouse_move(&#039;sd_suppress&#039;);" onMouseOut="mouse_move();">';
              			echo '			<input type="checkbox" id="supress" name="supress"'.$suppressed.' />';
    		            echo '			<label for="suppress" onMouseOver="mouse_move(&#039;sd_suppress&#039;);" onMouseOut="mouse_move();">Suppress Trigger Actions on Boot?</label>';
              			echo '		</div>';
              			echo '	</div>';
              			echo '</div>';
              			echo '<div class="form-group">';
              			echo '	<div class="col-sm-12" style="text-align:left; min-width:350px; max-width:450px">';
              			echo '		<div class="radio radio-success"  style="text-align:left">';
              			echo '			<input type="radio" id="group1" name="group1" value="output" />';	
                    echo '  		<label for="group1">This I/O pin is an Output</label>';
                    echo '				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
              			echo '			<input type="radio" id="group1" name="group1" value="input" checked/>';	
                    echo '  		<label for="group1">This I/O pin is an Input</label>';
                    echo '		</div>';
                  	echo '	</div>';
                  	echo '</div>';
                  	echo '<div class="form-group">';
                  	echo '	<div class="col-sm-12" style="text-align:left">';
                  	echo '		<div class="checkbox checkbox-success">';
                    if($pullup =="Enabled")
                    {
                    	echo '		<input type="checkbox" id="pullup" name="pullup" checked />';
                    }
                    else
                    {
                    	echo '		<input type="checkbox" id="pullup" name="pullup" />';
                    } 
                    echo '			<label for="pullup">Enable pull-up resistor?</label>';
                    
                    echo '<br><br>';
                    
                    if($deglitch =="Enabled")
                    {
                    	echo '		<input type="checkbox" id="glitch" name="glitch" checked />';
                    }
                    else
                    {
                    	echo '		<input type="checkbox" id="glitch" name="glitch" />';
                    } 
                    echo '			<label for="glitch">Enable Glitch Filter?</label>';
                    echo '		</div>';
                    echo '	</div>';
                    echo '</div>';
                    
                    if($io_state == "0")
                    {
                    	$state = '<span id="gpio'.$id.'" style="color:green">LOW</span>';
                    }
                    else
                    {
                    	$state = '<span id="gpio'.$id.'" style="color:red">HIGH</span>';
                    }
                    
                  	echo '<p>';
        						echo '	<b>Input state: '. $state . '</b>';
        						echo '</p>';
        						echo '<div class="form-group">';
        						echo '	<div class="col-sm-12" style="text-align:left">';
        						echo '		<button name="save_btn" class="btn btn-success" type="submit" onMouseOver="mouse_move(&#039;b_apply&#039;);" onMouseOut="mouse_move();"><i class="fa fa-check" ></i> Save</button>';
        						echo '		<button name="cancel_btn" class="btn btn-primary" type="submit" onMouseOver="mouse_move(&#039;b_cancel&#039;);" onMouseOut="mouse_move();" formnovalidate><i class="fa fa-times"></i> Cancel</button>';
        						echo '	</div>';
        						echo '</div>';
        						//echo '<br>';
                  	echo '<div class="table-responsive">';
    				   			echo '	<table width="100%" border="1" class="table table-striped table-condensed">';
    				   			echo '		<thead>';
    				   			echo '			<tr>';
    				   			echo '			<th width="10%" style="background:#D6DFF7;">';
    				   			echo '				<div style="text-align:center;color:black">Enabled</div>';
    				   			echo '			</th>';
    				   			echo '			<th width="40%" style="background:red">';
    				   			echo '				<div style="text-align:center;color:black">GPIO '.$id.' High Trigger</div>';
    				   			echo '			</th>';
    				   			echo '			<th width="40%" style="background:#40FF40">';
    				   			echo '				<div style="text-align:center;color:black">GPIO '.$id.' Low Trigger</div>';
    				   			echo '			</th>';
    				   			echo '		</tr>';
    				   			echo '	</thead>';
    				   			echo '	<tbody>';
    				   			echo '		<tr>';
    				   			echo '			<td style="vertical-align:middle">';			
    				   			if($en == "1")
    				   			{
    				   				$active = "checked";
    				   			}
    				   			else
    				   			{
    				   				$active = " ";
    				   			}
    				   			echo '	<div class="checkbox checkbox-success"  style="text-align:center">';
                    echo '		<input type="checkbox" id="en" name="en" value="1" onMouseOver="mouse_move(&#039;sd_enabled&#039;);" onMouseOut="mouse_move();" '.$active.' />';
                    echo '	  	<label for="en"></label>';
                    echo '	 </div>';
    				   						
    				   			echo '		</td>';
    				   			echo '		<td>';
    				   			echo '			<div style="text-align:center;">';
    				   			echo '				<strong style="font-size: 15px;color:blue;">These events will fire when GPIO '.$id.' is a logic high.</strong>';
    				   			echo '			</div>';
    				   			echo '			<div style="text-align:center; margin-top: 15px;">';
    				   			echo '				<strong style="font-size: 15px;">Execute the actions below every:</strong>';
    				   			echo '			</div>';
    				   			echo '			<div style="text-align:center;">';
    				   			echo '				<select class="form-control input-sm" style="max-width:105px; min-width:105px; display: block; margin: 0 auto;" name="hi_flap" >';
										echo '					<option value="0">One Shot</option>';
															
															$ii=1;	if($hi_flap==$ii) {$chan=sprintf("selected");} else {$chan=sprintf(" ");} echo"<option ".$chan." value=".$ii.">".$ii." Second</option>";	
															for($ii=2; $ii<60; $ii++)	{	if($hi_flap==$ii) {$chan = "selected";} else {$chan = " ";} echo"<option " . $chan . " value='" . $ii . "'>" . $ii . " Seconds</option>";	}
															$ii=1; if($hi_flap==($ii*60)) {$chan = "selected";} else {$chan = " ";} echo "<option " . $chan . " value='" . $ii*60 . "'>" . $ii . " Minute</option>";
															for($ii=2; $ii<60; $ii++)	{	if($hi_flap==($ii*60)) {$chan = "selected";} else {$chan = " ";} echo "<option " . $chan . " value='" . $ii*60 . "'>" . $ii . " Minutes</option>";	}
															$ii=1;	if($hi_flap==($ii*60*60)) {$chan = "selected";} else {$chan = " ";} echo "<option " . $chan . " value='" . $ii*60*60 . "'>" . $ii . " Hour</option>";
															for($ii=2; $ii<25; $ii++)	{ if($hi_flap==($ii*60*60)) {$chan = "selected";} else {$chan = " ";} echo "<option " . $chan . " value='" . $ii*60*60 . "'>" . $ii . " Hours</option>";	}
															
		    				   	echo '				</select>';
		    				   	echo '			</div>';	
													
										echo '				</td>';
										echo '				<td style="vertical-align:middle">';
										echo '					<div style="text-align:center;"><strong style="font-size: 15px;color:blue;">These events will fire when GPIO '.$id.' is a logic low.</strong></div>';	
										
										echo '					<div style="text-align:center; margin-top: 15px;">';
    				   			echo '						<strong style="font-size: 15px;">Execute the actions below every:</strong>';
    				   			echo '					</div>';
    				   			echo '					<div style="text-align:center;">';
    				   			echo '						<select class="form-control input-sm" style="max-width:105px; min-width:105px; display: block; margin: 0 auto;" name="lo_flap" >';
										echo '						<option value="0">One Shot</option>';
															
															$ii=1;	if($lo_flap==$ii) {$chan=sprintf("selected");} else {$chan=sprintf(" ");} echo"<option ".$chan." value=".$ii.">".$ii." Second</option>";	
															for($ii=2; $ii<60; $ii++)	{	if($lo_flap==$ii) {$chan = "selected";} else {$chan = " ";} echo"<option " . $chan . " value='" . $ii . "'>" . $ii . " Seconds</option>";	}
															$ii=1; if($lo_flap==($ii*60)) {$chan = "selected";} else {$chan = " ";} echo "<option " . $chan . " value='" . $ii*60 . "'>" . $ii . " Minute</option>";
															for($ii=2; $ii<60; $ii++)	{	if($lo_flap==($ii*60)) {$chan = "selected";} else {$chan = " ";} echo "<option " . $chan . " value='" . $ii*60 . "'>" . $ii . " Minutes</option>";	}
															$ii=1;	if($lo_flap==($ii*60*60)) {$chan = "selected";} else {$chan = " ";} echo "<option " . $chan . " value='" . $ii*60*60 . "'>" . $ii . " Hour</option>";
															for($ii=2; $ii<25; $ii++)	{ if($lo_flap==($ii*60*60)) {$chan = "selected";} else {$chan = " ";} echo "<option " . $chan . " value='" . $ii*60*60 . "'>" . $ii . " Hours</option>";	}
															
		    				   echo '					</select>';
		    				   echo '				</div>';
										
										
										
										echo '				</td>';
										echo '			</tr>';
											
										echo '			<tr>';
    				   			echo '				<td style="vertical-align:middle">';
    				   			echo '					<div style="text-align:center;"><a name="#hi1"></a><br><a href="setup_notifications.php"><strong><u class="dotted">Alerts</u></strong></a></div>';
    				   			echo '				</td>';
    				   			echo '				<td>';		
    				   			selectbox("HI", "alert", $HI_alert_cmds);
    				   			echo '				</td>';		
    				   			echo '				<td>';						
    				   			selectbox("LO", "alert", $LO_alert_cmds);
    				   			echo '				</td>';						
    				   			echo '			</tr>';
    				   				
    				   			echo '			<tr>';				
    				   			echo '				<td style="vertical-align:middle">';
    				   			echo '					<div style="text-align:center;"><a name="#lo1"></a><br><a href="setup_scripts.php?"><strong><u class="dotted">Scripts</u></strong></a></div>';
    				   			echo '				</td>';
    				   			echo '				<td>';		
    				   			selectbox("HI", "script", $HI_script_cmds);
    				   			echo '				</td>';			
    				   			echo '				<td>';						
    				   			selectbox("LO", "script", $LO_script_cmds);
    				   			echo '				</td>';									
    				   			echo '			</tr>';
    				   				
    				   			echo '			<tr>';
										echo '				<td style="vertical-align:middle">';
										echo '				<div style="text-align:center;"><a href="#hi3"></a><a href="setup_file_explorer.php"><strong><u class="dotted">File</u></strong></a></center></div>';
										echo '				</td>';
											
										echo '				<td>';
										echo '					<div><label class="col-sm-2 control-label">Execute File:</label>';
              			echo '						<div class="col-sm-8">';
              			echo '							<input type="text" class="form-control" name="RunHiIoFile" value="'.$RunHiIoFile.'" />';
              			echo '						</div>';
              			echo '					</div>';
										echo '				</td>';
											
										echo '				<td>';
										echo '					<div><label class="col-sm-2 control-label">Execute File:</label>';
              			echo '						<div class="col-sm-8">';
              			echo '							<input type="text" class="form-control" name="RunLowIoFile" value="'.$RunLowIoFile.'" />';
              			echo '						</div>';
              			echo '					</div>';
										echo '				</td>';
										echo '			</tr>';
    				   			echo '		</tbody>';
  									echo '	</table>';    	    
    		  	  			echo '</div> <!-- END TABLE RESPONSIVE -->';
              			
              			echo '<div class="form-group">';
        						echo '	<div class="col-sm-12">';
        						echo '		<input type="hidden" name="id" value="'.$id.'">';
        						echo '		<button name="save_btn" class="btn btn-success" type="submit" onMouseOver="mouse_move(&#039;b_apply&#039;);" onMouseOut="mouse_move();"><i class="fa fa-check" ></i> Save</button>';
        						echo '		<button name="cancel_btn" class="btn btn-primary" type="submit" onMouseOver="mouse_move(&#039;b_cancel&#039;);" onMouseOut="mouse_move();" formnovalidate><i class="fa fa-times"></i> Cancel</button>';
        						echo '	</div>';
        						echo '</div>';
              		}
              		
              		if($data_direction == "O")
              		{
              			echo '<div class="form-group"><label class="col-sm-1 control-label" style="text-align:left"></label>';
              			echo '	<div class="col-sm-6" style="text-align:left">';
              			echo '		<div class="radio radio-success"  style="text-align:left">';
              			echo '			<input type="radio" id="group1" name="group1" value="output" checked/>';	
                    echo '  		<label for="group1">This I/O pin is an Output</label>';
                    echo '			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
              			echo '			<input type="radio" id="group1" name="group1" value="input" />';	
                    echo '  		<label for="group1">This I/O pin is an Input</label>';
                    echo '		</div>';
                  	echo '	</div>';
              			echo '</div>';
              			echo '<div class="form-group">';
        						echo '	<div class="col-sm-12">';
        						echo '		<input type="hidden" name="id" value="'.$id.'">';
        						echo '		<button name="change" class="btn btn-success" type="submit" onMouseOver="mouse_move(&#039;b_apply&#039;);" onMouseOut="mouse_move();"><i class="fa fa-check" ></i> Apply</button>';
        						echo '		<button name="cancel_btn" class="btn btn-primary" type="submit" onMouseOver="mouse_move(&#039;b_cancel&#039;);" onMouseOut="mouse_move();" formnovalidate><i class="fa fa-times"></i> Cancel</button>';
        						echo '	</div>';
        						echo '</div>';
        						echo '<br><br>';
        						
        						if($io_state == "0")
                    {
                    	$state = '<span id="gpio'.$id.'" style="color:green">LOW</span>';
                    }
                    else
                    {
                    	$state = '<span id="gpio'.$id.'" style="color:red">HIGH</span>';
                    }
        						
        						echo '<p>';
        						echo '	<b>Output state: '. $state . '</b>';
        						echo '<div class="form-group">';
        						echo '	<div class="col-sm-2">';
        						echo '		<input type="hidden" name="id" value="'.$id.'">';
        						if($io_state == "0")
        						{
        							echo '		<button name="btn_state" class="btn btn-warning" type="submit" onMouseOver="mouse_move(&#039;sd_switch2hi&#039;);" onMouseOut="mouse_move();"><i class="fa fa-check" ></i> SET HIGH</button>';
        						}
        						else
        						{
        							echo '		<button name="btn_state" class="btn btn-warning" type="submit" onMouseOver="mouse_move(&#039;sd_switch2lo&#039;);" onMouseOut="mouse_move();"><i class="fa fa-check" ></i> SET LOW</button>';
        						}
        						echo '	</div>';
        						echo '</div>';
        						echo '</p>';
              		}
              		
              	?>
              	
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
