<?				/* ---------------- Arduino UNO Web Interface (C) 2014 ETHERTEK CIRCUITS ---------------- */

global $name,$notes,$en,$HI_alert_cmds,$LO_alert_cmds,$HI_script_cmds,$LO_script_cmds,$hi_flap,$lo_flap,$dos,$RunHiIoFile,$RunLowIoFile,$iodir,$iostate,$pullup;

$method = $_SERVER['REQUEST_METHOD'];





if($method=="GET")
{
	if(isset($_GET["gxiosetup"]))
		{
			$myvar=$_GET["gxiosetup"];
			$dbh = new PDO('sqlite:/etc/rms100.db');
			
			$result  = $dbh->query("SELECT * FROM uno_io WHERE id='" . $myvar . "';");
			foreach($result as $row)
				{
					$name = $row['name'];
					$notes = $row['notes'];
					$en = $row['en'];
					$HI_alert_cmds = $row['HI_alert_cmds'];
					$LO_alert_cmds = $row['LO_alert_cmds'];
					$HI_script_cmds = $row['HI_script_cmds'];
					$LO_script_cmds = $row['LO_script_cmds'];
					$hi_flap = $row['hi_flap'];
					$lo_flap = $row['lo_flap'];
					$dos = $row['dos'];
					$RunHiIoFile = $row['RunHiIoFile'];
					$RunLowIoFile = $row['RunLowIoFile'];
					$iodir = $row['iodir'];
					$iostate = $row['iostate'];
					$pullup = $row['pullup'];		
				}
			$dbh = NULL;	
			io_setup($myvar);
		}
	else
		{
			io_displaypage();
		}	
}

