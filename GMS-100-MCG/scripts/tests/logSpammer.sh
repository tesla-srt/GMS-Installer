#!/bin/sh
for i in $(seq 0 170);
do
	/data/custom/scripts/devices/alarm1.php
	/data/custom/scripts/devices/alarm2.php
	/data/custom/scripts/devices/alarm3.php
	/data/custom/scripts/devices/alarm4.php
	/data/custom/scripts/devices/voltmeter1.php
	/data/custom/scripts/devices/voltmeter2.php
	/data/custom/scripts/devices/voltmeter3.php
done
