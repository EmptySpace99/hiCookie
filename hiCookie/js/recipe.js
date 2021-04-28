'use strict';


// viene eseguito appena lo script viene caricato
$(()=>{
    addAllListeners();
    updateActiveTime();

    setInterval(()=>{
        updateActiveTime();
    },55000);
});

async function readRecipeImage(input) {
    var img_uploaded=document.getElementById("image-uploaded");
    var img_box= document.getElementById("recipe-image-container");

    if(img_uploaded)
        //remove last image
        img_uploaded.parentNode.removeChild(img_uploaded);

    //read new image
    var img = await readMedia(input);
    if(img && img.tagName=="IMG"){
        img.onload = ()=>{

            //set img properties
            img.className="recipe-image";
            img.alt="recipe-image";
            img.id="image-uploaded";
            img_box.className="img-box recipe-image-container";

            //append as child
            img_box.appendChild(img);
        }
    }
}

async function readStepImage(input,id) {

    //read img
    var img = await readMedia(input);
    if(img && img.tagName=="IMG"){
        img.onload = ()=>{

            //get step id
            var stepId=id_extractor(id,"step-image-button");
    
            //get step images container
            var stepBoxImages = document.getElementById(`step-box-images${stepId}`);
            var imageId;
            if(stepBoxImages)
                imageId = stepBoxImages.childNodes.length + 1; //get number of step-images and increase it by 1
            
            if(imageId){
                //set step-image box properties and content
                var step_img_box = document.createElement('div');
                step_img_box.className = "step-box-image";
                step_img_box.id = `step-img-box${imageId}|${stepId}`;
                step_img_box.innerHTML=`
                <div class="circle-box">${imageId}</div>
                <div class="far fa-trash-alt" id="delete-image-button${imageId}|${stepId}">`;
    
                //set img properties 
                img.alt="step-image";
                img.className="step-img";
    
                //append childs
                step_img_box.appendChild(img);
                stepBoxImages.appendChild(step_img_box);
    
                //add event listeners
                var deleteImageButton= document.getElementById(`delete-image-button${imageId}|${stepId}`);
                deleteImageButton.addEventListener("click",function(){
                    deleteStepImage(this.id);
                });
    
                //reset input button to solve bug when insert multiple times the same image
                var stepImageButton = document.getElementById(id);
                stepImageButton.value="";
            }
        }
    }
}

function add_ingredient(){
    var ingredientsContainer= document.getElementById("ingredients-container");

    //get ingredient id
    var ingredientId=ingredientsContainer.childNodes.length + 1;

    //clone ingredient node
    var new_ingredient_line= document.getElementById("ingredient-line1").cloneNode(true);

    //update properties
    new_ingredient_line.id=`ingredient-line${ingredientId}`;
    new_ingredient_line.querySelector("#ingredient-trash-box1").id = `ingredient-trash-box${ingredientId}`;
    new_ingredient_line.querySelector("[name='ingredient-name']").value="";
    new_ingredient_line.querySelector("[name='ingredient-quantity']").value='1';

    //append new ingredient
    ingredientsContainer.appendChild(new_ingredient_line);

    //add event listeners
    addIngredientListeners(ingredientId);
}


function deleteIngredient(id){
    //get id to identify ingredient to delete
    var id_num = id_extractor(id,"ingredient-trash-box");

    if(parseInt(id_num)>1){
        var node = document.getElementById("ingredient-line"+id_num);
        if(node && node.parentNode){
            node.parentNode.removeChild(node);
            updateIngredients();
        }
    }
}

function updateIngredients(){
    //get elements to update
    var ingredientsLine = document.getElementsByClassName("ingredient-line");
    var ingredientsDeleteButton = document.getElementsByClassName("delete-ingredient-button");

    //get ingredients number
    var ingredientsContainer= document.getElementById("ingredients-container");
    if(ingredientsContainer)
        var len = ingredientsContainer.children.length;
    
    //update elements ids
    let i;
    for(i=0; i<len; i++){
        ingredientsLine[i].id = `ingredient-line${i+1}`;
        ingredientsDeleteButton[i].id = `ingredient-trash-box${i+1}`;
    }
}


