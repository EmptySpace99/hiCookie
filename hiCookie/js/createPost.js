'use strict';

const postsBox = document.getElementById("posts-box");
const newPostButton = document.getElementById("new-post-button");
const flexibleBox = document.getElementsByClassName('flexible-box')[0];


async function loadPostMedia(input){
    var imgORvideo = await readMedia(input);
    //console.log(imgORvideo); //debug

    if(imgORvideo && imgORvideo.tagName=='VIDEO'){

        imgORvideo.onloadstart = ()=>{
            createPostPreview(imgORvideo);
        }

    }

    if(imgORvideo && imgORvideo.tagName=='IMG'){

        imgORvideo.onload = ()=>{
            createPostPreview(imgORvideo);
        }
    }
    
}


function createPostPreview(imgORvideo){
    if (imgORvideo){
        var newPostContainer= document.getElementById("new-post-container");

        if(newPostContainer){
            newPostContainer.innerHTML=`<i class="fas fa-plus"></i>`;  //clear last img or video added
            newPostContainer.className="new-post-container border-none";
            imgORvideo.className="fitSize";
            imgORvideo.id="media-uploaded";
            imgORvideo.alt="media-preview";
            newPostContainer.append(imgORvideo);
        }
    }
}


function openCreatePost(){ //open form to create new post
    var checkPost = document.getElementById("new-post");

    if(!checkPost)
    {
        var newPost= document.createElement("form");
        newPost.className="new-post animate__animated animate__zoomIn";
        newPost.id="new-post";
        newPost.innerHTML=`
        <div class=post>
        <div class="new-post-text"><span>Create post</span></div>
        <textarea class=post-textarea id="post-title" name="title" rows="1" placeholder="Title..."></textarea>
        <label for="upload-post">
        <div id=new-post-container class="new-post-container">
        <i class="fas fa-plus"></i>
        </div>
        </label>
        <input class="display-none" type='file' id="upload-post">
        <textarea class=post-textarea id="new-post-description" name="description" rows="3" placeholder="Description..."></textarea>
        <button id="share-button">Share</button>
        <div id="close-post-button" class=close-post-button><i class="fal fa-times-circle"></i></div>
        </div>`;

        if(flexibleBox)
            flexibleBox.appendChild(newPost);
            
        var closeButton = document.getElementById("close-post-button");
        var uploadPost = document.getElementById("upload-post");
        var form = document.getElementById("new-post");

        if(closeButton)
            closeButton.addEventListener("click",closeCreatePost);

        if(uploadPost){
            uploadPost.addEventListener("change",function(){
                loadPostMedia(this);
            });
        }

        if(form){
            form.addEventListener("submit",(e)=>{
                e.preventDefault(); //dont refresh page
                var mediafile = document.getElementById("media-uploaded"); // take the video or image
                var description = document.getElementById("new-post-description"); //take the video/image description
                var title= document.getElementById("post-title");

                if((mediafile && mediafile.src!="") || (description && description.value!='')){
                    uploadPostToDB(mediafile,description,title);
                    closeCreatePost();
                }
                else 
                    createNotification(
                        "You must insert a description or a mediafile to create a post!",
                        false,
                        true,
                        false
                    );
            });
        }
    }
}


function closeCreatePost(){ //close form to create new post
    var newPost= document.getElementById("new-post");

    if(newPost){
        newPost.className="new-post animate__animated animate__zoomOut";
        setTimeout(
            ()=>newPost.remove(),
            500
        );  //wait 0.5s before delete node to see zoomOut effect
    }
}


function uploadPostToDB(mediafile,description,title){
    var data={};

    if(mediafile){
        data.mediafile=mediafile.src;
        data.mediatype=mediafile.tagName;
    }
    if(description)
        data.description=description.value
    if(title)
        data.title=title.value;

    //POST REQUEST to insert post in db
    $.ajax({
        url: 'uploadPost.php',
        type: 'POST',
        data: data,
    })
    .done(function(response){
        //console.log(response); //debug
        
        if(response=='success'){
            getLastPost();
        }
        else
            createNotification(response,true);
    })
    .fail(function(){
        console.log("request fail");
    });
}


