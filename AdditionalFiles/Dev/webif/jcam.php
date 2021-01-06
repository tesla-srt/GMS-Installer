<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>RMS-100 J-Cam</title>
<script language="javascript" type="text/javascript" src="javascript/functions.js"></script>
<script language="javascript">

      function send_command(cmd) {
        document.getElementById('hints').firstChild.nodeValue = "Send command: " + cmd;
        AJAX_get('/?action=command&command='+ cmd)
      }

      function AJAX_response(text) {
      if(text < 0)
      	{
      		msg="Command Failed";
      	}
      else
      	{
      		msg="Command OK"
      	}
        document.getElementById('hints').firstChild.nodeValue = "Got response: " + msg;
      }

      function KeyDown(ev) {
        ev = ev || window.event;
        pressed = ev.which || ev.keyCode;

        switch (pressed) {
          case 37:
              send_command('pan_plus');
            break;
          case 39:
              send_command('pan_minus');
            break;
          case 38:
              send_command('tilt_minus');
            break;
          case 40:
              send_command('tilt_plus');
            break;
          case 32:
              send_command('reset_pan_tilt');
          break;
          default:
              break;
        }
      }

      document.onkeydown = KeyDown;

    </script>
</head>
<script type="text/javascript">

/* Copyright (C) 2007 Richard Atterer, richardÂ©atterer.net
   This program is free software; you can redistribute it and/or modify it
   under the terms of the GNU General Public License, version 2. See the file
   COPYING for details. */

var imageNr = 0; // Serial number of current image
      var finished = new Array(); // References to img objects which have finished downloading
      var paused = false;
      var previous_time = new Date();

      function createImageLayer() {
        var img = new Image();
        img.style.position = "absolute";
        img.style.zIndex = -1;
        img.onload = imageOnload;
        img.onclick = imageOnclick;
        img.width = 512;
        img.height = 384;
        img.src = "/?action=snapshot&n=" + (++imageNr);
        var webcam = document.getElementById("webcam");
        webcam.insertBefore(img, webcam.firstChild);
      }

// Two layers are always present (except at the very beginning), to avoid flicker
      function imageOnload() {
        this.style.zIndex = imageNr; // Image finished, bring to front!
        while (1 < finished.length) {
          var del = finished.shift(); // Delete old image(s) from document
          del.parentNode.removeChild(del);
        }
        finished.push(this);
        current_time = new Date();
        delta = current_time.getTime() - previous_time.getTime();
        fps   = (1000.0 / delta).toFixed(3);
        document.getElementById('info').firstChild.nodeValue = delta + " ms (" + fps + " fps)";
        previous_time = current_time;
        createImageLayer();
      }

function imageOnclick() { // Clicking on the image will pause the stream
  paused = !paused;
  if (!paused) createImageLayer();
}

</script>
<body onload="createImageLayer();">

<div id="webcam" style="width:512px;height:394px"><noscript><img src="/?action=snapshot" width="512px" height="384px" /></noscript></div>

<p> 
<form name="command_panel" action="" onsubmit="return false;">
 <input type="button" value="Reset Camera" onclick="send_command('reset')">
 <input type="button" value="Brightness +" onclick="send_command('brightness_plus')">
 <input type="button" value="Brightness -" onclick="send_command('brightness_minus')">
 <input type="button" value="Contrast +" onclick="send_command('contrast_plus')">
 <input type="button" value="Contrast -" onclick="send_command('contrast_minus')">
</form>
</p>
<br>
    <div id="hints" style="background-color: white;">Camera Messages</div>

 Runtime info:<p id="info">-</p>


</body>
</html>
