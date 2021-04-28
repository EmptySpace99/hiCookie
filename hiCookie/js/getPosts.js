'use strict';

function getLastPost(){

    //POST REQUEST to get last post upload in db
    $.ajax({
        url: 'getLastPost.php',
        type: 'GET'
    })
    .done(function(response){
       //console.log(response); //debug
        
        try {
            var posts=JSON.parse(response); //convert in js array
            var len = posts.length;
            loadPost(posts,len,'#posts-box');
            createNotification("Posted!");
            updateActiveTime();
        } 
        catch (error) {
            console.log(error);
        }
    })
    .fail(function(){
        console.log("request fail");
    });
}


function getPosts(posts){ //get all user posts
    
    var posts = JSON.parse(posts); //convert in js array
    var len = posts.length;
    loadPost(posts,len,'#posts-box');
       
}