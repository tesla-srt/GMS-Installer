<?				/* ---------------- RMS-100 EXTEMP-GRAPH.PHP V0.5 (C) 2016 ETHERTEK CIRCUITS ---------------- */

//error_reporting(E_ALL);
include "lib.php";
	
//if(empty($_GET) && empty($_POST)) 
//{ 
//	/* no parameters passed*/
//	echo "This web page must be accessed through the RMS web interface!";
//	exit(0);
//}

$hostname = trim(file_get_contents("/etc/hostname"));

foreach ($_POST as $key => $input_arr)
{
  	$_POST[$key] = preg_replace("/[^a-zA-Z0-9\s!@#$%&*()_\-=+?.,:\/]/", "", $input_arr);
}

// setup_btn was clicked
if(isset ($_POST['setup_btn']))
{
	header("Location: setup_extemp.php");
}


	
//************************************
//*                                  *
//*           Change Log             *
//*                                  *
//************************************

// May 5,2013 added 5 and 10 year graphs.
// BUGFIX: added missing $time_now = time(); in function clean_temp_images.
// BUGFIX: fixed missing </a> tags.



//************************************
//*                                  *
//*         End Change Log           *
//*                                  *
//************************************


// Add a CRON Job to run this script every minute: nice -n 10 php /usr/local/webif/extemp-graph.php gen_graphs

if($argv[1] == "gen_graphs")	{ $_GET["action"] = "gen_graphs"; $cli=1;	}		// For command line ( CRON ) usage

$img_type = "png";	// png or svg
$tmp_path = "/usr/local/webif/rrd/tmp";
$lazy = TRUE;		// TRUE or FALSE - TRUE = skip regenerating the graphs when RRD doesnt think you need too. FALSE to regen graphs always (SLOW)

$month = date('F');
$day = date('d');
$year = date('Y');
$hour = date('H');
$min = date('i');
$sec = date('s');
$start_date = "$month $day $year $hour\:$min\:$sec";
$time_now = time() - 60;

if( isset( $_GET["action"] ) )
	{
	if($_GET["action"] == "gen_graphs")
		{
		action_gen_graphs();		
		}
	else if($_GET["action"] == "viewgraph")
		{
		action_viewgraph();
		}
	else if($_GET["action"] == "zoomgraph")
		{
		action_zoomgraph();
		}
	}
else // No action specified. Display graph overview.
	{
	graph_overview();
	}


exit(0);

/* ---------------------------- FUNCTIONZ ---------------------------------------------------- */

function graph_overview()
	{
		global $hostname;
		
		$dbh = new PDO('sqlite:/etc/rms100.db');
		
		// Reset Button
		if(isset ($_POST['reset_button']))
			{
				system("/etc/init.scripts/S90extemp stop > /dev/null");
				sleep(2);
				$theDate = date("M-d-Y");
				$theCommand = "mv /data/rrd/tmp/extemp.rrd /data/rrd/extemp.rrd.old-" . $theDate;
				system($theCommand);
				system("rm -f /data/rrd/extemp.rrd");
				system("rm -f /data/rrd/tmp/extemp*");
				header("Location: extemp-graph.php");
			}
		
		// Set Calibration Button
		if(isset ($_POST['cal_button']))
			{
				// Strip illegal characters from $_POST data
				$input_arr = array();
				foreach ($_POST as $key => $input_arr)
					{
  					$_POST[$key] = preg_replace("[^0-9.-]", "", $input_arr);
					}
				
				if(empty ($_POST['cal']))
				{
					//no calibration value
					$calibration = 0;
				}
				
				else
				{
					$calibration = $_POST['cal'];
				}
				
				
				$result  = $dbh->exec("UPDATE extemp SET calibration_value='" . $calibration . "'"); 
				
			}
		
		
		
		
		$result  = $dbh->query('SELECT * FROM extemp');
		foreach($result as $row)
			{
				$calibration = $row['calibration_value'];
			}
		
		$result  = $dbh->query("SELECT * FROM display_options;");			
		foreach($result as $row)
		{
			$screen_animations = $row['screen_animations'];
		}
		
		//start daemon if not running
		system("pidof extemp > /tmp/sdpid");
		if ( 0 == filesize( "/tmp/sdpid" ) )
			{
    		system("/etc/init.scripts/S90extemp start > /dev/null");
			}
		unlink("/tmp/sdpid");
		
		$timespan = "hour";	// Select which timespan used for the graph overview page: hour day week month year
		
		if($timespan == "hour")	{		$start = "-1h";	}
		else if($timespan == "day")	{		$start = "-1d";	}
		else if($timespan == "week")	{		$start = "-1w";	}
		else if($timespan == "month")	{		$start = "-1m";	}
		else if($timespan == "year")	{		$start = "-1y";	}
		else if($timespan == "5year")	{		$start = "-5y";	}
		else if($timespan == "10year")	{		$start = "-10y";	}
		
		html_header();
		start_header();
		left_nav("extemp");
		echo '<script language="javascript" type="text/javascript">';
		echo '	SetContext("extemp")';
		echo '</script>';
		
		echo "<!-- Main Wrapper -->\n";
		echo "<div id='wrapper'>\n";
		if($screen_animations == "CHECKED")
		{
			echo '<div class="content animate-panel"';
		}
		else
		{
			echo '<div class="content">';
		}

		echo "	<div class='row'>\n";
		echo "		<div class='col-sm-6'>\n";
		echo "      	<div class='hpanel4'>\n";
		echo "          <div class='panel-body' style='background:#F1F3F6;border:none; min-width:468px; max-width:469px'>\n";
		echo "						<div style='max-width:400px'>\n";
  	echo "							<h5 style='text-align:center;'>$hostname USB External Temperature Sensor Graphs - Data for the last ".$timespan.". Click on the Graph to drill down.<br></h5>\n";
		echo "						</div>\n";
		
		
		
		
		$filename = "/var/rrd/extemp-" . $timespan . ".png";
		if (file_exists($filename)) 
		{
  	  //file exists
  	  echo "<p style='text-align:left'><a href='extemp-graph.php?action=viewgraph&g=temp'><img src='/rrd/tmp/extemp-" . $timespan . ".png' title='Click for more details'></a>   \n";
		} 
		else 
		{
  	  //file does not exist
  	  echo "<p style='text-align:left'><img src='images/no-rrd-temperature.jpg' height='140' title='Data not Available'>  \n";
		}           	          	          		
		
		
		
		echo "<table width='70%' border='0'>\n";
		echo "<tr>\n";
		
		$filename = "/var/rmsdata/extempc";
		if (file_exists($filename)) 
		{
  	  //file exists
  	  $fh = fopen($filename, 'r');
  	  $tempc = fgets($fh);
  	  echo "<td><b>Temperature Celsius:</b></td><td><span id='tempc' style='color:blue'><b>" . $tempc . "</b></span></td>\n";
		} 
		else 
		{
  	  //file does not exist
  	  echo "<td><b>Temperature Celsius:</b></td><td><span style='color:red'><b>No Data Yet...</b></span></td>\n";
		}
		echo "</tr>\n";
		echo "<tr>\n";
		$filename = "/var/rmsdata/extempf";
		if (file_exists($filename)) 
		{
  	  //file exists
  	  $fh = fopen($filename, 'r');
  	  $tempf = fgets($fh);
  	  echo "<td><b>Temperature Fahrenheit:</b></td><td><span id='tempf' style='color:green'><b>" . $tempf . "</b></span></td>\n";
		} 
		else 
		{
  	  //file does not exist
  	  echo "<td><b>Temperature Fahrenheit:</b></td><td><span style='color:red'><b>No Data Yet...</b></span></td>\n";
		}
		echo "</tr>\n";
		
		echo "</table>\n";
		echo "<br>";
		echo "<form name='EXTEMP' action='extemp-graph.php' method='post' class='form-horizontal'>";
		echo '	<button name="setup_btn" class="btn btn-success" type="submit" onMouseOver="mouse_move(&#039;sd_extemp_opts&#039;);" onMouseOut="mouse_move();"><i class="fa fa-check" ></i> Options</button>';
		echo "</form>";
		
		
		echo "        	</div><!-- PANEL BODY -->\n";
		echo "      	</div><!-- END HPANEL -->\n";
		echo "    	</div><!-- END COL-SM-6 -->\n";
		echo "    </div><!-- END ROW -->\n";
		echo "  </div><!-- END ANIMATE PANEL -->\n";
		echo "</div><!-- END MAIN WRAPPER -->\n";
		echo "	<script language='javascript' type='text/javascript'>";
		echo "		function display_temps()";
		echo "		{";
		echo "				var myRandom = parseInt(Math.random()*999999999);";
		echo "			 	$.getJSON('extemp_server.php?element=temps&rand=' + myRandom,";
		echo "			 	function(data)";
		echo "			 	{";
		echo "			 		$.each (data.temp, function (k, v) { $('#' + k).text (v); });";
		echo "					setTimeout (display_temps, 1000);";
		echo "			 	}";
		echo "			);";
		echo "		}";
		echo "display_temps();";
		echo "</script>";
		
		
		
		html_footer();
	}		

