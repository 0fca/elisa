<?php
include_once ('mvc/controller/helper/executors/Command.php');

class ExecutorConfig
{
    public $descriptorspec = array();
    public $cwd = "";
    public $env = array();
    public $cmd = "";
}