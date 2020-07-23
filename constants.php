<?php
    define("ftpHomeDirs","/fs/ftp.infinite.pl/idok/");
    define("cookie_xpire", 60*10);
    define("mailTo","dasi@infinite.pl");
    define("mailFrom","ftp-panel-elisa");
    define("subject","Elisa FTP Panel - critical error");
    define("configPath","./data/config.popo");
    define("hashAlgorithm", "sha256");
    #define("configObject", unserialize(file_get_contents(configPath)));

    const dbNames = array();
?>
