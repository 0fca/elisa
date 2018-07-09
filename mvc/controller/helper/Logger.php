<?php
    include_once("Level.php");

    final class Logger{
        const pathDebug = "data/debug/debugLog.log";
        const adminLogPath = "data/admin/admin.log";

        static public function debug($debugMessage, $level){
            $date = date('m/d/Y h:i:s a', time());
            $content = "Time: ".$date." L: ".$level.":".$debugMessage."\n";
            FilesystemHelper::printFile(self::pathDebug, $content);
        } 

        static public function adminLog($debugMessage, $level){
            //date_default_timezone_set('Poland/Warsaw');
            $date = date('m/d/Y h:i:s a', time());
            $content = "Time: ".$date." L: ".$level.":".$debugMessage."\n";
            FilesystemHelper::printFile(self::adminLogPath, $content);
        }
    }
?>