if($method=="POST")
{
	// Get all form variables...
	if(isset($_POST["name"])){$name=$_POST["name"];}
	if(isset($_POST["notes"])){$notes=$_POST["notes"];}
	if(isset($_POST["en"])){$en=1;}else{$en=0;}
	if(isset($_POST["current_HI_alert_cmds"])){$HI_alert_cmds=$_POST["current_HI_alert_cmds"];}
	if(isset($_POST["current_HI_script_cmds"])){$HI_script_cmds=$_POST["current_HI_script_cmds"];}
	if(isset($_POST["current_LO_alert_cmds"])){$LO_alert_cmds=$_POST["current_LO_alert_cmds"];}
	if(isset($_POST["current_LO_script_cmds"])){$LO_script_cmds=$_POST["current_LO_script_cmds"];}
	if(isset($_POST["hi_flap"])){$current_hi_flap=$_POST["hi_flap"];}
	if(isset($_POST["lo_flap"])){$current_lo_flap=$_POST["lo_flap"];}
	if(isset($_POST["current_ionum"])){$current_ionum=$_POST["current_ionum"];}
	if(isset($_POST["current_iotype"])){$current_iotype=$_POST["current_iotype"];}
	if(isset($_POST["current_dos"])){$current_dos=$_POST["current_dos"];}
	if(isset($_POST["runhiiofile"])){$RunHiIoFile=$_POST["runhiiofile"];}
	if(isset($_POST["runlowiofile"])){$RunLowIoFile=$_POST["runlowiofile"];}
	if(isset($_POST["suppress"])){$suppressed=$_POST["suppress"];}
	if(isset($_POST["group1"])){$inout1=$_POST["group1"];}
	if(isset($_POST["group2"])){$inout2=$_POST["group2"];}
	if(isset($_POST["iostate"])){$iostate=$_POST["iostate"];}
	if(isset($_POST["pullup"])){$pullup=$_POST["pullup"];}
	
	
	if(isset($_POST["gxiosetup2"])){do_buttons('2',"ok");}
	if(isset($_POST["gxioapply2"])){do_buttons('2',"apply");} 
	if(isset($_POST["gxiosetup3"])){do_buttons('3',"ok");}
	if(isset($_POST["gxioapply3"])){do_buttons('3',"apply");}
	if(isset($_POST["gxiosetup4"])){do_buttons('4',"ok");}
	if(isset($_POST["gxioapply4"])){do_buttons('4',"apply");}
	if(isset($_POST["gxiosetup5"])){do_buttons('5',"ok");}
	if(isset($_POST["gxioapply5"])){do_buttons('5',"apply");}
	if(isset($_POST["gxiosetup6"])){do_buttons('6',"ok");}
	if(isset($_POST["gxioapply6"])){do_buttons('6',"apply");}
	if(isset($_POST["gxiosetup7"])){do_buttons('7',"ok");}
	if(isset($_POST["gxioapply7"])){do_buttons('7',"apply");}
	if(isset($_POST["gxiosetup8"])){do_buttons('8',"ok");}
	if(isset($_POST["gxioapply8"])){do_buttons('8',"apply");}
	if(isset($_POST["gxiosetup9"])){do_buttons('9',"ok");}
	if(isset($_POST["gxioapply9"])){do_buttons('9',"apply");}
	if(isset($_POST["gxiosetup10"])){do_buttons('10',"ok");}
	if(isset($_POST["gxioapply10"])){do_buttons('10',"apply");}
	if(isset($_POST["gxiosetup11"])){do_buttons('11',"ok");}
	if(isset($_POST["gxioapply11"])){do_buttons('11',"apply");}
	if(isset($_POST["gxiosetup12"])){do_buttons('12',"ok");}
	if(isset($_POST["gxioapply12"])){do_buttons('12',"apply");}
	if(isset($_POST["gxiosetup13"])){do_buttons('13',"ok");}
	if(isset($_POST["gxioapply13"])){do_buttons('13',"apply");}
	
	if(isset($_POST["gxio_refresh"])){io_setup($current_ionum);exit;}
	if(isset($_POST["main_refresh"])){io_displaypage();exit;}
	
	if(isset($_POST["uno_cancel"])){io_displaypage();exit;}
	
	if(isset($_POST["gxio_set_hi2"])){$outFile = "/tmp/unodctrl"; $fh = fopen($outFile, 'w');	fwrite($fh,"IO2STATE HIGH");fclose($fh);usleep(1500000);io_setup($current_ionum);exit;}
	if(isset($_POST["gxio_set_hi3"])){$outFile = "/tmp/unodctrl"; $fh = fopen($outFile, 'w');	fwrite($fh,"IO3STATE HIGH");fclose($fh);usleep(1500000);io_setup($current_ionum);exit;}
	if(isset($_POST["gxio_set_hi4"])){$outFile = "/tmp/unodctrl"; $fh = fopen($outFile, 'w');	fwrite($fh,"IO4STATE HIGH");fclose($fh);usleep(1500000);io_setup($current_ionum);exit;}
	if(isset($_POST["gxio_set_hi5"])){$outFile = "/tmp/unodctrl"; $fh = fopen($outFile, 'w');	fwrite($fh,"IO5STATE HIGH");fclose($fh);usleep(1500000);io_setup($current_ionum);exit;}
	if(isset($_POST["gxio_set_hi6"])){$outFile = "/tmp/unodctrl"; $fh = fopen($outFile, 'w');	fwrite($fh,"IO6STATE HIGH");fclose($fh);usleep(1500000);io_setup($current_ionum);exit;}
	if(isset($_POST["gxio_set_hi7"])){$outFile = "/tmp/unodctrl"; $fh = fopen($outFile, 'w');	fwrite($fh,"IO7STATE HIGH");fclose($fh);usleep(1500000);io_setup($current_ionum);exit;}
	if(isset($_POST["gxio_set_hi8"])){$outFile = "/tmp/unodctrl"; $fh = fopen($outFile, 'w');	fwrite($fh,"IO8STATE HIGH");fclose($fh);usleep(1500000);io_setup($current_ionum);exit;}
	if(isset($_POST["gxio_set_hi9"])){$outFile = "/tmp/unodctrl"; $fh = fopen($outFile, 'w');	fwrite($fh,"IO9STATE HIGH");fclose($fh);usleep(1500000);io_setup($current_ionum);exit;}
	if(isset($_POST["gxio_set_hi10"])){$outFile = "/tmp/unodctrl"; $fh = fopen($outFile, 'w');	fwrite($fh,"IO10STATE HIGH");fclose($fh);usleep(1500000);io_setup($current_ionum);exit;}
	if(isset($_POST["gxio_set_hi11"])){$outFile = "/tmp/unodctrl"; $fh = fopen($outFile, 'w');	fwrite($fh,"IO11STATE HIGH");fclose($fh);usleep(1500000);io_setup($current_ionum);exit;}
	if(isset($_POST["gxio_set_hi12"])){$outFile = "/tmp/unodctrl"; $fh = fopen($outFile, 'w');	fwrite($fh,"IO12STATE HIGH");fclose($fh);usleep(1500000);io_setup($current_ionum);exit;}
	if(isset($_POST["gxio_set_hi13"])){$outFile = "/tmp/unodctrl"; $fh = fopen($outFile, 'w');	fwrite($fh,"IO13STATE HIGH");fclose($fh);usleep(1500000);io_setup($current_ionum);exit;}

	if(isset($_POST["gxio_set_low2"])){$outFile = "/tmp/unodctrl"; $fh = fopen($outFile, 'w');	fwrite($fh,"IO2STATE LOW");fclose($fh);usleep(1500000);io_setup($current_ionum);exit;}
	if(isset($_POST["gxio_set_low3"])){$outFile = "/tmp/unodctrl"; $fh = fopen($outFile, 'w');	fwrite($fh,"IO3STATE LOW");fclose($fh);usleep(1500000);io_setup($current_ionum);exit;}
	if(isset($_POST["gxio_set_low4"])){$outFile = "/tmp/unodctrl"; $fh = fopen($outFile, 'w');	fwrite($fh,"IO4STATE LOW");fclose($fh);usleep(1500000);io_setup($current_ionum);exit;}
	if(isset($_POST["gxio_set_low5"])){$outFile = "/tmp/unodctrl"; $fh = fopen($outFile, 'w');	fwrite($fh,"IO5STATE LOW");fclose($fh);usleep(1500000);io_setup($current_ionum);exit;}
	if(isset($_POST["gxio_set_low6"])){$outFile = "/tmp/unodctrl"; $fh = fopen($outFile, 'w');	fwrite($fh,"IO6STATE LOW");fclose($fh);usleep(1500000);io_setup($current_ionum);exit;}
	if(isset($_POST["gxio_set_low7"])){$outFile = "/tmp/unodctrl"; $fh = fopen($outFile, 'w');	fwrite($fh,"IO7STATE LOW");fclose($fh);usleep(1500000);io_setup($current_ionum);exit;}
	if(isset($_POST["gxio_set_low8"])){$outFile = "/tmp/unodctrl"; $fh = fopen($outFile, 'w');	fwrite($fh,"IO8STATE LOW");fclose($fh);usleep(1500000);io_setup($current_ionum);exit;}
	if(isset($_POST["gxio_set_low9"])){$outFile = "/tmp/unodctrl"; $fh = fopen($outFile, 'w');	fwrite($fh,"IO9STATE LOW");fclose($fh);usleep(1500000);io_setup($current_ionum);exit;}
	if(isset($_POST["gxio_set_low10"])){$outFile = "/tmp/unodctrl"; $fh = fopen($outFile, 'w');	fwrite($fh,"IO10STATE LOW");fclose($fh);usleep(1500000);io_setup($current_ionum);exit;}
	if(isset($_POST["gxio_set_low11"])){$outFile = "/tmp/unodctrl"; $fh = fopen($outFile, 'w');	fwrite($fh,"IO11STATE LOW");fclose($fh);usleep(1500000);io_setup($current_ionum);exit;}
	if(isset($_POST["gxio_set_low12"])){$outFile = "/tmp/unodctrl"; $fh = fopen($outFile, 'w');	fwrite($fh,"IO12STATE LOW");fclose($fh);usleep(1500000);io_setup($current_ionum);exit;}
	if(isset($_POST["gxio_set_low13"])){$outFile = "/tmp/unodctrl"; $fh = fopen($outFile, 'w');	fwrite($fh,"IO13STATE LOW");fclose($fh);usleep(1500000);io_setup($current_ionum);exit;}
	

	if(isset($_POST["HI_alert_additem"])&& isset($_POST["HI_alert_addcmd"]))
		{
			$tmpvar1 = $_POST["HI_alert_addcmd"];
			if($tmpvar1!="(null)")
				{
					$HI_alert_cmds = $HI_alert_cmds . $tmpvar1 . ".";
				}
			io_setup($current_ionum);
			exit;	
		}
	
	if(isset($_POST["HI_alert_delitem"])&& isset($_POST["HI_alert_delcmd"]))
		{
			$tmpvar1 = $_POST["HI_alert_delcmd"];
			$hsc=explode(".", $HI_alert_cmds);
			$hsc_cnt = count($hsc);
			if($hsc_cnt > 1)
				{
					unset($hsc[$tmpvar1]);
					$HI_alert_cmds = implode(".",$hsc);	
				}
			io_setup($current_ionum);
			exit;	
		}
	
	if(isset($_POST["HI_script_additem"])&& isset($_POST["HI_script_addcmd"]))
		{
			$tmpvar1 = $_POST["HI_script_addcmd"];
			if($tmpvar1!="(null)")
				{
					$HI_script_cmds = $HI_script_cmds . $tmpvar1 . ".";
				}
			io_setup($current_ionum);
			exit;	
		}
	
	
	if(isset($_POST["HI_script_delitem"])&& isset($_POST["HI_script_delcmd"]))
		{
			$tmpvar1 = $_POST["HI_script_delcmd"];
			$hsc=explode(".", $HI_script_cmds);
			$hsc_cnt = count($hsc);
			if($hsc_cnt > 1)
				{
					unset($hsc[$tmpvar1]);
					$HI_script_cmds = implode(".",$hsc);	
				}
			io_setup($current_ionum);
			exit;	
		}
	
	
	if(isset($_POST["LO_alert_additem"])&& isset($_POST["LO_alert_addcmd"]))
		{
			$tmpvar1 = $_POST["LO_alert_addcmd"];
			if($tmpvar1!="(null)")
				{
					$LO_alert_cmds = $LO_alert_cmds . $tmpvar1 . ".";
				}
			io_setup($current_ionum);
			exit;	
		}
	
	if(isset($_POST["LO_alert_delitem"])&& isset($_POST["LO_alert_delcmd"]))
		{
			$tmpvar1 = $_POST["LO_alert_delcmd"];
			$hsc=explode(".", $LO_alert_cmds);
			$hsc_cnt = count($hsc);
			if($hsc_cnt > 1)
				{
					unset($hsc[$tmpvar1]);
					$LO_alert_cmds = implode(".",$hsc);	
				}
			io_setup($current_ionum);
			exit;	
		}
	
	
	if(isset($_POST["LO_script_additem"])&& isset($_POST["LO_script_addcmd"]))
		{
			$tmpvar1 = $_POST["LO_script_addcmd"];
			if($tmpvar1!="(null)")
				{
					$LO_script_cmds = $LO_script_cmds . $tmpvar1 . ".";
				}
			io_setup($current_ionum);
			exit;	
		}
	
	
	if(isset($_POST["LO_script_delitem"])&& isset($_POST["LO_script_delcmd"]))
		{
			$tmpvar1 = $_POST["LO_script_delcmd"];
			$hsc=explode(".", $LO_script_cmds);
			$hsc_cnt = count($hsc);
			if($hsc_cnt > 1)
				{
					unset($hsc[$tmpvar1]);
					$LO_script_cmds = implode(".",$hsc);	
				}
			io_setup($current_ionum);
			exit;	
		}
	
	
	
	
	
	
	
	
	io_setup($current_ionum);

}





