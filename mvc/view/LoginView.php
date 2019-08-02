<?php 
    include_once('constants.php');
    include_once('mvc/controller/helper/AuthController.php');
    include_once('mvc/model/UserModel.php');
    include_once('messages.php');   
    include_once('router.php');

    $systemUser = $_COOKIE['userHash'];
    if($systemUser !== NULL){
      $systemUser = NULL;
      setcookie('userHash', NULL);
      $_SESSION["returnUrl"] = NULL;
      Router::redirect('/');
    }
?>   
    <div class="row">    

      <section style="width: 100%;">
      <form name="authUserForm" method="post">
        <h3>Logowanie do sekcji administracyjnej</h3>
        <div class="form-group">
          <label for="username">Login</label>
          <input type="text" id = "username" name="username"></input>
        </div>
        <div class="form-group">
          <label for="password">Hasło</label>
          <input type="password" id="password" name="password"></input>
        </div>
        <div class="form-group">
          <button class="btn btn-success" name="submitted">OK</button>
        </div>
      </section>
      </form>
  </div>
          <div class="infoMsg">
          <?php
            if (isset($_POST["submitted"])) {
              $passHash = hash("sha1", $_POST['password']);
              $result = AuthController::authorize($passHash, $_POST['username']);
              $model =  $result["model"];

              if($model !== NULL && $result["result"]){
                $stringified = serialize($model);
                $userHash = hash("sha1", $stringified);
                $cookie_xpire = cookie_xpire;
                if($cookie_xpire !== NULL){
                  setcookie("userHash", $userHash, time()+$cookie_xpire);
                }else{
                  setcookie("userHash", $userHash);
                }
                
                //var_dump($_SESSION['returnUrl']);
                $url = $_SESSION['returnUrl'] !== NULL ? $_SESSION['returnUrl'] :  $_SERVER['PHP_SELF'];
                Router::redirect($url);
              }else{
                echo "<p style='color: red;'>".$result["message"]."</p>"; 
              }
            } 
          ?>
          </div>