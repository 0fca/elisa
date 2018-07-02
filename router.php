<?php
    include_once('constants.php');
    session_start(); 

    final class Router{
            static public function route($viewName){//rebuild this to param function
                $userid = self::decodeUrl("userid");
                $mode = self::decodeUrl("mode");
                $viewtype = self::decodeUrl("viewtype");

                $filename = 'mvc/view/'.$viewName.".php";
                if($userid != NULL){
                    $_SESSION['userid'] = $userid;
                }

                if($mode != NULL){
                    $_SESSION['mode'] = $mode;
                }    

                if(file_exists($filename)){
                    include($filename);
                }else{
                    if($viewName == "/" || $viewName === NULL){
                        include("wwwroot/html/main.html");
                    }else{
                        $filename = 'wwwroot/html/'.$viewName.".html";
                        include($filename);
                    }
                }  
            }  

            static private function decodeUrl($phrase){
                $actual_link = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
                $parts = parse_url($actual_link);
                parse_str($parts['query'], $query);
                if(array_key_exists($phrase,$query)){
                    $retVal = $query[$phrase];
                    return $retVal; 
                }
                return NULL;
            }
    }
?>