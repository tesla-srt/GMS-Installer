<?php
    //error_reporting(E_ALL);
    unlink("/mnt/usbflash/running");
    $command = "grep address /etc/network/interfaces | cut -f 2 -d ' ' > /tmp/sdip";
	exec($command);
	$url = trim(file_get_contents("/tmp/sdip"));
    unlink("/tmp/sdip");
    exec("/sbin/reboot > /dev/null 2>&1 &");
?>

<!DOCTYPE html>
<html>
<head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0 shrink-to-fit=no">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
</head>
<body>
    <script>
        window.setTimeout(function(){top.window.location.replace("http://<?php echo $url;?>/index.php")}, 90000)
    </script>
    <p>Redirecting in 90 seconds... </p>
    <br>
    <a href="http://<?php echo $url;?>/index.php">Click here if you are not redirected in 90 seconds.</a>
</body>
</html>