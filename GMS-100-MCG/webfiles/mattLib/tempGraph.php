<?php
/**

TODO:
    Define a way to only generate graphs as needed.
    
    if an hour graph exists and is older then 1 hour then regenerate the graph
    if it is under an hour do not regenerate the graph.
    

*/


include "/usr/local/webif/lib.php";
$img_type = "png";	// png or svg
$tmp_path = "/usr/local/webif/rrd/tmp";
$hostname = trim(file_get_contents("/etc/hostname"));
$time_now = time() - 60;
$graph_width = "400";
$graph_height = "100";
$month = date('F');
$day = date('d');
$year = date('Y');
$hour = date('H');
$min = date('i');
$sec = date('s');
$start_date = "$month $day $year $hour\:$min\:$sec";
$lazy = FALSE;
$hostname = trim(file_get_contents("/etc/hostname"));
//$tmp_path = "/var/rrd";


    $clean_folder   = '/mnt/usbflash/graphs/';	// Define the folder to clean (keep trailing slashes)
    $fileTypes      = '*-*.png';	// Filetypes to check (you can also use *.*)
    $expire_time    = 30; 						// Here you can define after how many minutes the files should get deleted
    $time_now = time();
    foreach (glob($clean_folder . $fileTypes) as $Filename)	{
			$FileCreationTime = filectime($Filename);								
			$FileAge = $time_now - $FileCreationTime; 										
			if ($FileAge > ($expire_time * 60))											
				{
                unlink($Filename);
				}
			}

if (!file_exists("/mnt/usbflash/graphs/temp-hour.png")) {
    exec("nice -19 php /usr/local/webif/rms-graph.php gen_graphs &");
}

run();

function action_gen_graphs() {
    global $timespan_view;
    
    $s = (isset($_GET["s"]) ? $_GET["s"] : "");
    $e = (isset($_GET["e"]) ? $_GET["e"] : "");
    
 //   $startDate = $s;
 //   $endDate = $e;
    
    //if(strtotime($s) != FALSE || strtotime($e) != FALSE) {
    //    echo "foobar";
        $startDate = strtotime($s); //strtotime("20200518"); //1589774400
        $endDate = strtotime($e); //strtotime("20200519"); //1589860800
   // } else {
   ///    echo "ok";
   // }
    //echo "----" . $endDate . "----";
    //echo $startDate;
    temp_graph($startDate, $endDate, "/mnt/usbflash/graphs/custom-temp.png");
	}

function run() {
    
    $s = (isset($_GET["s"]) ? $_GET["s"] : "");
    $e = (isset($_GET["e"]) ? $_GET["e"] : "");
    
    if($s != "" && $e != "") {
        action_gen_graphs();
    }
    
    $imageArray = array (
    'temp' => 
    array (
        'hour' => get_temp_hour(),
        'custom' => get_temp_custom()
        ), 
    );
        
    $dataString = json_encode($imageArray);
    print($dataString);
}

function get_temp_hour() {
    $imageData = base64_encode(file_get_contents("/mnt/usbflash/graphs/temp-hour.png"));
    return $imageData;                       
}

function get_temp_day() {
    $imageData = base64_encode(file_get_contents("/mnt/usbflash/graphs/temp-day.png"));
    return $imageData;
}

function get_temp_week() {
    $imageData = base64_encode(file_get_contents("/mnt/usbflash/graphs/temp-week.png"));
    return $imageData;
}

function get_temp_month() {
    $imageData = base64_encode(file_get_contents("/mnt/usbflash/graphs/temp-month.png"));
    return $imageData;
}

function get_temp_year() {
        $imageData = base64_encode(file_get_contents("/mnt/usbflash/graphs/temp-year.png"));
        return $imageData;
}

function get_temp_custom() {
    $imageData = base64_encode(file_get_contents("/mnt/usbflash/graphs/custom-temp.png"));
    return $imageData;                       
}

function temp_graph( $start, $end, $temp_filename )
	{
	global $hostname, $img_type, $tmp_path, $cli, $start_date, $lazy, $time_now, $graph_width, $graph_height;
		$filename = $temp_filename;
		$g_start_text = str_replace(":", '\:', date("Y-m-d H:i", $start ) );	
		$timespan = ""; 	

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
								"--font", "TITLE:9:",
								"--font", "AXIS:9:",
								"--font", "LEGEND:9:",
								"--font", "UNIT:9: ",
								"--slope",
								"-w " . $graph_width,
								"-h " . $graph_height,
								"--rigid",
								"-l -40",
								"-u 170",
								"--title=$hostname - Pod Temperature",
								"--vertical-label=Temperature",
								"COMMENT:FROM\: $g_start_text - TO\: $g_end_text" . $timespan . "\c",
								"COMMENT:\s",
								"DEF:b=$tmp_path/rms.rrd:tempf:AVERAGE",
								"LINE:b#0000ff:Fahrenheit",
								"GPRINT:b:MIN:Min\:%3.1lf%s\g",
								"GPRINT:b:MAX:Max\:%3.1lf%s\g",
								"GPRINT:b:AVERAGE:Avg\:%3.1lf%s\g",
								"GPRINT:b:LAST:Cur\:%3.1lf%s ",
								);
	if($end != "")	{	$opts[] = "--end";	$opts[] = $end;	}
	if($lazy == TRUE){	$opts[] = "--lazy";	}
	
    create_graph($opts, $filename);

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

?>