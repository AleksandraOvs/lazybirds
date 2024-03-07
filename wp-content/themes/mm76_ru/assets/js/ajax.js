jQuery( function($){

    var button = $('#loadmore button');

    button.click( function(){
    //alert('click');
    $.ajax({
        type: 'POST',
        url: loadmore.ajax_url,
        data: {
            paged: button.data('paged'),
            action: 'loadmore'
        },
        beforeSend: function(xhr){
            button.text('Загружаю');
        },
        success: function (data){
            button.parent().before(data);
            button.text('Смотреть ещё');
            
        }


    });  
 
	return false;
    });

    
    
} );