function action_viewgraph()
	{
	global $time_now,$hostname;
	$dbh = new PDO('sqlite:/etc/rms100.db');
		$result  = $dbh->query("SELECT * FROM display_options;");			
		foreach($result as $row)
		{
			$screen_animations = $row['screen_animations'];
		}	
	$dbh = NULL;
	
	html_header();
	start_header();
	left_nav("extemp");
	echo "<!-- Main Wrapper -->\n";
	echo "<div id='wrapper'>\n";
	echo "	<div class='row'>\n";
	echo "		<div class='col-lg-12'>\n";
	echo "			<h3 style='text-align:center;'>$hostname USB External Temperature Sensor Graphs.<br>Click on a Graph to zoom.</h3>\n";
	echo "		</div>\n";
	echo "	</div>\n";
	if($screen_animations == "CHECKED")
		{
			echo '<div class="content animate-panel">';
		}
		else
		{
			echo '<div class="content">';
		}
	
	echo "  	<!-- FIRST PANEL ROW -->\n";
	echo "  	<div class='row top-buffer'>\n";
	echo "    	<div class='col-lg-12'>\n";
	echo "      	<div class='hpanel'>\n";
	echo "          <div class='panel-body' style='background:#F1F3F6;border:none;'>\n";
		
	
	
	// viewgraph requires g to be set...
	if(! isset( $_GET["g"] ) )	{ echo "<br>Malformed URL<br>";	exit(1);	}
	$g = $_GET["g"];
	if ($g == "temp")
		{
			temp_graph("-1h", "", "");
			echo "<p style='text-align:center'><a href='extemp-graph.php?action=zoomgraph&g=temp&t=hour&graph_start=" . ($time_now - 3600) . "&graph_end=" . $time_now . "'>";
			echo "<img src='/rrd/tmp/extemp-hour.png'></a></p>   \n";
			
			temp_graph("-1d", "", "");
			echo "<p style='text-align:center'><a href='extemp-graph.php?action=zoomgraph&g=temp&t=day&graph_start=" . ($time_now - 86400) . "&graph_end=" . $time_now . "'>";
			echo "<img src='/rrd/tmp/extemp-day.png'></a></p>\n";
			
			temp_graph("-1w", "", "");
			echo "<p style='text-align:center'><a href='extemp-graph.php?action=zoomgraph&g=temp&t=week&graph_start=" . ($time_now - 604800) . "&graph_end=" . $time_now . "'>";
			echo "<img src='/rrd/tmp/extemp-week.png'></a></p>   \n";
			
			temp_graph("-1m", "", "");
			echo "<p style='text-align:center'><a href='extemp-graph.php?action=zoomgraph&g=temp&t=month&graph_start=" . ($time_now - 2419200) . "&graph_end=" . $time_now . "'>";
			echo "<img src='/rrd/tmp/extemp-month.png'></a></p>\n";
			
			temp_graph("-1y", "", "");
			echo "<p style='text-align:center'><a href='extemp-graph.php?action=zoomgraph&g=temp&t=year&graph_start=" . ($time_now - 31536000) . "&graph_end=" . $time_now . "'>";
			echo "<img src='/rrd/tmp/extemp-year.png'></a></p>\n";
			
			temp_graph("-5y", "", "");
			echo "<p style='text-align:center'><a href='extemp-graph.php?action=zoomgraph&g=temp&t=5year&graph_start=" . ($time_now - 157680000) . "&graph_end=" . $time_now . "'>";
			echo "<img src='/rrd/tmp/extemp-5year.png'></a></p>\n";
			
			temp_graph("-10y", "", "");
			echo "<p style='text-align:center'><a href='extemp-graph.php?action=zoomgraph&g=temp&t=10year&graph_start=" . ($time_now - 315360000) . "&graph_end=" . $time_now . "'>";
			echo "<img src='/rrd/tmp/extemp-10year.png'></a></p>\n";
			
			echo "<p style='text-align:center'><a HREF='/extemp-graph.php'>Click here to go back to previous page</a></p>\n";			
		}
	else { echo "<br>Malformed URL<br>";	}
	
	echo "        	</div><!-- PANEL BODY -->\n";
	echo "      	</div><!-- END HPANEL -->\n";
	echo "    	</div><!-- END COL-LG-4 -->\n";
	echo "    </div><!-- END ROW -->\n";
	echo "  </div><!-- END ANIMATE PANEL -->\n";
	echo "</div><!-- END MAIN WRAPPER -->\n";
	
	
	html_footer();
	}
		
