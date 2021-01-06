<?php
	//error_reporting(E_ALL);

	
	function start_header()
	{
		echo '
		<div id="header">
		    <div class="solid-line">
		    </div>
		    <div id="logo" class="light-version">
		        <span>
		            <a href="http://www.gridsurfer.net/index.html" target="_blank" style="color:white">GMS-100</a>
		        </span>
		    </div>
		    <nav role="navigation">
		        <div class="header-link hide-menu"><i class="fa fa-bars" style="color:white;"></i></div>
		        
		        <div id="logo2" class="light-version hidden-xs">
		        	<span>
		            Solar Rig Tech
		        	</span>
		    		</div>
		        
		        <div id="logo" class="light-version visible-xs" style="max-width:150px">
		        	<span>
		            GMS-100
		        	</span>
		    		</div>
		        
		        <div class="dropdown-menu-left visible-xs" style="float:right;">
 							<button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown">
 								<span class="caret"></span>
 							</button>
							<ul class="dropdown-menu hdropdown notification animated flipInX nav navbar-nav">
		          	<li>
		            	<a href="/setup_power.php?restart">
		                <span class="label label-warning">&nbsp;&nbsp;</span> Reboot
		              </a>
		            </li>
		            <li>
		                <a href="/setup_power.php?shutdown">
		                    <span class="label label-danger">&nbsp;&nbsp;</span> Shut Down
		                </a>
		            </li>
		            <li>
		                <a href="/setup_power.php">
		                    <span class="label label-success">&nbsp;&nbsp;</span> See all Power Options
		                </a>
		            </li>

		          </ul>
						</div>
		        
		        <div class="navbar-right">
							<ul class="nav navbar-nav no-borders">
								<li class="dropdown">
									<a class="dropdown-toggle" href="#" data-toggle="dropdown">
										<i class="fa fa-power-off" style="color:white;"></i>
		              </a>
		              <ul class="dropdown-menu hdropdown notification animated flipInX">
										<li>
											<a href="/setup_power.php?restart">
												<span class="label label-warning">&nbsp;&nbsp;</span> Reboot
		                  </a>
		                </li>
		                <li>
											<a href="/setup_power.php?shutdown">
												<span class="label label-danger">&nbsp;&nbsp;</span> Shut Down
		                  </a>
		                </li>
		                <li>
											<a href="/setup_power.php">
												<span class="label label-success">&nbsp;&nbsp;</span> See all Power Options
		                  </a>
		                </li>
		              </ul>
		            </li>
		          </ul>
		        </div>
		    </nav>
		</div>
		<!-- END Header -->
		';
	}
	
	function setup_top_header()
	{
		$hostname = trim(file_get_contents("/etc/hostname"));
		echo "<!DOCTYPE html>";
		echo "<html>";
		echo "<head>";
  	echo "  <meta charset='utf-8'>";
  	echo "  <meta name='viewport' content='width=device-width, initial-scale=1.0'>";
  	echo "  <meta http-equiv='X-UA-Compatible' content='IE=edge'>";
  	echo "  <!-- Page title -->";
  	echo "  <title>". $hostname . "</title>";
  	$myrand = rand();
  	echo "  <link rel='shortcut icon' type='image/ico' href='/favicon.ico?".$myrand."' />\n"; 		
  	echo "  <!-- CSS -->";
  	echo "  <link rel='stylesheet' href='/css/fontawesome/css/font-awesome.css' />";
  	echo "  <link rel='stylesheet' href='/css/animate.css' />";
  	echo "  <link rel='stylesheet' href='/css/bootstrap.css' />";
  	echo "	<link rel='stylesheet' href='/css/awesome-bootstrap-checkbox.css' />";
  	echo "  <link rel='stylesheet' href='/css/sweetalert.css' />";
  	echo "  <link rel='stylesheet' href='/css/ethertek.css'>";
  	echo "  <!-- Java Scripts -->";
		echo "	<script src='/javascript/jquery.min.js'></script>";
		echo "	<script src='/javascript/bootstrap.min.js'></script>";
		echo "	<script src='/javascript/sweetalert.min.js'></script>";
		echo "	<script src='/javascript/conhelp.js'></script>";
		echo "	<script src='/javascript/ethertek.js'></script>";
		echo "	<script language='javascript' type='text/javascript'>";
		echo "		SetContext('setup');";
		echo "	</script>";
		echo "</head>";
		echo "<body class='fixed-navbar fixed-sidebar'>";
		echo "	<div class='splash'> <div class='solid-line'></div><div class='splash-title'><h1>Loading... Please Wait...</h1><div class='spinner'> <div class='rect1'></div> <div class='rect2'></div> <div class='rect3'></div> <div class='rect4'></div> <div class='rect5'></div> </div> </div> </div>";
		echo "	<!--[if lt IE 7]>";
		echo "	<p class='alert alert-danger'>You are using an <strong>old</strong> web browser. Please upgrade your browser to improve your experience.</p>";
		echo "	<![endif]-->";

	}
	
	
	
	
	function left_nav($page)
	{
		//$current_page = basename($_SERVER['PHP_SELF']);
		
		
		echo "<!-- Navigation -->\n";
		echo "<aside id='menu' style='background-color:#00C000;'>\n";
		echo "	<div id='navigation'>\n";
		echo "		<ul class='nav' id='side-menu'>\n";
		
		if($page == "home")
		{
			echo "		<li id='home' class='active'>\n";
		}
		else
		{
			echo "		<li id='home'>\n";
		}
		echo "		    <a href='/index.php' onMouseOver='mouse_move(&#039;b_home&#039;);'  onMouseOut='mouse_move();'><img src='/images/nav_home.gif'><span style='margin-left:10px;font-size:12px;'>Home</span></a>\n";
		echo "			</li>\n";
		
		if($page == "ios")
		{
			echo "		<li id='ios' class='active'>\n";
		}
		else
		{
			echo "		<li id='ios'>\n";
		}
		echo "		    <a href='/ios.php' onMouseOver='mouse_move(&#039;b_ios&#039;);'  onMouseOut='mouse_move();'><img src='/images/ios.gif'><span style='margin-left:10px;font-size:12px;'>IOs</span></a>\n";
		echo "			</li>\n";
		
		if($page == "relays")
		{
			echo "		<li id='relays' class='active'>\n";
		}
		else
		{
			echo "		<li id='relays'>\n";
		}
		echo "		    <a href='/relays.php' onMouseOver='mouse_move(&#039;b_relays&#039;);'  onMouseOut='mouse_move();'><img src='/images/relay16x16.gif'><span style='margin-left:10px;font-size:12px;'>RELAYS</span></a>\n";
		echo "			</li>\n";
		
		if($page == "temperature")
		{
			echo "		<li id='temperature' class='active'>\n";
		}
		else
		{
			echo "		<li id='temperature'>\n";
		}
		echo "		    <a href='/temperature.php' onMouseOver='mouse_move(&#039;b_temperature&#039;);' onMouseOut='mouse_move();'><img src='/images/sun.gif'><span style='margin-left:10px;font-size:12px;'>TEMPERATURE</span></a>\n";
		echo "			</li>\n";
		
		if($page == "voltmeters")
		{
			echo "		<li id='voltmeters' class='active'>\n";
		}
		else
		{
			echo "		<li id='voltmeters'>\n";
		}
		echo "		    <a href='/voltmeters.php' onMouseOver='mouse_move(&#039;b_voltmeters&#039;);' onMouseOut='mouse_move();'><img src='/images/battery_small.gif'><span style='margin-left:10px;font-size:12px;'>VOLTMETERS</span></a>\n";
		echo "			</li>\n";
		
		if($page == "rms-graph")
		{
			echo "		<li id='graphs' class='active'>\n";
		}
		else
		{
			echo "		<li id='graphs'>\n";
		}
		echo "		    <a href='/rms-graph.php' onMouseOver='mouse_move(&#039;sd_graphs&#039;);' onMouseOut='mouse_move();'><img src='/images/graph.gif'><span style='margin-left:10px;font-size:12px;'>GRAPHS</span></a>\n";
		echo "			</li>\n";
		
		
    $dbh = new PDO('sqlite:/etc/rms100.db');
		$result  = $dbh->query("SELECT * FROM device_mgr ORDER BY id;");			
		foreach($result as $row)
			{
				$id = $row['id'];
				$name = $row['name'];
				$device = $row['device'];
				$init = $row['init'];
				$baud = $row['baud'];
				$flowctl = $row['flowctl'];
				$type = $row['type'];
				$enabled = $row['enabled'];
				$sdvar1 = $row['sdvar1'];
				$sdvar2 = $row['sdvar2'];
				$sdvar3 = $row['sdvar3'];
				$sdvar4 = $row['sdvar4'];
				$sdvar5 = $row['sdvar5'];
				$sdvar6 = $row['sdvar6'];
				$sdvar7 = $row['sdvar7'];
				$sdvar8 = $row['sdvar8'];
				$sdvar9 = $row['sdvar9'];
				$sdvar10 = $row['sdvar10'];
				$sdvar11 = $row['sdvar11'];
				$sdvar12 = $row['sdvar12'];
				
				if($type == "CAMERA")
				{
					
					if($page == "camera")
					{
						echo "		<li id='camera' class='active'>\n";
					}
					else
					{
						echo "		<li id='camera'>\n";
					}
					echo "		    <a href='/device_web_cam.php' onMouseOver='mouse_move(&#039;b_camera&#039;);' onMouseOut='mouse_move();'><img src='/images/webcam16x16.gif'><span style='margin-left:10px;font-size:12px;'>" . $name . "</span></a>\n";
					echo "			</li>\n";
				}
				
				if($type == "GPS")
				{
					
					if($page == "gps")
					{
						echo "		<li id='gps' class='active'>\n";
					}
					else
					{
						echo "		<li id='gps'>\n";
					}
					echo "		    <a href='/device_gps.php' onMouseOver='mouse_move(&#039;gps_default&#039;);' onMouseOut='mouse_move();'><img src='/images/gps16x16.gif'><span style='margin-left:10px;font-size:12px;'>" . $name . "</span></a>\n";
					echo "			</li>\n";
				}
				
				if($type == "RDB")
				{
					
					if($page == "rdb")
					{
						echo "		<li id='rdb' class='active'>\n";
					}
					else
					{
						echo "		<li id='rdb'>\n";
					}
					echo "		    <a href='/device_rdb.php' onMouseOver='mouse_move(&#039;rdb_default&#039;);' onMouseOut='mouse_move();'><img src='/images/relay16x16.gif'><span style='margin-left:10px;font-size:12px;'>" . $name . "</span></a>\n";
					echo "			</li>\n";
				}
				
				if($type == "VDB")
				{
					
					if($page == "vdb")
					{
						echo "		<li id='vdb' class='active'>\n";
					}
					else
					{
						echo "		<li id='vdb'>\n";
					}
					echo "		    <a href='/device_vdb.php' onMouseOver='mouse_move(&#039;vdb_default&#039;);' onMouseOut='mouse_move();'><img src='/images/vdb16x16.gif'><span style='margin-left:10px;font-size:12px;'>" . $name . "</span></a>\n";
					echo "			</li>\n";
				}
				
				if($type == "EXTEMP")
				{
					
					if($page == "extemp")
					{
						echo "		<li id='extemp' class='active'>\n";
					}
					else
					{
						echo "		<li id='extemp'>\n";
					}
					echo "		    <a href='/extemp-graph.php' onMouseOver='mouse_move(&#039;sd_extemp&#039;);' onMouseOut='mouse_move();'><img src='/images/extemp16x16.gif'><span style='margin-left:10px;font-size:12px;'>" . $name . "</span></a>\n";
					echo "			</li>\n";
				}
				
				if($type == "EFOY")
				{
					
					if($page == "efoy")
					{
						echo "		<li id='efoy' class='active'>\n";
					}
					else
					{
						echo "		<li id='efoy'>\n";
					}
					echo "		    <a href='/device_efoy.php' onMouseOver='mouse_move(&#039;efoy_default&#039;);' onMouseOut='mouse_move();'><img src='/images/efoy16x16.gif'><span style='margin-left:10px;font-size:12px;'>" . $name . "</span></a>\n";
					echo "			</li>\n";
				}
				
				if($type == "CUSTOM")
				{
					if($page == "custom")
					{
						echo "		<li id='custom' class='active'>\n";
						echo "		    <a href='/" . $sdvar1 . "' onMouseOver='mouse_move(&#039;sd_custom&#039;);' onMouseOut='mouse_move();'><img src='" . $sdvar2 . "'><span style='margin-left:10px;font-size:12px;'>" . $name . "</span></a>\n";
						echo "		</li>\n";
					}
					else
					{
						echo "		<li id='custom'>\n";
						echo "		    <a href='" . $sdvar1 . "' onMouseOver='mouse_move(&#039;sd_custom&#039;);' onMouseOut='mouse_move();'><img src='" . $sdvar2 . "'><span style='margin-left:10px;font-size:12px;'>" . $name . "</span></a>\n";
						echo "		</li>\n";
					}
				}
				
						
			}
		
		
		echo "		</ul>\n";
		echo "	</div>\n";
		echo "	<div>\n";
		echo "	<br>\n";
		echo "	</div>\n";
		echo "	<div>\n";
		echo "		<ul class='nav' id='side-menu'>\n";
		
		if($page == "setup")
		{
			echo "		<li id='setup' class='active'>\n";
		}
		else
		{
			echo "		<li id='setup'>\n";
		}
		echo "	    	<a href='/setup.php' onMouseOver='mouse_move(&#039;b_setup&#039;);' onMouseOut='mouse_move();'><img src='/images/setup.gif'><span style='margin-left:10px;font-size:12px;'>SETUP</span></a>\n";
		echo "	    </li>\n";
		echo "	    <li>\n";
		echo "	    	<a href='#' onMouseOver='mouse_move(&#039;b_help&#039;);' onMouseOut='mouse_move();' data-toggle='modal' data-target='#HelpModal'><img src='/images/nav_help.gif'><span style='margin-left:10px;font-size:12px;'>HELP</span></a>\n";
		echo "	    </li>\n";
		echo "	  </ul>\n";
		echo "	  <div class='modal fade' id='HelpModal' tabindex='-1' role='dialog' aria-hidden='true'>\n";
		echo "	 		<div class='modal-dialog'>\n";
		echo "	 			<div class='modal-content'>\n";
		echo "	 				<div class='modal-header text-center'>\n";
		echo "				    <h4 class='modal-title'><img src='/images/help.gif'> GMS-100 Help and Support</h4>\n";
		echo "	 				</div>\n";
		echo "	 				<div class='modal-body'>\n";
		echo "				    <p>\n";
		echo "				    	<span style='color:blue;'><strong>Navigation:</strong></span><br><br>\n";
		echo "	 				    	The shortcuts in the left navigation pane gives you access to the following GMS-100 pages:\n";
		echo "				    </p>\n";
		echo "				    <p>\n";
		echo "				    	<ol>\n";
		echo "				    		<li><span style='color:red;'><strong>Home:</strong></span> this page shows a basic overview of the GMS-100 system.</li>\n";
		echo "				    		<li><span style='color:red;'><strong>IOs:</strong></span> shows all the general purpose IOs available.</li>\n";
		echo "				    		<li><span style='color:red;'><strong>Relays:</strong></span> lets you control and setup the onboard power relays.</li>\n";
		echo "				    		<li><span style='color:red;'><strong>Temperature:</strong></span> lets you view and setup temperature related options.</li>\n";
		echo "				    		<li><span style='color:red;'><strong>Voltmeters:</strong></span>  lets you view and setup the onboard ADC inputs.</li>\n";
		echo "				    		<li><span style='color:red;'><strong>Graphs:</strong></span> lets you view graphical data of system functions.</li>\n";
		echo "				    		<li><span style='color:red;'><strong>Setup:</strong></span> allows you to configure and manage the GMS-100 board.</li>\n";
		echo "				    		<li><span style='color:red;'><strong>Help:</strong></span> displays this help box.</li>\n";
		echo "				    	</ol>\n";
		echo "				    </p>\n";
		echo "				    <p>\n";
		echo "				    	<span style='color:blue;'><strong>Support:</strong></span><br><br>\n";
		echo "	 				    	<strong>Main Website: </strong><a href='http://www.gridsurfer.net/index.html'> http://www.gridsurfer.net/index.html</a>\n";
		echo "				    </p>\n";
		echo "	 				</div>\n";
		echo "	 				<div class='modal-footer'>\n";
		echo "				    <button type='button' class='btn btn-success' data-dismiss='modal'>Close</button>\n";
		echo "	 				</div>\n";
		echo "	 			</div>\n";
		echo "	 		</div>\n";
		echo "	 	</div>\n";
		echo "	</div>\n";
		echo "	<div style='background-color: #D6DFF7; height:200px; border-top:3px solid #00C000;border-right:3px solid #00C000;padding: 10px;'>\n";
		echo "		<table border='0' width='100%' id='conhelp'>\n";
		echo "			<thead>\n";
		echo "				<tr>\n";
		echo "					<td width='100%'><div id='contexthelp'><div><span id='contexthelp_text' style='font-size:12px'>Javascript must be enabled for context help to function!</span></div></div></td>\n";
		echo "				</tr>\n";
		echo "			</thead>\n";
		echo "		</table>\n";
		echo "	</div>\n";
		echo "	<div>\n";
		echo "		<br>\n";
		
		$build = trim(file_get_contents("/etc/BUILDNUM"));
		$build = explode("=",$build);
		$kbuild = trim(file_get_contents("/etc/KBUILD"));
		
		$hostname = trim(file_get_contents("/etc/hostname"));
		echo "			<span style='display:block;text-align:center;color:white;font-size:10px'>".$hostname."<br></span>\n";
		echo "			<br>\n";
		
		echo "			<span style='display:block;text-align:center;color:white;'>Kernel Build Date:<br></span>\n";
		echo "			<span style='display:block;text-align:center;color:white;'>" . $kbuild . "</span>\n";
		echo "			<br>\n";
		echo "			<span style='display:block;text-align:center;color:white;'>Root FS Build: " . $build[1] . "<br></span>\n";
		
		if(file_exists("/tmp/fwv"))
		{
			$fwv = trim(file_get_contents("/tmp/fwv"));
			if($fwv > $build[1])
			{
				echo "			<span id='blinkfw'>\n";
				echo "				<a target='_blank' href='http://www.gridsurfer.net/index.html' STYLE='display:block;text-align:center; color:#FFFFFF;'>Update Available!</a>\n";
				echo "			</span>\n";
				
				echo '			<script type="text/javascript">';
				echo '			    function blinkFirmware() {';
				echo '			        if ($("#blinkfw").css("visibility").toUpperCase() == "HIDDEN") {';
				echo '			            $("#blinkfw").css("visibility", "visible");';
				echo '			            setTimeout(blinkFirmware, 500);';
				echo '			        } else {';
				echo '			            $("#blinkfw").css("visibility", "hidden");';
				echo '			            setTimeout(blinkFirmware, 500);';
				echo '			        }';
				echo '			    }';
				echo '			    blinkFirmware();';
				echo '			</script>';
			}
		}
		echo "	</div>\n";
		echo "</aside>\n";
		echo "<!-- END Navigation -->\n";
		
	}
	
