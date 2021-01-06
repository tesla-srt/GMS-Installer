var helpmsg = new Array(267); 
// helpmsg[''] = "";
// 265 so far
/*      Left CGI help     */

helpmsg['b_home'] = "The Home page shows an overview of the GMS-100 board. At a glance you can view the status of the voltmeters, relays, temperature, io pins, etc.";
helpmsg['b_ios'] = "Setup and control the GMS-100 onboard Alarms, Inputs, and Outputs.";
helpmsg['b_relays'] = "Setup and control the GMS-100 on-board latching relays.";
helpmsg['b_temperature'] = "View the GMS-100 onboard temperature sensor and set high / low threshholds.";
helpmsg['b_voltmeters'] = "Setup and view the GMS-100 onboard voltmeters and set triggers for relay scripts and email alerts.";
helpmsg['b_camera'] = "View the output of compatible Usb Web Cameras.";
helpmsg['sd_gps'] = "View the output of compatible Usb GPS units.";
helpmsg['sd_gprs'] = "Control the GMS-100 board with SMS messages using a GPRS modem.";
helpmsg['sd_extemp'] = "Monitor the output of an external USB temperature sensor.";
helpmsg['sd_uno'] = "Monitor or control the I/Os on an Ardunio UNO board.";
helpmsg['b_setup'] = "The Setup page allows you to configure your GMS-100 board.";
helpmsg['b_help'] = "Click here for a brief overview of the GMS-100 board and links to the GMS-100 website!";
helpmsg['sd_vdb'] = "View the output of a USB Voltmeter Board.";
helpmsg['sd_rdb'] = "Control a USB Relay Board.";

/*      Home / Overview CGI help       */
helpmsg['home_default'] = "Welcome to GMS-100! The home page gives you a quick overview of system statistics, alarms, gpios, relays, and voltmeters.";
helpmsg['home_stats'] = "View memory usage, disk usage, and other statistics.";

/*      IOs CGI help      */
helpmsg['ios_default'] = "GMS-100 gives you control of 5 alarm inputs, 4 general purpose pins that can be configured as inputs or outputs, and 1 push button for all of your I/O needs!";
helpmsg['b_ios_alarm'] = "Click to create notes and setup events for this alarm.";
helpmsg['b_ios_alarmsetup'] = "Configure general alarm parameters.";
helpmsg['b_ios_input'] = "Allows you to create notes and setup events for this input pin.";
helpmsg['b_ios_output'] = "Allows you to name and create notes for this output pin.";
helpmsg['b_ios_ioscripts'] = "Allows you to create I/O scripts.";
helpmsg['b_ios_gxio'] = "Allows you to create notes and setup events for this GPIO pin.";
helpmsg['sd_ios_btn1'] = "Click to setup options for Micro Push Button 1.";
helpmsg['sd_switch2lo'] = "Make the output a logic LOW.";
helpmsg['sd_switch2hi'] = "Make the output a logic HIGH.";


/*       Relays CGI help     */
helpmsg['relays_default'] = "GMS-100 gives you control of 2 onboard general purpose power relays. Want more relays? Add a USB Relay Daughter Board!";
helpmsg['b_relays_relaysetupa'] = "Relays 1 and 2 can handle up to 10 Amps @ 120VAC or 60VDC. Click here to name this relay, and enter notes on how it is hooked up.";
helpmsg['b_relays_relaycontrol_on'] = "Double Click to make this relay bridge the COM and NO contacts.";
helpmsg['b_relays_relaycontrol_off'] = "Double Click to make this relay bridge the COM and NC contacts.";
helpmsg['b_relays_relaysetupscripts'] = "Define your custom relay scripts here.";
helpmsg['b_relays_relay_db'] = "Control the relays on the Relay Daughter Board.";
helpmsg['sd_relays_db_setboot'] = "Click to save Relay Daughter Board boot up options.";
helpmsg['sd_relays_db_enable'] = "Click to enable communication to this Relay Daughter Board.";
helpmsg['sd_relays_db_disable'] = "Click to disable communication to this Relay Daughter Board.";
helpmsg['sd_relays_db_save'] = "Click to save Name and Notes for this Relay Daughter Board.";
helpmsg['sd_relays_rdb'] = "Click to select this Relay Daughter Board.";
helpmsg['sd_rdbled_set'] = "Click to save the LED status.";
helpmsg['b_relays_addrelaycript'] = "Add a new relay script.";
helpmsg['b_relays_execrelaycript'] = "Execute a script.";
helpmsg['b_relays_deleterelaycript'] = "Delete a script.";
helpmsg['relayscripts_default'] = "Define your custom relay scripts here.";
helpmsg['relay_nc_name'] = "When this relay is in the Normally Closed state it should be labeled as:";
helpmsg['relay_no_name'] = "When this relay is in the Normally Open state it should be labeled as:";


