<?php
	error_reporting(E_ALL);
	include_once "mattLib/Utils.php";
	include_once "mattLib/InputHandler.php";
	include_once "mattLib/DatabaseHandler.php";

	$hostname = getFormattedHostname();
	$mac_address = getMacAddress();
	$ip_address = getIpAddress();
	$serverDate = getServerDate();
	setPcStateOnWhenPingAlive();

	if(isset($_POST["cycle_pc_power"]))
	{
		turnPcOn();
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

	<!-- CSS -->
	<link rel="stylesheet" href="mattLib/dependencies/bootstrap.min.css" />
	<link rel="stylesheet" href="mattLib/SolarRig.css">
	<link rel="stylesheet" href="mattLib/dependencies/Chart.min.css">
	<link href="https://fonts.googleapis.com/css?family=Roboto+Condensed:400,700&display=swap" rel="stylesheet">

	<!-- Java Scripts -->
	<script>//PHP Conversion functions
		function getHostName() {return "<?php echo $hostname; ?>";}
		function getServerDate() {return "<?php echo $serverDate; ?>";}
		function getMacAddress() {return "<?php echo $mac_address; ?>";}
		function getVm1Name() {return "<?php echo $v1_name; ?>";}
		function getVm2Name() {return "<?php echo $v2_name; ?>";}
		function getVm3Name() {return "<?php echo $v3_name; ?>";}
		function getAlarm1Name() {return "<?php echo $a1_name; ?>";}
		function getAlarm2Name() {return "<?php echo $a2_name; ?>";}
		function getAlarm3Name() {return "<?php echo $a3_name; ?>";}
		function getAlarm4Name() {return "<?php echo $a4_name; ?>";}
		function getDataThrottle() {return "<?php echo $dt; ?>";}
		function getAutoPcShutdownState() {return "<?php echo $autoShutdownState; ?>";}
	</script>
	<script src="mattLib/dependencies/jquery-3.4.1.min.js"></script>
	<script src="mattLib/dependencies/bootstrap.bundle.min.js"></script>
	<script src="mattLib/dependencies/fontawesome/solid.js"></script>
	<script src="mattLib/dependencies/fontawesome/fontawesome.min.js" data-auto-replace-svg="nest"></script>

	<script src="mattLib/dependencies/moment.min.js"></script>
	<script src="mattLib/dependencies/Chart.min.js"></script>
	<script>//sync graph time to server time. 
		//Called by chart plugin streaming, and graphhandler.
		var serverDate = new Date(getServerDate());
		var serverOffset = moment(serverDate).diff(new Date());

		function currentServerDate() {
			return moment().add(serverOffset, 'milliseconds');
		}
	</script>

	<script src="mattLib/dependencies/chartjs-plugin-streaming.min.js"></script>
	<script src="mattLib/GraphHandler.js"></script>
	<script src="mattLib/StateHandler.js"></script>
	<script src="mattLib/ValueUpdater.js"></script>
	<script src="mattLib/LogUpdater.js"></script>
	<script src="mattLib/GuiHandler.js"></script>

	<script>//debug Ajax Requests
		function debugData(timeOut) 
		{
			var sysPowerVoltage = 0;
			var sysPowerVoltageRaw = 0;
			var battVoltage = 0;
			var battVoltageRaw = 0;
			var poeVoltage = 0;
			var poeVoltageRaw = 0;

			var homeallData;
			var gpio1;

			update();

			function startRow() { return "<tr>";}
			function addData(label = "", data = "") {return "<td><b>"+label+"</b></td><td>"+data+"</td>";}
			function endRow() {return "</tr>";}
			function createTable(tableName, table) { $("#"+tableName).html(table);}

			function printAlarmState(state) {return (state == 1? "Fault":"Norm");}
			function printRelayState(state) {return (state == 1? "NC": "NO");}

			function update(){
				var myRandom = parseInt(Math.random() * 999999999);
				$.getJSON('mattLib/SdServer.php?element=vmall&rand=' + myRandom,
					function(data) {
						sysPowerVoltage = data.vm1;
						battVoltage = data.vm2;
						poeVoltage = data.vm3;
					}
				);
				$.getJSON('mattLib/SdServer.php?element=vmallraw&rand=' + myRandom,
					function(data) {
						sysPowerVoltageRaw = data.vm1;
						battVoltageRaw = data.vm2;
						poeVoltageRaw = data.vm3;
					}
				);
				$.getJSON('mattLib/SdServer.php?element=pcBooter&rand=' + myRandom,
					function(data) {
						gpio1 = data;
					}
				);
				$.getJSON('mattLib/SdServer.php?element=homeall&rand=' + myRandom,
					function(data) {
						homeallData = data;
						updateTables();
						setTimeout(update, timeOut);
					}
				);
			}

			function updateTables() {
				var isPowerCycling = (homeallData.r1 == 0);
				var isRebooting = (homeallData.isrebooting == 1);
				var isAutoShutdownOn = (getAutoPcShutdownState() == 1);
				var hasPcOnBeenTriggered = (homeallData.hasPcOnBeenTriggered == 1);
				var isPcOn = (homeallData.pcstate == 1);

				var isSysPowerOn = (sysPowerVoltage >= 5);
				var isBatteryLow = (battVoltage <= 22.5);
				
				var table = "";
				table += startRow();
				table += addData("isPowerCycling", isPowerCycling.toString());
				table += addData("isAutoShutdownOn", isAutoShutdownOn.toString());
				table += addData("isRebooting:", isRebooting.toString());
				table += addData("isSysPowerOn", isSysPowerOn.toString());
				table += endRow();

				table += startRow();
				table += addData("hasPcOnBeenTriggered", hasPcOnBeenTriggered.toString());
				table += addData("isPcOn", isPcOn.toString());
				table += addData("isBatteryLow", isBatteryLow.toString());
				table += endRow();
				createTable("states",table);

				table = "";
				table += startRow();
				table += addData("canPcBoot", (!isPcOn && !hasPcOnBeenTriggered).toString());
				table += addData("canPingBootPc", (!isRebooting && isAutoShutdownOn && isSysPowerOn && !hasPcOnBeenTriggered).toString());
				table += addData("canVm2ShutdownPc", (isAutoShutdownOn && !isSysPowerOn).toString());
				table += endRow();

				table += startRow();
				table += addData("isPcOnBtnEnabled", (!(!isAutoShutdownOn && (hasPcOnBeenTriggered || isRebooting || isPowerCycling || isPcOn))).toString());
				table += addData("isPcOffBtnEnabled", (!(!isAutoShutdownOn && (isRebooting || isPowerCycling || !isPcOn))).toString());
				table += addData();
				table += endRow();
				createTable("trigs",table);

				table = "";
				table += startRow();
				table += addData("vm1", sysPowerVoltage);
				table += addData("vm2", battVoltage);
				table += addData("vm3", poeVoltage);
				table += endRow();

				table += startRow();
				table += addData("vm1raw", sysPowerVoltageRaw);
				table += addData("vm2raw", battVoltageRaw);
				table += addData("vm3raw", poeVoltageRaw);
				table += endRow();
				createTable("vms",table);

				table = "";
				table += startRow();
				table += addData("MEM %", homeallData.meminfo);
				table += addData("TEMP",homeallData.tempf);
				table += addData("GPIO 1", (gpio1 == 1? "High":"Low"));
				table += endRow();
				table += startRow();
				table += addData("pwrRelay", printRelayState(homeallData.r1));
				table += addData("tmpRelay", printRelayState(homeallData.r2));
				table += addData();
				table += endRow();
				createTable("more", table);

				table = "";
				table += startRow();
				table += addData("tamper", printAlarmState(homeallData.a1));
				table += addData("door", printAlarmState(homeallData.a2));
				table += addData("surge", printAlarmState(homeallData.a3));
				table += addData("PoE", printAlarmState(homeallData.a4));
				table += endRow();
				createTable("alarms", table);
			}
		}

		debugData(1000);
	</script>

</head>

<body style="font-family: 'Roboto Condensed', sans-serif;" class="bg-info">

	<div class="containter">
		<div class="row justify-content-center">
			<div class="col-11">
				<h4><b>PHP Server Values:</b></h4>
			</div>
		</div>
		
		<?php
			$batteryPower = getBatteryVoltage();
			$powerDownThreshold = getPowerDownThreshold();
			$isBatteryLessThanThreshold = $batteryPower <= $powerDownThreshold;
		?>
		<div class="row justify-content-center">
			<div class="col-11 border rounded bg-secondary mx-2 my-2">
				<h5><b>Misc</b></h5>
				<table class="table table-sm table-striped table-light">
					<tr>
						<td><b>Battery</b></td>
						<td><?php echo $batteryPower;?></td>
						<td><b>PC down Threshold</b></td>
						<td><?php echo $powerDownThreshold; ?></td>
					</tr>
					<tr>
						<td><b>Is Battery &#60; Threshold?</b></td>
						<td><?php echo ($isBatteryLessThanThreshold?"true":"false"); ?></td>
						<td><b>ping to PC:</b></td>
						<td><?php echo (isPcPingAlive()? "alive":"dead");?></td>
					</tr>
				</table>
			</div>
		</div>
		<div class="row justify-content-center">
			<div class="col-11">
				<h4 class="px-2"><b>Dynamically Updated Values:</b></h4>
			</div>
		</div>
		<div class="row justify-content-center">
			<div class="col-11 border rounded bg-secondary mx-2 my-2">
				<h5><b>STATES</b></h5>
				<table class="table table-sm table-striped table-light">
					<tbody id ="states"></tbody>
				</table>
				<h5><b>TRIGGERABLE DEVICES</b></h5>
				<table class="table table-sm table-striped table-light">
					<tbody id ="trigs"></tbody>
				</table>
			</div>
		</div>

		<div class="row justify-content-center">
			<div class="col-6 border rounded bg-secondary mx-1 my-2">
				<h5><b>VOLTMETERS</b></h5>
				<table class="table table-sm table-striped table-light">
					<tbody id="vms"></tbody>
				</table>
			</div>

			<div class="col-5 border rounded bg-secondary mx-1 my-2">
				<h5><b>MORE</b></h5>
				<table class="table table-sm table-striped table-light">
					<tbody id="more"></tbody>
				</table>
			</div>
		</div>

		<div class="row justify-content-center">
			<div class="col-6 border rounded bg-secondary mx-1 my-2">
				<h5><b>ALARMS</b></h5>
				<table class="table table-sm table-striped table-light">
					<tbody id="alarms"></tbody>
				</table>
			</div>

			<div class="col-5 border rounded bg-secondary mx-1 my-2">
				<h5><b>Buttons</b></h5>
				<form action='debug.php' method='post'>
					<button name="cycle_pc_power" class="btn btn-danger shadow" type="submit" >Cycle PC Power</button>
				</form>

				<table class="table table-sm table-striped table-light">
					<tbody id="buttons">
					</tbody>
				</table>
			</div>
		</div>
	</div>

</body>

</html>