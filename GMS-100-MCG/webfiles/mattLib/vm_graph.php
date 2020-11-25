<?php
include "/usr/local/webif/lib.php";
$graph_path = "/mnt/usbflash/graphs";
$graph_path = "/data/custom/html/mattLib/graphs";

$img_type = "png";	// png or svg
$tmp_path = "/usr/local/webif/rrd/tmp";
$lazy = FALSE;		// TRUE or FALSE - TRUE = skip regenerating the graphs when RRD doesnt think you need too. FALSE to regen graphs always (SLOW)


$graph_width = "400";
$graph_height = "100";

$hostname = trim(file_get_contents("/etc/hostname"));

$month = date('F');
$day = date('d');
$year = date('Y');
$hour = date('H');
$min = date('i');
$sec = date('s');
$start_date = "$month $day $year $hour\:$min\:$sec";
$time_now = time() - 60;

$vm_info = array( "name" => array());
$vm_info['name'][] = "bump";

$dbh = new PDO('sqlite:/etc/rms100.db');
$result  = $dbh->query('SELECT * FROM voltmeters');
foreach($result as $row)
	{
	$vm_info['name'][] = $row['name'];
	}


if($argv[1] == "gen_graphs") {
    $lazy = FALSE;
    gen_graphs();
}


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

if (!file_exists("/mnt/usbflash/graphs/vm1-hour.png")) {
    exec("nice -19 php /data/custom/html/mattLib/vm_graph.php gen_graphs &");
}



run();

function gen_graphs() {
    for ($i = 1; $i < 4; $i++) {
        vm_graph($i, "-1h", "", "/mnt/usbflash/graphs/vm" . $i . "-hour.png");
    }
}

function run() {
    $vmnum = (isset($_GET["v"]) ? $_GET["v"] : "");
    
    //vm_graph(1, "-1h", $start_, "/data/custom/html/mattLib/graphs/vm1-hour.png");
    
    $imageArray = array (
    'vm1' => 
        array (
            'hour' => get_vm_hour(1),
            'day' => get_vm_day(1),
            'week' => get_vm_week(1),
            'month' => get_vm_month(1),
            'year' => get_vm_year(1),
            'custom' => get_vm_custom(1)
        ), 
    'vm2' =>
        array (
            'hour' => get_vm_hour(2),
            'day' => get_vm_day(2),
            'week' => get_vm_week(2),
            'month' => get_vm_month(2),
            'year' => get_vm_year(2),
            'custom' => get_vm_custom(2)
        ),
    'vm3' => 
        array (
            'hour' => get_vm_hour(3),
            'day' => get_vm_day(3),
            'week' => get_vm_week(3),
            'month' => get_vm_month(3),
            'year' => get_vm_year(3),
            'custom' => get_vm_custom(3)
        )
        
    );
        
    $dataString = json_encode($imageArray);
    print($dataString);
}

function clean($range) {
    
}




function get_vm_hour($vmnum) {
    //vm_graph($vmnum, "-1h", "", "/mnt/usbflash/graphs/vm" . $vmnum . "-hour.png");
    //vm_graph($vmnum, "-1h", "", "/mnt/usbflash/graphs/vm" . $vmnum . "-hour.png");
   // $imageData = base64_encode(file_get_contents("/mnt/usbflash/graphs/vm" . $vmnum . "-hour.png"));
    $imageData = base64_encode(file_get_contents("/mnt/usbflash/graphs/vm" . $vmnum . "-hour.png"));
    return $imageData;                       
}

function get_vm_day($vmnum) {
    //vm_graph($vmnum, "-1d", "", "/mnt/usbflash/graphs/vm" . $vmnum . "-day.png");
    $imageData = base64_encode(file_get_contents("/mnt/usbflash/graphs/vm" . $vmnum . "-day.png"));
    return $imageData;                       
}

function get_vm_week($vmnum) {
  //  vm_graph($vmnum, "-1w", "", "/mnt/usbflash/graphs/vm" . $vmnum . "-week.png");
    $imageData = base64_encode(file_get_contents("/mnt/usbflash/graphs/vm" . $vmnum. "-week.png"));
    return $imageData;                       
}

function get_vm_month($vmnum) {
 //   vm_graph($vmnum, "-1m", "", "/mnt/usbflash/graphs/vm" . $vmnum . "-month.png");
    $imageData = base64_encode(file_get_contents("/mnt/usbflash/graphs/vm" . $vmnum . "-month.png"));
    return $imageData;                       
}

function get_vm_year($vmnum) {
    vm_graph($vmnum, "-1y", "", "/mnt/usbflash/graphs/vm" . $vmnum . "-year.png");
    $imageData = base64_encode(file_get_contents("/mnt/usbflash/graphs/vm" . $vmnum . "-year.png"));
    return $imageData;                       
}

