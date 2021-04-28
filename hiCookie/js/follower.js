'use strict';

function follow(){
    $('#follow-button').click(()=>{

        $.ajax({
            url: 'follow.php',
            type: 'GET'
        })
        .done(function(response){
            
            if(response=='success'){
                $('#follow-button').replaceWith("<button id='unfollow-button'>Unfollow</button>");
                unfollow();
            }
        })
        .fail(function(){
            console.log("request fail");
        });
    });
}


function unfollow(){
    $('#unfollow-button').click(()=>{

        $.ajax({
            url: 'unfollow.php',
            type: 'GET'
        })
        .done(function(response){
            
            if(response=='success'){
                $('#unfollow-button').replaceWith("<button id='follow-button'>Follow</button>");
                follow();
            }
        })
        .fail(function(){
            console.log("request fail");
        });
    });
}


function createFollowerOrFollowing(user_id, user_image, firstname, lastname, follower_value){
    var follower = document.createElement('div');
    follower.className='follower-box';

    //check if firstname or lastname are too long
    var total_lenght = firstname.length + lastname.length;
    
    if(firstname.length>10 && total_lenght>20)
        firstname = firstname.substring(0,10)+'..';

    if(lastname.length>10  && total_lenght>20)
        lastname = lastname.substring(0,10)+'..';
        
    follower.innerHTML=`
        <a href="show_profile.php?user_id=${user_id}">
            <img src="${user_image}" alt="">
            <strong>${firstname} ${lastname}</strong>
        </a>
    `;

    if(follower_value){
        $('#followers').append(follower);
    }  
    else{
        $('#following').append(follower);
    }
        
}


function getFollowersAndFollowing(followers){

    var followers = JSON.parse(followers); //convert in js array
    var len = followers.length;
    let i=0;

    for(i=0; i<len; i++){
        createFollowerOrFollowing(
            followers[i].user_id,
            followers[i].user_image,
            followers[i].firstname,
            followers[i].lastname,
            followers[i].follower_value
        );
    }

    //update followers number
    $('#followers')
        .find('.follow-title')
        .html(`Followers: ${$('#followers').children().length-1}`);

    //update following number
    $('#following')
        .find('.follow-title')
        .html(`Following: ${$('#following').children().length-1}`);

}