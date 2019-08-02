<?php 
    include_once('mvc/controller/helper/Logger.php');
    include_once('mvc/controller/helper/Level.php');

    final class FilesystemHelper{
        private static $dataPath = "data/";

        static public function listData(){
            return scandir(self::$dataPath);
        }

        static public function removeFile($name){
            return unlink(self::$dataPath . "{$name}".".auth");
        }

        static public function printUserFile($name, $content){
            file_put_contents(self::$dataPath . "{$name}.auth", $content);
        }

        static public function printToDbFile($name, $hash){//this method doesnt work, do not use it
            file_put_contents(self::$dataPath . "userHash.data", $name.":".$hash.";", FILE_APPEND);
        }

        static public function printAllToDbFile($content){
            file_put_contents(self::$dataPath . "userHash.data", $content);
        }

        static public function readFromDbFile(){
            return file_get_contents(self::$dataPath . "userHash.data");
        }

        static public function printFile($absolutePath, $content){
            file_put_contents($absolutePath, $content, FILE_APPEND);
        }

        static public function readFile($dataFileName){
            return file_get_contents(self::$dataPath . "{$dataFileName}.auth");
        }

        static public function readLogFile(){
            return file_get_contents(self::$dataPath . "admin/admin.log");
        }

        static public function fileExists($fileName){
            return file_exists(self::$dataPath . "{$fileName}".".auth");
        }

        static public function createFtpFolder($name, $uid){
            file_put_contents(self::$dataPath . "/users/{$name}" . "#" . $uid, "szczekuszka");
            return self::$dataPath . "users/{$name}" . "#" . $uid;
        }
    } 
?>