<?php
    class FtpUserModel{
        private $name;
        private $homeDir;
        private $gid;
        private $uid;
        private $passwd;

        public function __construct(){
            
        }

        public function getName(){
            return $this->name;
        }    

        public function getHomeDir(){
            return $this->homeDir;
        }

        public function getGid(){
            return $this->gid;
        }

        public function getUid(){
            return $this->uid;
        }

        public function getPassHash(){
            return $this->passwd;
        }

        public function setName($name){
            $this->name = $name;
        }

        public function setUid($uid){
            $this->uid = $uid;
        }

        public function setGid($gid){
            $this->gid = $gid;
        }

        public function setPassHash($hash){
            $this->passwd = $hash;
        }

        public function setHomeDir($homeDir){
            $this->homeDir = $homeDir;
        }
    }
?>