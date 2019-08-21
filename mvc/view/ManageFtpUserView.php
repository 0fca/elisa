<?php
    include_once('mvc/controller/ManageFtpUserController.php');
    include_once('mvc/model/FtpUserModel.php');
    include_once('router.php');
    include_once('constants.php');
    include_once('messages.php');
    include_once('mvc/controller/helper/AuthController.php');
    include_once('mvc/controller/helper/PasswordGenerator.php');

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
    $mode = $_SESSION['mode'];
    $systemUserModel = AuthController::authorizeByHash($_COOKIE['userHash'])["model"];
    
    if($systemUserModel !== NULL){
        if($systemUserModel->isAuthorized()){
            $userList = $_SESSION["usersList"];
        }else{
            $mode = "add";
        }
    }else{
        $_SESSION["returnUrl"] = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        Router::redirect("/?view=LoginView");
    }
    
    if($id === NULL && ($mode == "edit" || $mode == "delete")){
        Router::redirect("/?view=FtpUserListView");
    }else{
        $model = new FtpUserModel();
        $manageUserView = new ManageUserView($model);

        if($mode !== "add") {
            $model = ManageFtpUserController::getUserById($id);
            $manageUserView = new ManageUserView($model);
        }
    }
    
?>
    <?php
        $message = $_SESSION["returnMessage"];
        $isErr = $_SESSION['isReturnError'];
            if($message !== NULL){
                if(!$isErr){
                    echo ManageUserView::printInfoMessage($message);
                }else{
                    echo ManageUserView::printErrorMessage($message);
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
        case "unlock":
        echo "<h3>Odblokuj użytkownika FTP $id</h3>";
        break;
    }


    ?>

    <form method="post">
        <?php
            $ftpModel = $manageUserView->getUserModel();

            include_once("mvc/view/FtpPartView_$mode.php");
        ?>
    </form>
    <?php
        try{
            if (isset($_POST["postData"])) {

                if(($_POST['nameField'] !== NULL && $_POST['homeDirField'] !== NULL) || $mode == "lock" || $mode == "unlock"){
                    $_SESSION['action'] = $mode;
                switch($mode){
                    case 'edit':
                        $ftpModel->setName($_POST['nameField']);
                        preg_match('/^((?!.*\/\/.*)(?!.*\/ .*)\/{1}([^\\(){}:\*\?<>\|\"\\"])+)$/', $_POST["homeDirField"], $output_array);
                        if($output_array[0] !== NULL) {
                            $ftpModel->setHomeDir($output_array[0]);
                            $needsPassChange = $_POST["needsPassChange"] == "on" ? true : false;
                            $ftpModel->setUid($_POST["uid"]);
                            $ftpModel->setQuota($_POST["quota"]);
                            $resultModel = ManageFtpUserController::editFtpUser($ftpModel, $needsPassChange, $id);
                            $ftpModel = $resultModel;
                            $_SESSION['returnMessage'] = ED0;
                            $_SESSION['isReturnError'] = false;
                        }else {
                            $_SESSION['returnMessage'] = E102;
                            $_SESSION['isReturnError'] = true;
                        }

                    break;
                    case 'add':
                        $password = PasswordGenerator::generatePassword(16);
                        $ftpModel = $manageUserView->getUserModel();
                        $ftpModel->setName($_POST['nameField']);
                        $ftpModel->setPassHash(hash(hashAlgorithm,$password));

                        preg_match('/^((?!.*\/\/.*)(?!.*\/ .*)\/{1}([^\\(){}:\*\?<>\|\"\\"])+)$/', $_POST["homeDirField"], $output_array);
                        if($output_array[0] !== NULL) {
                            $ftpModel->setHomeDir($output_array[0]);
                            $ftpModel->setQuota($_POST["quota"]);
                            $ftpModel->setRawPassword($password);
                            $_SESSION["ftpAddResult"] = serialize($ftpModel);
                            ManageFtpUserController::addFtpUser($ftpModel);
                            $_SESSION['returnMessage'] = A0;
                            $_SESSION['isReturnError'] = false;
                        }else{
                            $_SESSION['returnMessage'] = E102;
                            $_SESSION['isReturnError'] = true;
                        }
                    break;
                    case 'lock':
                        ManageFtpUserController::lockFtpUser($id);
                        $_SESSION['returnMessage'] = D0;
                        $_SESSION['isReturnError'] = false;
                    break;
                    case 'unlock':
                        ManageFtpUserController::unlockFtpUser($id);
                        $_SESSION['returnMessage'] = D2;
                        $_SESSION['isReturnError'] = false;
                    break;
                }

                if($mode == "add" || $mode == "edit"){
                    if(!$_SESSION["isReturnError"]) {
                        $_SESSION["ftpAddResult"] = serialize($ftpModel);
                        Router::redirect("/?view=FtpAddResultView");
                    }else{
                        Router::redirect("/?view=FtpUserListView");
                    }
                }else{
                    Router::redirect("/?view=FtpUserListView");
                }
                }else{
                    $_SESSION['returnMessage'] = E101;
                    $_SESSION['isReturnError'] = true;
                }
            }

        }catch(ManagementException $ex){
            $_SESSION['returnMessage'] = $ex->getMessage();
            $_SESSION['isReturnError'] = true;
            Router::redirect("/?view=ManageFtpUserView");
        }
    ?>
    <div class="errMsg" id="errDiv" hidden>
    <script>
        document.getElementById("nameField").addEventListener("keydown",function(e){
            if(e.code === "Enter"){
                validateUserData();
            }
        });
    </script>
