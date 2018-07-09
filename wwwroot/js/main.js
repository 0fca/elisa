
function filter(){
    let searchField = document.getElementById("searchField");
    if(searchField != null){
    searchFieldText =  searchField.value;

    if(searchFieldText != ""){
        let fileList = document.getElementsByTagName("tbody")[0];

        for(let i = 0; i < fileList.children.length; i++){
            if(!fileList.children[i].textContent.includes(searchFieldText)){
                fileList.children[i].setAttribute("hidden", true);
            }else if(fileList.children[i].getAttribute("hidden")){
                fileList.children[i].setAttribute("hidden", false);
            }
        }
    }else{
        resetFilter();
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

    homeDirField.value = "/ftp/users/"+document.getElementById("nameField").value.toLowerCase();
}

window.addEventListener('keydown', function(){
    filter();
});

