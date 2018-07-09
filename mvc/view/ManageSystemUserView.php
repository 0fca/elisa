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

    if($systemUserModel !== NULL){
        if($systemUserModel->isAuthorized()){
            $userList = $_SESSION["usersList"];
        }else{
            $_SESSION['errorCode'] = 403;
            Router::redirect("/elisa/?view=error");
        }
    }else{
        Router::redirect("/elisa/?view=LoginView");
    }
    $mode = $_SESSION['mode'];

    if($id !== NULL){
        if($mode === 'edit' || $mode === 'delete'){
            $model = new UserModel($id,"",false);
            $systemUserView = new SystemUserView($model);
        }else{
            $_SESSION['errorCode'] = 400;
            Router::redirect("/elisa/?view=error");
        }
    }else{
        Router::redirect("/elisa?view=SystemUserListView");
    }

?>
<div class="container">

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
    <form method="post" action="/elisa/?view=SystemUserListView">
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
        echo "<div class='content-container'>
            <div class='leftCol1'>
                <p>Nazwa użyszkodnika:</p>
            </div>
            <div class='leftCol2'>
                <p>Hasło:</p>
            </div>
            <div class='rightCol2'>
                <input type='password' id='passwordField' name='passwordField' placeholder='Hasło'/>
            </div>
        </div>";
    }
    ?>
        <span class="internav">
            <button type="submit" class="actionbutton" name="postData">OK</button>
            <button type="cancel" class="actionbutton">Anuluj</button>
        </span>
    </form>
    <?php
            if (isset($_POST["postData"])) {
                try{
                
                switch($mode){
                    case 'edit':
                        $userModel = new UserModel($_POST["nameField"], $_POST["passwordField"], false);
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
                }
            }
    ?>
</div>