function action_zoomgraph()
	{
	global $tmp_path;

	$g = $_GET["g"];	// $g = vm1 vm2 temp load etc
	$t = $_GET["t"];	// $t = hour day week etc
	
	$dbh = new PDO('sqlite:/etc/rms100.db');	
	$result  = $dbh->query("SELECT * FROM display_options;");			
	foreach($result as $row)
	{
		$screen_animations = $row['screen_animations'];
	}	
	$dbh = NULL;
	
	html_header();
	start_header();
	left_nav("extemp");
	echo "<!-- Main Wrapper -->\n";
	echo "<div id='wrapper'>\n";
	echo "	<div class='row'>\n";
	echo "		<div class='col-lg-12' style='text-align:center;'>\n";
	echo "			<h3 style='text-align:center;'>Zoom Graph</h3><br><span style='text-align:center'>Drag mouse over graph to zoom</span>\n";
	echo "		</div>\n";
	echo "	</div>\n";
	if($screen_animations == "CHECKED")
		{
			echo '<div class="content animate-panel">';
		}
		else
		{
			echo '<div class="content">';
		}
	
	echo "  	<!-- FIRST PANEL ROW -->\n";
	echo "  	<div class='row top-buffer'>\n";
	echo "    	<div class='col-lg-12'>\n";
	echo "      	<div class='hpanel'>\n";
	echo "          <div class='panel-body' style='background:#F1F3F6;border:none;'>\n";
		
	echo "<div id='zoomBox' style='position:absolute; overflow:hidden; left:0px; top:0px; width:0px; height:0px; visibility:visible; background:red; filter:alpha(opacity=50); -moz-opacity:0.5; -khtml-opacity:0.5; opacity:0.5'></div>\n";
	echo "<div id='zoomSensitiveZone' style='position:absolute; overflow:hidden; left:0px; top:0px; width:0px; height:0px; visibility:visible; cursor:crosshair; background:blue; filter:alpha(opacity=0); -moz-opacity:0; -khtml-opacity:0; opacity:0' oncontextmenu='return false'></div>\n";
		
	if ($g == "temp")
		{
			$zoom_file = "temp-" . $t . "-" . genrandomstring(6) . ".png";
			temp_graph( $_GET["graph_start"], $_GET["graph_end"], $tmp_path . "/" . $zoom_file);
			echo "<p style='text-align:center'><img id='zoomGraphImage' src='/rrd/tmp/" . $zoom_file . "?action=zoomgraph&g=" . $g . "&t=" . $t;
		}

	echo "&graph_start=" . $_GET["graph_start"];
	echo "&graph_end=" . $_GET["graph_end"];
	echo "&graph_height=400&graph_width=700'>";
	echo "<br>";
	echo "<a href='/extemp-graph.php?action=viewgraph&g=" . $g . "'>Click here</a> to return";

	print_zoom_js();
	echo "        	</div><!-- PANEL BODY -->\n";
	echo "      	</div><!-- END HPANEL -->\n";
	echo "    	</div><!-- END COL-LG-4 -->\n";
	echo "    </div><!-- END ROW -->\n";
	echo "  </div><!-- END ANIMATE PANEL -->\n";
	echo "</div><!-- END MAIN WRAPPER -->\n";
	
	html_footer();
	}


function temp_graph( $start, $end, $temp_filename )
	{
	global $hostname, $img_type, $tmp_path, $cli, $start_date, $lazy, $time_now;

	if($start == "-1h")	
		{	
		$filename = "$tmp_path/extemp-hour." . $img_type . "";
		$g_start_text = str_replace(":", '\:', date("Y-m-d H:i", ($time_now -  3600) ) );
		$timespan = " - (1 Hour)";	
		}
	else if($start == "-1d")	
		{	
		$filename = "$tmp_path/extemp-day." . $img_type . ""; 
		$g_start_text = str_replace(":", '\:', date("Y-m-d H:i", ($time_now -  86400) ) );
		$timespan = " - (1 Day)";	
		}
	else if($start == "-1w")	
		{
		$filename = "$tmp_path/extemp-week." . $img_type . "";
		$g_start_text = str_replace(":", '\:', date("Y-m-d H:i", ($time_now -  604800) ) );
		$timespan = " - (1 Week)";	
		}
	else if($start == "-1m")	
		{	
		$filename = "$tmp_path/extemp-month." . $img_type . "";
		$lastmonth = mktime( date("H"),  date("i"), 0, date("m")-1, date("d"),   date("Y"));
		$g_start_text = str_replace(":", '\:', date("Y-m-d H:i", $lastmonth ) );
		$timespan = " - (1 Month)";
		}
	else if($start == "-1y")	
		{	
		$filename = "$tmp_path/extemp-year." . $img_type . "";
		$g_start_text = str_replace(":", '\:', date("Y-m-d H:i", ($time_now -  31536000)  ) );
		$timespan = " - (1 Year)";	
		}
	else if($start == "-5y")	
		{	
		$filename = "$tmp_path/extemp-5year." . $img_type . "";
		$g_start_text = str_replace(":", '\:', date("Y-m-d H:i", ($time_now -  157680000)  ) );
		$timespan = " - (5 Years)";	
		}
	else if($start == "-10y")	
		{	
		$filename = "$tmp_path/extemp-10year." . $img_type . "";
		$g_start_text = str_replace(":", '\:', date("Y-m-d H:i", ($time_now -  315360000)  ) );
		$timespan = " - (10 Years)";	
		}
	else // specific start and end dates given (zoom graph)
		{	
		//$filename = "$tmp_path/vm" . $vmnum . "." . $img_type . "";
		$filename = $temp_filename;
		$g_start_text = str_replace(":", '\:', date("Y-m-d H:i", $start ) );	
		$timespan = ""; 
		}	

	if($end == "")
		{
		$g_end_text = str_replace(":", '\:', date("Y-m-d H:i", $time_now ) );
		}
	else
		{
		$g_end_text = str_replace(":", '\:', date("Y-m-d H:i", $end) );
		}

	$opts = array("--start", $start,
								"--imgformat=" . strtoupper($img_type),
								"--font", "TITLE:10:",
								"--font", "AXIS:9:",
								"--font", "LEGEND:9:",
								"--font", "UNIT:9: ",
								"--slope",
								//"--alt-autoscale",
								"--rigid",
								"-l -40",
								"-u 120",
								"-w 600",
								"-h 300",
								"--title=USB External Temperature Sensor",
								"--vertical-label=Temperature",
								"COMMENT:FROM\: $g_start_text - TO\: $g_end_text" . $timespan . "\c",
								"COMMENT:\s",
								"DEF:a=$tmp_path/extemp.rrd:tempc:AVERAGE",
								"DEF:b=$tmp_path/extemp.rrd:tempf:AVERAGE",
								"LINE:b#00ff00:Fahrenheit",
								"GPRINT:b:MIN:Min\:%3.1lf\g",
								"GPRINT:b:MAX:Max\:%3.1lf\g",
								"GPRINT:b:AVERAGE:Ave\:%3.1lf\g",
								"GPRINT:b:LAST:Cur\:%3.1lf\j",
								"LINE2:a#0000ff:Celsius   ",
								"GPRINT:a:MIN:Min\:%3.1lf\g",
								"GPRINT:a:MAX:Max\:%3.1lf\g",
								"GPRINT:a:AVERAGE:Ave\:%3.1lf\g",
								"GPRINT:a:LAST:Cur\:%3.1lf ",
								);
	if($end != "")	{	$opts[] = "--end";	$opts[] = $end;	}
	if($lazy == TRUE){	$opts[] = "--lazy";	}
	
	if($cli > 0)
		{
		$random_file = $tmp_path . "/" . genRandomString(10);
		create_graph($opts, $random_file);
		rename ($random_file, $filename);
		}
	else
		{
		create_graph($opts, $filename);
		}

	} 