/*       Action Logs CGI help     */
helpmsg['sd_system'] = "This log file shows all system messages.";
helpmsg['sd_rmsd'] = "This log file shows GS Daemon messages.";
helpmsg['sd_rmspingd'] = "This log file shows GS Ping Daemon messages.";
helpmsg['sd_rmstimerd'] = "This log file shows GS Timer Daemon messages.";
helpmsg['sd_boa'] = "This log file shows Lighttpd Web Server messages.";
helpmsg['sd_netstat'] = "This log file shows the network status. Same as running netstat -an on the command line.";
helpmsg['sd_snmp'] = "This log file shows Net SNMP Agent messages.";
helpmsg['sd_secure'] = "This log file shows only Security related messages.";

/*       Firewall CGI help     */
helpmsg['sd_firewall_delete'] = "Delete this Firewall Rule. Make sure the Firewall is disabled before deleting the last rule!";
helpmsg['sd_firewall_add'] = "Add a new Firewall Rule to the list.";
helpmsg['firewall_default'] = "Protect your GMS-100 board by allowing only certain ip addresses to connect.";

/*       Command Line Interface CGI help     */
helpmsg['sd_command_ok'] = "Click to execute the command in the command box.";
helpmsg['sd_command_cancel'] = "Click to return to the previous menu without executing any commands.";

/*       General Setup icon on setup-c1.htm CGI help     */
helpmsg['sd_general_setup'] = "Setup company details and notes about this GMS-100 board.";
helpmsg['general_default'] = "Enter your company name, phone number, and notes about this GMS-100 board.";

/*       Firmware Update icon on setup-c1.htm CGI help     */
helpmsg['sd_firmware'] = "GMS-100 can be upgraded with the newest operating system and utillities remotely.";
helpmsg['autoupdate'] = "GMS-100 will automatically download and install the latest firmware.";

/*       Database Backup/Restore icon on setup-c1.htm CGI help     */
helpmsg['sd_database'] = "Backup or Restore your GMS-100 SQL Database.";
helpmsg['database_default'] = "Backup or Restore your GMS-100 SQL Database.";

/*       File Explorer icon on setup-c1.htm CGI help     */
helpmsg['sd_explorer'] = "View and edit files on your GMS-100 board.";
helpmsg['explorer_default'] = "Use the built in file explorer to view and edit files on your GMS-100 board.";

/*       Command Line Interface icon on setup-c1.htm CGI help     */
helpmsg['sd_cli'] = "Execute system commands and run programs in this web based CLI.";

/*       Ping Control icon on setup-c1.htm CGI help     */
helpmsg['sd_ping_control'] = "GMS-100 gives you ping monitoring of many devices. It can alert you to devices that stop responding to ping packets.";
helpmsg['ping_default'] = "Add devices to be monitored for ping timeouts.";

/*       Scripts icon on setup-c1.htm CGI help     */
helpmsg['sd_scripts'] = "Create Relay and IO scripts to automate complex actions.";
helpmsg['sd_scripts_default'] = "Create Relay and IO scripts to automate complex actions.";
helpmsg['sd_addrelayscript'] = "Create a new Relay Script.";
helpmsg['sd_addioscript'] = "Create a new IO script.";
helpmsg['ioscripts_default'] = "Define your custom IO scripts here.";


