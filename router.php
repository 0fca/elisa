<?php
    include_once('constants.php');
    include_once('mvc/model/UserModel.php');
    session_start(); 
    $_SESSION['errorCode'] = 404;

    final class Router{
            static public function route($viewName){
                $userid = self::decodeUrl("userid");
                $mode = self::decodeUrl("mode");
                $viewtype = self::decodeUrl("viewtype");

                
                if($userid != NULL){
                    $_SESSION['userid'] = $userid;
                }

                if($mode != NULL){
                    $_SESSION['mode'] = $mode;
                }    

                include(self::recognizeViewName($viewName));
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

            static public function redirect($url){
                ob_start();
                header('Location: '.$url);
                ob_end_flush();
            }

            static private function recognizeViewName($viewName){
                $defaultNames = array();
                $defaultNames["/"] = "main.html";
                $defaultNames["index"] = "main.html";
                $defaultNames["error"] = "error/".$_SESSION['errorCode'].".html";

                $filename = 'mvc/view/'.$viewName.".php";
                if($viewName == NULL){
                    $viewName = "/";
                }

                if(file_exists($filename)){
                    return $filename;
                }else{
                    if(array_key_exists($viewName,$defaultNames)){
                        return "wwwroot/html/".$defaultNames[$viewName];
                    }else{
                        $filename = 'wwwroot/html/'.$viewName.".html";
                        if(file_exists($filename)){
                            return $filename;
                        }else{
                            return "wwwroot/html/error/404.html";
                        }
                    }
                } 
            }
    }
?>