function io_setup($num)
{
	global $name,$notes,$en,$HI_alert_cmds,$LO_alert_cmds,$HI_script_cmds,$LO_script_cmds,$hi_flap,$lo_flap,$dos,$RunHiIoFile,$RunLowIoFile,$iodir,$iostate,$pullup;
	
	html_header();
	printf("<div class=\"formArea\"><fieldset><legend>Arduino UNO GPIO %d Setup</legend>",$num);
	//printf("SHAZ: %s",shaz);
	echo"<table width='100%' cellspacing='0' cellpadding='0' border='0'>";
	echo"<tr>";
	echo"<td>";
	echo"<table class='formFields' cellspacing='0' width='100%'>";
	echo"<tr>";
	echo"<td class='name'><label for='fid-cname'><font color='blue'>GPIO name (25 chars max):</font></td>";
	echo"<td>";
	printf("<input type='text' name='name' id='fid-pname' value='%s",$name);	
	echo"' size='30' maxlength='25'></td></tr><tr><td class='name'><label for='fid-pname'>";
	echo"<font color='green'><b>Enter Notes about this GPIO:<br>(512 chars max)</b></font></td><td>";
	echo"<textarea rows='5' name='notes' id='fid-pname'>";
	echo $notes;
	echo"</textarea></td></tr>";
	echo"</table></div>";
	
	if (file_exists("/var/rmsdata/unodat")) 
		{
			$file = fopen("/var/rmsdata/unodat","r");
			$unodatraw = fgets($file);
			fclose($file);
			$io = explode("|", $unodatraw);
			if($num==2){$x=1; $y=2;}
			if($num==3){$x=3; $y=4;}		
			if($num==4){$x=5; $y=6;}	
			if($num==5){$x=7; $y=8;}
			if($num==6){$x=9; $y=10;}
			if($num==7){$x=11; $y=12;}
			if($num==8){$x=13; $y=14;}
			if($num==9){$x=15; $y=16;}
			if($num==10){$x=17; $y=18;}
			if($num==11){$x=19; $y=20;}
			if($num==12){$x=21; $y=22;}
			if($num==13){$x=23; $y=24;}
			
			if($io[$x]=="o"){$input=" ";$output="checked";$pullup_pin=" ";$iodir="O";}
			if($io[$x]=="I"){$input="checked";$output=" ";$pullup_pin=" ";$iodir="I";}
			if($io[$x]=="Ip"){$input="checked";$output=" ";$pullup_pin="checked";$iodir="I";}
			if($io[$y]=="0"){$iostate="LOW";}else{$iostate="HIGH";}
			
			if($iodir=="I")
				{
//					echo"<div class='formArea'><table class='formFields' cellspacing='0' width='100%'>\n";
//					if($suppressed=="on")
//							{
//								$suppressed="CHECKED";
//							}			
//					echo"<tr><td class='name'><font color='blue'>Suppress Trigger Actions on Boot?</font></td>";
//					printf("<td><input type='checkbox' name='suppress' %s></font></td></tr>\n", $suppressed);
//					echo"</table>";
					
					echo"<br>";
					printf("<font color='blue'><b><input type='radio' name='group2' value='output' %s>This I/O pin is an Output &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;",$output);
					printf("<input type='radio' name='group2' value='input' %s> This I/O pin is an Input</b></font>",$input);
					echo"<br><br>";
					printf("<font color='blue'><b>Enable pull-up resistor?</b></font><input type='checkbox' name='pullup' %s>",$pullup_pin);
					
					echo"<br>";
					echo"<br>";
					
					if($iostate=="LOW")
						{
							printf("<font color='blue'><b>Input state:</b></font>&nbsp;&nbsp;&nbsp;&nbsp;<font color='red'><b>%s</b></font>",$iostate);
						}
					else
						{
							printf("<font color='blue'><b>Input state:</b></font>&nbsp;&nbsp;&nbsp;&nbsp;<font color='green'><b>%s</b></font>",$iostate);
						}
					echo"<br>";
					echo"<br>";
					
					printf("<input type='submit' name='gxioapply%d' class='commonButtonsd_ok' onMouseOver='mouse_move(&#039;b_apply&#039;);' onMouseOut='mouse_move();' value='Apply'>\n",$num);
					
					printf("<input type='submit' name='gxiosetup%d' class='commonButtonsd_ok' onMouseOver='mouse_move(&#039;b_ok&#039;);' onMouseOut='mouse_move();' value='OK'>\n",$num);	
					
					echo"<input type='submit' name='uno_cancel' class='commonButtonsd_cancel' onMouseOver='mouse_move(&#039;b_cancel&#039;);' onMouseOut='mouse_move();' value='Cancel'>";
					
					echo"<br>";
					echo"<br>";
					echo"<br>";
					
					//triggers(type, num, en, lo_flap, hi_flap, HI_alert_cmds, LO_alert_cmds, HI_script_cmds, LO_script_cmds, RunHiIoFile, RunLowIoFile);
					
					
					
					echo"<fieldset><legend>GPIO Triggers</legend>";
 					echo"<table class='1formFields' cellspacing='0' width='100%' border='1'>";
					echo"<tr class='oddrowbg'>";
					echo"<th><p align='center'>Enabled</p></th>";					
					echo"<th STYLE='background-color:  #FF4040'><p align='center'>GPIO High Trigger</p></th>";
					echo"<th STYLE='background-color:  #40FF40'><p align='center'>GPIO Low Trigger</p></th>";				
					echo"</tr><tr class='oddrowbg'>";
					echo"<td><p align='center'><input type='checkbox' name='en' ";
					if($en=="1")	{	echo"checked";	}
					echo"></p></td>";
					echo"<td><p align=\"center\">These events will fire when GPIO pin is a logic high.<br>";
		
					echo"<br>Execute Actions Below Every: &nbsp;<select name='hi_flap'>";
					echo"<option value='0'>One Shot</option>\n";
					$ii=1;	if($hi_flap==ii) {$chan="selected";} else {$chan=" ";} printf ("<option %s value='%d'>%d Second</option>\n",$chan, $ii, $ii);	
					for($ii=2; $ii<60; $ii++)	{	if($hi_flap==$ii) {$chan="selected";} else {$chan=" ";} printf ("<option %s value='%d'>%d Seconds</option>\n",$chan, $ii, $ii);	}
					$ii=1; if($hi_flap==($ii*60)) {$chan="selected";} else {$chan=" ";} printf ("<option %s value='%d'>%d Minute</option>\n",$chan, ($ii*60), $ii);
					for($ii=2; $ii<60; $ii++)	{	if($hi_flap==($ii*60)) {$chan="selected";} else {$chan=" ";} printf ("<option %s value='%d'>%d Minutes</option>\n",$chan, ($ii*60), $ii);	}
					$ii=1;	if($hi_flap==($ii*60*60)) {$chan="selected";} else {$chan=" ";} printf ("<option %s value='%d'>%d Hour</option>\n",$chan, ($ii*60*60), $ii);
					for($ii=2; $ii<25; $ii++)	{ if($hi_flap==($ii*60*60)) {$chan="selected";} else {$chan=" ";} printf ("<option %s value='%d'>%d Hours</option>\n",$chan, ($ii*60*60), $ii);	}
					echo"</select> &nbsp;when triggered.</p></td>";
					echo"<td><p align='center'>These events will fire when GPIO pin is a logic low.<br>";
		
					echo"<br>Execute Actions Below Every: &nbsp;<select name='lo_flap'>";
					echo"<option value='0'>One Shot</option>\n"; 
					$ii=1;	if($lo_flap==$ii) {$chan="selected";} else {$chan=" ";} printf ("<option %s value='%d'>%d Second</option>\n",$chan, $ii, $ii);	
					for($ii=2; $ii<60; $ii++)	{	if($lo_flap==$ii) {$chan="selected";} else {$chan=" ";} printf ("<option %s value='%d'>%d Seconds</option>\n",$chan, $ii, $ii);	}
					$ii=1; if($lo_flap==($ii*60)) {$chan="selected";} else {$chan=" ";} printf ("<option %s value='%d'>%d Minute</option>\n",$chan, ($ii*60), $ii);
					for($ii=2; $ii<60; $ii++)	{	if($lo_flap==($ii*60)) {$chan="selected";} else {$chan=" ";} printf ("<option %s value='%d'>%d Minutes</option>\n",$chan, ($ii*60), $ii);	}
					$ii=1;	if($lo_flap==($ii*60*60)) {$chan="selected";} else {$chan=" ";} printf ("<option %s value='%d'>%d Hour</option>\n",$chan, ($ii*60*60), $ii);
					for($ii=2; $ii<25; $ii++)	{ if($lo_flap==($ii*60*60)) {$chan="selected";} else {$chan=" ";} printf ("<option %s value='%d'>%d Hours</option>\n",$chan, ($ii*60*60), $ii);	}
	
					echo"</select> &nbsp;when triggered.";
		
					echo"</p></td></tr><tr class='evenrowbg'>";
					echo"<td><a name='#hi1'></a><br><center><a href='/setup.cgi?doit=alerts'><b>Alerts</b></a></center></td><td>";
					selectbox("HI", "alert", $HI_alert_cmds);
					echo"</td><td>";
					selectbox("LO", "alert", $LO_alert_cmds);
					echo"</td></tr><tr class='oddrowbg'>";
					echo"<td><a href='#hi2'></a><br><center><a href='/setup.cgi?scripts=do'><b>Scripts</b></a></center></td><td>";
					selectbox("HI", "script", $HI_script_cmds);
					echo"</td><td>";
					selectbox("LO", "script", $LO_script_cmds);
					echo"</td></tr>";
					echo"</td></tr>";
	
					echo"<tr class='evenrowbg'>";
					echo"<td><a href='#hi3'></a><br><center><a href='/editcgi.cgi?file=&'><b>File</b></a></center><br></td>";
	
					echo"<td class='name'><br><center><font color='blue'>Execute File:&nbsp;&nbsp;&nbsp;</font><input type='text' name='runhiiofile' ";
					printf("size='30' maxlength='100' value='%s'></center></td>\n", $RunHiIoFile);
	
					echo"<td class='name'><br><center><font color='blue'>Execute File:&nbsp;&nbsp;&nbsp;</font><input type='text' name='runlowiofile' ";
					printf("size='30' maxlength='100' value='%s'></center></td>\n", $RunLowIoFile);
					echo"</tr>";
	
					echo"</table></fieldset>\n";
					// Trigger END		

					printf("<input type='hidden' name='current_HI_alert_cmds' value='%s'>\n", $HI_alert_cmds);
					printf("<input type='hidden' name='current_HI_script_cmds' value='%s'>\n", $HI_script_cmds);
					printf("<input type='hidden' name='current_LO_alert_cmds' value='%s'>\n", $LO_alert_cmds);
					printf("<input type='hidden' name='current_LO_script_cmds' value='%s'>\n", $LO_script_cmds);
					printf("<br><input type='hidden' name='current_ionum' value='%d'>\n", $num);
				}
			
			else
				{
					echo"<br>";
					printf("<font color='blue'><b><input type='radio' name='group2' value='output' %s>This I/O pin is an Output &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;",$output);
					printf("<input type='radio' name='group2' value='input' %s> This I/O pin is an Input</b></font>",$input);
					echo"<br>";
					echo"<br>";
					
					printf("<input type='submit' name='gxioapply%d' class='commonButtonsd_ok' onMouseOver='mouse_move(&#039;b_apply&#039;);' onMouseOut='mouse_move();' value='Apply'>\n",$num);
				
					printf("<input type='submit' name='gxiosetup%d' class='commonButtonsd_ok' onMouseOver='mouse_move(&#039;b_ok&#039;);' onMouseOut='mouse_move();' value='OK'>\n",$num);	
					
					echo"<input type='submit' name='uno_cancel' class='commonButtonsd_cancel' onMouseOver='mouse_move(&#039;b_cancel&#039;);' onMouseOut='mouse_move();' value='Cancel'>";
					
					echo"<br>";
					echo"<br>";
					echo"<br>";
					echo"<br>";
				
					if($iostate=="LOW")
						{
							printf("<font color='blue'><b>Output state:</b></font>&nbsp;&nbsp;&nbsp;&nbsp;<font color='red'><b>%s</b></font>",$iostate);
						}
					else
						{
							printf("<font color='blue'><b>Output state:</b></font>&nbsp;&nbsp;&nbsp;&nbsp;<font color='green'><b>%s</b></font>",$iostate);
						}
				
					echo"&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
		  	
					if($iostate=="LOW")
						{
							printf("<input type='submit' name='gxio_set_hi%d' class='commonButtonsd_ok' onMouseOver='mouse_move(&#039;b_refresh&#039;);' onMouseOut='mouse_move();' value='Set High'>\n",$num);
						}
					else
						{
							printf("<input type='submit' name='gxio_set_low%d' class='commonButtonsd_ok' onMouseOver='mouse_move(&#039;b_refresh&#039;);' onMouseOut='mouse_move();' value='Set Low'>\n",$num);
						}
				
					echo"<input type='submit' name='gxio_refresh' class='commonButtonsd_ok' onMouseOver='mouse_move(&#039;b_refresh&#039;);' onMouseOut='mouse_move();' value='Refresh'>\n";
				
					echo"<br>";
					echo"<br>";
					printf("<br><input type='hidden' name='current_ionum' value='%d'>\n", $num);
				}
			
			
			echo"</div>";
			
		}//end if file exists
	else
		{
			echo"<H1><center>UNODAT file not found!</center></H1>";
			html_footer();
			return;
		}
	

	
	
	
	
	
	
	
	
	
	html_footer();
}



