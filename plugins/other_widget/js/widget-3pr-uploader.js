// PC 用画像クリアボタン
function clickClear3pr(value){
(function ($) {
	$(value).siblings('.media-image-3pr-pc').val('');
	$(value).siblings('.media-alt-3pr-pc').val('');
    $(value).parent('p').next('.media.image-3pr').empty();
})(jQuery);	
}

// PC 用画像選択ボタン
function clickSelect3pr(value){
(function ($) {
		
	var custom_uploader,
		$imageUrl = $(value).siblings('.media-image-3pr-pc'),
		$imageAlt = $(value).siblings('.media-alt-3pr-pc'),
		$mediaWrap = $(value).parent('p').next('.media.image-3pr');
	
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
            $imageUrl.val('');
			$imageAlt.val('');
			$mediaWrap.empty();
 
            $imageUrl.val(file.toJSON().url);
            $imageAlt.val(file.toJSON().title);
 
			$mediaWrap.append('<img class="media-image 3pr-image" src="'+ file.toJSON().url +'" alt="'+ file.toJSON().title +'" />');
        });
    });
    custom_uploader.open();
	
})(jQuery);		
}

// SP 用画像クリアボタン
function clickClear3prSP(value){
(function ($) {	
    $(value).siblings('.media-image-3pr-sp').val('');
	$(value).siblings('.media-alt-3pr-sp').val('');
    $(value).parent('p').next('.media.image-3pr-sp').empty();
})(jQuery);	
}

// SP 用画像選択ボタン
function clickSelect3prSP(value){
(function ($) {
	
	var custom_uploader,
		$imageUrl = $(value).siblings('.media-image-3pr-sp'),
		$imageAlt = $(value).siblings('.media-alt-3pr-sp'),
		$mediaWrap = $(value).parent('p').next('.media.image-3pr-sp');
	
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
            
            $imageUrl.val('');
            $imageAlt.val('');
            $mediaWrap.empty();
 
            $imageUrl.val(file.toJSON().url);
            $imageAlt.val(file.toJSON().title);
 
			$mediaWrap.append('<img class="media-image image-3pr-sp" src="'+ file.toJSON().url +'" alt="'+ file.toJSON().title +'" />');
        });
    });
    custom_uploader.open();
	
})(jQuery);		
}