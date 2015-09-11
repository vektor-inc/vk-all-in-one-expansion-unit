// ClearBtn for pcImage
function clickClear3pr(value){
(function ($) {
	$(value).siblings('.media_image_3pr_pc').val('');
	$(value).siblings('.media_alt_3pr_pc').val('');
    $(value).parent('p').next('.media.image_3pr').empty();
})(jQuery);
}

// SelectBtn for pcImage
function clickSelect3pr(value){
(function ($) {

	var custom_uploader,
		$imageUrl = $(value).siblings('.media_image_3pr_pc'),
		$imageAlt = $(value).siblings('.media_alt_3pr_pc'),
		$mediaWrap = $(value).parent('p').next('.media.image_3pr');

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

			$mediaWrap.append('<img class="media_image 3pr_image" src="'+ file.toJSON().url +'" alt="'+ file.toJSON().title +'" />');
        });
    });
    custom_uploader.open();
})(jQuery);
}

// ClearBtn for spImage
function clickClear3prSP(value){
(function ($) {
    $(value).siblings('.media_image_3pr_sp').val('');
	$(value).siblings('.media_alt_3pr_sp').val('');
    $(value).parent('p').next('.media.image_3pr_sp').empty();
})(jQuery);
}

// ClearBtn for spImage
function clickSelect3prSP(value){
(function ($) {

	var custom_uploader,
		$imageUrl = $(value).siblings('.media_image_3pr_sp'),
		$imageAlt = $(value).siblings('.media_alt_3pr_sp'),
		$mediaWrap = $(value).parent('p').next('.media.image_3pr_sp');

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

			$mediaWrap.append('<img class="media_image image_3pr_sp" src="'+ file.toJSON().url +'" alt="'+ file.toJSON().title +'" />');
        });
    });
    custom_uploader.open();
})(jQuery);
}