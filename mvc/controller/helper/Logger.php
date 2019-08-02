<?php
    include_once("Level.php");
    $array = array_keys(get_defined_vars());

    final class Logger{
        const pathDebug = "data/debug/debugLog.log";
        const adminLogPath = "data/admin/admin.log";

        static public function debug($debugMessage, $level){
            $date = date('m/d/Y h:i:s a', time());
            $content = $date." L: ".$level.":".$debugMessage."\n";
            FilesystemHelper::printFile(self::pathDebug, $content);
        } 

        static public function adminLog($debugMessage, $level, $className){
            $date = date('m/d/Y h:i:s a', time());
            $content = $date." L: ".$level." Class: ".$className.":".$debugMessage."\n";

            FilesystemHelper::printFile(self::adminLogPath, $content);
        }

        static public function getLastLogs(){
            $file = explode("\n", FilesystemHelper::readLogFile());
            $logs = "";

            for($i = sizeof($file); $i >= sizeof($file) - 10; $i--){
                $logs .= $file[$i] . "\n";
                //self::debug($logs);
            }
            
            return $logs;
        }
    }
?>
