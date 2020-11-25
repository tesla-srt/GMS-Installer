<?php //Gui Functions.
//include 'tempGraph.php';
function outputPolaritySettings($pol)
{
    if ($pol == "BOTH") {
        echo "<td style='width:5%'><span>+/- 60v</span></td>";
    } elseif ($pol == "POS") {
        echo "<td style='width:5%'><span>0&nbsp;&nbsp;to +60v</span></td>";
    } elseif ($pol == "NEG") {
        echo "<td style='width:5%'><span>0&nbsp;&nbsp;to&nbsp;&nbsp;-60v</span></td>";
    }
}

function getAlarmInnerClass()
{
    echo "'col-auto bg-col text-center border rounded px-0 py-0 mx-1 my-1 shadow'";
}

function getAlarmInnerClassAlt()
{
    echo "'col bg-col text-center border rounded px-0 py-0 mx-1 my-1 shadow'";
}

function getAlarmHeaderClass()
{
    echo "'bg-label text-center border-bottom rounded-top py-1 px-2'";
}

function getDefaultClass()
{
    echo "'col-auto bg-col text-center border rounded mx-1 my-1 shadow px-0 py-0'";
}

function getHeaderClass()
{
    echo "'bg-label border-bottom rounded-top py-1 px-2'";
}

function getLogButtonClass()
{
    echo "'btn btn-info btn-block shadow'";
}

function getLogButtonClassSmall()
{
    echo "'btn btn-sm btn-info btn-block shadow'";
}

function printEventLogName($name)
{
    echo $name . " Event Log";
}

function isOff($state)
{
    return $state == "0" ? true : false;
}

?>

<!-- Start Navbar-->
<nav class="navbar bg-label shadow-lg py-0 mb-0 sticky ">
    <a class="navbar-brand text-body py-0" style="font-size:2.25em;" href="http://microenj.com" target="_blank"><img
                src="/mattLib/images/logo1.png"></a>
    <span class="navbar-text text-center ml-auto py-0 pr-5" style="font-size:1.50em"><span data-toggle="tooltip"
                                                                                           data-placement="bottom"
                                                                                           title="<b>Hostname:</b> Change in Network Settings"><?php echo getFormattedHostname(); ?></span>  -  <span
                data-toggle="tooltip" data-placement="bottom"
                title="<b>Location:</b> Change in Network Settings"><?php echo getLocation(); ?></span></span>
    <div class="nav-item dropdown ml-auto pl-5">
        <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown">Admin</a>
        <div class="dropdown-menu dropdown-menu-right bg-col ">
            <a href="#optionsModal" class="dropdown-item" data-toggle="modal">Auto Function Settings</a>
            <a href="#alertSettingsModal" class="dropdown-item" data-toggle="modal">Email Alert Settings</a>
            <a href="setup.php" class="dropdown-item">Network Settings</a>
            <a href="setup_password.php" class="dropdown-item">Password Settings</a>
            <a href="#accessLog" id="_accessLog" class="dropdown-item" data-toggle="modal">Access Log</a>
            <a href="#helpModal" class="dropdown-item" data-toggle="modal">Help</a>
            <a href="#aboutModal" class="dropdown-item" data-toggle="modal">About</a>
        </div>
    </div>
</nav>
<!-- End Navbar-->

