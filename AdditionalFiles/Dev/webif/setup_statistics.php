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
	
	$sd_errmsg = "";
	$sd_errmsg2 = "";
	$alert_flag = "0";
	
/////////////////////////////////////////////////////////////////
//                                                             //
//                    GET PROCESSING                           //
//                                                             //
/////////////////////////////////////////////////////////////////
// clearcache Button	was clicked
if(isset ($_GET['clearcache']))
{
	system("echo 3 > /proc/sys/vm/drop_caches"); 
	sleep(1);
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
	header("Location: setup.php?context=setup");
}

	
system("uptime > /tmp/tmp-webif-date");
$uptime = file_get_contents("/tmp/tmp-webif-date");
unlink("/tmp/tmp-webif-date");
	



	
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <!-- Page title -->
    <title><?php echo $hostname ?></title>
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
			SetContext('stats');
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
	SetContext('stats');
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
		<form name='Statistics' action='setup_statistics.php' method='post' class="form-horizontal">  	
    	<fieldset>
  			<!-- INFO BLOCK START -->
  			<div class="row">
  				<div class="col-sm-12">
  					<legend><img src="images/btn_statistics_bg.gif"> Statistical Information</legend>
					</div>
					<div class="col-sm-12">
						<span style="color:blue; font-weight: 800;">&nbsp;&nbsp;&nbsp;System Uptime:</span><strong><?php echo $uptime;	?></strong>	
					</div>
    			<div class="col-sm-12">
    		  	<div class="hpanel">
    		  		<div class="panel-body" style="background:#F1F3F6;border:none;">
    		  	  	<?php
    		  	  		if(file_exists("rrd/tmp/load-hour.png"))
    		  	  		{
										
								
								
    		  	  			echo "			<a href='rms-graph.php?action=viewgraph&amp;g=load'><img src='rrd/tmp/load-hour.png' height='140'></a>";
					
						
    		  	  		}
    		  	  		else
    		  	  		{
    		  	  			echo "<img src='images/no-rrd-load.jpg' height='140'>";
    		  	  		}
    		  	  	?>
    		
    		    
					
					
				
							</div> <!-- END PANEL BODY -->
						</div> <!-- END HPANEL4 -->
					</div> <!-- END COL-MD-4 -->
    		
  			</div>	<!-- END ROW -->
    		
    		<div class="row">
    		<br>	
    			<div class="col-sm-12" style="max-width:450px">
    				<div class="hpanel5">
    					<div class="panel-heading">
    				  	<span style="color:blue;font-weight: 800;"> Process Information:</span>
    				  </div>
    		  		<div class="panel-body" style="background:#F1F3F6;border:none; max-width:450px"> 
    		  			<?php
    		  	    	system("ps > /tmp/tmp-ps");
    		  	    	$ps = file_get_contents("/tmp/tmp-ps");
    		  	    	unlink("/tmp/tmp-ps");
    		  	    ?> 	    	
    		  	    <textarea rows="6" cols="40" style="font-family:Lucida Console"><?php echo $ps; ?></textarea>	
    		  	  </div> <!-- END PANEL BODY -->
						</div> <!-- END HPANEL4 -->
					</div> <!-- END COL-MD-4 -->  
				</div>
				<div class="row">
    		  <div class="col-sm-12" style="max-width:450px">
    		  	<div class="hpanel5">
    		  		<div class="panel-heading">
    				  	<span style="color:blue;font-weight: 800;"> Memory Usage: </span><span style="color:black;font-weight: 800;"><a href="setup_statistics.php?clearcache=yes">&nbsp;&nbsp;&nbsp;<u class="dotted">Click here to clear RMS Memory cache</u></a></span>
    				  </div>
    		  		<div class="panel-body" style="background:#F1F3F6;border:none;">	    	
    		  	    	
    		  	    <?php
    		  	    	system("cat /proc/meminfo > /tmp/tmp-minfo");
    		  	    	$minfo = file_get_contents("/tmp/tmp-minfo");
    		  	    	unlink("/tmp/tmp-minfo");
    		  	    ?>	
    		  	    <textarea rows="6" cols="40" style="font-family:Lucida Console"><?php echo $minfo; ?></textarea>		
    		  	  
    		  	  </div> <!-- END PANEL BODY -->
						</div> <!-- END HPANEL4 -->
					</div> <!-- END COL-MD-4 --> 
    		</div> <!-- END ROW -->
    		<div class="row">
    			<br>	
    			<div class="col-sm-12" style="max-width:450px">
    				<div class="hpanel5">
    					<div class="panel-heading">
    				  	<span style="color:blue;font-weight: 800;"> Disk Usage:</span>
    				  </div>  	  
    		  	  <div class="panel-body" style="background:#F1F3F6;border:none;">	
    		  	  	<?php
    		  	    	system("df > /tmp/tmp-dinfo");
    		  	    	$dinfo = file_get_contents("/tmp/tmp-dinfo");
    		  	    	unlink("/tmp/tmp-dinfo");
    		  	    ?>	
    		  	  	<textarea rows="6" cols="40" style="font-family:Lucida Console"><?php echo $dinfo; ?></textarea>		
    		  	  
    		  	  </div> <!-- END PANEL BODY -->
						</div> <!-- END HPANEL4 -->
					</div> <!-- END COL-MD-4 --> 
				</div>
				<div class="row">					
    		  <div class="col-sm-12" style="max-width:450px">
    		  	<div class="hpanel5">
    		  		<div class="panel-heading">
    				  	<span style="color:blue;font-weight: 800;"> Interface Config: </span>
    				  </div>
    		  		<div class="panel-body" style="background:#F1F3F6;border:none;">	
    		  	  
    		  	  	<?php
    		  	    	system("ifconfig > /tmp/tmp-interface");
    		  	    	$interface = file_get_contents("/tmp/tmp-interface");
    		  	    	unlink("/tmp/tmp-interface");
    		  	    ?>	
    		  	    <textarea rows="6" cols="40" style="font-family:Lucida Console"><?php echo $interface; ?></textarea>		
    		  		</div> <!-- END PANEL BODY --> 
						</div> <!-- END HPANEL4 -->
					</div> <!-- END COL-MD-4 -->
				</div> <!-- END ROW -->
				<div class="row">
					<div class="col-sm-5">
    		  	<div class="hpanel5">
    		  		<div class="panel-body" style="background:#F1F3F6;border:none;">
    		  			<br>
 	  						<br>
    						<div>
    							<button name="refresh_btn" class="btn btn-success" type="submit" onMouseOver="mouse_move(&#039;b_refresh&#039;);" onMouseOut="mouse_move();"><i class="fa fa-check" ></i> Refresh</button>
    							<button name="cancel_btn" class="btn btn-primary" type="submit" onMouseOver="mouse_move(&#039;b_cancel&#039;);" onMouseOut="mouse_move();" formnovalidate><i class="fa fa-times"></i> Cancel</button> 
  							</div>	
  						</div> <!-- END PANEL BODY -->
    		  	</div> <!-- END COL-MD-5 -->
    		  </div> <!-- END HPANEL4 -->	
    		</div> <!-- END ROW -->   
  		</fieldset>  
		</form>
  </div> <!-- END CONTENT -->    
</div> <!-- END Main Wrapper -->

</body>
</html> 
