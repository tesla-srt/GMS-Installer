#!/bin/sh

#############################################
#                                           #
# SNMP trap sending script example.         #
# Copy this file to /usr/local/webif/custom #
# and rename it to describe the trap.       #
# Make sure it is executeable,              #
# chmod 755 mynewtrapfile.sh                #
# Edit new trap file to suit your needs.    #
#                                           #
# Supports SNMP v1,v2c,v3                   #
#                                           #
# (C)EtherTek Circuits 2010                 #
#                                           #
#############################################

# More information on sending traps can be found at
# http://www.net-snmp.org/wiki/index.php/TUT:snmptrap
# http://www.net-snmp.org/wiki/index.php/TUT:snmptrap_SNMPv3


########################################
# Get IP address of RMS board.         #
# SNMP v1 only.                        #
########################################
IP=`ifconfig  | grep 'inet addr:'| grep -v '127.0.0.1' | cut -d: -f2 | awk '{ print $1}'`

########################################
# Get readings from the RMS unit.      #
# Uncomment the readings that you want #
# to include in the snmp trap.         #
########################################
#TEMPC=`cat /var/rmsdata/tempc`
#TEMPF=`cat /var/rmsdata/tempf`
VM1=`cat /var/rmsdata/vm1`
#VM2=`cat /var/rmsdata/vm2`
#VM3=`cat /var/rmsdata/vm3`
#VM4=`cat /var/rmsdata/vm4`
#VM5=`cat /var/rmsdata/vm5`
#VM6=`cat /var/rmsdata/vm6`
#ELAY1=`cat /var/rmsdata/relay1`
#RELAY2=`cat /var/rmsdata/relay2`
#RELAY3=`cat /var/rmsdata/relay3`
#ALARM1=`cat /var/rmsdata/alarm1`
#ALARM2=`cat /var/rmsdata/alarm2`
#ALARM3=`cat /var/rmsdata/alarm3`
#ALARM4=`cat /var/rmsdata/alarm4`
#ALARM5=`cat /var/rmsdata/alarm5`
#BUTTON1=`cat /var/rmsdata/button1`
#BUTTON2=`cat /var/rmsdata/button2`
#GPIO1=`cat /var/rmsdata/gpio1`
#GPIO2=`cat /var/rmsdata/gpio2`
#GPIO3=`cat /var/rmsdata/gpio3`
#GPIO4=`cat /var/rmsdata/gpio4`

#########################################
# Get variables from the RMS database.  #
# Uncomment the variables that you want #
# to include in the snmp trap.          #
#########################################
# Voltmeters
#VM1NAME=$(sqlite3shell "select name from voltmeters where id=1")
#VM2NAME=$(sqlite3shell "select name from voltmeters where id=2")
#VM3NAME=$(sqlite3shell "select name from voltmeters where id=3")
#VM4NAME=$(sqlite3shell "select name from voltmeters where id=4")
#VM5NAME=$(sqlite3shell "select name from voltmeters where id=5")
#VM6NAME=$(sqlite3shell "select name from voltmeters where id=6")

# Relays
#RELAY1NAME=$(sqlite3shell "select name from relays where id=1")
#RELAY2NAME=$(sqlite3shell "select name from relays where id=2")
#RELAY3NAME=$(sqlite3shell "select name from relays where id=3")
#RELAY1_NC_STATE_NAME=$(sqlite3shell "select state from relay_script_cmds where command='00'")
#RELAY1_NO_STATE_NAME=$(sqlite3shell "select state from relay_script_cmds where command='01'")
#RELAY2_NC_STATE_NAME=$(sqlite3shell "select state from relay_script_cmds where command='02'")
#RELAY2_NO_STATE_NAME=$(sqlite3shell "select state from relay_script_cmds where command='03'")
#RELAY3_NC_STATE_NAME=$(sqlite3shell "select state from relay_script_cmds where command='04'")
#RELAY3_NO_STATE_NAME=$(sqlite3shell "select state from relay_script_cmds where command='05'")

#if [ "$RELAY1" = "1" ]; then 
#	RELAY1_STATE=$RELAY1_NO_STATE_NAME
#else
#	RELAY1_STATE=$RELAY1_NC_STATE_NAME
#fi
#
#if [ "$RELAY2" = "1" ]; then 
#	RELAY2_STATE=$RELAY2_NO_STATE_NAME
#else
#	RELAY2_STATE=$RELAY2_NC_STATE_NAME
#fi
#
#if [ "$RELAY3" = "1" ]; then 
#	RELAY3_STATE=$RELAY3_NO_STATE_NAME
#else
#	RELAY3_STATE=$RELAY3_NC_STATE_NAME
#fi

########################################
# Set the destination ip address of    #
# your trap server.                    #
########################################
DESTIP="10.10.10.226"

########################################
# Set the community password.          #
# SNMP v1,v2 only                      #
########################################
COMMUNITYPASSWORD="public"

########################################
# Set the trap number.                 #
# This can be any number that has      #
# meaning to you. SNMP v1 only.        #
########################################
TRAPNUMBER="1"

########################################
# Set the message that you want to     #
# send to the SNMP trap server.        #
# Use the variables above to make      #
# meaningful SNMP trap messages.       #
########################################
# In the example below, $VM1 contains the Voltmeter 1 current reading.

MESSAGE="Voltmeter 1 is now at a dangerous level of $VM1"

#Some more example messages. Uncomment the proper variables above to use.

#MESSAGE="Voltmeter 2 is now at a dangerous level of $VM2"
#MESSAGE="Temperature is now at a dangerous level of $TEMPC degrees Celsius!"
#MESSAGE="Temperature is now at a dangerous level of $TEMPF degrees Fahrenheit!"
#MESSAGE="Relay 1 - $RALAY1NAME is now $RELAY1_STATE!"





###############################################
# Now send the SNMP Trap. SNMP v2c is used in #
# the example below.                          #
# SNMP v3 command below needs editing.        #
# Change the engine ID, username and password #
###############################################

# SNMP v1 COMMAND #
#snmptrap -v 1 -c $COMMUNITYPASSWORD $DESTIP '1.3.6.1.4.1.21749' $IP 6 $TRAPNUMBER '' 1.3.6.1.4.1.21749.3.2 s "$MESSAGE"

# SNMP v2c COMMAND #
snmptrap -v 2c -c $COMMUNITYPASSWORD $DESTIP '' 1.3.6.1.4.1.21749 1.3.6.1.4.1.21749.3.2 s "$MESSAGE"

# SNMP v3 COMMAND #
#snmptrap -e 0x80000523015A0000E2 -v 3 -u ethertek -l authNoPriv -a MD5 -A my_password $DESTIP '' 1.3.6.1.4.1.21749 1.3.6.1.4.1.21749.3.2 s "$MESSAGE"