<div class="container-fluid">
    <!-- Start Voltmeters Block-->
    <div class="row justify-content-center">
        <div class="row flex bg-label shadow rounded border px-0 py-0 my-0">

            <div class="col-auto">
                <div id="solarPower" class="epoch gauge-small"></div>
                <label for="solarPower" class="label mx-sm-4" aria-label="solarPower" style="color:green"><i
                            class="fas fa-car-battery" style="font-size:1em;color:green"></i>&nbsp;Solar Power</label>
            </div>
            <div class="col-auto">
                <div id="pvAmps" class="epoch gauge-small"></div>
                <label for="pvAmps" class="label mx-sm-4" aria-label="pvAmps" style="color:deepskyblue"><i
                            class="fas fa-bolt" style="font-size:1em;color:deepskyblue"></i>&nbsp;PV Current</label>
            </div>

            <div class="col-auto">
                <div id="loadAmps" class="epoch gauge-small"></div>
                <label for="loadAmps" class="label mx-sm-4" aria-label="loadAmps" style="color:orangered"><i
                            class="fas fa-plug" style="font-size:1em;color:orangered"></i>&nbsp;Load Current</label>
            </div>

            <div class="col-auto">
                <div id="pvWatts" class="epoch gauge-small"></div>
                <label for="pvWatts" class="label mx-sm-4" aria-label="pvWatts" style="color:mediumpurple"><i
                            class="fas fa-atom" style="font-size:1em;color:mediumpurple"></i>&nbsp;PV Wattage</label>
            </div>

            <div class="col-auto">
                <div id="loadWatts" class="epoch gauge-small"></div>
                <label for="loadWatts" class="label mx-sm-4" aria-label="loadWatts" style="color:darkgoldenrod"><i
                            class="fas fa-atom" style="font-size:1em;color:darkgoldenrod"></i>&nbsp;Load Wattage</label>
            </div>

        </div>

        <!--        <div class ='col-9 bg-label my-0 shadow rounded border px-0 py-0' style="display:none">-->
        <!--            <canvas id="voltmeterGraph" height="275"></canvas>-->
        <!--        </div>-->

    </div>
    <div class="row my-1 mx-0 justify-content-center">
        <div class="row flex">
            <div class="col-auto px-0 mx-1">
                <div class='card bg-light w-auto'>
                    <h6 class="card-header">Volts to LVD</h6>
                    <div class="card-body">
                        <p class="card-text"><span class="odometer" id="o1">0.00</span> V</p>
                    </div>
                </div>
            </div>
            <div class="col-auto px-0 mx-1">
                <div class='card bg-light w-auto'>
                    <h6 class="card-header">Charging Amps</h6>
                    <div class="card-body">
                        <p class="card-text"><span class="odometer" id="o3">0.00</span> A</p>
                    </div>
                </div>
            </div>
            <div class="col-auto px-0 mx-1">
                <div class='card bg-light w-auto'>
                    <h6 class="card-header">Charge vs. Discharge Watts</h6>
                    <div class="card-body">
                        <p class="card-text"><span class="odometer" id="o4">0.00</span> W</p>
                    </div>
                </div>
            </div>
            <div class="col-auto px-0 mx-1">
                <div class='card bg-light w-auto'>
                    <h6 class="card-header">Approx. Battery Health</h6>
                    <div class="card-body">
                        <p class="card-text"><span class="odometer" id="o2">0.00</span> Hours</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- End Voltmeters Block-->
    <div class="row justify-content-center">
        <!-- Start Alarm Block -->
        <div class='col-auto ml-1 mr-0'>
            <div class="row">
                <div class=<?php getAlarmInnerClass(); ?>>
                    <h6 id='a1Label' class=<?php getAlarmHeaderClass(); ?>><?php echo $a1_name; ?></h6>
                    <div class="pl-3 pr-3 pb-1 text-left">
                        <i id="alarm1" class="fas fa-check-circle text-success" style="font-size:1em"></i>
                        &nbsp;&nbsp;
                        <span id='a1state' style='color:green;font-size:1em'>NORMAL</span>
                    </div>
                </div>
                <div class=<?php getAlarmInnerClassAlt(); ?>>
                    <h6 id='a2Label' class=<?php getAlarmHeaderClass(); ?>><?php echo $a2_name; ?></h6>
                    <div class="pl-3 pb-1 text-left">
                        <i id='alarm2' class="fas fa-check-circle text-success" style="font-size:1em"></i>
                        &nbsp;&nbsp;
                        <span id='a2state' style='color:green;font-size:1em'>NORMAL</span>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class=<?php getAlarmInnerClass(); ?>>
                    <h6 id='a3Label' class=<?php getAlarmHeaderClass(); ?>><?php echo $a3_name; ?></h6>
                    <div class="pl-3 pr-2 pb-1 text-left">
                        <i id='alarm3' class="fas fa-check-circle text-success" style="font-size:1em"></i>
                        &nbsp;&nbsp;&nbsp;&nbsp;
                        <span id='a3state' style='color:green;font-size:1em'>NORMAL</span>
                    </div>
                </div>
                <div class=<?php getAlarmInnerClass(); ?>>
                    <h6 id='a4Label' class=<?php getAlarmHeaderClass(); ?>><?php echo $a4_name; ?></h6>
                    <div class="pl-3 pr-3 pb-1 text-left">
                        <i id='alarm4' class="fas fa-check-circle text-success" style="font-size:1em"></i>
                        &nbsp;&nbsp;
                        <span id='a4state' style='color:green;font-size:1em'>NORMAL</span>
                    </div>
                </div>
            </div>
        </div>
        <!-- End Alarm Block -->

        <!-- PC Control -->
        <div id="pcControlCard" data-toggle="tooltip" data-placement="top"
             title="<b>Note:</b> There is a small delay in <b>status</b> while PC powers up/shuts down"
             class=<?php getDefaultClass() ?>>
            <h6 class=<?php getHeaderClass(); ?>>PC Controls</h6>
            <div class="px-2">
                <table class="table table-sm table-borderless mt-0 mb-1">
                    <tr>
                        <td><?php InputHandler::showBtnPcOn(isOff($autoShutdownState)); ?></td>
                    </tr>
                    <tr>
                        <td><?php InputHandler::showBtnPcOff(isOff($autoShutdownState)); ?></td>
                    </tr>
                </table>
                <span id="pcState">Status:</span>
            </div>
        </div>
        <!-- End Pc Control -->

        <!-- System Reboot-->
        <div class=<?php getDefaultClass(); ?>>
            <h6 class=<?php getHeaderClass(); ?>><?php echo $rly1_name . " Relay" ?></h6>
            <div class="mt-n1">

                <i id='r1Left' class="fas fa-power-off" style="font-size:2.25em;"></i>

                <!--<span style="font-size:2.25em"><b>l</b></span>
                <i id='r1Right' class=" fas fa-circle-notch text-secondary" style="font-size:2.25em"></i>-->
            </div>
            <div id='r1N' style='color:#6c757d'>Normal Operation</div>

            <div class='px-2 pb-1 mt-1 mb-1'>
                <?php InputHandler::showBtnSystemReboot(); ?>
            </div>
        </div>
        <!-- End System Reboot-->

        <!-- Start Climate Control-->
        <div class='col-auto bg-col text-center border rounded mx-1 my-1 shadow px-0 py-0'>
            <h6 class=<?php getHeaderClass(); ?>><?php echo $rly2_name . " Relay" ?></h6>
            <table class="table table-borderless table-sm mb-0 mt-n2 mb-1">
                <tr>
                    <td>
                        <i id='r2Left' class="fas fa-circle text-secondary" style="font-size:2.25em"></i>
                        <span style="font-size:2.25em"><b>l</b></span>
                        <i id='r2Right' class="fas fa-circle-notch text-secondary" style="font-size:2.25em"></i>

                        <div id='r2N' style='color:red'>Fan Off</div>
                    </td>
                    <td>
                        <table class="table table-sm table-borderless my-0">
                            <tr>
                                <td><b>Fan On:</b></td>
                                <td><span id="fanOnTemp"><?php echo $fanOnTemp; ?>°F</span></td>
                            </tr>
                            <tr>
                                <td id="tempfLabel"><b>Current:</b></td>
                                <td><span><span id='tempf'></span></span></td>
                            </tr>
                            <tr>
                                <td><b>Fan Off:</b></td>
                                <td><span id="fanOffTemp"><?php echo $fanOffTemp; ?>°F</span></td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <?php InputHandler::showFanBtnGroup(isOff($autoFanState)); ?>
                    </td>
                </tr>
            </table>
        </div>
        <!-- End Ciimate Control-->

        <!-- Start Event Logging Block-->
        <div class='col-auto bg-col text-center border rounded mx-1 my-1 shadow px-0 pt-0 pb-2'>
            <h6 class=<?php getHeaderClass(); ?>>Event Logs</h6>
            <table class="table table-borderless table-sm my-0">
                <tr>
                    <td>
                        <button id='v1Btn' class=<?php getLogButtonClassSmall() ?>><?php echo $v1_name ?></button>
                    </td>
                    <td>
                        <button id='v2Btn' class=<?php getLogButtonClassSmall() ?>><?php echo $v2_name ?></button>
                    </td>

                </tr>
                <tr>
                    <td>
                        <button id='v3Btn' class=<?php getLogButtonClassSmall() ?>><?php echo $v3_name ?></button>
                    </td>
                    <td>
                        <button id='a1Btn' class=<?php getLogButtonClassSmall() ?>><?php echo $a1_name ?></button>
                    </td>

                </tr>
                <tr>
                    <td>
                        <button id='a2Btn' class=<?php getLogButtonClassSmall() ?>><?php echo $a2_name ?></button>
                    </td>
                    <td>
                        <button id='a3Btn' class=<?php getLogButtonClassSmall() ?>><?php echo $a3_name ?></button>
                    </td>

                </tr>
                <tr>
                    <td>
                        <button id='a4Btn' class=<?php getLogButtonClassSmall() ?>><?php echo $a4_name ?></button>
                    </td>
                    <td>
                        <button id='tempBtn' class=<?php getLogButtonClassSmall() ?>>Graph View</button>
                    </td>
                </tr>
            </table>
            <!--
                        <table class="table table-borderless table-sm my-n2">

                        </table>
            -->
        </div>
        <!-- End Event Logging Block-->

        <!-- Start Info Block -->
        <div class="col-auto bg-col text-center border rounded mx-1 my-1 shadow px-0 py-0 ">
            <h6 class=<?php getHeaderClass(); ?>>System Info</h6>
            <table class="table table-sm table-borderless text-left my-0">
                <tr>
                    <td><b>System Time: </b></td>
                    <td style="color:blue" id="sys_time"></td>
                </tr>
                <tr>
                    <td><b>Pod Name: </b></td>
                    <td style="color:blue" id="info_podname"><?php echo getFormattedHostname(); ?></td>
                </tr>
                <tr>
                    <td><b>Location: </b></td>
                    <td style="color:blue" id="info_location"><?php echo getLocation(); ?></td>
                </tr>
                <tr>
                    <td><b>MAC Address:</b></td>
                    <td style="color:blue"><?php echo $mac_address; ?></td>
                </tr>
                <!-- <tr>
                     <td><b>Memory:</b></td>
                     <td>
                         <div class="progress" style="height:25px; min-width:150px" >
                             <div id='progressbar9' class="progress-bar bg-info" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">0</div>
                         </div>
                     </td>
                 </tr>
                 <tr>
                     <td><b>Disk:</b></td>
                     <td>
                         <div class="progress" style="height:25px; min-width:150px" >
                             <div id='progressbar10' class="progress-bar bg-info" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">0</div>
                         </div>
                     </td>
                 </tr>-->
                <tr>
                    <td><b>Uptime:</b></td>
                    <td><span id='uptime' style="color:blue"></span></td>
                </tr>
                <tr>
                    <td><b>Version:</b></td>
                    <td><span id="version" style="color:blue"><?php echo getFwVersion() ?></span></td>
                </tr>
            </table>
        </div>
        <!-- End Info Block -->
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

    <footer class="footer text-center"><span><?php printCopyright();?></span></footer>
    <!-- End Sticky Footer -->