function loadPost(posts=[], len, appendTo, isFavorite=false, isHome=false){
    var post;
    let i;

    for(i=0; i<len; i++){

        //set id
        if(posts[i].from_user_id)
            posts[i].post_id = `${posts[i].post_id}-sharedBy-${posts[i].from_user_id}-sharedTo-${posts[i].to_user_id}`;

        if(isFavorite)
            posts[i].post_id = posts[i].post_id+'-favorite';

        //create post
        post = createNewPost(posts[i]);

        if(post){

            if(isFavorite)
                post = createFavoritePost(post, posts[i].post_id.split('-')[0]);

            if(isHome){
                post.style.flexBasis = '100%'; 
                post.className +=' post-box-margin animate__animated animate__zoomIn animation_delay_4ms';
            }

            $(appendTo).prepend(post);
            getComments(post.children[0].id);

            //add listeners
            post_listeners(`#${post.children[0].id}`);

            if(posts[i].like_value){ 
                removeLikeListener(posts[i].post_id);
            }
            else
                addLikeListener(posts[i].post_id);

        }

    }

    fetchUsersToShare(posts, len);
}


function updatePostsInfo(url){
    
    $.ajax({
        url: url,
        type: 'GET',
    })
    .done(function(response){
        //console.log(response); //debug
        
        try {
            var posts = JSON.parse(response);
            var len = posts.length;

            var posts_len = $('.box-shadow-dark').length
            var post_id;

            for (let k = 0; k < len; k++) {
                for (let i = 0; i < posts_len; i++) {

                    post_id = $('.box-shadow-dark').eq(i).attr('id');

                    if(post_id.split('-')[0]==posts[k].post_id){
    
                        //update likes number
                        $(`#${post_id}`)
                            .find('.likes-num')
                            .text('Likes: ' + posts[k].likes);
    
                        //update comments number
                        $(`#${post_id}`)
                            .find('.comments-num')
                            .text('Comments: ' + posts[k].comments);
    
                        //update shares number
                        $(`#${post_id}`)
                            .find('.shares-num')
                            .text('Shares: ' + posts[k].shares);
                    }
                }
            }

        } catch (error) {
            console.log(error);
        }
    })
    .fail(function(){
        console.log("request fail");
    });
}


function createNewPost(post=[]){
    var post_info='', 
    user_info='', 
    current_user_img='', 
    deletePostButton='', 
    favoritePostButton='',
    recipe_link = '',
    create=false,
    postBox = document.createElement("div");
    postBox.className="post-box";

    if(post.isRecipe){
        recipe_link = `show_recipe.php?recipe=${post.recipe_id}`;
    }

    //insert current_user_image
    if(post.current_user_image){
        current_user_img = `<img class=comment-img src="${post.current_user_image}" alt="current-user-image"></img>`
    }

    //insert user_image
    if(post.user_image){
        user_info+=`<img src="${post.user_image}" alt="user-image"></img>`
    }

    //if current user is owner of post
    if(post.isOwner)
        deletePostButton ='<div class=delete-post-button> <i class="fal fa-trash-alt"></i> <span>Delete post</span></div>'

    //if post is in your favorites
    if(post.isFavorite)
        favoritePostButton = '<div class="favorite-post-button remove-from-favorites"> <i class="fas fa-star"></i>  <span>Remove from favorites</span></div>'
    else
        favoritePostButton = '<div class="favorite-post-button add-to-favorites"> <i class="fas fa-star"></i>  <span>Add to favorites</span></div>'

    if(post.firstname && post.lastname){ //insert username
        user_info+=`<div class="column-box"> <div> <a href=show_profile.php?user_id=${post.user_id}>${post.firstname} ${post.lastname}</a>`;
    }

    //if post is a shared post
    if(post.from_user_id){
        user_info+=`<a href=show_profile.php?user_id=${post.from_user_id}> <strong class=shared>shared by ${post.from_user_firstname} ${post.from_user_lastname}</strong> </a>`;

        //post_id = `${post.post_id}-sharedBy-${post.from_user_id}-sharedTo-${post.to_user_id}`;
    }

    user_info+='</div>';

    if(post.created_at){ //insert date
        user_info+=`<span class="date">${post.created_at}</span>`;
    }

    if(post.title && post.isRecipe){
        post_info+=`<div class="post-container"><strong><a href="${recipe_link}">${post.title}</a></strong></div>`;
    }
    else if(post.title){ //insert title
        post_info+=`<div class="post-container"><strong>${post.title}</strong></div>`;
    }

    if(post.mediapath && post.mediatype=="IMG" && post.isRecipe){ //insert image as link to recipe
        create=true;
        post_info+=`<div class=post-container><a href="${recipe_link}"><img src=${post.mediapath} alt="post-image"></a></div>`;
    }
    else if(post.mediapath && post.mediatype=="IMG"){ //insert image
        create=true;
        post_info+=`<div class=post-container><img src=${post.mediapath} alt="post-image"></div>`;
    }
    else if(post.mediapath && post.mediatype=="VIDEO"){ //insert video
        create=true;
        post_info+=`<div class=post-container><video controls autoplay muted loop src=${post.mediapath}></video></div>`;
    }

    if(post.description){ //insert description
        create=true;
        post_info+=`<div class=post-container><div>${post.description}</div></div>`;
    }

    if(create){
        postBox.innerHTML=`
        <div class='box-shadow-dark' id=${post.post_id}>
            <div class="user-box">
                <div class="left-box">
                    ${user_info}
                    </div>
                </div>
                <div class="options-box">
                    <i class="far fa-ellipsis-v post-options-button"></i>  
                    <div class="post-options animate__animated animate__rotateInDownRight animate__faster">
                        ${deletePostButton}
                        ${favoritePostButton}
                    </div>
                </div>
            </div>
            ${post_info}
            <div class='inline-box'>
                <span class='likes-num'>Likes: ${post.likes}</span>
                <span class='comments-num'>Comments: ${post.comments}</span>
                <span class='shares-num'>Shares: ${post.shares}</span>
            </div>
            <div class="inline-box">
                <div class="like-button"><i class="fas fa-thumbs-up"></i><span>Like</span></div> 
                <div class='comment-button'><i class="fas fa-comment"></i><span>Comment</span></div> 
                <div class='share-button'><i class="fas fa-share-alt"></i><span>Share</span></div>
            </div>
            <div class="comments-container comment-box" style='display:none;'>
                <div class="close-box"><i class="close-comment-box far fa-times-circle" aria-hidden="true"></i></div>
                    <div class="comments-box"></div>
                    <div class="comment-box">
                        ${current_user_img}
                        <form class="send-comment" method="POST">
                            <textarea maxlength="1000" placeholder="Write a comment..."></textarea>
                            <button><i class="fas fa-paper-plane"></i> </button>
                        </form>
                </div>
            </div>
            <div class='share-container' style='display:none;'>
                <div class='close-box'>
                    <div class=center>
                        <strong>Share on friend's profile</strong>
                    </div>
                    <i class="close-share-box far fa-times-circle" aria-hidden="true"></i>
                </div>
                <div class="share-box"></div>
            </div>
        </div>`;

        return postBox;
    }
    else return undefined;
}


