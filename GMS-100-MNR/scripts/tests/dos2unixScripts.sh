#!/bin/sh
chmod -R 775 /data/custom/scripts
for file in /data/custom/scripts/*/*; do dos2unix $file; done