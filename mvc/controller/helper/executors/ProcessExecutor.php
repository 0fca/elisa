<?php
include_once ('mvc/controller/helper/executors/ExecutorConfig.php');


final class ProcessExecutor
{
    private $descriptorspec = "";
    private $cwd = "";
    private $env = array();
    private $cmd = "";

    public function __construct(ExecutorConfig $executorConfig)
    {
        $this->descriptorspec = $executorConfig->descriptorspec;
        $this->env = $executorConfig->env;
        $this->cwd = $executorConfig->cwd;
        $this->cmd = $executorConfig->cmd;
    }

    public function execute($stdinData = ""){
        $process = proc_open($this->cmd, $this->descriptorspec, $pipes, $this->cwd, $this->env);
        if (is_resource($process)) {
            $stdout = "";
            $stderr = "";

            if($stdinData !== '') {
                fwrite($pipes[0], $stdinData . "\r\n");
                fclose($pipes[0]);
            }

            if($pipes[1] !== NULL) {
                $stdout = stream_get_contents($pipes[1]);
                fclose($pipes[1]);
            }
            if($pipes[2] !== NULL) {
                $stderr = stream_get_contents($pipes[2]);
                fclose($pipes[2]);
            }

            $return_value = proc_close($process);
            $result = array();
            $result["returnCode"] = $return_value;
            $result["stderr"] = $stderr;
            $result["stdout"] = $stdout;
            return $result;
        }
    }
}