</div>

<!-- Start Options Modal -->
<div id="optionsModal" class="modal fade" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content bg-col">
            <div class="modal-header bg-label">
                <h5 class="modal-title">Auto Function Settings</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <form action='index.php' method='post'>
                <div class="modal-body">
                    <div class="input-group">
                        <label for="selSysVoltage">System Voltage: </label>
                        <select class=" form-control-sm custom-select custom-select-sm" id="selSysVoltage" name="selSysVoltage">
                            <option value="11.80" <?php if ($systemVoltage == "11.80") {
                                echo "SELECTED";
                            } ?>>12 Volts
                            </option>
                            <option value="23.00" <?php if ($systemVoltage == "23.00") {
                                echo "SELECTED";
                            } ?>>24 Volts
                            </option>
                            <option value="46.00" <?php if ($systemVoltage == "46.00") {
                                echo "SELECTED";
                            } ?>>48 Volts
                            </option>
                        </select>
                    </div>
                    <p class="text-secondary ml-4"><small>Sets the Solar System Voltage. Responsible for handling LVD alerts.
                            <br> <strong>Note:</strong> Only change this if you are sure of what you're doing. Changing will cause page to "hang" on refresh.
                        </small></p>

                    <div class="custom-control custom-switch">
                        <input type="checkbox" class="custom-control-input" id="chBxAutoShutdown"
                               name="chBxAutoShutdown"
                               value="1" <?php InputHandler::toggleState2CheckState($autoShutdownState); ?>>
                        <label class="custom-control-label" for="chBxAutoShutdown">Auto PC Shutdown</label>
                    </div>
                    <p class="text-secondary ml-4"><small>Enables/Disables the PC to perform an auto soft shutdown when
                            the battery bank voltage drops below <?php echo $systemVoltage ?>v.</small></p>

                    <div class="custom-control custom-switch">
                        <input type="checkbox" class="custom-control-input" id="chBxAutoFan" name="chBxAutoFan"
                               value="1" <?php InputHandler::toggleState2CheckState($autoFanState); ?> >
                        <label class="custom-control-label" for="chBxAutoFan">Auto Fan</label>
                    </div>
                    <p class="text-secondary ml-4"><small>Enables/Disables the temperature controlled fan.</small></p>

                    <span class="pl-1">Set Auto Fan Control Temperature Range</span>
                    <table class="table table-borderless my-0">
                        <tr class="px-3">
                            <td><input id="fanOffInput" type="number" class="form-control" name="textInFanOffTemp"
                                       placeholder="Fan Off Temp: <?php sprintf("%.1f", $fanOffTemp);
                                       echo $fanOffTemp; ?>°F"></td>
                            <td><input id="fanOnInput" type="number" class="form-control" name="textInFanOnTemp"
                                       placeholder="Fan On Temp: <?php sprintf("%.1f", $fanOnTemp);
                                       echo $fanOnTemp; ?>°F"></td>
                        </tr>
                    </table>
                    <p class="text-secondary ml-4"><small>Sets the values for the temperature controlled fan. The <b>Fan
                                Off Temp</b> cannot exceed the <b>Fan On Temp</b>.</small></p>

                </div>

                <div class="modal-footer bg-label">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" name="btnServerSettingsUpdate">Update</button>
                </div>

            </form>
        </div>
    </div>
</div>
<!-- End Options Modal -->

