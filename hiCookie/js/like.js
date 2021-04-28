'use strict';

function addLike(post_id){

    var post_id_clean = post_id.split('-')[0];

    //POST REQUEST to add a like to a post in db
    $.ajax({
        url: 'addLike.php',
        type: 'POST',
        data: {
            post_id: post_id_clean,
        },
    })
    .done(function(response){

        //console.log(response); //debug

        if(response=="success"){

            var posts = $('#posts-box, #favorites-box').find(`[id^='${post_id_clean}-']`);
            var len = posts.length;

            for(let i=0; i<len; i++){

                removeLikeListener(posts.eq(i).attr('id'));
            }
            removeLikeListener(post_id_clean);
            
        }
    })
    .fail(function(){
        console.log("request fails");
    });
}


function removeLike(post_id){

    var post_id_clean = post_id.split('-')[0];

    
    //POST REQUEST to add a like to a post in db
    $.ajax({
        url: 'removeLike.php',
        type: 'POST',
        data: {
            post_id: post_id_clean,
        },
    })
    .done(function(response){

        //console.log(response); //debug

        if(response=="success"){

            var posts = $('#posts-box, #favorites-box').find(`[id^='${post_id_clean}-']`);
            var len = posts.length;

            for(let i=0; i<len; i++){

                addLikeListener(posts.eq(i).attr('id'));
            }
            addLikeListener(post_id_clean);
            
        }
    })
    .fail(function(){
        console.log("request fails");
    });
}


function removeLikeListener(post_id){

    removeAllListeners(post_id);
    addLikeIcon(post_id);
        
    $(`#${post_id}`)
        .find('.like-button')    
        .click(function(){

            var post_id = $(this).parents()[1].id;
            removeLike(post_id);
        });
}


function addLikeListener(post_id){

    removeAllListeners(post_id);
    removeLikeIcon(post_id);
    
    $(`#${post_id}`)
        .find('.like-button')    
        .click(function(){

            var post_id = $(this).parents()[1].id; 
            addLike(post_id);
        });
}


function addLikeIcon(post_id){

    $(`#${post_id}`)
        .find('.like-button')
        .addClass('selected-box animate__heartBeat');
}


function removeLikeIcon(post_id){
    
    $(`#${post_id}`)
        .find('.like-button')
        .removeClass('selected-box animate__heartBeat');
}

function removeAllListeners(post_id){

    var like_button = $(`#${post_id}`).find('.like-button');
    like_button.replaceWith(like_button.clone()); //to remove all listeners
}