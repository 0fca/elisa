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

        public function printOptionContent(){
            $content = "";
            foreach($this->users as $user){
                $userName = explode(".",$user)[0];
                $content .= "<option>$userName</option>";
            }
            return $content;
        }

        public function printTableContent(){
            $content = "";
            if(sizeof($this->users) == 0){
                return UserListView::printInfoMessage(DB0A);
            }
            $i = 0;
            foreach($this->users as $user){
                $lockOpt = "<a class='btn btn-primary mr-4' href='{$_SERVER['PHP_SELF']}?view=ManageFtpUserView&mode=edit&userid={$user->getName()}'>Edytuj</a>".
                           "<a class='btn btn-danger' href='{$_SERVER['PHP_SELF']}?view=ManageFtpUserView&mode=lock&userid={$user->getName()}'>Zablokuj</a>";
                if($user->getHomeDir() == "/sbin/nologin"){
                    $lockOpt = "<a class='btn btn-success' href='{$_SERVER['PHP_SELF']}?view=ManageFtpUserView&mode=unlock&userid={$user->getName()}'>Odblokuj</a>";
                }

                $content .= "<tr>".
                    "<th scope='row'>$i</th>".
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
                        "<p>".($user->getQuota() !== NULL ? $user->getQuota() : "Ni mo")."</p>".
                    "</td>".
                    "<td>".
                        $lockOpt.
                    "</td>".
                "</tr>";
                $i++;
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
                $systemUserListView = new UserListView($userList);
            }catch(ManagementException $ex){
                $_SESSION['returnMessage'] = $ex->getMessage();
                $_SESSION['isReturnError'] = true;
            }
        }else{
            $_SESSION['errorCode'] = 403;
            Router::redirect("/?view=ManageFtpUserView&mode=add");
        }
    }else{
        $_SESSION["returnUrl"] = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        Router::redirect("/?view=LoginView");
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
    
<div class="row justify-content-center pt-5">
    <div class="col-14 width-fit">
      <div class="row pb-4">
        <div class="col-12">
          <h1 class="h2">Lista użytkowników FTP</h1>
          <a class="btn btn-success" href=<?php echo $_SERVER['PHP_SELF'] . "?view=ManageFtpUserView&mode=add";?>>Dodaj nowego</a>
          <span class="internav">
                <p style="margin-right: 15px;">Wpisz szukaną frazę:</p>
                <input type='search' id='searchField' oninput="filter();"/>
            </span>
        </div>
      </div>
      <div class="row width-fit">
          <table class="table table-hover table-bordered text-center">
            <thead class="thead-dark">
              <tr>
                <th scope="col">#</th>
                <th scope="col">Nazwa</th>
                <th scope="col">Katalog domowy</th>
                <th scope="col">UID</th>
                <th scope="col">GID</th>
                <th scope="col">Quota (MB)</th>
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

<script>
    document.getElementById("searchField").addEventListener("keydown",function(e){
        if(e.code === "Enter"){
            filter();
        }
    });
</script> 
