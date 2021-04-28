<?php

    function checkValidCharacters($string){
        $valid_characters = array("A","B","C","D","E","F","G","H","I","J",
        "K","L","M","N","O","P","Q","R","S","T",
        "U","V","W","X","Y","Z","a","b","c","d",
        "e","f","g","h","i","j","k","l","m","n",
        "o","p","q","r","s","t","u","v","w","x",
        "y","z","1","2","3","4","5","6","7","8",
        "9","0"," ");

        foreach(str_split($string) as $char){
            if(!in_array($char, $valid_characters)){
                return $char;
            } 
        }

        return null;
    }
    
?>