function uptime()
{
	$uptime = trim(file_get_contents( "/proc/uptime"));
	$uptime = explode(" ",$uptime);
	$uptime = $uptime[0];
	$uptime = $uptime / 60 / 60 / 24;
	$time = sprintf("%4.2f Days",$uptime);
	
//	$days = explode(".",(($uptime % 31556926) / 86400));
//	$hours = explode(".",((($uptime % 31556926) % 86400) / 3600));
//	$minutes = explode(".",(((($uptime % 31556926) % 86400) % 3600) / 60));
//	$time = ".";
//	if ($minutes > 0){ $time=$minutes[0]." mins".$time; }
//	if ($minutes > 0 && ($hours > 0 || $days > 0)){ $time = ", ".$time; }  
//	if ($hours > 0){ $time = $hours[0]." hours".$time; } 
//	if ($hours > 0 && $days > 0){ $time = ", ".$time; }  
//	if ($days > 0){ $time = $days[0]." days".$time; }
  return $time;
}	
	
	
	
function is_valid_domain_name($domain_name)
{
    return (preg_match("/^([a-z\d](-*[a-z\d])*)(\.([a-z\d](-*[a-z\d])*))*$/i", $domain_name) //valid chars check
            && preg_match("/^.{1,253}$/", $domain_name) //overall length check
            && preg_match("/^[^\.]{1,63}(\.[^\.]{1,63})*$/", $domain_name)   ); //length of each label
}	
	