function io_displaypage()
{
	
	$dbh = new PDO('sqlite:/etc/rms100.db');
	$result  = $dbh->query("SELECT * FROM uno_io WHERE id='2';");
	foreach($result as $row)
	{
		$uno_name2 = $row['name'];
		$uno_notes2 = $row['notes'];
	}
	
	$result  = $dbh->query("SELECT * FROM uno_io WHERE id='3';");
	foreach($result as $row)
	{
		$uno_name3 = $row['name'];
		$uno_notes3 = $row['notes'];
	}
	
	$result  = $dbh->query("SELECT * FROM uno_io WHERE id='4';");
	foreach($result as $row)
	{
		$uno_name4 = $row['name'];
		$uno_notes4 = $row['notes'];
	}
	
	$result  = $dbh->query("SELECT * FROM uno_io WHERE id='5';");
	foreach($result as $row)
	{
		$uno_name5 = $row['name'];
		$uno_notes5 = $row['notes'];
	}
	
	$result  = $dbh->query("SELECT * FROM uno_io WHERE id='6';");
	foreach($result as $row)
	{
		$uno_name6 = $row['name'];
		$uno_notes6 = $row['notes'];
	}
	
	$result  = $dbh->query("SELECT * FROM uno_io WHERE id='7';");
	foreach($result as $row)
	{
		$uno_name7 = $row['name'];
		$uno_notes7 = $row['notes'];
	}
	
	$result  = $dbh->query("SELECT * FROM uno_io WHERE id='8';");
	foreach($result as $row)
	{
		$uno_name8 = $row['name'];
		$uno_notes8 = $row['notes'];
	}
	
	$result  = $dbh->query("SELECT * FROM uno_io WHERE id='9';");
	foreach($result as $row)
	{
		$uno_name9 = $row['name'];
		$uno_notes9 = $row['notes'];
	}
	
	$result  = $dbh->query("SELECT * FROM uno_io WHERE id='10';");
	foreach($result as $row)
	{
		$uno_name10 = $row['name'];
		$uno_notes10 = $row['notes'];
	}
	
	$result  = $dbh->query("SELECT * FROM uno_io WHERE id='11';");
	foreach($result as $row)
	{
		$uno_name11 = $row['name'];
		$uno_notes11 = $row['notes'];
	}
	
	$result  = $dbh->query("SELECT * FROM uno_io WHERE id='12';");
	foreach($result as $row)
	{
		$uno_name12 = $row['name'];
		$uno_notes12 = $row['notes'];
	}
	
	$result  = $dbh->query("SELECT * FROM uno_io WHERE id='13';");
	foreach($result as $row)
	{
		$uno_name13 = $row['name'];
		$uno_notes13 = $row['notes'];
	}
	
	$dbh = NULL;
	
	
	html_header();
	
	
	echo"<div class=\"toolsArea\"><fieldset><legend>Arduino UNO I/O Extender</legend>\n";		
	echo"<br><br>\n";		
	
	$pid_file = "/var/run/rmsunod.pid";
	if (file_exists($pid_file))
	{
		$daemon_status = "on";
		$daemon_running = "Running";
	}
	else
	{
		$daemon_status = "off";
		$daemon_running = "Not Running";
	}
	
	echo"<img src='../skins/winxp.new.compact/images/arduino32x32.gif'>";
	echo"<br>";
	echo"<font color='blue'>Start or Stop the RMS UNO daemon in the <a href='/setup.cgi?svc_mgr'>service manager.</a></font>";
	echo"&nbsp;&nbsp;&nbsp;<img src='../skins/winxp.new.compact/images/serv" . $daemon_status . ".gif' width='16' height='16'>&nbsp;&nbsp;&nbsp;RMS UNO service " .  $daemon_running . ".<br><br>\n";
	echo"</table></fieldset></div>\n\n";
	
	echo"<div class='toolsArea'>";
	echo"<fieldset><legend>UNO GPIO Overview</legend><table border='0' cellspacing='0' width='80%'>";
	
	echo"<tr>";
	echo"<th colspan='7'><center>User Defined I/O Pins (5.0 volts max)</center></th>";
	echo"</tr>";
	
	
	//GPIO 2
	echo"<tr>";
	echo"<td width='10%' align='center'>";
	printf("<span onMouseOver=\"this.T_TITLE='GPIO 2 - %s';return escape('%s');\"  onMouseOut='mouse_move();'>",$uno_name2,$uno_notes2);
	echo"<a href='/uno.php?gxiosetup=2' style='text-decoration: none'><font color='blue'><b>GPIO 2</b></font></a>";
	echo"</span></td>";
	//GPIO 3
	echo"<td width='10%' align='center'>";
	printf("<span onMouseOver=\"this.T_TITLE='GPIO 3 - %s';return escape('%s');\"  onMouseOut='mouse_move();'>",$uno_name3,$uno_notes3);
	echo"<a href='/uno.php?gxiosetup=3' style='text-decoration: none'><font color='blue'><b>GPIO 3</b></font></a>";
	echo"</span></td>";
	//GPIO 4
	echo"<td width='10%' align='center'>";
	printf("<span onMouseOver=\"this.T_TITLE='GPIO 4 - %s';return escape('%s');\"  onMouseOut='mouse_move();'>",$uno_name4,$uno_notes4);
	echo"<a href='/uno.php?gxiosetup=4' style='text-decoration: none'><font color='blue'><b>GPIO 4</b></font></a>";
	echo"</span></td>";
	//GPIO 5
	echo"<td width='10%' align='center'>";
	printf("<span onMouseOver=\"this.T_TITLE='GPIO 5 - %s';return escape('%s');\"  onMouseOut='mouse_move();'>",$uno_name5,$uno_notes5);
	echo"<a href='/uno.php?gxiosetup=5' style='text-decoration: none'><font color='blue'><b>GPIO 5</b></font></a>";
	echo"</span></td>";
	//GPIO 6
	echo"<td width='10%' align='center'>";
	printf("<span onMouseOver=\"this.T_TITLE='GPIO 6 - %s';return escape('%s');\"  onMouseOut='mouse_move();'>",$uno_name6,$uno_notes6);
	echo"<a href='/uno.php?gxiosetup=6' style='text-decoration: none'><font color='blue'><b>GPIO 6</b></font></a>";
	echo"</span></td>";
	//GPIO 7
	echo"<td width='10%' align='center'>";
	printf("<span onMouseOver=\"this.T_TITLE='GPIO 7 - %s';return escape('%s');\"  onMouseOut='mouse_move();'>",$uno_name7,$uno_notes7);
	echo"<a href='/uno.php?gxiosetup=7' style='text-decoration: none'><font color='blue'><b>GPIO 7</b></font></a>";
	echo"</span></td>";
	echo"</tr>";
	
	echo"<tr>";
	$file = fopen("/var/rmsdata/unodat","r");
	$raw = fgets($file);
	fclose($file);
	$myio = explode("|", $raw);
	
	for($mynum=2;$mynum<8;$mynum++)
	 {
		if($mynum==2){$x=1; $y=2;}
		if($mynum==3){$x=3; $y=4;}		
		if($mynum==4){$x=5; $y=6;}	
		if($mynum==5){$x=7; $y=8;}
		if($mynum==6){$x=9; $y=10;}
		if($mynum==7){$x=11; $y=12;}
		if($myio[$y]=="0"){$iostate="low";}else{$iostate="high";}
		printf("<td align='center'><a href='uno.php?gxiosetup=%d'>",$mynum);
		printf("<div id='gpio%d'><img src='../skins/winxp.new.compact/images/io_%s.gif' alt=''></div>",$mynum,$iostate);
		echo"</a></td>";				
	 }
	echo"</tr>";
	echo"<tr>";
	for($mynum=2;$mynum<8;$mynum++)
	 {
	 		if($mynum==2){$myname = $uno_name2;$mynotes = $uno_notes2;}
	 		if($mynum==3){$myname = $uno_name3;$mynotes = $uno_notes3;}
	 		if($mynum==4){$myname = $uno_name4;$mynotes = $uno_notes4;}
	 		if($mynum==5){$myname = $uno_name5;$mynotes = $uno_notes5;}
	 		if($mynum==6){$myname = $uno_name6;$mynotes = $uno_notes6;}
	 		if($mynum==7){$myname = $uno_name7;$mynotes = $uno_notes7;}	 		
	 		printf("<td align='center'><span  onMouseOver=\"this.T_TITLE='GPIO %d - %s';return escape('%s');\"  onMouseOut='mouse_move();'>",$mynum,$myname,$mynotes);
			printf("<a href='/uno.php?gxiosetup=%d' style='text-decoration: none'><font color=\"#2C60D1\">%s</font></a>",$mynum,$myname);
			echo"</span></td>";	
	 }
	echo"</tr>";
	echo"<tr>";
	for($mynum=2;$mynum<8;$mynum++)
	 {
	 		if($mynum==2){$x=1; $y=2;}
			if($mynum==3){$x=3; $y=4;}		
			if($mynum==4){$x=5; $y=6;}	
			if($mynum==5){$x=7; $y=8;}
			if($mynum==6){$x=9; $y=10;}
			if($mynum==7){$x=11; $y=12;}
			if($myio[$x]=="o"){$iodir="OUTPUT";}
			if($myio[$x]=="I"){$iodir="INPUT";}
			if($myio[$x]=="Ip"){$iodir="INPUT(p)";}
			if($myio[$y]=="0"){$iostate="LOW";$iocolor='RED';}else{$iostate="HIGH";$iocolor='GREEN';}
	
	 		echo"<td align=\"center\">";
	 		printf("<div id='gpio%dstate'><font color='blue'><b>%s: </b></font><font color='%s'>%s</font></div>",$mynum,$iodir,$iocolor,$iostate);
	 		echo"</td>";	 		
	 }
	
	echo"</tr></table>";
	
	echo"<br><br>";
	
	echo"<table border='0' cellspacing='0' width='80%'>";
	echo"<tr>";
	//GPIO 8
	echo"<td width='10%' align='center'>";
	printf("<span onMouseOver=\"this.T_TITLE='GPIO 8 - %s';return escape('%s');\"  onMouseOut='mouse_move();'>",$uno_name8,$uno_notes8);
	echo"<a href='/uno.php?gxiosetup=8' style='text-decoration: none'><font color='blue'><b>GPIO 8</b></font></a>";
	echo"</span></td>";
	//GPIO 9
	echo"<td width='10%' align='center'>";
	printf("<span onMouseOver=\"this.T_TITLE='GPIO 9 - %s';return escape('%s');\"  onMouseOut='mouse_move();'>",$uno_name9,$uno_notes9);
	echo"<a href='/uno.php?gxiosetup=9' style='text-decoration: none'><font color='blue'><b>GPIO 9</b></font></a>";
	echo"</span></td>";
	//GPIO 10
	echo"<td width='10%' align='center'>";
	printf("<span onMouseOver=\"this.T_TITLE='GPIO 10 - %s';return escape('%s');\"  onMouseOut='mouse_move();'>",$uno_name10,$uno_notes10);
	echo"<a href='/uno.php?gxiosetup=10' style='text-decoration: none'><font color='blue'><b>GPIO 10</b></font></a>";
	echo"</span></td>";
	//GPIO 11
	echo"<td width='10%' align='center'>";
	printf("<span onMouseOver=\"this.T_TITLE='GPIO 11 - %s';return escape('%s');\"  onMouseOut='mouse_move();'>",$uno_name11,$uno_notes11);
	echo"<a href='/uno.php?gxiosetup=11' style='text-decoration: none'><font color='blue'><b>GPIO 11</b></font></a>";
	echo"</span></td>";
	//GPIO 12
	echo"<td width='10%' align='center'>";
	printf("<span onMouseOver=\"this.T_TITLE='GPIO 12 - %s';return escape('%s');\"  onMouseOut='mouse_move();'>",$uno_name12,$uno_notes12);
	echo"<a href='/uno.php?gxiosetup=12' style='text-decoration: none'><font color='blue'><b>GPIO 12</b></font></a>";
	echo"</span></td>";
	//GPIO 13
	echo"<td width='10%' align='center'>";
	printf("<span onMouseOver=\"this.T_TITLE='GPIO 13 - %s';return escape('%s');\"  onMouseOut='mouse_move();'>",$uno_name13,$uno_notes13);
	echo"<a href='/uno.php?gxiosetup=13' style='text-decoration: none'><font color='blue'><b>GPIO 13</b></font></a>";
	echo"</span></td>";
	echo"</tr>";
	echo"<tr>";
	for($mynum=8;$mynum<14;$mynum++)
	 {
		if($mynum==8){$x=13; $y=14;}
		if($mynum==9){$x=15; $y=16;}		
		if($mynum==10){$x=17; $y=18;}	
		if($mynum==11){$x=19; $y=20;}
		if($mynum==12){$x=21; $y=22;}
		if($mynum==13){$x=23; $y=24;}
		if($myio[$y]=="0"){$iostate="low";}else{$iostate="high";}
		printf("<td align='center'><a href='uno.php?gxiosetup=%d'>",$mynum);
		printf("<div id='gpio%d'><img src='../skins/winxp.new.compact/images/io_%s.gif' alt=''></div>",$mynum,$iostate);
		echo"</a></td>";				
	 }
	echo"</tr>";
	echo"<tr>";
	for($mynum=8;$mynum<14;$mynum++)
	 {
	 		if($mynum==8){$myname = $uno_name8;$mynotes = $uno_notes8;}
	 		if($mynum==9){$myname = $uno_name9;$mynotes = $uno_notes9;}
	 		if($mynum==10){$myname = $uno_name10;$mynotes = $uno_notes10;}
	 		if($mynum==11){$myname = $uno_name11;$mynotes = $uno_notes11;}
	 		if($mynum==12){$myname = $uno_name12;$mynotes = $uno_notes12;}
	 		if($mynum==13){$myname = $uno_name13;$mynotes = $uno_notes13;}	 		
	 		printf("<td align='center'><span  onMouseOver=\"this.T_TITLE='GPIO %d - %s';return escape('%s');\"  onMouseOut='mouse_move();'>",$mynum,$myname,$mynotes);
			printf("<a href='/uno.php?gxiosetup=%d' style='text-decoration: none'><font color=\"#2C60D1\">%s</font></a>",$mynum,$myname);
			echo"</span></td>";	
	 }
	echo"</tr>";
	echo"<tr>";
	for($mynum=8;$mynum<14;$mynum++)
	 {
	 		if($mynum==8){$x=13; $y=14;}
			if($mynum==9){$x=15; $y=16;}		
			if($mynum==10){$x=17; $y=18;}	
			if($mynum==11){$x=19; $y=20;}
			if($mynum==12){$x=21; $y=22;}
			if($mynum==13){$x=23; $y=24;}
			if($myio[$x]=="o"){$iodir="OUTPUT";}
			if($myio[$x]=="I"){$iodir="INPUT";}
			if($myio[$x]=="Ip"){$iodir="INPUT(p)";}
			if($myio[$y]=="0"){$iostate="LOW";$iocolor='RED';}else{$iostate="HIGH";$iocolor='GREEN';}
	
	 		echo"<td align=\"center\">";
	 		printf("<div id='gpio%dstate'><font color='blue'><b>%s: </b></font><font color='%s'>%s</font></div>",$mynum,$iodir,$iocolor,$iostate);
	 		echo"</td>";	 		
	 }
	echo"</tr></table></fieldset></div>";
	echo"<br><br><br>";
	echo"<input type='submit' name='main_refresh' class='commonButtonsd_ok' onMouseOver='mouse_move(&#039;b_refresh&#039;);' onMouseOut='mouse_move();' value='Refresh'>\n";
	
//	$sd1=1;
//	$dbh = new PDO('sqlite:/etc/rms100.db');
//	sprintf($tmpsql, "SELECT * FROM scripts where id = '1';");
//							$result  = $dbh->query("SELECT * FROM scripts where id = '" . $sd1 . "';");
//							$row = $result->fetch();
//							$sbuf1 = $row['type'];
//	foreach($result as $row)
//	{
//		$sbuf1 = $row['type'];
//		echo $sbuf1;
//	}						
//	$dbh=NULL;
	
	html_footer();

}

