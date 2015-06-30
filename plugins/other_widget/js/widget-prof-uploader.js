function clickClear(){
(function ($) {
	$('.media-url').val('');
	$('.media-alt').val('');
    $('.media').empty();
})(jQuery);	
}

function clickSelect(){
(function ($) {
	var custom_uploader;
	
	if (custom_uploader) {
        custom_uploader.open();
        return;
    }
    
    custom_uploader = wp.media({
        title: 'Select image',
        library: { type: 'image' },
        button: { text: 'Select image' },
        multiple: false
    });
    
    custom_uploader.on("select", function() {
    var images = custom_uploader.state().get('selection');
         images.each(function(file){
            $('.media-url').val('');
            $('.media').empty();
 
            $('.media-url').val(file.toJSON().url);
            $('.media-alt').val(file.toJSON().title);
 
			$('.media').append('<img class="media-image" src="'+ file.toJSON().url +'" alt="'+ file.toJSON().title +'" />');
        });
    });
    custom_uploader.open();
	
})(jQuery);		
}