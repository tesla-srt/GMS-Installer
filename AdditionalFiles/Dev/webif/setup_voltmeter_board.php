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
	$id = "";
	$name = "";
	$notes = "";
	$hi_t = "";
	$lo_t = "";
	$h_en = "";
	$l_en = "";
	$HI_alert_cmds = "";
	$HI_N_alert_cmds = "";
	$HI_script_cmds = "";
	$HI_N_script_cmds = "";
	$LO_alert_cmds = "";
	$LO_N_alert_cmds = "";
	$LO_script_cmds = "";
	$LO_N_script_cmds = "";
	$hi_flap = "";
	$lo_flap = "";
	$vmadj = "";
	$per = "";
	$mode = "";
	$shunta = "";
	$shuntmv = "";
	$RunHiFile = "";
	$RunHiNFile = "";
	$RunLowFile = "";
	$RunLowNFile = "";
	$hi_t_min = "";
	$lo_t_max = "";
	$polling = "";
	$mjumper = "";
	$watt_mode_enabled = "";
	$watt_base_vm = "";
	$vmadd = "";
	$v_mode = "";
	$active = "";
	$text = "";
	$alert_flag = "";
	$dbh = new PDO('sqlite:/etc/rms100.db');
	
		
/////////////////////////////////////////////////////////////////
//                                                             //
//                    GET PROCESSING                           //
//                                                             //
/////////////////////////////////////////////////////////////////


