pagePluginReSize();
jQuery(window).resize(function(){
	pagePluginReSize();
});

/*-------------------------------------------*/
/*	facebookLikeBox
/*-------------------------------------------*/
function pagePluginReSize(){
	// jQuery('.fb_iframe_widget').each(function(){
	// 	var element = jQuery(this).parent().width();
	// 	console.log(element);
	// 	jQuery(this).attr('data-width',element);
	// 	jQuery(this).children('span:first').css({"width":element});
	// 	jQuery(this).children('span iframe.fb_ltr').css({"width":element});
	// });
}

!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+'://platform.twitter.com/widgets.js';fjs.parentNode.insertBefore(js,fjs);}}(document, 'script', 'twitter-wjs');

/*-------------------------------------------*/
/*	jquery.flatheights.js 
/*-------------------------------------------*/
(function($){
$(function() {
    $('.prArea > .subSection-title').flatHeights();
    $('.prArea > .summary').flatHeights();
    $('.childPage_list_title').flatHeights();
});
window.onload = function() {
    $('.childPage_list_box').flatHeights();
}
})(jQuery);