// **************************
// *                        *
// *    Functions Below     *
// *                        *
// **************************

function html_header()
	{
		echo "<!DOCTYPE HTML PUBLIC '-//W3C//DTD HTML 4.0 Transitional//EN' 'http://www.w3.org/TR/html4/loose.dtd'>\n";
		echo "<html>\n";
		echo "<head>\n";
		echo "<title>GPS Data</title>\n";
		echo "<style TYPE='text/css'>\n";
		echo "img {  border-style: none; }   \n";
		echo "\n";
		echo "\n";
		echo "</style>\n";
		echo "<meta http-equiv='Content-Type' content='text/html; charset=utf-8'>\n";
		echo "<META HTTP-EQUIV='Pragma' CONTENT='no-cache'>\n";
		echo "<META HTTP-EQUIV='CACHE-CONTROL' CONTENT='NO-CACHE'>\n";
		echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"/skins/winxp.new.compact/css/general.css\">\n";
		echo "<link rel=\"stylesheet\" type=\"text/nonsense\" href=\"/skins/winxp.new.compact/css/misc.css\">\n";
		echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"/skins/winxp.new.compact/css/main/custom.css\">\n";
		echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"/skins/winxp.new.compact/css/main/layout.css\">\n\n";
		echo "<script language='javascript' type='text/javascript' src='/javascript/common.js?'></script>\n";
		echo "<script language='javascript' type='text/javascript' src='/javascript/jQuery.js?'></script>\n";
		echo "<link rel='stylesheet' type='text/css' href='/skins/winxp.new.compact/css/jQuery-ui.css'>\n";
		echo "<script language='javascript' type='text/javascript' src='/javascript/jQuery-ui.js?'></script>\n";
		echo "<script language='javascript' type='text/javascript'>\n";
		echo "function _body_onload()\n";
		echo " {\n";
		echo " loff();\n";
		echo " SetContext('gps');\n";
		echo " }\n\n";
		
		echo "function _body_onunload()\n";
		echo " {\n";
		echo " lon();\n";
		echo " }\n\n";
		echo "var opt_no_frames = false;\n";
		echo "var opt_integrated_mode = false;\n";
		echo "</script>\n";
		
		echo "\n";
		echo "</head>\n";
		echo"<body onLoad='_body_onload();'>";
		echo"<div class=\"screenBody\">\n";
		echo"<form name='f' action='uno.php' method='post' >\n\n";

//		echo"<script language='javascript' type='text/javascript'>\n";
//		echo"function display_info()\n";
//		echo"{\n";
//		echo"     var myRandom = parseInt(Math.random()*999999999);\n";
//		echo"     $.getJSON('uno_server.php?element=info&rand=' + myRandom,";
//		echo"     function(data)";
//		echo"      {";
//		echo"         $.each (data.unodata, function (k, v) { $('#' + k).text (v); });";
//		echo"         setTimeout (display_info, 1000);";
//		echo"         $('#gpio2').replaceWith(\"<div id='gpio2'><img src='../skins/winxp.new.compact/images/io_\" + data.unodata.io2state + \".gif' alt=''></div>\"); ";
//		echo"      }\n";
//		echo"    );\n";
//		echo"}\n";
//		echo"display_info();\n";
//		echo"</script>\n";	
	}       
	
	
	                                           

