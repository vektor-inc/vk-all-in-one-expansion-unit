/*----------------------------------------------------------*/
/*	scroll
/*----------------------------------------------------------*/
// Scroll function
$(window).scroll(function() {
	var scroll = $(this).scrollTop();
	if ($(this).scrollTop() > 1) {
		$('body').addClass('scrolled');
	} else {
		$('body').removeClass('scrolled');
	}
});
