<?php
    include_once('helper/DatabaseController.php');
    include_once('mvc/controller/helper/PasswordGenerator.php');
    include_once('messages.php');
    include_once('mvc/controller/exceptions/DbOperationFailedException.php');
    include_once('mvc/model/FtpUserModel.php');



    final class ManageFtpUserController{
        static public function editFtpUser($model, $needsChangingPassword, $oldId){
            try{
                if($needsChangingPassword){
                    $password = PasswordGenerator::generatePassword(16);
                    $model->setPassHash(hash("sha1", $password));
                    $model->setRawPassword($password);
                }
            
                DatabaseController::updateFtpUser($model, false, $oldId);
            }catch(DbOperationFailedException $ex){
                Logger::adminLog("Couldn't edit FTP user $oldId.",Level::ERROR, get_called_class());
                throw new ManagementException($ex->getMessage());
            }catch(InconsistentDbStateException $iex){
                Logger::adminLog("Couldn't edit FTP user $oldId; INCONSISTENCY detected",Level::ERROR, get_called_class());
                throw new ManagementException($iex->getMessage());
            }catch(DbConnectionFailedException $cex){
                Logger::adminLog("Couldn't edit FTP user $oldId; connection failed.",Level::ERROR, get_called_class());
                throw new ManagementException($cex->getMessage());
            }
            return $model;
        }

        static public function addFtpUser($ftpModel){
            $uid = self::getUid();
            $gid = self::getGid();
            try{
                $ftpModel->setGid($gid);
                $ftpModel->setUid($uid);
                $test = DatabaseController::getFtpUser($ftpModel->getName());
                //var_dump($ftpModel->getUid());
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
        }

        static public function lockFtpUser($id){
            try{
                $ftpModel = DatabaseController::getFtpUser($id);
                $ftpModel->setPassHash(hash("md5",PasswordGenerator::generatePassword(16)));
                DatabaseController::updateFtpUser($ftpModel, true, NULL);
            }catch(DbOperationFailedException $ex){
                Logger::adminLog("Couldn't lock FTP user.",Level::ERROR, get_called_class());
                throw new ManagementException($ex->getMessage());
            }catch(InconsistentDbStateException $iex){
                Logger::adminLog("Couldn't lock FTP user; INCONSISTENCY detected",Level::ERROR, get_called_class());
                throw new ManagementException($iex->getMessage());
            }catch(DbConnectionFailedException $cex){
                Logger::adminLog("Couldn't lock FTP user; connection failed.",Level::ERROR, get_called_class());
                throw new ManagementException($cex->getMessage());
            }
        }

        static public function unlockFtpUser($id){
            try{
                $ftpModel = DatabaseController::getFtpUser($id);
                $ftpModel->setHomeDir(ftpHomeDirs . "$id");
                $ftpModel->setPassHash(hash("sha1",PasswordGenerator::generatePassword(16)));
                DatabaseController::updateFtpUser($ftpModel, false, $ftpModel->getName());
            }catch(DbOperationFailedException $ex){
                Logger::adminLog("Couldn't unlock FTP user.",Level::ERROR, get_called_class());
                throw new ManagementException($ex->getMessage());
            }catch(InconsistentDbStateException $iex){
                Logger::adminLog("Couldn't unlock FTP user; INCONSISTENCY detected",Level::ERROR, get_called_class());
                throw new ManagementException($iex->getMessage());
            }catch(DbConnectionFailedException $cex){
                Logger::adminLog("Couldn't unlock FTP user; connection failed.",Level::ERROR, get_called_class());
                throw new ManagementException($cex->getMessage());
            }
        }

        static public function listFtpUsers(){
            try{
                Logger::adminLog("Listing FTP users...",Level::INFORMATION, get_called_class());
                return DatabaseController::listFtpUsers();
            }catch(DbOperationFailedException $ex){
                Logger::adminLog("Couldn't list FTP users.",Level::ERROR, get_called_class());
                throw new ManagementException($ex->getMessage());
            }catch(DbConnectionFailedException $cex){
                Logger::adminLog("Couldn't list FTP users; connection failed.",Level::ERROR, get_called_class());
                throw new ManagementException($cex->getMessage());
            }
        }

        static public function getUserById($id){
            try{
                Logger::adminLog("Querying for user:  $id",Level::INFORMATION, get_called_class());
                return DatabaseController::getFtpUser($id);
            }catch(DbOperationFailedException $ex){
                Logger::adminLog("Couldn't get FTP user $id.",Level::ERROR, get_called_class());
                throw new ManagementException($ex->getMessage());
            }catch(DbConnectionFailedException $cex){
                Logger::adminLog("Couldn't get FTP user $id; connection failed.",Level::ERROR, get_called_class());
                throw new ManagementException($cex->getMessage());
            }
        }

        static private function getUid(){
            $i = DatabaseController::getLastRow()+1;
            return $i;
        }

        static private function getGid(){
            return 88888;
        }
    }
?>