<!-- Start Email Alert Settings -->
<div id="alertSettingsModal" class="modal fade" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content bg-col">
            <div class="modal-header bg-label">
                <h5 class="modal-title">Email Alert Settings</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <form action='index.php' id="alertSettingForm" method='post'>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group row">
                                <label for="textInEmailAlertFrom" class="col-sm-3 col-form-label">From Email</label>
                                <div class="col-sm-9">
                                    <input id="textInEmailAlertFrom" name="textInEmailAlertFrom" type="email"
                                           class="form-control" placeholder="<?php echo $emailAlertFrom; ?>">
                                    <span class="text-secondary ml-2"><small>Sets the email origin for all event alerts.</small></span>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="textInEmailAlertTo" class="col-sm-3 col-form-label">To Email</label>
                                <div class="col-sm-9">
                                    <input id="textInEmailAlertTo" name="textInEmailAlertTo" type="email"
                                           class="form-control" placeholder="<?php echo $emailAlertTo; ?>">
                                    <span class="text-secondary ml-2"><small>Sets the email destination for all event alerts.</small></span>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="textInEmailAlertSmtpServer" class="col-sm-3 col-form-label">SMTP
                                    Server</label>
                                <div class="col-lg-6">
                                    <input id="textInEmailAlertSmtpServer" name="textInEmailAlertSmtpServer" type="text"
                                           class="form-control" placeholder="<?php echo $emailAlertSmtp; ?>">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="textInEmailAlertPort" class="col-sm-3 col-form-label">Port</label>
                                <div class="col-sm-5">
                                    <input id="textInEmailAlertPort" name="textInEmailAlertPort" type="number"
                                           class="form-control" min="1" max="65536"
                                           placeholder="<?php echo $emailAlertPort; ?>">
                                </div>
                            </div>

                            <div class="custom-control custom-switch pb-2">
                                <input type="checkbox" onclick='validate();' class="custom-control-input"
                                       id="chBoxEmailAlertEnableAuth" name="chBoxEmailAlertEnableAuth"
                                       value="1" <?php echo $emailAlertAuthCheck; ?>>
                                <label class="custom-control-label" for="chBoxEmailAlertEnableAuth">Enable
                                    Authorization</label>
                            </div>

                            <div class="form-group row">
                                <label for="textInEmailAlertUsername" class="col-sm-3 col-form-label">Username</label>
                                <div class="col-sm-9">
                                    <input id="textInEmailAlertUsername" name="textInEmailAlertUsername" type="text"
                                           class="form-control" placeholder="<?php echo $emailAlertUsername; ?>">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="textInEmailAlertPassword" class="col-sm-3 col-form-label">Password</label>
                                <div class="col-sm-9">
                                    <input id="textInEmailAlertPassword" name="textInEmailAlertPassword" type="password"
                                           class="form-control" placeholder="Password">
                                </div>
                            </div>

                            <div class="custom-control custom-radio custom-control-inline">
                                <input type="radio" class="custom-control-input" name="radioEmailAlertAuthGroup"
                                       id="radioEmailAlertAuthGroup1"
                                       value="startTls" <?php echo $emailAlertStartTls; ?>>
                                <label class="custom-control-label" for="radioEmailAlertAuthGroup1">StartTLS</label>
                            </div>
                            <div class="custom-control custom-radio custom-control-inline">
                                <input type="radio" class="custom-control-input" name="radioEmailAlertAuthGroup"
                                       id="radioEmailAlertAuthGroup2" value="ssl" <?php echo $emailAlertSsl; ?>>
                                <label class="custom-control-label" for="radioEmailAlertAuthGroup2">SSL</label>
                            </div>
                        </div>
                        <!--Start Alert Toggles-->
                        <div class="col-lg-6">
                            <div class="form-group row">
                                <span class="ml-1"><strong>Email Alert & Log Feedback Preferences</strong></span>
                            </div>
                            <!--Door Alert-->
                            <div class="form-group row">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" id="chBoxDoorAlertActive"
                                           name="chBoxDoorAlertActive"
                                           value="1" <?php InputHandler::toggleState2CheckState($doorAlertState); ?> >
                                    <label class="custom-control-label" for="chBoxDoorAlertActive">Door Alert</label>
                                </div>
                                <p class="text-secondary my-0"><small>&nbsp;&nbsp;<strong>Enables/Disables</strong> the
                                        <strong>Door Contact</strong> email alert.</small></p>
                                <div class="input-group-sm">
                                    <label for="selDoorAlertFlap"><small>How often to send alert: </small></label>
                                    <select class=" form-control-sm  custom-select-sm" id="selDoorAlertFlap"
                                            name="selDoorAlertFlap">
                                        <option value="1800" <?php if ($doorAlertFlap == "1800") {
                                            echo "SELECTED";
                                        } ?>>Once Every 30 Minutes
                                        </option>
                                        <option value="3600" <?php if ($doorAlertFlap == "3600") {
                                            echo "SELECTED";
                                        } ?>>Every Hour
                                        </option>
                                        <option value="14400" <?php if ($doorAlertFlap == "14400") {
                                            echo "SELECTED";
                                        } ?>>Every 4 Hours
                                        </option>
                                        <option value="28800" <?php if ($doorAlertFlap == "28800") {
                                            echo "SELECTED";
                                        } ?>>Every 8 Hours
                                        </option>
                                        <option value="43200" <?php if ($doorAlertFlap == "43200") {
                                            echo "SELECTED";
                                        } ?>>Every 12 Hours
                                        </option>
                                        <option value="86400" <?php if ($doorAlertFlap == "86400") {
                                            echo "SELECTED";
                                        } ?>>Every 24 Hours
                                        </option>
                                    </select>
                                </div>
                            </div>

                            <!--Surge Alert-->
                            <div class="form-group row">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" id="chBoxSurgeAlertActive"
                                           name="chBoxSurgeAlertActive"
                                           value="1" <?php InputHandler::toggleState2CheckState($surgeAlertState); ?> >
                                    <label class="custom-control-label" for="chBoxSurgeAlertActive">Surge Alert</label>
                                </div>
                                <p class="text-secondary my-0"><small>&nbsp;&nbsp;<strong>Enables/Disables</strong> the
                                        <strong>Surge Protection</strong> email alert.</small></p>
                                <div class="input-group-sm">
                                    <label for="selSurgeAlertFlap"><small>How often to send alert: </small></label>
                                    <select class=" form-control-sm  custom-select-sm" id="selSurgeAlertFlap"
                                            name="selSurgeAlertFlap">
                                        <option value="1800" <?php if ($surgeAlertFlap == "1800") {
                                            echo "SELECTED";
                                        } ?>>Once Every 30 Minutes
                                        </option>
                                        <option value="3600" <?php if ($surgeAlertFlap == "3600") {
                                            echo "SELECTED";
                                        } ?>>Every Hour
                                        </option>
                                        <option value="14400" <?php if ($surgeAlertFlap == "14400") {
                                            echo "SELECTED";
                                        } ?>>Every 4 Hours
                                        </option>
                                        <option value="28800" <?php if ($surgeAlertFlap == "28800") {
                                            echo "SELECTED";
                                        } ?>>Every 8 Hours
                                        </option>
                                        <option value="43200" <?php if ($surgeAlertFlap == "43200") {
                                            echo "SELECTED";
                                        } ?>>Every 12 Hours
                                        </option>
                                        <option value="86400" <?php if ($surgeAlertFlap == "86400") {
                                            echo "SELECTED";
                                        } ?>>Every 24 Hours
                                        </option>
                                    </select>
                                </div>
                            </div>

                            <!-- Switch Alert -->
                            <div class="form-group row">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" id="chBoxSwitchAlertActive"
                                           name="chBoxSwitchAlertActive"
                                           value="1" <?php InputHandler::toggleState2CheckState($switchAlertState); ?> >
                                    <label class="custom-control-label" for="chBoxSwitchAlertActive">Switch
                                        Alert</label>
                                </div>
                                <p class="text-secondary my-0"><small>&nbsp;&nbsp;<strong>Enables/Disables</strong> the
                                        <strong>Network Switch</strong> email alert.</small></p>
                                <div class=" input-group-sm">
                                    <label for="selSwitchAlertFlap"><small>How often to send alert: </small></label>
                                    <select class=" form-control-sm  custom-select-sm" id="selSwitchAlertFlap"
                                            name="selSwitchAlertFlap">
                                        <?php if ($switchAlertFlap < "1800") {
                                            echo "<option value='" . floatval($switchAlertFlap) . "' SELECTED>DEBUG</option>";
                                        } ?>
                                        <option value="1800" <?php if ($switchAlertFlap == "1800") {
                                            echo "SELECTED";
                                        } ?>>Once Every 30 Minutes
                                        </option>
                                        <option value="3600" <?php if ($switchAlertFlap == "3600") {
                                            echo "SELECTED";
                                        } ?>>Every Hour
                                        </option>
                                        <option value="14400" <?php if ($switchAlertFlap == "14400") {
                                            echo "SELECTED";
                                        } ?>>Every 4 Hours
                                        </option>
                                        <option value="28800" <?php if ($switchAlertFlap == "28800") {
                                            echo "SELECTED";
                                        } ?>>Every 8 Hours
                                        </option>
                                        <option value="43200" <?php if ($switchAlertFlap == "43200") {
                                            echo "SELECTED";
                                        } ?>>Every 12 Hours
                                        </option>
                                        <option value="86400" <?php if ($switchAlertFlap == "86400") {
                                            echo "SELECTED";
                                        } ?>>Every 24 Hours
                                        </option>
                                    </select>
                                </div>
                            </div>

                            <!-- Tamper Alert -->
                            <div class="form-group row">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" id="chBoxTamperAlertActive"
                                           name="chBoxTamperAlertActive"
                                           value="1" <?php InputHandler::toggleState2CheckState($tamperAlertState); ?> >
                                    <label class="custom-control-label" for="chBoxTamperAlertActive">Tamper
                                        Alert</label>
                                </div>
                                <p class="text-secondary my-0"><small>&nbsp;&nbsp;<strong>Enables/Disables</strong> the
                                        <strong>Tamper</strong> email alert.</small></p>
                                <div class="input-group-sm">
                                    <label for="selTamperAlertFlap"><small>How often to send alert: </small></label>
                                    <select class=" form-control-sm custom-select-sm" id="selTamperAlertFlap"
                                            name="selTamperAlertFlap">
                                        <option value="1800" <?php if ($tamperAlertFlap == "1800") {
                                            echo "SELECTED";
                                        } ?>>Once Every 30 Minutes
                                        </option>
                                        <option value="3600" <?php if ($tamperAlertFlap == "3600") {
                                            echo "SELECTED";
                                        } ?>>Every Hour
                                        </option>
                                        <option value="14400" <?php if ($tamperAlertFlap == "14400") {
                                            echo "SELECTED";
                                        } ?>>Every 4 Hours
                                        </option>
                                        <option value="28800" <?php if ($tamperAlertFlap == "28800") {
                                            echo "SELECTED";
                                        } ?>>Every 8 Hours
                                        </option>
                                        <option value="43200" <?php if ($tamperAlertFlap == "43200") {
                                            echo "SELECTED";
                                        } ?>>Every 12 Hours
                                        </option>
                                        <option value="86400" <?php if ($tamperAlertFlap == "86400") {
                                            echo "SELECTED";
                                        } ?>>Every 24 Hours
                                        </option>
                                    </select>
                                </div>
                            </div>

                            <!--Power Alert-->
                            <div class="form-group row">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" id="chBoxPowerAlertActive"
                                           name="chBoxPowerAlertActive"
                                           value="1" <?php InputHandler::toggleState2CheckState($powerAlertState); ?> >
                                    <label class="custom-control-label" for="chBoxPowerAlertActive">Power Alert</label>
                                </div>
                                <p class="text-secondary my-0">&nbsp;&nbsp;<small><strong>Enables/Disables</strong> the
                                        <strong>Power Failure</strong> email alert.</small></p>
                                <div class="input-group-sm">
                                    <label for="selPowerAlertFlap"><small>How often to send alert: </small></label>
                                    <select class=" form-control-sm custom-select-sm" id="selPowerAlertFlap"
                                            name="selPowerAlertFlap">
                                        <option value="1800" <?php if ($powerAlertFlap == "1800") {
                                            echo "SELECTED";
                                        } ?>>Once Every 30 Minutes
                                        </option>
                                        <option value="3600" <?php if ($powerAlertFlap == "3600") {
                                            echo "SELECTED";
                                        } ?>>Every Hour
                                        </option>
                                        <option value="14400" <?php if ($powerAlertFlap == "14400") {
                                            echo "SELECTED";
                                        } ?>>Every 4 Hours
                                        </option>
                                        <option value="28800" <?php if ($powerAlertFlap == "28800") {
                                            echo "SELECTED";
                                        } ?>>Every 8 Hours
                                        </option>
                                        <option value="43200" <?php if ($powerAlertFlap == "43200") {
                                            echo "SELECTED";
                                        } ?>>Every 12 Hours
                                        </option>
                                        <option value="86400" <?php if ($powerAlertFlap == "86400") {
                                            echo "SELECTED";
                                        } ?>>Every 24 Hours
                                        </option>
                                    </select>
                                </div>
                            </div>

                            <!-- Temp Alert -->
                            <div class="form-group row">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="form-control form-control-sm custom-control-input"
                                           id="chBoxTempAlertActive" name="chBoxTempAlertActive"
                                           value="1" <?php InputHandler::toggleState2CheckState($tempAlertState); ?> >
                                    <label class="custom-control-label" for="chBoxTempAlertActive">Temperature
                                        Alert</label>
                                </div>
                                <p class="text-secondary my-0"><small><strong>Enables/Disables</strong> the Temperature
                                        <strong>email alert</strong> & Sets the <strong>target</strong>
                                        temperatures.</small></p>
                                <div class="form-group form-inline my-0">
                                    <div class="input-group input-group-sm">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text">Low:</div>
                                        </div>
                                        <input type="text" class="form-control-sm" size="13" name="textLoTempAlert"
                                               id="textLoTempAlert"
                                               placeholder="Low Temp: <?php echo "$tempAlertLoTrigger"; ?>">
                                        <div class="input-group-append">
                                            <div class="input-group-text">°<?php echo "$defaultTempUnit"; ?></div>
                                        </div>
                                    </div>
                                    <div class="input-group input-group-sm">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text">High:</div>
                                        </div>
                                        <input type="text" class="form-control-sm" size="13" name="textHiTempAlert"
                                               id="textHiTempAlert"
                                               placeholder="High Temp: <?php echo "$tempAlertHiTrigger" ?>">
                                        <div class="input-group-append">
                                            <div class="input-group-text">°<?php echo "$defaultTempUnit"; ?></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="input-group-sm">
                                    <label for="selTempAlertFlap"><small>How often to send alert: </small></label>
                                    <select class="form-control-sm custom-select-sm" id="selTempAlertFlap"
                                            name="selTempAlertFlap">
                                        <option value="1800" <?php if ($tempAlertFlap == "1800") {
                                            echo "SELECTED";
                                        } ?>>Once Every 30 Minutes
                                        </option>
                                        <option value="3600" <?php if ($tempAlertFlap == "3600") {
                                            echo "SELECTED";
                                        } ?>>Every Hour
                                        </option>
                                        <option value="14400" <?php if ($tempAlertFlap == "14400") {
                                            echo "SELECTED";
                                        } ?>>Every 4 Hours
                                        </option>
                                        <option value="28800" <?php if ($tempAlertFlap == "28800") {
                                            echo "SELECTED";
                                        } ?>>Every 8 Hours
                                        </option>
                                        <option value="43200" <?php if ($tempAlertFlap == "43200") {
                                            echo "SELECTED";
                                        } ?>>Every 12 Hours
                                        </option>
                                        <option value="86400" <?php if ($tempAlertFlap == "86400") {
                                            echo "SELECTED";
                                        } ?>>Every 24 Hours
                                        </option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer bg-label">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" name="btnEmailAlertSettingsUpdate">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- End Email Alert Settings -->

