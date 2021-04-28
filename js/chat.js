'use strict';

$(()=>{
    fetchUsersChat();
    updateChatTime();

    setInterval(()=>{
        updateActiveTime();
        check_status();
    }
    ,55000);

    setInterval(()=>{
        getLastMessage();
    }, 3000);

});


function check_status(){ 
    //GET REQUEST
    $.ajax({
        url: 'checkStatus.php',
        type: 'GET',
    })
    .done(function(response){
        //console.log(response); //debug

        try {
            var users=JSON.parse(response); //convert in js array
            var len = users.length;
            let i, user;


            for(i=0; i<len; i++){
                user = $(`#message-box${users[i].user_id}`);
                if(users[i].status){
                    user.find('.status-circle').css('background-color','rgb(6, 180, 6)');
                    user.find('.status-name').html('Online');
                }
                else{
                    user.find('.status-circle').css('background-color','rgb(196, 10, 10)');
                    user.find('.status-name').html('Offline');
                }   
            }
        } 
        catch (error) {
            console.log(error);
        }
    })
    .fail(function(){
        console.log('request failed');
    });
}


function sendMessage(to_user_id, chat_message){
    //POST REQUEST
    $.ajax({
        url: 'uploadMessage.php',
        type: 'POST',
        data:{
            to_user_id:to_user_id,
            chat_message:chat_message
        }
    })
    .done(function(response){
        //console.log(response); //debug
    })
    .fail(function(){
        console.log('request failed');
    });
}


function getMessages(user_id){
    $.ajax({
        url: 'getMessages.php',
        type: 'POST',
        data:{
            user_id: user_id,
        }
    })
    .done(function(response){
        //console.log(response); //debug

        try {
            response = JSON.parse(response);
            var len = response.length;
            let i;

            for(i=0;i<len; i++){
                createChatMessage(
                    response[i].chat_message, 
                    response[i].created_at, 
                    response[i].to_user_id, 
                    response[i].from_user_id,
                    response[i].current_user_id,
                );
            }   
        } 
        catch (error) {
            console.log(error);
        }
        
    })
    .fail(function(){
        console.log('request failed');
    });
}


function getLastMessage(){
    $.ajax({
        url: 'getLastMessage.php',
        type: 'GET',

    })
    .done(function(response){
        //console.log(response); //debug

        try {
            response = JSON.parse(response);
            var len = response.length;
            let i;

            if(len>0){
                for(i=0;i<len; i++){
                    createChatMessage(
                        response[i].chat_message, 
                        response[i].created_at, 
                        response[i].to_user_id, 
                        response[i].from_user_id,
                        response[i].current_user_id,
                    );
                }
    
                updateChatTime();
            }
            
        } 
        catch (error) {
            console.log(error);
        }
        
    })
    .fail(function(){
        console.log('request failed');
    });
}


function createChatMessage(chat_message, created_at, to_user_id, from_user_id, current_user_id){
    var mex_line= document.createElement("div");
    var mex_container_class, messageList;

    if(to_user_id == current_user_id)
        var messagesBox= document.getElementById(`message-box${from_user_id}`);
    else
        var messagesBox= document.getElementById(`message-box${to_user_id}`);
        

    if(current_user_id==from_user_id){
        mex_container_class="myMessages";
        mex_line.className="mymexLine";
    }
    else{
        mex_container_class="otherMessages";
        mex_line.className="othermexLine";
    }

    mex_line.innerHTML=`
        <div class="${mex_container_class} animate__animated animate__zoomIn animate__fast">
            <div class=mex>${chat_message}</div>
            <div class=date>${created_at}</div>
        </div>
    `;
    
    if(messagesBox){
        messageList = messagesBox.getElementsByClassName("messageList")[0];
        messageList.appendChild(mex_line);
        messageList.scrollTop=messageList.scrollHeight - messageList.clientHeight; //to keep scrollbar always bottom when messages are loaded
    }
}