<?php
	error_reporting(E_ALL);
	include "lib.php";
	$hostname = trim(file_get_contents("/etc/hostname"));
	
	$alert_flag = "0";
	$ip = "";
	$snmpv = "0";
	$username = "";
	$rocommunity = "";
	$rwcommunity = "";
	$plain_password = "";
	$syscontact = "";
	$tmp = "";
	$newconf = "";
	$text = "";
	
	
	$conf = "#########################################\n".
			"#                                       #\n".
			"#  /etc/snmpd/snmpd.conf:               #\n".
			"#  (C)reated by EtherTek Circuits 2016  #\n".       
			"#                                       #\n".
			"#########################################\n".
			"\n".
			"sysservices 76\n".
			"\n".
			"agentuser root\n".
			"\n".
			"agentgroup root\n".
			"\n".
			"agentaddress 161\n".
			"\n".
			"engineID EtherTekCircuits(C)2009\n".
			"\n";
          
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
	
	
	$my_meth = $_SERVER['REQUEST_METHOD'];
	if($my_meth = "GET")
	{
		//Determine SNMP version
		$search = 'rwuser ';
		$lines = file('/etc/snmp/snmpd.conf');
		// Store true when the text is found
		$found = false;
		foreach($lines as $line)
		{
		  if(strpos($line, $search) !== false)
		  {
		    if($line[0] == "#")
		    {
		    	$found = true;
		    	$snmpv = "1";
		    	$tmp = explode(" ",$line);
		    	$username = trim($tmp[1]);
		    	//echo "SNMPv1 ".$line;
		    	break;
		    }
		    else
		    {
		    	$found = true;
		    	$snmpv = "3";
		    	$tmp = explode(" ",$line);
		    	$username = trim($tmp[1]);
		    	//echo "SNMPv3 Username ".$username;
		    	break;
		    }
		  }
		}
		// If the text was not found, ERROR
		if(!$found)
		{
		  die("/etc/snmp/snmpd.conf file damaged!");
		}
		
		if($snmpv == "1")
		{
			//Determine ro Password
			$search = 'rocommunity ';
			$lines = file('/etc/snmp/snmpd.conf');
			// Store true when the text is found
			$found = false;
			foreach($lines as $line)
			{
			  if(strpos($line, $search) !== false)
			  {
			    $found = true;
			    $tmp = explode(" ",$line);
			    $rocommunity = trim($tmp[1]);
			  }
			}
			// If the text was not found, ERROR
			if(!$found)
			{
			  die("/etc/snmp/snmpd.conf file damaged!");
			}
			
			//Determine rw Password
			$search = 'rwcommunity ';
			$lines = file('/etc/snmp/snmpd.conf');
			// Store true when the text is found
			$found = false;
			foreach($lines as $line)
			{
			  if(strpos($line, $search) !== false)
			  {
			    $found = true;
			    $tmp = explode(" ",$line);
			    $rwcommunity = trim($tmp[1]);
			  }
			}
			// If the text was not found, ERROR
			if(!$found)
			{
			  die("/etc/snmp/snmpd.conf file damaged!");
			}
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

	
// SAVE v1 Button	was clicked
if(isset ($_POST['save_v1_btn']))
{	
	$rocommunity = $_POST['rocommunity'];
	$rwcommunity = $_POST['rwcommunity'];
	$username = $_POST['username'];
	$snmpv = $_POST['snmpv'];
	
  $conf = $conf . "#rwuser ".$username."\n\n";
  $conf = $conf . "rocommunity ".$rocommunity."\n\n";        
  $conf = $conf . "rwcommunity ".$rwcommunity."\n\n";         
  
  file_put_contents("/etc/snmp/snmpd.conf",$conf);        
  exec("/etc/init.scripts/S59snmpd stop");
  sleep(2);
  exec("/etc/init.scripts/S59snmpd start");
     
  $alert_flag = "1";  
}          


// Enable SNMPv3 Button	was clicked
if(isset ($_POST['enablev3']))
{	
	$newconf = "";
	$rocommunity = $_POST['rocommunity'];
	$rwcommunity = $_POST['rwcommunity'];
	$username = $_POST['username'];
	$snmpv = $_POST['snmpv'];
	
  $newconf = $conf . "rwuser ".$username."\n\n";
  $newconf = $newconf . "#rocommunity ".$rocommunity."\n\n";        
  $newconf = $newconf . "#rwcommunity ".$rwcommunity."\n\n";         
  
  file_put_contents("/etc/snmp/snmpd.conf",$newconf);        
  exec("/etc/init.scripts/S59snmpd stop");
  sleep(2);
  exec("/etc/init.scripts/S59snmpd start");
  $snmpv = "3";   
  $alert_flag = "1";
	
}


// Enable SNMPv1 Button	was clicked
if(isset ($_POST['enablev1']))
{	
	$newconf = "";
	//$rocommunity = $_POST['rocommunity'];
	//$rwcommunity = $_POST['rwcommunity'];
	$username = $_POST['username'];
	$snmpv = $_POST['snmpv'];
	
	//Determine ro Password
	$search = 'rocommunity ';
	$lines = file('/etc/snmp/snmpd.conf');
	// Store true when the text is found
	$found = false;
	foreach($lines as $line)
	{
	  if(strpos($line, $search) !== false)
	  {
	    $found = true;
	    $tmp = explode(" ",$line);
	    $rocommunity = trim($tmp[1]);
	  }
	}
	// If the text was not found, ERROR
	if(!$found)
	{
	  die("/etc/snmp/snmpd.conf file damaged!");
	}
	
	//Determine rw Password
	$search = 'rwcommunity ';
	$lines = file('/etc/snmp/snmpd.conf');
	// Store true when the text is found
	$found = false;
	foreach($lines as $line)
	{
	  if(strpos($line, $search) !== false)
	  {
	    $found = true;
	    $tmp = explode(" ",$line);
	    $rwcommunity = trim($tmp[1]);
	  }
	}
	// If the text was not found, ERROR
	if(!$found)
	{
	  die("/etc/snmp/snmpd.conf file damaged!");
	}
	
  $newconf = $conf . "#rwuser ".$username."\n\n";
  $newconf = $newconf . "rocommunity ".$rocommunity."\n\n";        
  $newconf = $newconf . "rwcommunity ".$rwcommunity."\n\n";         
  
  file_put_contents("/etc/snmp/snmpd.conf",$newconf);        
  exec("/etc/init.scripts/S59snmpd stop");
  sleep(2);
  exec("/etc/init.scripts/S59snmpd start");
  $snmpv = "1";   
  $alert_flag = "1";
	
}

// SAVE sysContact Button	was clicked
if(isset ($_POST['save_syscontact_btn']))
{	
	$syscontact = $_POST['syscontact'];
	$snmpv = $_POST['snmpv'];
	$query = "SELECT * FROM snmp_config WHERE id=1";
	$result  = $dbh->query($query);
	foreach($result as $row)
	{
		$plain_password = $row['plain_password'];	
		$username = $row['username'];	
	}
	
	$tmp = sprintf("/usr/bin/snmpset -v 3 -u %s -l authNoPriv -a MD5 -A %s localhost .1.3.6.1.2.1.1.4.0 s \"%s\"",$username,$plain_password,$syscontact);
  exec($tmp);
  sleep(1);
  $snmpv = "3";   
  $alert_flag = "1";
}
        
// SAVE username Button	was clicked
if(isset ($_POST['save_username_btn']))
{	
	$newconf = "";
	$username = $_POST['username'];
	$snmpv = $_POST['snmpv'];
	$query = "SELECT * FROM snmp_config WHERE id=1";
	$result  = $dbh->query($query);
	foreach($result as $row)
	{
		$plain_password = $row['plain_password'];	
		$old_username = $row['username'];
	}
	
	$tmp = sprintf("/usr/bin/snmpusm -v3 -u %s -l authNoPriv -a MD5 -A %s localhost create %s %s > /dev/null",$old_username,$plain_password,$username,$old_username);
	exec($tmp);
	$tmp = sprintf("/usr/bin/snmpusm -v3 -u %s -l authNoPriv -a MD5 -A %s localhost delete %s > /dev/null",$old_username,$plain_password,$old_username);
	exec($tmp);
	$query = sprintf("UPDATE snmp_config SET username='%s' WHERE id='1';",$username);
	$result  = $dbh->exec($query);	
	
  $newconf = $conf . "rwuser ".$username."\n\n";
  $newconf = $newconf . "#rocommunity ".$rocommunity."\n\n";        
  $newconf = $newconf . "#rwcommunity ".$rwcommunity."\n\n";         
  
  file_put_contents("/etc/snmp/snmpd.conf",$newconf);        
  exec("/etc/init.scripts/S59snmpd stop");
  sleep(2);
  exec("/etc/init.scripts/S59snmpd start");
  $snmpv = "3";   
  $alert_flag = "1";
}

// SAVE password Button	was clicked
if(isset ($_POST['save_password_btn']))
{	
	$newconf = "";
	$snmpv = $_POST['snmpv'];
	$plain_password = $_POST['plain_password'];
	$cplain_password = $_POST['cplain_password'];
	
	if($plain_password !== $cplain_password)
	{
		$text = "Password mismatch!";
		$alert_flag = "2";
		goto noSave;
	}
	
	$query = "SELECT * FROM snmp_config WHERE id=1";
	$result  = $dbh->query($query);
	foreach($result as $row)
	{
		$old_plain_password = $row['plain_password'];	
		$username = $row['username'];
	}
	
	$query = sprintf("UPDATE snmp_config SET plain_password='%s' WHERE id='1';", $plain_password);
	$result  = $dbh->exec($query);
	
	$tmp = sprintf("/usr/bin/snmpusm -v3 -u %s -l authNoPriv -a MD5 -A %s -x DES localhost passwd %s %s > /dev/null",$username,$old_plain_password,$old_plain_password,$plain_password);
	exec($tmp);
  $snmpv = "3";   
  $alert_flag = "1";
  
  noSave:
  
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
    <link rel="shortcut icon" type="image/ico" href="rms100favocon.ico?" />

    <!-- CSS -->
    <link rel="stylesheet" href="css/fontawesome/css/font-awesome.css" />
    <link rel="stylesheet" href="css/animate.css" />
    <link rel="stylesheet" href="css/bootstrap.css" />
    <link rel="stylesheet" href="css/awesome-bootstrap-checkbox.css" />
    <link rel="stylesheet" href="css/sweetalert.css" />
    <link rel="stylesheet" href="css/ethertek.css">
    
    <!-- Java Scripts -->
		<script src="javascript/jquery.min.js"></script>
		<script src="javascript/bootstrap.min.js"></script>
		<script src="javascript/sweetalert.min.js"></script>
		<script src="javascript/conhelp.js"></script>
		<script src="javascript/ethertek.js"></script>
		<script language="javascript" type="text/javascript">
			SetContext('snmp');
		</script>
		<script>
			function showPassword()
			{
  			var showPasswordCheckBox = document.getElementById("en");
  			if(showPasswordCheckBox.checked)
  			{
        	document.getElementById("pass1").type="PASSWORD";
        	document.getElementById("pass2").type="PASSWORD";
  			}
  			else
  			{
      		document.getElementById("pass1").type="TEXT";
      		document.getElementById("pass2").type="TEXT";
  			}
			}
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
	SetContext('snmp');
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
      		<div class="panel-body" style="max-width:600px">
      	  	<form name='SNMP' action='setup_snmp.php' method='post' class="form-horizontal">  	
      	    	<fieldset>
      	    		<?php
      	    			//$snmpv = "3";
      	    			if($snmpv == "1")
      	    			{
      	    				echo "<legend><img src='images/snmp.gif'> SNMP v1 and v2 Settings</legend>";
      	    				echo "		<button name='enablev3' class='btn btn-danger' type='submit' onMouseOver='mouse_move(\"sd_snmpv3\");' onMouseOut='mouse_move();'><i class='fa fa-question'></i> Enable SNMP v3</button>";
      	    				echo "<hr>";
      	    				echo "<div class='form-group'><label class='col-sm-4 control-label' style='text-align:left; max-width:180px; min-width:180px'>Read Only Password:</label>";
              			echo " <div class='col-sm-12' style='max-width:400px'>";
              			echo "  <input type='password' class='form-control' name='rocommunity'  id='pass1' value='".$rocommunity."' required/>";
              			echo " </div>";
              			echo "</div>";
      	    				echo "<div class='form-group'><label class='col-sm-4 control-label' style='text-align:left; max-width:180px; min-width:180px'>Read Write Password:</label>";
              			echo " <div class='col-sm-12' style='max-width:400px'>";
              			echo "  <input type='password' class='form-control' name='rwcommunity'  id='pass2' value='".$rwcommunity."' required/>";
              			echo " </div>";
              			echo "</div>";
      	    				echo "<div class='form-group'>";
              			echo "	<div class='col-sm-12'>";
              	  	echo "		<button name='save_v1_btn' class='btn btn-success' type='submit' onMouseOver='mouse_move(\"b_apply\");' onMouseOut='mouse_move();'><i class='fa fa-check'></i> Save</button>";
              	  	echo "		<button name='cancel_btn' class='btn btn-primary' type='submit' onMouseOver='mouse_move(\"b_cancel\");' onMouseOut='mouse_move();' formnovalidate><i class='fa fa-times'></i> Cancel</button>";
              	  	echo "	</div>";
              			echo "</div>";
              			echo "<div class='row'>";
              			echo "	<div class='checkbox checkbox-success'>";
                    echo "		<input type='checkbox' id='en' name='en' onclick='showPassword()'; checked> />";
                    echo "		<label for='en'>Hide Passwords</label>";
                    echo "	</div>";
                    echo "</div><br><br>";
      	    				echo "<input type='hidden' name='username' value='".$username."'>";
      	    				
      	    			}
      	    			else
      	    			{
										$query = "SELECT * FROM snmp_config WHERE id=1";
										$result  = $dbh->query($query);
										foreach($result as $row)
										{
											$plain_password = $row['plain_password'];	
										}
      	    				$tmp = sprintf("/usr/bin/snmpget -O vq -v3 -u %s -l authNoPriv -a MD5 -A %s localhost sysContact.0 > /tmp/sysContact.txt",$username,$plain_password);
      	    				exec($tmp);
      	    				$syscontact = file_get_contents("/tmp/sysContact.txt");
      	    				unlink("/tmp/sysContact.txt");
      	    				
      	    				
      	    				echo "<legend><img src='images/snmp.gif'> SNMP v3 Settings</legend>";
      	    				echo "		<button name='enablev1' class='btn btn-danger' type='submit' onMouseOver='mouse_move(\"sd_snmpv1\");' onMouseOut='mouse_move();'><i class='fa fa-question'></i> Enable SNMP v1 and v2</button>";
      	    				echo "<hr>";
      	    				echo "<div class='form-group'><label class='col-sm-4 control-label' style='text-align:left; max-width:180px; min-width:180px'>System Contact Name:</label>";
              			echo "	<div class='col-sm-12' style='max-width:400px'>";
              			echo "		<input type='text' class='form-control' name='syscontact' value='".$syscontact."' required/>";
              			echo "	</div>";
              			echo "</div>";
              			echo "<div class='row'>";
              			echo "	<div class='col-sm-12'>";
              			echo "		<button name='save_syscontact_btn' class='btn btn-success' type='submit' onMouseOver='mouse_move(\"b_apply\");' onMouseOut='mouse_move();'><i class='fa fa-check'></i> Apply</button>";
              			echo "	</div>";
              			echo "</div>";
              			echo "<hr>";
              			
              			echo "<div class='form-group'><label class='col-sm-4 control-label' style='text-align:left; max-width:180px; min-width:180px'>Username:</label>";
              			echo " <div class='col-sm-12' style='max-width:400px'>";
              			echo "  <input pattern='.{3,63}' title='3 to 63 characters' type='text' class='form-control' name='username' value='".$username."' oninvalid='this.setCustomValidity(\"Must be between 3 and 63 Characters!\")' required/>";
              			echo " </div>";
              			echo "</div>";
              			echo "<div class='row'>";
              			echo "	<div class='col-sm-12'>";
              			echo "		<button name='save_username_btn' class='btn btn-success' type='submit' onMouseOver='mouse_move(\"b_apply\");' onMouseOut='mouse_move();'><i class='fa fa-check'></i> Apply</button>";
              			echo "	</div>";
              			echo "</div>";
              			echo "<hr>";
              			
              			echo "<div class='form-group'><label class='col-sm-4 control-label' style='text-align:left; max-width:180px; min-width:180px'>Password:</label>";
              			
              			echo " <div class='col-sm-12' style='max-width:400px'>";
              			echo "  <input pattern='.{8,63}' title='8 to 63 characters' type='password' id='pass1' class='form-control' name='plain_password' value='".$plain_password."' oninvalid='this.setCustomValidity(\"Must be between 8 and 63 Characters!\")' required/>";
              			echo " </div>";
              			echo "</div>";
              			echo "<div class='form-group'><label class='col-sm-4 control-label' style='text-align:left; max-width:180px; min-width:180px'>Confirm Password:</label>";
              			echo " <div class='col-sm-12' style='max-width:400px'>";
              			echo "  <input pattern='.{8,63}' title='8 to 63 characters' type='password' id='pass2' class='form-control' name='cplain_password' value='".$plain_password."' oninvalid='this.setCustomValidity(\"Must be between 8 and 63 Characters!\")' required/>";
              			echo " </div>";
              			echo "</div>";
              			echo "<div class='row'>";
              			echo "	<div class='col-sm-12'>";
              			echo "		<button name='save_password_btn' class='btn btn-success' type='submit' onMouseOver='mouse_move(\"b_apply\");' onMouseOut='mouse_move();'><i class='fa fa-check'></i> Apply</button>";
              			echo "	</div>";
              			echo "</div>";
              			
              			echo "<hr>";
              			echo "<div class='form-group'>";
              			echo "	<div class='col-sm-12'>";
              	  	echo "		<button name='cancel_btn' class='btn btn-primary' type='submit' onMouseOver='mouse_move(\"b_cancel\");' onMouseOut='mouse_move();' formnovalidate><i class='fa fa-times'></i> Cancel</button>";
              	  	echo "	</div>";
              			echo "</div>";
              			echo "<div class='row'>";
              			echo "	<div class='checkbox checkbox-success'>";
                    echo "		<input type='checkbox' id='en' name='en' onclick='showPassword()'; checked> />";
                    echo "		<label for='en'>Hide Passwords</label>";
                    echo "	</div>";
                    echo "</div><br><br>";
      	    			}
      	    		?>
      	    		
              	<?php echo "<input type='hidden' name='snmpv' value='".$snmpv."'>";  ?>
              </fieldset>  
						</form>
	
      		</div> <!-- END PANEL BODY --> 
      	</div> <!-- END PANEL WRAPPER --> 
      </div>  <!-- END COL --> 
    </div> <!-- END ROW --> 
  </div> <!-- END CONTENT -->    
</div> <!-- END Main Wrapper -->




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
echo"  title:'Error!',";
echo"  text: '" . $text . "',";
echo"  type: 'error',";
echo"  showConfirmButton: false,";
echo"	 html: true,";
echo"  timer: 2500";
echo"});";
echo"</script>";
}


?>
</body>
</html> 
