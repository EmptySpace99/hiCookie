'use strict';

function showChat(user_id){
    var messageList =$(`#message-box${user_id}`).find('.messageList');

    $('#presentation-page').hide();
    $('.message-box').hide();
    $(`#message-box${user_id}`).show();
    messageList.scrollTop(messageList[0].scrollHeight - messageList[0].clientHeight);
};


function createChat(user={}){
    var messageBox = document.createElement('div');
    messageBox.id=`message-box${user.user_id}`;
    messageBox.className = 'message-box';
    messageBox.innerHTML=
        `<div class="column-box message-box-relative">
        <div class="user-box user-title top-box">
            <div class=inline-box>
                <img src=${user.user_image} alt="user-image">
                <div class='column-box'> 
                    <div class=inline-box >
                        <a href="show_profile.php?user_id=${user.user_id}"><strong>${user.firstname} ${user.lastname}</strong></a>
                        <div class='status-circle circle-red'></div>
                    </div>
                    <span class='status-name'>Offline</span>
                </div>
            </div>
        </div>
        <div class=messageList></div>
        <form id=message-form${user.user_id} class=message-form method='post'>
            <textarea class=message-input placeholder="Write a message"></textarea>
            <button><i class="fas fa-paper-plane"></i></button>
        </form>
        </div>`;

    $('#chat-box').append(messageBox);
    $(`#message-form${user.user_id}`).submit(function(e){
        e.preventDefault();
        var to_user_id = this.id.split('message-form').join('');
        var chat_message = this.children[0].value;
        sendMessage(to_user_id, chat_message);
        this.children[0].value='';
    });
    $(`#message-box${user.user_id}`).hide();

    getMessages(user.user_id);
}

function createChatroom(user={}){
    var chatroomBox = document.getElementById("chatroom-box");
    var chatroom= document.createElement("div");
    chatroom.className = "chatroom";
    chatroom.id = 'chatroom'+user.user_id;
    chatroom.innerHTML=`
        <img src='${user.user_image}'></img>
        <div class="chatroom-title"> <h4>${user.firstname}  ${user.lastname}</h4> <p>Start conversation</p> </div>
    `;

    chatroomBox.appendChild(chatroom);
    $(`#${chatroom.id}`).click(function(){
        showChat(this.id.split('chatroom').join(''));
    });
}

function fetchUsersChat(){

    $.ajax({
        url: 'fetchUsersChat.php',
        type: 'GET',
    })
    .done(function(response){
        //console.log(response); //debug

        try {
            var users=JSON.parse(response); //convert in js array
            var len = users.length;
            let i;

            for(i=0; i<len; i++){
                createChatroom(users[i]);
                createChat(users[i]);
            }
            
            updateActiveTime();
            check_status();
        } 
        catch (error) {
            console.log(error);
        }
        
    })
    .fail(function(){
        console.log("request fail");
    }); 
}




