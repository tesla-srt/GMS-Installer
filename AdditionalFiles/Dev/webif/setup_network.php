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
	
	$result  = $dbh->query("SELECT * FROM throttle;");			
	foreach($result as $row)
	{
		$dt = $row['delay'];
	}
	
	$alert_flag = "0";
	$sd_errmsg2 = "";
	$ip_address = "";
	$subnet_mask = "";
	$gateway = "";
	$dns1 = "";
	$dns2 = "";
	$mac = "";
	$station_name = "";
	$location = "";
	$domain = "";
	$vlanid = "0";
	$vlanmtu = "1478";
	$vlan_enabled = "no";
	$command = "";
	
/////////////////////////////////////////////////////////////////
//                                                             //
//                    GET PROCESSING                           //
//                                                             //
/////////////////////////////////////////////////////////////////
if($_SERVER['REQUEST_METHOD'] == "GET")
{
	
	$result  = $dbh->query("SELECT syslocation FROM snmp_config;");			
	foreach($result as $row)
		{
			$location = $row['syslocation'];
		}
	system("cat /etc/resolv.conf |grep search | cut -d ' ' -f2 > /tmp/domain.txt");
	$domain = file_get_contents("/tmp/domain.txt"); unlink("/tmp/domain.txt");
	$station_name = trim(file_get_contents("/etc/hostname"));
	$ip_address = $_SERVER['SERVER_ADDR'];
	$mac = file_get_contents("/var/macaddress");
	$mac = str_replace("\n", '', $mac); // remove new lines
	system("/sbin/ifconfig eth0 | grep 'inet addr:' | cut -d: -f4 | awk '{ print $1}' > /tmp/sm.txt");
	$subnet_mask = file_get_contents("/tmp/sm.txt"); unlink("/tmp/sm.txt");
	system("ip route show default | awk '/default/ {print $3}' > /tmp/gw.txt");
	$gateway = file_get_contents("/tmp/gw.txt"); unlink("/tmp/gw.txt");
	
	system("cat /etc/resolv.conf | awk 'FNR == 2 {print $2}' > /tmp/d1.txt");	
	$dns1 = file_get_contents("/tmp/d1.txt"); unlink("/tmp/d1.txt");
	system("cat /etc/resolv.conf | awk 'FNR == 3 {print $2}' > /tmp/d2.txt");	
	$dns2 = file_get_contents("/tmp/d2.txt"); unlink("/tmp/d2.txt");
	
	if(file_exists("/etc/vlanid"))
	{
		$vlan_enabled = "yes";
		$vlanid = file_get_contents("/etc/vlanid");
	}
	else
	{
		$vlan_enabled = "no";
		$vlanid = 0;
	}
	
	if(file_exists("/etc/vlanmtu"))
	{
		$vlanmtu = file_get_contents("/etc/vlanmtu");
	}
	else
	{
		file_put_contents("/etc/vlanmtu", $vlanmtu);
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
	header("Location: setup.php?context=setup");
}

	
// OK Button	was clicked
if(isset ($_POST['save_btn']))
{	

	$ip_address = $_POST["ip_address"];
	if(!filter_var($ip_address, FILTER_VALIDATE_IP)) 
	{
   $alert_flag = "2";
	}
	$subnet_mask = $_POST["subnet_mask"];
	if(!filter_var($subnet_mask, FILTER_VALIDATE_IP)) 
	{
   $alert_flag = "3";
	}
	$gateway = $_POST["gateway"];
	if(!filter_var($gateway, FILTER_VALIDATE_IP)) 
	{
   $alert_flag = "4";
	}
	$dns1 = $_POST["dns1"];
	if(!filter_var($dns1, FILTER_VALIDATE_IP)) 
	{
   $alert_flag = "5";
	}
	$dns2 = $_POST["dns2"];
	if(!filter_var($dns2, FILTER_VALIDATE_IP)) 
	{
   $alert_flag = "6";
	}
	
	//Check Broadcast subnetting
	$command = sprintf("ipcalc -b %s %s > /tmp/bc",$ip_address,$subnet_mask);
	system($command);
	system("cat /tmp/bc |grep BROADCAST | sed s/BROADCAST=// > /tmp/bc1");
	$broadcast = file_get_contents("/tmp/bc1");
	if(strlen($broadcast < 7))
	{
		$alert_flag = "7";
	}
	$broadcast = str_replace("\n", '', $broadcast); // remove new lines
	unlink("/tmp/bc");
	unlink("/tmp/bc1");
	
	//Check Network subnetting
	$command = sprintf("ipcalc -n %s %s > /tmp/nw",$ip_address,$subnet_mask);
	system($command);
	system("cat /tmp/nw |grep NETWORK | sed s/NETWORK=// > /tmp/nw1");
	$network = file_get_contents("/tmp/nw1");
	if(strlen($network < 7))
	{
		$alert_flag = "7"; //reuse alert 7
	}
	$network = str_replace("\n", '', $network); // remove new lines
	unlink("/tmp/nw");
	unlink("/tmp/nw1");
	
	$mac = file_get_contents("/var/macaddress");
	$mac = str_replace("\n", '', $mac); // remove new lines
	$station_name = $_POST["station_name"];
	if(is_valid_domain_name($station_name) != 1)
		{
			$alert_flag = "10";
		}
	$location = $_POST["location"];
	$domain = $_POST["domain"];
	$vlanmtu = $_POST['vlanmtu'];
	file_put_contents("/etc/vlanmtu",$vlanmtu);
	if(isset ($_POST['vlan']))
	{
		$vlan_enabled = "yes";
		$vlanid = $_POST['vlanid'];
	}
	else
	{
		$vlan_enabled = "no";
		$vlanid = 0;
	}
	
	if($alert_flag == 0)
	{
		$alert_flag = "1";
		
		$file = fopen("/tmp/interfaces.new","w");
		fputs($file,"# Configure Loopback\n");
		fputs($file,"auto lo\n");
		fputs($file,"iface lo inet loopback\n");
		fputs($file,"# Configure eth0\n");
		fputs($file,"auto eth0\n");
		fputs($file,"iface eth0 inet static\n");
		fprintf($file,"address %s\n",$ip_address);
		fprintf($file,"network %s\n",$network);
		fprintf($file,"netmask %s\n",$subnet_mask);
		fprintf($file,"broadcast %s\n",$broadcast);
		fprintf($file,"gateway %s\n",$gateway);
		fclose($file);
		
		$file = fopen("/tmp/resolv.conf.new", "w");
		fprintf($file, "search %s\n",$domain);
		fprintf($file, "nameserver %s\n",$dns1);
		fprintf($file, "nameserver %s\n\n",$dns2);
		fclose($file);
		system("mv /tmp/resolv.conf.new /etc/resolv.conf");
		system("mv /tmp/interfaces.new /etc/network/interfaces");
		
		$file = fopen("/tmp/hostname.new", "w");
		fprintf($file, "%s\n",$station_name);
		fclose($file);
		system("mv /tmp/hostname.new /etc/hostname");
		$command = sprintf("hostname %s",$station_name);
		system($command);
		
		$file = fopen("/tmp/hosts.new", "w");
		fprintf($file, "127.0.0.1\tlocalhost\n");
		fprintf($file, "%s\t%s\n",$ip_address,$station_name);
		fclose($file);
		system("mv /tmp/hosts.new /etc/hosts");
		
		$result  = $dbh->query("SELECT * FROM snmp_config WHERE id='1';");			
		foreach($result as $row)
		{
			$username = $row['username'];
			$password = $row['plain_password'];		
		}
		
		if($vlan_enabled =="yes")
		{
			//create file
			file_put_contents("/etc/vlanid",$vlanid);
		}
		else
		{
			if(file_exists("/etc/vlanid")){unlink("/etc/vlanid");}
		}
		
		//Determine SNMP version
		$search = 'rwuser ';
		$lines = file('/etc/snmp/snmpd.conf');
		
		foreach($lines as $line)
		{
		  if(strpos($line, $search) !== false)
		  {
		    if($line[0] == "#")
		    {
		    	$snmpv = "1";
		    	//echo "SNMPv1 ".$line;
		    	break;
		    }
		    else
		    {
		    	$snmpv = "3";
		    	$tmp = explode(" ",$line);
		    	$username = trim($tmp[1]);
		    	//echo "SNMPv3 Username ".$username;
		    	break;
		    }
		  }
		}
		
		if($snmpv == "1")
		{
			//Determine rw Password
			$search = 'rwcommunity ';
			$lines = file('/etc/snmp/snmpd.conf');
			foreach($lines as $line)
			{
				if(strpos($line, $search) !== false)
				{
			  	$tmp = explode(" ",$line);
			  	$rwcommunity = trim($tmp[1]);
				}
			}
			$command = sprintf("/usr/bin/snmpset -v 1 -c %s localhost sysLocation.0 s \"%s\" > /dev/null",$rwcommunity,$location);
			system($command);
		}
		else
		{
			$command = sprintf("/usr/bin/snmpset -v 3 -u %s -l authNoPriv -a MD5 -A %s localhost .1.3.6.1.2.1.1.6.0 s \"%s\" > /dev/null",$username,$password,$location);
			system($command);
		}
		
		$result  = $dbh->exec("UPDATE snmp_config SET syslocation='" . $location . "' WHERE id='1';"); 
		
		$result  = $dbh->exec("UPDATE throttle SET delay='" . $dt . "';"); 
			
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
    <link rel="stylesheet" href="css/jquery.bootstrap-touchspin.min.css" />
		<link rel="stylesheet" href="css/awesome-bootstrap-checkbox.css" />
    <link rel="stylesheet" href="css/sweetalert.css" />
    <link rel="stylesheet" href="css/ethertek.css">
    
    <!-- Java Scripts -->
		<script src="javascript/jquery.min.js"></script>
		<script src="javascript/jquery-ui.min.js"></script>
		<script src="javascript/bootstrap.min.js"></script>
		<script src="javascript/jquery.bootstrap-touchspin.min.js"></script>
		<script src="javascript/sweetalert.min.js"></script>
		<script src="javascript/conhelp.js"></script>
		<script src="javascript/ethertek.js"></script>
		<script language="javascript" type="text/javascript">
			SetContext('setup');
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
	SetContext('setup');
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
      	  	<form name='NetworkSetup' action='setup_network.php' method='post' class="form-horizontal">  	
      	    	<fieldset>
      	    		<legend><img src="images/network_setup.gif"> Network IP Options</legend>
      	    		<div class="form-group"><label class="col-sm-3 control-label" style="text-align:left; min-width:140px; max-width:140px">IP Address:</label>
              		<div class="col-sm-6" style="min-width:270px; max-width:270px">
              			<input type="text" class="form-control input-sm" name='ip_address' value='<?php echo $ip_address; ?>' required />
              		</div>
              	</div>	    	    	
								
								<div class="form-group"><label class="col-sm-3 control-label" style="text-align:left; min-width:140px; max-width:140px">Subnet Mask:</label>
              		<div class="col-sm-6" style="min-width:270px; max-width:270px">
              			<input type="text" class="form-control input-sm" name='subnet_mask' value='<?php echo $subnet_mask; ?>' required />
              		</div>
              	</div>
	            	
								<div class="form-group"><label class="col-sm-3 control-label" style="text-align:left; min-width:140px; max-width:140px">Gateway:</label>
              		<div class="col-sm-6" style="min-width:270px; max-width:270px">
              			<input type="text" class="form-control input-sm" name='gateway' value='<?php echo $gateway; ?>' required />
              		</div>
              	</div>
              	
              	<div class="form-group"><label class="col-sm-3 control-label" style="text-align:left; min-width:140px; max-width:140px">DNS Server 1:</label>
              		<div class="col-sm-6" style="min-width:270px; max-width:270px">
              			<input type="text" class="form-control input-sm" name='dns1' value='<?php echo $dns1; ?>' required />
              		</div>
              	</div>
              	
              	<div class="form-group"><label class="col-sm-3 control-label" style="text-align:left; min-width:140px; max-width:140px">DNS Server 2:</label>
              		<div class="col-sm-6" style="min-width:270px; max-width:270px">
              			<input type="text" class="form-control input-sm" name='dns2' value='<?php echo $dns2; ?>' required />
              		</div>
              	</div>
              	
              	<div class="form-group"><label class="col-sm-3 control-label" style="text-align:left; min-width:140px; max-width:140px">Mac Address:</label>
              		<div class="col-sm-6" style="min-width:270px; max-width:270px">
              			<input type="text" class="form-control input-sm" name='mac' value='<?php echo $mac; ?>' disabled />
              		</div>
              	</div>
              
              	<legend>General Network Options</legend>
              	
              	<div class="form-group"><label class="col-sm-3 control-label" style="text-align:left; min-width:140px; max-width:140px">Station Name:</label>
              		<div class="col-sm-6" style="min-width:270px; max-width:270px">
              			<input type="text" class="form-control input-sm" name='station_name' value='<?php echo $station_name; ?>' required />
              		</div>
              	</div>
              	
              	<div class="form-group"><label class="col-sm-3 control-label" style="text-align:left; min-width:140px; max-width:140px">Location:</label>
              		<div class="col-sm-6" style="min-width:270px; max-width:270px">
              			<input type="text" class="form-control input-sm" name='location' value='<?php echo $location; ?>' required />
              		</div>
              	</div>
              	
              	<div class="form-group"><label class="col-sm-3 control-label" style="text-align:left; min-width:140px; max-width:140px">Domain Name:</label>
              		<div class="col-sm-6" style="min-width:270px; max-width:270px">
              			<input type="text" class="form-control input-sm" name='domain' value='<?php echo $domain; ?>' required />
              		</div>
              	</div>
              	
              	<legend>Vlan Tagging Options</legend>
              	
              	<div class="form-group"><label class="col-sm-3 control-label" style="text-align:left; min-width:140px; max-width:140px">Vlan Enabled?</label>
              		<div class="col-sm-6" style="min-width:190px; max-width:190px">
              			<div class="checkbox checkbox-success">
              				 <?php
                    		if($vlan_enabled == "yes")
              					{
              						echo "<input id='vlan' type='checkbox' name='vlan' checked>";
              					}
                    		else
                    		{
                    			echo "<input id='vlan' type='checkbox' name='vlan'>";
                    		}
                       ?>  
                      <label for="vlan"></label>     
                    </div>
              		</div>
              	</div>
              			
              	<div class="form-group"><label class="col-sm-3 control-label" style="text-align:left; min-width:140px; max-width:140px">Vlan ID:</label>
              		<div class="col-sm-6" style="min-width:150px; max-width:150px" onMouseOver="mouse_move('sd_timers_info');" onMouseOut="mouse_move();">
              			<input class="form-control input-sm" id="vlanid" type="text" name="vlanid" style="text-align:center" value="<?php echo $vlanid; ?>">
              		</div>
              	</div>
              	
              	<div class="form-group"><label class="col-sm-3 control-label" style="text-align:left; min-width:140px; max-width:140px">Vlan MTU:</label>
              		<div class="col-sm-6" style="min-width:150px; max-width:150px" onMouseOver="mouse_move('sd_timers_info');" onMouseOut="mouse_move();">
              			<input class="form-control input-sm" id="vlanmtu" type="text" name="vlanmtu" style="text-align:center" value="<?php echo $vlanmtu; ?>">
              		</div>
              	</div>
              	
              	<legend>Web Browser Data Throttling (ms)</legend>
              	
              	<div class="form-group"><label class="col-sm-3 control-label" style="text-align:left; min-width:140px; max-width:140px">Data Throttling:</label>
              		<div class="col-sm-6" style="min-width:150px; max-width:150px" onMouseOver="mouse_move('sd_timers_info');" onMouseOut="mouse_move();">
              			<input class="form-control input-sm" id="dt" type="text" name="dt" style="text-align:center" value="<?php echo $dt; ?>">
              		</div>
              	</div>
              	
              	<div class="form-group">
              		<div class="col-sm-8 col-sm-offset-3">
              	  	<button name="save_btn" class="btn btn-success " type="submit" onMouseOver="mouse_move(&#039;b_apply&#039;);" onMouseOut="mouse_move();"><i class="fa fa-check"></i> Save</button>
              	  	<button name="cancel_btn" class="btn btn-primary" type="submit" onMouseOver="mouse_move(&#039;b_cancel&#039;);" onMouseOut="mouse_move();" formnovalidate><i class="fa fa-times"></i> Cancel</button>
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

<script>
$(function(){
    $("#vlanid").TouchSpin({
        min: 0,
        max: 4096,
        step: 1,
        decimals: 0,
        boostat: 5,
        maxboostedstep: 10,
    });
});

$(function(){
    $("#vlanmtu").TouchSpin({
        min: 400,
        max: 1522,
        step: 1,
        decimals: 0,
        boostat: 5,
        maxboostedstep: 10,
    });
});

$(function(){
    $("#dt").TouchSpin({
        min: 1000,
        max: 60000,
        step: 1,
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
echo"	swal({";
echo"		title: 'Success!',";
echo"  	text: 'Settings Saved! Reboot for settings to take effect.',";
echo"  	type: 'success',";
echo"		showCancelButton: true,";
echo"		cancelButtonText: 'Reboot Later',";
echo"		html: true,";
echo"		confirmButtonColor: '#DD6B55',";
echo"		confirmButtonText: 'Reboot Now!',";
echo"		closeOnConfirm: false";
echo"	},";
echo"	function(){";
echo"		window.location.href = 'setup_power.php?confirm=restart';";
echo"	});";
echo"</script>";
}

if($alert_flag == "2")
{
echo"<script>";
echo"swal({";
echo"  title:'Error!',";
echo"  text: 'Invalid IP Address Entered!',";
echo"  type: 'error',";
echo"  confirmButtonText: 'OK'";
echo"});";
echo"</script>";
}

if($alert_flag == "3")
{
echo"<script>";
echo"swal({";
echo"  title:'Error!',";
echo"  text: 'Invalid Subnet Mask Entered!',";
echo"  type: 'error',";
echo"  confirmButtonText: 'OK'";
echo"});";
echo"</script>";
}

if($alert_flag == "4")
{
echo"<script>";
echo"swal({";
echo"  title:'Error!',";
echo"  text: 'Invalid Gateway IP Entered!',";
echo"  type: 'error',";
echo"  confirmButtonText: 'OK'";
echo"});";
echo"</script>";
}

if($alert_flag == "5")
{
echo"<script>";
echo"swal({";
echo"  title:'Error!',";
echo"  text: 'Invalid DNS Server 1 IP Entered!',";
echo"  type: 'error',";
echo"  confirmButtonText: 'OK'";
echo"});";
echo"</script>";
}

if($alert_flag == "6")
{
echo"<script>";
echo"swal({";
echo"  title:'Error!',";
echo"  text: 'Invalid DNS Server 2 IP Entered!',";
echo"  type: 'error',";
echo"  confirmButtonText: 'OK'";
echo"});";
echo"</script>";
}

if($alert_flag == "7")
{
echo"<script>";
echo"swal({";
echo"  title:'Error!',";
echo"  text: 'Bad IP / Netmask Combination Entered!',";
echo"  type: 'error',";
echo"  confirmButtonText: 'OK'";
echo"});";
echo"</script>";
}

if($alert_flag == "10")
{
echo"<script>";
echo"swal({";
echo"  title:'Error!',";
echo"  text: 'Invalid Station Name! Only alpha numeric characters, period, and dash allowed!',";
echo"  type: 'error',";
echo"  confirmButtonText: 'OK'";
echo"});";
echo"</script>";
}

?>
</body>
</html> 
