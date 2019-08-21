<?php
include_once "mvc/controller/helper/executors/ExecutorConfig.php";
include_once "mvc/controller/helper/executors/FtpPasswdExecutor.php";
include_once "mvc/controller/helper/executors/CommandFormatter.php";
include_once "ConfigurationProvider.php";
include_once "mvc/model/UserModel.php";

$systemUserModel = AuthController::authorizeByHash($_COOKIE['userHash'])["model"];

if(file_exists(configPath)) {
    if ($systemUserModel !== NULL) {
        if (!$systemUserModel->isAuthorized()) {
            $_SESSION['errorCode'] = 404;
            Router::redirect("/?view=404");
        }
    } else {
        $_SESSION["returnUrl"] = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        Router::redirect("/?view=LoginView");
    }
}


$ftpdPasswdFilePath = "/var/www/html/data";
$datasource = "file";

if(isset($_POST["p"])
&& isset($_POST["ds"])) {
    $ftpdPasswdFilePath = $_POST["p"];
    $datasource = $_POST["ds"];
    $message = "";

    preg_match('/^((?!.*\/\/.*)(?!.*\/ .*)\/{1}([^\\(){}:\*\?<>\|\"\\"])+)$/' , $ftpdPasswdFilePath, $output_array);

    if($output_array[0] === NULL){
        $message = "Path is invalid.";
    }else {

        $ftpdPasswdFilePath = $output_array[0];


        $executorConfig = new ExecutorConfig();
        $cmd = new Command();
        $cmd->setBinary("cat");
        $cmd->setArgs(array("$ftpdPasswdFilePath/ftpd.passwd"));
        $executorConfig->cmd = CommandFormatter::format($cmd);
        $executorConfig->cwd = $ftpdPasswdFilePath;
        $executorConfig->descriptorspec = array(
            0 => array("pipe", "r"),
            1 => array("pipe", "w"),
            2 => array("pipe", "w")
        );

        $addConfig = new ExecutorConfig();
        $cmd = new Command();
        $cmd->setBinary("ftpasswd");

        $cmd->setArgs(array(
            "--file=%s",
            "--stdin",
            "--name=%s",
            "--passwd",
            "--home=%s",
            "--uid=%d",
            "--gid=%d",
            "--shell=/bin/sh",
            "--" . hashAlgorithm
        ));

        $addConfig->cmd = CommandFormatter::format($cmd);
        $addConfig->cwd = $ftpdPasswdFilePath;
        $addConfig->descriptorspec = array(
            0 => array("pipe", "r"),
            1 => array("pipe", "w"),
            2 => array("pipe", "w")
        );

        $out = array(
            "datasource" => $datasource,
            "file" => "$ftpdPasswdFilePath/ftpd.passwd",
            "executorsConfig" => array(
                "ListingConfig" => $executorConfig,
                "AddConfig" => $addConfig
            )
        );


        $configObject = serialize($out);
        file_put_contents("./data/config.popo", $configObject);

        ConfigurationProvider::loadConfiguration();
        echo "<p class='text-primary'>Configured datasource: " . ConfigurationProvider::getConfigurationField("datasource") . "</p><br/>";
        echo "<p class='text-primary'>Configured file: " . ConfigurationProvider::getConfigurationField("file") . '</p>';
    }
}
?>
<form method="post">
<div class="row">
    <div class="col-sm-10 col-md-10 col-lg-12">
        <p class="text-danger"><?php echo $message;?></p>
        <div class="form-group">
            <label for="passwdPath" title="Passwd File Path" content="Proftpd passwd file location"></label>
            <input id="passwdPath" type="text" class="form-control" name="p" value=<?php echo $ftpdPasswdFilePath; ?>/>
            <label for="ds" title="Passwd File Path" content="Proftpd passwd file location"></label>
            <select id="ds" class="form-control" name="ds">
                <option value="file">File</option>
                <option value="database">Database</option>
            </select>
        </div>
    </div>
    <div class="col-sm-10 col-md-10 col-lg-12">
        <button class="btn btn-primary form-control" type="submit">Dump</button>
    </div>
</div>
</form>


