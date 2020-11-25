<?php //gets all used data from the primary database.
    //include_once "/data/custom/html/mattLib/InputHandler.php";
    
	$dbh = new PDO('sqlite:/etc/rms100.db');
	
//SYSTEM LOCATION
	$result  = $dbh->query("SELECT syslocation FROM snmp_config;");			
	foreach($result as $row)
	{
		$syslocation = $row['syslocation'];
	}

//ALARMS
	$result  = $dbh->query("SELECT * FROM io WHERE id='1' AND type='alarm';");			
	foreach($result as $row)
	{
		$a1_name = $row['name'];
	}
	
	$result  = $dbh->query("SELECT * FROM io WHERE id='2' AND type='alarm';");			
	foreach($result as $row)
	{
		$a2_name = $row['name'];
	}

	$result  = $dbh->query("SELECT * FROM io WHERE id='3' AND type='alarm';");			
	foreach($result as $row)
	{
		$a3_name = $row['name'];
	}

	$result  = $dbh->query("SELECT * FROM io WHERE id='4' AND type='alarm';");			
	foreach($result as $row)
	{
		$a4_name = $row['name'];
	}
	
	$result  = $dbh->query("SELECT * FROM io WHERE id='5' AND type='alarm';");			
	foreach($result as $row)
	{
		$a5_name = $row['name'];
	}

//RELAYS
	$result  = $dbh->query("SELECT * FROM relays WHERE id='1';");			
	foreach($result as $row)
	{
		$rly1_name = $row['name'];
		$rly1NC_color = $row['nc_color'];
		$rly1NO_color = $row['no_color'];
	}
	
	$result  = $dbh->query("SELECT * FROM relays WHERE id='2';");			
	foreach($result as $row)
	{
		$rly2_name = $row['name'];
		$rly2NC_color = $row['nc_color'];
		$rly2NO_color = $row['no_color'];
	}
	
//VOLTMETERS
    $v1LoT = "";
	$v2LoT = "";
	$v3LoT = "";
	$result  = $dbh->query("SELECT * FROM vm_polarity WHERE id='1';");			
	foreach($result as $row)
	{
		$v1_pol = $row['polarity'];
	}

	$result  = $dbh->query("SELECT * FROM vm_polarity WHERE id='2';");			
	foreach($result as $row)
	{
		$v2_pol = $row['polarity'];
	}
	
	$result  = $dbh->query("SELECT * FROM vm_polarity WHERE id='3';");			
	foreach($result as $row)
	{
		$v3_pol = $row['polarity'];
	}

	$result  = $dbh->query("SELECT * FROM voltmeters WHERE id='1';");			
	foreach($result as $row)
	{
		$v1_name = $row['name'];
        $v1LoT = $row['lo_t_max'];
	}
	
	$result  = $dbh->query("SELECT * FROM voltmeters WHERE id='2';");			
	foreach($result as $row)
	{
		$v2_name = $row['name'];
		$autoShutdownState = $row['l_en'];
        $v2LoT = $row['lo_t_max'];
	}
	
	$result  = $dbh->query("SELECT * FROM voltmeters WHERE id='3';");			
	foreach($result as $row)
	{
		$v3_name = $row['name'];
        $v3LoT = $row['lo_t_max'];
	}
	
//THROTTLE
	$result  = $dbh->query("SELECT * FROM throttle;");			
	foreach($result as $row)
	{
		$dt = $row['delay'];
	}

//TEMPERATURE
    $defaultTempUnit = '';
	$result  = $dbh->query("SELECT * FROM temperature;");
	foreach($result as $row)
	{
		$fanOnTemp = floor($row['hi_t']);
		$fanOffTemp = floor($row['hi_t_min']);
		$autoFanState = $row['h_en'];
		$defaultTempUnit = $row['default_temp'];
	}

