<?php
    class UserListView{
        private $users;

        public function __construct($model){
            $this->users = $model;
        }

        public function getUsers(){
            return $this->users;
        }
    }
?>
<div class="container">
    <span class="internav">
        <p>Wpisz szukaną frazę:</p>
        <input type='search' id='searchinput'/>
    </span>
</div>    