// ClearBtn for pcImage
function clickClearPrBroks(value){
(function ($) {
	$(value).siblings('.pr_media_image').val('');
	$(value).siblings('.pr_media_alt').val('');
    $(value).parent('p').next('.media.image_pr').empty();
})(jQuery);
}

// SelectBtn for pcImage
function clickSelectPrBroks(value){
(function ($) {

	var custom_uploader,
		$imageUrl = $(value).siblings('.pr_media_image'),
		$imageAlt = $(value).siblings('.pr_media_alt'),
		$mediaWrap = $(value).parent('p').next('.media.image_pr');

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

			$mediaWrap.append('<img class="media_img" src="'+ file.toJSON().url +'" alt="'+ file.toJSON().title +'" />');
        });
    });
    custom_uploader.open();
})(jQuery);
}