function selectbox($c1, $c2, $current_cmds)
{
	// c1 = HI || HI_N || LO || LO_N
	// c2 = script || alert
	$sd_query="";
	$ii=0;
	$buf="";
	$sdbuf1="";
	$sdbuf2="";
	$label="";
	
	$dbh = new PDO('sqlite:/etc/rms100.db');
	
	if($c2 == "alert") 
	{ 
		$label = "Alerts"; 
	}
	else 
	{
		$label = "Scripts"; 
	}
	
	echo "<div class='table-responsive' style='text-align:center;color:green;'>";
	echo "<table style='margin:auto;' width='75%'>";
	echo "<tr><td>Available ".$label."<br>";
	echo "<select multiple size='5' id='".$c1."_".$c2."_addcmd' name='".$c1."_".$c2."_addcmd' class='form-control' style='min-width:200px; font-family: monospace;'>";
	
	if($c2 == "script") 
	{ 
		$query = "SELECT * FROM scripts WHERE type = 'relay' UNION SELECT * FROM scripts WHERE type = 'io' ORDER BY id"; 
	}
	else if($c2 == "alert")	
	{ 
		$query = "SELECT * FROM alerts ORDER BY id"; 
	}
	
	$result  = $dbh->query($query);
	foreach($result as $row)
	{
		$id = $row['id'];
		$sdbuf1 = $row['type'];
		$sdbuf2 = $row['name'];
		if($sdbuf1 == "relay")
		{
			$sdbuf1 = "RELAY";
		}
		if($sdbuf1 == "io")
		{
			$sdbuf1 = "GXIO";
		}	
		echo "<option value='".$id."'>".$sdbuf1." - ".$sdbuf2."</option>";
	}
	echo "</select>";
	
	echo "<br><p style='text-align:center;'>";
	echo "<input id='add_".$c1."_".$c2."' type='button' class='btn btn-success' name='add_command' value='ADD' onMouseOver='mouse_move(\"b_cmd_add\");' onMouseOut='mouse_move();'>";
	
	echo"</p></td>";
	
	echo"<td style='text-align:center;'><br>&nbsp;<i class='fa fa-arrow-left'></i><i class='fa fa-arrow-right'></i>&nbsp;<br><br><br>";
	echo"</td>";
	
	echo"<td style='text-align:center;color:blue'>Selected ".$label."<br>";
	echo "<select multiple size='5' id='".$c1."_".$c2."_delcmd' name='".$c1."_".$c2."_delcmd[]' class='form-control' style='min-width:200px; font-family: monospace;'>";
	
	// sample commands:		"2.12.7.\0x00"
	
	if(strlen($current_cmds)!==0)
	{
		$buf = explode(".",$current_cmds);
		$count = count($buf);
	
		for($ii=1; $ii < $count; $ii++)
		{
			echo"<option value='".$buf[$ii-1]."'>";
			if($c2 == "script") 
			{ 
				$query = sprintf("SELECT * FROM scripts where id = '%s';", $buf[$ii-1]);
				$result  = $dbh->query($query);
				foreach($result as $row)
				{
					$sdbuf1 = $row['type'];
					$sdbuf2 = $row['name'];
				}
				
				if(strlen($sdbuf1)>7)  {  $sdbuf1 = substr($sdbuf1, 0, 6); 	}
				if(strlen($sdbuf2)>9) {	 $sdbuf2 = substr($sdbuf2, 0, 24); 	}
				if($sdbuf1 == "relay")
				{
					$sdbuf1 = "RELAY";
				}
				if($sdbuf1 == "io")
				{
					$sdbuf1 = "GXIO";
				}
				$query = sprintf("%s - %s",$sdbuf1,$sdbuf2);
				echo $query."</option>";
			}
			else if($c2 == "alert")
			{ 
				$query = sprintf("SELECT * FROM alerts where id = '%s';", $buf[$ii-1]);
				$result  = $dbh->query($query);
				foreach($result as $row)
				{
					$sdbuf1 = $row['type'];
					$sdbuf2 = $row['name'];
				}
				if(strlen($sdbuf2)>9)
				{
				    //trim and add dots if more than 10 chars
					$sdbuf2 = substr($sdbuf2, 0, 24);
				}
				$query = sprintf("%s - %s",$sdbuf1,$sdbuf2);
				echo $query."</option>";			
			}
			else
			{
			  echo "uuuu</option>";
			}
		}
	}
	
	echo "</select>";
	
	echo "<br><p style='text-align:center;'>";
	echo "<input id='remove_".$c1."_".$c2."' type='button' class='btn btn-primary' name='del_command' value='Remove' onMouseOver='mouse_move(\"b_cmd_del\");' onMouseOut='mouse_move();'>";
	
	echo "</p></td></tr>";
	echo "</table></div>";
}
	
