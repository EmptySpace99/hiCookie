'use strict';

function getAllFavorites(){

    $.ajax({
        url: 'getAllFavorites.php',
        type: 'GET',
    })
    .done(function(response){
        //console.log(response); //debug
        try {
            var favorites = JSON.parse(response); //convert in js array
            var len = favorites.length;
            
            loadPost(favorites, len, '#favorites-box',true);
        } 
        catch (error) {
            //console.log(error) //debug
        }
    })
    .fail(function(){
        console.log('request failed');
    });
}


function addToFavorites(post_id){

    var post_id_clean = post_id.split('-')[0];

    //POST REQUEST
    $.ajax({
        url: 'addToFavorites.php',
        type: 'POST',
        data:{
            post_id: post_id_clean
        }
    })
    .done(function(response){
        //console.log(response); //debug

        if(response.startsWith('success=')){

            var favorite_post = document.getElementById(post_id).parentNode.cloneNode(true); //creo clone del post
            var posts = $('#posts-box').find(`[id^='${post_id_clean}-']`); //prendo tutti i post che hanno lo stesso id-
            var len = posts.length;

            //create favorite post
            favorite_post = createFavoritePost(favorite_post, post_id_clean);
            $('#favorites-box').prepend(favorite_post);
            post_listeners(`#${post_id_clean}-favorite`);

            if($(`#${post_id_clean}-favorite`).find('.animate__heartBeat')[0])
                removeLikeListener(`${post_id_clean}-favorite`);
            else
                addLikeListener(`${post_id_clean}-favorite`);
            

            //update posts favorite button (considering multiple shares of the same post)
            for(let i=0; i<len; i++){
                replaceWithRemoveFavorite(posts.eq(i).attr('id'));
            }

            //update post favorite button in case user is owner 
            replaceWithRemoveFavorite(post_id_clean);

            createNotification("Added to favorites!");
        }
    })
    .fail(function(){
        console.log('request failed');
    });
}


function createFavoritePost(favorite_post, post_id){

    favorite_post.children[0].id = `${post_id}-favorite`;

    if(favorite_post.querySelector('.shared')){  // if post is shared remove "shared by Filippo" element for example
        favorite_post.querySelector('.shared').remove();
    }

    //remove delete button and modify favorite post button
    favorite_post.children[0].getElementsByClassName('post-options')[0].innerHTML=`
        <div class="favorite-post-button remove-from-favorites"> 
            <i class="fas fa-star"></i>  
            <span>Remove from favorites</span>
        </div>
    `;

    return favorite_post;
}



function removeFromFavorites(post_id){

    var post_id_clean = post_id.split('-')[0];
        
    //POST REQUEST
    $.ajax({
        url: 'removeFromFavorites.php',
        type: 'POST',
        data:{
            post_id:post_id_clean
        }
    })
    .done(function(response){
        //console.log(response); //debug
        
        if(response.startsWith('success=')){

            //console.log(post_id_clean);//debug

            var posts = $('#posts-box').find(`[id^='${post_id_clean}-']`);
            var len = posts.length;

            //remove from favorites
            $(`#${post_id_clean}-favorite`).parent().remove();

            //update posts favorite button (considering multiple shares of the same post)
            for(let i=0; i<len; i++){
                replaceWithAddFavorite(posts.eq(i).attr('id'));
            }

            //update post favorite button in case user is owner 
            replaceWithAddFavorite(post_id_clean);

            createNotification("Removed from favorites!");
        }
    })
    .fail(function(){
        console.log('request failed');
    });
}

function replaceWithAddFavorite(post_id){

    $(`#${post_id}`)
        .find('.favorite-post-button')
        .replaceWith(`
            <div class="favorite-post-button add-to-favorites"> 
                <i class="fas fa-star"></i>  
                <span>Add to favorites</span>
            </div>`
        );
    
    addToFavoriteListener(`#${post_id}`);
    
}

function replaceWithRemoveFavorite(post_id){

    $(`#${post_id}`)
        .find('.favorite-post-button')
        .replaceWith(`
            <div class="favorite-post-button remove-from-favorites"> 
                <i class="fas fa-star"></i>  
                <span>Remove from favorites</span>
            </div>`
        );

    removeFromFavoriteListener(`#${post_id}`);
    
}

function addToFavoriteListener(post_id){

    $(post_id)
        .find('.add-to-favorites')
        .click(function(){

            var post_id = $(this).parents()[3].id;
            $(`#${post_id}`).find('.post-options').hide();
            addToFavorites(post_id); 

        });
}

function removeFromFavoriteListener(post_id){

    $(post_id)
        .find('.remove-from-favorites')
        .click(function(){

            var post_id = $(this).parents()[3].id;
            $(`#${post_id}`).find('.post-options').hide();
            removeFromFavorites(post_id);
        });
}