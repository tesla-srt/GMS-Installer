 
<?php		/* ----------- A better File Explorer/Editor (C) 2001-2016 ETHERTEK CIRCUITS ----------- */

include "lib.php";

$alert_flag = "0";

/////////////////////////////////////////////////////////////////
//                                                             //
//                    POST PROCESSING                          //
//                                                             //
/////////////////////////////////////////////////////////////////
	if( isset( $_POST["edit_file_path"] ) )
	{
		$mypath = $_POST["edit_file_path"];
		$mypath = $mypath . "/";
		$myfilename = $_POST["edit_file_name"];
		$mycontents = $_POST["editbox"];
		$mycontents = base64_decode($mycontents);
		$mycontents = stripslashes($mycontents);
		$h = fopen($myfilename, "w");
		if(isset($_POST["convert"]))
			{
    		$mycontents = str_replace("\r","",$mycontents);
			}
		fwrite($h, $mycontents); //save the file
		fclose($h);
		goto escape_hatch;
	}


/////////////////////////////////////////////////////////////////
//                                                             //
//                    GET PROCESSING                           //
//                                                             //
/////////////////////////////////////////////////////////////////
	
	if( isset( $_GET["button"] ) )
	{
		$mypath = $_GET["button"];
		if($mypath == "/")
		{
			goto escape_hatch;
		}
		
		$pieces = explode("/", $mypath);
		$result = count($pieces);
		$result--;
		if(substr($mypath, -1) == "/")
		{
			$result--;
		}
		
		$mypath = "/";
   	for ($i = 1; $i < $result; $i++)
   	{
   		$mypath = $mypath . $pieces[$i] . "/";
   	}
		goto escape_hatch;
	}

	if( isset( $_GET["file"] ) )
	{
		$mypath = $_GET["file"];
		if($mypath != "/")
		{
			if(is_file($mypath)!=true)
			{
				$mypath = $mypath . "/"; //add forward slash
			}
			
		}
		
	}
	else
	{
		$mypath = "/";
	}

escape_hatch:

$hostname = trim(file_get_contents("/etc/hostname"));
echo "<!DOCTYPE html>";
echo "<html>";
echo "<head>";
echo "  <meta charset='utf-8'>";
echo "  <meta name='viewport' content='width=device-width, initial-scale=1.0'>";
echo "  <meta http-equiv='X-UA-Compatible' content='IE=edge'>";
echo "  <!-- Page title -->";
echo "  <title>". $hostname . "</title>";
echo "  <link rel='shortcut icon' type='image/ico' href='rms100favicon.ico?<?php echo rand(); ?>' />";
echo "  <!-- CSS -->";
echo "  <link rel='stylesheet' href='css/fontawesome/css/font-awesome.css' />";
echo "  <link rel='stylesheet' href='css/animate.css' />";
echo "  <link rel='stylesheet' href='css/bootstrap.css' />";
echo "	<link rel='stylesheet' href='css/awesome-bootstrap-checkbox.css' />";
echo "  <link rel='stylesheet' href='css/sweetalert.css' />";
echo "  <link rel='stylesheet' href='css/ethertek.css'>";
echo "  <!-- Java Scripts -->";
echo "	<script src='javascript/jquery.min.js'></script>";
echo "	<script src='javascript/jquery-ui.min.js'></script>";
echo "	<script src='javascript/bootstrap.min.js'></script>";
echo "	<script src='javascript/sweetalert.min.js'></script>";
echo "	<script src='javascript/conhelp.js'></script>";
echo "	<script src='javascript/ethertek.js'></script>";
echo "	<script language='javascript' type='text/javascript'>";
echo "		SetContext('explorer');";
echo "	</script>";
echo "</head>";
echo "<body class='fixed-navbar fixed-sidebar'>";
echo "	<div class='splash'> <div class='solid-line'></div><div class='splash-title'><h1>Loading...</h1><div class='spinner'> <div class='rect1'></div> <div class='rect2'></div> <div class='rect3'></div> <div class='rect4'></div> <div class='rect5'></div> </div> </div> </div>";
echo "	<!--[if lt IE 7]>";
echo "	<p class='alert alert-danger'>You are using an <strong>old</strong> web browser. Please upgrade your browser to improve your experience.</p>";
echo "	<![endif]-->";
start_header();
left_nav("setup");
echo "<script language='javascript' type='text/javascript'>";
echo "	SetContext('explorer');";
echo "</script>";
echo "<!-- Main Wrapper -->\n";
echo "<div id='wrapper'>\n";
echo "	<div class='row'>\n";
echo "		<div class='col-sm-12'>\n";
echo "			<div class='hpanel4'>\n";
echo "     		<div class='panel-body'>\n";

echo "<b>Directory:</b> " . $mypath . "<br><br>";
echo "<a href='setup_file_explorer.php?button='/'><img src='images/root_folder.gif' title='Root Folder'></a>";
echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
echo "<a href='setup_file_explorer.php?button=" . $mypath . "'><img src='images/udir.gif' title='Up One Level'></a><br><br>";

