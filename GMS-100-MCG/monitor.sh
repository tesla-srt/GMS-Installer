#!/bin/sh
# (C) EtherTek Circuits 2011 Dan Pattison
# Simple shell script to log amperage values from adc1 and adc2 also voltage from adc3 and the temperature in celsius.
# Run this script from Cron every minute or whatever.
# Filename changes every day, stored in ram filesystem.
# Comma delimited. TIME,ADC1 as amps,ADC2 as amps,ADC3 as volts, Temperature in celsius

# log file stored in ram, but could be on a USB flash stick also
OUTPUT_FILENAME="/tmp/`date +%b%d-%y`.log"

echo -n `date +%H:%M:%S,` >> $OUTPUT_FILENAME

VM1_RAW=`cat /var/rmsdata/vm1`
VM1_AMP_RATING=`echo "select shunta from voltmeters where id=1;" | sqlite /etc/rms100.db`
VM1_MV_RATING=`echo "select shuntmv from voltmeters where id=1;" | sqlite /etc/rms100.db`
VM1_PRECISION=`echo "select per from voltmeters where id=1;" | sqlite /etc/rms100.db`

#AWK can handle floats so the kung-foo below formats the reading based
#on the precision stored in the SQL database.
[ $VM1_PRECISION -eq 0 ] && VM1_AWKSCRIPT=' { printf( "%3.0f", ($1 / $2) * ($3 * 1000) ) } '
[ $VM1_PRECISION -eq 1 ] && VM1_AWKSCRIPT=' { printf( "%3.1f", ($1 / $2) * ($3 * 1000) ) } '
[ $VM1_PRECISION -eq 2 ] && VM1_AWKSCRIPT=' { printf( "%3.2f", ($1 / $2) * ($3 * 1000) ) } '
[ $VM1_PRECISION -eq 3 ] && VM1_AWKSCRIPT=' { printf( "%3.3f", ($1 / $2) * ($3 * 1000) ) } '
[ $VM1_PRECISION -eq 4 ] && VM1_AWKSCRIPT=' { printf( "%3.4f", ($1 / $2) * ($3 * 1000) ) } '
[ $VM1_PRECISION -eq 5 ] && VM1_AWKSCRIPT=' { printf( "%3.5f", ($1 / $2) * ($3 * 1000) ) } '
[ $VM1_PRECISION -eq 6 ] && VM1_AWKSCRIPT=' { printf( "%3.6f", ($1 / $2) * ($3 * 1000) ) } '

echo $VM1_AMP_RATING $VM1_MV_RATING $VM1_RAW | awk "$VM1_AWKSCRIPT" >> $OUTPUT_FILENAME
echo -n "," >> $OUTPUT_FILENAME

VM2_RAW=`cat /var/rmsdata/vm2`
VM2_AMP_RATING=`echo "select shunta from voltmeters where id=2;" | sqlite /etc/rms100.db`
VM2_MV_RATING=`echo "select shuntmv from voltmeters where id=2;" | sqlite /etc/rms100.db`
VM2_PRECISION=`echo "select per from voltmeters where id=2;" | sqlite /etc/rms100.db`

#AWK can handle floats so the kung-foo below formats the reading based
#on the precision stored in the SQL database.
[ $VM2_PRECISION -eq 0 ] && VM2_AWKSCRIPT=' { printf( "%3.0f", ($1 / $2) * ($3 * 1000) ) } '
[ $VM2_PRECISION -eq 1 ] && VM2_AWKSCRIPT=' { printf( "%3.1f", ($1 / $2) * ($3 * 1000) ) } '
[ $VM2_PRECISION -eq 2 ] && VM2_AWKSCRIPT=' { printf( "%3.2f", ($1 / $2) * ($3 * 1000) ) } '
[ $VM2_PRECISION -eq 3 ] && VM2_AWKSCRIPT=' { printf( "%3.3f", ($1 / $2) * ($3 * 1000) ) } '
[ $VM2_PRECISION -eq 4 ] && VM2_AWKSCRIPT=' { printf( "%3.4f", ($1 / $2) * ($3 * 1000) ) } '
[ $VM2_PRECISION -eq 5 ] && VM2_AWKSCRIPT=' { printf( "%3.5f", ($1 / $2) * ($3 * 1000) ) } '
[ $VM2_PRECISION -eq 6 ] && VM2_AWKSCRIPT=' { printf( "%3.6f", ($1 / $2) * ($3 * 1000) ) } '

echo $VM2_AMP_RATING $VM2_MV_RATING $VM2_RAW | awk "$VM2_AWKSCRIPT" >> $OUTPUT_FILENAME
echo -n "," >> $OUTPUT_FILENAME

VM3_RAW=`cat /var/rmsdata/vm3`
echo -n "$VM3_RAW," >> $OUTPUT_FILENAME

TEMPC=`cat /var/rmsdata/tempc`
echo -n "$TEMPC" >> $OUTPUT_FILENAME
echo "" >> $OUTPUT_FILENAME


exit 0
