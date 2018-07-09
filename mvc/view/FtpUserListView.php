<?php
    include_once('mvc/model/UserModel.php');
    include_once('mvc/controller/helper/AuthController.php');
    include_once('mvc/controller/ManageFtpUserController.php');
    include_once('mvc/model/FtpUserModel.php');
    include_once('messages.php');

    class UserListView{
        private $users;

        public function __construct($model){
            $this->users = $model;
        }

        public function getUsers(){
            return $this->users;
        }

        public function printContent(){
            $content = "";
            foreach($this->users as $user){

                $content .= "<tr>".
                    "<td>".
                        "<p>".$user->getName()."</p>".
                    "</td>".
                    "<td>".
                        "<p>".$user->getHomeDir()."</p>".
                    "</td>".
                    "<td>".
                        "<p>".$user->getUid()."</p>".
                    "</td>".
                    "<td>".
                        "<p>".$user->getGid()."</p>".
                    "</td>".
                    "<td>".
                        "<button class='actionbutton' type='submit' value='edit' formaction='/elisa?view=ManageFtpUserView&mode=edit&userid={$user->getName()}'>Edytuj</button>".
                        "<button class='actionbutton' type='submit' value='lock' formaction='/elisa?view=ManageFtpUserView&mode=lock&userid={$user->getName()}'>Zablokuj</button>".
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
            try{
                $userList = ManageFtpUserController::listFtpUsers();
            }catch(ManagementException $ex){
                $_SESSION['returnMessage'] = $ex->getMessage();
                $_SESSION['isReturnError'] = true;
            }
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
    <span class="internav">
        <p style="margin-right: 15px;">Wpisz szukaną frazę:</p>
        <input type='search' id='searchField'/>
    </span>
    <br/>
    <form action="<?php print $_SERVER['PHP_SELF']; ?>" name="listForm" method="post">
        <div>
            <button class="actionbutton" type="submit" formaction="/elisa/?view=ManageFtpUserView&mode=add">Dodaj nowego</button>
        <table>
            <caption>List użyszkodników serwera FTP</caption>
            <thead>
                <th>
                    Nazwa
                </th>
                <th>
                    Katalog domowy
                </th>
                <th>
                    UID
                </th>
                <th>
                    GID
                </th>
                <th>
                    Opcje
                </th>
            </thead>
            <tbody>
               <?php
               if($userList !== NULL){
                    $userListView = new UserListView($userList);
                    echo $userListView->printContent();
               }   
               ?>
            </tbody>
        </table>
        </div>
    </form>
</div>    