if(isset ($_GET['vmsetup']))
{
	$id = $_GET['vmsetup'];
	$query = sprintf("SELECT * FROM iso_voltmeters WHERE id='%d';",$id);
	$result  = $dbh->query($query);
	foreach($result as $row)
	{
		$name = $row['name'];
		$notes = $row['notes'];
		$hi_t = $row['hi_t'];
		$lo_t = $row['lo_t'];
		$h_en = $row['h_en'];
		$l_en = $row['l_en'];
		$HI_alert_cmds = $row['HI_alert_cmds'];
		$HI_N_alert_cmds = $row['HI_N_alert_cmds'];
		$HI_script_cmds = $row['HI_script_cmds'];
		$HI_N_script_cmds = $row['HI_N_script_cmds'];
		$LO_alert_cmds = $row['LO_alert_cmds'];
		$LO_N_alert_cmds = $row['LO_N_alert_cmds'];
		$LO_script_cmds = $row['LO_script_cmds'];
		$LO_N_script_cmds = $row['LO_N_script_cmds'];
		$hi_flap = $row['hi_flap'];
		$lo_flap = $row['lo_flap'];
		$vmadj = $row['vmadj'];
		$per = $row['per'];
		$mode = $row['mode'];
		$shunta = $row['shunta'];
		$shuntmv = $row['shuntmv'];
		$RunHiFile = $row['RunHiFile'];
		$RunHiNFile = $row['RunHiNFile'];
		$RunLowFile = $row['RunLowFile'];
		$RunLowNFile = $row['RunLowNFile'];
		$hi_t_min = $row['hi_t_min'];
		$lo_t_max = $row['lo_t_max'];	
		$polling = $row['polling'];	
		$watt_mode_enabled = $row['watt_mode_enabled'];	
		$watt_base_vm = $row['watt_base_vm'];	
		
		if($mode == "v")
		{
			$vm_mode = "Voltmeter";
		}
		else if($mode == "a")
		{
			if($watt_mode_enabled == "UNCHECKED")
			{
				$vm_mode = "Ammeter";
				$v_mode = "Amp";
			}
			else
			{
				$vm_mode = "Wattmeter";
				$v_mode = "Watt";
			}	
		}
	}
	
	
	for($ii=1; $ii<7; $ii++) //6 voltmeters
	{
		$query = sprintf("SELECT * FROM iso_voltmeters where id = '%d'", $ii);
		$result  = $dbh->query($query);
		foreach($result as $row)
		{
			if($ii == 1){$vmode1 = $row['mode'];}
			if($ii == 2){$vmode2 = $row['mode'];}
			if($ii == 3){$vmode3 = $row['mode'];}
			if($ii == 4){$vmode4 = $row['mode'];}
			if($ii == 5){$vmode5 = $row['mode'];}
			if($ii == 6){$vmode6 = $row['mode'];}	
		}	
	}
	
	$query = sprintf("SELECT * FROM iso_vm_polarity where id = '%d'", $id);
	$result  = $dbh->query($query);
	foreach($result as $row)
	{
		$polarity = $row['polarity'];
		$vpolarity = $row['vpolarity'];	
		$averaging = $row['averaging'];	
		$weight = $row['weight'];	
	}	
	
	$query = sprintf("SELECT * FROM iso_vm_graph_opts where vm = '%d'", $id);
	$result  = $dbh->query($query);
	foreach($result as $row)
	{
		$slope_enable = $row['slope_enable'];
		$limit_enable = $row['limit_enable'];	
		$lower_limit = $row['lower_limit'];	
		$upper_limit = $row['upper_limit'];
		$lower_limit = sprintf("%4.2f",$lower_limit);
		$upper_limit = sprintf("%4.2f",$upper_limit);
	}	
	
	$query = sprintf("SELECT * FROM iso_vm_trig_supress where id = '%d'", $id);
	$result  = $dbh->query($query);
	foreach($result as $row)
	{
		$supress = $row['supress'];
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
	header("Location: device_vdb.php");
}

if($_SERVER['REQUEST_METHOD'] == "POST")
{
	$id =  $_POST['id'];
	$name =  $_POST['name']; 
	$notes = $_POST['notes'];
	$hi_t = $_POST['hi_t'];
	$lo_t = $_POST['lo_t'];
	
	if(isset($_POST['shunta']))
	{
		$shunta = $_POST['shunta'];
	}
	else
	{
		$query = sprintf("SELECT * FROM voltmeters WHERE id='%d';",$id);
		$result  = $dbh->query($query);
		foreach($result as $row)
		{
			$shunta = $row['shunta'];
		}
	}
	
	if(isset($_POST['shuntmv']))
	{
		$shuntmv = $_POST['shuntmv'];
	}
	else
	{
		$query = sprintf("SELECT * FROM voltmeters WHERE id='%d';",$id);
		$result  = $dbh->query($query);
		foreach($result as $row)
		{
			$shuntmv = $row['shuntmv'];
		}
	}
	
	if(isset($_POST['watt_mode_enabled']))
	{
		$watt_mode_enabled = "CHECKED";
	}
	else
	{
		$watt_mode_enabled = "UNCHECKED";
	}
	
	if(isset($_POST['watt_base_vm']))
	{
		$watt_base_vm = $_POST['watt_base_vm'];
	}
	
	
	$hi_flap = $_POST['hi_flap'];
	$lo_flap = $_POST['lo_flap'];
	$RunHiFile = $_POST['RunHiFile'];
	$RunHiNFile = $_POST['RunHiNFile'];
	$RunLowFile = $_POST['RunLowFile'];
	$RunLowNFile = $_POST['RunLowNFile'];
	$hi_t_min = $_POST['hi_t_min'];
	$lo_t_max = $_POST['lo_t_max'];
	$vmadj = $_POST['vmadj'];
	$per = $_POST['per'];
	$mode = $_POST['mode'];
	$polling = $_POST['polling'];	
	$mjumper = "checked";		
	$polarity = $_POST['polarity'];
	$vpolarity = $_POST['vpolarity'];
	
	$v_mode = $_POST['v_mode'];
	
	if($mode == "v")
		{
			$vm_mode = "Voltmeter";
		}
		else if($mode == "a")
		{
			if($watt_mode_enabled == "UNCHECKED")
			{
				$vm_mode = "Ammeter";
				$v_mode = "Amp";
			}
			else
			{
				$vm_mode = "Wattmeter";
				$v_mode = "Watt";
			}	
		}
	
	if(isset($_POST['polling']))
	{
		$polling = "CHECKED";
	}
	else
	{
		$polling = "UNCHECKED";
	}
	
	if(isset($_POST['averaging']))
	{
		$averaging = "CHECKED";
	}
	else
	{
		$averaging = "UNCHECKED";
	}
	
	$weight = $_POST['weight'];
	if(isset($_POST['slope_enable']))
	{
		$slope_enable = "CHECKED";
	}
	else
	{
		$slope_enable = "UNCHECKED";
	}

	if(isset($_POST['limit_enable']))
	{
		$limit_enable = "CHECKED";
	}
	else
	{
		$limit_enable = "UNCHECKED";
	}
	$lower_limit = $_POST['lower_limit'];	
	$upper_limit = $_POST['upper_limit'];
	
	if(isset ($_POST['h_en']))
	{
		$h_en = "1";
	}
	else
	{
		$h_en = "0";
	}
	
	if(isset ($_POST['l_en']))
	{
		$l_en = "1";
	}
	else
	{
		$l_en = "0";
	}
	
	$vmode1 = $_POST['vmode1'];
	$vmode2 = $_POST['vmode2'];
	$vmode3 = $_POST['vmode3'];
	$vmode4 = $_POST['vmode4'];
	$vmode5 = $_POST['vmode5'];
	$vmode6 = $_POST['vmode6'];
	
	if(isset($_POST['supress']))
	{
		$supress = "CHECKED";
	}
	else
	{
		$supress = "UNCHECKED";
	}
	
	if($mode == "v")
	{
		$vm_mode = "Voltmeter";
	}
	else if($mode == "a")
	{
		if($watt_mode_enabled == "UNCHECKED")
		{
			$vm_mode = "Ammeter";
		}
		else
		{
			$vm_mode = "Wattmeter";
		}	
	}
	
	$HI_alert_cmds = "";
	if(isset($_POST['HI_alert_delcmd']))
	{
		foreach ($_POST['HI_alert_delcmd'] as $HI_alert_delcmdBox)
		{
    	$HI_alert_cmds = $HI_alert_cmds . $HI_alert_delcmdBox . ".";
  	} 
	}
//	echo "HI_alert_cmds " . $HI_alert_cmds . "\n";
	
	$HI_N_alert_cmds = "";
	if(isset($_POST['HI_N_alert_delcmd']))
	{
		foreach ($_POST['HI_N_alert_delcmd'] as $HI_N_alert_delcmdBox)
		{
    	$HI_N_alert_cmds = $HI_N_alert_cmds . $HI_N_alert_delcmdBox . ".";
  	} 
	}
//	echo "HI_N_alert_cmds " . $HI_N_alert_cmds . "\n";
	
	$LO_alert_cmds = "";
	if(isset($_POST['LO_alert_delcmd']))
	{
		foreach ($_POST['LO_alert_delcmd'] as $LO_alert_delcmdBox)
		{
    	$LO_alert_cmds = $LO_alert_cmds . $LO_alert_delcmdBox . ".";
  	} 
	}
//	echo "LO_alert_cmds " . $LO_alert_cmds . "\n";
	
	$LO_N_alert_cmds = "";
	if(isset($_POST['LO_N_alert_delcmd']))
	{
		foreach ($_POST['LO_N_alert_delcmd'] as $LO_N_alert_delcmdBox)
		{
    	$LO_N_alert_cmds = $LO_N_alert_cmds . $LO_N_alert_delcmdBox . ".";
  	} 
	}
//	echo "LO_N_alert_cmds " . $LO_N_alert_cmds . "\n";
	
	$HI_script_cmds = "";
	if(isset($_POST['HI_script_delcmd']))
	{
		foreach ($_POST['HI_script_delcmd'] as $HI_script_delcmdBox)
		{
    	$HI_script_cmds = $HI_script_cmds . $HI_script_delcmdBox . ".";
  	} 
	}
//	echo "HI_script_cmds " . $HI_script_cmds . "\n";
	
	$HI_N_script_cmds = "";
	if(isset($_POST['HI_N_script_delcmd']))
	{
		foreach ($_POST['HI_N_script_delcmd'] as $HI_N_script_delcmdBox)
		{
    	$HI_N_script_cmds = $HI_N_script_cmds . $HI_N_script_delcmdBox . ".";
  	} 
	}
//	echo "HI_N_script_cmds " . $HI_N_script_cmds . "\n";
	
	$LO_script_cmds = "";
	if(isset($_POST['LO_script_delcmd']))
	{
		foreach ($_POST['LO_script_delcmd'] as $LO_script_delcmdBox)
		{
    	$LO_script_cmds = $LO_script_cmds . $LO_script_delcmdBox . ".";
  	} 
	}
//	echo "LO_script_cmds " . $LO_script_cmds . "\n";
	
	$LO_N_script_cmds = "";
	if(isset($_POST['LO_N_script_delcmd']))
	{
		foreach ($_POST['LO_N_script_delcmd'] as $LO_N_script_delcmdBox)
		{
    	$LO_N_script_cmds = $LO_N_script_cmds . $LO_N_script_delcmdBox . ".";
  	} 
	}
//	echo "LO_N_script_cmds " . $LO_N_script_cmds . "\n";
	
}

// Apply or OK Button	was clicked
if(isset($_POST['apply_btn']) || isset($_POST['ok_btn']))
{
	if($vm_mode == "Ammeter")
	{
		if($shuntmv < 1)
		{
			$text = "Shunt Millivolt value must be at least 1mv!";
			$alert_flag = "2";
			goto noSave;
		}
		if($shunta < 1)
		{
			$text = "Shunt Amps value must be at least 1 amp!";
			$alert_flag = "2";
			goto noSave;
		}
	}
	
	if(($h_en == "1") && ($hi_t <= $hi_t_min))
	{
		$text = "The High Trigger Max Value must be Greater than the High Trigger Min Value!";
		$alert_flag = "2";
		goto noSave;
	}
	if(($l_en == "1") && ($lo_t >= $lo_t_max))
	{
		$text = "The Low Trigger Max Value must be Less than the Low Trigger Min Value!";
		$alert_flag = "2";
		goto noSave;
	}
	
	if($vmadj == 0)
	{
		$text = "Multiplier must not be 0!";
		$alert_flag = "2";
		goto noSave;
	}
	
	$query = sprintf("UPDATE iso_voltmeters SET name='%s', notes='%s', hi_t='%2.4f', lo_t='%2.4f', h_en='%d', l_en='%d', HI_alert_cmds='%s', HI_N_alert_cmds='%s', HI_script_cmds='%s', HI_N_script_cmds='%s', LO_alert_cmds='%s', LO_N_alert_cmds='%s', LO_script_cmds='%s', LO_N_script_cmds='%s', hi_flap='%d', lo_flap='%d', vmadj='%2.4f', per='%d', mode='%s', shunta='%d', shuntmv='%d', RunHiFile='%s', RunHiNFile='%s', RunLowFile='%s', RunLowNFile='%s', hi_t_min='%2.4f', lo_t_max='%2.4f', polling='%s', watt_mode_enabled='%s', watt_base_vm='%d'  WHERE id='%d';", $name, $notes, $hi_t, $lo_t, $h_en, $l_en, $HI_alert_cmds, $HI_N_alert_cmds, $HI_script_cmds, $HI_N_script_cmds, $LO_alert_cmds, $LO_N_alert_cmds, $LO_script_cmds, $LO_N_script_cmds, $hi_flap, $lo_flap, $vmadj, $per, $mode, $shunta, $shuntmv, $RunHiFile, $RunHiNFile, $RunLowFile, $RunLowNFile, $hi_t_min, $lo_t_max, $polling, $watt_mode_enabled, $watt_base_vm, $id);
	$result  = $dbh->exec($query); 

	$query = sprintf("UPDATE iso_vm_trig_supress SET supress='%s' WHERE id='%d';", $supress, $id);
	$result  = $dbh->exec($query);

	$query = sprintf("UPDATE iso_vm_polarity SET polarity='%s', vpolarity='%s', averaging='%s', weight='%s'  WHERE id='%d';", $polarity, $vpolarity, $averaging, $weight, $id);
	$result  = $dbh->exec($query);				

	restart_some_services();

	if(isset($_POST['ok_btn']))
	{
		header("Location: device_vdb.php?confirm=yes");
	}

	$alert_flag = "1";
	
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
			SetContext('vdb');
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
     
     $('#add_HI_N_alert').click(function() {  
        return !$('#HI_N_alert_addcmd option:selected').clone().appendTo('#HI_N_alert_delcmd');  
     });  
     $('#remove_HI_N_alert').click(function() {  
        return !$('#HI_N_alert_delcmd option:selected').remove();   
     });
     
     $('#add_LO_alert').click(function() {  
        return !$('#LO_alert_addcmd option:selected').clone().appendTo('#LO_alert_delcmd');  
     });  
     $('#remove_LO_alert').click(function() {  
        return !$('#LO_alert_delcmd option:selected').remove();   
     });
     
     $('#add_LO_N_alert').click(function() {  
        return !$('#LO_N_alert_addcmd option:selected').clone().appendTo('#LO_N_alert_delcmd');  
     });  
     $('#remove_LO_N_alert').click(function() {  
        return !$('#LO_N_alert_delcmd option:selected').remove();   
     });
     
     //scripts
     $('#add_HI_script').click(function() {  
        return !$('#HI_script_addcmd option:selected').clone().appendTo('#HI_script_delcmd');  
     });  
     $('#remove_HI_script').click(function() {  
        return !$('#HI_script_delcmd option:selected').remove();   
     });
     
     $('#add_HI_N_script').click(function() {  
        return !$('#HI_N_script_addcmd option:selected').clone().appendTo('#HI_N_script_delcmd');  
     });  
     $('#remove_HI_N_script').click(function() {  
        return !$('#HI_N_script_delcmd option:selected').remove();   
     });
     
     $('#add_LO_script').click(function() {  
        return !$('#LO_script_addcmd option:selected').clone().appendTo('#LO_script_delcmd');  
     });  
     $('#remove_LO_script').click(function() {  
        return !$('#LO_script_delcmd option:selected').remove();   
     });
     
     $('#add_LO_N_script').click(function() {  
        return !$('#LO_N_script_addcmd option:selected').clone().appendTo('#LO_N_script_delcmd');  
     });  
     $('#remove_LO_N_script').click(function() {  
        return !$('#LO_N_script_delcmd option:selected').remove();   
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
   
  <script>

	function display_vm()
	{
	        var myRandom = parseInt(Math.random()*999999999);
	        <?php echo "$.getJSON('iso_vm_server.php?element=vm".$id."all&rand=' + myRandom,"; ?>
	              function(data)
	              {
	                   setTimeout (display_vm, 1000);
	
	                   if (data.vm<?php echo $id; ?>all.vm<?php echo $id; ?>p2 == 'Volts')
	                     {
	                   			$('#vstr').replaceWith("<div id='vstr'><span style='color:red'><b>" + data.vm<?php echo $id; ?>all.vm<?php echo $id; ?>p1 + "&nbsp;" + data.vm<?php echo $id; ?>all.vm<?php echo $id; ?>p2 + " &nbsp;&nbsp;[" + data.vm<?php echo $id; ?>all.vm<?php echo $id; ?>p3 + " mv]</span>&nbsp;&nbsp;&nbsp;&nbsp;as of &nbsp;&nbsp;&nbsp;&nbsp;<span style='color:blue'>" + data.vm<?php echo $id; ?>all.vm<?php echo $id; ?>p4 + "&nbsp;-&nbsp;" + data.vm<?php echo $id; ?>all.vm<?php echo $id; ?>p5 + "</b></span></div>");
	                     }
	                   else
	                     {
	                   			$('#vstr').replaceWith("<div id='vstr'><span style='color:red'><b>" + data.vm<?php echo $id; ?>all.vm<?php echo $id; ?>p1 + "&nbsp;" + data.vm<?php echo $id; ?>all.vm<?php echo $id; ?>p2 + " (Raw value: " + data.vm<?php echo $id; ?>all.vm<?php echo $id; ?>p6 + "&nbsp;&nbsp;[" + data.vm<?php echo $id; ?>all.vm<?php echo $id; ?>p3 + " mv])</span>&nbsp;&nbsp;&nbsp;&nbsp;as of &nbsp;&nbsp;&nbsp;&nbsp;<span style='color:blue'>" + data.vm<?php echo $id; ?>all.vm<?php echo $id; ?>p4 + "&nbsp;-&nbsp;" + data.vm<?php echo $id; ?>all.vm<?php echo $id; ?>p5 + "</b></span></div>");
	                   			if (data.vm<?php echo $id; ?>all.vm<?php echo $id; ?>p7 == 'UNCHECKED')
	                     			{
	                   					$('#am').replaceWith("<span id='am'>"  + data.vm<?php echo $id; ?>all.vm<?php echo $id; ?>p9 + "</span>"); 
	                     			}
	                   			else
	                     			{
	                   					$('#wm').replaceWith("<span id='wm'>" + data.vm<?php echo $id; ?>all.vm<?php echo $id; ?>p10 + "</span>"); 
	                     			}
	                     	}
	              }
	        );
	}
	

	display_vm();
	</script>
   
   
   
   
</head>
<body class="fixed-navbar fixed-sidebar">

<div class='splash'> <div class='solid-line'></div><div class='splash-title'><h1>Loading... Please Wait...</h1><div class='spinner'> <div class='rect1'></div> <div class='rect2'></div> <div class='rect3'></div> <div class='rect4'></div> <div class='rect5'></div> </div> </div> </div>

<!--[if lt IE 7]>
<p class="alert alert-danger">You are using an <strong>old</strong> web browser. Please upgrade your browser to improve your experience.</p>
<![endif]-->

<?php start_header(); ?>

<?php left_nav("vdb"); ?>
<script language="javascript" type="text/javascript">
	SetContext('vdb');
</script>
<!-- Main Wrapper -->

<form name='Voltmeters' action='setup_voltmeter_board.php' method='post' class="form-horizontal" onsubmit="selectAllOptions('HI_alert_delcmd');selectAllOptions('HI_N_alert_delcmd');selectAllOptions('LO_alert_delcmd');selectAllOptions('LO_N_alert_delcmd');selectAllOptions('HI_script_delcmd');selectAllOptions('HI_N_script_delcmd');selectAllOptions('LO_script_delcmd');selectAllOptions('LO_N_script_delcmd');">  	
	<fieldset>
		<div id="wrapper">
			<div class="content">
		  	<!-- INFO BLOCK START -->
		  	
		  			<div class="row">
		    			<div class="col-sm-12">
		    		  	<div class="hpanel3">
		    		  	  <div class="panel-body" style="text-align:left; background:#F1F3F6;border:none;">
		    				  	<legend>USB Voltmeter <?php echo $id; ?> Setup</legend> 
		
		    				  	<div class="form-group"><label class="col-sm-1 control-label" style="text-align:left" onMouseOver="mouse_move(&#039;sd_vm_setmode&#039;);" onMouseOut="mouse_move();">ADC Mode:</label>
		              		<div class="col-sm-4 col-md-3 col-lg-3">
		              			<div class="table-responsive">
		    				   				<table width="100%" border="0">
		    				   						<tr>
		    				   							<?php
		    				  								if($mode == "v")
		    				  								{
		    				  									echo '<td>';
		    				  									echo '<div class="radio radio-success" style="text-align:left">';
		    				  									echo '<input type="radio" id="mode" name="mode" value="v" checked/>';
		    				  									echo '<label for="mode" onMouseOver="mouse_move(&#039;sd_vm_mode&#039;);" onMouseOut="mouse_move();">Voltmeter</label>';
		    				  									echo '</div>';
		    				  									echo '</td>';
		    				  									echo '<td>';
		    				  									echo '<div class="radio radio-success" style="text-align:center">';
		    				  									echo '<input type="radio" id="mode" name="mode" value="a" />';
		    				  									echo '<label for="mode" onMouseOver="mouse_move(&#039;sd_amp_mode&#039;);" onMouseOut="mouse_move();">Ammeter</label>';
		    				  									echo '</div>';
		    				  									echo '</td>';
		    				  									
		    				  								}
		    				  								else
		    				  								{
		    				  									echo '<td>';
		    				  									echo '<div class="radio radio-success" style="text-align:left">';
		    				  									echo '<input type="radio" id="mode" name="mode" value="v" />';
		    				  									echo '<label for="mode" onMouseOver="mouse_move(&#039;sd_vm_mode&#039;);" onMouseOut="mouse_move();">Voltmeter</label>';
		    				  									echo '</div>';
		    				  									echo '</td>';
		    				  									echo '<td>';
		    				  									echo '<div class="radio radio-success" style="text-align:center">';
		    				  									echo '<input type="radio" id="mode" name="mode" value="a" checked/>';
		    				  									echo '<label for="mode" onMouseOver="mouse_move(&#039;sd_amp_mode&#039;);" onMouseOut="mouse_move();">Ammeter</label>';
		    				  									echo '</div>';
		    				  									echo '</td>';
		    				  								}
		    				  								
		    				  								
		    				  							?>
		              	       	</tr>
		              	      </table>
		              	    </div>
		              		</div>
		              	</div>
		    				  	
		    				  	<?php
		    				  		if($mode == "a")
		    				  		{
		    				  			echo '<div class="form-group"><label class="col-sm-1 control-label" style="text-align:left" onMouseOver="mouse_move(&#039;sd_shunt_values&#039;);" onMouseOut="mouse_move();">Shunt Values:</label>';
		              			echo '	<div class="table-responsive col-sm-4 col-md-3 col-lg-3">';
		              			echo ' 		<table width="100%" border="0">';
		    				   			echo ' 			<tr>';	
		    				   			echo ' 				<td width="10%" onMouseOver="mouse_move(&#039;sd_shunt_values&#039;);" onMouseOut="mouse_move();">';			
		              			echo '					Amps:';
		              			echo ' 				</td>';
		              			echo '				<td width="25%">';
		              			echo '					<input type="text" class="form-control input-sm" name="shunta" value="'.$shunta.'" onMouseOver="mouse_move(&#039;sd_shunt_values&#039;);" onMouseOut="mouse_move();" required />';
		              			echo ' 				</td>';
		              			echo ' 				<td width="15%" style="text-align:right"  onMouseOver="mouse_move(&#039;sd_shunt_values&#039;);" onMouseOut="mouse_move();">';			
		              			echo '					Millivolts: ';
		              			echo ' 				</td>';
		              			echo '				<td width="25%">';
		              			echo '					<input type="text" class="form-control input-sm" name="shuntmv" value="'.$shuntmv.'" onMouseOver="mouse_move(&#039;sd_shunt_values&#039;);" onMouseOut="mouse_move();" required />';
		              			echo ' 				</td>';
		              			echo ' 			</tr>';
		              			echo ' 		</table>';	
		              			echo '	</div>';
		              			echo '</div>';
		              			
//		              			echo '<div class="form-group"><label class="col-sm-1 control-label" style="text-align:left" onMouseOver="mouse_move(&#039;sd_wattmeter&#039;);" onMouseOut="mouse_move();">Wattmeter:</label>';
//		              			echo '	<div class="table-responsive col-sm-12 col-md-10 col-lg-8">';
//		              			echo ' 		<table width="100%" border="0">';
//		    				   			echo ' 			<tr>';	
//		    				   			echo ' 				<td width="20%">';			
//		              			echo '					<div class="checkbox checkbox-success" style="text-align:left" onMouseOver="mouse_move(&#039;sd_watt_mode&#039;);" onMouseOut="mouse_move();">';
//		                    echo '   					<input type="checkbox" id="watt_enabled" name="watt_enabled" value="1" '.$active.' />';
//		                    echo '       			<label for="watt_enabled">Enable Watt Mode</label>';
//		                    echo '     			</div>';
//		              			echo ' 				</td>';
//		              			
//		              			
//		              			
//		              			echo '				<td width="10%">';
//		              			echo '					<span onMouseOver="mouse_move(&#039;sd_adc_base&#039;);" onMouseOut="mouse_move();">';
//		              			echo '						<select name="vm_base" class="form-control input-sm" style="max-width:60px;">';
//		              			if($id !== "1" && $vmode1 == "v")
//												{
//													if($watt_base_vm =="1"){echo'<option value="1" SELECTED>1</option>';}else{echo'<option value="1">1</option>';}
//												}
//												if($id !== "2" && $vmode2 == "v")
//												{
//													if($watt_base_vm =="2"){echo'<option value="2" SELECTED>2</option>';}else{echo'<option value="2">2</option>';}
//												}
//												if($id !== "3" && $vmode3 == "v")
//												{
//													if($watt_base_vm =="3"){echo'<option value="3" SELECTED>3</option>';}else{echo'<option value="3">3</option>';}
//												}
//												if($id !== "4" && $vmode4 == "v")
//												{
//													if($watt_base_vm =="4"){echo'<option value="4" SELECTED>4</option>';}else{echo'<option value="4">4</option>';}
//												}
//												if($id !== "5" && $vmode5 == "v")
//												{
//													if($watt_base_vm =="5"){echo'<option value="5" SELECTED>5</option>';}else{echo'<option value="5">5</option>';}
//												}
//												if($id !== "6" && $vmode6 == "v")
//												{
//													if($watt_base_vm =="6"){echo'<option value="6" SELECTED>6</option>';}else{echo'<option value="6">6</option>';}
//												}
//												if($id !== "7" && $vmode7 == "v")
//												{
//													if($watt_base_vm =="7"){echo'<option value="7" SELECTED>7</option>';}else{echo'<option value="7">7</option>';}
//												}
//												if($id !== "8" && $vmode8 == "v")
//												{
//													if($watt_base_vm =="8"){echo'<option value="8" SELECTED>8</option>';}else{echo'<option value="8">8</option>';}
//												}
//
//		              			echo '						</select>';
//		              			echo ' 					</span>';
//		              			echo ' 				</td>';
//		              			
//		              			echo ' 				<td>';
//		              			echo '					Choose which ADC channel (<b>in Voltmeter Mode</b>) that<br>will be used as the base reading for watt calculations';
//		              			
//		              			
//		              			echo ' 				</td>';
//		              			echo ' 			</tr>';
//		              			echo ' 		</table>';	
//		              			echo '	</div>';
//		              			echo '</div>';
		              			
		              			
		    				  		}
		    				  	else
		    				  		{
		    				  			echo '<input type="hidden" name="shunta" value="'.$shunta.'">';
												echo '<input type="hidden" name="shuntmv" value="'.$shuntmv.'">';
												//echo '<input type="hidden" name="watt_mode_enabled" value="'.$watt_mode_enabled.'">';
												//echo '<input type="hidden" name="watt_base_vm" value="'.$watt_base_vm.'">';
												
		    				  		}
		    				  	echo '<input type="hidden" name="vmode1" value="'.$vmode1.'">';
		    				  	echo '<input type="hidden" name="vmode2" value="'.$vmode2.'">';
		    				  	echo '<input type="hidden" name="vmode3" value="'.$vmode3.'">';
		    				  	echo '<input type="hidden" name="vmode4" value="'.$vmode4.'">';
		    				  	echo '<input type="hidden" name="vmode5" value="'.$vmode5.'">';
		    				  	echo '<input type="hidden" name="vmode6" value="'.$vmode6.'">';
		    				  	echo '<input type="hidden" name="id" value="'.$id.'">';
		    				  	echo '<input type="hidden" name="v_mode" value="'.$v_mode.'">';
		    				  	
		    				  	?>
		    				  	
		    				  	<div class="form-group"><label class="col-sm-1 control-label" style="text-align:left" onMouseOver="mouse_move('sd_reading');" onMouseOut="mouse_move();">ADC Reading:</label>
		              		<div class="col-sm-12 col-md-10 col-lg-8">
		              			<label class="control-label" style="text-align:left" onMouseOver="mouse_move('sd_reading');" onMouseOut="mouse_move();">
		              				<div id='vstr'></div>
		              				</label>
		              		</div>
		              	</div>
		    				  <?php
		    				  		if($mode == "a")
		    				  		{	
//		    				  			echo '<div class="form-group"><label class="col-sm-1 control-label" style="text-align:left" onMouseOver="mouse_move(&#039;amp_hour&#039;);" onMouseOut="mouse_move();">'.$v_mode.' Hours:</label>';
//		              			echo '	<div class="col-sm-8">';
//		              			echo '		<label class="control-label" style="text-align:left" onMouseOver="mouse_move(&#039;amp_hour&#039;);" onMouseOut="mouse_move();">';
//		              			if($v_mode == "Amp")
//		              			{
//		              				echo '			<span id="am">0.000000</span>';
//		              			}
//		              			if($v_mode == "Watt")
//		              			{
//		              				echo '			<span id="wm">0.000000</span>';
//		              			}
//		              			echo '		</label>&nbsp;&nbsp;&nbsp;';
//		              			echo '		<button name="reset" class="btn btn-primary" type="submit"><i class="fa fa-check" ></i> Reset '.$v_mode.' Hour Counter '.$id.'</button>';
//		              			echo '	</div>';
//		              			echo '</div>';
		    				  		}
		    				  ?>
		    				  	<div class="form-group"><label class="col-sm-1 control-label" style="text-align:left" onMouseOver="mouse_move('sd_precision');" onMouseOut="mouse_move();">Precision</label>
              				<div class="col-sm-6 col-md-2 col-lg-2" onMouseOver="mouse_move('sd_precision');" onMouseOut="mouse_move();">
              					<input class="input-sm" id="per" type="text" name="per" style="text-align:center" value="<?php echo $per; ?>">
              				</div>
              			</div>
		    				  	
		    				  	<div class="form-group"><label class="col-sm-1 control-label" style="text-align:left" onMouseOver="mouse_move('sd_adj');" onMouseOut="mouse_move();">Adjustment:</label>
              				<div class="col-sm-6 col-md-2 col-lg-2" onMouseOver="mouse_move('sd_mul');" onMouseOut="mouse_move();">
              					<input type="text" class="form-control input-sm" name="vmadj" style="text-align:left" value="<?php echo $vmadj; ?>">
              				</div>
              				<div>
              					<span class="col-sm-6 col-md-4 col-lg-2 control-label" style="text-align:left">Multiply or Divide</span>
              				</div>
              			</div>
		    				  	
		    				  	<?php
		              		if($polarity=="BOTH")
												{
													$r1="CHECKED";
												}
											else
												{
													$r1=" ";
												}
											if($polarity=="POS")
												{
													$r2="CHECKED";
												}
											else
												{
													$r2=" ";
												}
											if($polarity=="NEG")
												{
													$r3="CHECKED";
												}
											else
												{
													$r3=" ";
												}
		
		              		?>
		              	
		              	<div class="form-group"><label class="col-sm-1 control-label" style="text-align:left" onMouseOver="mouse_move(&#039;sd_polarity_filter&#039;);" onMouseOut="mouse_move();">Polarity Filter:</label>
		              		<div class="col-sm-6 col-md-6 col-lg-4">
		              			<div class="table-responsive">
		    				   				<table width="100%" border="0">
		    				   					<tr>
		    				  						<td>
		    				  							<div class="radio radio-success" style="text-align:left" onMouseOver="mouse_move(&#039;sd_polarity_filter&#039;);" onMouseOut="mouse_move();">
		    				  								<input type="radio" id="BOTH" name="polarity" value="BOTH" <?php echo $r1; ?>/>
		              								<label for="BOTH">Show Both</label>
		    				  							</div>
		    				  						</td>
		    				  						<td>
		    				  							<div class="radio radio-warning" style="text-align:center" onMouseOver="mouse_move(&#039;sd_polarity_filter&#039;);" onMouseOut="mouse_move();">
		    				  								<input type="radio" id="POS" name="polarity" value="POS"  <?php echo $r2; ?>/>
		              								<label for="POS">Positive Only</label>
		    				  							</div>
		    				  						</td>
		    				  						<td>
		    				  							<div class="radio radio-danger" style="text-align:center" onMouseOver="mouse_move(&#039;sd_polarity_filter&#039;);" onMouseOut="mouse_move();">
		    				  								<input type="radio" id="NEG" name="polarity" value="NEG"  <?php echo $r3; ?>/>
		              								<label for="NEG">Negative Only</label>
		    				  							</div>
		    				  						</td>	
		              	       	</tr>
		              	      </table>
		              	    </div>
		              		</div>
		              	</div>
		    				  	
		    				  	<?php
		    				  		if($vpolarity=="NORMAL")
											{
												$p1="CHECKED";
												$p2=" ";
											}
											else
											{
												$p1=" ";
												$p2="CHECKED";
											}
		    				  	?>
		    				  	
		    				  	<div class="form-group"><label class="col-sm-1 control-label" style="text-align:left" onMouseOver="mouse_move(&#039;sd_polarity_view&#039;);" onMouseOut="mouse_move();">Polarity:</label>
		              		<div class="col-sm-6 col-md-5 col-lg-3">
		              			<div class="table-responsive">
		    				   				<table width="100%" border="0">
		    				   					<tr>
		    				  						<td width="35%">
		    				  							<div class="radio radio-success" style="text-align:left" onMouseOver="mouse_move(&#039;sd_polarity_view&#039;);" onMouseOut="mouse_move();">
		    				  								<input type="radio" id="NORMAL" name="vpolarity" value="NORMAL" <?php echo $p1; ?>/>
		              								<label for="NORMAL">Normal</label>
		    				  							</div>
		    				  						</td>
		    				  						<td>
		    				  							<div class="radio radio-danger" style="text-align:center" onMouseOver="mouse_move(&#039;sd_polarity_view&#039;);" onMouseOut="mouse_move();">
		    				  								<input type="radio" id="REVERSE" name="vpolarity" value="REVERSE"  <?php echo $p2; ?>/>
		              								<label for="REVERSE">Reverse</label>
		    				  							</div>
		    				  						</td>
		              	       	</tr>
		              	      </table>
		              	    </div>
		              		</div>
		              	</div>
		    				  	
		    				  	<?php
		    				  		if($averaging=="on")
											{
												$averaging="CHECKED";
											}
											
										?>
		    				  	<div class="form-group"><label class="col-sm-1 control-label" style="text-align:left" onMouseOver="mouse_move(&#039;sd_average&#039;);" onMouseOut="mouse_move();">Averaging Filter:</label>
		              		<div class="table-responsive col-sm-6 col-md-5 col-lg-3">
		              	 		<table width="100%" border="0">
		    				   	 			<tr>
		    				   	 				<td width="20%">	
		              						<div class="checkbox checkbox-success" style="text-align:left" onMouseOver="mouse_move(&#039;sd_average&#039;);" onMouseOut="mouse_move();">
		                   					<input type="checkbox" id="averaging" name="averaging" <?php echo $averaging; ?> />
		                       			<label for="averaging"></label>
		                     			</div>
		              	 				</td>
		              					<td width="60%" style="text-align:right">
		              						Weight Factor:&nbsp;&nbsp;	
		              	 				</td>
		              	 				<td>
		              						<span onMouseOver="mouse_move(&#039;sd_average&#039;);" onMouseOut="mouse_move();">
		              							<input type="text" class="form-control input-sm" name="weight" style="text-align:left" value="<?php echo $weight; ?>">
		              	 					</span>
		              	 				</td>
		              	 			</tr>
		              	 		</table>	
		              		</div>
		              	</div>
		    				  	
		    				  	<?php
		    				  		if($polling=="on")
											{
												$polling="CHECKED";
											}
		    				  	?>
		    				  	
		    				  	<div class="form-group"><label class="col-sm-1 control-label" style="text-align:left" onMouseOver="mouse_move(&#039;sd_polling&#039;);" onMouseOut="mouse_move();">Polling Enabled?</label>
		              		<div class="table-responsive col-sm-4 col-md-2 col-lg-1">
		              	 		<table width="100%" border="0">
		    				   	 			<tr>
		    				   	 				<td width="10%">	
		              						<div class="checkbox checkbox-success" style="text-align:left" onMouseOver="mouse_move(&#039;sd_polling&#039;);" onMouseOut="mouse_move();">
		                   					<input type="checkbox" id="polling" name="polling" <?php echo $polling; ?> />
		                       			<label for="polling"></label>
		                     			</div>
		              	 				</td>
		              	 			</tr>
		              	 		</table>	
		              		</div>
		              	</div>
		              	
		              	<div class="form-group"><label class="col-sm-2 col-md-1 control-label" style="text-align:left" onMouseOver="mouse_move(&#039;sd_suppress&#039;);" onMouseOut="mouse_move();">Suppression?</label>
		              		<div class="col-sm-10 col-md-10 col-lg-10">
		              			<div class="checkbox checkbox-danger" style="text-align:left" onMouseOver="mouse_move(&#039;sd_suppress&#039;);" onMouseOut="mouse_move();">
		                   		<input type="checkbox" id="supress" name="supress" <?php echo $supress; ?> />
		                      <label for="supress">Suppress Trigger Actions on Boot?</label>
		                    </div>
		                  </div>
		              	</div>
		              	
		              	
		              	<div class="form-group"><label class="col-sm-1 control-label" style="text-align:left" onMouseOver="mouse_move(&#039;sd_polling&#039;);" onMouseOut="mouse_move();">Graph Options:</label>
		              		<div class="table-responsive col-sm-10 col-md-8 col-lg-6">
		              	 		<table width="100%" border="0">
		    				   	 			<tr>
		    				   	 				<td width="15%">	
		              						<div class="checkbox checkbox-success" style="text-align:left" onMouseOver="mouse_move(&#039;sd_slope&#039;);" onMouseOut="mouse_move();">
		                   					<input type="checkbox" id="slope_enable" name="slope_enable" <?php echo $slope_enable; ?> />
		                       			<label for="slope_enable">Slope Enabled?</label>
		                     			</div>
		              	 				</td>
		              	 				<td width="15%">	
		              						<div class="checkbox checkbox-success" style="text-align:left" onMouseOver="mouse_move(&#039;sd_limit&#039;);" onMouseOut="mouse_move();">
		                   					<input type="checkbox" id="limit_enable" name="limit_enable" <?php echo $limit_enable; ?> />
		                       			<label for="limit_enable">Limits Enabled?</label>
		                     			</div>
		              	 				</td>
		              	 				<td width="6%">
		              						<span onMouseOver="mouse_move(&#039;sd_lower&#039;);" onMouseOut="mouse_move();">
		              							<input type="text" class="form-control input-sm" name="lower_limit" style="text-align:left" value="<?php echo $lower_limit; ?>">
		              	 					</span>
		              	 				</td>
		              	 				<td width="10%">
		              						<span onMouseOver="mouse_move(&#039;sd_lower&#039;);" onMouseOut="mouse_move();">
		              							&nbsp;&nbsp;Lower Limit
		              	 					</span>
		              	 				</td>
		              	 				<td width="6%">
		              						<span onMouseOver="mouse_move(&#039;sd_upper&#039;);" onMouseOut="mouse_move();">
		              							<input type="text" class="form-control input-sm" name="upper_limit" style="text-align:left" value="<?php echo $upper_limit; ?>">
		              	 					</span>
		              	 				</td>
		              	 				<td width="10%">
		              						<span onMouseOver="mouse_move(&#039;sd_upper&#039;);" onMouseOut="mouse_move();">
		              							&nbsp;&nbsp;Upper Limit
		              	 					</span>
		              	 				</td>
		              	 			</tr>
		              	 		</table>	
		              		</div>
		              	</div>
		              	
		              	<div class="form-group"><label class="col-sm-1 control-label" style="text-align:left" onMouseOver="mouse_move(&#039;sd_vm_name&#039;);" onMouseOut="mouse_move();">Voltmeter Name:</label>
		              		<div class="col-sm-col-sm-5 col-md-4 col-lg-3">
		              			<input type="text" class="form-control input-sm" name="name" style="text-align:left"  onMouseOver="mouse_move(&#039;sd_vm_name&#039;);" onMouseOut="mouse_move();" value="<?php echo $name; ?>">
		                  </div>
		              	</div>
		              	
		    				  	<div class="form-group"><label class="col-sm-1 control-label" style="text-align:left" onMouseOver="mouse_move(&#039;sd_vm_notes&#039;);" onMouseOut="mouse_move();">Voltmeter Notes:</label>
		              		<div class="col-sm-col-sm-5 col-md-4 col-lg-3">
		              			<textarea  rows="3" cols="48" class="form-control" name='notes'  onMouseOver="mouse_move(&#039;sd_vm_notes&#039;);" onMouseOut="mouse_move();" required><?php echo $notes; ?></textarea>
		              		</div>
		              	</div>
		              	
		    				  	<div class="form-group">
		        					<div class="col-sm-col-sm-5 col-md-4 col-lg-4">
		        						<button name="apply_btn" class="btn btn-success" type="submit" onMouseOver="mouse_move(&#039;b_apply&#039;);" onMouseOut="mouse_move();"><i class="fa fa-check" ></i> Apply</button>
		        						<button name="ok_btn" class="btn btn-success" type="submit" onMouseOver="mouse_move(&#039;b_apply&#039;);" onMouseOut="mouse_move();"><i class="fa fa-check" ></i> OK</button>
		        						<button name="cancel_btn" class="btn btn-primary" type="submit" onMouseOver="mouse_move(&#039;b_cancel&#039;);" onMouseOut="mouse_move();" formnovalidate><i class="fa fa-times"></i> Cancel</button>
		        					</div>
		        				</div>
		    				  	
		    				  	
		    				  	
		    				  	<div class="table-responsive">
		    				   		<table width="100%" border="1" class="table table-striped table-condensed">
		    				   			<thead>
		    				   				<tr>
		    				   					<th width="10%" style="background:#D6DFF7;">
		    				   						<div style="text-align:center;color:black">Enabled</div>
		    				   					</th>
		    				   					<th width="40%" style="background:red">
		    				   						<div style="text-align:center;color:black"><?php echo $vm_mode.' '.$id; ?> High Trigger Range</div>
		    				   					</th>
		    				   					<th width="40%" style="background:#40FF40">
		    				   						<div style="text-align:center;color:black"><?php echo $vm_mode.' '.$id; ?> High Normal Trigger</div>
		    				   					</th>
		    				   				</tr>
		    				   			</thead>
		    				   			<tbody>
		    				   				<tr>
		    				   					<td style="vertical-align:middle">
		    				   						<?php
		    				   							if($h_en == "1")
		    				   							{
		    				   								$active = "checked";
		    				   							}
		    				   							else
		    				   							{
		    				   								$active = " ";
		    				   							}
		    				   						?>
		    				   						
		    				   							<div class="checkbox checkbox-success"  style="text-align:center">
		                            	<input type="checkbox" id="h_en" name="h_en" value="1" <?php echo $active; ?>> />
		                              <label for="h_en"></label>
		                            </div>	
		    				   					</td>
		    				   					
		    				   					<td>
		    				   						<div class="table-responsive">
		    				   							<table class="table table-condensed table-hover">
																	<tr>
																		<td style="text-align:right;vertical-align:middle">
																			<strong style="font-size: 15px;">High trigger value (max):</strong>
		    				   									</td>	
		    				   									<td>
		              										<input style="max-width:30%;" type="text" class="form-control input-sm" name='hi_t' value='<?php $hi_t = sprintf("%.4f",$hi_t); echo $hi_t; ?>' onMouseOver="mouse_move(&#039;vhimax&#039;);" onMouseOut="mouse_move();" required />		
		    				   									</td>
		    				   								</tr>
		    				   								<tr>
																		<td style="text-align:right;vertical-align:middle">
																			<strong style="font-size: 15px;">High trigger value (min):</strong>
		    				   									</td>	
		    				   									<td>
		              										<input style="max-width:30%;" type="text" class="form-control input-sm" name='hi_t_min' value='<?php $hi_t_min = sprintf("%.4f",$hi_t_min); echo $hi_t_min; ?>' onMouseOver="mouse_move(&#039;vhimin&#039;);" onMouseOut="mouse_move();" required />		
		    				   									</td>
		    				   								</tr>
		    				   								<tr>
		    				   									<td style="text-align:right;vertical-align:middle">
		    				   										<strong style="font-size: 15px;">Execute the actions below every:</strong> 
																		</td>
																		<td>
																			<select class="form-control input-sm" style="max-width:40%;" name="hi_flap" >
																				<option value="0">One Shot</option>
																				<?php
																				$ii=1;	if($hi_flap==$ii) {$chan=sprintf("selected");} else {$chan=sprintf(" ");} echo"<option ".$chan." value=".$ii.">".$ii." Second</option>";	
																				for($ii=2; $ii<60; $ii++)	{	if($hi_flap==$ii) {$chan = "selected";} else {$chan = " ";} echo"<option " . $chan . " value='" . $ii . "'>" . $ii . " Seconds</option>";	}
																				$ii=1; if($hi_flap==($ii*60)) {$chan = "selected";} else {$chan = " ";} echo "<option " . $chan . " value='" . $ii*60 . "'>" . $ii . " Minute</option>";
																				for($ii=1; $ii<60; $ii++)	{	if($hi_flap==($ii*60)) {$chan = "selected";} else {$chan = " ";} echo "<option " . $chan . " value='" . $ii*60 . "'>" . $ii . " Minutes</option>";	}
																				$ii=1;	if($hi_flap==($ii*60*60)) {$chan = "selected";} else {$chan = " ";} echo "<option " . $chan . " value='" . $ii*60*60 . "'>" . $ii . " Hour</option>";
																				for($ii=1; $ii<25; $ii++)	{ if($hi_flap==($ii*60*60)) {$chan = "selected";} else {$chan = " ";} echo "<option " . $chan . " value='" . $ii*60*60 . "'>" . $ii . " Hours</option>";	}
																				?>
		    				   										</select>
		    				   									</td>
		    				   								</tr>
		    				   							</table>
		    				   						</div>
														</td>
														
														<td style="vertical-align:middle">
															<div style="text-align:center;"><strong style="font-size: 15px;color:black;">These events will fire when <?php echo $vm_mode.' '.$id; ?> drops below the high trigger minimum value.</strong></div>	
														</td>
													</tr>
													
													<tr>
		    				   					<td style="vertical-align:middle">
		    				   						<div style="text-align:center;"><a name="#hi1"></a><br><a href="setup_notifications.php"><strong><u class="dotted">Create Alerts</u></strong></a></div>
		    				   					</td>
		    				   					<td>		
		    				   						<?php selectbox("HI", "alert", $HI_alert_cmds); ?>	
		    				   					</td>				
		    				   					<td>						
		    				   						<?php selectbox("HI_N", "alert", $HI_N_alert_cmds); ?>
		    				   					</td>							
		    				   				</tr>
		    				   				
		    				   				<tr>				
		    				   					<td style="vertical-align:middle">
		    				   						<div style="text-align:center;"><a name="#hiN1"></a><br><a href="setup_scripts.php?"><strong><u class="dotted">Create Scripts</u></strong></a></div>
		    				   					</td>
		    				   					<td>		
		    				   						<?php selectbox("HI", "script", $HI_script_cmds); ?>	
		    				   					</td>				
		    				   					<td>						
		    				   						<?php selectbox("HI_N", "script", $HI_N_script_cmds); ?>
		    				   					</td>										
		    				   				</tr>
		    				   				
		    				   				<tr>
														<td style="vertical-align:middle">
														<div style="text-align:center;"><a href="#hi3"></a><a href="setup_file_explorer.php"><strong><u class="dotted">File</u></strong></a></center></div>
														</td>
													
														<td>
															<div><label class="col-sm-2 control-label">Execute File:</label>
		              							<div class="col-sm-8">
		              								<input type="text" class="form-control" name='RunHiFile' value='<?php echo $RunHiFile; ?>' />
		              							</div>
		              						</div>
														</td>
													
														<td>
															<div><label class="col-sm-2 control-label">Execute File:</label>
		              							<div class="col-sm-8">
		              								<input type="text" class="form-control" name='RunHiNFile' value='<?php echo $RunHiNFile; ?>' />
		              							</div>
		              						</div>
														</td>
													</tr>
		    				   			</tbody>
		  								</table>      	    
		    		  	  	</div> <!-- END TABLE RESPONSIVE -->
		    		  	  	
		    		  	  	
		    		  	  	<div class="table-responsive">
		    				   		<table width="100%" border="1" class="table table-striped table-condensed">
		    				   			<thead>
		    				   				<tr>
		    				   					<th width="10%" style="background:#D6DFF7;">
		    				   						<div style="text-align:center;color:black">Enabled</div>
		    				   					</th>
		    				   					<th width="40%" style="background:yellow">
		    				   						<div style="text-align:center;color:black"><?php echo $vm_mode.' '.$id; ?> Low Trigger Range</div>
		    				   					</th>
		    				   					<th width="40%" style="background:#40FF40">
		    				   						<div style="text-align:center;color:black"><?php echo $vm_mode.' '.$id; ?> Low Normal Trigger</div>
		    				   					</th>
		    				   				</tr>
		    				   			</thead>
		    				   			<tbody>
		    				   				<tr>
		    				   					<td style="vertical-align:middle">
		    				   						<?php
		    				   							if($l_en == "1")
		    				   							{
		    				   								$active = "checked";
		    				   							}
		    				   							else
		    				   							{
		    				   								$active = " ";
		    				   							}
		    				   						?>
		    				   						
		    				   					
		    				   							<div class="checkbox checkbox-success"  style="text-align:center">
		                            	<input type="checkbox" id="l_en" name="l_en" value="1" <?php echo $active; ?>> />
		                              <label for="l_en"></label>
		                            </div>	
		    				   					</td>
		    				   					
		    				   					<td>
		    				   						<div class="table-responsive">
		    				   							<table class="table table-condensed table-hover">
																	<tr>
																		<td style="text-align:right;vertical-align:middle">
																			<strong style="font-size: 15px;">Low trigger value (min):</strong>
		    				   									</td>	
		    				   									<td>
		              										<input style="max-width:30%;" type="text" class="form-control input-sm" name='lo_t_max' value='<?php $lo_t_max = sprintf("%.4f",$lo_t_max); echo $lo_t_max; ?>' onMouseOver="mouse_move(&#039;vlomin&#039;);" onMouseOut="mouse_move();" required />		
		    				   									</td>
		    				   								</tr>
		    				   								<tr>
																		<td style="text-align:right;vertical-align:middle">
																			<strong style="font-size: 15px;">Low trigger value (max):</strong>
		    				   									</td>	
		    				   									<td>
		              										<input style="max-width:30%;" type="text" class="form-control input-sm" name='lo_t' value='<?php $lo_t = sprintf("%.4f",$lo_t); echo $lo_t; ?>' onMouseOver="mouse_move(&#039;vlomax&#039;);" onMouseOut="mouse_move();" required />		
		              										
		    				   									</td>
		    				   								</tr>
		    				   								<tr>
		    				   									<td style="text-align:right;vertical-align:middle">
		    				   										<strong style="font-size: 15px;">Execute the actions below every:</strong> 
																		</td>
																		<td>
																			<select class="form-control input-sm" style="max-width:40%;" name="lo_flap" >
																				<option value="0">One Shot</option>
																				<?php
																				$ii=1;	if($lo_flap==$ii) {$chan=sprintf("selected");} else {$chan=sprintf(" ");} echo"<option ".$chan." value=".$ii.">".$ii." Second</option>";	
																				for($ii=2; $ii<60; $ii++)	{	if($lo_flap==$ii) {$chan = "selected";} else {$chan = " ";} echo"<option " . $chan . " value='" . $ii . "'>" . $ii . " Seconds</option>";	}
																				$ii=1; if($lo_flap==($ii*60)) {$chan = "selected";} else {$chan = " ";} echo "<option " . $chan . " value='" . $ii*60 . "'>" . $ii . " Minute</option>";
																				for($ii=1; $ii<60; $ii++)	{	if($lo_flap==($ii*60)) {$chan = "selected";} else {$chan = " ";} echo "<option " . $chan . " value='" . $ii*60 . "'>" . $ii . " Minutes</option>";	}
																				$ii=1;	if($lo_flap==($ii*60*60)) {$chan = "selected";} else {$chan = " ";} echo "<option " . $chan . " value='" . $ii*60*60 . "'>" . $ii . " Hour</option>";
																				for($ii=1; $ii<25; $ii++)	{ if($lo_flap==($ii*60*60)) {$chan = "selected";} else {$chan = " ";} echo "<option " . $chan . " value='" . $ii*60*60 . "'>" . $ii . " Hours</option>";	}
																				?>
		    				   										</select>
		    				   									</td>
		    				   								</tr>
		    				   							</table>
		    				   						</div>
														</td>
														
														<td style="vertical-align:middle">
															<div style="text-align:center;"><strong style="font-size: 15px;color:black;">These events will fire when <?php echo $vm_mode.' '.$id; ?> rises above the low trigger minimum value.</strong></div>	
														</td>
													</tr>
													
													<tr>
		    				   					<td style="vertical-align:middle">
		    				   						<div style="text-align:center;"><a name="#lo1"></a><br><a href="setup_notifications.php"><strong><u class="dotted">Create Alerts</u></strong></a></div>
		    				   					</td>
		    				   					<td>		
		    				   						<?php selectbox("LO", "alert", $LO_alert_cmds); ?>	
		    				   					</td>				
		    				   					<td>						
		    				   						<?php selectbox("LO_N", "alert", $LO_N_alert_cmds); ?>
		    				   					</td>							
		    				   				</tr>
		    				   				
		    				   				<tr>				
		    				   					<td style="vertical-align:middle">
		    				   						<div style="text-align:center;"><a name="#loN1"></a><br><a href="setup_scripts.php?"><strong><u class="dotted">Create Scripts</u></strong></a></div>
		    				   					</td>
		    				   					<td>		
		    				   						<?php selectbox("LO", "script", $LO_script_cmds); ?>	
		    				   					</td>				
		    				   					<td>						
		    				   						<?php selectbox("LO_N", "script", $LO_N_script_cmds); ?>
		    				   					</td>										
		    				   				</tr>
		    				   				
		    				   				<tr>
														<td style="vertical-align:middle">
														<div style="text-align:center;"><a href="#lo3"></a><a href="setup_file_explorer.php"><strong><u class="dotted">File</u></strong></a></center></div>
														</td>
													
														<td>
															<div><label class="col-sm-2 control-label">Execute File:</label>
		              							<div class="col-sm-8">
		              								<input type="text" class="form-control" name='RunLowFile' value='<?php echo $RunLowFile; ?>' />
		              							</div>
		              						</div>
														</td>
													
														<td>
															<div><label class="col-sm-2 control-label">Execute File:</label>
		              							<div class="col-sm-8">
		              								<input type="text" class="form-control" name='RunLowNFile' value='<?php echo $RunLowNFile; ?>' />
		              							</div>
		              						</div>
														</td>
													</tr>
		    				   			</tbody>
		  								</table>      	    
		    		  	  	</div> <!-- END TABLE RESPONSIVE -->
		    		  	  	
		    		  	  	<div class="form-group">
		        					<div class="col-sm-2">
		        						<button name="apply_btn" class="btn btn-success" type="submit" onMouseOver="mouse_move(&#039;b_apply&#039;);" onMouseOut="mouse_move();"><i class="fa fa-check" ></i> Apply</button>
		        						<button name="ok_btn" class="btn btn-success" type="submit" onMouseOver="mouse_move(&#039;b_apply&#039;);" onMouseOut="mouse_move();"><i class="fa fa-check" ></i> OK</button>
		        						<button name="cancel_btn" class="btn btn-primary" type="submit" onMouseOver="mouse_move(&#039;b_cancel&#039;);" onMouseOut="mouse_move();" formnovalidate><i class="fa fa-times"></i> Cancel</button>
		        					</div>
		        				</div>
		    		  		</div> <!-- END PANEL BODY --> 
		    		  	</div> <!-- END HPANEL3 --> 
		    		  </div> <!-- END COL-MD-12 --> 
		    		</div> <!-- END ROW -->
		    			
		  </div> <!-- END CONTENT -->    
		</div> <!-- END Main Wrapper -->
	</fieldset>
</form>	

<script>
$(function(){
    $("#per").TouchSpin({
        min: 1,
        max: 6,
        step: 1,
        decimals: 0,
        boostat: 5,
        maxboostedstep: 6,
    });
});

</script>



<?php 

if($alert_flag == "1")
{
echo"<script>";
echo"swal({";
echo"  title:'Success!',";
echo"  text: 'Settings Saved',";
echo"  type: 'success',";
echo"  showConfirmButton: false,";
echo"	 html: true,";
echo"  timer: 2500";
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

if($alert_flag == "3")
{
echo"<script>";
echo"swal({";
echo"  title:'Success!',";
echo"  text: 'Amp Hour Counter Reset',";
echo"  type: 'success',";
echo"  showConfirmButton: false,";
echo"	 html: true,";
echo"  timer: 2500";
echo"});";
echo"</script>";
}

if($alert_flag == "4")
{
echo"<script>";
echo"swal({";
echo"  title:'Success!',";
echo"  text: 'Watt Hour Counter Reset',";
echo"  type: 'success',";
echo"  showConfirmButton: false,";
echo"	 html: true,";
echo"  timer: 2500";
echo"});";
echo"</script>";
}


echo "</body>";
echo "</html>";

?>

