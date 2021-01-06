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
	
	$result  = $dbh->query("SELECT * FROM io WHERE id='1' AND type='btn';");			
	foreach($result as $row)
		{
			$button1_name = $row['name'];
			$button1_notes = $row['notes'];
			$button1_notes = str_replace(array("\r\n", "\r", "\n"), "<br>", $button1_notes);
		}
	
	
if(isset ($_GET['action']))
{
	$action = $_GET['action'];
	if($action == "edit")
	{
		$success = $_GET['success'];
		if($success = "yes")
		{
			$type = $_GET['type'];
			$myid = $_GET['id'];
			$text = $type." # " . $myid . " Updated";		   						
			$alert_flag = "2";
		}	
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
			SetContext('ios');
		</script>
		
		<script language="javascript" type="text/javascript">
		function display_io ()
		{
		        var myRandom = parseInt(Math.random()*999999999);
		        $.getJSON('sdserver.php?element=ios&rand=' + myRandom,
		            function(data)
		            {
		
		                  //$.each (data.ios, function (k, v) { $('#' + k).text (v); });
		                  setTimeout (display_io, 1000);
		
		                  if (data.ios.alarm1 == 1)
		                    {
		                     if (data.ios.aHi1 == 'RED')
		                      {
		                      	$('#alarm1').replaceWith("<div id='alarm1'><img src='images/red_att.gif'></div>");
		                      	$('#a1state').replaceWith("<div id='a1state' style='color:red'>" + data.ios.a1hi + "</div>");
		                     	}
		                     else
		                      {
		                      	$('#alarm1').replaceWith("<div id='alarm1'><img src='images/ok.gif'></div>");
		                      	$('#a1state').replaceWith("<div id='a1state' style='color:green'>" + data.ios.a1hi + "</div>");
		                      }
		                    }
		
		                  else
		                    {
		                     if (data.ios.aLi1 == 'RED')
		                      {
		                      	$('#alarm1').replaceWith("<div id='alarm1'><img src='images/red_att.gif'></div>");
		                      	$('#a1state').replaceWith("<div id='a1state' style='color:red'>" + data.ios.a1lo + "</div>");
		                      }
		                     else
		                      {
		                      	$('#alarm1').replaceWith("<div id='alarm1'><img src='images/ok.gif'></div>");
		                      	$('#a1state').replaceWith("<div id='a1state' style='color:green'>" + data.ios.a1lo + "</div>");
		                      }
		                    }
		
		                   if (data.ios.alarm2 == 1)
		                    {
		                     if (data.ios.aHi2 == 'RED')
		                      {
		                      	$('#alarm2').replaceWith("<div id='alarm2'><img src='images/red_att.gif'></div>");
		                      	$('#a2state').replaceWith("<div id='a2state' style='color:red'>" + data.ios.a2hi + "</div>");
		                      }
		                     else
		                      {
		                      	$('#alarm2').replaceWith("<div id='alarm2'><img src='images/ok.gif'></div>");
		                      	$('#a2state').replaceWith("<div id='a2state' style='color:green'>" + data.ios.a2hi + "</div>");
		                      }
		                    }
		
		                   else
		                    {
		                     if (data.ios.aLi2 == 'RED')
		                      {
		                      	$('#alarm2').replaceWith("<div id='alarm2'><img src='images/red_att.gif'></div>");
		                      	$('#a2state').replaceWith("<div id='a2state' style='color:red'>" + data.ios.a2lo + "</div>");
		                      }
		                     else
		                      {
		                      	$('#alarm2').replaceWith("<div id='alarm2'><img src='images/ok.gif'></div>");
		                      	$('#a2state').replaceWith("<div id='a2state' style='color:green'>" + data.ios.a2lo + "</div>");
		                      }
		                    }
		
		                   if (data.ios.alarm3 == 1)
		                    {
		                     if (data.ios.aHi3 == 'RED')
		                      {
		                      	$('#alarm3').replaceWith("<div id='alarm3'><img src='images/red_att.gif'></div>");
		                      	$('#a3state').replaceWith("<div id='a3state' style='color:red'>" + data.ios.a3hi + "</div>");
		                      }
		                     else
		                      {
		                      	$('#alarm3').replaceWith("<div id='alarm3'><img src='images/ok.gif'></div>");
		                      	$('#a3state').replaceWith("<div id='a3state' style='color:green'>" + data.ios.a3hi + "</div>");
		                      }
		                    }
		
		                   else
		                    {
		                     if (data.ios.aLi3 == 'RED')
		                      {
		                       $('#alarm3').replaceWith("<div id='alarm3'><img src='images/red_att.gif'></div>");
		                       $('#a3state').replaceWith("<div id='a3state' style='color:red'>" + data.ios.a3lo + "</div>");
		                      }
		                     else
		                      {
		                       $('#alarm3').replaceWith("<div id='alarm3'><img src='images/ok.gif'></div>");
		                       $('#a3state').replaceWith("<div id='a3state' style='color:green'>" + data.ios.a3lo + "</div>");
		                      }
		                    }
		
		                   if (data.ios.alarm4 == 1)
		                    {
		                     if (data.ios.aHi4 == 'RED')
		                      {
		                       $('#alarm4').replaceWith("<div id='alarm4'><img src='images/red_att.gif'></div>");
		                       $('#a4state').replaceWith("<div id='a4state' style='color:red'>" + data.ios.a4hi + "</div>");
		                      }
		                     else
		                      {
		                       $('#alarm4').replaceWith("<div id='alarm4'><img src='images/ok.gif'></div>");
		                       $('#a4state').replaceWith("<div id='a4state' style='color:green'>" + data.ios.a4hi + "</div>");
		                      }
		                    }
		
		                   else
		                    {
		                     if (data.ios.aLi4 == 'RED')
		                      {
		                       $('#alarm4').replaceWith("<div id='alarm4'><img src='images/red_att.gif'></div>");
		                       $('#a4state').replaceWith("<div id='a4state' style='color:red'>" + data.ios.a4lo + "</div>");
		                      }
		                     else
		                      {
		                       $('#alarm4').replaceWith("<div id='alarm4'><img src='images/ok.gif'></div>");
		                       $('#a4state').replaceWith("<div id='a4state' style='color:green'>" + data.ios.a4lo + "</div>");
		                      }
		                    }
		
		                   if (data.ios.alarm5 == 1)
		                    {
		                     if (data.ios.aHi5 == 'RED')
		                      {
		                       $('#alarm5').replaceWith("<div id='alarm5'><img src='images/red_att.gif'></div>");
		                       $('#a5state').replaceWith("<div id='a5state' style='color:red'>" + data.ios.a5hi + "</div>");
		                      }
		                     else
		                      {
		                       $('#alarm5').replaceWith("<div id='alarm5'><img src='images/ok.gif'></div>");
		                       $('#a5state').replaceWith("<div id='a5state' style='color:green'>" + data.ios.a5hi + "</div>");
		                      }
		                    }
		
		                   else
		                    {
		                     if (data.ios.aLi5 == 'RED')
		                      {
		                       $('#alarm5').replaceWith("<div id='alarm5'><img src='images/red_att.gif'></div>");
		                       $('#a5state').replaceWith("<div id='a5state' style='color:red'>" + data.ios.a5lo + "</div>");
		                      }
		                     else
		                      {
		                       $('#alarm5').replaceWith("<div id='alarm5'><img src='images/ok.gif'></div>");
		                       $('#a5state').replaceWith("<div id='a5state' style='color:green'>" + data.ios.a5lo + "</div>");
		                      }
		                    }
		                    
		                   if (data.ios.io1 == 0)
		                    {
		                     $('#gpio1').replaceWith("<div id='gpio1'><img src='images/io_low.gif'></div>");
		                     $('#gpio1state').replaceWith("<div id='gpio1state'><span style='color:red'>LOW</span></div>");
		                    }
		                   else
		                    {
		                     $('#gpio1').replaceWith("<div id='gpio1'><img src='images/io_high.gif'></div>");
		                     $('#gpio1state').replaceWith("<div id='gpio1state'><span style='color:green'>HIGH</span></div>");
		                    }
		
		                   if (data.ios.io2 == 0)
		                    {
		                     $('#gpio2').replaceWith("<div id='gpio2'><img src='images/io_low.gif'></div>");
		                     $('#gpio2state').replaceWith("<div id='gpio2state'><span style='color:red'>LOW</span></div>");
		                    }
		                   else
		                    {
		                     $('#gpio2').replaceWith("<div id='gpio2'><img src='images/io_high.gif'></div>");
		                     $('#gpio2state').replaceWith("<div id='gpio2state'><span style='color:green'>HIGH</span></div>");
		                    }
		
		                   if (data.ios.io3 == 0)
		                    {
		                     $('#gpio3').replaceWith("<div id='gpio3'><img src='images/io_low.gif'></div>");
		                     $('#gpio3state').replaceWith("<div id='gpio3state'><span style='color:red'>LOW</span></div>");
		                    }
		                   else
		                    {
		                     $('#gpio3').replaceWith("<div id='gpio3'><img src='images/io_high.gif'></div>");
		                     $('#gpio3state').replaceWith("<div id='gpio3state'><span style='color:green'>HIGH</font></div>");
		                    }
		
		                   if (data.ios.io4 == 0)
		                    {
		                     $('#gpio4').replaceWith("<div id='gpio4'><img src='images/io_low.gif'></div>");
		                     $('#gpio4state').replaceWith("<div id='gpio4state'><span style='color:red'>LOW</span></div>");
		                    }
		                   else
		                    {
		                     $('#gpio4').replaceWith("<div id='gpio4'><img src='images/io_high.gif'></div>");
		                     $('#gpio4state').replaceWith("<div id='gpio4state'><span style='color:green'>HIGH</span></div>");
		                    }
												
											 if (data.ios.io1dir == 0)
		                    {
		                    	$('#gpio1dir').replaceWith("<div id='gpio1dir'><span>INPUT</span></div>");
		                    }
		                   else
		                    {
		                     $('#gpio1dir').replaceWith("<div id='gpio1dir'><span>OUTPUT</span></div>");
		                    }
												
												if (data.ios.io2dir == 0)
		                    {
		                    	$('#gpio2dir').replaceWith("<div id='gpio2dir'><span>INPUT</span></div>");
		                    }
		                   else
		                    {
		                     $('#gpio2dir').replaceWith("<div id='gpio2dir'><span>OUTPUT</span></div>");
		                    }
		                    
		                    if (data.ios.io3dir == 0)
		                    {
		                    	$('#gpio3dir').replaceWith("<div id='gpio3dir'><span>INPUT</span></div>");
		                    }
		                   else
		                    {
		                     $('#gpio3dir').replaceWith("<div id='gpio3dir'><span>OUTPUT</span></div>");
		                    }
		                    
		                    if (data.ios.io4dir == 0)
		                    {
		                    	$('#gpio4dir').replaceWith("<div id='gpio4dir'><span>INPUT</span></div>");
		                    }
		                   else
		                    {
		                     $('#gpio4dir').replaceWith("<div id='gpio4dir'><span>OUTPUT</span></div>");
		                    }	
												
												if (data.ios.btn1 == 1)
		                    {
		                     $('#btn1state').replaceWith("<div id='btn1state' style='color:green'>UP</div>");
		                    } 	
		                    else
		                    {
		                     $('#btn1state').replaceWith("<div id='btn1state' style='color:red'>DOWN</div>");
		                    }
		            }
		        );
		}

	
		display_io ();
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
			echo '<div class="content animate-panel" data-effect="fadeInRightBig">';
		}
		else
		{
			echo '<div class="content">';
		}
	?>
  	<!-- INFO BLOCK START -->
  	<div class="row">
  		<div class="col-md-12"><legend>IO Options</legend></div>
  	</div>
  	
  	<div class="row">
    	<div class="col-sm-12">
      	<div class="hpanel3">
      	  <div class="panel-body" style="text-align:center; background:#F1F3F6;border:none;">
    		  	<div class="table-responsive">
    		   		<table cellpadding="1" cellspacing="1" style="width:15%">
    		   			<tbody>
    		    			<tr> 
    		    				<td>
    		    					<a href="setup_scripts.php" onMouseOver="mouse_move(&#039;b_ios_ioscripts&#039;);" onMouseOut="mouse_move();">
      	    					<img src="images/ioscripts.gif"><br><span><b>Create IO Scripts</b></span>
      	    					</a>
    		    				</td>
    		    			</tr>
    		    		</tbody>
							</table>      	    
      	  	</div> <!-- END TABLE RESPONSIVE -->
      		</div> <!-- END PANEL BODY --> 
      	</div> <!-- END HPANEL3 --> 
      </div> <!-- END COL-MD-12 --> 
    </div> <!-- END ROW --> 	
    <br>  
    
    <div class="row">
  		<div class="col-sm-12"><legend>Alarm Input Setup</legend></div>
  	</div>  
    
    <div class="row">
    	<div class="col-sm-12">
      	<div class="hpanel3">
      	  <div class="panel-body" style="text-align:center; background:#F1F3F6;border:none;">
    		  	<div class="table-responsive">
    		   		<table style="width:75%;">
              	 
                <tr> 	
                	<td style="text-align:center;width:15%;"><a href="setup_alarm.php?alarm=1" onMouseOver="Tip('<?php echo $a1_notes; ?>',TITLE,'Alarm 1 - <?php echo $a1_name; ?>',TITLEBGCOLOR,'#000000',BGCOLOR,'#ffff33'); mouse_move(&#039;b_ios_alarm&#039;);" onMouseOut="UnTip();mouse_move();"><span class="an">Alarm 1</span></a></td>
                  <td style="text-align:center;width:15%;"><a href="setup_alarm.php?alarm=2" onMouseOver="Tip('<?php echo $a2_notes; ?>',TITLE,'Alarm 2 - <?php echo $a2_name; ?>',TITLEBGCOLOR,'#000000',BGCOLOR,'#ffff33'); mouse_move(&#039;b_ios_alarm&#039;);" onMouseOut="UnTip();mouse_move();"><span class="an">Alarm 2</span></a></td>
                  <td style="text-align:center;width:15%;"><a href="setup_alarm.php?alarm=3" onMouseOver="Tip('<?php echo $a3_notes; ?>',TITLE,'Alarm 3 - <?php echo $a3_name; ?>',TITLEBGCOLOR,'#000000',BGCOLOR,'#ffff33'); mouse_move(&#039;b_ios_alarm&#039;);" onMouseOut="UnTip();mouse_move();"><span class="an">Alarm 3</span></a></td>
                  <td style="text-align:center;width:15%;"><a href="setup_alarm.php?alarm=4" onMouseOver="Tip('<?php echo $a4_notes; ?>',TITLE,'Alarm 4 - <?php echo $a4_name; ?>',TITLEBGCOLOR,'#000000',BGCOLOR,'#ffff33'); mouse_move(&#039;b_ios_alarm&#039;);" onMouseOut="UnTip();mouse_move();"><span class="an">Alarm 4</span></a></td>
                  <td style="text-align:center;width:15%;"><a href="setup_alarm.php?alarm=5" onMouseOver="Tip('<?php echo $a5_notes; ?>',TITLE,'Alarm 5 - <?php echo $a5_name; ?>',TITLEBGCOLOR,'#000000',BGCOLOR,'#ffff33'); mouse_move(&#039;b_ios_alarm&#039;);" onMouseOut="UnTip();mouse_move();"><span class="an">Alarm 5</span></a></td>
                </tr>
                <tr>
                	<td style="text-align:center;width:15%;"><a href="setup_alarm.php?alarm=1" onMouseOver="mouse_move(&#039;b_ios_alarm&#039;);" onMouseOut="mouse_move();"><div id='alarm1'><img src='images/red_att.gif'></div></a></td>
                  <td style="text-align:center;width:15%;"><a href="setup_alarm.php?alarm=2" onMouseOver="mouse_move(&#039;b_ios_alarm&#039;);" onMouseOut="mouse_move();"><div id='alarm2'><img src='images/red_att.gif'></div></a></td>
                  <td style="text-align:center;width:15%;"><a href="setup_alarm.php?alarm=3" onMouseOver="mouse_move(&#039;b_ios_alarm&#039;);" onMouseOut="mouse_move();"><div id='alarm3'><img src='images/red_att.gif'></div></a></td>
                  <td style="text-align:center;width:15%;"><a href="setup_alarm.php?alarm=4" onMouseOver="mouse_move(&#039;b_ios_alarm&#039;);" onMouseOut="mouse_move();"><div id='alarm4'><img src='images/red_att.gif'></div></a></td>
                  <td style="text-align:center;width:15%;"><a href="setup_alarm.php?alarm=5" onMouseOver="mouse_move(&#039;b_ios_alarm&#039;);" onMouseOut="mouse_move();"><div id='alarm5'><img src='images/red_att.gif'></div></a></td>
                </tr>
                <tr>
                	<td style="text-align:center;width:15%;"><a href="setup_alarm.php?alarm=1" onMouseOver="Tip('<?php echo $a1_notes; ?>',TITLE,'Alarm 1 - <?php echo $a1_name; ?>',TITLEBGCOLOR,'#000000',BGCOLOR,'#ffff33'); mouse_move(&#039;b_ios_alarm&#039;);" onMouseOut="UnTip();mouse_move();"><span style="color:blue"><?php echo $a1_name; ?></span></a></td>
                  <td style="text-align:center;width:15%;"><a href="setup_alarm.php?alarm=2" onMouseOver="Tip('<?php echo $a2_notes; ?>',TITLE,'Alarm 2 - <?php echo $a2_name; ?>',TITLEBGCOLOR,'#000000',BGCOLOR,'#ffff33'); mouse_move(&#039;b_ios_alarm&#039;);" onMouseOut="UnTip();mouse_move();"><span style="color:blue"><?php echo $a2_name; ?></span></a></td>
                  <td style="text-align:center;width:15%;"><a href="setup_alarm.php?alarm=3" onMouseOver="Tip('<?php echo $a3_notes; ?>',TITLE,'Alarm 3 - <?php echo $a3_name; ?>',TITLEBGCOLOR,'#000000',BGCOLOR,'#ffff33'); mouse_move(&#039;b_ios_alarm&#039;);" onMouseOut="UnTip();mouse_move();"><span style="color:blue"><?php echo $a3_name; ?></span></a></td>
                  <td style="text-align:center;width:15%;"><a href="setup_alarm.php?alarm=4" onMouseOver="Tip('<?php echo $a4_notes; ?>',TITLE,'Alarm 4 - <?php echo $a4_name; ?>',TITLEBGCOLOR,'#000000',BGCOLOR,'#ffff33'); mouse_move(&#039;b_ios_alarm&#039;);" onMouseOut="UnTip();mouse_move();"><span style="color:blue"><?php echo $a4_name; ?></span></a></td>
                  <td style="text-align:center;width:15%;"><a href="setup_alarm.php?alarm=5" onMouseOver="Tip('<?php echo $a5_notes; ?>',TITLE,'Alarm 5 - <?php echo $a5_name; ?>',TITLEBGCOLOR,'#000000',BGCOLOR,'#ffff33'); mouse_move(&#039;b_ios_alarm&#039;);" onMouseOut="UnTip();mouse_move();"><span style="color:blue"><?php echo $a5_name; ?></span></a></td>
                </tr>
                <tr>
                	<td style="text-align:center;width:15%;"><a href='setup_alarm.php?alarm=1' onMouseOver="mouse_move(&#039;b_ios_alarm&#039;);" onMouseOut="mouse_move();"><div id='a1state' style='color:red'>TRIGGERED</div></a></td>
                  <td style="text-align:center;width:15%;"><a href='setup_alarm.php?alarm=2' onMouseOver="mouse_move(&#039;b_ios_alarm&#039;);" onMouseOut="mouse_move();"><div id='a2state' style='color:red'>TRIGGERED</div></a></td>
                  <td style="text-align:center;width:15%;"><a href='setup_alarm.php?alarm=3' onMouseOver="mouse_move(&#039;b_ios_alarm&#039;);" onMouseOut="mouse_move();"><div id='a3state' style='color:red'>TRIGGERED</div></a></td>
                  <td style="text-align:center;width:15%;"><a href='setup_alarm.php?alarm=4' onMouseOver="mouse_move(&#039;b_ios_alarm&#039;);" onMouseOut="mouse_move();"><div id='a4state' style='color:red'>TRIGGERED</div></a></td>
                  <td style="text-align:center;width:15%;"><a href='setup_alarm.php?alarm=5' onMouseOver="mouse_move(&#039;b_ios_alarm&#039;);" onMouseOut="mouse_move();"><div id='a5state' style='color:red'>TRIGGERED</div></a></td>
                </tr>
              </table>   	    
      	  	</div> <!-- END TABLE RESPONSIVE -->
      		</div> <!-- END PANEL BODY --> 
      	</div> <!-- END HPANEL3 --> 
      </div> <!-- END COL-MD-12 --> 
    </div> <!-- END ROW --> 	
   
   <br><br>  
    
    <div class="row">
  		<div class="col-sm-12"><legend>GPIO Setup</legend></div>
  	</div>  
    
    <div class="row">
    	<div class="col-sm-12">
      	<div class="hpanel3">
      	  <div class="panel-body" style="text-align:center; background:#F1F3F6;border:none;">
    		  	<div class="table-responsive">
    		   		<table style="width:75%;">
              	 
                <tr>
                	<td style="text-align:center;width:15%;"><a href="setup_io.php?io=1" onMouseOver="Tip('<?php echo $io1_notes; ?>',TITLE,'GPIO 1 - <?php echo $io1_name; ?>',TITLEBGCOLOR,'#000000',BGCOLOR,'#ffff33'); mouse_move(&#039;b_ios_gxio&#039;);" onMouseOut="UnTip();mouse_move();"><span class="an">GPIO 1</span></a></td>
                  <td style="text-align:center;width:15%;"><a href="setup_io.php?io=2" onMouseOver="Tip('<?php echo $io2_notes; ?>',TITLE,'GPIO 2 - <?php echo $io2_name; ?>',TITLEBGCOLOR,'#000000',BGCOLOR,'#ffff33'); mouse_move(&#039;b_ios_gxio&#039;);" onMouseOut="UnTip();mouse_move();"><span class="an">GPIO 2</span></a></td>
                  <td style="text-align:center;width:15%;"><a href="setup_io.php?io=3" onMouseOver="Tip('<?php echo $io3_notes; ?>',TITLE,'GPIO 3 - <?php echo $io3_name; ?>',TITLEBGCOLOR,'#000000',BGCOLOR,'#ffff33'); mouse_move(&#039;b_ios_gxio&#039;);" onMouseOut="UnTip();mouse_move();"><span class="an">GPIO 3</span></a></td>
                  <td style="text-align:center;width:15%;"><a href="setup_io.php?io=4" onMouseOver="Tip('<?php echo $io4_notes; ?>',TITLE,'GPIO 4 - <?php echo $io4_name; ?>',TITLEBGCOLOR,'#000000',BGCOLOR,'#ffff33'); mouse_move(&#039;b_ios_gxio&#039;);" onMouseOut="UnTip();mouse_move();"><span class="an">GPIO 4</span></a></td>
                </tr>
                <tr>
                	<td style="text-align:center;width:15%;"><a href="setup_io.php?io=1" onMouseOver="mouse_move(&#039;b_ios_gxio&#039;);" onMouseOut="mouse_move();"><div id='gpio1'><img src='images/io_low.gif'></div></a></td>
                  <td style="text-align:center;width:15%;"><a href="setup_io.php?io=2" onMouseOver="mouse_move(&#039;b_ios_gxio&#039;);" onMouseOut="mouse_move();"><div id='gpio2'><img src='images/io_low.gif'></div></a></td>
                  <td style="text-align:center;width:15%;"><a href="setup_io.php?io=3" onMouseOver="mouse_move(&#039;b_ios_gxio&#039;);" onMouseOut="mouse_move();"><div id='gpio3'><img src='images/io_low.gif'></div></a></td>
                  <td style="text-align:center;width:15%;"><a href="setup_io.php?io=4" onMouseOver="mouse_move(&#039;b_ios_gxio&#039;);" onMouseOut="mouse_move();"><div id='gpio4'><img src='images/io_low.gif'></div></a></td>
                </tr>
                <tr>
                	<td style="text-align:center;width:15%;"><a href="setup_io.php?io=1" onMouseOver="Tip('<?php echo $io1_notes; ?>',TITLE,'GPIO 1 - <?php echo $io1_name; ?>',TITLEBGCOLOR,'#000000',BGCOLOR,'#ffff33'); mouse_move(&#039;b_ios_gxio&#039;);" onMouseOut="UnTip();mouse_move();"><span style="color:blue"><?php echo $io1_name; ?></span></a></td>
                  <td style="text-align:center;width:15%;"><a href="setup_io.php?io=2" onMouseOver="Tip('<?php echo $io2_notes; ?>',TITLE,'GPIO 2 - <?php echo $io2_name; ?>',TITLEBGCOLOR,'#000000',BGCOLOR,'#ffff33'); mouse_move(&#039;b_ios_gxio&#039;);" onMouseOut="UnTip();mouse_move();"><span style="color:blue"><?php echo $io2_name; ?></span></a></td>
                  <td style="text-align:center;width:15%;"><a href="setup_io.php?io=3" onMouseOver="Tip('<?php echo $io3_notes; ?>',TITLE,'GPIO 3 - <?php echo $io3_name; ?>',TITLEBGCOLOR,'#000000',BGCOLOR,'#ffff33'); mouse_move(&#039;b_ios_gxio&#039;);" onMouseOut="UnTip();mouse_move();"><span style="color:blue"><?php echo $io3_name; ?></span></a></td>
                  <td style="text-align:center;width:15%;"><a href="setup_io.php?io=4" onMouseOver="Tip('<?php echo $io4_notes; ?>',TITLE,'GPIO 4 - <?php echo $io4_name; ?>',TITLEBGCOLOR,'#000000',BGCOLOR,'#ffff33'); mouse_move(&#039;b_ios_gxio&#039;);" onMouseOut="UnTip();mouse_move();"><span style="color:blue"><?php echo $io4_name; ?></span></a></td>
                </tr>
                <tr>
                	<td style="text-align:center;width:15%;"><a href="setup_io.php?io=1"><div id="gpio1dir"><span>OUTPUT</span></div></a></td>
                  <td style="text-align:center;width:15%;"><a href="setup_io.php?io=2"><div id="gpio2dir"><span>OUTPUT</span></div></a></td>
                  <td style="text-align:center;width:15%;"><a href="setup_io.php?io=3"><div id="gpio3dir"><span>OUTPUT</span></div></a></td>
                  <td style="text-align:center;width:15%;"><a href="setup_io.php?io=4"><div id="gpio4dir"><span>OUTPUT</span></div></a></td>
                </tr> 
                <tr>
                	<td style="text-align:center;width:15%;"><a href="setup_io.php?io=1" onMouseOver="mouse_move(&#039;b_ios_gxio&#039;);" onMouseOut="mouse_move();"><div id='gpio1state' style='color:red'>LOW</div></a></td>
                  <td style="text-align:center;width:15%;"><a href="setup_io.php?io=2" onMouseOver="mouse_move(&#039;b_ios_gxio&#039;);" onMouseOut="mouse_move();"><div id='gpio2state' style='color:red'>LOW</div></a></td>
                  <td style="text-align:center;width:15%;"><a href="setup_io.php?io=3" onMouseOver="mouse_move(&#039;b_ios_gxio&#039;);" onMouseOut="mouse_move();"><div id='gpio3state' style='color:red'>LOW</div></a></td>
                  <td style="text-align:center;width:15%;"><a href="setup_io.php?io=4" onMouseOver="mouse_move(&#039;b_ios_gxio&#039;);" onMouseOut="mouse_move();"><div id='gpio4state' style='color:red'>LOW</div></a></td>
                </tr>  
              </table>      	    
      	  	</div> <!-- END TABLE RESPONSIVE -->
      		</div> <!-- END PANEL BODY --> 
      	</div> <!-- END HPANEL3 --> 
      </div> <!-- END COL-MD-12 --> 
    </div> <!-- END ROW --> 
  	
  	<br><br>  
    
    <div class="row">
  		<div class="col-sm-12"><legend>Micro Push Button Setup</legend></div>
  	</div>  
    
    <div class="row">
    	<div class="col-sm-12">
      	<div class="hpanel3">
      	  <div class="panel-body" style="text-align:center; background:#F1F3F6;border:none;">
    		  	<div class="table-responsive">
    		   		<table cellpadding="1" cellspacing="1" style="width:15%;">
    		   			<tbody>
    		    			<tr> 
                		<td style="text-align:center;"><a href="setup_button.php?btn=1" onMouseOver="Tip('<?php echo $button1_notes; ?>',TITLE,'BUTTON 1 - <?php echo $button1_name; ?>',TITLEBGCOLOR,'#000000',BGCOLOR,'#ffff33'); mouse_move(&#039;sd_ios_btn1&#039;);" onMouseOut="UnTip();mouse_move();"><span class="an">BUTTON 1</span></a></td>
                	</tr>
                	<tr>
                		<td style="text-align:center;"><a href="setup_button.php?btn=1" onMouseOver="mouse_move(&#039;sd_ios_btn1&#039;);" onMouseOut="mouse_move();"><div id='button1'><img src='images/micro_button1.gif'></div></a></td>
                	</tr>
                	<tr>
                		<td style="text-align:center;"><a href="setup_button.php?btn=1" onMouseOver="Tip('<?php echo $button1_notes; ?>',TITLE,'BUTTON 1 - <?php echo $button1_name; ?>',TITLEBGCOLOR,'#000000',BGCOLOR,'#ffff33'); mouse_move(&#039;sd_ios_btn1&#039;);" onMouseOut="UnTip();mouse_move();"><span style="color:blue"><?php echo $button1_name; ?></span></a></td>
                	</tr>
                	<tr>
                		<td style="text-align:center;"><a href='setup_button.php?btn=1' onMouseOver="mouse_move(&#039;sd_ios_btn1&#039;);" onMouseOut="mouse_move();"><div id='btn1state' style='color:green'>UP</div></a></td>
                	</tr>
    		    		</tbody>
							</table>      	    
      	  	</div> <!-- END TABLE RESPONSIVE -->
      		</div> <!-- END PANEL BODY --> 
      	</div> <!-- END HPANEL3 --> 
      </div> <!-- END COL-MD-12 --> 
    </div> <!-- END ROW --> 
  	
  	
 
  </div> <!-- END CONTENT -->    
</div> <!-- END Main Wrapper -->
<script type="text/javascript" src="javascript/wz_tooltip.js"></script>	

<?php

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


?>


</body>
</html> 
