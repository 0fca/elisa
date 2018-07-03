<?php 
    include_once('messages.php');
    include_once('mvc/model/UserModel.php');

    final class AuthController{
        static $message = "";
        static $ldap_connection;
        private static $systemUserModel;

        static public function authorize($password, $username){
            $result = array();
            $result["message"] = I1;
            if(file_exists("data/".$username.".auth")){
                $authString = file_get_contents("data/".$username.".auth");
                self::$systemUserModel = unserialize($authString);
                $result["model"] = self::$systemUserModel;
                var_dump($result["model"]);
                $result["message"] = (self::$systemUserModel->getPassHash() == $password && self::$systemUserModel->getUserName() == $username) ? L0 : L100;
            }
            return $result;
        }
        
        static public function register($userModel){
            $result = "Zarejestrowano.";
            if(!file_exists("data/".$userModel->getUserName().".auth")){
                file_put_contents("data/".$userModel->getUserName().".auth", serialize($userModel));
            }
            return $result;
        }
    }
?>