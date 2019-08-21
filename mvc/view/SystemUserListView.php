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

        public function printTableContent(){
            $content = "";
            $index = 0;
            foreach($this->users as $user){
                $userName = explode(".auth",$user)[0];
                $content .= "<tr>
                <th scope='row'>$index</th>
                <td>$userName</td>
                <td>
                  <a class='btn btn-primary' href='{$_SERVER['PHP_SELF']}?view=ManageSystemUserView&mode=edit&userid={$userName}'>Edytuj</a>";
                  if($userName !== "admin"){
                    $content .= "<a class='btn btn-danger' href='{$_SERVER['PHP_SELF']}?view=ManageSystemUserView&mode=delete&userid={$userName}'>Usuń</a>";
                  }
                $content .= "</td>
              </tr>";
              $index++;
            }
            return $content;
        }
        public function printOptionContent(){
            $content = "";
            foreach($this->users as $user){
                $userName = explode(".auth",$user)[0];
                $content .= "<option>$userName</option>";
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
            $systemUserListView = new SystemUserListView($userList);
        }else{
            $_SESSION['errorCode'] = 403;
            Router::redirect("/?view=error");
        }
    }else{
        $_SESSION["returnUrl"] = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        Router::redirect("/?view=LoginView");
    }
?>
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
  <div class="row justify-content-center pt-5">
    <div class="col-14 width-fit">
      <div class="row pb-4">
        <div class="col-12">
          <h1 class="h2">Lista użytkowników systemowych</h1>
          <a href="/?view=RegisterView" class="btn btn-success" type="submit">Dodaj nowego</a>
        </div>
      </div>
      <div class="row width-fit">
          <table class="table table-hover table-bordered text-center">
            <thead class="thead-dark">
              <tr>
                <th scope="col">#</th>
                <th scope="col">Nazwa</th>
                <th scope="col">Akcje</th>
              </tr>
            </thead>
            <tbody>
               <?php
                if($systemUserListView !== NULL){
                    echo $systemUserListView->printTableContent(); 
                }
               ?>
            </tbody>
          </table>
      </div>
    </div>
  </div>
    </div>