<!-- Start Help Modal -->
<div id="helpModal" class="modal fade" aria-hidden="true">
    <div class="modal-dialog ">
        <div class="modal-content bg-col">
            <div class="modal-header bg-label">
                <h5 class="modal-title">Help</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <div class="modal-body">
                <ul class="list-unstyled">
                    <li>Voltmeters:
                        <ul class="text-secondary small">
                            <li>Click on the voltmeter name above the graph to Show/Hide the specified values.</li>
                            <li>Hovering over a point will display a pop up of the specific voltage value.</li>
                        </ul>
                    </li>
                    <li>Event Logs:
                        <ul class="text-secondary small">
                            <li>All event logs will display a new log when a NORMAL or FAULT event occurs.</li>
                            <li>An email alert is sent out on all FAULT states, when voltmeters drop below their
                                threshold, or when system power is restored.
                            </li>
                        </ul>
                    </li>
                    <li>PC Status:
                        <ul class="text-secondary small">
                            <li>The PC will be pinged frequently to monitor it's ON/OFF state.</li>
                        </ul>
                    </li>
                    <li>System Reboot:
                        <ul class="text-secondary small">
                            <li>
                                If the PC is on when you click on System Reboot, it will perform a soft PC shutdown,
                                with a duration of 40 seconds.
                                Once complete, it will cycle the power for 5 seconds, which reboots all connected
                                devices.
                            </li>
                            <li>You cannot use System Reboot when battery is under 22.5v (Low Power Mode). This is
                                ignored when Auto Pc Shutdown is disabled.
                            </li>
                        </ul>
                    </li>
                    <li>Auto Fan:
                        <ul class="text-secondary small">
                            <li>Disabling the Auto Fan gives you manual ON/OFF control.</li>
                        </ul>
                    </li>
                    <li>Auto PC Shutdown:
                        <ul class="text-secondary small">
                            <li>enabling the Auto PC Shutdown will disable manual PC ON/OFF control.</li>
                            <li>If battery power is below 22.5v, and Auto PC Shutdown is enabled, it will also prevent
                                you from doing a System Reboot.
                                You can regain System Reboot control if you disable the Auto PC Shutdown feature.
                            </li>
                            <li>If the PC is OFF, and System Power is ON, it’ll attempt to turn the PC on every minute.
                                This function is ignored during a System Reboot, or if Auto PC Shutdown is disabled.
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>

            <div class="modal-footer bg-label">
                <a href="/mattLib/logarchive.php" target="_blank" class="btn btn-warning" id="dlLogArchive">Download Log
                    Archive</a>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<!-- End Help Modal -->

