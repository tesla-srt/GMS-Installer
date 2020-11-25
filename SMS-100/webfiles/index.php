<?php
	//error_reporting(E_ALL);
	include_once "mattLib/Utils.php";
	include_once "mattLib/InputHandler.php";
	include_once "mattLib/DatabaseHandler.php";
    include "/data/custom/scripts/classes/Logger.php";
    $cookie_name = "login";
    $cookie_value = "1";
    if(!isset($_COOKIE[$cookie_name])) {
        setcookie($cookie_name, $cookie_value, time() + 3600, "/");
        login_attempt();
    }
	$hostname = trim(file_get_contents("/etc/hostname"));
	$mac_address = getMacAddress();
	$ip_address = getIpAddress();
	$serverDate = getServerDate();
	setPcStateOnWhenPingAlive();
?>

<!DOCTYPE html>
<html>
	<head>
        <meta http-equiv="refresh" content="3601;URL='index.php'">
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0 shrink-to-fit=no">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
        
		<!-- Page title -->
		<title><?php echo $hostname; ?></title>
		<link rel="shortcut icon" type="image/ico" href="./mattLib/images/favicon.ico" />
        
         <?php
            if (isFirstRun()) {
                syncTime();
                echo "<script>\n";
                echo "alert('You must change the password to continue.');\n";
                echo "window.location.href = 'setup_password.php';\n";
                echo "</script>";
            }
        ?>
        
		<!-- CSS -->
		<link rel="stylesheet" href="mattLib/dependencies/bootstrap.min.css" />
<!--        <link rel="stylesheet" href="mattLib/dependencies/toggle-switch.css" />-->
		<link rel="stylesheet" href="mattLib/SolarRig.css">
		<link rel="stylesheet" href="mattLib/dependencies/Chart.min.css">        
		<link href="https://fonts.googleapis.com/css?family=Roboto+Condensed:400,700&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="mattLib/dependencies/tempusdominus-bootstrap-4.min.css"/>
<!--        <link rel="stylesheet" type="text/css" href="mattLib/dependencies/datepicker.css" />-->
        <link rel="stylesheet" type="text/css" href="mattLib/dependencies/fontawesome/fontawesome.min.new.css" />
        <link rel="stylesheet" type="text/css" href="mattLib/dependencies/epoch.min.css" />
        <link rel="stylesheet" href="mattLib/dependencies/odometer-theme-minimal.css" />
        <link rel="stylesheet" href="mattLib/dependencies/sweetalert.css" />


        <style>
			/* Hide number spinners */
			/* Chrome, Safari, Edge, Opera */
			input::-webkit-outer-spin-button,
			input::-webkit-inner-spin-button {
				-webkit-appearance: none;
				margin: 0;
			}

			/* Firefox */
			input[type=number] {
				-moz-appearance:textfield;
			}
		</style>
        
		<!-- Java Scripts -->
		<script>//PHP Conversion functions
			function getHostName() {return "<?php echo $hostname;?>";}
			function getServerDate(){return "<?php echo $serverDate;?>";}
			function getMacAddress() {return "<?php echo $mac_address;?>";}
			function getVm1Name() {return "<?php echo $v1_name; ?>";}
			function getVm2Name() {return "<?php echo $v2_name; ?>";}
			function getVm3Name() {return "<?php echo $v3_name; ?>";}
			function getAlarm1Name() {return "<?php echo $a1_name;?>";}
			function getAlarm2Name() {return "<?php echo $a2_name;?>";}
			function getAlarm3Name() {return "<?php echo $a3_name;?>";}
			function getAlarm4Name() {return "<?php echo $a4_name;?>";}
			function getDataThrottle() {return "<?php echo $dt;?>";}
			function getAutoPcShutdownState() {return "<?php echo $autoShutdownState;?>";}
			function getIpAddress() {return "<?php echo $ip_address;?>";}
		</script>
        
		<!--<script type="text/javascript" src="mattLib/dependencies/jquery-3.4.1.min.js"></script>-->
		<script type="text/javascript" src="mattLib/dependencies/jquery-3.5.1.min.js"></script>
		<script type="text/javascript" src="mattLib/dependencies/bootstrap.bundle.min.js"></script>
		<script type="text/javascript" src="mattLib/dependencies/fontawesome/solid.min.new.js"></script>
		<script type="text/javascript" src="mattLib/dependencies/fontawesome/fontawesome.min.new.js" data-auto-replace-svg="nest"></script>
       <!-- <script type="text/javascript" src="mattLib/dependencies/datepicker.js"></script> -->
