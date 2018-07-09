<?php
    include_once('mvc/controller/ManageFtpUserController.php');
    include_once('mvc/model/FtpUserModel.php');
    include_once('router.php');
    include_once('constants.php');
    include_once('mvc/controller/helper/AuthController.php');

    class ManageUserView{
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

    $id = $_SESSION['userid'];
    $systemUserModel = AuthController::authorizeByHash($_COOKIE['userHash'])["model"];
    
    if($systemUserModel !== NULL){
        if($systemUserModel->isAuthorized()){
            $userList = $_SESSION["usersList"];
        }else{
            //$_SESSION['errorCode'] = 403;
            Router::redirect("/elisa/?view=ManageFtpUserView&mode=add");
        }
    }else{
        Router::redirect("/elisa/?view=LoginView");
    }
    
    if($id === NULL){
        Router::redirect("/elisa?view=FtpUserListView");
    }else{
        $model = ManageFtpUserController::getUserById($id);
        $manageUserView = new ManageUserView($model);
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
    <?php
    switch($mode){
        case "add":
        echo "<h3>Dodaj użytkownika FTP</h3>";
        break;
        case "edit":
        echo "<h3>Zarządzaj użytkownikiem FTP: ";
           
            if($id !== NULL){
                echo $id;
            }
            
        echo "</h3>";
        break;
        case "lock":
        echo "<h3>Zablokuj użytkownika FTP $id</h3>";
        break;
    }
    ?>

    <form method="post">
        <?php
            include("mvc/view/FtpPartView_{$mode}.php"); 
        ?>
    </form>
    <?php
        try{
            if (isset($_POST["postData"])) {
                if($_POST['nameField'] !== NULL && $_POST['passwordField'] !== NULL && $_POST['homeDirField'] !== NULL){
                $mode = $_SESSION['mode'];
                switch($mode){
                    case 'edit':
                        $ftpModel = $manageUserView->getUserModel();
                        $ftpModel->setName($_POST['nameField']);
                        $ftpModel->setPassHash(hash("sha1",$_POST['passwordField']));
                        $ftpModel->setHomeDir($_POST['homeDirField']);
                        $result = ManageFtpUserController::editFtpUser($ftpModel);

                        $_SESSION['returnMessage'] = ED0;
                        $_SESSION['isReturnError'] = false;
                    break;
                    case 'add':
                        $ftpModel = $manageUserView->getUserModel();
                        $ftpModel->setName($_POST['nameField']);
                        $ftpModel->setPassHash(hash("sha1",$_POST['passwordField']));
                        $ftpModel->setHomeDir($_POST['homeDirField']);
                        ManageFtpUserController::addFtpUser($ftpModel);
                        $_SESSION['returnMessage'] = A0;
                        $_SESSION['isReturnError'] = false;
                    break;
                    case 'lock':
                        throw new UnsupportedOperationException("System aktualnie nie wspiera tej operacji.");
                    break;
                }
                Router::redirect("/elisa/?view=FtpUserListView");
                }else{
                    $_SESSION['returnMessage'] = E101;
                    $_SESSION['isReturnError'] = true;
                }
            }
        }catch(ManagementException $ex){
            $_SESSION['returnMessage'] = $ex->getMessage();
            $_SESSION['isReturnError'] = true;
        }
    ?>
</div>