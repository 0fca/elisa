<?php 
    include_once('constants.php');
    include_once('mvc/controller/helper/AuthController.php');
    include_once('messages.php');   
    include_once('mvc/model/UserModel.php');
    include_once('router.php');

    $systemUserModel = AuthController::authorizeByHash($_COOKIE['userHash'])["model"];
    
    if($systemUserModel !== NULL){
        if(!$systemUserModel->isAuthorized()){
          $_SESSION['errorCode'] = 403;
          Router::redirect("/?view=error");
        }
    }else{
      $_SESSION["returnUrl"] = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
      Router::redirect("/?view=LoginView");
    }
?>
 <div class="row">    

<section style="width: 100%;">
<form name="authUserForm" method="post">
  <h3>Rejestrowanie nowego użytkownika systemowego</h3>
  <div class="form-group">
    <label for="nameField">Login</label>
    <input type="text" id = "nameField" name="username" oninput="validateUserData();"></input>
  </div>
  <div class="form-group">
    <label for="password">Hasło</label>
    <input type="password" id="password" name="password"></input>
  </div>
  <div class="form-group">
    <label for="is_auth">Autoryzowany</label>
    <input name="is_auth" id="is_auth" type="checkbox" />
  </div>
  <div class="form-group">
    <button class="btn btn-success" name="postData" id="postData">OK</button>
    <a class="btn btn-danger" href="/?view=SystemUserListView" class="button-link">Anuluj</a>
  </div>
</section>
</form>
</div>
          <div class="msg">
          <?php
            if (isset($_POST["postData"])) {
              $passHash = hash("sha1", $_POST['password']);
              $userModel = new UserModel($_POST['username'], $passHash, $_POST["is_auth"] == "on" ? true : false);

              AuthController::register($userModel);
              Router::redirect("/index.php?view=SystemUserListView");
            } 
          ?>
          </div>
      <div class="errMsg" id="errDiv" hidden>
      <script>
        document.getElementById("nameField").addEventListener("keydown",function(e){
            if(e.keyCode == 8){
                validateUserData();
            }
        });
      </script>