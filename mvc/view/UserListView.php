<?php
    include_once('mvc/model/UserModel.php');
    include_once('mvc/model/FtpUserModel.php');
    include_once('messages.php');

    class UserListView{
        private $users;

        public function __construct($model){
            $this->users = $model;
        }

        public function getUsers(){
            return $this->users;
        }

        public function printContent(){
            $content = "";
            foreach($this->users as $user){

                $content = "<tr>".
                    "<td>".
                        "<p>".$user->getName()."</p>".
                    "</td>".
                "</tr>".
                "<tr>".
                    "<td>".
                        "<p>".$user->getHomeDir()."</p>".
                    "</td>".
                "</tr>".
                "<tr>".
                    "<td>".
                        "<p>".$user->getUid()."</p>".
                    "</td>".
                "</tr>".
                "<tr>".
                    "<td>".
                        "<p>".$user->getGid()."</p>".
                    "</td>".
                "</tr>";
            }
            return $content;
        }

        static public function printErrorMessage($message){
            return "<div class='errMsg'>".
                        "$message".
                    "</div>".
                    "<br/>";
        }

        static public function printInfoMessage($message){
            return "<div class='infMsg'>".
                        "$message".
                    "</div>".
                    "<br/>";
        }
    }

    $systemUser = unserialize($_COOKIE["systemUser"]);
    var_dump($systemUser);
    if($systemUser->isAuthorized()){
        $userList = $_SESSION["usersList"];
    }else{
        //Router::redirect("/elisa/?view=LoginView");
    }
?>
<div class="container">
    <?php
        function localPrint($message){
            echo $message;
        } 
    ?>
    <span class="internav">
        <p style="margin-right: 15px;">Wpisz szukaną frazę:</p>
        <input type='search' id='searchinput'/>
    </span>
    <br/>
    
    <div>
        <table>
            <caption>List użyszkodników serwera FTP</caption>
            <thead>
                <th>
                    Nazwa
                </th>
                <th>
                    Katalog domowy
                </th>
                <th>
                    UID
                </th>
                <th>
                    GID
                </th>
            </thead>
            <tbody>
               <?php
               if($userModel !== NULL){
                    $userListView = new UserListView($userList);
                    echo $userListView->printContent();
                    localPrint(UserListView::printInfoMessage("This shit done well."));//TODO: Implement message for loading from DB.
               }else{
                    localPrint(UserListView::printErrorMessage(I1));
               }    
               ?>
            </tbody>
        </table>
    </div>
</div>    