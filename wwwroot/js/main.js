
function filter(){
    let searchFieldText = document.getElementById("searchField").value;
    let fileList = document.getElementsByTagName("tbody")[0];

    for(let i = 0; i < fileList.children.length; i++){
        if(!fileList.children[i].children[1].textContent.includes(searchFieldText)){
            fileList.children[i].setAttribute("hidden", true);
        }else if(fileList.children[i].getAttribute("hidden")){
            fileList.children[i].removeAttribute("hidden");
        }
    }
}

function resetFilter(){
    let fileList = document.getElementsByTagName("tbody")[0];

    for(let i = 0; i < fileList.children.length; i++){
        fileList.children[i].removeAttribute("hidden", false);  
    }
}


function homeDirAutoFill(){
    let homeDirField = document.getElementById("homeDirField");
    let placeHolderValue = homeDirField.getAttribute("placeholder");
    if(placeHolderValue != null){
        homeDirField.value = placeHolderValue+document.getElementById("nameField").value.toLowerCase();
    }else{
        let splitted = homeDirField.value.split("/").slice(homeDirField.value.split("/").length - 1).join("/");
        console.log(splitted);
        homeDirField.value = homeDirField.value.replace(splitted,"")+document.getElementById("nameField").value.toLowerCase();
    }
}

function selectDataField(passField){
    let passFieldValue = passField.value;

    if(passFieldValue != null){
        passField.focus();
        passField.select();
    }
}

function validateUserData(){
    let userName = document.getElementById("nameField").value;
    let a = /[\,\-\ A-Z\\]/;
    let match = true;
    let div = document.getElementById("errDiv");
    let postDataButton = document.getElementById("postData");

    for(let i = 0; i < userName.length; i++){
        let c = userName.charCodeAt(i);
        let isSpaceChar = c == ".".charCodeAt(0) || c == "-".charCodeAt(0) || c == "_".charCodeAt(0);
        if((c < 48 || c > 57) && ( c < 65 || c > 90 ) && (c < 97 || c > 122) && !isSpaceChar){
            match = false;
            break;
        }
    }

    if(!match){
        div.removeAttribute("hidden");
        div.textContent = "Nazwa użytkownika może zawierać tylko znaki alfabetu łacińskiego oraz przecinki, podkreślniki i kropki.";
        postDataButton.setAttribute("disabled", true);
    }else{
        div.setAttribute("hidden", true);
        postDataButton.removeAttribute("disabled");
    }
}

document.addEventListener("keydown", function(e){
    if(e.keyCode == 112){
        document.getElementById("pdf_link").click();
    }
});