function showPostOptions(optionButton){

    var optionsBox = optionButton.parentNode;

    //get postOptions
    if(optionsBox)
        var postOptions = optionsBox.children[1];

    //if post options is displayed hide it
    if(postOptions.style.display=="flex"){

        postOptions.className="post-options animate__animated animate__rotateOutUpRight animate__faster";

        setTimeout(()=>{
            postOptions.style.display="none";
        },500);
    }

    //else show it
    else{
        postOptions.className="post-options animate__animated animate__rotateInDownRight animate__faster";
        postOptions.style.display="flex";
    }
}


function deletePost(post_id){
    
    var ids = post_id.split('-');

    //POST REQUEST
    $.ajax({
        url: 'deletePost.php',
        type: 'POST',
        data:{
            post_id: ids[0],
            shared_by: ids[2] | null,
            shared_to: ids[4] | null
        }
    })
    .done(function(response){
        //console.log(response); //debug

        if(response=='success'){

            document.getElementById(post_id).parentNode.remove();
        }
    })
    .fail(function(err) {
        console.log(err);
    });
}


function post_listeners(post_id){

    $(post_id).find('.post-options-button').click(function(){

        showPostOptions(this);

    });

    $(post_id).find('.delete-post-button').click(function(){

        var post_id = $(this).parents()[3].id;
        deletePost(post_id);

    });

    addToFavoriteListener(post_id);

    removeFromFavoriteListener(post_id);

    $(post_id).find('.comment-button').click(function(){

        var post_id = $(this).parents()[1].id;
        $(`#${post_id}`)
            .find('.comments-container')
            .css('display','flex');

        var commentList = $(`#${post_id}`).find('.comments-box');
        commentList.scrollTop(commentList[0].scrollHeight - commentList[0].clientHeight);

    });

    $(post_id).find('.share-button').click(function(){

        var post_id = $(this).parents()[1].id;
        $(`#${post_id}`)
            .find('.share-container')
            .css('display','flex');

    });

    $(post_id).find('.close-share-box').click(function(){

        var post_id = $(this).parents()[2].id;
        $(`#${post_id}`).find('.share-container').hide();

    });

    $(post_id).find('.close-comment-box').click(function(){

        var post_id = $(this).parents()[2].id;
        $(`#${post_id}`).find('.comments-container').hide();

    });

    $(post_id).find(`.send-comment`).submit(function(e){
        
        e.preventDefault();
        var comment_content = $(this).find('textarea');
        var post_id = $(this).parents()[2].id;

        uploadComment(post_id, comment_content.val());
        comment_content.val('');
    });
}