<?php
    //error_reporting(E_ALL);
    /**
     * Contains methods to reduce the size of log files.
     */
    class LogSizeHandler
    {
        const MAX_SIZE = 10000;
        private $logPath;
        private $log;
        private $logLen;
        
        public static function removeOldLogsWhenLogExceedsMaxSize($logPath)
        {
            if(file_exists($logPath))
            {
                if(LogSizeHandler::doesFileExceedMaxSize($logPath))
                {
                    $lsh = new LogSizeHandler($logPath);
                    $lsh->removeOldLogs();
                }
            }
        }

        private static function doesFileExceedMaxSize($logPath)
        {
            clearstatcache(); //may need this for multiple log operations on a single script execute
            return filesize($logPath) > LogSizeHandler::MAX_SIZE;
        }

        private function __construct($logPath) 
        {
            $this->logPath = $logPath;
            $this->log = file_get_contents($logPath);
            $this->logLen = strlen($this->log);
        }

        private function removeOldLogs()
        {
            $truncatedLog = $this->getNewLogs();
            $this->overwriteLog($truncatedLog);
        }
        private function getNewLogs() 
        {
            $sArr = str_split($this->log, $this->findSplitPos());
            return $sArr[1];
        }
        private function findSplitPos()
        {
            $i = intval($this->logLen /2);
            while($i < $this->logLen && $this->log[$i] != "\n") {$i++;}
            return ++$i;//increment once to include \n
        }
        private function overwriteLog($truncatedLog)
        {
            $fh = fopen($this->logPath, "w");
            fwrite($fh, $truncatedLog);
            fclose($fh);
        }
    }
?>
