<?php
    class ManageUserView{
        private $userModel;

        public function __construct($model){
            $this->userModel = $model;
        }

        public function getUserModel(){
            return $this->userModel;
        }
    }

    
?>
<div>
    ManageUserView.
</div>