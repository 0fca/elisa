<!DOCTYPE html>

<html>
<head>
    <meta charset="UTF-8"/>
    <title>Panel administracyjny FTP</title>
    <link rel="stylesheet" type="text/css" href="wwwroot/css/main.css">
    <link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet"> 
    <link href="https://fonts.googleapis.com/css?family=Oxygen" rel="stylesheet"> 
    <link rel="icon" href="wwwroot/images/favicon-sml-blu.png">
    <script src="wwwroot/js/script.js"></script>
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
                        <button class="navbutton" type="submit">Główna</button>
                    </li>
                    <li>
                        <button class="navbutton" type="submit" name="view" value="UserListView">Zarządzaj użyszkodnikami</button>
                    </li>
                    <li>
                        <button class="navbutton" type="submit" name="view" value="aboutView">O aplikacji</button>
                    </li>
                </ul>
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