<!-- Start About Modal -->
<div id="aboutModal" class="modal fade" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content bg-col">
            <div class="modal-header bg-label">
                <h5 class="modal-title">About</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <div class="modal-body text-center">
                <h4>Grid Surfer Monitoring System</h4>

                <a href="http://www.gridsurfer.net/index.html"><h5>www.gridsurfer.net</h5></a>

                <small><?php printCopyright(); ?></small>
            </div>

            <div class="modal-footer bg-label">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<!-- End About Modal -->

<!-- Start Access Log Modal -->
<div id="accessLog" class="modal fade">.
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content bg-col">
            <div class="modal-header bg-label">
                <h5 id="accessLogTitle" class="modal-title">Access Logs</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div id='accessLogText' class="modal-body" style="white-space: pre-line">
            </div>
            <div class="modal-footer bg-label">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <input type="button" class="btn btn-primary"
                       onclick="guiHandler.printLog('accessLogTitle','accessLogText', getHostName())"
                       value="Export/Print"/>
            </div>
        </div>
    </div>
</div>
<!-- End Access Log Modal -->

<!-- Start Alarm Log Modals -->
<div id="a1Log" class="modal fade">.
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content bg-col">
            <div class="modal-header bg-label">
                <h5 id="a1LogTitle" class="modal-title"><?php printEventLogName($a1_name); ?></h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div id='a1LogText' class="modal-body" style="white-space: pre-line">
            </div>
            <div class="modal-footer bg-label">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <input type="button" class="btn btn-primary"
                       onclick="guiHandler.printLog('a1LogTitle','a1LogText', getHostName())" value="Export/Print"/>
            </div>
        </div>
    </div>
</div>

<div id="a2Log" class="modal fade">.
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content bg-col">
            <div class="modal-header bg-label">
                <h5 id="a2LogTitle" class="modal-title"><?php printEventLogName($a2_name); ?></h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div id='a2LogText' class="modal-body" style="white-space: pre-line">
            </div>
            <div class="modal-footer bg-label">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <input type="button" class="btn btn-primary"
                       onclick="guiHandler.printLog('a2LogTitle','a2LogText', getHostName())" value="Export/Print"/>
            </div>
        </div>
    </div>
</div>

