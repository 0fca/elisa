<?php
include_once ("mvc/controller/helper/executors/Command.php");


final class CommandFormatter
{
    static public function format(Command $command){
        $returnString = $command->getBinary() . " ";

        foreach ($command->getArgs() as $arg){
            $returnString .= $arg . " ";
        }
        return trim($returnString);
    }
}