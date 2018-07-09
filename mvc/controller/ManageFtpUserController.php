<?php
    include_once('helper/DatabaseController.php');
    include_once('messages.php');
    include_once('mvc/controller/exceptions/DbOperationFailedException.php');
    include_once('mvc/model/FtpUserModel.php');


    final class ManageFtpUserController{
        static public function editFtpUser($model){
            
        }

        static public function addFtpUser($ftpModel){
            $uid = self::getUid();
            $gid = self::getGid();
            try{
                $ftpModel->setGid($gid);
                $ftpModel->setUid($uid);

                DatabaseController::addFtpUser($ftpModel);
                Logger::adminLog("FTP user added successfully: {$ftpModel->getName()} ",Level::INFORMATION);
            }catch(DbOperationFailedException $ex){
                var_dump($ex);
            }
        }

        static public function listFtpUsers(){
            try{
                Logger::adminLog("Listing FTP users....",Level::INFORMATION);
                return DatabaseController::listFtpUsers();
            }catch(DbOperationFailedException $ex){
                Logger::adminLog("Couldn't get FTP users list.",Level::ERROR);
                throw new ManagementException($ex->getMessage());
            }
        }

        static public function getUserById($id){
            try{
                Logger::adminLog("Querying for user:  $id",Level::INFORMATION);
                return DatabaseController::getFtpUser($id);
            }catch(DbOperationFailedException $ex){
                Logger::adminLog("Couldn't get FTP user: $id",Level::ERROR);
                throw new ManagementException($ex->getMessage());
            }
        }

        static private function getUid(){
            $i = $_SESSION["rowCount"] + 1;
            Logger::debug("UID: {$i}",Level::ERROR);
            return $i;
        }

        static private function getGid(){
            return 0;
        }
    }
?>