<?php 
    //error_reporting(E_ALL);
    include_once "Utils.php";

    class InputHandler
    {
        //standard buttons
        const btnPcOffName = "btnPcOff";
        const btnPcOnName = "btnPcOn";
        const btnSystemRebootName = "btnSystemReboot";
        const btnFanOnName = "btnFanOn";
        const btnFanOffName = "btnFanOff";
        
        //server settings buttons
        const btnServerSettingsUpdate ="btnServerSettingsUpdate";
        const chBxAutoFan = "chBxAutoFan";
        const chBxAutoShutdown = "chBxAutoShutdown";
        const textInFanOffTemp = "textInFanOffTemp";
        const textInFanOnTemp = "textInFanOnTemp";
        
        //email alert settings buttons
        const btnEmailAlertSettingsUpdate = "btnEmailAlertSettingsUpdate";
        const textInEmailAlertFrom = "textInEmailAlertFrom";
        const textInEmailAlertTo = "textInEmailAlertTo";
        const textInEmailAlertSmtpServer = "textInEmailAlertSmtpServer";
        const textInEmailAlertPort = "textInEmailAlertPort";
        const chBoxEmailAlertEnableAuth = "chBoxEmailAlertEnableAuth";
        const textInEmailAlertUsername = "textInEmailAlertUsername";
        const textInEmailAlertPassword = "textInEmailAlertPassword";
        const radioEmailAlertAuthGroup = "radioEmailAlertAuthGroup";

        const chBoxSurgeAlertActive = "chBoxSurgeAlertActive";
        const selSurgeAlertFlap = "selSurgeAlertFlap";

        const chBoxDoorAlertActive = "chBoxDoorAlertActive";
        const selDoorAlertFlap = "selDoorAlertFlap";

        const chBoxPowerAlertActive = "chBoxPowerAlertActive";
        const selPowerAlertFlap = "selPowerAlertFlap";

        const chBoxSwitchAlertActive = "chBoxSwitchAlertActive";
        const selSwitchAlertFlap = "selSwitchAlertFlap";

        const chBoxTamperAlertActive = "chBoxTamperAlertActive";
        const selTamperAlertFlap = "selTamperAlertFlap";

        const chBoxTempAlertActive = "chBoxTempAlertActive";
        const textLoTempAlert = "textLoTempAlert";
        const textHiTempAlert = "textHiTempAlert";
        const selTempAlertFlap = "selTempAlertFlap";

        const btnFanState = "btnFanState";

        public static function runScriptOnAlertSubmit()
        {
            $btn = 'alertSubmitBtn';
            if(isset($_POST[$btn]))
            {
                switch ($_POST[$btn]) 
                {
                    case InputHandler::btnPcOffName:
                        turnPcOff();
                        setHasPcOffBeenTriggered(ON_STATE);
                        InputHandler::reloadSite();
                        break;
                    case InputHandler::btnPcOnName:
                        setHasPcOffBeenTriggered(OFF_STATE);
                        turnPcOnInBackground();
                        InputHandler::reloadSite();
                        break;
                    case InputHandler::btnSystemRebootName:
                        //echo "<script> alert('". $ip ."');</script>";
                        systemRebootInBackground();
                        InputHandler::reloadSite();
                        break;
                    case InputHandler::btnFanOffName:
                        setFanOff();
                        break;
                    case InputHandler::btnFanOnName:
                        setFanOn();
                        break;
                    case InputHandler::btnFanState:
                        setFan();
                        break;
                }
               // InputHandler::reloadSite();
            }
        }
        
        //HACK reload site
        private static function reloadSite()
        {
            ?><script>document.location.href="index.php";</script><?php
        }

        //needs to run in datapull block for dbh
        public static function processServerSettingsFormSubmission($dbh, $fanOnTemp, $fanOffTemp)
        {
            if(isset($_POST[InputHandler::btnServerSettingsUpdate]))
            {
                InputHandler::setTemperature($dbh, $fanOnTemp, $fanOffTemp);
                InputHandler::setAutoPcShutdownState($dbh);

                InputHandler::reloadSite();
                //alert flag here potentially
            }
        }
        private static function setAutoPcShutdownState($dbh)
        {
            $autoShutdownState = isset($_POST[InputHandler::chBxAutoShutdown]) ? ON_STATE:OFF_STATE;
            $prevAutoShutdownState = isAutoShutdownOn();
            if($autoShutdownState != $prevAutoShutdownState)
            {
                $query = sprintf("UPDATE voltmeters SET l_en='%d' WHERE id='2';", $autoShutdownState);
                $result  = $dbh->exec($query); 

                if($autoShutdownState == ON_STATE)
                {
                    $batteryPower = getBatteryVoltage();
                    $pcState = isPcStateOn();

                    if($batteryPower !== false && $pcState !== false)
                    {
                        $powerDownThreshold = getPowerDownThreshold($dbh);
                        if($batteryPower <= $powerDownThreshold)
                        {
                            runScriptInBackground("/data/custom/scripts/devices/voltmeter2Shutdown.php");
                            
                        }
                    }
                }
                
                
            }
            //untested addition
            restartSomeServices();
        }


        private static function setTemperature($dbh, $fanOnTemp, $fanOffTemp)
        {
            //Temperature update
            $newFanOnTemp = trim($_POST[InputHandler::textInFanOnTemp]);
            $newFanOffTemp = trim($_POST[InputHandler::textInFanOffTemp]);
            $fanOffTemp = trim($fanOffTemp);
            $fanOnTemp = trim($fanOnTemp);
            if(empty($newFanOnTemp) || !is_numeric($newFanOnTemp))
            {
                $newFanOnTemp = $fanOnTemp;
            }
            if(empty($newFanOffTemp) || !is_numeric($newFanOffTemp))
            {
                $newFanOffTemp = $fanOffTemp;
            }

            if($newFanOnTemp <= $newFanOffTemp)
            {
                $newFanOnTemp = $fanOnTemp;
                $newFanOffTemp = $fanOffTemp;
            }
            else 
            {
                $query = sprintf("UPDATE temperature SET hi_t='%2.2f', hi_t_min='%2.2f';", $newFanOnTemp, $newFanOffTemp);
                $result  = $dbh->exec($query);
            }


            //Set AutoFan State
            $autoFanState = isset($_POST[InputHandler::chBxAutoFan]) ? ON_STATE:OFF_STATE;
            $query = sprintf("UPDATE temperature SET h_en='%d';", $autoFanState);
            $result  = $dbh->exec($query);

            //Toggle Fan based on temp
            if($autoFanState == ON_STATE)
            {
                $currTemp = getTemp();
                
                if($currTemp !== false)
                {
                    if($currTemp >= $newFanOnTemp)
                    {
                        setFanOn();
                    }
                    elseif($currTemp < $newFanOffTemp)
                    {
                        setFanOff();
                    }
                }
            }
            
        }


        public static function processEmailAlertSettingsFormSubmission($dbh)
        {
            if(isset($_POST[InputHandler::btnEmailAlertSettingsUpdate]))
            {
                InputHandler::stripIllegalChars();

                InputHandler::tryUpdateAlertDatabase(InputHandler::textInEmailAlertFrom, "v5", $dbh);
                InputHandler::tryUpdateAlertDatabase(InputHandler::textInEmailAlertTo, "v1", $dbh);
                InputHandler::tryUpdateAlertDatabase(InputHandler::textInEmailAlertSmtpServer, "v3", $dbh);
                InputHandler::tryUpdateAlertDatabase(InputHandler::textInEmailAlertPort, "port", $dbh);

                if(isset($_POST[InputHandler::chBoxEmailAlertEnableAuth]))
                {
                    $authCheck = CHECKED_STATE;
                    InputHandler::tryUpdateAlertDatabase(InputHandler::textInEmailAlertUsername, "v7", $dbh);
                    InputHandler::tryUpdateAlertDatabase(InputHandler::textInEmailAlertPassword, "v8", $dbh);
                }
                else
                {
                    $authCheck = UNCHECKED_STATE;
                    //clear password and username
                    InputHandler::clearAlertDatabaseField("v7", $dbh);
                    InputHandler::clearAlertDatabaseField("v8", $dbh);
                }
                $query = sprintf("UPDATE alerts SET v6='%s';", $authCheck);
                $result = $dbh->exec($query);


                if(isset($_POST[InputHandler::radioEmailAlertAuthGroup]))
                {
                    $authGroup = $_POST[InputHandler::radioEmailAlertAuthGroup];
                    if($authGroup == "startTls")
                    {
                        $ssl = UNCHECKED_STATE; //v9
                        $tls = CHECKED_STATE; //v10
                    }
                    else
                    {
                        $ssl = CHECKED_STATE;
                        $tls = UNCHECKED_STATE;
                    }
                    $query = sprintf("UPDATE alerts SET v9='%s', v10='%s';", $ssl, $tls);
                    $result  = $dbh->exec($query);
                }
            //Door Alert
                $doorAlertOn = "5.";
                $doorAlertInterval = $_POST[InputHandler::selDoorAlertFlap];
                //echo "<script>alert('".$doorAlertInterval."');</script>";
                $alertOff = "";
                $alertDoorState = isset($_POST[InputHandler::chBoxDoorAlertActive]) ? $doorAlertOn:$alertOff;
                $prevAlertDoorState = isDoorAlertOn() == true ? $doorAlertOn:$alertOff;
                //echo "<script>alert('". $alertDoorState ."');</script>";
                    if($alertDoorState != $prevAlertDoorState)
                    {
                       $query = sprintf("UPDATE io SET HI_alert_cmds='%s' WHERE (id='2' AND type='alarm');", $alertDoorState);
                       $result  = $dbh->exec($query);
                    }
                $query = sprintf("UPDATE io SET hi_flap='%s' WHERE (id='2' AND type='alarm');", $doorAlertInterval);
                $result  = $dbh->exec($query);

            //Surge Alert
                $surgeAlertInterval = $_POST[InputHandler::selSurgeAlertFlap];
                $surgeAlertOn = "6.";
                $alertSurgeState = isset($_POST[InputHandler::chBoxSurgeAlertActive]) ? $surgeAlertOn:$alertOff;
                $prevAlertSurgeState = isSurgeAlertOn() == true ? $surgeAlertOn:$alertOff;

                if($alertSurgeState != $prevAlertSurgeState)
                {
                    $query = sprintf("UPDATE io SET HI_alert_cmds='%s' WHERE (id='3' AND type='alarm');", $alertSurgeState);
                    $result  = $dbh->exec($query);
                }
                $query = sprintf("UPDATE io SET hi_flap='%s' WHERE (id='3' AND type='alarm');", $surgeAlertInterval);
                $result  = $dbh->exec($query);

            //Switch Alert
                $switchAlertInterval = $_POST[InputHandler::selSwitchAlertFlap];
                $switchAlertOn = "7.";
                $alertSwitchState = isset($_POST[InputHandler::chBoxSwitchAlertActive]) ? $switchAlertOn:$alertOff;
                $prevAlertSwitchState = isSwitchAlertOn() == true ? $switchAlertOn:$alertOff;

                if($alertSwitchState != $prevAlertSwitchState)
                {
                    $query = sprintf("UPDATE io SET LO_alert_cmds='%s' WHERE (id='4' AND type='alarm');", $alertSwitchState);
                    $result  = $dbh->exec($query);
                }
                $query = sprintf("UPDATE io SET lo_flap='%s' WHERE (id='4' AND type='alarm');", $switchAlertInterval);
                $result  = $dbh->exec($query);

            //Tamper Alert
                $tamperAlertInterval = $_POST[InputHandler::selTamperAlertFlap];
                $tamperAlertOn = "4.";
                $alertTamperState = isset($_POST[InputHandler::chBoxTamperAlertActive]) ? $tamperAlertOn:$alertOff;
                $prevAlertTamperState = isTamperAlertOn() == true ? $tamperAlertOn:$alertOff;

                if($alertTamperState != $prevAlertTamperState)
                {
                    $query = sprintf("UPDATE io SET HI_alert_cmds='%s' WHERE (id='1' AND type='alarm');", $alertTamperState);
                    $result  = $dbh->exec($query);
                }
                $query = sprintf("UPDATE io SET hi_flap='%s' WHERE (id='1' AND type='alarm');", $tamperAlertInterval);
                $result  = $dbh->exec($query);

            //Power Alert
                $powerAlertInterval = $_POST[InputHandler::selPowerAlertFlap];
                $powerAlertOn = "8.";
                $alertPowerState = isset($_POST[InputHandler::chBoxPowerAlertActive]) ? $powerAlertOn:$alertOff;
                $prevAlertPowerState = isPowerAlertOn() == true ? $powerAlertOn:$alertOff;
                if($alertPowerState != $prevAlertPowerState)
                {
                    $query = sprintf("UPDATE voltmeters SET LO_alert_cmds='%s' WHERE id='1';", $alertPowerState);
                    $result  = $dbh->exec($query);
                }
                $query = sprintf("UPDATE voltmeters SET lo_flap='%s' WHERE id='1';", $powerAlertInterval);
                $result  = $dbh->exec($query);
                
            //Temp alert
                $tempAlertInterval = $_POST[InputHandler::selTempAlertFlap];
                $alertTempState = isset($_POST[InputHandler::chBoxTempAlertActive]);
                $prevTempAlertState = isTempAlertOn();
                $alertTempLoTrigger = $_POST[InputHandler::textLoTempAlert];
                $alertTempHiTrigger = $_POST[InputHandler::textHiTempAlert];

                $query = sprintf("UPDATE custom SET tempAlertLoTrigger='%s';", $alertTempLoTrigger);
                $result  = $dbh->exec($query);
                $query = sprintf("UPDATE custom SET tempAlertHiTrigger='%s';", $alertTempHiTrigger);
                $result  = $dbh->exec($query);

                if ($alertTempState != $prevTempAlertState) {
                    $query = sprintf("UPDATE custom SET tempAlertActive='%s';", $alertTempState);
                    $result  = $dbh->exec($query);
                }
                $query = sprintf("UPDATE custom SET tempAlertFlap='%s';", $tempAlertInterval);
                $result  = $dbh->exec($query);
                if(file_exists("/data/custom/html/first_run")) {
                    unlink("/data/custom/html/first_run");
                }
                restartSomeServices();
                InputHandler::reloadSite();           
            }
        }
        private static function stripIllegalChars()
        {
            // Strip illegal characters from $_POST data
            $input_arr = array();
            foreach ($_POST as $key => $input_arr)
            {
                $_POST[$key] = preg_replace("/[^a-zA-Z0-9\s!@#$%&*()_\-=+?.,:\/]/", "", $input_arr);
            }
        }

        private static function tryUpdateAlertDatabase($postName, $databaseColumn, $dbh)
        {
            if(isset($_POST[$postName]))
            {
                $newValue = trim($_POST[$postName]);
                if(!empty($newValue))
                {
                    $query = sprintf("UPDATE alerts SET '%s'='%s';",$databaseColumn, $newValue);
                    $result = $dbh->exec($query);
                }
            }
        }

        private static function tryUpdateAlarmDatabase($postName, $databaseColumn, $newValue)
        {
            if(isset($_POST[$postName]))
            {
                $newValue = trim($_POST[$postName]);
                if(!empty($newValue))
                {
                    $query = sprintf("UPDATE io SET '%s'='%s';",$databaseColumn, $newValue);
                    $result = $dbh->exec($query);
                }
            }
        }
        private static function clearAlertDatabaseField($databaseColumn, $dbh)
        {
            $query = sprintf("UPDATE alerts SET '%s'='%s';",$databaseColumn, "");
            $result = $dbh->exec($query);
        }

        //Standard Buttons
        public static function showBtnPcOff($isEnabled = true) {InputHandler::createButton(InputHandler::btnPcOffName, "PC OFF", "danger", $isEnabled); }
        public static function showBtnPcOn($isEnabled = true) {InputHandler::createButton(InputHandler::btnPcOnName, "PC ON", "success", $isEnabled);}
        public static function showBtnSystemReboot($isEnabled = true){InputHandler::createButton(InputHandler::btnSystemRebootName, "SYSTEM REBOOT", "warning", $isEnabled);}
        
        private static function createButton($name, $buttonText, $buttonClass ="info", $isEnabled = true)
        {
            $disabledMsg ="";
            if(!$isEnabled)
            {
                $disabledMsg = "disabled";
                $buttonClass = "outline-".$buttonClass;
            }
            echo "<button id='".$name."' name='".$name."' class='btn btn-".$buttonClass." btn-block shadow' ".$disabledMsg.">".$buttonText."</button>";
        }

        public static function toggleState2CheckState($state)
        {
            echo ($state ? "checked":"");
        }

        /** public static function showFanBtnGroup($isEnabled)
        {
            $disabledMsg = "";
            $outline = "";
            if(!$isEnabled)
            {
                $disabledMsg = "disabled";
                $outline = "outline-";
            }
            echo "<div class='btn-group shadow'>
                    <button id='".InputHandler::btnFanOffName."' class='btn btn-".$outline."secondary'".$disabledMsg.">FAN OFF</button>
                    <button id='".InputHandler::btnFanOnName."' class='btn btn-".$outline."primary'".$disabledMsg.">FAN ON</button>
                </div>";
        }
        
       public static function showFanBtnGroup($isEnabled)
        {
            $disabledMsg = "";
            $outline = "";
            if(!$isEnabled)
            {
                $disabledMsg = "disabled";
                $outline = "outline-";
            }
            echo "<fieldset>
                        <legend></legend>
                  <div class='switch-toggle shadow alert alert-light'>  
                    <input type='radio' id='".InputHandler::btnFanOffName."' name='".InputHandler::btnFanOffName."'".$disabledMsg.">
                    <label class='btn' for='".InputHandler::btnFanOffName."'>FAN OFF</label>
                    <input type='radio' id='".InputHandler::btnFanOnName."' name='".InputHandler::btnFanOnName."'".$disabledMsg.">
                    <label class='btn' for='".InputHandler::btnFanOnName."'>FAN ON</label>
                    <a class='btn btn-primary'></a>
                </div></fieldset>";
        }*/


        public static function showFanBtnGroup($isEnabled)
        {
            $isChecked = "";
            $disabledMsg = "";
            $outline = "";
            if(!$isEnabled)
            {
                $disabledMsg = "disabled";

            }
            echo "<form id='fanStateFrm' method='post' action='index.php'>
                    <fieldset>
                        <legend></legend>
                  <label class='rocker rocker-small'".$disabledMsg." data-toggle='tooltip' data-placement='bottom' title='Click to change fan On/Off'>
                    <input type='checkbox' onClick='$(\"#fanStateFrm\").submit()' value='1' id='".InputHandler::btnFanState."' name='".InputHandler::btnFanState."'".$disabledMsg.">
                    <span class='switch-left'>On</span>
                    <span class='switch-right'>Off</span>
                </label></fieldset></form>";
        }

        public static function processFanState()
        {

            InputHandler::stripIllegalChars();
            if (isset($_POST[InputHandler::btnFanState])) {
                setFanOn();
            } else {
                setFanOff();
            }
        }
    }
?>