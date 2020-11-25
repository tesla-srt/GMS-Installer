<?php
	//error_reporting(E_ALL);
	include_once "mattLib/Utils.php";

	function is_valid_domain_name($domain_name)
	{
		return (preg_match("/^([a-z\d](-*[a-z\d])*)(\.([a-z\d](-*[a-z\d])*))*$/i", $domain_name) //valid chars check
				&& preg_match("/^.{1,253}$/", $domain_name) //overall length check
				&& preg_match("/^[^\.]{1,63}(\.[^\.]{1,63})*$/", $domain_name)   ); //length of each label
	}	
    
    $first_run_bool = isFirstRun();
    function updateEmailAlertSubjects($hostName, $dbh)
    {
        $alertSubjects[0] = "FAULT - Tamper Event"; //4 
        $alertSubjects[1] = "OPEN - Door Contact Event"; //5
        $alertSubjects[2] = "FAULT - Surge Protection Event"; //6
        $alertSubjects[3] = "FAULT - PoE Switch Event"; //7
        $alertSubjects[4] = "POWER OUT - System Event"; //8
        $alertSubjects[5] = "LOW POWER - Battery Bank Event"; //9
        $alertSubjects[6] = "POWER OUT - PoE Switch Event"; //10
        $alertSubjects[7] = "POWER RESTORED - System Event"; //11
        $alertSubjects[8] = "Low Temperature Event"; //12
        $alertSubjects[9] = "High Temperature Event"; //13
        $len = count($alertSubjects);
        $offset = 4;
        
        for($i = 0; $i < $len; $i++)
        {
            $alertSubjects[$i] = $hostName." @ ".getLocation()." - ".$alertSubjects[$i];
            $query = sprintf("UPDATE alerts SET desc='%s', v2='%s', v4='%s' WHERE id='%d';",$alertSubjects[$i], $alertSubjects[$i], "System Event From ".$hostName."@".getLocation(), ($i+$offset));
            $dbh->exec($query);
        }
	}
	function updatePcPingTarget($pcServerIp, $dbh)
	{
		$query = sprintf("UPDATE ping_targets SET ip='%s' WHERE id=1;",$pcServerIp);
		$dbh->exec($query);
	}

	
	$hostname = trim(file_get_contents("/etc/hostname"));
	
	$dbh = new PDO('sqlite:/etc/rms100.db');
	
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
	
	
	/////////////////////////////////////////////////////////////////
	//                                                             //
	//                    GET PROCESSING                           //
	//                                                             //
	/////////////////////////////////////////////////////////////////
	if($_SERVER['REQUEST_METHOD'] == "GET")
	{
		$pcServerIP = getPcIp($dbh);

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
		header("Location: index.php");
	}

	if(isset ($_POST['restart_btn']))
	{
		header("Location: restart.php");
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

		$pcServerIP = $_POST["pc_server_ip"];

		
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

		if($alert_flag == 0)
		{
			$alert_flag = "1";
			
			$file = fopen("/etc/network/interfaces.new","w");
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
			
			$file = fopen("/etc/resolv.conf.new", "w");
			fprintf($file, "search %s\n",$domain);
			fprintf($file, "nameserver %s\n",$dns1);
			fprintf($file, "nameserver %s\n\n",$dns2);
			fclose($file);
			system("mv /etc/resolv.conf.new /etc/resolv.conf");
			system("mv /etc/network/interfaces.new /etc/network/interfaces");
			
			$file = fopen("/etc/hostname.new", "w");
			fprintf($file, "%s\n",$station_name);
			fclose($file);
			system("mv /etc/hostname.new /etc/hostname");
			
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
			//change ping target ip
			updateEmailAlertSubjects($station_name,$dbh);
			updatePcPingTarget($pcServerIP, $dbh);
		}

	}
		
?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0 shrink-to-fit=no">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">

	<!-- Page title -->
	<title><?php echo $hostname; ?></title>
	<link rel="shortcut icon" type="image/ico" href="mattLib/images/favicon.ico?<?php echo rand(); ?>" />
    <script>
        function getReferer() { return "<?php echo $_SERVER['HTTP_REFERER']; ?>"; }    
    </script>
    
	<link rel="stylesheet" href="mattLib/dependencies/bootstrap.min.css" />
	<link rel="stylesheet" href="mattLib/dependencies/sweetalert.css" />
	<link rel="stylesheet" href="mattLib/SolarRig.css">
	<link href="https://fonts.googleapis.com/css?family=Roboto+Condensed:400,700&display=swap" rel="stylesheet"> 
	
	<script src="mattLib/dependencies/jquery-3.4.1.min.js"></script>
	<script src="mattLib/dependencies/bootstrap.bundle.min.js"></script>
	<script src="mattLib/dependencies/sweetalert.min.js"></script>
    <script> function jsIsFirstRun() { return <?php echo $first_run_bool ?> }; </script>
