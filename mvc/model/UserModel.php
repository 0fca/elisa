<?php 
    class UserModel{
        private $userName;
        private $isAuthorized;
        private $passwordHash;

        public function __construct($userName, $passwordHash, $isAuthorized){
            $this->userName = $userName;
            $this->isAuthorized = $isAuthorized;
            $this->passwordHash = $passwordHash;
        }

        public function isAuthorized(){
            return $this->isAuthorized;
        }

        public function getUserName(){
            return $this->userName;
        }

        public function getPassHash(){
            return $this->passwordHash;
        }
        
    }
?>