function add_step(){ //create new step
    var preparation_box= document.getElementById("preparation-box");

    //get steps number
    var step_box_num = document.getElementsByClassName("step-box").length+ 1;
    var stepId = step_box_num.toString(); //per tenere traccia del numero degli step

    //set step box and content
    var step_box_container = document.createElement("div");
    step_box_container.id = `step-box-container${stepId}`;
    step_box_container.setAttribute("data-name","step-box-container");
    step_box_container.innerHTML=`
    <div class="step-box">
        <div class="column-box">
            <div data-name="circle-box" class="circle-box"> ${stepId} </div>
            <div data-name="delete-button" id="delete-button${stepId}"><i class="far fa-trash-alt" aria-hidden="true"></i></div>
            <label class="far fa-plus-circle step-image-button-label" for="step-image-button${stepId}" aria-hidden="true"></label>
            <input type="file" name="step-image-button" id="step-image-button${stepId}">
        </div>
        <textarea name="step-description" rows="4" maxlength="1000" id="step-description${stepId}" placeholder="Describe preparation step"></textarea>
    </div>
    <div id="step-box-images${stepId}" class="step-box-images"></div>`;

    //append child
    preparation_box.appendChild(step_box_container);

    //add event listeners
    addStepListeners(stepId);
}

function deleteStepImage(id){
    // get id to identify the image to delete
    var ids=id_extractor(id,'delete-image-button');
    var imageId = ids[0];
    var stepId = ids[1];

    //get step image node and delete it
    var stepImageBox = document.getElementById(`step-img-box${imageId}|${stepId}`);
    console.log(`step-img-box${imageId}|${stepId}`);
    if(stepImageBox){
        var imagesBox = stepImageBox.parentNode;
        if(imagesBox){
            imagesBox.removeChild(stepImageBox);

            //update step images numbers
            updateStepImages(id_extractor(imagesBox.id,"step-box-images"));
        } 
    }
}

function updateStepImages(stepId){ //need stepId to indentify the preparation step
    //get current number of step images
    var stepBoxImages =  document.getElementById(`step-box-images${stepId}`);
    var stepImgNum = stepBoxImages.children.length;

    //get all step box image
    var stepBoxImage = stepBoxImages.children;

    let i;
    for(i=0; i<stepImgNum; i++){
        //update step box image id
        stepBoxImage[i].id=`step-img-box${i+1}|${stepId}`;
        //update circle box
        stepBoxImage[i].children[0].innerHTML=i+1;
        //update delete button id
        stepBoxImage[i].children[1].id=`delete-image-button${i+1}|${stepId}`;
    }
}


function deleteStep(id){ //to delete a step
    // get id to identify the step to delete
    var stepId=id_extractor(id,"delete-button");

    if (parseInt(stepId)>1){ //check to don't delete first step
        var stepBox = document.getElementById(`step-box-container${stepId}`);
        if(stepBox){
            var stepsBox = stepBox.parentNode;
            if(stepsBox){
                stepsBox.removeChild(stepBox);
                updateSteps();
            }
        }
    }
}


function updateSteps(){   //update elements ids when a step is deleted

    //get all elements to update
    var stepBoxContainers = $('div[data-name="step-box-container"]');
    var circleBoxs = $('div[data-name="circle-box"]');
    var trashBoxs = $('div[data-name="delete-button"]');
    var stepImageLabels = document.getElementsByClassName("step-image-button-label");
    var stepImageButtons = document.getElementsByName("step-image-button");
    var stepDescriptions = document.getElementsByName("step-description");
    var stepBoxImages = document.getElementsByClassName("step-box-images");

    //get number of step-boxs
    var preparationStep = document.getElementById("preparation-box");
    if(preparationStep)
        var len = preparationStep.children.length - 1; // -1 cause there is a <p> element as title

    //update all elements
    let i;
    for(i=0; i<len; i++){
        stepBoxContainers[i].id = `step-box-container${i+1}`;
        circleBoxs[i].innerHTML = i+1;
        trashBoxs[i].id = `delete-button${i+1}`;
        stepImageLabels[i].setAttribute('for', `step-image-button${i+1}`);
        stepImageButtons[i].id = `step-image-button${i+1}`;
        stepDescriptions[i].id = `step-description${i+1}`;
        stepBoxImages[i].id =  `step-box-images${i+1}`;
        updateStepImages(i+1);
    }
}


function id_extractor(id,name){ //extract element id and his father id if it exists
    return id.replace(name,"").split("|");
}


