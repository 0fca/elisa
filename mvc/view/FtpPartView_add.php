<div class="content-container"> 
    <?php 
        echo "
        <div class='leftCol1'>
            <p>Nazwa użyszkodnika:</p>
        </div>";
    ?>
        <?php 
        if($systemUserModel->isAuthorized()){
            echo "<div class='leftCol2'>
                <p>Katalog domowy:</p>
            </div>";
        }
        ?>
        <div class="leftCol3">
            <p>Hasło:</p>
        </div>

        <div class="rightCol1">
            <input type="text" id="nameField"  name="nameField" placeholder="Nazwa" oninput="homeDirAutoFill();"/>
        </div>
        <?php 
        if($systemUserModel->isAuthorized()){
            echo "<div class='rightCol2'>
                <input type='text' id='homeDirField' name='homeDirField' placeholder='".ftpHomeDirs."'/>
            </div>";
        }
        ?>
        <div class="rightCol1">
            <input type="password" id="passwordField" name="passwordField"/>
        </div>
    </div>
        <span class="internav">
            <button type="submit" class="actionbutton" name="postData">OK</button>
            <button type="cancel" class="actionbutton">Anuluj</button>
        </span>