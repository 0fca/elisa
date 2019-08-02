<div class="content-container"> 
    <?php 
        echo "
        <div class='leftCol1'>
            <p>Nazwa użytkownika:</p>
        </div>";
    ?>
        <?php 
            echo "<div class='leftCol2'>
                <p>Katalog domowy:</p>
            </div>";
        ?>

        <?php 
            echo "<div class='leftCol3'>
                <p>Quota (MB):</p>
            </div>";
        ?>
        <div class="rightCol1">
            <input type="text" id="nameField"  name="nameField" placeholder="Nazwa" oninput="homeDirAutoFill(); validateUserData();" required/>
        </div>
        <?php 
            $status = "readonly";
            if($systemUserModel !== NULL){
                if($systemUserModel->isAuthorized()){
                    $status = "";
                }
            }
            echo "<div class='rightCol2'>
                <input type='text' id='homeDirField' name='homeDirField' placeholder='".ftpHomeDirs."' value='".ftpHomeDirs."' {$status} required/>
            </div>";
        
        ?>
	<div class="rightCol3">
		<input type="text" id="quota" name="quota" value="50" required/>
	</div>
    </div>
        </div>
        <span class="internav">
            <button type="submit" class="actionbutton" id="postData" name="postData">OK</button>
            <a href="/?view=FtpUserListView" class="button-link">Anuluj</a>
        </span>