/*       One Shot Timer icon on setup-c1.htm CGI help     */
helpmsg['timers_default'] = "One second resolution timers. Use them to run a program when the time is up.";
helpmsg['sd_timer'] = "Set a timer to run a program when the time is up.";
helpmsg['sd_timers_stop'] = "Click to Stop the timer.";
helpmsg['sd_timers_start'] = "Click to reload the timer and Start counting down from the beginning.";
helpmsg['sd_timers_info'] = "Click and roll the mouse wheel, or click the - and + buttons, or simply use the keyboard to change settings.";

/*       Logging CGI help     */
helpmsg['remotesyslog_default'] = "Set the IP address of a server to receive system log messages on UDP port 514. Leave the IP blank if you do not wish to send sys log messages. Setup cloud reporting options.";
helpmsg['logging'] = "Setup remote syslog and cloud reporting options.";
helpmsg['cloud_lat'] = "Use decimal format for GS Latitude.";
helpmsg['cloud_lon'] = "Use decimal format for GS Longitude.";

/*       SNMP icon on setup-c1.htm CGI help     */
helpmsg['sd_snmp'] = "Setup configuration settings for SNMP.";
helpmsg['snmp_default'] = "Enable SNMPv1, SNMPv2, or SNMPv3 and set passwords.";
helpmsg['sd_snmpv1'] = "Click to enable SNMPv1.";
helpmsg['sd_snmpv3'] = "Click to enable SNMPv3.";

/*       CRONTAB icon on setup-c1.htm CGI help     */
helpmsg['cron_default'] = "GMS-100 uses the standard Linux CRON scheduling agent. Use Cron to run commands at a set interval. Cron has one minute resolution.";
helpmsg['sd_cron_tab_add'] = "Add a new Cron job and choose when to have it run.";
helpmsg['sd_cron_tab_exec'] = "Execute this Cron job now!";
helpmsg['sd_cron_tab_delete'] = "Delete this Cron job from the list.";

/*       Ping Target CGI help     */
helpmsg['sd_pingtarget_edit'] = "Edit this Ping Target.";
helpmsg['sd_pingtarget_stop'] = "Stop pinging a Target.";
helpmsg['sd_pingtarget_start'] = "Start pinging a Target.";
helpmsg['sd_pingtarget_delete'] = "Delete a Ping Target.";

/*       Notification (alerts) CGI help     */
helpmsg['sd_email_alert_add'] = "Add an Email Alert.";
helpmsg['sd_msn_alert_add'] = "Add an MSN Alert.";
helpmsg['sd_sms_alert_add'] = "Add an SMS Alert.";
helpmsg['sd_email_alert_add_report'] = "Add an Email Report.";
helpmsg['sd_add_snmp_trap'] = "Add an SNMP Trap Alert.";
helpmsg['sd_alert_exec'] = "Execute Notification.";
helpmsg['sd_alert_delete'] = "Delete Notification.";
helpmsg['notification_default'] = "GMS-100 can send email alerts, reports, SNMP Traps, and SMS messages when certain system events happen. Setup this functionality here.";

/*       Buttons CGI help     */
helpmsg['sd_buttons'] = "Setup custom functionality of the micro push buttons.";


/*       Relays CGI help     */
helpmsg['relaysetup_default'] = "This screen allows you to name the relay and enter notes so you can keep track of how this relay is hooked up.";

/*       Temperature CGI help     */
helpmsg['temperature_default'] = "GMS-100 gives you an onboard digital temperature sensor that can be used to alert you to extreme temperature conditions.";
helpmsg['temperature_reset'] = "Reset the LM-75 temperature chip on the GMS-100 board.";
helpmsg['thimax'] = "If the temperature rises above this trigger point, a high temperature condition exists. Alerts and Scripts will execute.";
helpmsg['thimin'] = "The High temperature condition will not reset until the temperature falls below this trigger point.";
helpmsg['tlomin'] = "The Low temperature condition will not reset until the temperature rises above this trigger point.";
helpmsg['tlomax'] = "If the temperature falls below this trigger point, a low temperature condition exists. Alerts and Scripts will execute.";
helpmsg['temperature_adj'] = "Numbers greater than 1 acts as a multiplier. Numbers less than 1 acts as a divider.";

