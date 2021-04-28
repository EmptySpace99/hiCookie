function fetchUsersToShare(posts=[], len){
    $.ajax({
        url: 'fetchUsersChat.php',
        type: 'GET',
    })
    .done(function(response){
       //console.log(response); //debug

        try {
            var users=JSON.parse(response); //convert in js array
            var length = users.length;
            let i, k;
            
            for(k=0; k<len; k++){

                for(i=0; i<length; i++){
                    createShareLine(posts[k].post_id, users[i]);
                }
                   
            }
        } 
        catch (error) {
            console.log(error);
        }
        
    })
    .fail(function(){
        console.log("request fail");
    }); 
}


function createShareLine(post_id, user=[]){
    var shareLine = document.createElement('div');
    shareLine.className = 'share-line';
    shareLine.setAttribute('data-user_id', user.user_id);

    shareLine.innerHTML=`
        <div>
            <img class=share-img src=${user.user_image} alt="user-image">
            <strong>${user.firstname} ${user.lastname}</strong>
        </div>
        <div>
            <i class="fas fa-share"></i>
        </div>
    `
    $(`#${post_id}`).find('.share-box').append(shareLine);
    $(`#${post_id}`)
        .find(`div[data-user_id=${user.user_id}]`)
        .click(function(){
            var post_id = $(this).parents()[2].id;
            var to_user_id = $(this).attr('data-user_id');
            sharePost(post_id, to_user_id);
        });
}

function sharePost(post_id, to_user_id){

    if(post_id.includes('favorite'))
        post_id = post_id.split('-favorite').join('');
    
    if(post_id.includes('sharedBy'))
        post_id = post_id.split('-sharedBy')[0];

    $.ajax({
        url: 'sharePost.php',
        type: 'POST',
        data: {
            post_id: post_id,
            to_user_id: to_user_id,
        },
    })
    .done(function(response){
       //console.log(response); //debug

        if(response=='Shared!'){
            createNotification(response);
        }
        else if(
            response=="Already shared!" || 
            response=="You can't share post with the post's owner!"
        ){
            createNotification(response, false, true, false);
        }
    })
    .fail(function(){
        console.log("request fail");
    });
}