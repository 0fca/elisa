<?php
    include_once('mvc/model/FtpUserModel.php');
    include_once('mvc/model/UserModel.php');
    include_once('mvc/controller/exceptions/DbOperationFailedException.php');
    include_once('mvc/controller/helper/FilesystemHelper.php');
    include_once('messages.php');
    include_once('constants.php');
    include_once('mvc/controller/helper/Logger.php');
    include_once('mvc/controller/helper/Level.php');
    
    final class DatabaseController{
        static private $mysqlConnection;
        static private $localConnection;
        static private $localStatement;
        static private $stmt;
        static public $rowCount;

        static public function addToLocalDatabase($userModel){
            Logger::debug("Checking if user exists.",Level::INFORMATION);
            if(!FilesystemHelper::fileExists($userModel->getUserName())){
                Logger::debug("Adding to local db.",Level::INFORMATION);
                FilesystemHelper::addToDbFile($userModel->getUserName(), hash("sha1",serialize($userModel)));
                FilesystemHelper::printUserFile($userModel->getUserName(), serialize($userModel));
            }else{
                throw new DbOperationFailedException(DB101);
            }
        }

        static public function getFromLocalDatabase($userName){
            if(!FilesystemHelper::fileExists($userModel->getUserName())){
                $userModel = unserialize(FilesystemHelper::readFile($userModel->getUserName()));
                return $userModel;
            }else{
                throw new DbOperationFailedException(DB400);
            }
        }

        static public function deleteFromLocalDatabase($userName){
            if(FilesystemHelper::fileExists($userModel->getUserName())){

            }else{
                throw new DbOperationFailedException(DB201);
            }
        }

        static public function listLocalUsers(){
            $preparedList = array();
            $data = FilesystemHelper::listData();

            foreach($data as $entry){
                $parts = explode(".", $entry);
                if(sizeof($parts) > 1){
                    if($parts[1] == "auth"){
                        if($parts[0] !== ""){
                            $preparedList[$entry] = $parts[0];
                        }
                    }
                }
            }
            return $preparedList;
        }

        static public function listFtpUsers(){
            Logger::adminLog("Connecting to FTP database.",Level::INFORMATION);
            self::connect();
            $result = array();
            if(self::$mysqlConnection !== NULL){
                $sql_statement = "SELECT * FROM usertable";
                self::$stmt->prepare($sql_statement);
                self::$stmt->execute();
                $queryResult = self::$stmt->get_result();
                if($queryResult !== NULL){
                    while($row = $queryResult->fetch_array(MYSQLI_NUM)){
                        $userid = $row[0];
                        $passwd = $row[1];
                        $homeDir = $row[2];
                        $shell = $row[3];
                        $uid = $row[4];
                        $gid = $row[5];
                        $ftpUserModel = new FtpUserModel();
                        $ftpUserModel->setName($userid);
                        $ftpUserModel->setUid($uid);
                        $ftpUserModel->setGid($gid);
                        $ftpUserModel->setHomeDir($homeDir);
                        $ftpUserModel->setPassHash($passwd);
                        $result[$userid] = $ftpUserModel;
                    }
                    $_SESSION["rowCount"] = sizeof($result);
                }else{
                    Logger::debug("DB400 thrown.",Level::ERROR);
                    throw new DbOperationFailedException(DB400);
                }
                self::disconnect(); 
                Logger::adminLog("Disconnected from FTP database...",Level::INFORMATION);
            }else{
                Logger::adminLog("DB300 thrown.",Level::ERROR);
                throw new DbConnectionFailedException(DB300);
            }
            return $result;
        }

        static public function addFtpUser($ftpUserModel){
            Logger::adminLog("Connecting to FTP database.",Level::INFORMATION);
            self::connect();
            if(self::$mysqlConnection !== NULL){
                $sql_statement = "INSERT INTO usertable(userid,passwd,homedir,shell,uid,gid,count) VALUES('{$ftpUserModel->getName()}','{$ftpUserModel->getPassHash()}','{$ftpUserModel->getHomeDir()}','bash',{$ftpUserModel->getUid()},{$ftpUserModel->getGid()},0)";
                self::$stmt->prepare($sql_statement);
                self::$stmt->execute();
                Logger::adminLog("Adding FTP user statemend queried.",Level::INFORMATION);

            }else{
                Logger::adminLog("DB300 thrown",Level::ERROR);
                throw new DbConnectionFailedException(DB300);
            }
            Logger::adminLog("Disconnecting from FTP database.",Level::INFORMATION);
            self::disconnect();
        }
        
        static public function removeFtpUser($ftpModel){
            //delete from usertable where userid=''
            Logger::adminLog("Connecting to FTP database.",Level::INFORMATION);
            self::connect();
            if(self::$mysqlConnection !== NULL){
                $sql_statement = "update usertable set passwd={$ftpModel->getPassHash()}";
                self::$stmt->prepare($sql_statement);
                self::$stmt->execute();
                Logger::adminLog("Locking FTP user statement queried.",Level::INFORMATION);
            }else{
                Logger::adminLog("DB300 thrown.",Level::ERROR);
                throw new DbConnectionFailedException(DB300);
            }
            Logger::adminLog("Disconnecting from FTP database.",Level::INFORMATION);
            self::disconnect();
        }

        static public function getFtpUser($id){
            self::connect();
            Logger::adminLog("Connecting to FTP database.",Level::INFORMATION);
            if(self::$mysqlConnection !== NULL){
                $sql_statement = "select * from usertable where userid='{$id}'";
                self::$stmt->prepare($sql_statement);
                self::$stmt->execute();
                $queryResult = self::$stmt->get_result();
                $row = $queryResult->fetch_array(MYSQLI_NUM);
                $userid = $row[0];
                $passwd = $row[1];
                $homeDir = $row[2];
                $shell = $row[3];
                $uid = $row[4];
                $gid = $row[5];
                $ftpUserModel = new FtpUserModel();
                $ftpUserModel->setName($userid);
                $ftpUserModel->setUid($uid);
                $ftpUserModel->setGid($gid);
                $ftpUserModel->setHomeDir($homeDir);
                $ftpUserModel->setPassHash($passwd);
                Logger::adminLog("FTP user statement queried.",Level::INFORMATION);
                return $ftpUserModel;
            }else{
                Logger::adminLog("Connecting to FTP database.",Level::ERROR);
                throw new DbConnectionFailedException(DB300);
            }
            Logger::adminLog("Disconnecting from FTP database.",Level::INFORMATION);
            self::disconnect();
        }

        static public function editFtpUser(){

        }

        static public function getRowCount(){
            $i = self::$rowCount;
            return self::$rowCount;
        }

        static private function connect(){
            if(self::$mysqlConnection === NULL){
                self::$mysqlConnection = new mysqli(server_addr, adm_usr_name, adm_usr_pass, db_name);
                self::$stmt = self::$mysqlConnection->stmt_init();
            }
        }

        static private function disconnect(){
            if(self::$mysqlConnection !== NULL){
                self::$stmt->close();
                self::$mysqlConnection->close();
                self::$stmt = NULL;
                self::$mysqlConnection = NULL;
            }
        }
    }
?>