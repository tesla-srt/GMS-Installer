<?php
	error_reporting(E_ALL);
	include "lib.php";
	$hostname = trim(file_get_contents("/etc/hostname"));
	$alert_flag = "0";
	$id = "0";
	$confirmation = "";
	$dbh = new PDO('sqlite:/etc/rms100.db');
	
	$result  = $dbh->query("SELECT * FROM display_options;");			
	foreach($result as $row)
	{
		$screen_animations = $row['screen_animations'];
	}
	
	$result  = $dbh->query("SELECT * FROM throttle;");			
	foreach($result as $row)
	{
		$dt = $row['delay'];
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
	
	
	$result  = $dbh->query("SELECT * FROM vm_polarity WHERE id='1';");			
	foreach($result as $row)
		{
			$v1_pol = $row['polarity'];
		}
	
	$result  = $dbh->query("SELECT * FROM vm_polarity WHERE id='2';");			
	foreach($result as $row)
		{
			$v2_pol = $row['polarity'];
		}
	
	$result  = $dbh->query("SELECT * FROM vm_polarity WHERE id='3';");			
	foreach($result as $row)
		{
			$v3_pol = $row['polarity'];
		}
	
	$result  = $dbh->query("SELECT * FROM voltmeters WHERE id='1';");			
	foreach($result as $row)
		{
			$v1_name = $row['name'];
			$v1_notes = $row['notes'];
			$v1_notes = str_replace(array("\r\n", "\r", "\n"), "<br>", $v1_notes);
			$v1_mode = $row['mode'];
			$v1_vmadj = $row['vmadj'];
			$v1_per = $row['per'];
			$v1_watt = $row['watt_mode_enabled'];
		}
	
	$result  = $dbh->query("SELECT * FROM voltmeters WHERE id='2';");			
	foreach($result as $row)
		{
			$v2_name = $row['name'];
			$v2_notes = $row['notes'];
			$v2_notes = str_replace(array("\r\n", "\r", "\n"), "<br>", $v2_notes);
			$v2_mode = $row['mode'];
			$v2_vmadj = $row['vmadj'];
			$v2_per = $row['per'];
			$v2_watt = $row['watt_mode_enabled'];
		}
	
	$result  = $dbh->query("SELECT * FROM voltmeters WHERE id='3';");			
	foreach($result as $row)
		{
			$v3_name = $row['name'];
			$v3_notes = $row['notes'];
			$v3_notes = str_replace(array("\r\n", "\r", "\n"), "<br>", $v3_notes);
			$v3_mode = $row['mode'];
			$v3_vmadj = $row['vmadj'];
			$v3_per = $row['per'];
			$v3_watt = $row['watt_mode_enabled'];
		}
	
	$result  = $dbh->query("SELECT * FROM vm_polling_speed;");			
	foreach($result as $row)
		{
			$polling_speed_value = $row['polling_speed_value'];
		}

	$result  = $dbh->query("SELECT * FROM v_units WHERE id='1';");			
	foreach($result as $row)
		{
			$v1units_override = $row['override'];
			$v1units_name = $row['name'];
		}
	
	$result  = $dbh->query("SELECT * FROM v_units WHERE id='2';");			
	foreach($result as $row)
		{
			$v2units_override = $row['override'];
			$v2units_name = $row['name'];
		}
	
	$result  = $dbh->query("SELECT * FROM v_units WHERE id='3';");			
	foreach($result as $row)
		{
			$v3units_override = $row['override'];
			$v3units_name = $row['name'];
		}	
	
		
/////////////////////////////////////////////////////////////////
//                                                             //
//                    GET PROCESSING                           //
//                                                             //
/////////////////////////////////////////////////////////////////
	
if(isset ($_GET['confirm']))
{
	$confirm = $_GET['confirm'];
	if($confirm == "yes")
	{
		$alert_flag = "1";
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
	}

if(isset ($_GET['confirm']))
	{
		$confirm = $_GET['confirm'];
		if(isset($_GET['id']))
		{
			$id = $_GET['id'];
			if($confirm == "run")
			{
				$command = "rmsscript ".$id.". > /dev/null 2>&1 &";
				exec($command);
				$text = "Script ID #".$id." has been executed!";
				$alert_flag = "2";
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

if(isset ($_POST['graphs']))
	{
		header("Location: rms-graph.php");
	}

if(isset ($_POST['polling_speed']))
	{
		$polling_speed_value = $_POST['polling_speed_value'];
		$query = sprintf("UPDATE vm_polling_speed SET polling_speed_value='%s';",$polling_speed_value);
		$result  = $dbh->exec($query); 
		restart_some_services();
		
		$alert_flag = "1";
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
    <link rel="stylesheet" href="css/jquery-ui.min.css" />
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
			SetContext('voltmeters');
		</script>
		
		<script>
			
			function display_vm ()
			{
        var myRandom = parseInt(Math.random()*999999999);
        $.getJSON('sdserver.php?element=vmall&rand=' + myRandom,
                function(data)
                {
                       $.each (data.vms, function (k, v) { $('#' + k).text (v); });
                       <?php
		                  		echo"setTimeout (display_vm," . $dt . ");";
		                 	 ?>

                       if (parseFloat(data.vms.vm1) > 0)
                        {
                        	if(data.vms.vm1 > 100){data.vms.vm1 = 99;}
                        	$('#progressbar1').progressbar({ value: + data.vms.vm1 });
                        	$('#progressbar1 > div').css({ 'background': '#00FF00' });
                        	$('#progressbar1 > div').css({ 'border-color': '#00FF00' });
                       }
                       else
                        {
                        	data.vms.vm1 = Math.abs(data.vms.vm1);
                        	if(data.vms.vm1 > 100){data.vms.vm1 = 99;}
                        	$('#progressbar1').progressbar({ value: + data.vms.vm1 });
                        	$('#progressbar1 > div').css({ 'background': 'Red' });
                        	$('#progressbar1 > div').css({ 'border-color': 'Red' });
                        }
		
		                   if (parseFloat(data.vms.vm2) > 0)
                        {
                        	if(data.vms.vm2 > 100){data.vms.vm2 = 99;}
                        	$('#progressbar2').progressbar({ value: + data.vms.vm2 });
                        	$('#progressbar2 > div').css({ 'background': '#00FF00' });
                        	$('#progressbar2 > div').css({ 'border-color': '#00FF00' });
                       }
                       else
                        {
                        	data.vms.vm2 = Math.abs(data.vms.vm2);
                        	if(data.vms.vm2 > 100){data.vms.vm2 = 99;}
                        	$('#progressbar2').progressbar({ value: + data.vms.vm2 });
                        	$('#progressbar2 > div').css({ 'background': 'Red' });
                        	$('#progressbar2 > div').css({ 'border-color': 'Red' });
                        }
		
		                   if (parseFloat(data.vms.vm3) > 0)
                        {
                        	if(data.vms.vm3 > 100){data.vms.vm3 = 99;}
                        	$('#progressbar3').progressbar({ value: + data.vms.vm3 });
                        	$('#progressbar3 > div').css({ 'background': '#00FF00' });
                        	$('#progressbar3 > div').css({ 'border-color': '#00FF00' });
                       }
                       else
                        {
                        	data.vms.vm3 = Math.abs(data.vms.vm3);
                        	if(data.vms.vm3 > 100){data.vms.vm3 = 99;}
                        	$('#progressbar3').progressbar({ value: + data.vms.vm3 });
                        	$('#progressbar3 > div').css({ 'background': 'Red' });
                        	$('#progressbar3 > div').css({ 'border-color': 'Red' });
                        }
		
		                  
                }
        	);
			}

		display_vm ();
		</script>
		
		
		
		
		
</head>
<body class="fixed-navbar fixed-sidebar">

<div class='splash'> <div class='solid-line'></div><div class='splash-title'><h1>Loading... Please Wait...</h1><div class='spinner'> <div class='rect1'></div> <div class='rect2'></div> <div class='rect3'></div> <div class='rect4'></div> <div class='rect5'></div> </div> </div> </div>

<!--[if lt IE 7]>
<p class="alert alert-danger">You are using an <strong>old</strong> web browser. Please upgrade your browser to improve your experience.</p>
<![endif]-->

<?php start_header(); ?>

<?php left_nav("voltmeters"); ?>
<script language="javascript" type="text/javascript">
	SetContext('voltmeters');
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
		<form name='Voltmeters' action='voltmeters.php' method='post' class="form-horizontal">  	
    	<fieldset>
  			
  			
  			<!-- VOLTMETER BLOCK START -->    
    		<div class="row top-buffer">
    			<div class="col-sm-12">
    		  	<div class="hpanel">
    		      <div class="panel-body">
    		      	<legend>Voltmeter Overview</legend>
    		      	<div class="table-responsive">
    		        	<table width="100%" class="table-striped">
    		          	<thead>
    		            	<tr>
    		              	<th style="width:5%; background-color:#D6DFF7;"><span style="color:black">Voltmeter</span></th>
    		              	<th style="width:3%; background-color:#D6DFF7;"><span style="color:black">#</span></th>
    		              	<th style="width:10%; background-color:#D6DFF7;"><span style="color:black">Range</span></th>
    		              	<th style="width:10%; background-color:#D6DFF7;"><span style="color:black">Units</span></th>
    		              	<th style="width:10%; background-color:#D6DFF7;"><span style="color:black">Value</span></th>
    		              	<th style="width:32%; background-color:#D6DFF7;"><span style="color:black">Gauge</span></th>
    		              	<th style="width:20%; background-color:#D6DFF7;padding-left: 10px;"><span style="color:black">Name</span></th>
    		              	<th style="width:7%; background-color:#D6DFF7;"><span style="color:black">Adjust</span></th>
    		              	<th style="width:3%; background-color:#D6DFF7;"><span style="color:black">Precision</span></th>
    		            </thead>
    		            <tbody>
    		              <tr>
    		              	<td><a href="voltmeters_setup.php?vmsetup=1" onMouseOver="Tip('<?php echo $v1_notes; ?>',TITLE,'Voltmeter 1 - <?php echo $v1_name; ?>',TITLEBGCOLOR,'#000000',BGCOLOR,'#ffff33');" onMouseOut="UnTip();"><span style="color:blue;">Voltmeter</span></a></td>
    		              	<td><a href="voltmeters_setup.php?vmsetup=1" onMouseOver="Tip('<?php echo $v1_notes; ?>',TITLE,'Voltmeter 1 - <?php echo $v1_name; ?>',TITLEBGCOLOR,'#000000',BGCOLOR,'#ffff33');" onMouseOut="UnTip();"><span style="color:blue;">1</span></a></td>
    		              	<?php
    		              		if($v1_pol == "BOTH"){echo "<td><span>+/- 100</span></td>";}
    		              		if($v1_pol == "POS"){echo "<td><span>0&nbsp;&nbsp;to +100</span></td>";}
    		              		if($v1_pol == "NEG"){echo "<td><span>0&nbsp;&nbsp;to&nbsp;&nbsp;-100</span></td>";}
    		              		if($v1units_override == "CHECKED")
													{
														echo "<td><span>".$v1units_name."</span></td>";
													}
													else
													{
														if($v1_mode == "v"){echo "<td><span>VDC</span></td>";}
														if($v1_mode == "a")
														{
															if($v1_watt == "CHECKED"){echo "<td><span>WATTS</span></td>";}
															else{echo "<td><span>AMPS</span></td>";}
														} 
													}                  	
    		              	?>
    		                <td><div id='vm1'>0.000</div></td>
    		                <td>
    		                	<div id='progressbar1'></div>
    		                </td>
    		                <td	style="padding-left: 10px;"><a href="voltmeters_setup.php?vmsetup=1" onMouseOver="Tip('<?php echo $v1_notes; ?>',TITLE,'Voltmeter 1 - <?php echo $v1_name; ?>',TITLEBGCOLOR,'#000000',BGCOLOR,'#ffff33');" onMouseOut="UnTip();"><span style="color:blue;"><?php echo $v1_name; ?></span></a></td>
    		                <td><span><?php echo $v1_vmadj; ?></span></td>
    		                <td style="text-align:center;"><span><?php echo $v1_per; ?></span></td>
    		              </tr>
    		              <tr>
    		              	<td><a href="voltmeters_setup.php?vmsetup=2" onMouseOver="Tip('<?php echo $v2_notes; ?>',TITLE,'Voltmeter 2 - <?php echo $v2_name; ?>',TITLEBGCOLOR,'#000000',BGCOLOR,'#ffff33');" onMouseOut="UnTip();"><span style="color:blue;">Voltmeter</span></a></td>
    		              	<td><a href="voltmeters_setup.php?vmsetup=2" onMouseOver="Tip('<?php echo $v2_notes; ?>',TITLE,'Voltmeter 2 - <?php echo $v2_name; ?>',TITLEBGCOLOR,'#000000',BGCOLOR,'#ffff33');" onMouseOut="UnTip();"><span style="color:blue;">2</span></a></td>
    		                <?php
    		              		if($v2_pol == "BOTH"){echo "<td><span>+/- 100</span></td>";}
    		              		if($v2_pol == "POS"){echo "<td><span>0&nbsp;&nbsp;to +100</span></td>";}
    		              		if($v2_pol == "NEG"){echo "<td><span>0&nbsp;&nbsp;to&nbsp;&nbsp;-100</span></td>";}
    		              		if($v2units_override == "CHECKED")
													{
														echo "<td><span>".$v2units_name."</span></td>";
													}
													else
													{
														if($v2_mode == "v"){echo "<td><span>VDC</span></td>";}
														if($v2_mode == "a")
														{
															if($v2_watt == "CHECKED"){echo "<td><span>WATTS</span></td>";}
															else{echo "<td><span>AMPS</span></td>";}
														} 
													}
    		              	?>
    		                <td><div id='vm2'>0.000</div></td>
    		                <td>
    		                	<div id='progressbar2'></div>
    		                </td>
    		                <td	style="padding-left: 10px;"><a href="voltmeters_setup.php?vmsetup=2" onMouseOver="Tip('<?php echo $v2_notes; ?>',TITLE,'Voltmeter 2 - <?php echo $v2_name; ?>',TITLEBGCOLOR,'#000000',BGCOLOR,'#ffff33');" onMouseOut="UnTip();"><span style="color:blue;"><?php echo $v2_name; ?></span></a></td>
    		                <td><span><?php echo $v2_vmadj; ?></span></td>
    		                <td style="text-align:center;"><span><?php echo $v2_per; ?></span></td>
    		              </tr>
    		              <tr>
    		              	<td><a href="voltmeters_setup.php?vmsetup=3" onMouseOver="Tip('<?php echo $v3_notes; ?>',TITLE,'Voltmeter 3 - <?php echo $v3_name; ?>',TITLEBGCOLOR,'#000000',BGCOLOR,'#ffff33');" onMouseOut="UnTip();"><span style="color:blue;">Voltmeter</span></a></td>
    		              	<td><a href="voltmeters_setup.php?vmsetup=3" onMouseOver="Tip('<?php echo $v3_notes; ?>',TITLE,'Voltmeter 3 - <?php echo $v3_name; ?>',TITLEBGCOLOR,'#000000',BGCOLOR,'#ffff33');" onMouseOut="UnTip();"><span style="color:blue;">3</span></a></td>
    		                <?php
    		              		if($v3_pol == "BOTH"){echo "<td><span>+/- 100</span></td>";}
    		              		if($v3_pol == "POS"){echo "<td><span>0&nbsp;&nbsp;to +100</span></td>";}
    		              		if($v3_pol == "NEG"){echo "<td><span>0&nbsp;&nbsp;to&nbsp;&nbsp;-100</span></td>";}
    		              		if($v3units_override == "CHECKED")
													{
														echo "<td><span>".$v3units_name."</span></td>";
													}
													else
													{
														if($v3_mode == "v"){echo "<td><span>VDC</span></td>";}
														if($v3_mode == "a")
														{
															if($v3_watt == "CHECKED"){echo "<td><span>WATTS</span></td>";}
															else{echo "<td><span>AMPS</span></td>";}
														} 
													}
    		              	?>
    		                <td><div id='vm3'>0.000</div></td>
    		                <td>
    		                	<div id='progressbar3'></div>
    		                </td>
    		                <td	style="padding-left: 10px;"><a href="voltmeters_setup.php?vmsetup=3" onMouseOver="Tip('<?php echo $v3_notes; ?>',TITLE,'Voltmeter 3 - <?php echo $v3_name; ?>',TITLEBGCOLOR,'#000000',BGCOLOR,'#ffff33');" onMouseOut="UnTip();"><span style="color:blue;"><?php echo $v3_name; ?></span></a></td>
    		                <td><span><?php echo $v3_vmadj; ?></span></td>
    		                <td style="text-align:center;"><span><?php echo $v3_per; ?></span></td>
    		              </tr>
    		            </tbody>  
    		          </table>
								</div>	<!-- END TABLE RESPONSIVE -->	
    		    		<br>
    		    		<legend>Voltmeter Graphs</legend>
    		    		<div class="form-group">
		        			<div class="col-sm-2">
		        				<button name="graphs" class="btn btn-primary" type="submit" onMouseOver="mouse_move(&#039;sd_voltmeter-graphs&#039;);" onMouseOut="mouse_move();"><i class="fa fa-line-chart" ></i> Voltmeter Graphs</button>	
		        			</div>
		        		</div>
    		    		<br>
    		    		<legend>Voltmeter Polling Speed Delay</legend>
    		    		<div class="form-group">
    		    			<div class="col-sm-5" style="max-width:140px; margin-top:15px">
    		    				<select class="form-control input-sm" name="polling_speed_value" >
											<option value="0">No Delay</option>
											<?php
											$ii=1;	if($polling_speed_value==$ii) {$chan=sprintf("selected");} else {$chan=sprintf(" ");} echo"<option ".$chan." value=".$ii.">".$ii." Second</option>";	
											for($ii=2; $ii<60; $ii++)	{	if($polling_speed_value==$ii) {$chan = "selected";} else {$chan = " ";} echo"<option " . $chan . " value='" . $ii . "'>" . $ii . " Seconds</option>";	}
											$ii=1; if($polling_speed_value==($ii*60)) {$chan = "selected";} else {$chan = " ";} echo "<option " . $chan . " value='" . $ii*60 . "'>" . $ii . " Minute</option>";
											for($ii=2; $ii<60; $ii++)	{	if($polling_speed_value==($ii*60)) {$chan = "selected";} else {$chan = " ";} echo "<option " . $chan . " value='" . $ii*60 . "'>" . $ii . " Minutes</option>";	}
											$ii=1;	if($polling_speed_value==($ii*60*60)) {$chan = "selected";} else {$chan = " ";} echo "<option " . $chan . " value='" . $ii*60*60 . "'>" . $ii . " Hour</option>";
											for($ii=2; $ii<25; $ii++)	{ if($polling_speed_value==($ii*60*60)) {$chan = "selected";} else {$chan = " ";} echo "<option " . $chan . " value='" . $ii*60*60 . "'>" . $ii . " Hours</option>";	}
											?>
		    				   	</select>
		    				  </div>
		        			<div class="col-sm-2">
		        				<button name="polling_speed" class="btn btn-success" style="margin-top:15px" type="submit" onMouseOver="mouse_move(&#039;sd_voltmeter_polling&#039;);" onMouseOut="mouse_move();"><i class="fa fa-clock-o" ></i> SET</button>	
		        			</div>
		        		</div>
    		    		<br>
    		    		<legend>Voltmeter Setup</legend>
    		    		<div class="table-responsive">
    		        	<table width="100%">
    		          	<thead>
    		            	<tr>
    		    						<th width='10%' style='text-align:center; background-color:#D6DFF7;'>Voltmeter 1</th><th width='10%' style='text-align:center; background-color:#D6DFF7;'>Voltmeter 2</th><th width='10%' style='text-align:center; background-color:#D6DFF7;'>Voltmeter 3</th>
											</tr>
    		    				</thead>
    		            <tbody>
    		    					<tr>
    		    						<td style="text-align:center;">
    		    							<a href="voltmeters_setup.php?vmsetup=1"><img src="images/vm.jpg" onMouseOver="Tip('<?php echo $v1_notes; ?>',TITLE,'Voltmeter 1 - <?php echo $v1_name; ?>',TITLEBGCOLOR,'#000000',BGCOLOR,'#ffff33');" onMouseOut="UnTip();"></a>
												</td>
												
												<td style="text-align:center;">
    		    							<a href="voltmeters_setup.php?vmsetup=2"><img src="images/vm.jpg" onMouseOver="Tip('<?php echo $v2_notes; ?>',TITLE,'Voltmeter 2 - <?php echo $v2_name; ?>',TITLEBGCOLOR,'#000000',BGCOLOR,'#ffff33');" onMouseOut="UnTip();"></a>
												</td>
												
												<td style="text-align:center;">
    		    							<a href="voltmeters_setup.php?vmsetup=3"><img src="images/vm.jpg" onMouseOver="Tip('<?php echo $v3_notes; ?>',TITLE,'Voltmeter 3 - <?php echo $v3_name; ?>',TITLEBGCOLOR,'#000000',BGCOLOR,'#ffff33');" onMouseOut="UnTip();"></a>
												</td>
    		    					</tr>
    		    					<tr>
    		    						<td style="text-align:center;color:blue"><a href="voltmeters_setup.php?vmsetup=1" onMouseOver="Tip('<?php echo $v1_notes; ?>',TITLE,'Voltmeter 1 - <?php echo $v1_name; ?>',TITLEBGCOLOR,'#000000',BGCOLOR,'#ffff33');" onMouseOut="UnTip();"><?php echo $v1_name; ?></a></td>
    		    						<td style="text-align:center;color:blue"><a href="voltmeters_setup.php?vmsetup=2" onMouseOver="Tip('<?php echo $v2_notes; ?>',TITLE,'Voltmeter 2 - <?php echo $v2_name; ?>',TITLEBGCOLOR,'#000000',BGCOLOR,'#ffff33');" onMouseOut="UnTip();"><?php echo $v2_name; ?></a></td>
    		    						<td style="text-align:center;color:blue"><a href="voltmeters_setup.php?vmsetup=3" onMouseOver="Tip('<?php echo $v3_notes; ?>',TITLE,'Voltmeter 3 - <?php echo $v3_name; ?>',TITLEBGCOLOR,'#000000',BGCOLOR,'#ffff33');" onMouseOut="UnTip();"><?php echo $v3_name; ?></a></td>
    		    					</tr>
    		    				</tbody>
    		    			</table>
    		    		</div>
    		    		<br>
    		    		<div class="form-group"></div>
								
								<legend>Relay Scripts</legend> 
								
								<div class="row">
    							<div class="col-sm-12">
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
															echo " 		<a href='voltmeters.php?action=run&id=".$sid."' onMouseOver ='mouse_move(\"b_relays_execrelaycript\");'	onMouseOut='mouse_move();'>";
															echo "		<img src='images/on.gif' width='16' height='16' title='EXECUTE SCRIPT'></a>";
															echo "	</td>";
															echo "</tr>";
														}
    											?>
    										</tbody>
  										</table>      	    
    		  					</div> <!-- END TABLE RESPONSIVE -->		
    		  				</div> <!-- END COL-SM-12 --> 
    						</div> <!-- END ROW -->	
    		    	</div>	<!-- END PANEL BODY -->	
    		  	</div>	<!-- END HPANEL -->
    			</div>	<!-- END COL-SM-12 -->
    		</div> <!-- END VOLTMETER BLOCK -->		
    	</fieldset>
    </form>			
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
	echo"		window.location.href = 'voltmeters.php?confirm=run&id=" . $id . "';";
	echo"	});";
	echo"</script>";
}



echo "</body>";
echo "</html>";









?>









 
