function get_vm_custom($vmnum) {
    $lazy = FALSE;
    $s = (isset($_GET["s"]) ? $_GET["s"] : "");
    $e = (isset($_GET["e"]) ? $_GET["e"] : "");
    $startDate = $s;
    $endDate = $e;
    
    if ($s != "" && $e != "") {
        $startDate = strtotime($s);
        $endDate = strtotime($e);
        vm_graph($vmnum, $startDate, $endDate, "/mnt/usbflash/graphs/vm" . $vmnum . "-custom.png");
        $imageData = base64_encode(file_get_contents("/mnt/usbflash/graphs/vm" . $vmnum . "-custom.png"));
        return $imageData;   
    } else {
        return "";
    }
}

function vm_graph( $vmnum, $start, $end, $temp_filename ) {
	global $hostname, $vm_info, $img_type, $tmp_path, $cli, $start_date, $lazy, $time_now, $graph_width, $graph_height;
	if($start == "-1h")	
		{	
		$filename = $temp_filename;
		$g_start_text = str_replace(":", '\:', date("Y-m-d H:i", ($time_now -  3600) ) );
		$timespan = " - (1 Hour)";	
		}
	else if($start == "-1d")	
		{	
        $filename = $temp_filename;
        $g_start_text = str_replace(":", '\:', date("Y-m-d H:i", ($time_now -  86400) ) );
		$timespan = " - (1 Day)";	
		}
	else if($start == "-1w")	
		{
        $filename = $temp_filename;
	//	$filename = "$tmp_path/vm" . $vmnum . "-week." . $img_type . "";
		$g_start_text = str_replace(":", '\:', date("Y-m-d H:i", ($time_now -  604800) ) );
		$timespan = " - (1 Week)";	
		}
	else if($start == "-1m")	
		{	
        $filename = $temp_filename;
        $lastmonth = mktime( date("H"),  date("i"), 0, date("m")-1, date("d"),   date("Y"));
		$g_start_text = str_replace(":", '\:', date("Y-m-d H:i", $lastmonth ) );
		$timespan = " - (1 Month)";
		}
	else if($start == "-1y")	
		{	
//		$filename = "$tmp_path/vm" . $vmnum . "-year." . $img_type . "";
        $filename = $temp_filename;
		$g_start_text = str_replace(":", '\:', date("Y-m-d H:i", ($time_now -  31536000)  ) );
		$timespan = " - (1 Year)";	
		}
	else // specific start and end dates given (zoom graph)
		{	
		//$filename = "$tmp_path/vm" . $vmnum . "." . $img_type . "";
		$filename = $temp_filename;
        
        $dt = date("Y-m-d H:i", $start);
		$g_start_text = str_replace(":", '\:', $dt);	
        //$g_start_text = $dt;
        //console_log($dt);
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

	$negarea_color = "#FF0000";
	$negarea_outline_color = "#000000";
	$posarea_color = "#0cf550";
	$posarea_outline_color = "#000000";
	$min_color = "#FF7F50";
	$max_color = "#0000FF";
	// Voltmeter Mode
		$opts = array("--start", $start,
									"--imgformat=" . strtoupper($img_type),
									"--interlaced",
									"-w " . $graph_width,
									"-h " . $graph_height,
									"--font", "TITLE:10:",
									"--font", "AXIS:9:",
									"--font", "LEGEND:8:",
									"--font", "UNIT:9: ",
									"COMMENT:FROM\: $g_start_text - TO\: $g_end_text" . $timespan . "\c",
									"COMMENT:\s",
									"--title=$hostname - Voltmeter $vmnum - " . $vm_info['name'][$vmnum],
									"--vertical-label=Volts",
									"DEF:a=$tmp_path/rms.rrd:vm" . $vmnum . ":AVERAGE",
									"CDEF:grnpos=a,0,MAX",
									"CDEF:redneg=a,0,MIN",
									"AREA:grnpos" . $posarea_color . ":Volts ",
									"LINE1:grnpos" . $posarea_outline_color,
									"AREA:redneg" . $negarea_color,
									"LINE1:redneg" . $negarea_outline_color,

									"GPRINT:a:MIN:Min\:%8.3lf%s",
									"GPRINT:a:MAX:Max\:%8.3lf%s",
									"GPRINT:a:AVERAGE:Avg\:%8.3lf%s",
									"GPRINT:a:LAST:Cur\:%8.3lf%s\\n",
									);		
		
	
	if($vm_info['slope_enable'][$vmnum] == "CHECKED")
		{
			$opts[] = "--slope";
		}
		
		
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


function console_log($output, $with_script_tags = true) {
    $js_code = 'console.log(' . json_encode($output, JSON_HEX_TAG) . 
');';
    if ($with_script_tags) {
        $js_code = '<script>' . $js_code . '</script>';
    }
    echo $js_code;
}
?>