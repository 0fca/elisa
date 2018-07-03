<?php
    final class FtpUserModel{
        private $name;
        private $homeDir;
        private $gid;
        private $uid;
        private $passwd;

        public function __construct($name, $homeDir, $gid, $uid, $passwd){
            $this->id = $id;
            $this->name = $name;
            $this->homeDir = $homeDir;
            $this->gid = $gid;
            $this->uid = $uid;
            $this->passwd = $passwd;
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

        public function getPasswd(){
            return $this->passwd;
        }
    }
?>