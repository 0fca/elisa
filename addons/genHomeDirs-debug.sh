#!/bin/bash
# Skrypt uzupelniajacy do panelu webowego FTP
# Sprawdza czy kazdy user ma swoj katalog domowy
# 192.168.216.21;Ftp_Infinite_Pl_Proftpd;FtpInfProftpd;Jaw3yEstecU5H8Qep3As
TIMESTAMP=$(date +"%y/%m/%d-%H:%M:%S")
echo "> Starting the script at $TIMESTAMP"
LISTFILE=homedir.list
echo "> Running the SQL query..."
mysql Ftp_Infinite_Pl_Proftpd -u FtpInfProftpd -pJaw3yEstecU5H8Qep3As -h 192.168.216.21 -se "SELECT homedir,uid FROM usertable WHERE homedir like '/fs/ftp.infinite.pl/idok/%';" > $LISTFILE

echo "> Searching for missing directories..."
while read LINE; do
        DIRECTORY=`echo "$LINE" | awk '{print $1}'`
        if [ ! -d "$DIRECTORY" ]; then
                echo "> Directory $DIRECTORY not found. Creating..."
                mkdir $DIRECTORY
                echo " > Getting the proper UID..."
                FTPUID=`echo "$LINE" | awk '{print $2}'`
                echo " > Changing access rights to directory to $FTPUID:88888..."
                sudo chown -R $FTPUID:88888 $DIRECTORY
                echo " > Adding group write permissions..."
		sudo chmod -R g+w $DIRECTORY
                echo " > Done."
        fi
done < $LISTFILE
echo "> Done."
