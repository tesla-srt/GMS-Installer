<?php
	error_reporting(E_ALL);
	include "lib.php";
	
	$hostname = trim(file_get_contents("/etc/hostname"));
	$mac_address = trim(file_get_contents("/var/macaddress"));
	$ip_address = $_SERVER['SERVER_ADDR'];
	
	$dbh = new PDO('sqlite:/etc/rms100.db');
	
	$result  = $dbh->query("SELECT * FROM update_notice_conf;");	
	
	foreach($result as $row)
		{		
			$confirmation = $row['confirmation'];	
		}

	if($confirmation == "on")
		{
			exec ("curl -m 2 -k -s -o /tmp/fwv 'https://remotemonitoringsystems.ca/rms100/downloads/current_firmware_version' > /dev/null 2>&1 &");
		}
	
	$result  = $dbh->query("SELECT * FROM display_options;");			
	foreach($result as $row)
		{
			$info_block = $row['info_block'];
			$io_block = $row['io_block'];
			$relay_block = $row['relay_block'];
			$vm_block = $row['vm_block'];
			$alarm_block = $row['alarm_block'];
			$screen_animations = $row['screen_animations'];
		}
		
	$result  = $dbh->query("SELECT syslocation FROM snmp_config;");			
	foreach($result as $row)
		{
			$syslocation = $row['syslocation'];
		}
	
	$result  = $dbh->query("SELECT * FROM io WHERE id='1' AND type='alarm';");			
	foreach($result as $row)
		{
			$a1_name = $row['name'];
			$a1_notes = $row['notes'];
			$a1_notes = str_replace(array("\r\n", "\r", "\n"), "<br>", $a1_notes); 
		}
	
	$result  = $dbh->query("SELECT * FROM io WHERE id='2' AND type='alarm';");			
	foreach($result as $row)
		{
			$a2_name = $row['name'];
			$a2_notes = $row['notes'];
			$a2_notes = str_replace(array("\r\n", "\r", "\n"), "<br>", $a2_notes); 
		}
	
	$result  = $dbh->query("SELECT * FROM io WHERE id='3' AND type='alarm';");			
	foreach($result as $row)
		{
			$a3_name = $row['name'];
			$a3_notes = $row['notes'];
			$a3_notes = str_replace(array("\r\n", "\r", "\n"), "<br>", $a3_notes); 
		}
	
	$result  = $dbh->query("SELECT * FROM io WHERE id='4' AND type='alarm';");			
	foreach($result as $row)
		{
			$a4_name = $row['name'];
			$a4_notes = $row['notes'];
			$a4_notes = str_replace(array("\r\n", "\r", "\n"), "<br>", $a4_notes); 
		}
	
	$result  = $dbh->query("SELECT * FROM io WHERE id='5' AND type='alarm';");			
	foreach($result as $row)
		{
			$a5_name = $row['name'];
			$a5_notes = $row['notes'];
			$a5_notes = str_replace(array("\r\n", "\r", "\n"), "<br>", $a5_notes); 
		}
	
	$result  = $dbh->query("SELECT * FROM io WHERE id='1' AND type='gxio';");			
	foreach($result as $row)
		{
			$io1_name = $row['name'];
			$io1_notes = $row['notes'];
			$io1_notes = str_replace(array("\r\n", "\r", "\n"), "<br>", $io1_notes); 
		}
	
	$result  = $dbh->query("SELECT * FROM io WHERE id='2' AND type='gxio';");			
	foreach($result as $row)
		{
			$io2_name = $row['name'];
			$io2_notes = $row['notes'];
			$io2_notes = str_replace(array("\r\n", "\r", "\n"), "<br>", $io2_notes);
		}
	
	$result  = $dbh->query("SELECT * FROM io WHERE id='3' AND type='gxio';");			
	foreach($result as $row)
		{
			$io3_name = $row['name'];
			$io3_notes = $row['notes'];
			$io3_notes = str_replace(array("\r\n", "\r", "\n"), "<br>", $io3_notes);
		}
	
	$result  = $dbh->query("SELECT * FROM io WHERE id='4' AND type='gxio';");			
	foreach($result as $row)
		{
			$io4_name = $row['name'];
			$io4_notes = $row['notes'];
			$io4_notes = str_replace(array("\r\n", "\r", "\n"), "<br>", $io4_notes);
		}
	
	$result  = $dbh->query("SELECT * FROM relays WHERE id='1';");			
	foreach($result as $row)
		{
			$rly1_name = $row['name'];
			$rly1_notes = $row['notes'];
			$rly1_notes = str_replace(array("\r\n", "\r", "\n"), "<br>", $rly1_notes);
			$rly1NC_color = $row['nc_color'];
			$rly1NO_color = $row['no_color'];
		}
	
	$result  = $dbh->query("SELECT * FROM relays WHERE id='2';");			
	foreach($result as $row)
		{
			$rly2_name = $row['name'];
			$rly2_notes = $row['notes'];
			$rly2_notes = str_replace(array("\r\n", "\r", "\n"), "<br>", $rly2_notes);
			$rly2NC_color = $row['nc_color'];
			$rly2NO_color = $row['no_color'];
		}
	
	
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
	
	$result  = $dbh->query("SELECT * FROM throttle;");			
	foreach($result as $row)
	{
		$dt = $row['delay'];
	}
	
	$dbh = NULL;
	
	

