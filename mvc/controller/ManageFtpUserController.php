<?php
    include_once('helper/DatabaseController.php');
    include_once('helper/FileDatabaseController.php');
    include_once('mvc/controller/helper/PasswordGenerator.php');
    include_once('messages.php');
    include_once('mvc/controller/exceptions/DbOperationFailedException.php');
    include_once('mvc/model/FtpUserModel.php');
    include_once('ConfigurationProvider.php');
    ConfigurationProvider::loadConfiguration();


    final class ManageFtpUserController{
        static public function editFtpUser($model, $needsChangingPassword, $oldId){
            $datasource = ConfigurationProvider::getConfigurationField("datasource");
            if ($needsChangingPassword) {
                $password = PasswordGenerator::generatePassword(16);
                $model->setPassHash(hash(hashAlgorithm, $password));
                $model->setRawPassword($password);
            }

            if($datasource === "database") {
                try {
                    DatabaseController::updateFtpUser($model, false, $oldId);
                } catch (DbOperationFailedException $ex) {
                    Logger::adminLog("Couldn't edit FTP user $oldId.", Level::ERROR, get_called_class());
                    throw new ManagementException($ex->getMessage());
                } catch (InconsistentDbStateException $iex) {
                    Logger::adminLog("Couldn't edit FTP user $oldId; INCONSISTENCY detected", Level::ERROR, get_called_class());
                    throw new ManagementException($iex->getMessage());
                } catch (DbConnectionFailedException $cex) {
                    Logger::adminLog("Couldn't edit FTP user $oldId; connection failed.", Level::ERROR, get_called_class());
                    throw new ManagementException($cex->getMessage());
                }

            }else if($datasource === "file"){
                $result = FileDatabaseController::addFileUser($model, $oldId);
                $resultCode = $result["returnCode"];
                $output = $result["stdout"] !== NULL ? $result["stdout"] : $result["stderr"];

                if($resultCode !== 0){
                    Logger::adminLog("Couldn't edit file FTP user.",Level::ERROR, get_called_class());
                    Logger::adminLog($output,Level::ERROR, get_called_class());
                    throw new ManagementException($output);
                }
            }
            return $model;
        }

        static public function addFtpUser(FtpUserModel $ftpModel){
            $datasource = ConfigurationProvider::getConfigurationField("datasource");

            $uid = self::getUid();
            $gid = self::getGid();

            $ftpModel->setGid($gid);
            $ftpModel->setUid($uid);

            if($datasource !== NULL) {
            if ($datasource === "database") {

            try{
                $test = DatabaseController::getFtpUser($ftpModel->getName());

                if($test->getName() != $ftpModel->getName() && $test->getUid() != $ftpModel->getUid()){
                    DatabaseController::addFtpUser($ftpModel);
                    Logger::adminLog("FTP user added successfully: {$ftpModel->getName()} ",Level::INFORMATION, get_called_class());
                }else{
                    Logger::adminLog("Couldn't add FTP user, user {$ftpModel->getName()} exists",Level::ERROR, get_called_class());
                    throw new ManagementException(DB101);
                }
            }catch(DbOperationFailedException $ex){
                Logger::adminLog("Couldn't add FTP user.",Level::ERROR, get_called_class());
                throw new ManagementException($ex->getMessage());
            }catch(InconsistentDbStateException $iex){
                Logger::adminLog("Couldn't add FTP user; INCONSISTENCY detected",Level::ERROR, get_called_class());
                throw new ManagementException($iex->getMessage());
            }catch(DbConnectionFailedException $cex){
                Logger::adminLog("Couldn't add FTP user; connection failed.",Level::ERROR, get_called_class());
                throw new ManagementException($cex->getMessage());
            }
            }else if($datasource === "file"){
                $result = FileDatabaseController::addFileUser($ftpModel, $ftpModel->getName());
                $resultCode = $result["returnCode"];
                $output = $result["stdout"] !== NULL ? $result["stdout"] : $result["stderr"];

                if($resultCode !== 0){
                    Logger::adminLog("Couldn't add file FTP user.",Level::ERROR, get_called_class());
                    Logger::adminLog($output,Level::ERROR, get_called_class());
                    throw new ManagementException($output);
                }

            }
            }
        }

        static public function lockFtpUser($id){
            $datasource = ConfigurationProvider::getConfigurationField("datasource");

            if($datasource === "database") {
                try {
                    $ftpModel = DatabaseController::getFtpUser($id);
                    $ftpModel->setPassHash(hash(hashAlgorithm, PasswordGenerator::generatePassword(16)));
                    DatabaseController::updateFtpUser($ftpModel, true, NULL);
                } catch (DbOperationFailedException $ex) {
                    Logger::adminLog("Couldn't lock FTP user.", Level::ERROR, get_called_class());
                    throw new ManagementException($ex->getMessage());
                } catch (InconsistentDbStateException $iex) {
                    Logger::adminLog("Couldn't lock FTP user; INCONSISTENCY detected", Level::ERROR, get_called_class());
                    throw new ManagementException($iex->getMessage());
                } catch (DbConnectionFailedException $cex) {
                    Logger::adminLog("Couldn't lock FTP user; connection failed.", Level::ERROR, get_called_class());
                    throw new ManagementException($cex->getMessage());
                }
            }else if($datasource === "file"){
                $ftpModel = FileDatabaseController::listFileUsers()[$id];
                $ftpModel->setPassHash(hash(hashAlgorithm, PasswordGenerator::generatePassword(16)));
                $result = FileDatabaseController::blockFileUser($ftpModel);

                $resultCode = $result["returnCode"];
                $output = $result["stdout"];

                if($resultCode !== 0){
                    Logger::adminLog("Couldn't lock file FTP user.",Level::ERROR, get_called_class());
                    Logger::adminLog($output,Level::ERROR, get_called_class());
                    throw new ManagementException($output);
                }
            }
        }

        static public function unlockFtpUser($id){
            $datasource = ConfigurationProvider::getConfigurationField("datasource");

            if($datasource === "database") {
                try {
                    $ftpModel = DatabaseController::getFtpUser($id);
                    $ftpModel->setHomeDir(ftpHomeDirs . "$id");
                    $ftpModel->setPassHash(hash(hashAlgorithm, PasswordGenerator::generatePassword(16)));
                    DatabaseController::updateFtpUser($ftpModel, false, $ftpModel->getName());
                } catch (DbOperationFailedException $ex) {
                    Logger::adminLog("Couldn't unlock FTP user.", Level::ERROR, get_called_class());
                    throw new ManagementException($ex->getMessage());
                } catch (InconsistentDbStateException $iex) {
                    Logger::adminLog("Couldn't unlock FTP user; INCONSISTENCY detected", Level::ERROR, get_called_class());
                    throw new ManagementException($iex->getMessage());
                } catch (DbConnectionFailedException $cex) {
                    Logger::adminLog("Couldn't unlock FTP user; connection failed.", Level::ERROR, get_called_class());
                    throw new ManagementException($cex->getMessage());
                }
            }else if($datasource === "file"){
                $ftpModel = FileDatabaseController::listFileUsers()[$id];
                $ftpModel->setPassHash(hash(hashAlgorithm, PasswordGenerator::generatePassword(16)));
                $result = FileDatabaseController::unlockFileUser($ftpModel);

                $resultCode = $result["returnCode"];
                $output = $result["stdout"];

                if($resultCode !== 0){
                    Logger::adminLog("Couldn't unlock file FTP user.",Level::ERROR, get_called_class());
                    Logger::adminLog($output,Level::ERROR, get_called_class());
                    throw new ManagementException($output);
                }
            }
        }

        static public function listFtpUsers(){
            $datasource = ConfigurationProvider::getConfigurationField("datasource");

            if($datasource !== NULL) {
                if ($datasource === "database") {
                    $dbUsers = array();
                    try {
                        Logger::adminLog("Listing FTP users...", Level::INFORMATION, get_called_class());
                        $dbUsers = DatabaseController::listFtpUsers();
                    } catch (DbOperationFailedException $ex) {
                        Logger::adminLog("Couldn't list FTP users.", Level::ERROR, get_called_class());
                    } catch (DbConnectionFailedException $cex) {
                        Logger::adminLog("Couldn't list FTP users; connection failed.", Level::ERROR, get_called_class());
                    }
                    return $dbUsers;
                } else if ($datasource === "file") {
                    $ftpPasswdUsers = FileDatabaseController::listFileUsers();
                    return $ftpPasswdUsers;
                }
            }
            return array();
        }

        static public function getUserById($id){
            $datasource = ConfigurationProvider::getConfigurationField("datasource");
            if($datasource === "database") {
                try {
                    Logger::adminLog("Querying for user:  $id", Level::INFORMATION, get_called_class());
                    return DatabaseController::getFtpUser($id);
                } catch (DbOperationFailedException $ex) {
                    Logger::adminLog("Couldn't get FTP user $id.", Level::ERROR, get_called_class());
                    throw new ManagementException($ex->getMessage());
                } catch (DbConnectionFailedException $cex) {
                    Logger::adminLog("Couldn't get FTP user $id; connection failed.", Level::ERROR, get_called_class());
                    throw new ManagementException($cex->getMessage());
                }
            }else{
                $users = FileDatabaseController::listFileUsers();

                foreach($users as $user){
                    if($user->getName() === $id){
                        return $user;
                    }
                }
            }
        }

        static private function getUid(){
            $datasource = ConfigurationProvider::getConfigurationField("datasource");
            $i = 0;
            if($datasource === "database") {
                $i = DatabaseController::getLastRow() + 1;
            }else{
                $i = sizeof(FileDatabaseController::listFileUsers()) + 1;
            }
            return $i;
        }

        static private function getGid(){
            return 88888;
        }
    }
?>
