<?php 
    include_once('messages.php');
    include_once('mvc/model/UserModel.php');
    include_once('mvc/controller/ManageSystemUserController.php');
    include_once('mvc/controller/helper/Logger.php');
    include_once('mvc/controller/helper/Level.php');

    final class AuthController{
        private static $message = "";
        private static $ldap_connection;
        private static $systemUserModel;

        static public function authorize($password, $username){
            $result = array();
            $result["message"] = L100;
            if(file_exists("data/".$username.".auth")){
                $authString = FilesystemHelper::readFile($username);
                self::$systemUserModel = unserialize($authString);
                $result["model"] = self::$systemUserModel;
                $result["message"] = (self::$systemUserModel->getPassHash() == $password && self::$systemUserModel->getUserName() == $username) ? L0 : L100;
                Logger::adminLog("User authorized successfully:{$username} ",Level::INFORMATION);
            }
            return $result;
        }
        
        static public function register($userModel){
            self::$systemUserModel = $userModel;
            //var_dump($userModel);
            try{
                ManageSystemUserController::addSystemUser($userModel);
                $username = $userModel->getUserName();
                //Logger::adminLog("User registered successfully:{$username} ",Level::INFORMATION);
                $_SESSION['returnMessage'] = DB0;
                $_SESSION['isReturnError'] = false;
            }catch(ManagementException $ex){
                var_dump($ex);
                $_SESSION['returnMessage'] = $ex->getMessage();
                $_SESSION['isReturnError'] = true;
                //Logger::adminLog("Couldn't register the user. ",Level::ERROR);
            }
        }

        static public function getLoggedInUser(){
            return self::$systemUserModel;
        }

        static public function authorizeByHash($hash){//rewrite this func using FilesystemHelper; caution: without it, the system CANT work.
            $result = array();
            if($hash !== NULL){
                $hashTable = array();
                $hashTable = explode(';',file_get_contents("data/userHash.data"));

                foreach($hashTable as $line){
                    $name = explode(":", trim($line))[0];
                    $hash2 = explode(":", trim($line))[1];

                    if($hash == $hash2){
                        $result["message"] = L0;
                        $authString = file_get_contents("data/".$name.".auth");
                        self::$systemUserModel = unserialize($authString);
                        $result["model"] = self::$systemUserModel;
                        $username = self::$systemUserModel->getUserName();
                        Logger::adminLog("User authorized successfully:{$username} ",Level::INFORMATION);
                        return $result;
                    }
                }    
            }else{
                $result["message"] = L100;
            }
            return $result;
        }
    }
?>