/*       Display CGI help     */
helpmsg['display_default'] = "Decide which information blocks to display on the Home Page.";
helpmsg['display_animations'] = "If you have a slow computer, or if you have many browser tabs open, or if you just do not like the bling, uncheck this to disable screen animations.";

/*       Graph CGI help     */
helpmsg['graph_options_default'] = "Set the default graph time frame, set the graph size, reset graphs to factory default, or backup and restore the RRD graph database.";
helpmsg['graph_default'] = "View graphs of temperature, system load, and voltmeters.";
helpmsg['sd_default_graph_view'] = "Click to set the default graph view.";
helpmsg['voltmeter_graphs_reset'] = "* Warning * Click to reset Voltmeter graphs back to factory defaults.";

/*       USBisoVM CGI help     */
helpmsg['vdb_default'] = "View six isolated +/- 100 vdc 24 bit voltmeters.";
helpmsg['vdb_voltmeter_graphs_reset'] = "* Warning * Click to reset USB Voltmeter graphs back to factory defaults.";
helpmsg['vdb_voltmeter_graphs'] = "Click to view USB Voltmeter Graphs.";

/*       USB Relay Board CGI help     */
helpmsg['rdb_default'] = "Control five 10amp power relays on the USB Relay Board.";

/*       Voltmeters CGI help     */
helpmsg['voltmeters_default'] = "GMS-100 gives you the ability to accurately measure voltage of 3 independant DC sources.";
helpmsg['sd_voltmeter-graphs'] = "Click to view voltmeter Graphs.";
helpmsg['sd_graphs'] = "Click to view graphical data of system functions.";
helpmsg['sd_shunt_values'] = "Set the millivolt and amperage rating of your shunt.";
helpmsg['sd_wattmeter'] = "Turn this ADC channel into a wattmeter.";
helpmsg['sd_vm_setmode'] = "Set the mode in which this ADC channel should operate.";
helpmsg['sd_reading'] = "This is the ADC reading right now.";
helpmsg['sd_vm_name'] = "Give this voltmeter or ammeter a descriptive name.";
helpmsg['sd_vm_notes'] = "Describe what this voltmeter is monitoring.";
helpmsg['sd_vm_mode'] = "Set this ADC channel to Voltmeter Mode.";
helpmsg['sd_amp_mode'] = "Set this ADC channel to Ammeter Mode.";
helpmsg['sd_mjumper'] = "If the low voltage mode jumper has been removed from this voltmeter, uncheck the mode jumper box.";
helpmsg['sd_scale_mode'] = "Set the scale mode of this ADC channel.";
helpmsg['sd_watt_mode'] = "Click to set this ammeter to calculate watts.";
helpmsg['sd_adc_base'] = "Choose which ADC channel will be used as the base voltage for watt calculations. The chosen ADC channel must be in voltmeter mode.";
helpmsg['sd_voltmeter_polling'] = "Click to set the delay time of the voltmeter polling cycle. The longer the dealy is, the more power is saved. This useful for solar powered sites as a way to reduce power draw from the GMS-100 board.";
helpmsg['sd_graph_options'] = "Graph options include setting the slope and enabling graph boundary limits.";
helpmsg['sd_slope'] = "This option uses anti-aliasing and gives the graph a smoother look.";
helpmsg['sd_limit'] = "By default the graph will be autoscaling so that it will adjust the y-axis to the range of the data. You can change this behaviour by explicitly setting the limits. The displayed y-axis will then range at least from lower-limit to upper-limit.";
helpmsg['sd_lower'] = "Set the lower limit of this voltmeter graph.";
helpmsg['sd_upper'] = "Set the upper limit of this voltmeter graph.";
helpmsg['sd_precision'] = "Choose how many decimal places will be shown.";
helpmsg['sd_adj'] = "Use the adjustment values to set the desired reading.";
helpmsg['sd_mul'] = "Numbers greater than zero in the multiply or divide box will multiply the voltage. Numbers less than zero will divide the voltage.";
helpmsg['sd_add'] = "Numbers greater than zero in the add or subtract box will be added to the voltage. Numbers less than zero will be subtracted.";
helpmsg['sd_polarity_filter'] = "Filter out either positive or negative voltages.";
helpmsg['sd_polarity_view'] = "Switch the voltage polarity.";
helpmsg['sd_average'] = "Smooth out the readings on fluctuating voltage input. The weight factor should be between 0 and 1. The larger the number, the faster the answer is obtained. The lower the number, less bounce occurs.";
helpmsg['sd_polling'] = "Set polling ON or OFF. Turn OFF to save power if this voltmeter is not used.";
helpmsg['sd_suppress'] = "If there is an alarm condition when the board boots up or if the OK/SAVE/APPLY buttons are clicked, this setting will stop email alerts, relay scripts, and custom programs from running until the alarm condition is cleared.";
helpmsg['vhimax'] = "If the voltage rises above this trigger point, a high voltage condition exists. Alerts and Scripts will execute.";
helpmsg['vhimin'] = "The High voltage condition will not reset until the voltage falls below this trigger point.";
helpmsg['vlomin'] = "The Low voltage condition will not reset until the voltage rises above this trigger point.";
helpmsg['vlomax'] = "If the voltage falls below this trigger point, a low voltage condition exists. Alerts and Scripts will execute.";
helpmsg['ahimax'] = "If the amperage rises above this trigger point, a high amperage condition exists. Alerts and Scripts will execute.";
helpmsg['ahimin'] = "The High amperage condition will not reset until the amperage falls below this trigger point.";
helpmsg['alomin'] = "The Low amperage condition will not reset until the amperage rises above this trigger point.";
helpmsg['alomax'] = "If the amperage falls below this trigger point, a low amperage condition exists. Alerts and Scripts will execute.";
helpmsg['whimax'] = "If the wattage rises above this trigger point, a high wattage condition exists. Alerts and Scripts will execute.";
helpmsg['whimin'] = "The High wattage condition will not reset until the wattage falls below this trigger point.";
helpmsg['wlomin'] = "The Low wattage condition will not reset until the wattage rises above this trigger point.";
helpmsg['wlomax'] = "If the wattage falls below this trigger point, a low wattage condition exists. Alerts and Scripts will execute.";
helpmsg['amp_hour'] = "This is the accumulated amp hours since this voltmeter was put into amp mode.";
helpmsg['watt_hour'] = "This is the accumulated watt hours since this voltmeter was put into watt mode.";
helpmsg['override_units'] = "Depending on the voltmeter mode, the system will name the units VDC, AMPS, or WATTS. Customize the units name by checking this box.";
helpmsg['override_name'] = "Enter a custom units name to descride what is being measured. Example: PSI, KPa, Liters, etc.";
helpmsg['sd_voo'] = "The default order of operation for the adjustment is to multiply or divide first then add or subtract. When checked the order of operation is reversed.";