function secondsToTime($inputSeconds) {

    $secondsInAMinute = 60;
    $secondsInAnHour  = 60 * $secondsInAMinute;
    $secondsInADay    = 24 * $secondsInAnHour;

    // extract days
    $dayz = floor($inputSeconds / $secondsInADay);

    // extract hours
    $hourSeconds = $inputSeconds % $secondsInADay;
    $hourz = floor($hourSeconds / $secondsInAnHour);

    // extract minutes
    $minuteSeconds = $hourSeconds % $secondsInAnHour;
    $minutez = floor($minuteSeconds / $secondsInAMinute);

    // extract the remaining seconds
    $remainingSeconds = $minuteSeconds % $secondsInAMinute;
    $secondz = ceil($remainingSeconds);

    // return the final array
    $obj = array(
        'd' => (int) $dayz,
        'h' => (int) $hourz,
        'm' => (int) $minutez,
        's' => (int) $secondz,
    );
    return $obj;
}

function restart_some_services()
{
	//RMSD
	if(file_exists("/var/run/rmsd.pid"))
	{
		system("kill -HUP `cat /var/run/rmsd.pid`");
	}
	
	//RMSpingD
	if(file_exists("/var/run/rmspingd.pid"))
	{
		system("kill -HUP `cat /var/run/rmspingd.pid`");
	}
	
	//RMSvmD (USB iso voltmeter board)
	if(file_exists("/var/run/rmsvmd.pid"))
	{
		system("kill -HUP `cat /var/run/rmsvmd.pid`");
	}
}
	
	
?>