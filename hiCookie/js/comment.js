'use strict';

//create new comment
function createComment(post_id, comment_content, user_image, firstname, lastname,user_id){
    var commentList = $(`#${post_id}`).find('.comments-box');
    var comment = document.createElement('div');

    if(commentList && commentList[0]){
        
        comment.className='comment';
        comment.innerHTML=`
        <div class="column-box">
            <img class=comment-img src=${user_image} alt="user-image">
            <a href=show_profile.php?user_id=${user_id}>${firstname} ${lastname}</a>
        </div>
        <div class='message-content'>${comment_content}</div>
        `;
    
        commentList.append(comment);
        commentList.scrollTop(commentList[0].scrollHeight - commentList[0].clientHeight); //to keep scrollbar always bottom when messages are sent or received
    }
    
}

//insert new comment in db
function uploadComment(post_id, comment_content){

    var post_id = post_id.split('-')[0];

    $.ajax({
        url: 'uploadComment.php',
        type:'POST',
        data: {
            post_id: post_id,
            comment_content: comment_content,
        }
    })
    .done(function(response){
    })
    .fail(()=>{
        console.log('connection failed');
    });
}

//get all comments for your post when page is loaded
function getComments(post_id){

    var post_id_clean = post_id.split('-')[0];

    $.ajax({
        url: 'getComments.php',
        type:'POST',
        data: {
            post_id: post_id_clean,
        }
    })
    .done(function(response){
       //console.log(response); //debug
        try {
            response = JSON.parse(response);
            var len = response.length;
            let i;

            for(i=0;i<len; i++){
                    
                createComment(
                    post_id, 
                    response[i].comment_content,
                    response[i].user_image, 
                    response[i].firstname,
                    response[i].lastname,
                    response[i].user_id
                );
            }
        } 
        catch (error) {
           console.log(error); 
        }
            
    })
    .fail(()=>{
        console.log('connection failed');
    });
}


//get last comment inserted in db
function getLastComment(){

    $.ajax({
        url: 'getLastComment.php',
        type:'GET',
    })
    .done(function(response){
       //console.log(response); //debug
        try {
            response = JSON.parse(response);
            var len = response.length;
            var posts_len = $('.box-shadow-dark').length;
            var post_id;

            if(len>0){ //solo se c'è almeno un nuovo commento aggiorno l'ultima attività

                for(let k=0; k<len; k++){

                    for (let i = 0; i < posts_len; i++) {

                        post_id = $('.box-shadow-dark').eq(i).attr('id');
        
                        if(post_id.startsWith(response[k].post_id)){

                            createComment(
                                post_id, 
                                response[k].comment_content,
                                response[k].user_image, 
                                response[k].firstname,
                                response[k].lastname,
                                response[k].user_id
                            );
                        }
                    }
                   
                }
                updateCommentTime();
            }
        } catch (error) {
            console.log(error);
        }
        
    })
    .fail(()=>{
        console.log('connection failed');
    });
}