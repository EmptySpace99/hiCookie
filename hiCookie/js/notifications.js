function createNotification(
    text,
    wrong= false, 
    warning=false, 
    info=false
){
    var className = 'isa_success';
    var iconName = 'fas fa-check-circle';

    if(wrong){
        className = 'isa_error';
        iconName = 'fas fa-times-circle'
    }
    else if(warning){
        className = 'isa_warning';
        iconName = "fas fa-exclamation-circle";
    }  
    else if(info){
        className = 'isa_info';
        iconName = "fas fa-info-circle";
    }
       
    $('body').append(`
        <div class='${className} notification'> 
            <i class="${iconName}"></i>
            <span>${text}</span> 
        </div>`
    );
    setTimeout(
        ()=>$('.notification').first().remove(),
        3000
    );
}