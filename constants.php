<?php
    define("ftpHomeDirs","/fs/ftp.infinite.pl/idok/");
    define("cookie_xpire", 60*10);
    define("mailTo","dasi@infinite.pl");
    define("mailFrom","ftp-panel-elisa");
    define("subject","Elisa FTP Panel - critical error");
    define("configPath","./data/config.popo");
    define("hashAlgorithm", "sha256");
    #define("configObject", unserialize(file_get_contents(configPath)));

    const dbNames = array(0 => "192.168.216.21;Ftp_Infinite_Pl_Proftpd;FtpInfProftpd;Jaw3yEstecU5H8Qep3As",
                          1 => "192.168.216.22;Ftp_Infinite_Pl_Proftpd;FtpInfProftpd;Jaw3yEstecU5H8Qep3As",
                          2 => "192.168.216.23;Ftp_Infinite_Pl_Proftpd;FtpInfProftpd;Jaw3yEstecU5H8Qep3As",
                          3 => "192.168.216.24;Ftp_Infinite_Pl_Proftpd;FtpInfProftpd;Jaw3yEstecU5H8Qep3As",
                          4 => "192.168.216.25;Ftp_Infinite_Pl_Proftpd;FtpInfProftpd;Jaw3yEstecU5H8Qep3As",
                          5 => "192.168.216.10;Ftp_Infinite_Pl_Proftpd;FtpInfProftpd;Jaw3yEstecU5H8Qep3As",
                          6 => "192.168.216.11;Ftp_Infinite_Pl_Proftpd;FtpInfProftpd;Jaw3yEstecU5H8Qep3As",
                          7 => "192.168.216.12;Ftp_Infinite_Pl_Proftpd;FtpInfProftpd;Jaw3yEstecU5H8Qep3As");
?>