<?php
	error_reporting(E_ALL);
	include "lib.php";
	$hostname = trim(file_get_contents("/etc/hostname"));
	$alert_flag = "0";
	$range = "12v";
	
	$dbh = new PDO('sqlite:/etc/rms100.db');
	$result  = $dbh->query("SELECT * FROM display_options;");			
	foreach($result as $row)
	{
		$screen_animations = $row['screen_animations'];
	}
	
	$result  = $dbh->query("SELECT * FROM device_mgr WHERE type='EFOY';");			
	foreach($result as $row)
	{
		$range = $row['sdvar1'];
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

// Cancel Button	was clicked
if(isset ($_POST['cancel_btn']))
{
	header("Location: setup.php");
}

	
// OK Button	was clicked
if(isset ($_POST['save_btn']))
{	
	//DO OK OR SAVE STUFF HERE
	
	$alert_flag = "1";
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
    <link rel="stylesheet" href="css/ethertek.css">
    
    <!-- Java Scripts -->
		<script src='javascript/bindows_gauges.js'></script>
		<script src="javascript/jquery.min.js"></script>
		<script src="javascript/bootstrap.min.js"></script>
		<script src="javascript/conhelp.js"></script>
		<script src="javascript/ethertek.js"></script>
		
		<script language="javascript" type="text/javascript">
			SetContext('efoy');
		</script>
		
</head>
<body class="fixed-navbar fixed-sidebar">
<div class='splash'> <div class='solid-line'></div><div class='splash-title'><h1>Loading... Please Wait...</h1><div class='spinner'> <div class='rect1'></div> <div class='rect2'></div> <div class='rect3'></div> <div class='rect4'></div> <div class='rect5'></div> </div> </div> </div>

<!--[if lt IE 7]>
<p class="alert alert-danger">You are using an <strong>old</strong> web browser. Please upgrade your browser to improve your experience.</p>
<![endif]-->

<?php start_header(); ?>

<?php left_nav("efoy"); ?>
<script language="javascript" type="text/javascript">
	SetContext('efoy');
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
    	<div class="col-sm-12">
      	<div class="hpanel4">
      		<div class="panel-body" style="max-width:550px">
      	  	<form name='CUSTOM' action='custom_device_template.php' method='post' class="form-horizontal">  	
      	    	<fieldset>
      	    		<legend><img src="images/efoy.gif"> Efoy Monitor</legend>
      	    		
      	    		<div class="form-group"><label class="col-sm-4 control-label" style="text-align:left; color:blue;">Hours of Operation:</label>
              		<div class="col-sm-8">
              			<label class="control-label" id="ot" name="ot"></label>
              		</div>
              	</div>
      	    		
      	    		<div class="form-group"><label class="col-sm-4 control-label" style="text-align:left; color:blue;">Operating State:</label>
              		<div class="col-sm-8">
              			<label class="control-label" id="os" name="os"></label>
              		</div>
              	</div>
      	    		
      	    		<div class="form-group"><label class="col-sm-4 control-label" style="text-align:left; color:blue;">Operating Mode:</label>
              		<div class="col-sm-8">
              			<label class="control-label" id="mode" name="mode"></label>
              		</div>
              	</div>
      	    		
      	    		<div class="form-group"><label class="col-sm-4 control-label" style="text-align:left; color:blue;">Cumulative Output:</label>
              		<div class="col-sm-8">
              			<label class="control-label" id="coe" name="coe"></label>
              		</div>
              	</div>
      	    		
      	    		<div class="form-group"><label class="col-sm-4 control-label" style="text-align:left; color:blue;">Error Message:</label>
              		<div class="col-sm-8">
              			<label class="control-label" id="error" name="error"></label>
              		</div>
              	</div>
      	    		
      	    		<div class="form-group">
              		<div class="col-sm-12">
              			<label class="control-label" id="msg" name="msg" style="text-align:left"></label>
              		</div>
              	</div>
      	    		
      	    		<table width="100%" border="0">
      	    			<tbody>
      	    				<tr>
      	    					<td>
      	    						<div id="volts" style="width:250px; height:250px"></div>
      	    					</td>
      	    					<td>
      	    						<div id="amps" style="width:250px; height:250px"></div>
      	    					</td>
      	    				</tr>	
      	    			</tbody>
      	    		</table>
              		
              	<div class="form-group">
              		<div class="col-sm-12" style="text-align:center;color:green">
              			<label class="control-label">Values Refresh Every 10 Seconds.</label>
              		</div>
              	</div>
              	
              </fieldset>  
						</form>  	
      		</div> <!-- END PANEL BODY --> 
      	</div> <!-- END PANEL WRAPPER --> 
      </div>  <!-- END COL --> 
    </div> <!-- END ROW --> 
  </div> <!-- END CONTENT -->    
</div> <!-- END Main Wrapper -->

<script language="javascript" type="text/javascript">
<?php
	if($range == "12v")
	{
		echo"var volts = bindows.loadGaugeIntoDiv('efoy_12volts.xml', 'volts');";
	}
	else
	{
		echo"var volts = bindows.loadGaugeIntoDiv('efoy_24volts.xml', 'volts');";
	}
?>	
var amps = bindows.loadGaugeIntoDiv('efoy_amps.xml', 'amps');
function display_gauge()
{
     var myRandom = parseInt(Math.random()*999999999);
     $.getJSON('efoy_server.php?element=efoyall&rand=' + myRandom,     
     function(data)      
     {  
     		setTimeout(display_gauge, 10000);
			  var v = (data.efoy.volts);
			  volts.needle.setValue(v);
			  volts.label.setText(v);
			  
			  var a = (data.efoy.amps);
			  amps.needle.setValue(a);
			  amps.label.setText(a);
			  
			  var ot = (data.efoy.o_time);
	      	{
	        	$('#ot').replaceWith("<label class='control-label' id='ot' name='ot'>" + ot + "</label>");
	        }
			  var os = (data.efoy.o_state);
	      	{
	        	if(os.indexOf("error") != -1)
	        		{
	        			$('#os').replaceWith("<label class='control-label' id='os' name='os' style='color:red'>" + os + "</label>");
	        		}
	        	else
	        		{
	        			$('#os').replaceWith("<label class='control-label' id='os' name='os' style='color:green'>" + os + "</label>");
	        		}
	        }
	      var mode = (data.efoy.mode);
	      	{
	        	$('#mode').replaceWith("<label class='control-label' id='mode' name='mode'>" + mode + "</label>");
	        }  
	      var coe = (data.efoy.coe);
	      	{
	        	$('#coe').replaceWith("<label class='control-label' id='coe' name='coe'>" + coe + "</label>");
	        } 
	      var error = (data.efoy.error);
	      	{
	        	if(error.indexOf("no error") == -1)
	        		{
	        			$('#error').replaceWith("<label class='control-label' id='error' name='error' style='color:red'>" + error + "</label>");
	        		}
	        	else
	        		{
	        			$('#error').replaceWith("<label class='control-label' id='error' name='error' style='color:green'>" + error + "</label>");
	        		}
	        }  
	      var msg = (data.efoy.msg);
	      	{
	        	$('#msg').replaceWith("<label class='control-label' id='msg' name='msg'>" + msg + "</label>");
	        } 

      }
    );
}
display_gauge();
</script>
</body>
</html> 