?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <!-- Page title -->
    <title><?php echo $hostname; ?></title>
    <link rel="shortcut icon" type="image/ico" href="favicon.ico?<?php echo rand(); ?>" />

    <!-- CSS -->
    <link rel="stylesheet" href="css/jquery-ui.min.css" />
    <link rel="stylesheet" href="css/fontawesome/css/font-awesome.css" />
    <link rel="stylesheet" href="css/animate.css" />
    <link rel="stylesheet" href="css/bootstrap.css" />
    <link rel="stylesheet" href="css/ethertek.css">

    <!-- Java Scripts -->
		<script src="javascript/jquery.min.js"></script>
		<script src="javascript/jquery-ui.min.js"></script>
		<script src="javascript/bootstrap.min.js"></script>
		<script src="javascript/conhelp.js"></script>
		<script src="javascript/ethertek.js"></script>
			
		<script language="javascript" type="text/javascript">
		function display_vm ()
		{
		        var myRandom = parseInt(Math.random()*999999999);
		        $.getJSON('sdserver.php?element=homeall&rand=' + myRandom,
		            function(data)
		            {
		
		                  $.each (data.vms, function (k, v) { $('#' + k).text (v); });
		                  <?php
		                  	echo"setTimeout (display_vm," . $dt . ");";
		                  ?>
		
		                  if (data.vms.a1 == 1)
		                    {
		                     if (data.vms.aHi1 == 'RED')
		                      {
		                      	$('#alarm1').replaceWith("<div id='alarm1'><img src='images/red_att.gif'></div>");
		                      	$('#a1state').replaceWith("<div id='a1state' style='color:red'>" + data.vms.a1hi + "</div>");
		                     	}
		                     else
		                      {
		                      	$('#alarm1').replaceWith("<div id='alarm1'><img src='images/ok.gif'></div>");
		                      	$('#a1state').replaceWith("<div id='a1state' style='color:green'>" + data.vms.a1hi + "</div>");
		                      }
		                    }
		
		                  else
		                    {
		                     if (data.vms.aLi1 == 'RED')
		                      {
		                      	$('#alarm1').replaceWith("<div id='alarm1'><img src='images/red_att.gif'></div>");
		                      	$('#a1state').replaceWith("<div id='a1state' style='color:red'>" + data.vms.a1lo + "</div>");
		                      }
		                     else
		                      {
		                      	$('#alarm1').replaceWith("<div id='alarm1'><img src='images/ok.gif'></div>");
		                      	$('#a1state').replaceWith("<div id='a1state' style='color:green'>" + data.vms.a1lo + "</div>");
		                      }
		                    }
		
		                   if (data.vms.a2 == 1)
		                    {
		                     if (data.vms.aHi2 == 'RED')
		                      {
		                      	$('#alarm2').replaceWith("<div id='alarm2'><img src='images/red_att.gif'></div>");
		                      	$('#a2state').replaceWith("<div id='a2state' style='color:red'>" + data.vms.a2hi + "</div>");
		                      }
		                     else
		                      {
		                      	$('#alarm2').replaceWith("<div id='alarm2'><img src='images/ok.gif'></div>");
		                      	$('#a2state').replaceWith("<div id='a2state' style='color:green'>" + data.vms.a2hi + "</div>");
		                      }
		                    }
		
		                   else
		                    {
		                     if (data.vms.aLi2 == 'RED')
		                      {
		                      	$('#alarm2').replaceWith("<div id='alarm2'><img src='images/red_att.gif'></div>");
		                      	$('#a2state').replaceWith("<div id='a2state' style='color:red'>" + data.vms.a2lo + "</div>");
		                      }
		                     else
		                      {
		                      	$('#alarm2').replaceWith("<div id='alarm2'><img src='images/ok.gif'></div>");
		                      	$('#a2state').replaceWith("<div id='a2state' style='color:green'>" + data.vms.a2lo + "</div>");
		                      }
		                    }
		
		                   if (data.vms.a3 == 1)
		                    {
		                     if (data.vms.aHi3 == 'RED')
		                      {
		                      	$('#alarm3').replaceWith("<div id='alarm3'><img src='images/red_att.gif'></div>");
		                      	$('#a3state').replaceWith("<div id='a3state' style='color:red'>" + data.vms.a3hi + "</div>");
		                      }
		                     else
		                      {
		                      	$('#alarm3').replaceWith("<div id='alarm3'><img src='images/ok.gif'></div>");
		                      	$('#a3state').replaceWith("<div id='a3state' style='color:green'>" + data.vms.a3hi + "</div>");
		                      }
		                    }
		
		                   else
		                    {
		                     if (data.vms.aLi3 == 'RED')
		                      {
		                       $('#alarm3').replaceWith("<div id='alarm3'><img src='images/red_att.gif'></div>");
		                       $('#a3state').replaceWith("<div id='a3state' style='color:red'>" + data.vms.a3lo + "</div>");
		                      }
		                     else
		                      {
		                       $('#alarm3').replaceWith("<div id='alarm3'><img src='images/ok.gif'></div>");
		                       $('#a3state').replaceWith("<div id='a3state' style='color:green'>" + data.vms.a3lo + "</div>");
		                      }
		                    }
		
		                   if (data.vms.a4 == 1)
		                    {
		                     if (data.vms.aHi4 == 'RED')
		                      {
		                       $('#alarm4').replaceWith("<div id='alarm4'><img src='images/red_att.gif'></div>");
		                       $('#a4state').replaceWith("<div id='a4state' style='color:red'>" + data.vms.a4hi + "</div>");
		                      }
		                     else
		                      {
		                       $('#alarm4').replaceWith("<div id='alarm4'><img src='images/ok.gif'></div>");
		                       $('#a4state').replaceWith("<div id='a4state' style='color:green'>" + data.vms.a4hi + "</div>");
		                      }
		                    }
		
		                   else
		                    {
		                     if (data.vms.aLi4 == 'RED')
		                      {
		                       $('#alarm4').replaceWith("<div id='alarm4'><img src='images/red_att.gif'></div>");
		                       $('#a4state').replaceWith("<div id='a4state' style='color:red'>" + data.vms.a4lo + "</div>");
		                      }
		                     else
		                      {
		                       $('#alarm4').replaceWith("<div id='alarm4'><img src='images/ok.gif'></div>");
		                       $('#a4state').replaceWith("<div id='a4state' style='color:green'>" + data.vms.a4lo + "</div>");
		                      }
		                    }
		
		                   if (data.vms.a5 == 1)
		                    {
		                     if (data.vms.aHi5 == 'RED')
		                      {
		                       $('#alarm5').replaceWith("<div id='alarm5'><img src='images/red_att.gif'></div>");
		                       $('#a5state').replaceWith("<div id='a5state' style='color:red'>" + data.vms.a5hi + "</div>");
		                      }
		                     else
		                      {
		                       $('#alarm5').replaceWith("<div id='alarm5'><img src='images/ok.gif'></div>");
		                       $('#a5state').replaceWith("<div id='a5state' style='color:green'>" + data.vms.a5hi + "</div>");
		                      }
		                    }
		
		                   else
		                    {
		                     if (data.vms.aLi5 == 'RED')
		                      {
		                       $('#alarm5').replaceWith("<div id='alarm5'><img src='images/red_att.gif'></div>");
		                       $('#a5state').replaceWith("<div id='a5state' style='color:red'>" + data.vms.a5lo + "</div>");
		                      }
		                     else
		                      {
		                       $('#alarm5').replaceWith("<div id='alarm5'><img src='images/ok.gif'></div>");
		                       $('#a5state').replaceWith("<div id='a5state' style='color:green'>" + data.vms.a5lo + "</div>");
		                      }
		                    }
		
		
		                   if (data.vms.r1 == 1)
		                    {
		                     $('#relay1').replaceWith("<div id='relay1'><img src='images/nc-small.jpg'></div>");
		                     $('#r1N').replaceWith("<div id='r1N'><span style='color:"  + data.vms.r1nc_color + "'>" + data.vms.r1NC + "</span></div>");
		                    }
		                   else
		                    {
		                     $('#relay1').replaceWith("<div id='relay1'><img src='images/no-small.jpg'></div>");
		                     $('#r1N').replaceWith("<div id='r1N'><span style='color:"  + data.vms.r1no_color + "'>" + data.vms.r1NO + "</span></div>");
		                    }
		
		                   if (data.vms.r2 == 1)
		                    {
		                     $('#relay2').replaceWith("<div id='relay2'><img src='images/nc-small.jpg'></div>");
		                     $('#r2N').replaceWith("<div id='r2N'><span style='color:"  + data.vms.r2nc_color + "'>" + data.vms.r2NC + "</span></div>");
		                    }
		                   else
		                    {
		                     $('#relay2').replaceWith("<div id='relay2'><img src='images/no-small.jpg'></div>");
		                     $('#r2N').replaceWith("<div id='r2N'><span style='color:"  + data.vms.r2no_color + "'>" + data.vms.r2NO + "</span></div>");
		                    }

		                   if (data.vms.io1 == 0)
		                    {
		                     $('#gpio1').replaceWith("<div id='gpio1'><img src='images/io_low.gif'></div>");
		                     $('#gpio1state').replaceWith("<div id='gpio1state'><span style='color:red'>LOW</span></div>");
		                    }
		                   else
		                    {
		                     $('#gpio1').replaceWith("<div id='gpio1'><img src='images/io_high.gif'></div>");
		                     $('#gpio1state').replaceWith("<div id='gpio1state'><span style='color:green'>HIGH</span></div>");
		                    }
		
		                   if (data.vms.io2 == 0)
		                    {
		                     $('#gpio2').replaceWith("<div id='gpio2'><img src='images/io_low.gif'></div>");
		                     $('#gpio2state').replaceWith("<div id='gpio2state'><span style='color:red'>LOW</span></div>");
		                    }
		                   else
		                    {
		                     $('#gpio2').replaceWith("<div id='gpio2'><img src='images/io_high.gif'></div>");
		                     $('#gpio2state').replaceWith("<div id='gpio2state'><span style='color:green'>HIGH</span></div>");
		                    }
		
		                   if (data.vms.io3 == 0)
		                    {
		                     $('#gpio3').replaceWith("<div id='gpio3'><img src='images/io_low.gif'></div>");
		                     $('#gpio3state').replaceWith("<div id='gpio3state'><span style='color:red'>LOW</span></div>");
		                    }
		                   else
		                    {
		                     $('#gpio3').replaceWith("<div id='gpio3'><img src='images/io_high.gif'></div>");
		                     $('#gpio3state').replaceWith("<div id='gpio3state'><span style='color:green'>HIGH</font></div>");
		                    }
		
		                   if (data.vms.io4 == 0)
		                    {
		                     $('#gpio4').replaceWith("<div id='gpio4'><img src='images/io_low.gif'></div>");
		                     $('#gpio4state').replaceWith("<div id='gpio4state'><span style='color:red'>LOW</span></div>");
		                    }
		                   else
		                    {
		                     $('#gpio4').replaceWith("<div id='gpio4'><img src='images/io_high.gif'></div>");
		                     $('#gpio4state').replaceWith("<div id='gpio4state'><span style='color:green'>HIGH</span></div>");
		                    }
											 
											 if (data.vms.io1dir == 0)
		                    {
		                    	$('#gpio1dir').replaceWith("<div id='gpio1dir'><span>INPUT</span></div>");
		                    }
		                   else
		                    {
		                     $('#gpio1dir').replaceWith("<div id='gpio1dir'><span>OUTPUT</span></div>");
		                    }
												
												if (data.vms.io2dir == 0)
		                    {
		                    	$('#gpio2dir').replaceWith("<div id='gpio2dir'><span>INPUT</span></div>");
		                    }
		                   else
		                    {
		                     $('#gpio2dir').replaceWith("<div id='gpio2dir'><span>OUTPUT</span></div>");
		                    }
		                    
		                    if (data.vms.io3dir == 0)
		                    {
		                    	$('#gpio3dir').replaceWith("<div id='gpio3dir'><span>INPUT</span></div>");
		                    }
		                   else
		                    {
		                     $('#gpio3dir').replaceWith("<div id='gpio3dir'><span>OUTPUT</span></div>");
		                    }
		                    
		                    if (data.vms.io4dir == 0)
		                    {
		                    	$('#gpio4dir').replaceWith("<div id='gpio4dir'><span>INPUT</span></div>");
		                    }
		                   else
		                    {
		                     $('#gpio4dir').replaceWith("<div id='gpio4dir'><span>OUTPUT</span></div>");
		                    }
		                    
											 
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
								
		                    $('#progressbar9').progressbar({ value: + data.vms.meminfo });
												$('#progressbar9 > div').css({ 'background': '#00FF00' });
												$('#progressbar9 > div').css({ 'border-color': '#00FF00' });

												$('#progressbar10').progressbar({ value: + data.vms.diskinfo });
												$('#progressbar10 > div').css({ 'background': '#00FF00' });
												$('#progressbar10 > div').css({ 'border-color': '#00FF00' });
		                    
		            }
		        );
		}

	
		display_vm ();
		</script>		

