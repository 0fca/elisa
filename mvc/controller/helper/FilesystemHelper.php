<?php 
    include_once('mvc/controller/helper/Logger.php');
    include_once('mvc/controller/helper/Level.php');

    final class FilesystemHelper{
        private static $dataPath = "data/";

        static public function listData(){
            return scandir(self::$dataPath);
        }

        static public function removeFile($name){
            return unlink(self::$dataPath."{$name}".".auth");
        }

        static public function printUserFile($name, $content){
            file_put_contents(self::$dataPath."{$name}.auth", $content, FILE_APPEND);
        }

        static public function addToDbFile($name, $hash){
            $content = $name.":".$hash;
            file_put_contents(self::$dataPath."userHash.data", $content, FILE_APPEND);
        }

        static public function printFile($absolutePath, $content){
            file_put_contents($absolutePath, $content, FILE_APPEND);
        }

        static public function readFile($dataFileName){
            return file_get_contents(self::$dataPath."{$dataFileName}.auth");
        }

        static public function fileExists($fileName){
            return file_exists(self::$dataPath."{$fileName}".".auth");
        }
    } 
?>