<?php
    include_once("mvc/controller/helper/AuthController.php");
    include_once("ConfigurationProvider.php");
    $systemUser = $_COOKIE["userHash"];
    $_SESSION['returnUrl'] = "elisa/?view=SystemUserListView";
    $configArray = unserialize(file_get_contents(configPath));

    $logo = "wwwroot/images/logooftheyear2018.png";
    if($configArray["datasource"] === "file"){
        $logo = "wwwroot/images/logooftheyear2019.png";
    }

?>

<!DOCTYPE html>

<html lang="pl">
<head>
    <meta charset="UTF-8"/>
    <title>FTP - Panel administracyjny</title>
    <link rel="stylesheet" type="text/css" href="wwwroot/css/main.css">
    <link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet"> 
    <link href="https://fonts.googleapis.com/css?family=Oxygen" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons"
          rel="stylesheet">
    <link rel="icon" href="wwwroot/images/favicon-sml-blu.png">
    <script src="wwwroot/js/main.js"></script>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/css/bootstrap.min.css" integrity="sha384-GJzZqFGwb1QTTN6wy59ffF1BuGJpLSa9DkKMp0DgiMDm4iYMj70gZWKYbI706tWS" crossorigin="anonymous">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" rel="style-font">
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/js/bootstrap.min.js" integrity="sha384-B0UglyR+jN6CkvvICOB2joaf5I4l3gm9GU6Hc1og6Ls7i6U/mkkaduKaBhlAXv9k" crossorigin="anonymous"></script>
</head>
    <body>
    <header>
    <nav class="navbar navbar-expand-md navbar-dark fixed-top bg-inf">
            <a class="navbar-brand" href=<?php echo $_SERVER["PHP_SELF"]?>>
                <img alt="InfiniteLogo" title="<?php echo $configArray["datasource"] !== null ?  $configArray["datasource"] :  "No datasource configured";?>" width="60" height="52" src=<?php echo $logo; ?> />
            </a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <div class="navigation">
                <ul class="navbar-nav mr-auto">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle navbutton" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Zarządzaj...
                        </a>
                        <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                            <a class="dropdown-item" href=<?php echo $_SERVER["PHP_SELF"] . "?view=FtpUserListView";?>>... użytkownikami FTP</a>
                            <?php
                            if($systemUser !== NULL){
                                $model = AuthController::authorizeByHash($systemUser)["model"];
                                if($model->isAuthorized()){
                                    echo "<a class='dropdown-item' href={$_SERVER['PHP_SELF']}?view=SystemUserListView>... użytkownikami systemu</a>";
                                }
                            }
                            ?>
                        </div>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link navbutton" href=<?php echo $_SERVER["PHP_SELF"] . "?view=Help";?>>Pomoc</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link navbutton" href=<?php echo $_SERVER["PHP_SELF"] . "?view=About";?>>O aplikacji</a>
                    </li>

                </ul>
                <div class="form-inline my-2 my-lg-0">
                    <?php

                        $hint = $model !== NULL ? 'Zalogowano jako '.$model->getUserName() : "";

                        $accountIcon = "<i class='material-icons' title='.$hint.'>" .
                                        "account_circle" .
                                        "</i>";
                    ?>
                    <p class="userText"><?php echo $model === NULL ? "" : $accountIcon?></p>
                    <a class="nav-link navbutton" href=<?php echo $_SERVER["PHP_SELF"] . "?view=LoginView"?>><?php echo $systemUser === NULL ? "Zaloguj" : "Wyloguj"; ?></a>
                </div>

            </div>
        </div>
    </nav>
    </header>
    <main class="container body-content">
    <?php
        include_once('router.php');
        $view = "prompt";

        if(file_exists(configPath)){
            $view = $_GET["view"];
        }elseif($_GET["view"] === "DumpPoco"){
            $view = "DumpPoco";
        }
        Router::route($view);
    ?>
    </main>
    
    </body>

    <footer>
        <hr>
        Elisa - Panel administracyjny serwera FTP
    </footer>
</html>