<div id="a3Log" class="modal fade">.
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content bg-col">
            <div class="modal-header bg-label">
                <h5 id="a3LogTitle" class="modal-title"><?php printEventLogName($a3_name); ?></h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div id='a3LogText' class="modal-body" style="white-space: pre-line">
            </div>
            <div class="modal-footer bg-label">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <input type="button" class="btn btn-primary"
                       onclick="guiHandler.printLog('a3LogTitle','a3LogText', getHostName())" value="Export/Print"/>
            </div>
        </div>
    </div>
</div>

<div id="a4Log" class="modal fade">.
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content bg-col">
            <div class="modal-header bg-label">
                <h5 id="a4LogTitle" class="modal-title"><?php printEventLogName($a4_name); ?></h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div id='a4LogText' class="modal-body" style="white-space: pre-line">
            </div>
            <div class="modal-footer bg-label">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <input type="button" class="btn btn-primary"
                       onclick="guiHandler.printLog('a4LogTitle','a4LogText', getHostName())" value="Export/Print"/>
            </div>
        </div>
    </div>
</div>
<!-- End Alarm Log Modals -->

<!-- Start VM Log Modals -->
<div id="v1Log" class="modal fade">.
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content bg-col">
            <div class="modal-header bg-label">
                <h5 id="v1LogTitle" class="modal-title"><?php echo $v1_name; ?> Event Log</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div id='v1LogText' class="modal-body" style="white-space: pre-line">
            </div>
            <div class="modal-footer bg-label">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <input type="button" class="btn btn-primary"
                       onclick="guiHandler.printLog('v1LogTitle','v1LogText', getHostName())" value="Export/Print"/>
            </div>
        </div>
    </div>
</div>

<div id="v2Log" class="modal fade">.
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content bg-col">
            <div class="modal-header bg-label">
                <h5 id="v2LogTitle" class="modal-title"><?php echo $v2_name; ?> Event Log</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div id='v2LogText' class="modal-body" style="white-space: pre-line">
            </div>
            <div class="modal-footer bg-label">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <input type="button" class="btn btn-primary"
                       onclick="guiHandler.printLog('v2LogTitle','v2LogText', getHostName())" value="Export/Print"/>
            </div>
        </div>
    </div>
</div>

<div id="v3Log" class="modal fade">.
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content bg-col">
            <div class="modal-header bg-label">
                <h5 id="v3LogTitle" class="modal-title"><?php echo $v3_name; ?> Event Log</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div id='v3LogText' class="modal-body" style="white-space: pre-line">
            </div>
            <div class="modal-footer bg-label">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <input type="button" class="btn btn-primary"
                       onclick="guiHandler.printLog('v3LogTitle','v3LogText', getHostName())" value="Export/Print"/>
            </div>
        </div>
    </div>
</div>
<!-- End VM Log Modal -->

<!-- Start Alert Modal -->
<div id="alertModal" class="modal fade" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content bg-label">
            <div class="modal-header border-0">
                <h5 class="modal-title">Confirm Action</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div id='v3LogText' class="modal-body">
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <form name='confirmationForm' action='index.php' method='post'>
                        <button name='alertSubmitBtn' id="alertSubmitBtn" type="submit" class="btn btn-primary"
                                method='post'>Confirm
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- End Alert Modal -->

<!-- Start Temp Modal -->
<div id="tempLog" class="modal fade" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content bg-label">
            <div class="modal-header border-0">
                <h5 class="modal-title">Graph View</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <form action='index.php' method='post'>
                <div class="modal-body">
                    <div role="tab-panel">
                        <ul class="nav nav-pills nav-fill" role="tablist">
                            <li role="presentation" class="nav-item">
                                <a href="#tempTab" aria-controls="tempTab" class="nav-link active" role="tab"
                                   data-toggle="tab">Temperature</a>
                            </li>
                            <li role="presentation" class="nav-item">
                                <a href="#voltTab" aria-controls="voltTab" class="nav-link" role="tab"
                                   data-toggle="tab">Energy</a>
                            </li>
                        </ul>
                        <div class="tab-content">
                            <div role="tabpanel" class="tab-pane active" id="tempTab">
                                <div class="form-group row">
                                    <img src="/mattLib/images/400x100.png" id="tempGraphPng" width="99%" height="50%">
                                </div>
                                <p class="my-1"><strong>Choose a date then, press update.</strong></p>

                                <div class="form-group row">
                                    <label for="tempStartDate" class="col-form-label">Start Date/Time</label>

                                    <div class="col-sm-4">
                                        <div class="input-group date" id="tempStartPicker" data-target-input="nearest">
                                            <input type="text" id="tempStartDate"
                                                   class="form-control datetimepicker-input"
                                                   data-target="#tempStartPicker" name="tempStartDate"/>
                                            <div class="input-group-append" data-target="#tempStartPicker"
                                                 data-toggle="datetimepicker">
                                                <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="tempEndDate" class="col-form-label mx-1">End Date/Time</label>
                                    <div class="col-sm-4">
                                        <div class="input-group date" id="tempEndPicker" data-target-input="nearest">
                                            <input type="text" id="tempEndDate"
                                                   class="form-control datetimepicker-input"
                                                   data-target="#tempEndPicker" name="tempEndDate"/>
                                            <div class="input-group-append" data-target="#tempEndPicker"
                                                 data-toggle="datetimepicker">
                                                <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer bg-label py-2">
                                    <button type="button" class="btn btn-primary"
                                            onClick="getCustomTempGraph($('#tempStartDate').val(), $('#tempEndDate').val())"
                                            name="btnTempGraphUpdate" data-toggle="tooltip" data-placement="left"
                                            title="Click to update displayed Graph with your options">Update
                                    </button>
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                    <button type="button" class="btn btn-warning" onClick="downloadTempGraph()">Download
                                        PNG
                                    </button>
                                    <button type="button" class="btn btn-warning" onClick="downloadTempCsv();">Download
                                        CSV
                                    </button>
                                </div>
                            </div>
                            <div role="tabpanel" class="tab-pane" id="voltTab">
                                <div class="form-group row">
                                    <img src="/mattLib/images/400x100.png" id="vmGraphPng" width="99%" height="50%">
                                </div>
                                <div class="form-group row">
                                    <table class="table table-borderless table-sm mb-n4 mt-n3">
                                        <form id="vmRadFrm">
                                            <tr>
                                                <td>
                                                    <input type="radio" id="vm1Radio" name="vmRadio" value="vm1"
                                                           checked>
                                                    <label for="vm1Radio"><?php echo $v1_name ?></label></td>
                                                <td>
                                                    <input type="radio" id="vm2Radio" name="vmRadio" value="vm2">
                                                    <label for="vm2Radio"><?php echo $v2_name ?></label>
                                                </td>
                                                <td>
                                                    <input type="radio" id="vm3Radio" name="vmRadio" value="vm3">
                                                    <label for="vm3Radio"><?php echo $v3_name ?></label>
                                                </td>
                                            </tr>
                                        </form>
                                    </table>
                                </div>
                                <p class="my-1"><strong>Choose a date then, press update.</strong></p>
                                <div class="form-group row">
                                    <label for="vmStartDate" class="col-form-label">Start Date/Time</label>
                                    <div class="col-sm-4">
                                        <div class="input-group date" id="vmStartPicker" data-target-input="nearest">
                                            <input type="text" id="vmStartDate"
                                                   class="form-control datetimepicker-input"
                                                   data-target="#vmStartPicker" name="vmStartDate"/>
                                            <div class="input-group-append" data-target="#vmStartPicker"
                                                 data-toggle="datetimepicker">
                                                <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                            </div>
                                        </div>
                                        <!-- <input type="text" class="form-control" id="vmStartDate" name="vmStartDate" title="d-MMM-yyyy">-->
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="vmEndDate" class="col-form-label mx-1">End Date/Time</label>
                                    <div class="col-sm-4">
                                        <div class="input-group date" id="vmEndPicker" data-target-input="nearest">
                                            <input type="text" id="vmEndDate" class="form-control datetimepicker-input"
                                                   data-target="#vmEndPicker" name="vmEndDate"/>
                                            <div class="input-group-append" data-target="#vmEndPicker"
                                                 data-toggle="datetimepicker">
                                                <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                            </div>
                                        </div>
                                        <!--<input class="datepicker" id="vmEndDate" name="vmEndDate" title="d-MMM-yyyy">-->
                                    </div>
                                </div>
                                <div class="modal-footer bg-label py-2">
                                    <button type="button" class="btn btn-primary"
                                            onClick="getCustomVMGraph($('#vmStartDate').val(), $('#vmEndDate').val())"
                                            name="btnVMGraphUpdate" data-toggle="tooltip" data-placement="left"
                                            title="Click to update displayed Graph with your options">Update
                                    </button>
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                    <button type="button" class="btn btn-warning" onClick="downloadVMGraph()">Download
                                        PNG
                                    </button>
                                    <button type="button" class="btn btn-warning" onClick="downloadVMCsv();">Download
                                        CSV
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- End Temp Modal -->

