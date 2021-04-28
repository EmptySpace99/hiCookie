'use strict';

const uploadCover = document.getElementById("upload-cover");
const profileImage = document.getElementById("upload-profile-image");

$(()=>{
    addListeners();
    
    updateActiveTime();
    
    setInterval(()=>{
        updatePostsInfo('updatePostsInfo.php');
        getLastComment();
    }, 3000);

    setInterval(()=>{
        updateActiveTime();
    }, 55000);
    
    
});


async function loadCoverImage(input){

    var img = await readMedia(input);

    if(img && img.tagName=='IMG')
        img.onload = ()=>uploadCoverImage(img);
}


function uploadCoverImage(img){

    if(img && img.tagName=="IMG" && img.src!=''){
        
        $.ajax({
            url: 'uploadCoverImage.php',
            type: 'POST',
            data:{
                mediafile: img.src,
                mediatype: img.tagName
            },
        })
        .done(function(response){
           //console.log(response); //debug

            //insert uploaded image
            if(response.startsWith('success')){
                var img = $('<img>').attr('src', response.split('=')[1]);

                $('#upload-cover-label')
                    .html('')
                    .append(img);
            }
        })
        .fail(function(){
            console.log("request failed");
        });
    }
    
}


async function loadUserImage(input){

    var img = await readMedia(input);

    if(img && img.tagName=='IMG')
        img.onload = ()=>uploadUserImage(img);
}


function uploadUserImage(img){

    if(img && img.tagName=="IMG" && img.src!=''){

        $.ajax({
            url: 'uploadUserImage.php',
            type: 'POST',
            data:{
                mediafile: img.src,
                mediatype: img.tagName
            },
        })
        .done(function(response){
            //console.log(response); //debug

            //insert uploaded image
            if(response.startsWith('success')){
                var img = $('<img>').attr('src', response.split('=')[1]);

                $('#upload-profile-image-label')
                    .html('')
                    .append(img);
            }
            
        })
        .fail(function(){
            console.log("request failed");
        });
    }
}


function setUserInfo(userInfo){ //get biography, if user is logged or is owner
    
    userInfo = JSON.parse(userInfo);

    //insert biography
    if(userInfo.biography){
        updateBiographyText(userInfo.biography);
        createEditButton();
    }

    // if user isn't owner of the account
    if(!userInfo.isOwner){
        $('#editButtonBox').remove();
        $('#add-bio-button').remove();
        $('#new-post-button').remove();
        $('#favorites-icon').hide();
        $('#settings-icon').hide();
        $('.account').parent().removeClass('selected-box');
    }
    else{
        //add listeners and get favorite posts
        addOwnerListeners();
        getAllFavorites();
    }

    //if user is unlogged display message
    if(userInfo.unloggedUser){
        var message1 = createUnloggedMessage('Create an account or log in to hiCookie to see posts.');
        var message2 = createUnloggedMessage('Create an account or log in to hiCookie to see followers.');

        $('#posts-box').append(message1);
        $('#followers-box').append(message2);
        $('#favorites-icon').hide();
        $('#settings-icon').hide();
    }

    if(!userInfo.isOwner && !userInfo.unloggedUser && userInfo.isFollowing){
        $('#follow-box').prepend("<div class='center'><button id='unfollow-button'>Unfollow</button></div>");
        unfollow();
    }
    else if(!userInfo.isOwner && !userInfo.unloggedUser && !userInfo.isFollowing){
        $('#follow-box').prepend("<div class='center'><button id='follow-button'>Follow</button></div>");
        follow();
    }
}



function createUnloggedMessage(msg_content){
    var messageBox = document.createElement('div');
    var message = document.createElement('div');

    messageBox.className='center';
    message.innerHTML = msg_content;
    message.className='unlogged-user';
    
    messageBox.appendChild(message)
    return messageBox;
}


function modifySetting(button){

    //get firstname or lastname or email
    var elem = button.parentNode.children[1];

    if(button.value=='Edit'){

        var textContent = elem.textContent;
        var textarea = document.createElement('textarea');

        textarea.style.resize='none';
        textarea.style.padding='5px 10px';
        textarea.value = textContent;
        button.parentNode.replaceChild(textarea, elem);
        button.value = 'Save';
        button.textContent= 'Save';
    }
    else{
        var strong = document.createElement('strong');

        strong.textContent = elem.value;
        button.parentNode.replaceChild(strong, elem);
        button.value = 'Edit';
        button.textContent= 'Edit';
    }
    
}


