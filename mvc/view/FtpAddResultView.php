<?php
include_once("mvc/model/FtpUserModel.php");
$result = unserialize($_SESSION["ftpAddResult"]);
?>
<h3>Użytkownik serwera FTP: <?php $result->getName();?></h3>
<div class="content-container">
    <div class="leftCol1">
        <p>Dane: </p>
    </div>
    <div class="rightCol1">
        <textarea id="dataTextField" readonly><?php echo "Nazwa: " . $result->getName() . "\nHasło: " . trim($result->getRawPassword());?></textarea>
    </div>
    <a href="/?view=ManageFtpUserView">Dodaj następnego...</a>
    <a href="/">Do strony głównej</a>
</div>

<script>
    let passField = document.getElementById("dataTextField");
    selectDataField(passField);
</script>