/*      Setup CGI help      */
helpmsg['setup_default'] = "GMS-100 gives you great control over the different services your board provides.";
helpmsg['b_ip_aliasing'] = "Setup the IP Address, Station Name, Location, and Domain Name that you want this GMS-100 unit to have.";
helpmsg['b_system_time'] = "View or set the system date and time, or change time server settings.";
helpmsg['b_statistics'] = "View statistics of various aspects of the GMS-100 board.";
helpmsg['stats_default'] = "View memory usage, disk usage, and other statistics.";
helpmsg['b_support'] = "User manuals, support contacts, etc.";
helpmsg['b_restart_services'] = "Controls for (re)starting, stopping, and checking the status of services on the GMS-100 board.";
helpmsg['b_crontab_unix'] = "Add, delete, or edit cron jobs. Use Cron to run custom commands at specified intervals";
helpmsg['b_autoinstaller'] = "Firmware updater allows you to send a new firmware image to the GMS-100 board for updates and new features.";
helpmsg['b_server_pref'] = "Configure basic control panel parameters here.";
helpmsg['b_notifications'] = "GMS-100 can send email notifications when certain system events happen. Setup this functionality here.";
helpmsg['b_action_log'] = "View whats been happening on the system, and who was doing it.";
helpmsg['b_eventmgr'] = "The event manager will do specified tasks, when certain conditions are met.";
helpmsg['b_edit_adm'] = "Edit your contact information here";
helpmsg['b_change_passwd'] = "Change the password for the admin user.";
helpmsg['b_cp_access'] = "You can control access to the GMS-100 board by IP address. Click here to configure.";
helpmsg['b_reboot'] = "Restart the GMS-100 board.";
helpmsg['b_shutdown'] = "Shutdown the GMS-100 board. This is good to do before pulling the Green Power plug.";
helpmsg['sd_devicemgr'] = "Add and Configure devices to work with GMS-100.";
helpmsg['sd_display'] = "Click here to configure display options.";
helpmsg['sd_graph'] = "Click here to configure graph options.";
helpmsg['b_power'] = "Restart or Shutdown the GMS-100 board.";
helpmsg['power_default'] = "Restart or Shutdown the GMS-100 board.";
helpmsg['password_default'] = "Change the root password for web and shell access separately, or make them both the same.";

