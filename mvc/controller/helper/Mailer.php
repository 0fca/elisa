<?php 
    include_once("constants.php");

    final class Mailer{
        static public function sendMail($messageContent){
            $messageContent .= "\n\nLast information logged by the system: \n" . Logger::getLastLogs();
            Logger::adminLog("Sending mail...", Level::WARNING, get_called_class());
            $headers = 'From: ' . mailFrom . "\r\n";
            return mail(mailTo, subject, $messageContent, $headers);
        }
    }
?>