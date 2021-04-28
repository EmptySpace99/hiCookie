function updateActiveTime(){

    $.ajax({
        url: 'updateActiveTime.php',
        type: 'GET',
    })
    .done(function(){
    })
    .fail(function(){
        console.log("request fail");
    });
}


function updateCommentTime(){
    $.ajax({
        url:'updateCommentTime.php',
        type: 'GET',
    })
    .done(function(){
        //console.log("comment time updated");
    })
    .fail(function(){
        console.log("request fail");
    });
}


function updateChatTime(){

    $.ajax({
        url: 'updateChatTime.php',
        type: 'GET',
    })
    .done(function(){
    })
    .fail(function(){
        console.log('request failed');
    });
}