if(is_file($mypath)==true)
{
	// Edit File.
	
	echo "<form name='edit_file' action='setup_file_explorer.php' method='post'>";
	//echo "<input type='checkbox' name='convert' value='Yes' checked> Convert CR/LF to LF?<br><br>";
	echo "<div class='checkbox checkbox-success'>";
  echo "	<input id='convert' type='checkbox' name='convert' checked />";
  echo "  <label for='convert'> Convert CR/LF to LF?</label>";     
  echo "</div>";
	echo "<textarea cols='100' rows='30' name='editbox' id='basesixfour' style='width:100%'>";
	
	$handle = fopen($mypath, "r");
	$contents = fread($handle, filesize($mypath));
	fclose($handle);
	$contents = htmlspecialchars($contents);
	echo $contents;
	echo "</textarea><br><br>";
	
	echo "<div class='form-group'>";
  echo "	<div class='col-sm-3'>";
  echo "		<button name='save_btn' class='btn btn-success' type='submit' onMouseOver='mouse_move(&#039;b_apply&#039;);' onMouseOut='mouse_move();' onclick='base64_encode();'><i class='fa fa-check' ></i> Save</button>";
  echo "	</div>";
  echo "</div>";
	
	
	//echo "<input type='submit' value='Save File'>";
	echo "<br><br><input type='hidden' value='" . $mypath . "' name='edit_file_name'/>";
	$path_parts = pathinfo($mypath);
	$justpath = $path_parts['dirname'];
	echo "<input type='hidden' value='" . $justpath . "' name='edit_file_path'/>";
	echo "</form>";

}

else if(is_dir($mypath)==true)
{

		$handle = opendir($mypath);

    while (false !== ($entry = readdir($handle))) {
        if ($entry != "." && $entry != "..") 
        {
            $files[] = $entry;
        }
    }
    closedir($handle);
    sort($files);
    
    echo "<table width='70%' border='0'>";
    echo "<tr><th STYLE='background: #DBEAF9;' width='10%' align='left'>Permissions</th><th STYLE='background: #DBEAF9;' width='10%' align='left'>Size</th><th  STYLE='background: #DBEAF9;' width='30%' align='left'>Path</th></tr>";
    foreach ($files as $f) 
    {
    	$thepath = $mypath . $f;
    	$myperm = get_perms($thepath);
    	if(is_dir($thepath))
    	{
    		echo  "<tr><td>" . $myperm . "</td><td>0</td><td><a href='setup_file_explorer.php?file=" . $thepath . "'><b>" . $f . "/</b></a></td></tr>";
    	}
    	else
    	{
    		if (is_link($thepath)) 
    		{
    			$mylink = (readlink($thepath));
    			echo  "<tr><td>l" . $myperm . "</td><td>" . filesize($thepath) . "</td><td><a href='setup_file_explorer.php?file=" . $thepath . "'>" . $f . " -> " . $mylink . "</a></td></tr>";
  			}
  			else
  			{
  				echo  "<tr><td>f" . $myperm . "</td><td>" . filesize($thepath) . "</td><td><a href='setup_file_explorer.php?file=" . $thepath . "'>" . $f . "</a></td></tr>";
  			}
    		
    	}
		} 
   echo "</table>"; 
   echo "<br><br>";
}   
    
 

	echo "					</div><!-- PANEL BODY -->\n";
	echo "				</div><!-- PANEL -->\n";
	echo "			</div><!-- END col-sm-12 -->\n";
	echo "		</div> <!-- END ROW -->\n";
	echo "	</div> <!-- END Main Wrapper -->\n";
	
	echo "<script language='javascript'>\n";
	echo "	function base64_decode()\n";
	echo "	{\n";
  echo "  	var getText = document.getElementById('basesixfour').value;\n";
  echo "  	var base64_decode = atob(getText);\n";
  echo "  	document.getElementById('basesixfour').value = base64_decode;\n";
	echo "	}\n";
	echo "	function base64_encode()\n";
	echo "	{\n";
  echo "  	var getText = document.getElementById('basesixfour').value;\n";
  echo "  	var base64_encode = btoa(getText);\n";
  echo "  	document.getElementById('basesixfour').value = base64_encode;\n";
	echo "	}\n";
	echo "</script>\n";
	
	echo "</body>";
	echo "</html>";



// **************************
// *                        *
// *    Functions Below     *
// *                        *
// **************************


 function get_perms($filename)
        {
            $perms = fileperms($filename);

            if     (($perms & 0xC000) == 0xC000) { $info = 's'; }
            elseif (($perms & 0xA000) == 0xA000) { $info = 'l'; }
            elseif (($perms & 0x8000) == 0x8000) { $info = ''; }
            elseif (($perms & 0x6000) == 0x6000) { $info = 'b'; }
            elseif (($perms & 0x4000) == 0x4000) { $info = 'd'; }
            elseif (($perms & 0x2000) == 0x2000) { $info = 'c'; }
            elseif (($perms & 0x1000) == 0x1000) { $info = 'p'; }
            else                                 { $info = 'u'; }

            //
            $info .= (($perms & 0x0100) ? 'r' : '-');
            $info .= (($perms & 0x0080) ? 'w' : '-');
            $info .= (($perms & 0x0040) ? (($perms & 0x0800) ? 's' : 'x' ) : (($perms & 0x0800) ? 'S' : '-'));

            //
            $info .= (($perms & 0x0020) ? 'r' : '-');
            $info .= (($perms & 0x0010) ? 'w' : '-');
            $info .= (($perms & 0x0008) ? (($perms & 0x0400) ? 's' : 'x' ) : (($perms & 0x0400) ? 'S' : '-'));

            //
            $info .= (($perms & 0x0004) ? 'r' : '-');
            $info .= (($perms & 0x0002) ? 'w' : '-');
            $info .= (($perms & 0x0001) ? (($perms & 0x0200) ? 't' : 'x' ) : (($perms & 0x0200) ? 'T' : '-'));

            return $info;
        }




?>
