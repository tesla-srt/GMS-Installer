<?php
	error_reporting(E_ALL);
	include "lib.php";
	$hostname = trim(file_get_contents("/etc/hostname"));
	
	$alert_flag = "0";
	
	
/////////////////////////////////////////////////////////////////
//                                                             //
//                    GET PROCESSING                           //
//                                                             //
/////////////////////////////////////////////////////////////////

	$dbh = new PDO('sqlite:/etc/rms100.db');
	
	$result  = $dbh->query("SELECT * FROM display_options;");			
	foreach($result as $row)
	{
		$screen_animations = $row['screen_animations'];
	}
	
	$query = "SELECT * FROM update_notice_conf";
	$result  = $dbh->query($query);
	foreach($result as $row)
	{
		$confirmation = $row['confirmation'];	
	}
	if($confirmation == "on")
	{
		$confirmation = "checked";
	}
	else
	{
		$confirmation = " ";
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


// Confirmation Button was clicked
if(isset ($_POST['confirmation_btn']))
{	
	if(isset ($_POST['update_check']))
	{
		//checked
		$result  = $dbh->exec("UPDATE update_notice_conf SET confirmation='on';");
		$confirmation = "checked";
	}
	else
	{
		//unchecked
		$result  = $dbh->exec("UPDATE update_notice_conf SET confirmation='off';");
		$confirmation = " ";
	}	
	$alert_flag = "1";
}


	
// OK Button	was clicked
if(isset ($_POST['save_btn']))
{	
	
	
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
    <link rel="stylesheet" href="css/ladda-themeless.min.css" />
    <link rel="stylesheet" href="css/awesome-bootstrap-checkbox.css" />
    <link rel="stylesheet" href="css/sweetalert.css" />
    <link rel="stylesheet" href="css/ethertek.css">
    
    <!-- Java Scripts -->
		<script src="javascript/jquery.min.js"></script>
		<script src="javascript/jquery.form.js"></script>
		<script src="javascript/bootstrap.min.js"></script>
		<script src="javascript/spin.min.js"></script>
		<script src="javascript/ladda.min.js"></script>
		<script src="javascript/ladda.jquery.min.js"></script>
		<script src="javascript/sweetalert.min.js"></script>
		<script src="javascript/conhelp.js"></script>
		<script src="javascript/ethertek.js"></script>
		<script language="javascript" type="text/javascript">
			SetContext('firmware');
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
	SetContext('firmware');
</script>
<!-- Main Wrapper -->
<div id="wrapper">
	
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
    	<div class="col-sm-8">
      	<div class="hpanel4">
      		<div class="panel-body" style="max-width:600px">
      	  	<form name='FirmwareSetup' action='setup_firmware.php' method='post' class="form-horizontal">  	
      	    	<fieldset>
      	    		<legend><img src="images/srms2.gif"> Firmware Updates</legend> 
      	    		
      	    		<div class="form-group">
              		<div class="col-sm-12" style="max-width:270px">
              			<div class='checkbox checkbox-success'>
                    	<input type='checkbox' id='update_check' name='update_check' <?php echo $confirmation; ?> />
                    	<label for='update_check'>Enable check for firmware updates?</label>
                    </div>
              		</div>
              	</div>
              	<div class="row">
              		<div class="col-sm-12">
              			<button name="confirmation_btn" class="btn btn-success" type="submit" onMouseOver="mouse_move(&#039;b_apply&#039;);" onMouseOut="mouse_move();"> Apply</button>	
              		</div>
              	</div>
              	<hr>
              </fieldset>	
            </form>  	
              	
						<ul class="nav nav-tabs">
						  <li class="active"><a data-toggle="tab" href="#menu1">Root File System Upgrade</a></li>
						  <li><a data-toggle="tab" href="#menu2">Linux Kernel Upgrade</a></li>
						</ul>
						
						<div class="tab-content">
						  <div id="menu1" class="tab-pane fade in active">
						  	<br>
						    <h3>Root File System Upgrade</h3>
						    <p style="color:black">
						    	The Root File System Upgrade procedure will overwrite the Root File System 
						    	partition (/dev/mtd2) on the GMS-100 board.	
						    </p>
						    <p style="color:black">
						    	The GMS-100 board will retain the IP address and password it had prior 
						    	to the Root File System upgrade.
						    </p>
						    <p style="color:black">
						    	The GMS-100 SQL Database and the RRD Graph database will automatically be
						    	backed up and restored when the firmware update is complete.
						    </p>
						    <p style="color:blue">
						    	To upgrade, select the rootfs file by pressing &quot;Browse...&quot; and then 
						    	press &quot;Upgrade&quot;.
						    </p>
						    
						    <form name='UpgradeForm2' enctype='multipart/form-data' action='rootfsuploader.php' method='POST'>
						    	<p>
						    		<div class="input-group">
            					<label class="input-group-btn">
              					<span class="btn btn-primary">
                  				Browse&hellip; <input type="file" style="display: none;" name="rootfsfile" >
              					</span>
            					</label>
            					<input type="text" class="form-control" readonly>
            				</div>
						    		<div>
						    			<br>
						    			<button name="save_btn" class="ladda-button rfs btn btn-success"  data-style="zoom-in" value="Upgrade" type="submit"><i class="fa fa-check"></i> Upgrade</button>
						    			<button name="cancel_btn" class="btn btn-primary" type="submit" onMouseOver="mouse_move(&#039;b_cancel&#039;);" onMouseOut="mouse_move();" formnovalidate><i class="fa fa-times"></i> Cancel</button>
						    			<button name="autoupdate_btn" class="btn btn-info pull-right" type="submit" onMouseOver="mouse_move(&#039;autoupdate&#039;);" onMouseOut="mouse_move();" formnovalidate><i class="fa fa-upload"></i> Auto Update</button>
						    		</div>
										<br>
						    	</p>
						    	<p style="color:black">
						    		<strong style="color:red">IMPORTANT:</strong> After pressing the <strong style="color:green">&quot;Upgrade&quot;</strong> button, 
						    		the transfer process will start. It takes several minutes to tranfer the new firmware to the board. During this phase: Do <strong style="color:red">NOT</strong> close the browser, 
						    		Do <strong style="color:red">NOT</strong> press &quot;Back&quot;,
						    		Do <strong style="color:red">NOT</strong> press &quot;Cancel&quot;,
						    		Do <strong style="color:red">NOT</strong> press &quot;Stop&quot;. 
						    		Do <strong style="color:red">NOT</strong> reset or turn off the 
						    		RMS-100 board. The RMS-100 will restart automatically after the 
						    		upgrade is completed.
						    	</p>
						    </form>
						    <br>
						  </div>
						  
						  
						  <div id="menu2" class="tab-pane fade">
						  	<br>
						    <h3>Linux Kernel Upgrade</h3>
						    <p style="color:black">
						    	This operation will overwrite and upgrade the Linux Kernel partition (/dev/mtd1) 
						    	on the RMS-100 board. 
						    </p>
						    <p style="color:black">
						    	Files stored in the Root File System partition 
						    	(/dev/mtd2) will be untouched by the process, including the 
						    	RMS-100 SQL database, RRD Graph database, configuration files (under /etc), 
						    	and web pages (under /usr/local/webif).
						    </p>
						    <p style="color:black">
						    	The RMS-100 board will retain all settings it had prior to the Linux Kernel upgrade.
						    </p>
						    <p style="color:blue">
						    		To upgrade, select the uImage file by pressing &quot;Browse...&quot; and then 
						    		press &quot;Upgrade&quot;.
						    </p>
						    <form name='UpgradeForm1' enctype='multipart/form-data' action='kerneluploader.php' method='POST'>
						    	<p>
						    		<div class="input-group">
            					<label class="input-group-btn">
              					<span class="btn btn-primary">
                  				Browse&hellip; <input type="file" style="display: none;" name="kernelfile" >
              					</span>
            					</label>
            					<input type="text" class="form-control" readonly>
            				</div>
						    		<div>
						    			<br>
						    			<button name="save_btn" class="ladda-button kfs btn btn-success"  data-style="zoom-in" value="Upgrade" type="submit"><i class="fa fa-check"></i> Upgrade</button>
						    			<button name="cancel_btn" class="btn btn-primary" type="submit" onMouseOver="mouse_move(&#039;b_cancel&#039;);" onMouseOut="mouse_move();" formnovalidate><i class="fa fa-times"></i> Cancel</button>
						    		</div>
										<br>
						    	</p>
						    	<p style="color:black">
						    		<strong style="color:red">IMPORTANT:</strong> After pressing the <strong style="color:green">&quot;Upgrade&quot;</strong> button, 
						    		the transfer process will start. It takes several minutes to tranfer the new firmware to the board. During this phase: Do <strong style="color:red">NOT</strong> close the browser, 
						    		Do <strong style="color:red">NOT</strong> press &quot;Back&quot;,
						    		Do <strong style="color:red">NOT</strong> press &quot;Cancel&quot;,  
						    		Do <strong style="color:red">NOT</strong> press &quot;Stop&quot;. 
						    		Do <strong style="color:red">NOT</strong> reset or turn off the 
						    		RMS-100 board. The RMS-100 will restart automatically after the 
						    		upgrade is completed.
						    	</p>
						    </form>
						    <br>
						  </div>
						</div>
      		</div> <!-- END PANEL BODY --> 
      	</div> <!-- END PANEL WRAPPER --> 
      </div>  <!-- END COL --> 
    </div> <!-- END ROW --> 
  </div> <!-- END CONTENT -->    
</div> <!-- END Main Wrapper -->

<script>
	
		$(function() {

  // We can attach the `fileselect` event to all file inputs on the page
  $(document).on('change', ':file', function() {
    var input = $(this),
        numFiles = input.get(0).files ? input.get(0).files.length : 1,
        label = input.val().replace(/\\/g, '/').replace(/.*\//, '');
    input.trigger('fileselect', [numFiles, label]);
  });

  // We can watch for our custom `fileselect` event like this
  $(document).ready( function() {
      $(':file').on('fileselect', function(event, numFiles, label) {

          var input = $(this).parents('.input-group').find(':text'),
              log = numFiles > 1 ? numFiles + ' files selected' : label;

          if( input.length ) {
              input.val(log);
          } else {
              if( log ) alert(log);
          }

      });
  });

});


  
	// Bind progress buttons and simulate loading progress
			Ladda.bind( '.rfs', {
				callback: function( instance ) {
					var progress = 0;
					var interval = setInterval( function() {
						//progress = Math.min( progress + Math.random() * 0.1, 1 );
						progress = progress + 0.0010;
						
						instance.setProgress( progress );

						if( progress === 1 ) {
							instance.stop();
							clearInterval( interval );
						}
					}, 200 );
				}
			} );

			Ladda.bind( '.kfs', {
				callback: function( instance ) {
					var progress = 0;
					var interval = setInterval( function() {
						//progress = Math.min( progress + Math.random() * 0.1, 1 );
						progress = progress + 0.0150;
						
						instance.setProgress( progress );

						if( progress === 1 ) {
							instance.stop();
							clearInterval( interval );
						}
					}, 200 );
				}
			} );
</script>





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
?>
</body>
</html> 