function create_graph ( $opts, $filename )
	{
	$ret = rrd_graph($filename, $opts, count($opts));
	if( !is_array($ret) )
		{
		$err = rrd_error();
		echo "rrd_graph() ERROR: $err<br>";
		}	
	}

function html_header()
	{
	global $starttime, $cli;
	if($cli > 0)	{ return;	} 
	$mtime = microtime();
	$mtime = explode(" ",$mtime);
	$mtime = $mtime[1] + $mtime[0];
	$starttime = $mtime;
	echo "<!DOCTYPE html>\n";
		echo "<html>\n";
		echo "	<head>\n";
    echo "		<meta charset='utf-8'>\n";
    echo "		<meta name='viewport' content='width=device-width, initial-scale=1.0'>\n";
    echo "		<meta http-equiv='X-UA-Compatible' content='IE=edge'>\n"; 
    echo "		<meta http-equiv='refresh' content='60'>\n";		
    echo "		<!-- Page title -->\n";
    echo "		<title>USB External Temperature Sensor</title>\n";
    $myrand = rand();
    echo "		<link rel='shortcut icon' type='image/ico' href='rms100favocon.ico?".$myrand."' />\n"; 		
    echo "		<!-- CSS -->\n";
    echo "		<link rel='stylesheet' href='css/fontawesome/css/font-awesome.css' />\n";
    echo "		<link rel='stylesheet' href='css/animate.css' />\n";
    echo "		<link rel='stylesheet' href='css/bootstrap.css' />\n";
    echo "		<link rel='stylesheet' href='css/ethertek.css'>\n";		
    echo "		<!-- Java Scripts -->\n";
		echo "		<script src='javascript/jquery.min.js'></script>\n";
		echo "		<script src='javascript/bootstrap.min.js'></script>\n";
		echo "		<script src='javascript/conhelp.js'></script>\n";
		echo "		<script src='javascript/ethertek.js'></script>\n";
		echo "	<script language='javascript' type='text/javascript'>";
		echo "		SetContext('extemp');";
		echo "	</script>";
		echo "</head>\n";
		echo "<body class='fixed-navbar fixed-sidebar'>\n";
		
		echo "<div class='splash'><div class='splash-title'><h1>Generating Graphs... Please Wait...</h1><div class='spinner'> <div class='rect1'></div> <div class='rect2'></div> <div class='rect3'></div> <div class='rect4'></div> <div class='rect5'></div> </div> </div> </div>\n";

	}                                                   

function html_footer()
	{
	global $starttime, $cli;
	if($cli > 0)	{ return;	}
	$mtime = microtime();
	$mtime = explode(" ",$mtime);
	$mtime = $mtime[1] + $mtime[0];
	$endtime = $mtime;
	$totaltime = ($endtime - $starttime);
	printf ("<p style='text-align:center'>This page was generated in %2.4f seconds</p>\n", $totaltime);
	echo "</body>\n";
	echo "</html>\n";
	}                                                  

function genRandomString($length)
	{
	$characters = "0123456789ABCDEFGHIJKLMNOPQRSTUVWZYZabcdefghijklmnopqrstuvwxyz";
	$real_string_legnth = strlen($characters) - 1;
	$string="ID";
	for ($p = 0; $p < $length; $p++)
		{
		$string .= $characters[mt_rand(0, $real_string_legnth)];
		}
	return $string;
	}

function action_gen_graphs()
	{
	// Called from CRON .. Rebuild  1h graphs
	$start = "-1h";
	temp_graph($start, "", "");
	clean_temp_images();
	}

function clean_temp_images()
	{
		$clean_folder   = '/var/rrd/';	// Define the folder to clean (keep trailing slashes)
		$fileTypes      = '*-*-*.png';	// Filetypes to check (you can also use *.*)
		$expire_time    = 5; 						// Here you can define after how many minutes the files should get deleted
		$time_now = time();
		foreach (glob($clean_folder . $fileTypes) as $Filename)		// Find all files of the given file type
			{
			$FileCreationTime = filectime($Filename);									// Read file creation time
			$FileAge = $time_now - $FileCreationTime; 										// Calculate file age in seconds
			//print "Flie Name:" . $Filename . " - File Age: " . $FileAge .  " - File Creation Time: " . $FileCreationTime . "\n";
			if ($FileAge > ($expire_time * 60))												// Is the file older than the given time span?
				{
					//print "The file $Filename is older than $expire_time minutes\n";
					unlink($Filename);
				}
			}
	}