//EMAIL ALERT
	$result = $dbh->query("SELECT * FROM alerts WHERE id='3';");//3 is first index in db
	foreach($result as $row)
	{
		$emailAlertFrom = $row['v5'];
		$emailAlertTo = $row['v1'];
		$emailAlertSmtp = $row['v3'];
		$emailAlertPort = $row['port'];

		$emailAlertAuthCheck = $row['v6'];
		$emailAlertUsername = $row['v7'];
		$emailAlertSsl = $row['v9'];
		$emailAlertStartTls = $row['v10'];

	}

//Power Alert
    $result = $dbh->query("SELECT * FROM voltmeters WHERE id='1';");
	$powerAlertState = '1';
	$powerAlertFlap = "0";
	foreach($result as $row)
	{
        $powerAlertState = $row['LO_alert_cmds'];
        $powerAlertFlap = $row['lo_flap'];
    }
    if($powerAlertState == '8.') {
        $powerAlertState = '1';
    } else {
        $powerAlertState = '0';
    }

//Door Alert
    $result = $dbh->query("SELECT * FROM io WHERE id='2' AND type='alarm';");
    $doorAlertState = '1';
    $doorAlertFlap = '0';
    foreach($result as $row)
        {
            $doorAlertState = $row['HI_alert_cmds'];
            $doorAlertFlap = $row['hi_flap'];
        }
        if($doorAlertState == '5.') {
            $doorAlertState = '1';
        } else {
            $doorAlertState = '0';
        }
//Surge Alert
    $result = $dbh->query("SELECT * FROM io WHERE id='3' AND type='alarm';");
        $surgeAlertState = '1';
        $surgeAlertFlap = "0";
        foreach($result as $row)
        {
            $surgeAlertState = $row['HI_alert_cmds'];
            $surgeAlertFlap = $row['hi_flap'];

        }
        if($surgeAlertState == '6.') {
            $surgeAlertState = '1';
        } else {
            $surgeAlertState = '0';
        }

//Tamper Alert
    $result = $dbh->query("SELECT * FROM io WHERE id='1' AND type='alarm';");
        $tamperAlertState = '1';
        $tamperAlertFlap = "0";
        foreach($result as $row)
        {
            $tamperAlertState = $row['HI_alert_cmds'];
            $tamperAlertFlap = $row['hi_flap'];
        }
        if($tamperAlertState == '4.') {
            $tamperAlertState = '1';
        } else {
            $tamperAlertState = '0';
        }
//Switch Alert
    $result = $dbh->query("SELECT * FROM io WHERE id='4' AND type='alarm';");
        $switchAlertState = '1';
        $switchAlertFlap = "0";
        foreach($result as $row)
        {
            $switchAlertState = $row['LO_alert_cmds'];
            $switchAlertFlap = $row['lo_flap'];
        }
        if($switchAlertState == '7.') {
            $switchAlertState = '1';
        } else {
            $switchAlertState = '0';
        }


        $tempAlertState = '0';
//Temperature Alert
        $result = $dbh->query("SELECT * FROM custom");
        $tempAlertLoTrigger = "0";
        $tempAlertFlap = '0';
        $tempAlertHiTrigger = "0";
        foreach($result as $row)
        {
            $tempAlertLoTrigger = $row['tempAlertLoTrigger'];
        }
        $result = $dbh->query("SELECT * FROM custom");
        foreach($result as $row)
        {
            $tempAlertHiTrigger = $row['tempAlertHiTrigger'];
            $tempAlertState = $row['tempAlertActive'];
            $tempAlertFlap = $row['tempAlertFlap'];
            $lastTempAlertTime = $row['LastAlertTime'];
        }

    $result = $dbh->query("SELECT * FROM voltmeters WHERE id='1';");

    $systemVoltage = "0";
    foreach($result as $row)
    {
        $systemVoltage = floatval($row['lo_t_max']);
    }

//echo "<script>alert('". $systemVoltage ."');</script>";

//Post processing
	InputHandler::processServerSettingsFormSubmission($dbh, $fanOnTemp, $fanOffTemp);
	InputHandler::processEmailAlertSettingsFormSubmission($dbh);
	InputHandler::runScriptOnAlertSubmit();
	InputHandler::processFanState();

	$dbh = NULL;
?>