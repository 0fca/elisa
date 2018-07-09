<?php
    include_once('helper/DatabaseController.php');
    include_once('messages.php');
    include_once('mvc/controller/exceptions/ManagementException.php');
    include_once('mvc/model/UserModel.php');
    include_once('helper/Logger.php');
    include_once('helper/Level.php');

    final class ManageSystemUserController{
        static public function editSystemUser($model){
            
        }

        static public function addSystemUser($userModel){
            try{
                DatabaseController::addToLocalDatabase($userModel);
            }catch(DbOperationFailedException $ex){
                throw new ManagementException($ex->getMessage());
            }
        }

        static public function deleteSystemUser($id){
            try{
                DatabaseController::deleteFromLocalDatabase($id);
                Logger::adminLog("Deleting system user.", Level::INFORMATION);
            }catch(DbOperationFailedException $ex){
                throw new ManagementException($ex->getMessage());
            }
        }
    }
?>