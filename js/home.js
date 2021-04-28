'use strict';

$(()=>{
    getHomePosts();
    updateActiveTime();

    setInterval(()=>{
        updatePostsInfo('updateHomePostsInfo.php');
        getLastComment();
    }, 3000);

    setInterval(()=>{
        updateActiveTime();
    },55000);
});


function getHomePosts(){
    //POST REQUEST
    $.ajax({
        url: 'getHomePosts.php',
        type: 'GET',
    })
    .done(function(response){
        //console.log(response); //debug

        try {
            var posts = JSON.parse(response); //convert in js array
            var len = posts.length;
            if(len>0)
                loadPost(posts, len, '#posts-box', false, true);
            else{
                var messageBox = document.createElement('div');
                messageBox.innerHTML ="<strong style='font-size:30px;'>No new posts</strong>";
            
                messageBox.className='center';
                $('#posts-box').append(messageBox);
            }
        } 
        catch (error) {
            console.log(error);
        }   
        
    })
    .fail(function(err) {
        console.log(err);
    });
}

