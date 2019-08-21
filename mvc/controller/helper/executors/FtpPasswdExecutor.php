<?php
include_once ("mvc/controller/helper/executors/ProcessExecutor.php");
include_once ("mvc/controller/helper/executors/Command.php");
include_once ("mvc/controller/helper/executors/CommandFormatter.php");

final class FtpPasswdExecutor
{
    private $processExecutor;
    public function __construct()
    {

    }

    public function execute($stdinData = ""){
        $result = array();
        if($this->processExecutor !== NULL) {
            $result = $this->processExecutor->execute($stdinData);
        }
        return $result;
    }

    public function setExecutorConfig(ExecutorConfig $executorConfig){
        if($executorConfig !== NULL
            && (strpos($executorConfig->cmd, "ftpasswd") !== 0 || strpos($executorConfig->cmd, "cat") !== 0)) {
            $this->processExecutor = new ProcessExecutor($executorConfig);
        }
    }
}