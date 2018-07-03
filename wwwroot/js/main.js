function filter(){

}

function homeDirAutoFill(){
    console.log("Test");
    let homeDirField = document.getElementById("homeDirField");

    homeDirField.value = "/ftp/users/"+document.getElementById("nameField").value.toLowerCase();
}