function html_footer()
	{
		echo "<script language='JavaScript' type='text/javascript' src='/javascript/wz_tooltip.js'></script><script type='text/javascript'>";
		echo "try {";
		echo "document.getElementById('loaderContainerH').height = document.getElementById('screenH').offsetHeight;";
		echo "} catch (e) {";
		echo "}";
		echo "</script>";
		echo "</div>";
		echo "</form>";
		echo "</body>\n";
		echo "</html>\n";
	}                                 



function selectbox($c1, $c2, $current_cmds)
{
// c1 = HI || HI_N || LO || LO_N
// c2 = script || alert

	if($c2=="alert") {$label="Alerts"; }
	else ($label="Scripts"); 	
	
	//result = sqlite3_open("/etc/rms100.db", &db);
	$dbh = new PDO('sqlite:/etc/rms100.db');
	printf("<div align='center'><table><tr><td align='center'><font color='blue'>Available %s</font><br><select size='5' name='%s_%s_addcmd' multiple>",$label, $c1, $c2);

	if($c2=="script")
		{ 
			$result  = $dbh->query("SELECT * FROM scripts WHERE type = 'relay' UNION SELECT * FROM scripts WHERE type = 'io' ORDER BY id"); 
		}
	else
		{
			$result  = $dbh->query("SELECT * FROM alerts ORDER BY id"); 
		}


	foreach($result as $row)
		{
			$id = $row['id'];
			$sdbuf1 = $row['type'];
			$sdbuf2 = $row['name'];
			if($sdbuf1=="relay"){$sdbuf1="RELAY";}
			if($sdbuf1=="io"){$sdbuf1="GPIO";}
			printf("<option value='%s'>%s-%s</option>\n", $id, $sdbuf1, $sdbuf2);
		}

	printf("<option value='(null)'>-------------------------------------</option></select>");
	printf("<br><p align='center'><input type='submit' name='%s_%s_additem' value='Add' ", $c1, $c2);
	echo"class='commonButtonsd_add' onMouseOver='mouse_move(&#039;b_cmd_add&#039;);' onMouseOut='mouse_move();'></p>\n";
	echo"</td>";
	echo"<td align='center'><br>&nbsp;<-->&nbsp;<br><br><br>\n";
	echo"</td>";
	printf("<td align='center'><font color='green'>Selected %s</font><br><select size='5' name='%s_%s_delcmd'>\n", $label, $c1, $c2);

// sample commands:		"2.12.7.\0x00"

	//sprintf($buf,"%s", $current_cmds);
	//$current_cmds="1.";
	
//sprintf(tmpsql, "echo 'in selectbox Curcmds: -%s-\n' >> /tmp/bumf.txt", $current_cmds);	system(tmpsql);
//printf("<option value=\"\">%s", $current_cmds);


	$id=0;
	$cc = explode(".",$current_cmds);
	$cnt=count($cc);

	if($cnt > 1)
		{
			for($ii=1; $ii < $cnt; $ii++)
				{
					printf("<option value='%d'>", $id);
					if($c2=="script") 
						{ 
							//sprintf($tmpsql, "SELECT * FROM scripts where id = '%s';", $cnt[$bid]);
							$result  = $dbh->query("SELECT * FROM scripts where id = '" . $cc[$id] . "';");
							foreach($result as $row)
								{
									$sdbuf1 = $row['type'];
									$sdbuf2 = $row['name'];
								}			
							if($sdbuf1=="relay")
								{
									$sdbuf1="RELAY";
								}
							if($sdbuf1=="io")
								{
									$sdbuf1="GPIO";
								}
							printf("%s-%s</option>",$sdbuf1,$sdbuf2);
			  		}
					elseif($c2=="alert")
						{ 
							//sprintf($tmpsql, "SELECT * FROM alerts where id = '%s';", "1");
							$result  = $dbh->query("SELECT * FROM alets where id = '" . $cnt[$id] . "';");
							foreach($result as $row)
								{
									$sdbuf1 = $row['type'];
									$sdbuf2 = $row['name'];
								}	
							
							printf("%s-%s</option>",$sdbuf1,$sdbuf2);			
			  		}
			  	else
			  		{
			  			printf("uuuu</option>");
			  		}
					$id++;				
				}
			
		}
	$dbh=NULL;
	echo"<option value='(null)'>-------------------------------------</option></select>";

	printf("<br><p align='center'><input type='submit' name='%s_%s_delitem' value='Remove' ", $c1, $c2);
	echo"class='commonButtonsd_del' onMouseOver='mouse_move(&#039;b_cmd_del&#039;);' onMouseOut='mouse_move();'></p>\n";	
	echo"</td></tr>";
	echo"</table></div>";
}


