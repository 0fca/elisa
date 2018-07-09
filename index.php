<?php
    include_once("mvc/controller/helper/AuthController.php");
    $systemUser = $_COOKIE["userHash"];
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
</head>
    
    <form method="get">
        <header>
            <div id="navbar">
            <nav class="navigation">
                <button class="navbutton" type="submit">
                    <img src="wwwroot/images/logooftheyear2018.png"/>
                </button>
                <ul class="navigation__list">
                    <li>
                        <button class="navbutton" type="submit" name="view" value="FtpUserListView">Zarządzaj użyszkodnikami FTP</button>
                    </li>
                    <?php 
                        if($systemUser !== NULL){
                            $model = AuthController::authorizeByHash($systemUser)["model"];
                            if($model->isAuthorized()){
                                echo "<li>
                                    <button class='navbutton' type='submit' name='view' value='SystemUserListView'>Zarządzaj użyszkodnikami systemu</button>
                                </li>";
                            }
                        }
                    ?>
                    <li>
                        <button class="navbutton" type="submit" name="view" value="About">O aplikacji</button>
                    </li>
                </ul>
                <button class="navbutton" type="submit" name="view" value="LoginView"><?php echo $systemUser === NULL ? "Zaloguj" : "Wyloguj"; ?></button>
            </nav>
            </div>
        </header>
    </form>
    <body>

    <?php
        include_once('router.php');

        Router::route($_GET['view']);
    ?>
    </body>
</html>