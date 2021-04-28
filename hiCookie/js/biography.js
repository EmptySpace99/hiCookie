'use strict';

var biographyTextValue="";

function addBio(){

    updateBiographyText($('#biographyContainer').html());

    $('#bioBox').html('').append(`
    <div><textarea rows="3" maxlength="255" placeholder="Describe who you are" id="biography"></textarea></div>
    <div class="buttons-box">
        <button id='cancel-bio-button'>Cancel</button>
        <button id='save-bio-button'>Save</button>
    </div>`
    );

    $('#biography').val(biographyTextValue);

    $('#cancel-bio-button').click(()=>save());
    $('#save-bio-button').click(()=>uploadBiography());
}

function save(){

    if(biographyTextValue && biographyTextValue!="")
        createEditButton();
    else
        createAddBioButton();
        
}

function createEditButton(){

    $('#bioBox').html("").append(`
    <div class="biographyContainer" id="biographyContainer">${biographyTextValue}</div>
    <div class="underline" id="editButtonBox">
        <div class="biography">Edit</div>
    </div>`);

    var editButton=document.getElementById("editButtonBox");
    editButton.addEventListener("click", addBio);

}

function createAddBioButton(){

    $('#bioBox').html("").append(`
    <div id="add-bio-button" class="underline">
        <div class="biography">Add Bio</div>
    </div>`);
   
    $('#add-bio-button').click(()=>addBio())
}

function uploadBiography(){

    $.ajax({
        url: 'uploadBiography.php',
        data:{
            biography: $('#biography').val(),
        },
        type: 'POST',
    })
    .done(function(response){
        //console.log(response); //debug
        if(response.startsWith('success')){
            updateBiographyText(response.split('=')[1]);
            save();
        }
        else
            createNotification(response, false, true, false);
    })
    .fail(function(){
        console.log("request failed");
    });

}

function updateBiographyText(biography){
    biographyTextValue = biography;
}

$(()=>{
    $('#add-bio-button').click(()=>addBio());
});