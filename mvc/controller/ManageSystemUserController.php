<?php
    include_once('helper/DatabaseController.php');
    include_once('constants.php');
    include_once('messages.php');
    include_once('mvc/controller/exceptions/ManagementException.php');
    include_once('mvc/model/UserModel.php');
    include_once('mvc/controller/helper/Logger.php');
    include_once('mvc/controller/helper/Level.php');

    final class ManageSystemUserController{
        static public function editSystemUser($model, $oldName){
            try{
                $loggedIn = self::getLoggedInUser();
                DatabaseController::editLocalUser($model, $oldName);
                if($loggedIn->getUserName() === $oldName){
                    self::updateUserHash(hash(hashAlgorithm,serialize($model)));
                }
                Logger::adminLog("Edited local user.", Level::INFORMATION, get_called_class());
            }catch(DbOperationFailedException $ex){
                throw new ManagementException($ex->getMessage());
            }
        }

        static public function addSystemUser($userModel){
            try{
                DatabaseController::addToLocalDatabase($userModel);
                Logger::adminLog("Added to local database.",Level::INFORMATION, get_called_class());
            }catch(DbOperationFailedException $ex){
                throw new ManagementException($ex->getMessage());
            }
        }

        static public function deleteSystemUser($id){
            try{
                DatabaseController::deleteFromLocalDatabase($id);
                Logger::adminLog("Deleting system user.", Level::INFORMATION, get_called_class());
            }catch(DbOperationFailedException $ex){
                throw new ManagementException($ex->getMessage());
            }
        }

        static private function getLoggedInUser(){
            return $systemUserModel = AuthController::authorizeByHash($_COOKIE['userHash'])["model"];
        }

        static private function updateUserHash($hash){
            setcookie("userHash",$hash, cookie_xpire);
        }
    }
?>