function print_zoom_js()
	{

//	<STYLE MEDIA="print">
//	/*Turn off the zoomBox*/
//	div#zoomBox, div#zoomSensitiveZone {display: none}
//	/*This keeps IE from cutting things off*/
//	#why {position: static; width: auto}
//	</STYLE>

?>
	<script type="text/javascript">

<!--
/*
+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
+ Bonsai: A more user friendly zoom function for Cacti                        +
+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
+ Copyright (C) 2004  Eric Steffen                                            +
+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
+ This program is free software; you can redistribute it and/or               +
+ modify it under the terms of the GNU General Public License                 +
+ as published by the Free Software Foundation; either version 2              +
+ of the License, or (at your option) any later version.                      +
+                                                                             +
+ This program is distributed in the hope that it will be useful,             +
+ but WITHOUT ANY WARRANTY; without even the implied warranty of              +
+ MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the               +
+ GNU General Public License for more details.                                +
+                                                                             +
+ You should have received a copy of the GNU General Public License           +
+ along with this program; if not, write to the Free Software                 +
+ Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA. +
+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
+ email : eric.steffen@gmx.net                                                +
+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

 zoom.js version 0.4
*/
/*+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/

// Global constant
var cURLBase = "extemp-graph.php?action=zoomgraph";

// Global variables
var gZoomGraphName = "zoomGraphImage";
var gZoomGraphObj;
var gMouseObj;
var gUrlObj;
var gBrowserObj;

// Objects declaration

/*+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
/*++++++++++++++++++++++++++++++++  urlObj  +++++++++++++++++++++++++++++++++*/
/*+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/

function urlObj(url) {
	var urlBaseAndParameters;

	urlBaseAndParameters = url.split("?");
	this.urlBase = urlBaseAndParameters[0];
	this.urlParameters = urlBaseAndParameters[1].split("&");

	this.getUrlBase = urlObjGetUrlBase;
	this.getUrlParameterValue = urlObjGetUrlParameterValue;
}

/*++++++++++++++++++++++++  urlObjGetUrlBase  +++++++++++++++++++++++++++++++*/

function urlObjGetUrlBase() {
	return this.urlBase;
}

/*++++++++++++++++++++  urlObjGetUrlParameterValue  +++++++++++++++++++++++++*/

function urlObjGetUrlParameterValue(parameter) {
	var i;
	var fieldAndValue;
	var value;

	i = 0;
	while (this.urlParameters [i] != undefined) {
		fieldAndValue = this.urlParameters[i].split("=");
		if (fieldAndValue[0] == parameter) {
			value = fieldAndValue[1];
		}
		i++;
	}

	return value;
}



/*+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
/*+++++++++++++++++++++++++++++++  mouseObj  ++++++++++++++++++++++++++++++++*/
/*+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/

function mouseObj() {
	this.startedX = 0;
	this.startedY = 0;

	this.stoppedX = 0;
	this.stoppedY = 0;

	this.currentX = 0;
	this.currentY = 0;

	this.dragging = false;

	this.setEvent = mouseObjSetEvent;
	this.leftButtonPressed = mouseObjLeftButtonPressed;
	this.rightButtonPressed = mouseObjRightButtonPressed;
	this.getCurrentPosition = mouseObjGetCurrentPosition;
	this.saveCurrentToStartPosition = mouseObjSaveCurrentToStartPosition;
	this.saveCurrentToStopPosition = mouseObjSaveCurrentToStopPosition;
}

/*++++++++++++++++++++++++  mouseObjSetEvent  +++++++++++++++++++++++++++++++*/

function mouseObjSetEvent(theEvent) {
	if (gBrowserObj.browser == "Netscape") {
		this.event = theEvent;
	} else {
		this.event = window.event;
	}
}

/*++++++++++++++++++++++++  mouseObjLeftMouseButton  +++++++++++++++++++++++++++++++*/

function mouseObjLeftButtonPressed() {
	var LeftButtonPressed = false;
	// alert ("Button Pressed");
	if (gBrowserObj.browser == "IE") {
		LeftButtonPressed = (this.event.button < 2);
		// alert ("Net");
	} else {
		LeftButtonPressed = (this.event.which  < 2);
	}

	return LeftButtonPressed;
}

/*++++++++++++++++++++++++  mouseObjRightMouseButton  +++++++++++++++++++++++++++++++*/

function mouseObjRightButtonPressed() {
	var RightButtonPressed = false;
	//alert ("Button Pressed");
	if (gBrowserObj.browser == "IE") {
		if ((this.event.button >= 2) && (this.event.button != 4)) {
			RightButtonPressed = true;
		}
		// alert ("Net");
	} else {
		if (this.event.which > 2) {
			RightButtonPressed = true;
		}
	}

	return RightButtonPressed;
}	

/*+++++++++++++++++++  mouseObjGetCurrentPosition  ++++++++++++++++++++++++++*/

function mouseObjGetCurrentPosition() {
	this.currentX = this.event.clientX + document.body.scrollLeft;
	this.currentY = this.event.clientY + document.body.scrollTop;
}

/*+++++++++++++++++  mouseObjSaveCurrentToStartPosition  ++++++++++++++++++++*/

function mouseObjSaveCurrentToStartPosition() {
	this.startedX = this.currentX;
	this.startedY = this.currentY;
}

/*++++++++++++++++++  mouseObjSaveCurrentToStopPosition  ++++++++++++++++++++*/

function mouseObjSaveCurrentToStopPosition() {
	this.stoppedX = this.currentX;
	this.stoppedY = this.currentY;
}

/*+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
/*+++++++++++++++++++++++++++++  zoomGraphObj  ++++++++++++++++++++++++++++++*/
/*+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/

function zoomGraphObj(zoomGraphName) {
	// We use 3 zones. The first (zoomGraph) represent the entire graph image.
	// The second (zoomBox) represent the graph itself.
	// The last zone (zoomSensitiveZone) represent the area where the user can
	// launch the zoom function

	this.zoomGraphName = zoomGraphName;
	this.imgObject     = document.getElementById(this.zoomGraphName);
	gUrlObj            = new urlObj(this.imgObject.src);

	this.zoomGraphLeft   = 0;
	this.zoomGraphTop    = 0;
	this.zoomGraphRight  = 0;
	this.zoomGraphBottom = 0;
	this.zoomGraphWidth  = 0;
	this.zoomGraphHeight = 0;

	this.zoomBoxLeft   = 0;
	this.zoomBoxTop    = 0;
	this.zoomBoxRight  = 0;
	this.zoomBoxBottom = 0;
	this.zoomBoxWidth  = 0;
	this.zoomBoxHeight = 0;

	this.zoomSensitiveZoneLeft   = 0;
	this.zoomSensitiveZoneTop    = 0;
	this.zoomSensitiveZoneRight  = 0;
	this.zoomSensitiveZoneBottom = 0;
	this.zoomSensitiveZoneWith   = 0;
	this.zoomSensitiveZoneHeight = 0;

	this.refresh = zoomGraphObjRefresh;
	this.drawSelection = zoomGraphObjDrawSelection;

	this.refresh();
}

/*+++++++++++++++++++++++++++  zoomGraphObjRefresh  +++++++++++++++++++++++++*/

function zoomGraphObjRefresh() {
	//  constants
	var cZoomBoxName = "zoomBox";

	var titleFontSize = parseInt(gUrlObj.getUrlParameterValue("title_font_size"));

	if (titleFontSize == 0) {
		var cZoomBoxTopOffsetWOText = 15 - 1;
		var cZoomBoxTopOffsetWText  = 32 - 1;
		var cZoomBoxRightOffset     = -16;
	} else {
		var cZoomBoxTopOffsetWOText = 10 - 1;
		var cZoomBoxTopOffsetWText  = titleFontSize + (titleFontSize * 1.6) + 10 - 1;
		var cZoomBoxRightOffset     = -240;
	}

	// zone outside of Zoom box where user can move cursor to without causing odd behavior
	var cZoomSensitiveZoneName   = "zoomSensitiveZone";
	var cZoomSensitiveZoneOffset = 30;

	// variables
	var imgObject;
	// var imgSource;
	var imgAlt;

	var divObject;

	var left;
	var top;
	var width;
	var height;

	// zoomable selection area Width and Height
	var zoomBoxWidth;
	var zoomBoxHeight;

	imgObject = this.imgObject;
	//imgSource = imgObject.src;
	imgAlt = imgObject.alt;

	// determine the overall graph size
	width  = 711; //imgObject.width;
	height = 423; //imgObject.height;

	// get the graph area size from the url
	zoomBoxWidth  = parseInt(gUrlObj.getUrlParameterValue("graph_width")) + 1;
	zoomBoxHeight = parseInt(gUrlObj.getUrlParameterValue("graph_height")) + 1;

	// Get absolute image position relative to the overall window.
	//
	// start with the image's coordinates and walk through it's
	// ancestory of elements (tables, div's, spans, etc...) until
	// we're at the top of the display.  Along the way we add in each element's
	// coordinates to get absolute image postion.
	left = 0;
	top  = 0;
	do {
		left += imgObject.offsetLeft;
		top  += imgObject.offsetTop;
		imgObject  = imgObject.offsetParent;
	} while(imgObject);
	
	
	
	// set the images's Ix1,Iy1 and Ix2,Iy2 postions based upon results
	this.zoomGraphLeft   = left;
	this.zoomGraphTop    = top - 100;
	this.zoomGraphRight  = left + width;
	this.zoomGraphBottom = top  + height;
	this.zoomGraphWidth  = width;
	this.zoomGraphHeight = height;

	// calculate the right hand coordinate (rrdGAx2) of the zoom box (aka rrd Graph area)
	this.zoomBoxRight = this.zoomGraphRight + cZoomBoxRightOffset;

	// calculate the top coordinate (rrdGAy2) of the zoom box (aka rrd Graph area)
	if(imgAlt == "") {
		this.zoomBoxTop = this.zoomGraphTop + cZoomBoxTopOffsetWOText;
	} else {
		this.zoomBoxTop = this.zoomGraphTop + cZoomBoxTopOffsetWText;
	}

	// calculate the left hand coordinate (rrdGAx1) of the zoom box (aka rrd Graph area)
	this.zoomBoxLeft = this.zoomBoxRight - zoomBoxWidth;

	// calculate the bottom coordinate (rrdGAy1) of the zoom box (aka rrd Graph area)
	this.zoomBoxBottom = this.zoomBoxTop + zoomBoxHeight;

	// set the objects zoom sizes from the url values (aka rrd Graph size)
	this.zoomBoxWidth  = zoomBoxWidth;
	this.zoomBoxHeight = zoomBoxHeight;

	// this.drawSelection(this.zoomBoxLeft, this.zoomBoxTop, this.zoomBoxRight, this.zoomBoxBottom);
	this.drawSelection(0, 0, 0, 0); // reset selection

	divObject              = document.getElementById(cZoomBoxName);
	divObject.style.left   = this.zoomBoxLeft+'px';
	divObject.style.top    = this.zoomBoxTop+'px';
	divObject.style.width  = this.zoomBoxWidth+'px';
	divObject.style.height = this.zoomBoxHeight+'px';

	// allow the crosshair to extend outside of the Graph area without graphical glitches
	this.zoomSensitiveZoneLeft   = this.zoomBoxLeft - cZoomSensitiveZoneOffset;
	this.zoomSensitiveZoneTop    = this.zoomBoxTop - cZoomSensitiveZoneOffset;
	this.zoomSensitiveZoneRight  = this.zoomBoxRight + cZoomSensitiveZoneOffset;
	this.zoomSensitiveZoneBottom = this.zoomBoxBottom + cZoomSensitiveZoneOffset;
	this.zoomSensitiveZoneWidth  = this.zoomSensitiveZoneRight - this.zoomSensitiveZoneLeft;
	this.zoomSensitiveZoneHeight = this.zoomSensitiveZoneBottom - this.zoomSensitiveZoneTop;

	divObject              = document.getElementById(cZoomSensitiveZoneName);
	divObject.style.left   = this.zoomSensitiveZoneLeft+'px';
	divObject.style.top    = this.zoomSensitiveZoneTop+'px';
	divObject.style.width  = this.zoomSensitiveZoneWidth+'px';
	divObject.style.height = this.zoomSensitiveZoneHeight+'px';
}

/*++++++++++++++++++++++  zoomGraphObjDrawSelection  ++++++++++++++++++++++++*/

function zoomGraphObjDrawSelection (x1, y1, x2, y2) {
	var cZoomBoxName = "zoomBox";
	var divObject;

	x1 = x1 - this.zoomBoxLeft;
	x2 = x2 - this.zoomBoxLeft;
	y1 = y1 - this.zoomBoxTop;
	y2 = y2 - this.zoomBoxTop;

	var minX = Math.min(x1, x2);
	var maxX = Math.max(x1, x2) + 1;
	var minY = Math.min(y1, y2);
	var maxY = Math.max(y1, y2) + 1;

	divObject = document.getElementById(cZoomBoxName);
	divObject.style.clip ="rect(" + minY + "px " + maxX + "px " + maxY + "px " + minX + "px)";
}

/*+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
/*++++++++++++++++++++  standard functions definition  ++++++++++++++++++++++*/
/*+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/

/*
BrowserDetector()
Parses User-Agent string into useful info.

Source: Webmonkey Code Library
(http://www.hotwired.com/webmonkey/javascript/code_library/)

Author: Richard Blaylock
Author Email: blaylock@wired.com

Usage: var bd = new BrowserDetector(navigator.userAgent);
*/

// utility function to trim spaces from both ends of a string
function Trim(inString) {
	var retVal = "";
	var start = 0;
	while ((start < inString.length) && (inString.charAt(start) == ' ')) {
		++start;
	}

	var end = inString.length;

	while ((end > 0) && (inString.charAt(end - 1) == ' ')) {
		--end;
	}

	retVal = inString.substring(start, end);

	return retVal;
}

function BrowserDetector(ua) {
	// defaults
	this.browser = "Unknown";
	this.platform = "Unknown";
	this.version = "";
	this.majorver = "";
	this.minorver = "";

	uaLen = ua.length;

	// ##### split into stuff before parens and stuff in parens
	var preparens = "";
	var parenthesized = "";

	i = ua.indexOf("(");

	if (i >= 0) {
		preparens = Trim(ua.substring(0,i));
		parenthesized = ua.substring(i+1, uaLen);
		j = parenthesized.indexOf(")");
		if (j >= 0) {
			parenthesized = parenthesized.substring(0, j);
		}
	} else {
		preparens = ua;
	}

	// ##### first assume browser and version are in preparens
	// ##### override later if we find them in the parenthesized stuff
	var browVer = preparens;

	var tokens = parenthesized.split(";");
	var token = "";

	// # Now go through parenthesized tokens
	for (var i=0; i < tokens.length; i++) {
		token = Trim(tokens[i]);
		//## compatible - might want to reset from Netscape
		if (token == "compatible") {
			//## One might want to reset browVer to a null string
			//## here, but instead, we'll assume that if we don't
			//## find out otherwise, then it really is Mozilla
			//## (or whatever showed up before the parens).
			//## browser - try for Opera or IE
		} else if (token.indexOf("MSIE") >= 0) {
			browVer = token;
		} else if (token.indexOf("Opera") >= 0) {
			browVer = token;
		} else if ((token.indexOf("X11") >= 0) || (token.indexOf("SunOS") >= 0) || (token.indexOf("Linux") >= 0)) {
			//'## platform - try for X11, SunOS, Win, Mac, PPC
			this.platform = "Unix";
		} else if (token.indexOf("Win") >= 0) {
			this.platform = token;
		} else if ((token.indexOf("Mac") >= 0) || (token.indexOf("PPC") >= 0)) {
			this.platform = token;
		}
	}

	var msieIndex = browVer.indexOf("MSIE");
	if (msieIndex >= 0) {
		browVer = browVer.substring(msieIndex, browVer.length);
	}

	var leftover = "";
	if (browVer.substring(0, "Mozilla".length) == "Mozilla") {
		this.browser = "Netscape";
		leftover = browVer.substring("Mozilla".length+1, browVer.length);
	} else if (browVer.substring(0, "Lynx".length) == "Lynx") {
		this.browser = "Lynx";
		leftover = browVer.substring("Lynx".length+1, browVer.length);
	} else if (browVer.substring(0, "MSIE".length) == "MSIE") {
		this.browser = "IE";
		leftover = browVer.substring("MSIE".length+1, browVer.length);
	} else if (browVer.substring(0, "Microsoft Internet Explorer".length) == "Microsoft Internet Explorer") {
		this.browser = "IE"
		leftover = browVer.substring("Microsoft Internet Explorer".length+1, browVer.length);
	} else if (browVer.substring(0, "Opera".length) == "Opera") {
		this.browser = "Opera"
		leftover = browVer.substring("Opera".length+1, browVer.length);
	}

	leftover = Trim(leftover);

	// # try to get version info out of leftover stuff
	i = leftover.indexOf(" ");
	if (i >= 0) {
		this.version = leftover.substring(0, i);
	} else {
		this.version = leftover;
	}

	j = this.version.indexOf(".");
	if (j >= 0) {
		this.majorver = this.version.substring(0,j);
		this.minorver = this.version.substring(j+1, this.version.length);
	} else {
		this.majorver = this.version;
	}
} // function BrowserCap


/*++++++++++++++++++++++++++  initBonsai  ++++++++++++++++++++++++++*/

function initBonsai() {
	gBrowserObj   = new BrowserDetector(navigator.userAgent);
	//alert("Browser: " + gBrowserObj.browser + "\nPlatform: " + gBrowserObj.platform + "\nVersion: " + gBrowserObj.version + "\nMajorVer: " + gBrowserObj.majorver + "\nMinorVer: " + gBrowserObj.minorver);

	// gUrlObj = new urlObj(document.URL);
	gZoomGraphObj = new zoomGraphObj(gZoomGraphName);
	gMouseObj     = new mouseObj();
	initEvents();
}

/*+++++++++++++++++++++++++++  insideZoomBox  +++++++++++++++++++++++++++++++*/

function insideZoomBox() {
	var szLeft   = gZoomGraphObj.zoomSensitiveZoneLeft;
	var szTop    = gZoomGraphObj.zoomSensitiveZoneTop;
	var szRight  = gZoomGraphObj.zoomSensitiveZoneRight;
	var szBottom = gZoomGraphObj.zoomSensitiveZoneBottom;

	var mpX = gMouseObj.currentX;
	var mpY = gMouseObj.currentY;

	return ((mpX >= szLeft) && (mpX <= szRight) && (mpY >= szTop) && (mpY <= szBottom));
}

/*++++++++++++++++++++++++++++  initEvents  +++++++++++++++++++++++++++++++++*/

function initEvents() {
	document.onmousemove = onMouseMouveEvent;
	document.onmousedown = onMouseDownEvent;
	document.onmouseup = onMouseUpEvent;
	window.onresize = windowOnResizeEvent;

	if (gBrowserObj.browser == "Netscape") {
		document.captureEvents(Event.MOUSEMOVE);
		document.captureEvents(Event.MOUSEDOWN);
		document.captureEvents(Event.MOUSEUP);
	}
}

/*+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
/*+++++++++++++++++++++  events functions definition  +++++++++++++++++++++++*/
/*+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/

/*+++++++++++++++++++++++++++  onMouseDownEvent  ++++++++++++++++++++++++++++*/

function onMouseDownEvent(e) {
	gMouseObj.setEvent(e);
	gMouseObj.getCurrentPosition();

	if (insideZoomBox()) {
		if ((gMouseObj.leftButtonPressed()) && (!gMouseObj.dragging)) {
			gMouseObj.dragging = true;
			gMouseObj.saveCurrentToStartPosition();
			gZoomGraphObj.drawSelection(gMouseObj.currentX, gMouseObj.currentY, gMouseObj.currentX, gMouseObj.currentY);
		} else if (gMouseObj.rightButtonPressed()) {
			var test = true;
		}
	}
}

/*+++++++++++++++++++++++++++  onMouseMouveEvent  +++++++++++++++++++++++++++*/

function onMouseMouveEvent(e) {
	gMouseObj.setEvent(e);
	if (gMouseObj.dragging) {
		gMouseObj.getCurrentPosition();
		gZoomGraphObj.drawSelection(gMouseObj.startedX, gMouseObj.startedY, gMouseObj.currentX, gMouseObj.currentY);
	}
}

/*+++++++++++++++++++++++++++++  onMouseUpEvent  ++++++++++++++++++++++++++++*/

function onMouseUpEvent(e) {
	var graphStart;
	var graphEnd;

	var newGraphStart;
	var newGraphEnd;

	gMouseObj.setEvent(e);

	graphStart = parseInt(gUrlObj.getUrlParameterValue("graph_start"));
	graphEnd = parseInt(gUrlObj.getUrlParameterValue("graph_end"));

	// zoom out action
	if ((gMouseObj.rightButtonPressed()) && (insideZoomBox())) {
		var Timespan = graphEnd - graphStart;

		gMouseObj.dragging = false;
		newGraphEnd        = graphEnd   + Timespan * 2;
		newGraphStart      = graphStart - Timespan * 2;

		var urlBase       = cURLBase;
		//B var localGraphId  = gUrlObj.getUrlParameterValue("local_graph_id");
		//var rraId         = gUrlObj.getUrlParameterValue("rra_id");
		var graphWidth    = gUrlObj.getUrlParameterValue("graph_width");
		var graphHeight   = gUrlObj.getUrlParameterValue("graph_height");
		//var viewType      = gUrlObj.getUrlParameterValue("view_type");
		var titleFontSize = gUrlObj.getUrlParameterValue("title_font_size");
		
var g = gUrlObj.getUrlParameterValue("g");
var t = gUrlObj.getUrlParameterValue("t");

		//B open(urlBase + "&local_graph_id=" + localGraphId + "&rra_id=" + rraId + "&view_type=" + viewType + "&graph_start=" + newGraphStart + "&graph_end=" + newGraphEnd + "&graph_height=" + graphHeight + "&graph_width=" + graphWidth + "&title_font_size=" + titleFontSize, "_self");
		open(urlBase + "&g=" + g + "&t=" + t + "&graph_start=" + newGraphStart + "&graph_end=" + newGraphEnd + "&graph_height=" + graphHeight + "&graph_width=" + graphWidth, "_self");
	}

	// zoom in action
	if ((gMouseObj.leftButtonPressed()) && (gMouseObj.dragging)) {
		gMouseObj.getCurrentPosition();
		gMouseObj.saveCurrentToStopPosition();
		gMouseObj.dragging = false;

		// check for appropriate selection zone
		if (((gMouseObj.startedX < gZoomGraphObj.zoomBoxLeft)   && (gMouseObj.stoppedX < gZoomGraphObj.zoomBoxLeft)) ||
			((gMouseObj.startedX > gZoomGraphObj.zoomBoxRight)  && (gMouseObj.stoppedX > gZoomGraphObj.zoomBoxRight)) ||
			((gMouseObj.startedY > gZoomGraphObj.zoomBoxBottom) && (gMouseObj.stoppedY > gZoomGraphObj.zoomBoxBottom)) ||
			((gMouseObj.startedY < gZoomGraphObj.zoomBoxTop)    && (gMouseObj.stoppedY < gZoomGraphObj.zoomBoxTop))) {
			// alert("Selection Outside of Allowed Area");
		}else {
			var x1 = gMouseObj.startedX - gZoomGraphObj.zoomBoxLeft;
			var x2 = gMouseObj.stoppedX - gZoomGraphObj.zoomBoxLeft;

			var y1 = gMouseObj.startedY - gZoomGraphObj.zoomBoxTop;
			var y2 = gMouseObj.stoppedY - gZoomGraphObj.zoomBoxTop;

			var minX = Math.min(x1, x2);
			var maxX = Math.max(x1, x2);
			var minY = Math.min(y1, y2);
			var maxY = Math.max(y1, y2);

			if (minX < 0) {
				minX = 0;
			}

			if (maxX > gZoomGraphObj.zoomBoxWidth) {
				maxX = gZoomGraphObj.zoomBoxWidth;
			}

			if (minY < 0) {
				minY = 0;
			}

			if (maxY > gZoomGraphObj.zoomBoxHeight) {
				maxY = gZoomGraphObj.zoomBoxHeight;
			}

			if ((minX != maxX) || (minY != maxY)) {
				var OnePixel = (graphEnd - graphStart) / gZoomGraphObj.zoomBoxWidth;  // Represent # of seconds for 1 pixel on the graph

				newGraphEnd = Math.round(graphEnd - (gZoomGraphObj.zoomBoxWidth - maxX) * OnePixel);
				newGraphStart = Math.round(graphStart + minX * OnePixel);

				//  var urlBase = gUrlObj.getUrlBase();
				var urlBase       = cURLBase;
				//B var localGraphId  = gUrlObj.getUrlParameterValue("local_graph_id");
				//B var rraId         = gUrlObj.getUrlParameterValue("rra_id");
				var graphWidth    = gUrlObj.getUrlParameterValue("graph_width");
				var graphHeight   = gUrlObj.getUrlParameterValue("graph_height");
				//B var viewType      = gUrlObj.getUrlParameterValue("view_type");
				//B var titleFontSize = gUrlObj.getUrlParameterValue("title_font_size");

var g = gUrlObj.getUrlParameterValue("g");
var t = gUrlObj.getUrlParameterValue("t");

				open(urlBase + "&g=" + g + "&t=" + t + "&graph_start=" + newGraphStart + "&graph_end=" + newGraphEnd + "&graph_height=" + graphHeight + "&graph_width=" + graphWidth, "_self");
			}
		}
	}
}

/*+++++++++++++++++++++++++++  windowOnResizeEvent  +++++++++++++++++++++++++*/

function windowOnResizeEvent() {
	gZoomGraphObj.refresh();
}

/*+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
/*++++++++++++++++++++++++++++++  main script  ++++++++++++++++++++++++++++++*/
/*+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/

window.onload = initBonsai;

// end of script
//-->
</script>
<?

	
		
	}

	

	                                             
?>