</head>
<body class="fixed-navbar fixed-sidebar">

<div class='splash'> <div class='solid-line'></div><div class='splash-title'><h1>Loading... Please Wait...</h1><div class='spinner'> <div class='rect1'></div> <div class='rect2'></div> <div class='rect3'></div> <div class='rect4'></div> <div class='rect5'></div> </div> </div> </div>

<!--[if lt IE 7]>
<p class="alert alert-danger">You are using a <strong>old</strong> web browser. Please upgrade your browser to improve your experience.</p>
<![endif]-->

<?php start_header(); ?>

<?php left_nav("home"); ?>
<script language="javascript" type="text/javascript">
	SetContext('home');
</script>
<!-- Main Wrapper -->
<div id="wrapper">
	
	<?php
		if($screen_animations == "CHECKED")
		{
			echo '<div class="content animate-panel">';
		}
		else
		{
			echo '<div class="content">';
		}
	?>
  	
  	<?php if($info_block == "CHECKED"){	?> 			
  			<!-- INFO BLOCK START -->
  			<div class="row top-buffer">
    			<div class="col-sm-12">
    		  	<div class="hpanel">
    		    	<div class="panel-heading">
    		    		<div class="panel-tools">
    		    	  	<a class="showhide"><i class="fa fa-chevron-up"></i></a>
    		    	  	<a class="closebox"><i class="fa fa-times"></i></a>
    		    		</div>
    		    		<span style="color:black;font-weight: 800;">Solar Rig Tech System @ <?php echo $ip_address; ?></span>
    		    	</div>
    		      <div class="panel-body">
    		      	<div class="table-responsive">
    		        	<table cellpadding="1" cellspacing="1" style="width:100%">
    		            <tr>
    		            	<td style="width:10%">Station:</td>
    		              <td style="width:20%"><span style="color:blue"><?php echo $hostname; ?></span></td>
    		              <td style="width:10%">Temp:</td>
    		              <td style="width:20%">
    		              	<table>
    		              		<tr>
    		              			<td>
    		              				<div id='tempc' style='color:blue'></div>
    		              			</td>
    		              			<td>&nbsp;&nbsp;&nbsp;&nbsp;</td>
    		              			<td>
    		              				<div id='tempf' style='color:blue'></div>
    		              			</td>
    		              		</tr>
    		              	</table>	
    		              </td>
    		              <td style="width:10%"><a href="setup_statistics.php?context=setup"><u class="dotted">Memory:</u></a></td>
    		              <td style="width:30%">
    		              	<div id='progressbar9'></div>	
    		              </td>
    		            </tr>
    		            <tr>
    		            	<td>Location:</td>
    		              <td><span style="color:blue"><?php echo $syslocation; ?></span></td>
    		              <td>Mac:</td>
    		              <td><span style="color:blue"><?php echo $mac_address; ?></span></td>
    		              <td><a href="setup_statistics.php?context=setup"><u class="dotted">Disk:</u></a></td>
    		              <td>
    		              	<div id='progressbar10'></div>		
    		              </td>
    		            </tr>
    		            <tr>
    		            	<td>Date:</td>
    		              <td><div id='date' style="color:blue"></div></td>
    		              <td>Time:</td>
    		              <td><div id='time' style="color:blue"></div></td>
    		              <td><span>Uptime:</span></td>
    		              <td><div id='uptime' style="color:blue"></div></td>
    		            </tr>
    		            <tr>
    		            	<td>CPU:</td>
    		              <td><span style="color:blue">400 MHz</span></td>
    		              <td>OS:</td>
    		              <td><span style="color:blue">Linux</span></td>
    		              <td><span>GSD:</span></td>
    		              <td><span style="color:green"><strong>OK</strong></span></td>
    		            </tr>
    		          </table>
								</div>	
    		    	</div>
    		  	</div>
    			</div>
    		</div> <!-- END INFO BLOCK -->		
  	<?php	} ?>
  	
  	
    <?php if($alarm_block == "CHECKED"){	?>
    <!-- ALARM BLOCK START -->    
    <div class="row top-buffer">
    	<div class="col-sm-12">
      	<div class="hpanel">
        	<div class="panel-heading">
        		<div class="panel-tools">
        	  	<a class="showhide"><i class="fa fa-chevron-up"></i></a>
        	  	<a class="closebox"><i class="fa fa-times"></i></a>
        		</div>
        		<span style="color:black;font-weight: 800;">Alarm Overview</span>
        	</div>
          <div class="panel-body">
          	<div class="table-responsive">
            	<table style="width:100%">
              	<thead>
                	<tr>
                  	<th colspan="5" style="text-align:center; background-color:#D6DFF7;"><span style="color:black">Multi-Purpose Alarm Inputs (3.3 volts max)</span></th>
                  </tr>
                </thead> 
                <tr> 	
                	<td style="text-align:center;width:20%;"><a href="ios.php?context=ios" onMouseOver="Tip('<?php echo $a1_notes; ?>',TITLE,'Alarm 1 - <?php echo $a1_name; ?>',TITLEBGCOLOR,'#000000',BGCOLOR,'#ffff33');" onMouseOut="UnTip();"><span class="an">Alarm 1</span></a></td>
                  <td style="text-align:center;width:20%;"><a href="ios.php?context=ios" onMouseOver="Tip('<?php echo $a2_notes; ?>',TITLE,'Alarm 2 - <?php echo $a2_name; ?>',TITLEBGCOLOR,'#000000',BGCOLOR,'#ffff33');" onMouseOut="UnTip();"><span class="an">Alarm 2</span></a></td>
                  <td style="text-align:center;width:20%;"><a href="ios.php?context=ios" onMouseOver="Tip('<?php echo $a3_notes; ?>',TITLE,'Alarm 3 - <?php echo $a3_name; ?>',TITLEBGCOLOR,'#000000',BGCOLOR,'#ffff33');" onMouseOut="UnTip();"><span class="an">Alarm 3</span></a></td>
                  <td style="text-align:center;width:20%;"><a href="ios.php?context=ios" onMouseOver="Tip('<?php echo $a4_notes; ?>',TITLE,'Alarm 4 - <?php echo $a4_name; ?>',TITLEBGCOLOR,'#000000',BGCOLOR,'#ffff33');" onMouseOut="UnTip();"><span class="an">Alarm 4</span></a></td>
                  <td style="text-align:center;width:20%;"><a href="ios.php?context=ios" onMouseOver="Tip('<?php echo $a5_notes; ?>',TITLE,'Alarm 5 - <?php echo $a5_name; ?>',TITLEBGCOLOR,'#000000',BGCOLOR,'#ffff33');" onMouseOut="UnTip();"><span class="an">Alarm 5</span></a></td>
                </tr>
                <tr>
                	<td style="text-align:center;width:20%;"><a href="ios.php?context=ios"><div id='alarm1'><img src='images/red_att.gif'></div></a></td>
                  <td style="text-align:center;width:20%;"><a href="ios.php?context=ios"><div id='alarm2'><img src='images/red_att.gif'></div></a></td>
                  <td style="text-align:center;width:20%;"><a href="ios.php?context=ios"><div id='alarm3'><img src='images/red_att.gif'></div></a></td>
                  <td style="text-align:center;width:20%;"><a href="ios.php?context=ios"><div id='alarm4'><img src='images/red_att.gif'></div></a></td>
                  <td style="text-align:center;width:20%;"><a href="ios.php?context=ios"><div id='alarm5'><img src='images/red_att.gif'></div></a></td>
                </tr>
                <tr>
                	<td style="text-align:center;width:20%;"><a href="ios.php?context=ios" onMouseOver="Tip('<?php echo $a1_notes; ?>',TITLE,'Alarm 1 - <?php echo $a1_name; ?>',TITLEBGCOLOR,'#000000',BGCOLOR,'#ffff33');" onMouseOut="UnTip();"><span style="color:blue"><?php echo $a1_name; ?></span></a></td>
                  <td style="text-align:center;width:20%;"><a href="ios.php?context=ios" onMouseOver="Tip('<?php echo $a2_notes; ?>',TITLE,'Alarm 2 - <?php echo $a2_name; ?>',TITLEBGCOLOR,'#000000',BGCOLOR,'#ffff33');" onMouseOut="UnTip();"><span style="color:blue"><?php echo $a2_name; ?></span></a></td>
                  <td style="text-align:center;width:20%;"><a href="ios.php?context=ios" onMouseOver="Tip('<?php echo $a3_notes; ?>',TITLE,'Alarm 3 - <?php echo $a3_name; ?>',TITLEBGCOLOR,'#000000',BGCOLOR,'#ffff33');" onMouseOut="UnTip();"><span style="color:blue"><?php echo $a3_name; ?></span></a></td>
                  <td style="text-align:center;width:20%;"><a href="ios.php?context=ios" onMouseOver="Tip('<?php echo $a4_notes; ?>',TITLE,'Alarm 4 - <?php echo $a4_name; ?>',TITLEBGCOLOR,'#000000',BGCOLOR,'#ffff33');" onMouseOut="UnTip();"><span style="color:blue"><?php echo $a4_name; ?></span></a></td>
                  <td style="text-align:center;width:20%;"><a href="ios.php?context=ios" onMouseOver="Tip('<?php echo $a5_notes; ?>',TITLE,'Alarm 5 - <?php echo $a5_name; ?>',TITLEBGCOLOR,'#000000',BGCOLOR,'#ffff33');" onMouseOut="UnTip();"><span style="color:blue"><?php echo $a5_name; ?></span></a></td>
                </tr>
                <tr>
                	<td style="text-align:center;width:20%;"><a href='ios.php?context=ios'><div id='a1state' style='color:red'>TRIGGERED</div></a></td>
                  <td style="text-align:center;width:20%;"><a href='ios.php?context=ios'><div id='a2state' style='color:red'>TRIGGERED</div></a></td>
                  <td style="text-align:center;width:20%;"><a href='ios.php?context=ios'><div id='a3state' style='color:red'>TRIGGERED</div></a></td>
                  <td style="text-align:center;width:20%;"><a href='ios.php?context=ios'><div id='a4state' style='color:red'>TRIGGERED</div></a></td>
                  <td style="text-align:center;width:20%;"><a href='ios.php?context=ios'><div id='a5state' style='color:red'>TRIGGERED</div></a></td>
                </tr>
              </table>
						</div>	
        	</div>
      	</div>
    	</div>
    </div> <!-- END ALARM BLOCK -->
    <?php	} ?>    
        
    <?php if($io_block == "CHECKED"){	?>    
		<!-- GPIO BLOCK START -->        
  	<div class="row top-buffer">
    	<div class="col-sm-12">
      	<div class="hpanel">
        	<div class="panel-heading">
        		<div class="panel-tools">
        	  	<a class="showhide"><i class="fa fa-chevron-up"></i></a>
        	  	<a class="closebox"><i class="fa fa-times"></i></a>
        		</div>
        		<span style="color:black;font-weight: 800;">I/O Overview</span>
        	</div>
          <div class="panel-body">
          	<div class="table-responsive">
            	<table style="width:100%">
              	<thead>
                	<tr>
                  	<th colspan="4" style="text-align:center; background-color:#D6DFF7;"><span style="color:black">User Defined I/O Pins (3.3 volts max)</span></th>
                  </tr>
                </thead>  
                <tr>
                	<td style="text-align:center;width:25%;"><a href="ios.php?context=ios" onMouseOver="Tip('<?php echo $io1_notes; ?>',TITLE,'GPIO 1 - <?php echo $io1_name; ?>',TITLEBGCOLOR,'#000000',BGCOLOR,'#ffff33');" onMouseOut="UnTip();"><span class="an">GPIO 1</span></a></td>
                  <td style="text-align:center;width:25%;"><a href="ios.php?context=ios" onMouseOver="Tip('<?php echo $io2_notes; ?>',TITLE,'GPIO 2 - <?php echo $io2_name; ?>',TITLEBGCOLOR,'#000000',BGCOLOR,'#ffff33');" onMouseOut="UnTip();"><span class="an">GPIO 2</span></a></td>
                  <td style="text-align:center;width:25%;"><a href="ios.php?context=ios" onMouseOver="Tip('<?php echo $io3_notes; ?>',TITLE,'GPIO 3 - <?php echo $io3_name; ?>',TITLEBGCOLOR,'#000000',BGCOLOR,'#ffff33');" onMouseOut="UnTip();"><span class="an">GPIO 3</span></a></td>
                  <td style="text-align:center;width:25%;"><a href="ios.php?context=ios" onMouseOver="Tip('<?php echo $io4_notes; ?>',TITLE,'GPIO 4 - <?php echo $io4_name; ?>',TITLEBGCOLOR,'#000000',BGCOLOR,'#ffff33');" onMouseOut="UnTip();"><span class="an">GPIO 4</span></a></td>
                </tr>
                <tr>
                	<td style="text-align:center;width:25%;"><a href="ios.php?context=ios"><div id='gpio1'><img src='images/io_low.gif'></div></a></td>
                  <td style="text-align:center;width:25%;"><a href="ios.php?context=ios"><div id='gpio2'><img src='images/io_low.gif'></div></a></td>
                  <td style="text-align:center;width:25%;"><a href="ios.php?context=ios"><div id='gpio3'><img src='images/io_low.gif'></div></a></td>
                  <td style="text-align:center;width:25%;"><a href="ios.php?context=ios"><div id='gpio4'><img src='images/io_low.gif'></div></a></td>
                </tr>
                <tr>
                	<td style="text-align:center;width:25%;"><a href="ios.php?context=ios" onMouseOver="Tip('<?php echo $io1_notes; ?>',TITLE,'GPIO 1 - <?php echo $io1_name; ?>',TITLEBGCOLOR,'#000000',BGCOLOR,'#ffff33');" onMouseOut="UnTip();"><span style="color:blue"><?php echo $io1_name; ?></span></a></td>
                  <td style="text-align:center;width:25%;"><a href="ios.php?context=ios" onMouseOver="Tip('<?php echo $io2_notes; ?>',TITLE,'GPIO 2 - <?php echo $io2_name; ?>',TITLEBGCOLOR,'#000000',BGCOLOR,'#ffff33');" onMouseOut="UnTip();"><span style="color:blue"><?php echo $io2_name; ?></span></a></td>
                  <td style="text-align:center;width:25%;"><a href="ios.php?context=ios" onMouseOver="Tip('<?php echo $io3_notes; ?>',TITLE,'GPIO 3 - <?php echo $io3_name; ?>',TITLEBGCOLOR,'#000000',BGCOLOR,'#ffff33');" onMouseOut="UnTip();"><span style="color:blue"><?php echo $io3_name; ?></span></a></td>
                  <td style="text-align:center;width:25%;"><a href="ios.php?context=ios" onMouseOver="Tip('<?php echo $io4_notes; ?>',TITLE,'GPIO 4 - <?php echo $io4_name; ?>',TITLEBGCOLOR,'#000000',BGCOLOR,'#ffff33');" onMouseOut="UnTip();"><span style="color:blue"><?php echo $io4_name; ?></span></a></td>
                </tr>
                <tr>
                	<td style="text-align:center;width:16%;"><a href='ios.php?context=ios'><div id='gpio1dir'><span>OUTPUT</span></div></a></td>
                  <td style="text-align:center;width:16%;"><a href='ios.php?context=ios'><div id='gpio2dir'><span>OUTPUT</span></div></a></td>
                  <td style="text-align:center;width:16%;"><a href='ios.php?context=ios'><div id='gpio3dir'><span>OUTPUT</span></div></a></td>
                  <td style="text-align:center;width:16%;"><a href='ios.php?context=ios'><div id='gpio4dir'><span>OUTPUT</span></div></a></td>
                </tr> 
                <tr>
                	<td style="text-align:center;width:25%;"><a href='ios.php?context=ios'><div id='gpio1state' style='color:red'>LOW</div></a></td>
                  <td style="text-align:center;width:25%;"><a href='ios.php?context=ios'><div id='gpio2state' style='color:red'>LOW</div></a></td>
                  <td style="text-align:center;width:25%;"><a href='ios.php?context=ios'><div id='gpio3state' style='color:red'>LOW</div></a></td>
                  <td style="text-align:center;width:25%;"><a href='ios.php?context=ios'><div id='gpio4state' style='color:red'>LOW</div></a></td>
                </tr>  
              </table>
						</div>	
        	</div>
      	</div>
    	</div>
    </div> <!-- END GPIO BLOCK -->
    <?php	} ?>
    
    <?php if($relay_block == "CHECKED"){	?>     
    <!-- RELAY BLOCK START -->    
    <div class="row top-buffer">
    	<div class="col-sm-12">
      	<div class="hpanel">
        	<div class="panel-heading">
        		<div class="panel-tools">
        	  	<a class="showhide"><i class="fa fa-chevron-up"></i></a>
        	  	<a class="closebox"><i class="fa fa-times"></i></a>
        		</div>
        		<span style="color:black;font-weight: 800;">Relay Overview</span>
        	</div>
          <div class="panel-body">
          	<div class="table-responsive">
            	<table width="100%">
              	<thead>
                	<tr>
                  	<th colspan="2" style="text-align:center; background-color:#D6DFF7;"><span style="color:black">Multi-Purpose Power Relays</span></th>
                	</tr>
                </thead>
                <tr>
                	<td style="text-align:center;width:50%;"><a href="relays.php?context=relays" onMouseOver="Tip('<?php echo $rly1_notes; ?>',TITLE,'Relay 1 - <?php echo $rly1_name; ?>',TITLEBGCOLOR,'#000000',BGCOLOR,'#ffff33');" onMouseOut="UnTip();"><span class="an">RELAY 1</span></a></td>
                  <td style="text-align:center;width:50%;"><a href="relays.php?context=relays" onMouseOver="Tip('<?php echo $rly2_notes; ?>',TITLE,'Relay 2 - <?php echo $rly2_name; ?>',TITLEBGCOLOR,'#000000',BGCOLOR,'#ffff33');" onMouseOut="UnTip();"><span class="an">RELAY 2</span></a></td>
                </tr>
                <tr>
                	<td style="text-align:center;width:50%;"><a href="relays.php?context=relays"><div id='relay1'><img src='images/nc-small.jpg'></div></a></td>
                  <td style="text-align:center;width:50%;"><a href="relays.php?context=relays"><div id='relay2'><img src='images/nc-small.jpg'></div></a></td>
                </tr>
                <tr>
                	<td style="text-align:center;width:50%;"><a href="relays.php?context=relays" onMouseOver="Tip('<?php echo $rly1_notes; ?>',TITLE,'Relay 1 - <?php echo $rly1_name; ?>',TITLEBGCOLOR,'#000000',BGCOLOR,'#ffff33');" onMouseOut="UnTip();"><span style="color:blue"><?php echo $rly1_name; ?></span></a></td>
                  <td style="text-align:center;width:50%;"><a href="relays.php?context=relays" onMouseOver="Tip('<?php echo $rly2_notes; ?>',TITLE,'Relay 2 - <?php echo $rly2_name; ?>',TITLEBGCOLOR,'#000000',BGCOLOR,'#ffff33');" onMouseOut="UnTip();"><span style="color:blue"><?php echo $rly2_name; ?></span></a></td>
                </tr>
                <tr>
                	<td style="text-align:center;width:50%;"><a href='relays.php?context=relays'><div id='r1N' style='color:green'>COM-NC</div></a></td>
                  <td style="text-align:center;width:50%;"><a href='relays.php?context=relays'><div id='r2N' style='color:green'>COM-NC</div></a></td>
                </tr>
              </table>
						</div>	
        	</div>
      	</div>
    	</div>
    </div> <!-- END RELAY BLOCK -->
    <?php	} ?>
    
    <?php if($vm_block == "CHECKED"){	?>     
    <!-- VOLTMETER BLOCK START -->    
    <div class="row top-buffer">
    	<div class="col-sm-12">
      	<div class="hpanel">
        	<div class="panel-heading">
        		<div class="panel-tools">
        	  	<a class="showhide"><i class="fa fa-chevron-up"></i></a>
        	  	<a class="closebox"><i class="fa fa-times"></i></a>
        		</div>
        		<span style="color:black;font-weight: 800;">Voltmeter Overview</span>
        	</div>
          <div class="panel-body">
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
						</div>	
        	</div>
      	</div>
    	</div>
    </div> <!-- END VOLTMETER BLOCK -->    
		<?php	} ?>	
</div> <!-- END Main Wrapper -->
<script type="text/javascript" src="javascript/wz_tooltip.js"></script>	

</body>
</html>