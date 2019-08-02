<?php
    include_once("mvc/controller/helper/AuthController.php");
    $systemUser = $_COOKIE["userHash"];
    $_SESSION['returnUrl'] = "elisa/?view=SystemUserListView";
?>

<!DOCTYPE html>

<html>
<head>
    <meta charset="UTF-8"/>
    <title>FTP - Panel administracyjny</title>
    <link rel="stylesheet" type="text/css" href="wwwroot/css/main.css">
    <link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet"> 
    <link href="https://fonts.googleapis.com/css?family=Oxygen" rel="stylesheet"> 
    <link rel="icon" href="wwwroot/images/favicon-sml-blu.png">
    <script src="wwwroot/js/main.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" rel="style-font">
</head>
    <body>
    <header>
    <nav class="navbar navbar-expand-md navbar-dark fixed-top bg-inf">
            <a class="navbutton" href=<?php echo $_SERVER["PHP_SELF"]?>>
                <img src="wwwroot/images/logooftheyear2018.png"/>
            </a>
            <div class="navigation navpos">
                <ul class="navigation__list">
                    <li>
                        <a class="navbutton" href=<?php echo $_SERVER["PHP_SELF"] . "?view=FtpUserListView";?>>Zarządzaj użytkownikami FTP</a>
                    </li>
                    <?php 
                        if($systemUser !== NULL){
                            $model = AuthController::authorizeByHash($systemUser)["model"];
                            if($model->isAuthorized()){
                                echo "<li>
                                    <a class='navbutton' href={$_SERVER['PHP_SELF']}?view=SystemUserListView>Zarządzaj użytkownikami systemu</a>
                                </li>";
                            }
                        }
                    ?>
                    <li>
                        <a class="navbutton" href=<?php echo $_SERVER["PHP_SELF"] . "?view=Help";?>>Pomoc</a>
                    </li>
                    <li>
                        <a class="navbutton" href=<?php echo $_SERVER["PHP_SELF"] . "?view=About";?>>O aplikacji</a>
                    </li>
                </ul>
                <p class="userText"><?php echo $model === NULL ? "" : "Zalogowano jako ".$model->getUserName();?></p>
                <a class="navbutton" href=<?php echo $_SERVER["PHP_SELF"] . "?view=LoginView"?>><?php echo $systemUser === NULL ? "Zaloguj" : "Wyloguj"; ?></a>
            </div>
    </nav>
    </header>
    <main class="container body-content">
    <?php
        include_once('router.php');

        Router::route($_GET['view']);
    ?>
    </main>
    
    </body>

    <footer>
        <hr>
        Elisa - Panel administracyjny serwera FTP
    </footer>
</html>