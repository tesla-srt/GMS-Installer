<?php
	//error_reporting(E_ALL);
	include "lib.php";
	$hostname = trim(file_get_contents("/etc/hostname"));
	
	$dbh = new PDO('sqlite:/etc/rms100.db');
	
	$result  = $dbh->query("SELECT * FROM display_options;");			
	foreach($result as $row)
	{
		$screen_animations = $row['screen_animations'];
	}
	
	$alert_flag = "0";
	$timespan_view = "";
	$graph_width = "";
	$graph_height = "";
	
	
/////////////////////////////////////////////////////////////////
//                                                             //
//                    GET PROCESSING                           //
//                                                             //
/////////////////////////////////////////////////////////////////

$result  = $dbh->query("SELECT * FROM global_graph_opts;");
foreach($result as $row)
{
	$timespan_view = $row['timespan_view'];
	$graph_width = $row['graph_width'];
	$graph_height = $row['graph_height'];
}

if(isset ($_GET['confirm']))
{
	$action = $_GET['confirm'];
	if($action == "reset")
	{
		$the_date = strftime( "%b-%d-%Y");
		exec("/etc/init.scripts/S91rmsrrdd stop > /dev/null");
		sleep(2);
		$file_buff = sprintf("mv /data/rrd/tmp/rms.rrd /data/rrd/rms.rrd.old-%s",$the_date);
		exec($file_buff);
		exec("rm /data/rrd/rms.rrd");
		exec("rm /data/rrd/tmp/rms.rrd");
		exec("rm /data/rrd/tmp/vm1*");
		exec("rm /data/rrd/tmp/vm2*");
		exec("rm /data/rrd/tmp/vm3*");
		exec("rm /data/rrd/tmp/vm4*");
		exec("rm /data/rrd/tmp/vm5*");
		exec("rm /data/rrd/tmp/vm6*");
		exec("rm /data/rrd/tmp/load*");
		exec("rm /data/rrd/tmp/temp*");
		exec("/etc/init.scripts/S91rmsrrdd start > /dev/null");
		$alert_flag = "2";
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
	header("Location: setup.php");
}

	
// OK Button	was clicked
if(isset ($_POST['save_btn']))
{	
	$timespan_view = $_POST['timespan_view'];
	$graph_width = $_POST['graph_width'];
	$graph_height = $_POST['graph_height'];
	$query = sprintf("UPDATE global_graph_opts SET timespan_view='%s', graph_width='%s', graph_height='%s';", $timespan_view,$graph_width,$graph_height);
	$result  = $dbh->exec($query);
	$alert_flag = "1";
}

// RESET Button	was clicked
if(isset ($_POST['reset_btn']))
{	
	$alert_flag = "3";
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
    <link rel="stylesheet" href="css/jquery.bootstrap-touchspin.min.css" />
    <link rel="stylesheet" href="css/sweetalert.css" />
    <link rel="stylesheet" href="css/ethertek.css">
    
    <!-- Java Scripts -->
		<script src="javascript/jquery.min.js"></script>
		<script src="javascript/jquery-ui.min.js"></script>
		<script src="javascript/bootstrap.min.js"></script>
		<script src="javascript/spin.min.js"></script>
		<script src="javascript/jquery.bootstrap-touchspin.min.js"></script>
		<script src="javascript/sweetalert.min.js"></script>
		<script src="javascript/ladda.min.js"></script>
		<script src="javascript/ladda.jquery.min.js"></script>
		<script src="javascript/conhelp.js"></script>
		<script src="javascript/ethertek.js"></script>
		<script language="javascript" type="text/javascript">
			SetContext('graph_options');
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
	SetContext('graph_options');
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
    	<div class="col-sm-4">
      	<div class="hpanel4">
      		<div class="panel-body" style="max-width:600px">
      	  	<form name='GraphOptions' action='setup_graph_options.php' method='post' class="form-horizontal">  	
      	    	<fieldset>
      	    		<legend><img src="images/graph-32x32.gif"> Graph Options</legend> 
      	    		
      	    		<div class="form-group"><label class="col-sm-4 control-label" style="text-align:left">Default Graph View:</label>
              		<div class="col-sm-8" style="max-width:130px">
              			<select class="form-control" name="timespan_view">
              				<?php
              					if($timespan_view == "hour")
              					{
              						echo '<option selected value="hour">Hour</option>';
              					}
              					else
              					{
              						echo '<option value="hour">Hour</option>';
              					}
              					
              					if($timespan_view == "day")
              					{
              						echo '<option selected value="day">Day</option>';
              					}
              					else
              					{
              						echo '<option value="day">Day</option>';
              					}
              					
              					if($timespan_view == "week")
              					{
              						echo '<option selected value="week">Week</option>';
              					}
              					else
              					{
              						echo '<option value="week">Week</option>';
              					}
              				
              					if($timespan_view == "month")
              					{
              						echo '<option selected value="month">Month</option>';
              					}
              					else
              					{
              						echo '<option value="month">Month</option>';
              					}
              				
              					if($timespan_view == "year")
              					{
              						echo '<option selected value="year">Year</option>';
              					}
              					else
              					{
              						echo '<option value="year">Year</option>';
              					}
              					
              					if($timespan_view == "5year")
              					{
              						echo '<option selected value="5year">5 Year</option>';
              					}
              					else
              					{
              						echo '<option value="5year">5 Year</option>';
              					}
              					
              					if($timespan_view == "10year")
              					{
              						echo '<option selected value="10year">10 Year</option>';
              					}
              					else
              					{
              						echo '<option value="10year">10 Year</option>';
              					}
              					
              				?>
              			</select>	
              		</div>
              	</div>
      	    		
      	    		<div class="form-group"><label class="col-sm-4 control-label" style="text-align:left">Graph Width:</label>
              		<div class="col-sm-8" style="max-width:180px" onMouseOver="mouse_move('sd_timers_info');" onMouseOut="mouse_move();">
              			<input id="graph_width" type="text" name="graph_width" style="text-align:center" value="<?php echo $graph_width; ?>">
              		</div>
              	</div>
      	    		
      	    		<div class="form-group"><label class="col-sm-4 control-label" style="text-align:left">Graph Height:</label>
              		<div class="col-sm-8" style="max-width:180px" onMouseOver="mouse_move('sd_timers_info');" onMouseOut="mouse_move();">
              			<input id="graph_height" type="text" name="graph_height" style="text-align:center" value="<?php echo $graph_height; ?>">
              		</div>
              	</div>
              	
              	<div class="form-group">
              		<div class="col-sm-12">
              			<button name="save_btn" class="btn btn-success" type="submit" onMouseOver="mouse_move(&#039;sd_default_graph_view&#039;);" onMouseOut="mouse_move();"><i class="fa fa-check" ></i> Save</button>
              		</div>
              	</div>
              	
              	<legend>Reset Graphs</legend>
              	<p>
              		Click the RESET button below to reset the graph data back to factory default.<br>
									The old RRD graph database will be renamed to /data/rrd/rms.rrd.old-date 
              	</p>
              	
              	<div class="form-group">
              		<div class="col-sm-12">
              			<button name="reset_btn" class="btn btn-danger" type="submit" onMouseOver="mouse_move(&#039;voltmeter_graphs_reset&#039;);" onMouseOut="mouse_move();"><i class="fa fa-exclamation" ></i> Reset</button>
              			<button name="cancel_btn" class="btn btn-primary" type="submit" onMouseOver="mouse_move(&#039;b_cancel&#039;);" onMouseOut="mouse_move();" formnovalidate><i class="fa fa-times"></i> Cancel</button>
              		</div>
              	</div>
              	<hr>
              	<table width="100%" class="table-responsive">
              		<tr>
              			<td width="15%"><img src="images/db_backup.gif"></td>
              			<td width="85%"><a href="rrd/tmp/rms.rrd"><h5><u class="dotted" style="color:black"> Download RMS-100 RRD Graph Database</u></h5></a></td>
              		</tr>
              	</table>
              	
              	<hr>
              	
              	<table width="100%" class="table-responsive">
              		<tr>
              			<td width="15%"><img src="images/db_restore.gif"></td>
              			<td width="85%" style="color:black"><h5> Restore RMS-100 RRD Graph Database</h5></td>
              		</tr>
              	</table>
              </fieldset>  
						</form> 
						
						<form name='db' enctype='multipart/form-data' action='rrduploader.php' method='POST'>
							<fieldset>
						  	<p>
						  		<div class="input-group">
            				<label class="input-group-btn">
              				<span class="btn btn-primary">
              					Browse&hellip; <input type="file" style="display: none;" name="rrdfile" >
              				</span>
            				</label>
            				<input type="text" class="form-control" readonly>
            			</div>
						  		<div>
						  			<br>
						  			<button name="RRD_Restore" class="ladda-button rrd btn btn-success"  data-style="zoom-in" value="Restore" type="submit"><i class="fa fa-check"></i> Restore</button>
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
						  		RMS-100 board.
						  	</p>
						   </fieldset>
						</form>
						<br>	 	
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
			Ladda.bind( '.rrd', {
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


<script>
$(function(){

    $("#graph_width").TouchSpin({
        min: 10,
        max: 4000,
        step: 10,
        decimals: 0,
        boostat: 5,
        maxboostedstep: 10,
    });
    
    $("#graph_height").TouchSpin({
        min: 10,
        max: 4000,
        step: 10,
        decimals: 0,
        boostat: 5,
        maxboostedstep: 10,
    });

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

if($alert_flag == "2")
{
echo"<script>";
echo"swal({";
echo"  title:'Success!',";
echo"  text: 'Graphs Reset!',";
echo"  type: 'success',";
echo"  showConfirmButton: false,";
echo"  timer: 2000";
echo"});";
echo"</script>";
}

if($alert_flag == "3")
{
	echo"<script>";
	echo"	swal({";
	echo"		title: 'Reset Graph Data<br>Are you really sure?',";
	echo"		type: 'warning',";
	echo"		showCancelButton: true,";
	echo"		html: true,";
	echo"		confirmButtonColor: '#DD6B55',";
	echo"		confirmButtonText: 'Yes, do it!',";
	echo"		closeOnConfirm: false";
	echo"	},";
	echo"	function(){";
	echo"		window.location.href = 'setup_graph_options.php?confirm=reset';";
	echo"	});";
	echo"</script>";
}



?>
</body>
</html> 
