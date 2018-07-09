<?php
    include_once('mvc/model/UserModel.php');
    include_once('mvc/controller/helper/AuthController.php');
    include_once('mvc/controller/helper/DatabaseController.php');
    include_once('mvc/controller/helper/FilesystemHelper.php');
    include_once('mvc/model/FtpUserModel.php');
    include_once('messages.php');

    class SystemUserListView{
        private $users;

        public function __construct($model){
            $this->users = $model;
        }

        public function getSystemUsers(){
            return $this->users;
        }

        public function printContent(){
            $content = "";
            foreach($this->users as $user){
                $userName = explode(".",$user)[0];
                $content .= "<tr>".
                    "<td>".
                        "<p>".$userName."</p>".
                    "</td>".
                    "<td>".
                        "<button class='actionbutton' type='submit' formaction='/elisa/?view=ManageSystemUserView&mode=edit'>Edytuj</button>".
                        "<button class='actionbutton' type='submit' formaction='/elisa/?view=ManageSystemUserView&mode=delete&userid=$userName'>Usuń</button>".
                    "</td>".
                "</tr>";
            }
            return $content;
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
    
    if($systemUserModel !== NULL){
        if($systemUserModel->isAuthorized()){
            $userList = DatabaseController::listLocalUsers();
        }else{
            $_SESSION['errorCode'] = 403;
            Router::redirect("/elisa/?view=error");
        }
    }else{
        Router::redirect("/elisa/?view=LoginView");
    }
?>
<div class="container">
    <?php
        if($_SESSION['returnMessage'] !== NULL){
            if(!$_SESSION['isReturnError']){
                echo SystemUserListView::printInfoMessage($_SESSION['returnMessage']);
            }else{
                echo SystemUserListView::printErrorMessage($_SESSION['returnMessage']);
            }
            $_SESSION['returnMessage'] = NULL;
        }
    ?>
    <div>

    <form action="<?php print $_SERVER['PHP_SELF']; ?>" name="listForm" method="post">
        <button class="actionbutton" type="submit" formaction="/elisa/?view=RegisterView">Dodaj nowego</button>
        <table>
            <caption>List użyszkodników systemu</caption>
            <thead>
                <th>
                    Nazwa
                </th>
                <th>
                    Opcje
                </th>
            </thead>
            <tbody>
               <?php
               if($userList !== NULL){
                    $userListView = new SystemUserListView($userList);
                    echo $userListView->printContent();
               }  
               ?>
            </tbody>
        </table>
        </form>
    </div>
</div>    