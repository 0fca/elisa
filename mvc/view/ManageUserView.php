<?php
    include_once('mvc/controller/ManageUserController.php');
    include_once('mvc/model/UserModel.php');
    include_once('mvc/model/FtpUserModel.php');
    include_once('mvc/model/router.php');

    class ManageUserView{
        private $userModel;

        public function __construct($model){
            $this->userModel = $model;
        }

        public function getUserModel(){
            return $this->userModel;
        }
    }

    $model = $_SESSION['ftpUserModel'];
    $systemUser = unserialize($_COOKIE["systemUser"]);
    if($systemUser->isAuthorized()){
        $manageUserView = new ManageUserView($model);
    }else{
        Router::redirect("/");
    }
    
?>
<div class="container">
    <?php
        function localPrint($message){
            echo $message;
        } 
    ?>
    <h3>Zarządzaj użytkownikiem: 
            <?php 
            if($model !== NULL){
                if($model->getName() !== NULL) {
                    echo $model->getName();
                }else{
                    echo "";
                } 
            }
            ?>
        </h3>
    <div class="content-container">
        <div class="leftCol1">
            <p>Nazwa użyszkodnika:</p>
        </div>

        <div class="leftCol2">
            <p>Katalog domowy:</p>
        </div>

        <div class="leftCol3">
            <p>Hasło:</p>
        </div>

        <div class="rightCol1">
            <input type="text" id="nameField"  placeholder="Nazwa" oninput="homeDirAutoFill();"/>
        </div>
        <?php 
        if($systemUser->isAuthorized()){
            echo "<div class='rightCol2'>
                <input type='text' id='homeDirField' placeholder='/ftp/users/'/>
            </div>";
        }
        ?>
        <div class="rightCol1">
            <input type="password" id="passwordField"/>
        </div>
    </div>
    <form method="post">
        <span class="internav">
            <button type="submit" class="actionbutton">OK</button>
            <button type="cancel" class="actionbutton">Anuluj</button>
        </span>
    </form>
</div>