<div class="loading-modal modal fade" id="loadMe" tabindex="-1" role="dialog" aria-labelledby="loadMeLabel">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-body text-center">
                <div class="loader"></div>
                <div class="loader-txt">
                    <p>
                        <br>Refreshing...<br></p>
                </div>
            </div>
        </div>
    </div>
</div>

<!--    <div class="loading-modal modal fade bd-example-modal-lg" data-backdrop="static" data-keyboard="false" tabindex="-1">-->
<!--        <div class="modal-dialog modal-sm">-->
<!--            <div class="modal-content" style="width: 48px">-->
<!--                <div class="spinner-border text-info" role="status"></div>-->
<!--                <div class="modal-body"><strong>Loading...</strong></div>-->
<!--            </div>-->
<!--        </div>-->
<!--    </div>-->


<script type="text/javascript" src="mattLib/dependencies/odometer.min.js"></script>
<script>

    $('input[type=radio][name=vmRadio]').change(function () {
        //setVMGraphImage('hour');
        var s = $('#vmStartDate').val();
        var e = $('#vmEndDate').val();
        n = $("input[name='vmRadio']:checked");

        if (s.length < 1 || e.length < 1) {
            n.prop('checked', false);

            alert('Please choose a date.');

            $('#vm1radio').prop('checked', true);

        } else {
            getCustomVMGraph(s, e);
        }
    });

    function validate() {
        if (document.getElementById('chBoxEmailAlertEnableAuth').checked) {
            $('#textInEmailAlertUsername').removeAttr('disabled');
            $('#textInEmailAlertPassword').removeAttr('disabled');
            $('input:radio').removeAttr('disabled');

        } else {
            $('#textInEmailAlertUsername').attr('disabled', true);
            $('#textInEmailAlertPassword').attr('disabled', true);
            $('#alertSettingsModal input:radio').attr('disabled', true);
        }
    }

    $(document).ready(function () {
        validate();
        var t = setInterval(startTime, 1000);
    });

    $(function () {
        $('[data-toggle="tooltip"]').tooltip({html: true});

        $('#tempStartPicker').datetimepicker({
            sideBySide: true,
            toolbarPlacement: 'top',
            useCurrent: true,
            showToday: true,
            showClear: true,
            showClose: true,
            icons: {
                time: "fas fa-clock",
                date: "fas fa-calendar-alt",
                up: "fas fa-arrow-up",
                down: "fas fa-arrow-down"
            }
        });
        $('#tempEndPicker').datetimepicker({
            sideBySide: true,
            toolbarPlacement: 'top',
            useCurrent: true,
            showToday: true,
            showClear: true,
            showClose: true,
            icons: {
                time: "fas fa-clock",
                date: "fas fa-calendar-alt",
                up: "fas fa-arrow-up",
                down: "fas fa-arrow-down"
            }
        });

        $('#vmStartPicker').datetimepicker({
            sideBySide: true,
            toolbarPlacement: 'top',
            useCurrent: true,
            showToday: true,
            showClear: true,
            showClose: true,
            icons: {
                time: "fas fa-clock",
                date: "fas fa-calendar-alt",
                up: "fas fa-arrow-up",
                down: "fa fa-arrow-down"
            }
        });
        $('#vmEndPicker').datetimepicker({
            sideBySide: true,
            toolbarPlacement: 'top',
            useCurrent: true,
            showToday: true,
            showClear: true,
            showClose: true,
            icons: {
                time: "fas fa-clock",
                date: "fas fa-calendar-alt",
                up: "fas fa-arrow-up",
                down: "fas fa-arrow-down"
            }
        });
    })

    function startTime() {
        var today = new Date();
        var h = today.getHours();
        var m = today.getMinutes();
        var s = today.getSeconds();
        m = checkTime(m);
        s = checkTime(s);
        $("#sys_time").html(h + ":" + m + ":" + s);
    }

    function checkTime(i) {
        if (i < 10) {
            i = "0" + i
        }
        ;  // add zero in front of numbers < 10
        return i;
    }

    <?php if (!is_writable("/mnt/usbflash/root")) {
        echo"swal({";
        echo"  title:'Filesystem Error!',";
        echo"  text: 'Please Contact Support.',";
        echo"  type: 'error',";
        echo"  confirmButtonText: 'OK'";
        echo"});"; } ?>
</script>