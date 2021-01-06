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
	
	if(!file_exists("/var/run/rmsvmd.pid"))
	{
		exec("/etc/init.scripts/S98rmsvmd start > /dev/null");
		sleep(1);
	}
	
	$vdb_pid = trim(shell_exec('rmsvdb_id pid'));
	$vdb_dev = trim(shell_exec('rmsvdb_id dev'));

//	$pageWasRefreshed = isset($_SERVER['HTTP_CACHE_CONTROL']) && $_SERVER['HTTP_CACHE_CONTROL'] === 'max-age=0';
//	if($pageWasRefreshed ) 
//	{
//   	//page was refreshed;
//   	goto escape_hatch;
//	} 
	
	
	$result  = $dbh->query("SELECT * FROM iso_vm_polarity WHERE id='1';");			
	foreach($result as $row)
		{
			$v1_pol = $row['polarity'];
		}
	
	$result  = $dbh->query("SELECT * FROM iso_vm_polarity WHERE id='2';");			
	foreach($result as $row)
		{
			$v2_pol = $row['polarity'];
		}
	
	$result  = $dbh->query("SELECT * FROM iso_vm_polarity WHERE id='3';");			
	foreach($result as $row)
		{
			$v3_pol = $row['polarity'];
		}
	
	$result  = $dbh->query("SELECT * FROM iso_vm_polarity WHERE id='4';");			
	foreach($result as $row)
		{
			$v4_pol = $row['polarity'];
		}
	
	$result  = $dbh->query("SELECT * FROM iso_vm_polarity WHERE id='5';");			
	foreach($result as $row)
		{
			$v5_pol = $row['polarity'];
		}
	
	$result  = $dbh->query("SELECT * FROM iso_vm_polarity WHERE id='6';");			
	foreach($result as $row)
		{
			$v6_pol = $row['polarity'];
		}
		
	$result  = $dbh->query("SELECT * FROM iso_voltmeters WHERE id='1';");			
	foreach($result as $row)
		{
			$v1_name = $row['name'];
			$v1_notes = $row['notes'];
			$v1_mode = $row['mode'];
			$v1_vmadj = $row['vmadj'];
			$v1_per = $row['per'];
			$v1_watt = $row['watt_mode_enabled'];
		}
	
	$result  = $dbh->query("SELECT * FROM iso_voltmeters WHERE id='2';");			
	foreach($result as $row)
		{
			$v2_name = $row['name'];
			$v2_notes = $row['notes'];
			$v2_mode = $row['mode'];
			$v2_vmadj = $row['vmadj'];
			$v2_per = $row['per'];
			$v2_watt = $row['watt_mode_enabled'];
		}
	
	$result  = $dbh->query("SELECT * FROM iso_voltmeters WHERE id='3';");			
	foreach($result as $row)
		{
			$v3_name = $row['name'];
			$v3_notes = $row['notes'];
			$v3_mode = $row['mode'];
			$v3_vmadj = $row['vmadj'];
			$v3_per = $row['per'];
			$v3_watt = $row['watt_mode_enabled'];
		}
	
	$result  = $dbh->query("SELECT * FROM iso_voltmeters WHERE id='4';");			
	foreach($result as $row)
		{
			$v4_name = $row['name'];
			$v4_notes = $row['notes'];
			$v4_mode = $row['mode'];
			$v4_vmadj = $row['vmadj'];
			$v4_per = $row['per'];
			$v4_watt = $row['watt_mode_enabled'];
		}
	
	$result  = $dbh->query("SELECT * FROM iso_voltmeters WHERE id='5';");			
	foreach($result as $row)
		{
			$v5_name = $row['name'];
			$v5_notes = $row['notes'];
			$v5_mode = $row['mode'];
			$v5_vmadj = $row['vmadj'];
			$v5_per = $row['per'];
			$v5_watt = $row['watt_mode_enabled'];
		}
	
	$result  = $dbh->query("SELECT * FROM iso_voltmeters WHERE id='6';");			
	foreach($result as $row)
		{
			$v6_name = $row['name'];
			$v6_notes = $row['notes'];
			$v6_mode = $row['mode'];
			$v6_vmadj = $row['vmadj'];
			$v6_per = $row['per'];
			$v6_watt = $row['watt_mode_enabled'];
		}
	
	$result  = $dbh->query("SELECT * FROM iso_global_graph_opts;");			
	foreach($result as $row)
		{
			$timespan_view = $row['timespan_view'];
		}
	
	
