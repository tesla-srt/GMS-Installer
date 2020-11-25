#!/usr/bin/php
<?php
/* PHP FILE NAME: create-my-zip.php */
$date = date('n.d.Y');
$hostname = trim(file_get_contents("/etc/hostname"));
$fileName = "/mnt/usbflash/log/$hostname-LogArchive-$date.tar.gz";
exec('tar zcf '.$fileName.' /mnt/usbflash/log/*');

header('Content-Type: application/zip');
header("Content-Disposition: attachment; filename=$fileName");
header('Content-Length: ' . filesize($fileName));
readfile($fileName);