function do_buttons($io,$button)
{
	global $current_ionum,$name,$notes,$en,$HI_alert_cmds,$LO_alert_cmds,$HI_script_cmds,$LO_script_cmds,$current_hi_flap,$current_lo_flap,$current_dos,$RunHiIoFile,$RunLowIoFile,$iodir,$iostate,$pullup,$inout1,$inout2;
	
	
	if($pullup=="on"){}else{$pullup="off";}
	
	$dbh = new PDO('sqlite:/etc/rms100.db');
	$result  = $dbh->exec("UPDATE uno_io SET name='$name',notes='$notes',en='$en',HI_alert_cmds='$HI_alert_cmds',LO_alert_cmds='$LO_alert_cmds',HI_script_cmds='$HI_script_cmds',LO_script_cmds='$LO_script_cmds',hi_flap='$current_hi_flap',lo_flap='$current_lo_flap',dos='low',RunHiIoFile='$RunHiIoFile',RunLowIoFile='$RunLowIoFile',iodir='$iodir',iostate='$iostate',pullup='$pullup',glitch='NA' WHERE id='$io';");
	
	if($io=='2')
		{
			$result  = $dbh->exec("UPDATE io_script_cmds SET name='$name' WHERE command='1A';");
			$result  = $dbh->exec("UPDATE io_script_cmds SET name='$name' WHERE command='1B';");
			
			if($inout2=="output")
				{
					$outFile = "/tmp/unodctrl"; $fh = fopen($outFile, 'w');	fwrite($fh,"IO2DIR OUTPUT");fclose($fh);
				}
			else
				{
					if($pullup=="on")
						{
							$outFile = "/tmp/unodctrl"; $fh = fopen($outFile, 'w');	fwrite($fh,"IO2DIR PULLUP");fclose($fh);
						}
					else
						{
							$outFile = "/tmp/unodctrl"; $fh = fopen($outFile, 'w');	fwrite($fh,"IO2DIR INPUT");fclose($fh);
						}
				}
		}
	
	if($io=='3')
		{
			$result  = $dbh->exec("UPDATE io_script_cmds SET name='$name' WHERE command='1C';");
			$result  = $dbh->exec("UPDATE io_script_cmds SET name='$name' WHERE command='1D';");
			
			if($inout2=="output")
				{
					$outFile = "/tmp/unodctrl"; $fh = fopen($outFile, 'w');	fwrite($fh,"IO3DIR OUTPUT");fclose($fh);
				}
			else
				{
					if($pullup=="on")
						{
							$outFile = "/tmp/unodctrl"; $fh = fopen($outFile, 'w');	fwrite($fh,"IO3DIR PULLUP");fclose($fh);
						}
					else
						{
							$outFile = "/tmp/unodctrl"; $fh = fopen($outFile, 'w');	fwrite($fh,"IO3DIR INPUT");fclose($fh);
						}
				}
		}
	
	if($io=='4')
		{
			$result  = $dbh->exec("UPDATE io_script_cmds SET name='$name' WHERE command='1E';");
			$result  = $dbh->exec("UPDATE io_script_cmds SET name='$name' WHERE command='1F';");
			
			if($inout2=="output")
				{
					$outFile = "/tmp/unodctrl"; $fh = fopen($outFile, 'w');	fwrite($fh,"IO4DIR OUTPUT");fclose($fh);
				}
			else
				{
					if($pullup=="on")
						{
							$outFile = "/tmp/unodctrl"; $fh = fopen($outFile, 'w');	fwrite($fh,"IO4DIR PULLUP");fclose($fh);
						}
					else
						{
							$outFile = "/tmp/unodctrl"; $fh = fopen($outFile, 'w');	fwrite($fh,"IO4DIR INPUT");fclose($fh);
						}
				}
		}
		
	if($io=='5')
		{
			$result  = $dbh->exec("UPDATE io_script_cmds SET name='$name' WHERE command='1G';");
			$result  = $dbh->exec("UPDATE io_script_cmds SET name='$name' WHERE command='1H';");
			
			if($inout2=="output")
				{
					$outFile = "/tmp/unodctrl"; $fh = fopen($outFile, 'w');	fwrite($fh,"IO5DIR OUTPUT");fclose($fh);
				}
			else
				{
					if($pullup=="on")
						{
							$outFile = "/tmp/unodctrl"; $fh = fopen($outFile, 'w');	fwrite($fh,"IO5DIR PULLUP");fclose($fh);
						}
					else
						{
							$outFile = "/tmp/unodctrl"; $fh = fopen($outFile, 'w');	fwrite($fh,"IO5DIR INPUT");fclose($fh);
						}
				}
		}	
	
	if($io=='6')
		{
			$result  = $dbh->exec("UPDATE io_script_cmds SET name='$name' WHERE command='1I';");
			$result  = $dbh->exec("UPDATE io_script_cmds SET name='$name' WHERE command='1J';");
			
			if($inout2=="output")
				{
					$outFile = "/tmp/unodctrl"; $fh = fopen($outFile, 'w');	fwrite($fh,"IO6DIR OUTPUT");fclose($fh);
				}
			else
				{
					if($pullup=="on")
						{
							$outFile = "/tmp/unodctrl"; $fh = fopen($outFile, 'w');	fwrite($fh,"IO6DIR PULLUP");fclose($fh);
						}
					else
						{
							$outFile = "/tmp/unodctrl"; $fh = fopen($outFile, 'w');	fwrite($fh,"IO6DIR INPUT");fclose($fh);
						}
				}
		}	
	
	if($io=='7')
		{
			$result  = $dbh->exec("UPDATE io_script_cmds SET name='$name' WHERE command='1K';");
			$result  = $dbh->exec("UPDATE io_script_cmds SET name='$name' WHERE command='1L';");
			
			if($inout2=="output")
				{
					$outFile = "/tmp/unodctrl"; $fh = fopen($outFile, 'w');	fwrite($fh,"IO7DIR OUTPUT");fclose($fh);
				}
			else
				{
					if($pullup=="on")
						{
							$outFile = "/tmp/unodctrl"; $fh = fopen($outFile, 'w');	fwrite($fh,"IO7DIR PULLUP");fclose($fh);
						}
					else
						{
							$outFile = "/tmp/unodctrl"; $fh = fopen($outFile, 'w');	fwrite($fh,"IO7DIR INPUT");fclose($fh);
						}
				}
		}
	
	if($io=='8')
		{
			$result  = $dbh->exec("UPDATE io_script_cmds SET name='$name' WHERE command='1M';");
			$result  = $dbh->exec("UPDATE io_script_cmds SET name='$name' WHERE command='1N';");
			
			if($inout2=="output")
				{
					$outFile = "/tmp/unodctrl"; $fh = fopen($outFile, 'w');	fwrite($fh,"IO8DIR OUTPUT");fclose($fh);
				}
			else
				{
					if($pullup=="on")
						{
							$outFile = "/tmp/unodctrl"; $fh = fopen($outFile, 'w');	fwrite($fh,"IO8DIR PULLUP");fclose($fh);
						}
					else
						{
							$outFile = "/tmp/unodctrl"; $fh = fopen($outFile, 'w');	fwrite($fh,"IO8DIR INPUT");fclose($fh);
						}
				}
		}
	
	if($io=='9')
		{
			$result  = $dbh->exec("UPDATE io_script_cmds SET name='$name' WHERE command='1O';");
			$result  = $dbh->exec("UPDATE io_script_cmds SET name='$name' WHERE command='1P';");
			
			if($inout2=="output")
				{
					$outFile = "/tmp/unodctrl"; $fh = fopen($outFile, 'w');	fwrite($fh,"IO9DIR OUTPUT");fclose($fh);
				}
			else
				{
					if($pullup=="on")
						{
							$outFile = "/tmp/unodctrl"; $fh = fopen($outFile, 'w');	fwrite($fh,"IO9DIR PULLUP");fclose($fh);
						}
					else
						{
							$outFile = "/tmp/unodctrl"; $fh = fopen($outFile, 'w');	fwrite($fh,"IO9DIR INPUT");fclose($fh);
						}
				}
		}
	
	if($io=='10')
		{
			$result  = $dbh->exec("UPDATE io_script_cmds SET name='$name' WHERE command='1Q';");
			$result  = $dbh->exec("UPDATE io_script_cmds SET name='$name' WHERE command='1R';");
			
			if($inout2=="output")
				{
					$outFile = "/tmp/unodctrl"; $fh = fopen($outFile, 'w');	fwrite($fh,"IO10DIR OUTPUT");fclose($fh);
				}
			else
				{
					if($pullup=="on")
						{
							$outFile = "/tmp/unodctrl"; $fh = fopen($outFile, 'w');	fwrite($fh,"IO10DIR PULLUP");fclose($fh);
						}
					else
						{
							$outFile = "/tmp/unodctrl"; $fh = fopen($outFile, 'w');	fwrite($fh,"IO10DIR INPUT");fclose($fh);
						}
				}
		}
	
	if($io=='11')
		{
			$result  = $dbh->exec("UPDATE io_script_cmds SET name='$name' WHERE command='1S';");
			$result  = $dbh->exec("UPDATE io_script_cmds SET name='$name' WHERE command='1T';");
			
			if($inout2=="output")
				{
					$outFile = "/tmp/unodctrl"; $fh = fopen($outFile, 'w');	fwrite($fh,"IO11DIR OUTPUT");fclose($fh);
				}
			else
				{
					if($pullup=="on")
						{
							$outFile = "/tmp/unodctrl"; $fh = fopen($outFile, 'w');	fwrite($fh,"IO11DIR PULLUP");fclose($fh);
						}
					else
						{
							$outFile = "/tmp/unodctrl"; $fh = fopen($outFile, 'w');	fwrite($fh,"IO11DIR INPUT");fclose($fh);
						}
				}
		}
	
	if($io=='12')
		{
			$result  = $dbh->exec("UPDATE io_script_cmds SET name='$name' WHERE command='1U';");
			$result  = $dbh->exec("UPDATE io_script_cmds SET name='$name' WHERE command='1V';");
			
			if($inout2=="output")
				{
					$outFile = "/tmp/unodctrl"; $fh = fopen($outFile, 'w');	fwrite($fh,"IO12DIR OUTPUT");fclose($fh);
				}
			else
				{
					if($pullup=="on")
						{
							$outFile = "/tmp/unodctrl"; $fh = fopen($outFile, 'w');	fwrite($fh,"IO12DIR PULLUP");fclose($fh);
						}
					else
						{
							$outFile = "/tmp/unodctrl"; $fh = fopen($outFile, 'w');	fwrite($fh,"IO12DIR INPUT");fclose($fh);
						}
				}
		}
	
	if($io=='13')
		{
			$result  = $dbh->exec("UPDATE io_script_cmds SET name='$name' WHERE command='1W';");
			$result  = $dbh->exec("UPDATE io_script_cmds SET name='$name' WHERE command='1X';");
			
			if($inout2=="output")
				{
					$outFile = "/tmp/unodctrl"; $fh = fopen($outFile, 'w');	fwrite($fh,"IO13DIR OUTPUT");fclose($fh);
				}
			else
				{
					if($pullup=="on")
						{
							$outFile = "/tmp/unodctrl"; $fh = fopen($outFile, 'w');	fwrite($fh,"IO13DIR PULLUP");fclose($fh);
						}
					else
						{
							$outFile = "/tmp/unodctrl"; $fh = fopen($outFile, 'w');	fwrite($fh,"IO13DIR INPUT");fclose($fh);
						}
				}
		}
	

	$dbh = NULL;
	usleep(1500000);
	
	//Restart RMSD
	
	if (file_exists("/var/run/rmsd.pid")) 
		{
			//pid file exists so restart service
			system("/etc/init.scripts/S79rmsd stop > /dev/null");
			system("/etc/init.scripts/S79rmsd start > /dev/null");
		}
	if($button=="apply"){io_setup($current_ionum);exit;}
	if($button=="ok"){io_displaypage();exit;}
}









































?>
