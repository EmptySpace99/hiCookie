'use strict';

async function readMedia(input){
    var formats=['image/jpeg','image/jpg','image/png','video/mp4'];

    if (input.files && input.files[0] 
        && formats.includes(input.files[0].type)
    ){
        var file = input.files[0];
        var video = document.createElement("video");
        var img = document.createElement("img");
        var reader = new FileReader();  // to read image or video
        var isImg=false;
        var isVideo=false;

        if(file.type.startsWith("image")){
            reader.onload = e => img.src=e.target.result;
            isImg=true;
        }
        else if(file.type.startsWith("video")){
            reader.onload = e => video.src=e.target.result;
            isVideo=true;
        }

        try {
           reader.readAsDataURL(file);
        } catch (error) {
            console.log(error);
        }

        if(isImg) 
            return img;
        else if(isVideo){
            //console.log(video.duration);
            video.autoplay=true;
            video.muted=true;
            video.loop=true;
            return video;
        }
    }
    return undefined;
}