function addStepListeners(id){
    var deleteButton = document.getElementById(`delete-button${id}`);
    var stepImageButton = document.getElementById(`step-image-button${id}`);

    if(deleteButton){
        deleteButton.addEventListener("click",function(){
            deleteStep(this.id);
        });
    }

    if(stepImageButton){
        stepImageButton.addEventListener("change",function(){
            readStepImage(this,this.id);
        });
    }
}

function addIngredientListeners(id, value=false){
    var deleteIngredientButton = document.getElementById(`ingredient-trash-box${id}`);

    if(deleteIngredientButton){
        deleteIngredientButton.addEventListener("click",function(){
            deleteIngredient(this.id);
        });
    }

    if(value){
        var addIngredientButton = document.getElementById("add-ingredient");
        if(addIngredientButton){
            addIngredientButton.addEventListener("click",function(){
                add_ingredient();
            });
        }
    }
}

function addRecipeImageListener(){
    var uploadImage = document.getElementById("upload-photo");
    if(uploadImage){
        uploadImage.addEventListener("change",function(){
            readRecipeImage(this);
        });
    }
}


function uploadRecipe(){
    var recipeImage = $('#image-uploaded').attr('src');
    var recipeTitle = $('#recipe-title').val();
    var preparationTime = $('#preparation-time').children().eq(0).val() + ' ' + $('#preparation-time').children().eq(1).val();
    var cookingTime = $('#cooking-time').children().eq(0).val() + ' ' + $('#cooking-time').children().eq(1).val();
    var difficulty = $('#difficulty').val();
    var cost = $('#cost').val();
    var ingredientsForPeople = $('#ingredients-for-people').val();
    var curiosityText = $('#curiosity').val();
    var ingredients_len = $('.ingredient-line').length;
    var steps_len = $('[data-name="step-box-container"]').length;
    var step_images_len = 0;
    var ingredients=[],
    steps=[],
    images=[],
    step_images=[];
    

    //get all ingredients
    for(let i=0; i<ingredients_len; i++){
        ingredients.push({
            name: $('.ingredient-line').eq(i).find("[name='ingredient-name']").val(),
            quantity: $('.ingredient-line').eq(i).find("[name='ingredient-quantity']").val(),
            type: $('.ingredient-line').eq(i).find("[name='ingredient-type']").val(),
        });
    }

    //get all steps
    for(let i=0; i<steps_len; i++){
        images = [];
        step_images = $('[data-name="step-box-container"]').eq(i).find('.step-img');
        step_images_len = step_images.length;

        for(let k=0; k<step_images_len; k++){
            images.push(step_images[k].src);
        }

        steps.push({
            description: $('[data-name="step-box-container"]').eq(i).find("[name='step-description']").val(),
            images: images,
        });
    }

    /* //DEBUG
    var data;
    console.table(
        data = {
            recipeImage: recipeImage, 
            recipeTitle: recipeTitle, 
            preparationTime: preparationTime, 
            cookingTime: cookingTime, 
            difficulty: difficulty,
            cost:cost,
            ingredientsForPeople: ingredientsForPeople,
            ingredients: ingredients,
            curiosity: curiosityText,
            steps: steps
        }
    );*/

    //convert to json
    steps= JSON.stringify(steps);
    ingredients = JSON.stringify(ingredients);

    //POST REQUEST
    $.ajax({
        url: 'uploadRecipe.php',
        type: 'POST',
        data:{
            recipeImage: recipeImage, 
            recipeTitle: recipeTitle, 
            preparationTime: preparationTime, 
            cookingTime: cookingTime, 
            difficulty: difficulty,
            cost:cost,
            ingredientsForPeople: ingredientsForPeople,
            ingredients: ingredients,
            curiosity: curiosityText,
            steps: steps
        }
    })
    .done(function(response){
       //console.log(response); //debug
        if(response.startsWith('success')){
            var recipe_id = response.split('=')[1];
            window.location.href = 'show_recipe.php?recipe='+recipe_id;
        }
        else if(response.startsWith('Error'))
            createNotification(response,true, false, false);
    })
    .fail(function(){
        console.log('request failed');
    });
}


function addAllListeners(){

    addRecipeImageListener();  
    addIngredientListeners(1,true);
    addStepListeners(1);

    $('#add-step').click(()=>{
        add_step();
    });

    $('#recipe-form').submit((e)=>{
        e.preventDefault();
        uploadRecipe();
    });
}