function saveSettings(){
    var firstname = $('#firstname').parent().children()[1].textContent;
    var lastname = $('#lastname').parent().children()[1].textContent;
    var email = $('#email').parent().children()[1].textContent;
    var currentPassword = $('#current-password').val();
    var newPassword1 = $('#new-password1').val();
    var newPassword2 = $('#new-password2').val();

    $.ajax({
        url: 'update_profile.php',
        data:{
            firstname: firstname,
            lastname: lastname,
            email: email,
            currentPassword: currentPassword,
            newPassword1: newPassword1,
            newPassword2: newPassword2,
        },
        type: 'POST',
    })
    .done(function(response){

       //console.log(response); //debug

        try {
            var checks = JSON.parse(response);

            //if user would modify password
            if(checks.wantModifyPassword){

                if(checks.newPasswordIsMatched){

                    if(checks.currentPasswordIsMatched){

                        if(checks.passwordIsModified){

                            createNotification("Password modified with success!");
                        }
                        else
                            createNotification("Error: password not modified!",true, false, false);
                    }
                    else
                        createNotification("Error: current password doesn't match!",true, false, false);  
                }
                else
                    createNotification("Error: new passwords don't match!",true, false, false);
            }
            
            //if user would modify firstname
            if(checks.wantModifyFirstname){

                if(checks.firstnameIsModified){

                    location = location;
                }
                else
                    createNotification("Error: firstname not modified!",true, false, false);
            }

            //if user would modify lastname
            if(checks.wantModifyLastname){

                if(checks.lastnameIsModified){

                    location = location;
                }
                else
                    createNotification("Error: lastname not modified!",true, false, false);
            }


            //if user would modify email
            if(checks.wantModifyEmail){

                if(checks.emailIsModified){

                    createNotification("Email modified with success!");
                }
                else
                    createNotification("Error: email already used!",true, false, false);
            }
        } 
        catch(error) {

            if(response.startsWith("Error")){

                createNotification(response,true, false, false);
            }
        }
        
        
    })
    .fail(function(){
        console.log("request fail");
    });
}


function deleteProfile(){
    $.ajax({
        url: 'deleteProfile.php',
        type: 'GET',
    })
    .done(function(response){
        //console.log(response); //debug

        if(response=='success'){
            location='logout.php';
        }
    })
    .fail(function(){
        console.log("request fail");
    });
}


function addListeners(){

    $('#posts-icon').click(()=>{
        $('#favorites-icon').removeClass('selected-box');
        $('#followers-icon').removeClass('selected-box');
        $('#settings-icon').removeClass('selected-box');
        $('#posts-icon').addClass('selected-box');

        $('#favorites-box').hide();
        $('#followers-box').hide();
        $('#settings-box').hide();
        $('#posts-box').show();
    });

    $('#followers-icon').click(()=>{
        $('#posts-icon').removeClass('selected-box');
        $('#settings-icon').removeClass('selected-box');
        $('#favorites-icon').removeClass('selected-box');
        $('#followers-icon').addClass('selected-box');

        $('#posts-box').hide();
        $('#settings-box').hide();
        $('#favorites-box').hide();
        $('#followers-box').show();
    });

}


function addOwnerListeners(){
    if(uploadCover){
        uploadCover.addEventListener("change",function(){
            loadCoverImage(this);
        });
    }

    if(profileImage){
        profileImage.addEventListener("change",function(){
            loadUserImage(this);
        });
    }

    if(newPostButton)
        newPostButton.addEventListener("click",openCreatePost);

    $('#favorites-icon').click(()=>{
        $('#posts-icon').removeClass('selected-box');
        $('#followers-icon').removeClass('selected-box');
        $('#settings-icon').removeClass('selected-box');
        $('#favorites-icon').addClass('selected-box');

        $('#posts-box').hide();
        $('#followers-box').hide();
        $('#settings-box').hide();
        $('#favorites-box').show();
    });

    $('#settings-icon').click(()=>{
        $('#posts-icon').removeClass('selected-box');
        $('#followers-icon').removeClass('selected-box');
        $('#favorites-icon').removeClass('selected-box');
        $('#settings-icon').addClass('selected-box');

        $('#posts-box').hide();
        $('#followers-box').hide();
        $('#favorites-box').hide();
        $('#settings-box').show();
    });

    $('#firstname').click(function(){
        modifySetting(this);
    });

    $('#lastname').click(function(){
        modifySetting(this);
    });

    $('#email').click(function(){
        modifySetting(this);
    });

    $('#saveAllChanges').click(()=>saveSettings());

    $('#deleteProfile').click(()=>deleteProfile());
}
