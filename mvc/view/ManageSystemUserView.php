<?php
    include_once('mvc/controller/ManageSystemUserController.php');
    include_once('mvc/model/UserModel.php');
    include_once('mvc/model/FtpUserModel.php');
    include_once('router.php');
    include_once('constants.php');
    include_once('mvc/controller/helper/AuthController.php');
    include_once('mvc/controller/exceptions/ManagementException.php');
    include_once('mvc/controller/helper/Logger.php');
    include_once('mvc/controller/helper/Level.php');

    class SystemUserView{
        private $userModel;

        public function __construct($model){
            $this->userModel = $model;
        }

        public function getUserModel(){
            return $this->userModel;
        }

        static public function printErrorMessage($message){
            return "<div class='errMsg'>".
                        "$message".
                    "</div>".
                    "<br/>";
        }

        static public function printInfoMessage($message){
            return "<div class='infMsg'>".
                        "$message".
                    "</div>".
                    "<br/>";
        }
    }

    $systemUserModel = AuthController::authorizeByHash($_COOKIE['userHash'])["model"];
    $id = $_SESSION['userid'];
    $editedModel = NULL;

    if($systemUserModel !== NULL){
        if($systemUserModel->isAuthorized()){
            $userList = $_SESSION["usersList"];
        }else{
            $_SESSION['errorCode'] = 403;
            Router::redirect("/?view=error");
        }
    }else{
        $_SESSION["returnUrl"] = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        Router::redirect("/?view=LoginView");
    }
    $mode = $_SESSION['mode'];

    if($id !== NULL){
        if($mode === 'edit' || $mode === 'delete'){
            $editedModel = DatabaseController::getFromLocalDatabase($id);
            $systemUserView = new SystemUserView($editModel);
        }else{
            $_SESSION['errorCode'] = 400;
            Router::redirect("/?view=error");
        }
    }else{
        Router::redirect("/?view=SystemUserListView");
    }

?>
    <?php
        $message = $_SESSION["returnMessage"];
        $isErr = $_SESSION['isReturnError'];
            if($message !== NULL){
                if(!$isErr){
                    echo UserListView::printInfoMessage($message);
                }else{
                    echo UserListView::printErrorMessage($message);
                }
                $_SESSION['returnMessage'] = NULL;
            }
    
    ?>
    <form method="post">
    <?php 
    switch($mode){
        case "edit":
        echo "<h3>Zarządzaj użytkownikiem systemowym: ";
           
            if($id !== NULL){
                echo $id;
            }
            
        echo "</h3>";
        break;
        case "delete":
        echo "<h3>Usuń użytkownika systemowego $id</h3>";
        break;
    }
    ?>
    <?php 
    if($mode !== 'delete'){
        if($mode == 'add'){
        echo "<div class='content-container'>
            <div class='leftCol1'>
                <p>Nazwa użytkownika:</p>
            </div>
            <div class='leftCol2'>
                <p>Hasło:</p>
            </div>
            <div class='rightCol1'>
                    <input id='nameField' name='nameField' placeholder='Nazwa'/>
            </div>
            <div class='rightCol2'>
                    <input type='password' id='passwordField' name='passwordField' placeholder='Hasło'/>
            </div>
        </div>";
        }

        if($mode == 'edit'){
            echo "<div class='content-container'>
            <div class='leftCol1'>
                <p>Nazwa użytkownika:</p>
            </div>
            <div class='leftCol2'>
                <p>Hasło:</p>
            </div>
            <div class='rightCol1'>
                    <input id='nameField' name='nameField' placeholder='Nazwa' value='{$id}'/>
            </div>
            <div class='rightCol2'>
                    <input type='password' id='passwordField' name='passwordField' placeholder='Hasło' value='{$editedModel->getPassHash()}'/>
            </div>
            </div>";
        }
    }
    ?>
        <span class="internav">
            <button type="submit" class="actionbutton" name="postData">OK</button>
            <a class="button-link" href="/?view=SystemUserListView">Anuluj</a>
        </span>
    </form>
    <?php
            if (isset($_POST["postData"])) {
                try{
                
                switch($mode){
                    case 'edit':
                        $pass = $_POST['passwordField'];
                        if($editedModel->getPassHash() !== $pass){
                            $pass = hash("sha1",$_POST['passwordField']);
                        }
                        $userModel = new UserModel($_POST["nameField"], $pass, $editedModel->isAuthorized());
                        ManageSystemUserController::editSystemUser($userModel, $id);
                        $_SESSION['isReturnErr'] = false;
                        $_SESSION['returnMessage'] = DB0;
                    break;
                    case "delete":
                        ManageSystemUserController::deleteSystemUser($id);
                        $_SESSION['isReturnErr'] = false;
                        $_SESSION['returnMessage'] = DB0;
                    break;
                }
                }catch(ManagementException $ex){
                    $_SESSION['isReturnErr'] = true;
                    $_SESSION['returnMessage'] = $ex->getMessage();
                }finally{
                    Router::redirect("/?view=SystemUserListView");
                }
            }
    ?>
    <script>
        document.getElementById("nameField").addEventListener("keydown",function(e){
            if(e.keyCode == 8){
                validateUserData();
            }
        });
        </script>