</head>

<!--[if lt IE 7]>
<p class="alert alert-danger">You are using an <strong>old</strong> web browser. Please upgrade your browser to improve your experience.</p>
<![endif]-->

<body class="bg-body" style="font-family: 'Roboto Condensed', sans-serif;">
	<!-- Main Wrapper -->
	<div class="container-fluid">
		<form name='NetworkSetup' action='setup.php' method='post'>
			<div class="row justify-content-center">
				<div class="col-auto border rounded bg-label shadow mx-2 my-2">
					<legend> Network IP Options</legend>
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
				</div>
				<div class="col-auto border rounded bg-label shadow mx-2 my-2">
					<legend>General Network Options</legend>
					<div class="form-group"><label class="col-sm-3 control-label" style="text-align:left; min-width:140px; max-width:140px">Host Name:</label>
						<div class="col-sm-6" style="min-width:270px; max-width:270px">
							<input type="text" class="form-control input-sm" name='station_name' value='<?php echo $station_name; ?>' required />
						</div>
					</div>
					<div class="form-group"><label class="col-sm-3 control-label" style="text-align:left; min-width:140px; max-width:140px">Location / Display Name:</label>
						<div class="col-sm-6" style="min-width:270px; max-width:270px">
							<input type="text" class="form-control input-sm" name='location' value='<?php echo $location; ?>' required />
						</div>
					</div>
					<div class="form-group"><label class="col-sm-3 control-label" style="text-align:left; min-width:140px; max-width:140px">Domain Name:</label>
						<div class="col-sm-6" style="min-width:270px; max-width:270px">
							<input type="text" class="form-control input-sm" name='domain' value='<?php echo $domain; ?>' required />
						</div>
					</div>
					<div class="form-group"><label class="col-sm-6 control-label" style="text-align:left; min-width:140px; max-width:180px">NVR Server IP:</label>
						<div class="col-sm-6" style="min-width:270px; max-width:270px">
							<input type="text" class="form-control input-sm" name='pc_server_ip' value='<?php echo $pcServerIP; ?>' required />
						</div>
					</div>
				</div>
			</div>
			<div class="row justify-content-center">
				<div class="col-auto border rounded bg-label shadow mx-2">
					<div class="form-group">
						<table class="table table-borderless table-sm my-2">
							<tr>
								<td><button name="save_btn" class="btn btn-lg btn-primary btn-block shadow" type="submit" >Save</button></td>
								<td><button name="cancel_btn" class="btn btn-lg btn-secondary btn-block shadow" type="submit" formnovalidate>Cancel</button></td>
								<td><button name="restart_btn" class="btn btn-lg btn-warning btn-block shadow" type="submit" formnovalidate>Reboot</button></td>
							</tr>
						</table>
					</div>
				</div>
				<!-- Start Sticky Footer -->
					<style>
						html {
							position: relative;
							min-height: 100%;
						}
						body {
							margin-bottom: 60px; /* Margin bottom by footer height */
						}
						.footer {
							position: absolute;
							bottom: 0;
							width: 95%;
						}
					</style>
					<footer class="footer text-center"><span><?php //printCopyright();?></span></footer>
				<!-- End Sticky Footer -->
			</div>
		</form>



	</div>

	<?php 
		if($alert_flag == "1")
		{
		echo"<script>";
		echo"	swal({";
		echo"		title: 'Success!',";
		echo"  	text: 'Settings Saved!  Please shutdown the connected PC Server.<br>Reboot now to save all changes.',";
		echo"  	type: 'success',";
		echo"		showCancelButton: true,";
		echo"		cancelButtonText: 'Reboot Later',";
		echo"		html: true,";
		echo"		confirmButtonColor: '#DD6B55',";
		echo"		confirmButtonText: 'Reboot Now!',";
		echo"		closeOnConfirm: false";
		echo"	},";
		echo"	function(){";
		echo"		window.location.href = 'restart.php';";
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
    <script async> 
    if (jsIsFirstRun() && getReferer().indexOf('password') !== -1) {
        alert('Please complete all network setup steps as follows...');
    }
</script>
</html> 
