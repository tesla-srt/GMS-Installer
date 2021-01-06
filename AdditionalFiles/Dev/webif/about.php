<?php
	error_reporting(E_ALL);
	include "lib.php";
	$hostname = trim(file_get_contents("/etc/hostname"));
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <!-- Page title -->
    <title><?php echo $hostname; ?></title>
    <link rel="shortcut icon" type="image/ico" href="rms100favicon.ico?<?php echo rand(); ?>" />

    <!-- CSS -->
    <link rel="stylesheet" href="css/fontawesome/css/font-awesome.css" />
    <link rel="stylesheet" href="css/animate.css" />
    <link rel="stylesheet" href="css/bootstrap.css" />
    <link rel="stylesheet" href="css/ethertek.css">
    
    <!-- Java Scripts -->
		<script src="javascript/jquery.min.js"></script>
		<script src="javascript/bootstrap.min.js"></script>
		<script src="javascript/conhelp.js"></script>
		<script src="javascript/ethertek.js"></script>
		<script language="javascript" type="text/javascript">
			SetContext('setup');
			window.onload = function() 
        {

            /* --- config start --- */

            var starfieldCanvasId     = "starfieldCanvas", // id of the canvas to use
                framerate             = 60,                // frames per second this animation runs at (this is not exact)
                numberOfStarsModifier = 0.3,               // Number of stars, higher numbers than 1.0 have performance impact
                flightSpeed           = 0.02;              // speed of the flight, the higher this number the faster

            /* ---- config end ---- */

            var canvas        = document.getElementById(starfieldCanvasId),
                context       = canvas.getContext("2d"),
                width         = canvas.width,
                height        = canvas.height,
                numberOfStars = width * height / 1000 * numberOfStarsModifier,
                dirX          = width / 2,
                dirY          = height / 2,
                stars         = [],
                TWO_PI        = Math.PI * 2;

            // initialize starfield
            for(var x = 0; x < numberOfStars; x++) 
            {
                stars[x] = 
                {
                    x: range(0, width),
                    y: range(0, height),
                    size: range(0, 1)
                };
            }

            // when mouse moves over canvas change middle point of animation
            canvas.onmousemove = function(event) 
            {
                dirX = event.offsetX,
                dirY = event.offsetY;
            }

            // start tick at specified fps
            window.setInterval(tick, Math.floor(1000 / framerate));

            // main routine
            function tick() 
            {
                var oldX,
                    oldY;

                // reset canvas for next frame
                context.clearRect(0, 0, width, height);

                for(var x = 0; x < numberOfStars; x++) 
                {
                    // save old status
                    oldX = stars[x].x;
                    oldY = stars[x].y;

                    // calculate changes to star
                    stars[x].x += (stars[x].x - dirX) * stars[x].size * flightSpeed;
                    stars[x].y += (stars[x].y - dirY) * stars[x].size * flightSpeed;
                    stars[x].size += flightSpeed;

                    // if star is out of bounds, reset
                    if(stars[x].x < 0 || stars[x].x > width || stars[x].y < 0 || stars[x].y > height) 
                    {
                        stars[x] = 
                        {
                            x: range(0, width),
                            y: range(0, height),
                            size: 0
                        };
                    }

                    // draw star
                    context.strokeStyle = "rgba(255, 255, 255, " + Math.min(stars[x].size, 1) + ")";
                    context.lineWidth = stars[x].size;
                    context.beginPath();
                    context.moveTo(oldX, oldY);
                    context.lineTo(stars[x].x, stars[x].y);
                    context.stroke();
                }
                context.font = "30px Comic Sans MS";
								context.fillStyle = "red";
								context.textAlign = "center";
								context.fillText("RMS-100", canvas.width/2, 150); 
								context.fillText("(C)2016 EtherTek Circuits", canvas.width/2, 250);
								context.fillStyle = "green";
								context.fillText("Remote Monitoring Made Easy!", canvas.width/2, 350);
								context.fillStyle = "blue";
								context.fillText("www.RemoteMonitoringSystems.ca", canvas.width/2, 450);
            }

            // get a random number inside a range
            function range(start, end) 
            {
                return Math.random() * (end - start) + start;
            }
        };
		</script>
		
</head>
<body class="fixed-navbar fixed-sidebar">
<div class='splash'> <div class='solid-line'></div><div class='splash-title'><h1>Loading... Please Wait...</h1><div class='spinner'> <div class='rect1'></div> <div class='rect2'></div> <div class='rect3'></div> <div class='rect4'></div> <div class='rect5'></div> </div> </div> </div>

<!--[if lt IE 7]>
<p class="alert alert-danger">You are using an <strong>old</strong> web browser. Please upgrade your browser to improve your experience.</p>
<![endif]-->

<?php start_header(); ?>

<?php left_nav("setup"); ?>
<script language="javascript" type="text/javascript">
	SetContext('setup');
</script>
<!-- Main Wrapper -->
<div id="wrapper">
	<canvas style="background-color:black;" width="800" height="600" id="starfieldCanvas"></canvas>   
</div> <!-- END Main Wrapper -->
</body>
</html> 
