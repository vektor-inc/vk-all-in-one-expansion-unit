function clickClear(value){
(function ($) {
	$(value).siblings('.media_url').val('');
	$(value).siblings('.media_alt').val('');
    $(value).parent('p').next('.media').empty();
})(jQuery);
}

function clickSelect(value){
(function ($) {
	var custom_uploader,
		$imageUrl = $(value).siblings('.media_url'),
		$imageAlt = $(value).siblings('.media_alt'),
		$mediaWrap = $(value).parent('p').next('.media');

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

			$mediaWrap.append('<img class="media_image" src="'+ file.toJSON().url +'" alt="'+ file.toJSON().title +'" />');
        });
    });
    custom_uploader.open();

})(jQuery);
}