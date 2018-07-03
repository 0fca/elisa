<?php 
    include_once('constants.php');
    include_once('mvc/controller/helper/AuthController.php');
    include_once('messages.php');   
    include_once('mvc/model/UserModel.php');
    include_once('router.php');
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
    <caption>Rejestracja nowego użytkownika</caption>
    <tr><th>Login:</th><td><input name="username" type="text" size="20" autocomplete="off" /></td></tr>
    <tr><th>Hasło:</th><td><input name="password" size="20" type="password" /></td></tr>
    <tr>
      <td colspan="2" style="text-align: center;" >
        <button class="actionbutton" name="submitted" type="submit" >OK</button>
      </td>
    </tr>
  </table>
</form>
          <div class="msg">
          <?php
            if (isset($_POST["submitted"])) {
              $passHash = hash("sha1", $_POST['password']);
              $userModel = new UserModel($_POST['username'], $passHash, false);
              $result = AuthController::register($userModel);

              if($result){
                setcookie("systemUser", serialize($userModel));
                Router::redirect("/elisa?view=UserListView");
              }else{
                echo "<p style='color: red;'>".$result."</p>"; 
              }
            } 
          ?>
          </div>
</div>
</body>
</html>
?>