<?php
class ManagementException extends Exception{
    protected $message = "";

    public function __construct($message){
        $this->message = $message;
    }
}
?>