<?php
	error_reporting(E_ALL);
	include "lib.php";
	$hostname = trim(file_get_contents("/etc/hostname"));
	
	$alert_flag = "0";
	
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
		<script src="javascript/jquery-ui.min.js"></script>
		<script src="javascript/bootstrap.min.js"></script>
		<script src="javascript/spin.min.js"></script>
		<script src="javascript/ladda.min.js"></script>
		<script src="javascript/ladda.jquery.min.js"></script>
		<script src="javascript/sweetalert.min.js"></script>
		<script src="javascript/conhelp.js"></script>
		<script src="javascript/ethertek.js"></script>
		<script language="javascript" type="text/javascript">
			SetContext('database');
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
	SetContext('database');
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
      	  	<form name='DatabaseSetup' enctype='multipart/form-data' action='db_upgrade.php' method='post' class="form-horizontal">  	
      	    	<fieldset>
      	    		<legend><img src="images/db_backup.gif"> Backup SQL Database</legend> 
      	    		<div>
      	    			<a href="rms100.db" download="<?php echo $_SERVER['SERVER_ADDR']; ?>-<?php echo $hostname; ?>-rms100.db"><u class="dotted">Click to Download the SQL Database</u></a>
      	    		</div>
      	    		<br><br><br>
      	    		<legend><img src="images/db_restore.gif"> Restore SQL Database</legend> 
              	<p>
						    		<div class="input-group">
            					<label class="input-group-btn">
              					<span class="btn btn-primary">
                  				Browse&hellip; <input type="file" style="display: none;" name="dbfile" >
              					</span>
            					</label>
            					<input type="text" class="form-control" readonly>
            				</div>
						    		<div>
						    			<br>
						    			<button name="DB_Restore" class="ladda-button dbr btn btn-success"  data-style="zoom-in" type="submit"><i class="fa fa-check"></i> Restore</button>
						    			<button name="cancel_btn" class="btn btn-primary" type="submit" onMouseOver="mouse_move(&#039;b_cancel&#039;);" onMouseOut="mouse_move();" formnovalidate><i class="fa fa-times"></i> Cancel</button>
						    		</div>
										<br>
						    	</p>
						    	<p style="color:black">
						    		<strong style="color:red">IMPORTANT:</strong> After pressing the <strong style="color:green">&quot;Restore&quot;</strong> button, 
						    		the transfer process will start. During this phase: Do <strong style="color:red">NOT</strong> close the browser, 
						    		Do <strong style="color:red">NOT</strong> press &quot;Back&quot;,
						    		Do <strong style="color:red">NOT</strong> press &quot;Cancel&quot;,
						    		Do <strong style="color:red">NOT</strong> press &quot;Stop&quot;. 
						    		Do <strong style="color:red">NOT</strong> reset or turn off the 
						    		RMS-100 board. The RMS-100 will restart automatically after the 
						    		upgrade is completed.
						    	</p>
              	
              	
              	
              	
              	
              	
              	
							</fieldset>
						</form>
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
  
  // Bind progress buttons and simulate loading progress
			Ladda.bind( '.dbr', {
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
  
});


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