//	$result  = $dbh->query("SELECT * FROM vm_polling_speed;");			
//	foreach($result as $row)
//		{
//			$polling_speed_value = $row['polling_speed_value'];
//		}

		
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

if(isset ($_GET['confirm']))
{
	$action = $_GET['confirm'];
	if($action == "reset")
	{
		$the_date = strftime( "%b-%d-%Y");
		exec("/etc/init.scripts/S98rmsvmd stop > /dev/null");
		sleep(2);
		$file_buff = sprintf("mv /data/rrd/tmp/usbvb.rrd /data/rrd/usbvb.rrd.old-%s",$the_date);
		exec($file_buff);
		exec("rm /data/rrd/usbvb.rrd");
		exec("rm /data/rrd/tmp/usbvb.rrd");
		exec("rm /data/rrd/tmp/usb-vm1*");
		exec("rm /data/rrd/tmp/usb-vm2*");
		exec("rm /data/rrd/tmp/usb-vm3*");
		exec("rm /data/rrd/tmp/usb-vm4*");
		exec("rm /data/rrd/tmp/usb-vm5*");
		exec("rm /data/rrd/tmp/usb-vm6*");
		exec("/etc/init.scripts/S98rmsvmd start > /dev/null");
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

if(isset ($_POST['graphs']))
	{
		header("Location: usb-vm-graph.php");
	}

//if(isset ($_POST['polling_speed']))
//	{
//		$polling_speed_value = $_POST['polling_speed_value'];
//		$query = sprintf("UPDATE vm_polling_speed SET polling_speed_value='%s';",$polling_speed_value);
//		$result  = $dbh->exec($query); 
//		restart_some_services();
//		
//		$alert_flag = "1";
//	}


// RESET Button	was clicked
if(isset ($_POST['graphs_reset']))
{	
	$alert_flag = "3";
}

// SET Button	was clicked
if(isset ($_POST['graphs_view']))
{	
	$timespan_view = $_POST['timespan_view'];
//	$graph_width = $_POST['graph_width'];
//	$graph_height = $_POST['graph_height'];
	$query = sprintf("UPDATE iso_global_graph_opts SET timespan_view='%s';", $timespan_view);
	$result  = $dbh->exec($query);
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
			SetContext('vdb');
		</script>
		
		<script>
			
			function display_vm ()
			{
        var myRandom = parseInt(Math.random()*999999999);
        $.getJSON('iso_vm_server.php?element=vmall&rand=' + myRandom,
                function(data)
                {
                       $.each (data.vms, function (k, v) { $('#' + k).text (v); });
                       setTimeout (display_vm, 1000);

                       if (parseFloat(data.vms.vm1) > 0)
                        {
                         $('#progressbar1').progressbar({ value: + data.vms.vm1 });
                         $('#progressbar1 > div').css({ 'background': '#00FF00' });
                         $('#progressbar1 > div').css({ 'border-color': '#00FF00' });
                       }
                       else
                        {
                        	data.vms.vm1 = Math.abs(data.vms.vm1);
                        	$('#progressbar1').progressbar({ value: + data.vms.vm1 });
                        	$('#progressbar1 > div').css({ 'background': 'Red' });
                        	$('#progressbar1 > div').css({ 'border-color': 'Red' });
                        }
		
		                   if (parseFloat(data.vms.vm2) > 0)
                        {
                         $('#progressbar2').progressbar({ value: + data.vms.vm2 });
                         $('#progressbar2 > div').css({ 'background': '#00FF00' });
                         $('#progressbar2 > div').css({ 'border-color': '#00FF00' });
                       }
                       else
                        {
                        	data.vms.vm2 = Math.abs(data.vms.vm2);
                        	$('#progressbar2').progressbar({ value: + data.vms.vm2 });
                        	$('#progressbar2 > div').css({ 'background': 'Red' });
                        	$('#progressbar2 > div').css({ 'border-color': 'Red' });
                        }
		
		                   if (parseFloat(data.vms.vm3) > 0)
                        {
                         $('#progressbar3').progressbar({ value: + data.vms.vm3 });
                         $('#progressbar3 > div').css({ 'background': '#00FF00' });
                         $('#progressbar3 > div').css({ 'border-color': '#00FF00' });
                       }
                       else
                        {
                        	data.vms.vm3 = Math.abs(data.vms.vm3);
                        	$('#progressbar3').progressbar({ value: + data.vms.vm3 });
                        	$('#progressbar3 > div').css({ 'background': 'Red' });
                        	$('#progressbar3 > div').css({ 'border-color': 'Red' });
                        }
		
		                   if (parseFloat(data.vms.vm4) > 0)
                        {
                         $('#progressbar4').progressbar({ value: + data.vms.vm4 });
                         $('#progressbar4 > div').css({ 'background': '#00FF00' });
                         $('#progressbar4 > div').css({ 'border-color': '#00FF00' });
                       }
                       else
                        {
                        	data.vms.vm4 = Math.abs(data.vms.vm4);
                        	$('#progressbar4').progressbar({ value: + data.vms.vm4 });
                        	$('#progressbar4 > div').css({ 'background': 'Red' });
                        	$('#progressbar4 > div').css({ 'border-color': 'Red' });
                        }
		
		                   if (parseFloat(data.vms.vm5) > 0)
                        {
                         $('#progressbar5').progressbar({ value: + data.vms.vm5 });
                         $('#progressbar5 > div').css({ 'background': '#00FF00' });
                         $('#progressbar5 > div').css({ 'border-color': '#00FF00' });
                       }
                       else
                        {
                        	data.vms.vm5 = Math.abs(data.vms.vm5);
                        	$('#progressbar5').progressbar({ value: + data.vms.vm5 });
                        	$('#progressbar5 > div').css({ 'background': 'Red' });
                        	$('#progressbar5 > div').css({ 'border-color': 'Red' });
                        }
		
		                   if (parseFloat(data.vms.vm6) > 0)
                        {
                         $('#progressbar6').progressbar({ value: + data.vms.vm6 });
                         $('#progressbar6 > div').css({ 'background': '#00FF00' });
                         $('#progressbar6 > div').css({ 'border-color': '#00FF00' });
                       }
                       else
                        {
                        	data.vms.vm6 = Math.abs(data.vms.vm6);
                        	$('#progressbar6').progressbar({ value: + data.vms.vm6 });
                        	$('#progressbar6 > div').css({ 'background': 'Red' });
                        	$('#progressbar6 > div').css({ 'border-color': 'Red' });
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

<?php left_nav("vdb"); ?>
<script language="javascript" type="text/javascript">
	SetContext('vdb');
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
		<form name='USB Voltmeters' action='device_vdb.php' method='post' class="form-horizontal">  	
    	<fieldset>
  			
  			
  			<!-- VOLTMETER BLOCK START -->    
    		<div class="row top-buffer">
    			<div class="col-sm-12">
    		  	<div class="hpanel">
    		      <div class="panel-body">
    		      	<legend>USB Voltmeter Board Overview</legend>
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
    		              	<td><a href="setup_voltmeter_board.php?vmsetup=1" onMouseOver="Tip('<?php echo $v1_notes; ?>',TITLE,'Voltmeter 1 - <?php echo $v1_name; ?>',TITLEBGCOLOR,'#000000',BGCOLOR,'#ffff33');" onMouseOut="UnTip();"><span style="color:blue;">Voltmeter</span></a></td>
    		              	<td><a href="setup_voltmeter_board?vmsetup=1" onMouseOver="Tip('<?php echo $v1_notes; ?>',TITLE,'Voltmeter 1 - <?php echo $v1_name; ?>',TITLEBGCOLOR,'#000000',BGCOLOR,'#ffff33');" onMouseOut="UnTip();"><span style="color:blue;">1</span></a></td>
    		              	<?php
    		              		if($v1_pol == "BOTH"){echo "<td><span>+/- 100</span></td>";}
    		              		if($v1_pol == "POS"){echo "<td><span>0&nbsp;&nbsp;to +100</span></td>";}
    		              		if($v1_pol == "NEG"){echo "<td><span>0&nbsp;&nbsp;to&nbsp;&nbsp;-100</span></td>";}
    		              	
													if($v1_mode == "v"){echo "<td><span>VDC</span></td>";}
													if($v1_mode == "a")
														{
															if($v1_watt == "CHECKED"){echo "<td><span>WATTS</span></td>";}
															else{echo "<td><span>AMPS</span></td>";}
														}                  	
    		              	?>
    		                <td><div id='vm1'>0.000</div></td>
    		                <td>
    		                	<div id='progressbar1'></div>
    		                </td>
    		                <td	style="padding-left: 10px;"><a href="setup_voltmeter_board.php?vmsetup=1" onMouseOver="Tip('<?php echo $v1_notes; ?>',TITLE,'Voltmeter 1 - <?php echo $v1_name; ?>',TITLEBGCOLOR,'#000000',BGCOLOR,'#ffff33');" onMouseOut="UnTip();"><span style="color:blue;"><?php echo $v1_name; ?></span></a></td>
    		                <td><span><?php echo $v1_vmadj; ?></span></td>
    		                <td style="text-align:center;"><span><?php echo $v1_per; ?></span></td>
    		              </tr>
    		              <tr>
    		              	<td><a href="setup_voltmeter_board.php?vmsetup=2" onMouseOver="Tip('<?php echo $v2_notes; ?>',TITLE,'Voltmeter 2 - <?php echo $v2_name; ?>',TITLEBGCOLOR,'#000000',BGCOLOR,'#ffff33');" onMouseOut="UnTip();"><span style="color:blue;">Voltmeter</span></a></td>
    		              	<td><a href="setup_voltmeter_board.php?vmsetup=2" onMouseOver="Tip('<?php echo $v2_notes; ?>',TITLE,'Voltmeter 2 - <?php echo $v2_name; ?>',TITLEBGCOLOR,'#000000',BGCOLOR,'#ffff33');" onMouseOut="UnTip();"><span style="color:blue;">2</span></a></td>
    		                <?php
    		              		if($v2_pol == "BOTH"){echo "<td><span>+/- 100</span></td>";}
    		              		if($v2_pol == "POS"){echo "<td><span>0&nbsp;&nbsp;to +100</span></td>";}
    		              		if($v2_pol == "NEG"){echo "<td><span>0&nbsp;&nbsp;to&nbsp;&nbsp;-100</span></td>";}
    		              		if($v2_mode == "v"){echo "<td><span>VDC</span></td>";}
													if($v2_mode == "a")
														{
															if($v2_watt == "CHECKED"){echo "<td><span>WATTS</span></td>";}
															else{echo "<td><span>AMPS</span></td>";}
														}
    		              	?>
    		                <td><div id='vm2'>0.000</div></td>
    		                <td>
    		                	<div id='progressbar2'></div>
    		                </td>
    		                <td	style="padding-left: 10px;"><a href="setup_voltmeter_board.php?vmsetup=2" onMouseOver="Tip('<?php echo $v2_notes; ?>',TITLE,'Voltmeter 2 - <?php echo $v2_name; ?>',TITLEBGCOLOR,'#000000',BGCOLOR,'#ffff33');" onMouseOut="UnTip();"><span style="color:blue;"><?php echo $v2_name; ?></span></a></td>
    		                <td><span><?php echo $v2_vmadj; ?></span></td>
    		                <td style="text-align:center;"><span><?php echo $v2_per; ?></span></td>
    		              </tr>
    		              <tr>
    		              	<td><a href="setup_voltmeter_board.php?vmsetup=3" onMouseOver="Tip('<?php echo $v3_notes; ?>',TITLE,'Voltmeter 3 - <?php echo $v3_name; ?>',TITLEBGCOLOR,'#000000',BGCOLOR,'#ffff33');" onMouseOut="UnTip();"><span style="color:blue;">Voltmeter</span></a></td>
    		              	<td><a href="setup_voltmeter_board.php?vmsetup=3" onMouseOver="Tip('<?php echo $v3_notes; ?>',TITLE,'Voltmeter 3 - <?php echo $v3_name; ?>',TITLEBGCOLOR,'#000000',BGCOLOR,'#ffff33');" onMouseOut="UnTip();"><span style="color:blue;">3</span></a></td>
    		                <?php
    		              		if($v3_pol == "BOTH"){echo "<td><span>+/- 100</span></td>";}
    		              		if($v3_pol == "POS"){echo "<td><span>0&nbsp;&nbsp;to +100</span></td>";}
    		              		if($v3_pol == "NEG"){echo "<td><span>0&nbsp;&nbsp;to&nbsp;&nbsp;-100</span></td>";}
    		              		if($v3_mode == "v"){echo "<td><span>VDC</span></td>";}
													if($v3_mode == "a")
														{
															if($v3_watt == "CHECKED"){echo "<td><span>WATTS</span></td>";}
															else{echo "<td><span>AMPS</span></td>";}
														}
    		              	?>
    		                <td><div id='vm3'>0.000</div></td>
    		                <td>
    		                	<div id='progressbar3'></div>
    		                </td>
    		                <td	style="padding-left: 10px;"><a href="setup_voltmeter_board.php?vmsetup=3" onMouseOver="Tip('<?php echo $v3_notes; ?>',TITLE,'Voltmeter 3 - <?php echo $v3_name; ?>',TITLEBGCOLOR,'#000000',BGCOLOR,'#ffff33');" onMouseOut="UnTip();"><span style="color:blue;"><?php echo $v3_name; ?></span></a></td>
    		                <td><span><?php echo $v3_vmadj; ?></span></td>
    		                <td style="text-align:center;"><span><?php echo $v3_per; ?></span></td>
    		              </tr>
    		              <tr>
    		              	<td><a href="setup_voltmeter_board.php?vmsetup=4" onMouseOver="Tip('<?php echo $v4_notes; ?>',TITLE,'Voltmeter 4 - <?php echo $v4_name; ?>',TITLEBGCOLOR,'#000000',BGCOLOR,'#ffff33');" onMouseOut="UnTip();"><span style="color:blue;">Voltmeter</span></a></td>
    		              	<td><a href="setup_voltmeter_board.php?vmsetup=4" onMouseOver="Tip('<?php echo $v4_notes; ?>',TITLE,'Voltmeter 4 - <?php echo $v4_name; ?>',TITLEBGCOLOR,'#000000',BGCOLOR,'#ffff33');" onMouseOut="UnTip();"><span style="color:blue;">4</span></a></td>
    		                <?php
    		              		if($v4_pol == "BOTH"){echo "<td><span>+/- 100</span></td>";}
    		              		if($v4_pol == "POS"){echo "<td><span>0&nbsp;&nbsp;to +100</span></td>";}
    		              		if($v4_pol == "NEG"){echo "<td><span>0&nbsp;&nbsp;to&nbsp;&nbsp;-100</span></td>";}
    		              		if($v4_mode == "v"){echo "<td><span>VDC</span></td>";}
													if($v4_mode == "a")
														{
															if($v4_watt == "CHECKED"){echo "<td><span>WATTS</span></td>";}
															else{echo "<td><span>AMPS</span></td>";}
														}
    		              	?>
    		                <td><div id='vm4'>0.000</div></td>
    		                <td>
    		                	<div id='progressbar4'></div>
    		                </td>
    		                <td	style="padding-left: 10px;"><a href="setup_voltmeter_board.php?vmsetup=4" onMouseOver="Tip('<?php echo $v4_notes; ?>',TITLE,'Voltmeter 4 - <?php echo $v4_name; ?>',TITLEBGCOLOR,'#000000',BGCOLOR,'#ffff33');" onMouseOut="UnTip();"><span style="color:blue;"><?php echo $v4_name; ?></span></a></td>
    		                <td><span><?php echo $v4_vmadj; ?></span></td>
    		                <td style="text-align:center;"><span><?php echo $v4_per; ?></span></td>
    		              </tr>
    		              <tr>
    		              	<td><a href="setup_voltmeter_board.php?vmsetup=5" onMouseOver="Tip('<?php echo $v5_notes; ?>',TITLE,'Voltmeter 5 - <?php echo $v5_name; ?>',TITLEBGCOLOR,'#000000',BGCOLOR,'#ffff33');" onMouseOut="UnTip();"><span style="color:blue;">Voltmeter</span></a></td>
    		              	<td><a href="setup_voltmeter_board.php?vmsetup=5" onMouseOver="Tip('<?php echo $v5_notes; ?>',TITLE,'Voltmeter 5 - <?php echo $v5_name; ?>',TITLEBGCOLOR,'#000000',BGCOLOR,'#ffff33');" onMouseOut="UnTip();"><span style="color:blue;">5</span></a></td>
    		                <?php
    		              		if($v5_pol == "BOTH"){echo "<td><span>+/- 100</span></td>";}
    		              		if($v5_pol == "POS"){echo "<td><span>0&nbsp;&nbsp;to +100</span></td>";}
    		              		if($v5_pol == "NEG"){echo "<td><span>0&nbsp;&nbsp;to&nbsp;&nbsp;-100</span></td>";}
    		              		if($v5_mode == "v"){echo "<td><span>VDC</span></td>";}
													if($v5_mode == "a")
														{
															if($v5_watt == "CHECKED"){echo "<td><span>WATTS</span></td>";}
															else{echo "<td><span>AMPS</span></td>";}
														}
    		              	?>
    		                <td><div id='vm5'>0.000</div></td>
    		                <td>
    		                	<div id='progressbar5'></div>
    		                </td>
    		                <td	style="padding-left: 10px;"><a href="setup_voltmeter_board.php?vmsetup=5" onMouseOver="Tip('<?php echo $v5_notes; ?>',TITLE,'Voltmeter 5 - <?php echo $v5_name; ?>',TITLEBGCOLOR,'#000000',BGCOLOR,'#ffff33');" onMouseOut="UnTip();"><span style="color:blue;"><?php echo $v5_name; ?></span></a></td>
    		                <td><span><?php echo $v5_vmadj; ?></span></td>
    		                <td style="text-align:center;"><span><?php echo $v5_per; ?></span></td>
    		              </tr>
    		              <tr>
    		              	<td><a href="setup_voltmeter_board.php?vmsetup=6" onMouseOver="Tip('<?php echo $v6_notes; ?>',TITLE,'Voltmeter 6 - <?php echo $v6_name; ?>',TITLEBGCOLOR,'#000000',BGCOLOR,'#ffff33');" onMouseOut="UnTip();"><span style="color:blue;">Voltmeter</span></a></td>
    		              	<td><a href="setup_voltmeter_board.php?vmsetup=6" onMouseOver="Tip('<?php echo $v6_notes; ?>',TITLE,'Voltmeter 6 - <?php echo $v6_name; ?>',TITLEBGCOLOR,'#000000',BGCOLOR,'#ffff33');" onMouseOut="UnTip();"><span style="color:blue;">6</span></a></td>
    		                <?php
    		              		if($v6_pol == "BOTH"){echo "<td><span>+/- 100</span></td>";}
    		              		if($v6_pol == "POS"){echo "<td><span>0&nbsp;&nbsp;to +100</span></td>";}
    		              		if($v6_pol == "NEG"){echo "<td><span>0&nbsp;&nbsp;to&nbsp;&nbsp;-100</span></td>";}
    		              		if($v6_mode == "v"){echo "<td><span>VDC</span></td>";}
													if($v6_mode == "a")
														{
															if($v6_watt == "CHECKED"){echo "<td><span>WATTS</span></td>";}
															else{echo "<td><span>AMPS</span></td>";}
														}
    		              	?>
    		                <td><div id='vm6'>0.000</div></td>
    		                <td>
    		                	<div id='progressbar6'></div>
    		                </td>
    		                <td	style="padding-left: 10px;"><a href="setup_voltmeter_board.php?vmsetup=6" onMouseOver="Tip('<?php echo $v6_notes; ?>',TITLE,'Voltmeter 6 - <?php echo $v6_name; ?>',TITLEBGCOLOR,'#000000',BGCOLOR,'#ffff33');" onMouseOut="UnTip();"><span style="color:blue;"><?php echo $v6_name; ?></span></a></td>
    		                <td><span><?php echo $v6_vmadj; ?></span></td>
    		                <td style="text-align:center;"><span><?php echo $v6_per; ?></span></td>
    		              </tr>
    		            </body>  
    		          </table>
								</div>	<!-- END TABLE RESPONSIVE -->	
    		    		<br>
    		    		<legend>USB Voltmeter Graphs</legend>
    		    		<div class="form-group">
		        			<div class="col-sm-2" style="min-width:200px">
		        				<button name="graphs" class="btn btn-primary" type="submit" onMouseOver="mouse_move(&#039;sd_voltmeter-graphs&#039;);" onMouseOut="mouse_move();"><i class="fa fa-line-chart" ></i> USB Voltmeter Graphs</button>	
		        			</div>
									<div  class="visible-xs"><br></div>
		        			<div class="col-sm-2" style="min-width:150px; max-width:150px">
		        				 <select class="form-control" id="timespan_view" name="timespan_view">
		        				 	
		        				 	<?php
		        				 		if($timespan_view == "hour")
		        				 		{
		        				 			echo '<option value="hour" selected>Hour</option>';
		        				 		}
		        				 		else
		        				 		{
		        				 			echo '<option value="hour">Hour</option>';
		        				 		}
		        				 		
		        				 		if($timespan_view == "day")
		        				 		{
		        				 			echo '<option value="day" selected>Day</option>';
		        				 		}
		        				 		else
		        				 		{
		        				 			echo '<option value="day">Day</option>';
		        				 		}
		        				 		
		        				 		if($timespan_view == "week")
		        				 		{
		        				 			echo '<option value="week" selected>Week</option>';
		        				 		}
		        				 		else
		        				 		{
		        				 			echo '<option value="week">Week</option>';
		        				 		}
		        				 		
		        				 		if($timespan_view == "month")
		        				 		{
		        				 			echo '<option value="month" selected>Month</option>';
		        				 		}
		        				 		else
		        				 		{
		        				 			echo '<option value="month">Month</option>';
		        				 		}
		        				 		
		        				 		if($timespan_view == "year")
		        				 		{
		        				 			echo '<option value="year" selected>Year</option>';
		        				 		}
		        				 		else
		        				 		{
		        				 			echo '<option value="year">Year</option>';
		        				 		}
		        				 		
		        				 		if($timespan_view == "5year")
		        				 		{
		        				 			echo '<option value="5year" selected>5 Year</option>';
		        				 		}
		        				 		else
		        				 		{
		        				 			echo '<option value="5year">5 Year</option>';
		        				 		}
		        				 		
		        				 		if($timespan_view == "10year")
		        				 		{
		        				 			echo '<option value="10year" selected>10 Year</option>';
		        				 		}
		        				 		else
		        				 		{
		        				 			echo '<option value="10year">10 Year</option>';
		        				 		}
		        				 	?>
		        				 	
  									</select>	
		        			</div>
									<div  class="visible-xs"><br></div>
		        			<div class="col-sm-2" style="min-width:150px">
		        				<button name="graphs_view" class="btn btn-success" type="submit" onMouseOver="mouse_move(&#039;sd_default_graph_view&#039;);" onMouseOut="mouse_move();"><i class="fa fa-check" ></i> Set Graph View</button>	
		        			</div>
									<div  class="visible-xs"><br></div>
		        			<div class="col-sm-2" style="min-width:150px">
		        				<button name="graphs_reset" class="btn btn-danger" type="submit" onMouseOver="mouse_move(&#039;voltmeter_graphs_reset&#039;);" onMouseOut="mouse_move();"><i class="fa fa-exclamation" ></i> Reset Graphs</button>	
		        			</div>
		        		</div>
    		    		<br>
    		    		
    		    		<legend>USB Voltmeter Setup</legend>
    		    		<div class="table-responsive">
    		        	<table width="100%">
    		          	<thead>
    		            	<tr>
    		    						<th width='10%' style='text-align:center; background-color:#D6DFF7;'>Voltmeter 1</th><th width='10%' style='text-align:center; background-color:#D6DFF7;'>Voltmeter 2</th><th width='10%' style='text-align:center; background-color:#D6DFF7;'>Voltmeter 3</th>
												<th width='10%' style='text-align:center; background-color:#D6DFF7;'>Voltmeter 4</th><th width='10%' style='text-align:center; background-color:#D6DFF7;'>Voltmeter 5</th><th width='10%' style='text-align:center; background-color:#D6DFF7;'>Voltmeter 6</th>
												
    		    					</tr>
    		    				</thead>
    		            <tbody>
    		    					<tr>
    		    						<td style="text-align:center;">
    		    							<a href="setup_voltmeter_board.php?vmsetup=1"><img src="images/vm.jpg" onMouseOver="Tip('<?php echo $v1_notes; ?>',TITLE,'Voltmeter 1 - <?php echo $v1_name; ?>',TITLEBGCOLOR,'#000000',BGCOLOR,'#ffff33');" onMouseOut="UnTip();"></a>
												</td>
												
												<td style="text-align:center;">
    		    							<a href="setup_voltmeter_board.php?vmsetup=2"><img src="images/vm.jpg" onMouseOver="Tip('<?php echo $v2_notes; ?>',TITLE,'Voltmeter 2 - <?php echo $v2_name; ?>',TITLEBGCOLOR,'#000000',BGCOLOR,'#ffff33');" onMouseOut="UnTip();"></a>
												</td>
												
												<td style="text-align:center;">
    		    							<a href="setup_voltmeter_board.php?vmsetup=3"><img src="images/vm.jpg" onMouseOver="Tip('<?php echo $v3_notes; ?>',TITLE,'Voltmeter 3 - <?php echo $v3_name; ?>',TITLEBGCOLOR,'#000000',BGCOLOR,'#ffff33');" onMouseOut="UnTip();"></a>
												</td>
												
												<td style="text-align:center;">
    		    							<a href="setup_voltmeter_board.php?vmsetup=4"><img src="images/vm.jpg" onMouseOver="Tip('<?php echo $v4_notes; ?>',TITLE,'Voltmeter 4 - <?php echo $v4_name; ?>',TITLEBGCOLOR,'#000000',BGCOLOR,'#ffff33');" onMouseOut="UnTip();"></a>
												</td>
												
												<td style="text-align:center;">
    		    							<a href="setup_voltmeter_board.php?vmsetup=5"><img src="images/vm.jpg" onMouseOver="Tip('<?php echo $v5_notes; ?>',TITLE,'Voltmeter 5 - <?php echo $v5_name; ?>',TITLEBGCOLOR,'#000000',BGCOLOR,'#ffff33');" onMouseOut="UnTip();"></a>
												</td>
												
												<td style="text-align:center;">
    		    							<a href="setup_voltmeter_board.php?vmsetup=6"><img src="images/vm.jpg" onMouseOver="Tip('<?php echo $v6_notes; ?>',TITLE,'Voltmeter 6 - <?php echo $v6_name; ?>',TITLEBGCOLOR,'#000000',BGCOLOR,'#ffff33');" onMouseOut="UnTip();"></a>
												</td>
												
    		    					</tr>
    		    					<tr>
    		    						<td style="text-align:center;color:blue"><a href="setup_voltmeter_board.php?vmsetup=1" onMouseOver="Tip('<?php echo $v1_notes; ?>',TITLE,'Voltmeter 1 - <?php echo $v1_name; ?>',TITLEBGCOLOR,'#000000',BGCOLOR,'#ffff33');" onMouseOut="UnTip();"><?php echo $v1_name; ?></a></td>
    		    						<td style="text-align:center;color:blue"><a href="setup_voltmeter_board.php?vmsetup=2" onMouseOver="Tip('<?php echo $v2_notes; ?>',TITLE,'Voltmeter 2 - <?php echo $v2_name; ?>',TITLEBGCOLOR,'#000000',BGCOLOR,'#ffff33');" onMouseOut="UnTip();"><?php echo $v2_name; ?></a></td>
    		    						<td style="text-align:center;color:blue"><a href="setup_voltmeter_board.php?vmsetup=3" onMouseOver="Tip('<?php echo $v3_notes; ?>',TITLE,'Voltmeter 3 - <?php echo $v3_name; ?>',TITLEBGCOLOR,'#000000',BGCOLOR,'#ffff33');" onMouseOut="UnTip();"><?php echo $v3_name; ?></a></td>
    		    						<td style="text-align:center;color:blue"><a href="setup_voltmeter_board.php?vmsetup=4" onMouseOver="Tip('<?php echo $v4_notes; ?>',TITLE,'Voltmeter 4 - <?php echo $v4_name; ?>',TITLEBGCOLOR,'#000000',BGCOLOR,'#ffff33');" onMouseOut="UnTip();"><?php echo $v4_name; ?></a></td>
    		    						<td style="text-align:center;color:blue"><a href="setup_voltmeter_board.php?vmsetup=5" onMouseOver="Tip('<?php echo $v5_notes; ?>',TITLE,'Voltmeter 5 - <?php echo $v5_name; ?>',TITLEBGCOLOR,'#000000',BGCOLOR,'#ffff33');" onMouseOut="UnTip();"><?php echo $v5_name; ?></a></td>
    		    						<td style="text-align:center;color:blue"><a href="setup_voltmeter_board.php?vmsetup=6" onMouseOver="Tip('<?php echo $v6_notes; ?>',TITLE,'Voltmeter 6 - <?php echo $v6_name; ?>',TITLEBGCOLOR,'#000000',BGCOLOR,'#ffff33');" onMouseOut="UnTip();"><?php echo $v6_name; ?></a></td>
    		    					</tr>
    		    				</tbody>
    		    			</table>
    		    		</div>
    		    		<br>
    		    	</div>	<!-- END PANEL BODY -->	
    		  	</div>	<!-- END HPANEL -->
    			</div>	<!-- END COL-HG-12 -->
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

if($alert_flag == "3")
{
	echo"<script>";
	echo"	swal({";
	echo"		title: 'Reset USB Graph Data<br>Are you really sure?',";
	echo"		type: 'warning',";
	echo"		showCancelButton: true,";
	echo"		html: true,";
	echo"		confirmButtonColor: '#DD6B55',";
	echo"		confirmButtonText: 'Yes, do it!',";
	echo"		closeOnConfirm: false";
	echo"	},";
	echo"	function(){";
	echo"		window.location.href = 'device_vdb.php?confirm=reset';";
	echo"	});";
	echo"</script>";
}


echo "</body>";
echo "</html>";









?>









 
