/*      Heartbeat PHP help      */
helpmsg['heartbeat_default'] = "Configure the debug port to act as a regular com port. You can also enable or disable the onboard heartbeat led.";

/*      Service Manager CGI help      */
helpmsg['setup_svc_mgr_default'] = "GMS-100 service manager control panel.";
helpmsg['svc_mgr_none'] = "Service is not running and not set to start on boot, OR this service is not meant to stay running.";
helpmsg['svc_mgr_warn'] = "Service is running but it is not set to start on boot.";
helpmsg['svc_mgr_on'] = "Service is set to start on boot and it is running.";
helpmsg['svc_mgr_off'] = "Service is set to start on boot but it is not running.";
helpmsg['svc_mgr_start'] = "Click to start the service.";
helpmsg['svc_mgr_stop'] = "Click to stop the service.";
helpmsg['svc_mgr_restart'] = "Click to restart the service.";
helpmsg['svc_mgr_hup'] = "Click to send the HUP signal to the service.";
helpmsg['svc_mgr_del_init'] = "Click to make this service not start at boot.";
helpmsg['svc_mgr_add_init'] = "Click to make this service start at boot.";
helpmsg['svc_mgr_del'] = "Delete this service from the list.";
helpmsg['svc_mgr_refresh'] = "Refresh the services page.";
helpmsg['svc_mgr_cancel'] = "Return to the System Setup page.";
helpmsg['svc_mgr_add'] = "Click to add a new service.";


/*        General help       */
helpmsg['b_ok'] = "Click to save changes and exit.";
helpmsg['b_refresh'] = "Click to Refresh values.";
helpmsg['b_cancel'] = "Click to return to the previous page without saving changes.";
helpmsg['b_apply'] = "Click to Save and Apply changes.";
helpmsg['b_cmd_add'] = "Click to ADD Alert or Script to the Trigger list.";
helpmsg['b_cmd_del'] = "Click to Remove Alert or Script from the Trigger list.";
helpmsg['sd_cmd_add'] = "Click to ADD Alert or Script to the Selected Commands list.";
helpmsg['sd_cmd_del'] = "Click to Remove Alert or Script from the Selected Commands list.";
helpmsg['b_activate'] = "Click to Activate new IP Address immediately.";
helpmsg['sd_relays_db_setconfirmation'] = "Click to set this option.";

/*        MODEM Help       */
helpmsg['modem_default'] = "Control and test your Modem settings!";
helpmsg['sd_modem_add'] = "ADD a new MODEM.";
helpmsg['modem_mgr_delete'] = "Delete this MODEM entry.";
helpmsg['sd_modem'] = "Setup MODEMS for use with GMS-100.";

/*        GPRS Help       */
helpmsg['gprs_default'] = "GMS-100 lets you send and receive SMS messages using a GPRS modem.";
helpmsg['sd_gprs_add'] = "ADD a new GPRS modem.";
helpmsg['sd_gprs_only_one'] = "Only one GPRS modem allowed.";
helpmsg['gprs_mgr_delete'] = "Delete this GPRS entry.";

/*      GPS CGI help      */
helpmsg['gps_default'] = "GMS-100 lets you monitor GPS readings for your mobile monitoring needs!";
helpmsg['gps_mgr_delete'] = "Delete this GPS entry.";
helpmsg['sd_gps_add'] = "ADD a new GPS.";
helpmsg['sd_gps_only_one'] = "Only one USB GPS allowed.";
helpmsg['sd_gps_reset'] = "Click to reset the GPS daemon.";