<!--        <script type="text/javascript" src="mattLib/dependencies/timepicker.js"></script> -->
		<script type="text/javascript" src="mattLib/dependencies/moment.min.js"></script>
        <script type="text/javascript" src="mattLib/dependencies/tempusdominus-bootstrap-4.min.js"></script>

		<script type="text/javascript" src="mattLib/dependencies/Chart.min.js"></script>
		<script type="text/javascript" src="mattLib/dependencies/d3.js"></script>
		<script type="text/javascript" src="mattLib/dependencies/epoch.min.js"></script>
        <script src="mattLib/dependencies/sweetalert.min.js"></script>


        <script>
            //sync graph time to server time.
			//Called by chart plugin streaming, and graphhandler.
			var serverDate = new Date(getServerDate());
			var serverOffset = moment(serverDate).diff(new Date());
			function currentServerDate(){return moment().add(serverOffset,'milliseconds');}
		</script>

		<script type="text/javascript" src="mattLib/dependencies/chartjs-plugin-streaming.min.js"></script>
		<script type="text/javascript" src="mattLib/GraphHandler.js"></script>
		<script type="text/javascript" src="mattLib/StateHandler.js"></script>
		<script type="text/javascript" src="mattLib/ValueUpdater.js"></script>
		<script type="text/javascript" src="mattLib/LogUpdater.js"></script>
		<script type="text/javascript" src="mattLib/GuiHandler.js"></script>
	</head>

	<body class="bg-body my-0" style="font-family: 'Roboto Condensed', sans-serif;">
		<!--[if lt IE 7]>
		<p class="alert alert-danger">You are using a <strong>old</strong> web browser. Please upgrade your browser to improve your experience.</p>
		<![endif]-->

		
        
        <?php include "mattLib/Gui.php"; ?>
        
        <script type="text/javascript" src="mattLib/dependencies/nicklib.js"></script>
		<script>//Gui Operations
			var logUpdater = LogUpdater();
			var guiHandler = GuiHandler(logUpdater);
            
            /*$('#btnTempGraphUpdate').on('click', function () {
            var img = $('#tempGraphPng');
            var src = img.attr('src');
            var i = src.indexOf('?dummy=');
            src = i != -1 ? src.substring(0, i) : src;

            var d = new Date();
            img.attr('src', src + '?dummy=' + d.getTime());
            });*/

            
            $('#tempBtn').on('click', function () {
                setTempGraphImage('hour');
                setVMGraphImage('hour');
            });
            
			$(document).ready(function(){
				guiHandler.openLogOnClick("v1Btn", "v1Log");
				guiHandler.openLogOnClick("v2Btn", "v2Log");
				guiHandler.openLogOnClick("v3Btn", "v3Log");
				guiHandler.openLogOnClick("a1Btn", "a1Log");
				guiHandler.openLogOnClick("a2Btn", "a2Log");
				guiHandler.openLogOnClick("a3Btn", "a3Log");
				guiHandler.openLogOnClick("a4Btn", "a4Log");
                guiHandler.openLogOnClick("_accessLog", "accessLog");
				guiHandler.openAlertOnClick("btnPcOff");
				guiHandler.openAlertOnClick("btnPcOn");
				guiHandler.openAlertOnClick("btnSystemReboot");
                openGraphOnClick("tempBtn", "tempLog");
                //openGraphOnClick("vmBtn", "vmLog");

                $('.loading-modal').modal('hide');
            });
			guiHandler.resetOptionsOnModalExit();
			guiHandler.disableLogLoopingOnExit();


			var stateHandler = StateHandler(getAutoPcShutdownState());
			var dataThrottle = 2150;
			var graphHandler = GraphHandler(currentServerDate);
			graphHandler.createGraph(getVm1Name(), getVm2Name(), getVm3Name());
            
            if(getMacAddress() == "test"){
				//dataThrottle = 1;
				//stateHandler.setIsAutoPcShutdownOn(true);
                    updateValues(dataThrottle, graphHandler, stateHandler, 'tests/MockSdServer.php?element=homeall&rand=');
                }else{
                    updateValues(dataThrottle, graphHandler, stateHandler, 'mattLib/SdServer.php?element=homeall&rand=');
                }
        </script>
	</body>
</html>