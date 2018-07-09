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
      Router::redirect('/elisa');
    }
?>
<!DOCTYPE html>

<html>
<head>
    <meta charset="UTF-8"/>
    <title>Panel administratora</title>
    <link rel="stylesheet" type="text/css" href="style.css">
    <link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet"> 
    <link href="https://fonts.googleapis.com/css?family=Oxygen" rel="stylesheet"> 
    <link rel="icon" href="data/favicon-sml-blu.png">
    <script src="js/script.js"></script>
</head>
<body>    
<div class="container">
    <form name="authUserForm" method="post">
        
    <table>
    <caption>Logowanie do sekcji administracyjnej!</caption>
    <tr><th>Login:</th><td><input name="username" type="text" size="20" autocomplete="off" /></td></tr>
    <tr><th>Hasło:</th><td><input name="password" size="20" type="password" /></td></tr>
    <tr>
      <td colspan="2" style="text-align: center;" >
        <button class="actionbutton" name="submitted" type="submit" >OK</button>
      </td>
    </tr>
  </table>
</form>
          <div class="infoMsg">
          <?php
            if (isset($_POST["submitted"])) {
              $passHash = hash("sha1", $_POST['password']);
              $result = AuthController::authorize($passHash, $_POST['username']);
              $model =  $result["model"];
              
              if($model !== NULL){
                $stringified = serialize($model);
                $userHash = hash("sha1", $stringified);
                $cookie_xpire = cookie_xpire;
                if($cookie_xpire !== NULL){
                  setcookie("userHash", $userHash, time()+$cookie_xpire);
                }else{
                  setcookie("userHash", $userHash);
                }
                

                Router::redirect("/elisa?view=FtpUserListView");
              }else{
                echo "<p style='color: red;'>".$result["message"]."</p>"; 
              }
            } 
          ?>
          </div>
</div>
</body>
</html>