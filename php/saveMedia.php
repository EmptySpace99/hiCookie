<?php

    function saveMedia($mediafile, $mediatype){

        if(isset($mediafile) && isset($mediatype)){
            $filename=uniqid('media', true); //create unique name for mediafile
    
            if($mediatype=="IMG"){
                //clear url
                $data = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $mediafile));
                $path='../images/'.$filename;
            }
            else if($mediatype=="VIDEO"){
                //clear url
                $data = base64_decode(preg_replace('#^data:video/\w+;base64,#i', '', $mediafile));
                $path='../videos/'.$filename;
            }
    
            if(isset($path) && isset($data)){

                file_put_contents($path, $data);

                if(file_exists($path))
                    return $path;
            }
        }

        return null;
    }
?>