<?php
    //error_reporting(E_ALL);
    date_default_timezone_set('America/New_York');
    include "LogSizeHandler.php";

    /**
     * Handles all event logging.
     */
    class Logger
    {
        const VM1 = "vm1";
        const VM2 = "vm2";
        const VM3 = "vm3";
        const ALARM1 = "alarm1";
        const ALARM2 = "alarm2";
        const ALARM3 = "alarm3";
        const ALARM4 = "alarm4";
        const ALARM5 = "alarm5";

        const VOLTMETER_TYPE = 0;
        const ALARM_TYPE = 1;

        private $device;
        private $type;
        private $optionalMsg;
        private $isOptionalMsgShowing;

        /**
         * Pass in one of the const device names
         */
        public static function LogEvent($device, $optionalMsg = "", $isOptionalMsgShowing = false)
        {
            $logger = new Logger($device, $optionalMsg, $isOptionalMsgShowing);
            $filePath = $logger->getFilePath();
            //LogSizeHandler::removeOldLogsWhenLogExceedsMaxSize($filePath);
            $logger->updateLogFile($filePath);
            $logger->writeLogFile("/mnt/usbflash/lastlog", "w");
            
        }
        private function __construct($device, $optionalMsg, $isOptionalMsgShowing)
        {
            $this->optionalMsg = $optionalMsg;
            $this->device = $device;
            //this seems redundant
            $this->isOptionalMsgShowing = $isOptionalMsgShowing;
            $this->setType($device);
        }
        private function setType($device)
        {
            if($device === Logger::VM1 || $device === Logger::VM2 ||$device === Logger::VM3)
            {
                $this->type = Logger::VOLTMETER_TYPE;
            }
            elseif($device === Logger::ALARM1 || $device === Logger::ALARM2 || $device === Logger::ALARM3
                || $device === Logger::ALARM4 || $device === Logger::ALARM5)
            {
                $this->type = LOGGER::ALARM_TYPE;
            }
            else throw new Exception('Device is not valid.');
        }
        private function getFilePath() {return "/mnt/usbflash/log/event_log_".$this->device.".log";}

        private function updateLogFile($filePath)
        {
            $fh = fopen($filePath, "a");
            fwrite($fh, $this->getLog());
            fclose($fh);
        }

        private function writeLogFile($filePath, $m)
        {
            $fh = fopen($filePath, $m);
            fwrite($fh, $this->getLog());
            fclose($fh);
        }

        /**
         * Example format:         "9.29.2019-8:45:03 PM Surge Protection Alarm Triggered"
         * Optional Msg:                 "PC Shutdown Initiated"
         */
        private function getLog() 
        {
            return 
                $this->format2Bold($this->getDateTime()).
                //$this->getStation().
                $this->getName().
                $this->getFormattedDeviceValue().
                $this->getOptionalMsg();
        }
        private function format2Bold($str) { return "<b>".$str."</b>";}
        private function getDateTime() {return date('n.d.Y')."-".date('g:i:s A')." ";}
        private function getStation() {return trim(file_get_contents("/etc/hostname")).", ";}
        private function getName() { return $this->queryDbForDeviceName();}
        private function getFormattedDeviceValue() 
        {
            $value = trim(file_get_contents("/var/rmsdata/".$this->device));
            if($this->type === Logger::ALARM_TYPE)
            {
                $triggerNames = $this->queryDbForTriggerNames();
                if($this->device == Logger::ALARM4) {
                    //return "Event ".$this->format2Bold(($value == 1? $triggerNames[0] : $triggerNames[1])."\n");
                }
                return "Event ".$this->format2Bold(($value == 1? $triggerNames[1] : $triggerNames[0])."\n");
            }
            return "Voltage at ".$this->format2Bold($value."v\n");
        }
        private function getOptionalMsg()
        {
            if($this->isOptionalMsgShowing)
            {
                if(empty($this->optionalMsg))
                {
                    return "";
                } 
                return "\t".$this->optionalMsg."\n";
            }
            return "";
        }

        private function queryDbForDeviceName()
        {
            $id = $this->getQueryId();
            $dbh = $this->getDb();
            if($this->type === Logger::ALARM_TYPE)
            {
                $result  = $dbh->query("SELECT * FROM io WHERE id='".$id."' AND type='alarm';");
            }
            else 
            {
	            $result  = $dbh->query("SELECT * FROM voltmeters WHERE id='".$id."';");			
            }
            foreach ($result as $row) { return $row['name']." "; }
            return "";
        }
        private function queryDbForTriggerNames()
        {
            $id = $this->getQueryId();
            $dbh = $this->getDb();
            $query = sprintf("SELECT * FROM alarm_options WHERE id = '%d'", $id);
            $result  = $dbh->query($query);
            foreach($result as $row)
            {	
                $lo_state_name = $row['lo_state_name'];
                $hi_state_name = $row['hi_state_name']; 					
            }
            if(is_null($lo_state_name)){$lo_state_name = "NORMAL";}
            if(is_null($hi_state_name)){$hi_state_name = "FAULT";}

            return array($lo_state_name, $hi_state_name);
        }
        private function getQueryId() { return $this->device[strlen($this->device)-1];}
        private function getDb() {return new PDO('sqlite:/etc/rms100.db');}
    }

?>