/*      CAMERA CGI help      */
helpmsg['camera_mgr_delete'] = "Delete this CAMERA entry.";
helpmsg['sd_camera_add'] = "ADD a new CAMERA.";
helpmsg['sd_camera_only_one'] = "Only one USB camera allowed.";

/*      EXTEMP CGI help      */
helpmsg['extemp_mgr_delete'] = "Delete this Temperature sensor.";
helpmsg['sd_extemp_add'] = "ADD a new Temperature Sensor.";
helpmsg['sd_extemp_only_one'] = "Only one Temperature Sensor allowed.";
helpmsg['extemp_default'] = "View the External USB temperature sensor and set high / low threshholds.";
helpmsg['sd_extemp_opts'] = "Click to set options and triggers based on readings from the External USB Temperature Sensor.";
helpmsg['sd_extemp_reset'] = "Issue a soft reset to the USB bus. See power options to issue a hard reset.";
helpmsg['sd_extemp_cal'] = "Calibrate the External USB Temperature Sensor to the desired reading.";

/*      UNO CGI help      */
helpmsg['uno_mgr_delete'] = "Delete this Arduino I/O board.";
helpmsg['sd_uno_add'] = "ADD a new Arduino I/O board.";
helpmsg['sd_uno_only_one'] = "Only one Arduino UNO board allowed.";

/*      USBisoVM CGI help      */
helpmsg['sd_vdb_add'] = "Add a USB Voltmeter board for six 24 bit +/- 100 volt electrically isolated voltmeters.";
helpmsg['sd_vdb_done'] = "Only one USB voltmeter board allowed.";
helpmsg['vdb_mgr_delete'] = "Delete this USB Voltmeter Board.";

/*      USB Relay Board CGI help      */
helpmsg['sd_rdb_add'] = "Add a USB Relay board for five more 10amp power relays.";
helpmsg['sd_rdb_done'] = "Only one USB relay board allowed.";
helpmsg['rdb_mgr_delete'] = "Delete this USB Relay Board.";
helpmsg['rdb_relaysetup'] = "Relays 1 - 5 can handle up to 10 Amps @ 120VAC or 60VDC. Click here to name this relay, and enter notes on how it is hooked up.";

/*      CUSTOM CGI help      */
helpmsg['custom_mgr_delete'] = "Delete this CUSTOM device.";
helpmsg['sd_custom_add'] = "ADD a new CUSTOM device.";
helpmsg['sd_custom'] = "View or Control this Custom Device.";
helpmsg['custom_default'] = "View or Control this Custom Device.";

/*      EFOY CGI help      */
helpmsg['efoy_mgr_delete'] = "Delete this EFOY device.";
helpmsg['sd_efoy_only_one'] = "Only one EFOY device allowed.";
helpmsg['sd_efoy_add'] = "ADD a new EFOY device.";
helpmsg['efoy_default'] = "View or Control this Efoy Device.";

/*        Time Help       */
helpmsg['sd_sync_time'] = "Sync system time with time server specified.";
helpmsg['sd_save_time'] = "Save time server settings.";

/*        Firmware Help       */
helpmsg['firmware_default'] = "This is the Firmware Upgrade Area. Upload new firmware features and modifications to the GMS-100 board.";


function _SetConHelp(conhelp_name, direct)
{
//alert("UUUU" + conhelp_name + "UUUU" + direct + "UUUU");
/* We keep track of everything */
bmancontext = GetContext();
if (!bmancontext) { bmancontext = "home"; }
//alert(conhelp_name);


if (!conhelp_name) 
	{
	beval = "helpmsg['" + bmancontext + "_default']"
	//alert(beval);
	helptext = eval(beval);
	}
	
	
else 	 {	helptext = helpmsg[conhelp_name];	}

//if (helptext==NULL){helptext="Custom Page.";}
//alert(helptext);
	
top.self.document.getElementById('contexthelp_text').innerHTML = helptext;

}