#!/bin/bash
# Skrypt uzupelniajacy do panelu webowego FTP
# Sprawdza czy kazdy user ma swoj katalog domowy
# 192.168.216.21;Ftp_Infinite_Pl_Proftpd;FtpInfProftpd;Jaw3yEstecU5H8Qep3As
LISTFILE=homedir.list
mysql Ftp_Infinite_Pl_Proftpd -u FtpInfProftpd -pJaw3yEstecU5H8Qep3As -h 192.168.216.21 -se "SELECT homedir,uid FROM usertable WHERE homedir like '/fs/ftp.infinite.pl/idok/%';" > $LISTFILE

while read LINE; do
        DIRECTORY=`echo "$LINE" | awk '{print $1}'`
        if [ ! -d "$DIRECTORY" ]; then
		TIMESTAMP=$(date +"%y/%m/%d-%H:%M:%S")
                echo "$TIMESTAMP> Directory $DIRECTORY not found. Creating..."
                mkdir $DIRECTORY
                FTPUID=`echo "$LINE" | awk '{print $2}'`
                echo "$TIMESTAMP > Changing access rights to directory to $FTPUID:88888..."
                sudo chown -R $FTPUID:88888 $DIRECTORY
		sudo chmod -R g+w $DIRECTORY
        fi
done < $LISTFILE
