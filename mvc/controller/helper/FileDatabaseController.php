<?php
include_once ("mvc/model/FtpUserModel.php");
include_once ("mvc/controller/helper/executors/FtpPasswdExecutor.php");
include_once "ConfigurationProvider.php";
ConfigurationProvider::loadConfiguration();

final class FileDatabaseController
{
    static public function listFileUsers(){
        $passwdExecutor = new FtpPasswdExecutor();
        $passwdExecutor->setExecutorConfig(ConfigurationProvider::getConfigurationField("executorsConfig")["ListingConfig"]);
        $result = $passwdExecutor->execute();
        $ftpUsersArray = array();

        if($result['returnCode'] === 0){
            $stdoutLines = explode("\n",trim($result["stdout"]));

            foreach ($stdoutLines as $line){
                $split = explode(":", $line);
                $ftpUserModel = new FtpUserModel();
                $ftpUserModel->setName($split[0]);
                $ftpUserModel->setPassHash($split[1]);
                $ftpUserModel->setUid($split[2]);
                $ftpUserModel->setGid($split[3]);
                $ftpUserModel->setHomeDir($split[5]);
                $ftpUsersArray[$split[0]] = $ftpUserModel;
            }
        }
        return $ftpUsersArray;
    }

    static public function addFileUser(FtpUserModel $ftpUserModel, string $oldId){
        $passwdExecutor = new FtpPasswdExecutor();
        $addConfig = ConfigurationProvider::getConfigurationField("executorsConfig")["AddConfig"];
        $file = ConfigurationProvider::getConfigurationField("file");
        $addConfig->cmd = sprintf($addConfig->cmd, $file ,$ftpUserModel->getName() === $oldId ? $ftpUserModel->getName() : $oldId, $ftpUserModel->getHomeDir(),$ftpUserModel->getUid(), $ftpUserModel->getGid());
        $passwdExecutor->setExecutorConfig($addConfig);

        $result = $passwdExecutor->execute($ftpUserModel->getRawPassword());
        return $result;
    }

    static public function blockFileUser(FtpUserModel $ftpUserModel){
        $passwdExecutor = new FtpPasswdExecutor();
        $addConfig = ConfigurationProvider::getConfigurationField("executorsConfig")["AddConfig"];
        $file = ConfigurationProvider::getConfigurationField("file");
        $addConfig->cmd = sprintf($addConfig->cmd, $file ,$ftpUserModel->getName(), "/sbin/nologin" ,$ftpUserModel->getUid(), $ftpUserModel->getGid());
        $passwdExecutor->setExecutorConfig($addConfig);

        $result = $passwdExecutor->execute($ftpUserModel->getRawPassword());
        return $result;
    }

    static public function unlockFileUser(FtpUserModel $ftpUserModel){
        $passwdExecutor = new FtpPasswdExecutor();
        $addConfig = ConfigurationProvider::getConfigurationField("executorsConfig")["AddConfig"];
        $file = ConfigurationProvider::getConfigurationField("file");
        $addConfig->cmd = sprintf($addConfig->cmd, $file, $ftpUserModel->getName(), ftpHomeDirs. "/" . $ftpUserModel->getName() ,$ftpUserModel->getUid(), $ftpUserModel->getGid());
        $passwdExecutor->setExecutorConfig($addConfig);

        $result = $passwdExecutor->execute($ftpUserModel->getRawPassword());
        return $result;
    }
}