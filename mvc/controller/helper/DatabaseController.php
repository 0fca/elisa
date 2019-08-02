<?php
    include_once('mvc/model/FtpUserModel.php');
    include_once('mvc/model/UserModel.php');
    include_once('mvc/controller/exceptions/DbOperationFailedException.php');
    include_once('mvc/controller/exceptions/DbConnectionFailedException.php');
    include_once('mvc/controller/exceptions/InconsistentDbStateException.php');
    include_once('mvc/controller/helper/FilesystemHelper.php');
    include_once('messages.php');
    include_once('constants.php');
    include_once('mvc/controller/helper/Logger.php');
    include_once('mvc/controller/helper/Level.php');
    include_once("mvc/controller/helper/Mailer.php");
    
    final class DatabaseController{
        static private $localConnection;
        static private $localStatement;
        static public $rowCount;
        static private $con;

        static private $statements = array();
        static private $dbConnections = array();

        static public function addToLocalDatabase($userModel){
            Logger::debug("Checking if user exists.",Level::INFORMATION);
            if(!FilesystemHelper::fileExists($userModel->getUserName())){
                Logger::debug("Adding to local db.",Level::INFORMATION);
                $dbContent = FilesystemHelper::readFromDbFile();
                $dbContent .= $userModel->getUserName() . ":" . hash("sha1",serialize($userModel)) . ";";
                Logger::adminLog($dbContent, Level::INFORMATION, get_called_class());
                FilesystemHelper::printAllToDbFile($dbContent);
                FilesystemHelper::printUserFile($userModel->getUserName(), serialize($userModel));
            }else{
                throw new DbOperationFailedException(DB101);
            }
        }

        static public function getFromLocalDatabase($userName){
            if(FilesystemHelper::fileExists($userName)){
                $userModel = unserialize(FilesystemHelper::readFile($userName));
                return $userModel;
            }else{
                throw new DbOperationFailedException(DB400);
            }
        }

        static public function deleteFromLocalDatabase($userName){
            if(FilesystemHelper::fileExists($userName)){
                $resultContent = self::deleteFromUserHashData($userName);
                Logger::debug("Result: " . $resultContent, Level::INFORMATION);
                FilesystemHelper::printAllToDbFile($resultContent);
                Logger::debug(FilesystemHelper::removeFile($userName), Level::INFORMATION);
            }else{
                throw new DbOperationFailedException(DB201);
            }
        }

        static public function editLocalUser($userModel, $oldName){
            if(FilesystemHelper::fileExists($oldName)){
                $resultContent = self::deleteFromUserHashData($oldName);
                $resultContent .= $userModel->getUserName() . ":" . hash("sha1",serialize($userModel)) . ";";
                FilesystemHelper::removeFile($oldName);
                FilesystemHelper::printUserFile($userModel->getUserName(), serialize($userModel));
                FilesystemHelper::printAllToDbFile($resultContent);
            }
        }

        static public function listLocalUsers(){
            $preparedList = array();
            $data = FilesystemHelper::listData();
            foreach($data as $entry){
                $parts = explode(".auth", $entry);
                if(sizeof($parts) > 1){
                    if($parts[0] !== ""){
                        $preparedList[$entry] = $parts[0];
                    }
                }
            }
            return $preparedList;
        }

        static public function listFtpUsers($index = 0){
            $con = self::connect($index);
            $result = array();

            if($con !== NULL){
                $sql_statement = "SELECT userid,passwd,homedir,uid,gid,comment FROM usertable";
                $stmt = $con->stmt_init();
                if($stmt !== NULL){
                $stmt->prepare($sql_statement);
                $stmt->execute();
                $queryResult = $stmt->get_result();
                if($queryResult !== NULL){
                    while($row = $queryResult->fetch_array(MYSQLI_NUM)){
                        $userid = $row[0];
                        $passwd = $row[1];
                        $homeDir = $row[2];
                        $uid = $row[3];
                        $gid = $row[4];
			$quota = $row[5];
                        $ftpUserModel = new FtpUserModel();
                        $ftpUserModel->setName($userid);
                        $ftpUserModel->setUid($uid);
                        $ftpUserModel->setGid($gid);
                        $ftpUserModel->setHomeDir($homeDir);
                        $ftpUserModel->setPassHash($passwd);
			$ftpUserModel->setQuota($quota);
                        $result[$userid] = $ftpUserModel;
                    }
                    $_SESSION["rowCount"] = sizeof($result);
                }else{
                    Logger::adminLog("DB400 thrown.",Level::ERROR, get_called_class());
                    throw new DbOperationFailedException(DB400);
                }
                }else{
                    Logger::adminLog("DB401 thrown.",Level::ERROR, get_called_class());
                    throw new DbOperationFailedException(DB401);
                }
                self::disconnect($con, $stmt); 
            }else{
                Logger::adminLog("DB300 thrown.",Level::ERROR, get_called_class());
                throw new DbConnectionFailedException(DB300);
            }
            return $result;
        }

        static public function addFtpUser($ftpUserModel){
            $result = self::checkConsistency();
            //var_dump($result);
            if(!$result["result"]){
                Logger::adminLog("Inconsistency detected. Differences between databases: " . $result["diffs"] . "Was difference in length: " . $result["differedInLength"], Level::ERROR, get_called_class());

                $content = "Inconsistency first detected while checking db: " . $result["diffs"] . "Was difference in length: " . $result["differedInLength"] . ".\n" . "Operation: ".__METHOD__.". \nUser model: " . serialize($ftpUserModel);
                Mailer::sendMail($content);
                //Logger::debug(Logger::getLastLogs());
                throw new InconsistentDbStateException(DB2);
            }

            for($i = 0; $i < sizeof(dbNames); $i++){
                $con = self::connect($i);
                $stmt = $con->stmt_init();

                if($stmt !== NULL){
                
                    $sql_statement = "INSERT INTO usertable(userid,passwd,homedir,uid,gid,comment) VALUES('{$ftpUserModel->getName()}','{$ftpUserModel->getPassHash()}','{$ftpUserModel->getHomeDir()}',{$ftpUserModel->getUid()},{$ftpUserModel->getGid()},'{$ftpUserModel->getQuota()}')";
                    $stmt->prepare($sql_statement);
                    $stmt->execute();
                    Logger::adminLog("Adding FTP user statement queried.",Level::INFORMATION, get_called_class());
                    $path = FilesystemHelper::createFtpFolder($ftpUserModel->getName(), $ftpUserModel->getUid());
                    Logger::adminLog("Attempted to create FTP user dir: " . $path , Level::WARNING, get_called_class());
                }else{
                    Logger::adminLog("DB401 thrown for connection no." . $i, Level::ERROR, get_called_class());
                    throw new DbConnectionFailedException(DB401);
                }

                self::disconnect($con, $stmt);
            }
        }

        static public function updateFtpUser($model, $shouldLock, $oldId){
            $result = self::checkConsistency();

            if(!$result["result"]){
                Logger::adminLog("Inconsistency detected. Differences between databases: " . $result["diffs"] . "Was difference in length: " . $result["differedInLength"], Level::ERROR, get_called_class());
                $content = "Inconsistency detected. Differences between databases: " . $result["diffs"] . "Was difference in length: " . $result["differedInLength"] . ".\n" . "Operation: ". __METHOD__ . "; Locking: " . ($shouldLock == 1 ? "TRUE" : "FALSE") . ". \nUser model: " . serialize($model);

                Mailer::sendMail($content);
                throw new InconsistentDbStateException(DB2);
            }

            for($i = 0; $i < sizeof(dbNames); $i++){
                $con = self::connect($i);
                $stmt = $con->stmt_init();

                if($stmt !== NULL){
                    $sql_statement = "";
                    if(!$shouldLock){
                        $sql_statement = "update usertable set usertable.userid='{$model->getName()}', usertable.homedir='{$model->getHomeDir()}', usertable.uid='{$model->getUid()}', usertable.gid='{$model->getGid()}', usertable.passwd='{$model->getPassHash()}',usertable.comment='{$model->getQuota()}' where userid='{$oldId}'";
                    }else{
                        $sql_statement = "update usertable set usertable.passwd='{$model->getPassHash()}', usertable.homedir='***BLOCKED***' where userid='{$model->getName()}'";
                        Logger::adminLog($sql_statement, Level::WARNING, get_called_class());
                    }
                    $stmt->prepare($sql_statement);
                    $stmt->execute();
                    Logger::adminLog(($shouldLock ? "Locking " :"Adding "). "FTP user statemend queried.",Level::INFORMATION, get_called_class());
                }else{
                    Logger::adminLog("DB500 thrown", Level::ERROR, get_called_class());
                    throw new DbConnectionFailedException(DB500);
                }
                self::disconnect($con, $stmt);
            }
        }

        static public function getFtpUser($id){
            $con = self::connect(0);

            if($con !== NULL){
                $stmt = $con->stmt_init();
                $sql_statement = "select userid,passwd,homedir,uid,gid,comment from usertable where userid='{$id}'";
                $stmt->prepare($sql_statement);
                $stmt->execute();
                $queryResult = $stmt->get_result();

                    $row = $queryResult->fetch_array(MYSQLI_NUM);
                    $userid = $row[0];
                    $passwd = $row[1];
                    $homeDir = $row[2];
                    $uid = $row[3];
                    $gid = $row[4];
		    $quota = $row[5];
                    $ftpUserModel = new FtpUserModel();
                    $ftpUserModel->setName($userid);
                    $ftpUserModel->setUid($uid);
                    $ftpUserModel->setGid($gid);
                    $ftpUserModel->setHomeDir($homeDir);
                    $ftpUserModel->setPassHash($passwd);
		    $ftpUserModel->setQuota($quota);
                    $count = sizeof($row);
                    Logger::adminLog("FTP user({$uid}) statement queried.",Level::INFORMATION, get_called_class());
                
                
                return $ftpUserModel;
            }else{
                Logger::adminLog("DB300 thrown",Level::ERROR, get_called_class());
                throw new DbConnectionFailedException(DB300);
            }

            self::disconnect($con, $stmt);
        }

        static public function getRowCount(){
            $con = self::connect(0);
            if($con !== NULL){
                $stmt = $con->stmt_init();
                $sql_statement = "SELECT COUNT(*) FROM usertable";
                $stmt->prepare($sql_statement);
                $stmt->execute();
                $queryResult = $stmt->get_result();
                self::$rowCount = $queryResult->fetch_array(MYSQLI_NUM)[0];
                $_SESSION["rowCount"] = self::$rowCount;
            }
            self::disconnect($con, $stmt);
            return self::$rowCount;
        }

        static private function connect($index){
            $dbStr = explode(";",dbNames[$index]);
            $con = new mysqli($dbStr[0], $dbStr[2], $dbStr[3], $dbStr[1]);
            
            Logger::adminLog("Connection to database " . $dbStr[3] . " established.", Level::INFORMATION, get_called_class());
            return $con;
        }

        static private function disconnect($con, $stmt){
            if($con !== NULL){
                $stmt->close();
                $stmt = NULL;
                $con->close();
                $con = NULL;
            }
        }

        static private function checkConsistency(){
            $result = array();
            $dbArray = array();
            $userArrays = array();
            $singleUserArr = array();
            $strUser2 = array();

            $max = 0;
            //Here we are searching for maximal length, because we want all records not just part of them.
            for($i = 0; $i < sizeof(dbNames); $i++){
                $singleUserArr = self::listFtpUsers($i);

                if(sizeof($singleUserArr) >= $max){
                    $userArrays[$i] = $singleUserArr;
                    $max = sizeof($singleUserArr);
                }
            }
            //We got max length of table so we can check all tables if they are long enough, if so we add them to appropiate array.
            for($i = 0; $i < sizeof(dbNames); $i++){
                if(sizeof($userArrays[$i]) == $max){
                    foreach($userArrays[$i] as $user){
                        $strUser2[$user->getName()] = serialize($user);
                    }
                    $dbArray[$i] = $strUser2;
                }
            }
            Logger::adminLog("Databases with appropiate length count: " . sizeof($dbArray),Level::INFORMATION, get_called_class());
            $diffs = "";
            //basing on the information that length is equal to tha maximal we know we can check diffs between data in arrays.
            for($it = 0;  $it <  sizeof($dbArray); $it++){
                $im = 0;
                foreach($dbArray as $db){
                    $currentDiff = array_diff($dbArray[$it], $db);
                    if(sizeof($currentDiff) > 0){
                        $diffs .= explode(";",dbNames[$it])[0] . "->" . explode(";",dbNames[$im])[0] . "\nDifferent records: \n" . implode("\n" ,$currentDiff) . "\n";
                        $im++;
                    }
                }
            }
            if($diffs == ""){
                $diffs = "None\n";
            }
            $result["result"] = (sizeof(dbNames) == sizeof($dbArray)) && ($diffs == "None\n");
            $result["differedInLength"] = (sizeof(dbNames) != sizeof($dbArray) ? "TRUE" : "FALSE");
            $result["diffs"] = $diffs;
        
            return $result;
        }

        static private function deleteFromUserHashData($userName){
            $dbcontent = FilesystemHelper::readFromDbFile();
            $parts = explode(";", $dbcontent);
            $resultContent = "";

            foreach($parts as $part){
                if($part !== NULL && $part != ""){
                    $name = explode(":",$part)[0];
                    if(trim($name) != trim($userName)){
                        $resultContent .= $part . ";";
                    }
                }
            }
            return $resultContent;
        }

        static public function getLastRow(){
            $con = self::connect(0);
            $stmt = $con->stmt_init();
            $sql_statement = "SELECT MAX(uid) FROM usertable; ";
            $stmt->prepare($sql_statement);
            $stmt->execute();
            $queryResult = $stmt->get_result();
            $uid = $queryResult->fetch_array(MYSQLI_NUM);
            return $uid[0];
        }
    }
?>
