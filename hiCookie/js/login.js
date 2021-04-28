'use strict';

const goToSignUp= document.getElementById("goToSignUp");
const goToSignIn = document.getElementById("goToSignIn");
const signIn = document.getElementById("signIn");
const signUp = document.getElementById("signUp");
const front = goToSignUp.parentNode.parentNode;
const back = goToSignIn.parentNode.parentNode;

function toSignUp(){
    if(front && back){
        front.className="front rotation-180";
        back.className="back rotation0";
    }
}

function toSignIn(){
    if(front && back){
        front.className="front rotation0";
        back.className="back rotation180";
    }
}


function createErrorIcon(){
    var errorIcon = document.createElement('i');
    errorIcon.className="error-icon far fa-times-octagon";
    return errorIcon;
}

function checkPassword(){
    var password1 = document.getElementById("password1");
    var password2 = document.getElementById("password2");
    var errorIcon;
    
    if(password1.value!="" && password2.value!='' && password1.value!=password2.value){
        //check if error icons are already added
        if(password1.parentNode && !password1.parentNode.children[2]){
            //create first error icon and append it
            errorIcon = createErrorIcon();
            password1.parentNode.appendChild(errorIcon);

            //create second error icon and append it
            errorIcon = createErrorIcon();
            password2.parentNode.appendChild(errorIcon);
        }

        return false;
    }
    else{
        //remove error icons
        if(password1.parentNode && password1.parentNode.children[2]){
            password1.parentNode.children[2].remove();
            password2.parentNode.children[2].remove();
        }
        return true;
    }
}

function signInRequest(){
    var email = document.getElementById("email");
    var password = document.getElementById("password");

    if(email.value!='' && password.value!=''){
        //POST REQUEST to sign in
        $.ajax({
            url: 'login.php',
            type: 'POST',
            data: {
                email: email.value,
                pass: password.value
            },
            })
            .done(function(response){
                //console.log(response); //debug

                if(response.startsWith('success')){
                    var user_id = response.split('=')[1];
                    window.location.href = 'show_profile.php?user_id='+user_id;
                }

                else if(response.startsWith('Error')){
                    createNotification(response, true, false, false);
                }
                
            })
            .fail(function(){
                console.log("request fail");
            });
    }
   
}

function signUpRequest(){
    var firstname = document.getElementById("firstname");
    var lastname = document.getElementById("lastname");
    var email = document.getElementById("your-email");
    var password1 = document.getElementById("password1");
    var password2 = document.getElementById("password2");
    
    if(
        firstname.value!='' && lastname.value!='' && 
        email.value!='' && password1.value!='' && 
        password2.value!='' && checkPassword()
    ){
        //POST REQUEST to insert user data in db
        $.ajax({
            url: 'registration.php',
            type: 'POST',
            data: {
                firstname: firstname.value,
                lastname: lastname.value,
                email: email.value,
                pass: password1.value,
                confirm: password2.value
            },
        })
        .done(function(response){
            console.log(response); //debug

            if(response=='success'){

                createNotification('Successful registration!');
                toSignIn();

                //clear values
                firstname.value='';
                lastname.value='';
                email.value='';
                password1.value='';
                password2.value='';
            }

            else if(response.startsWith('Error')){
                createNotification(response, true, false, false);
            }
        })
        .fail(function(){
            console.log("request fail");
        });
    }
}


$(()=>{
    if(goToSignUp){
        goToSignUp.addEventListener("click",()=>{
            toSignUp();
        })
    }

    if(goToSignIn){
        goToSignIn.addEventListener("click",()=>{
            toSignIn();
        });
    }

    if(signIn){
        signIn.addEventListener("submit",(e)=>{
            e.preventDefault();
            signInRequest();
        });
    }
    
    if(signUp){
        signUp.addEventListener("submit",(e)=>{
            e.preventDefault();
            signUpRequest();
        });
    }

    $('#password1').keyup(()=>{
        checkPassword();
    });

    $('#password2').keyup(()=>{
        checkPassword();
    });
    
});