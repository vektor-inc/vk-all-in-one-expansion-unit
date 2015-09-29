/*-------------------------------------------*/
/*  facebookLikeBox
/*-------------------------------------------*/
/*  jquery.flatheights.js 
/*-------------------------------------------*/
/*  snsCount
/*-------------------------------------------*/

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
	$('.childPage_list_text').flatHeights();
    $('.childPage_list_box').flatHeights();
}
})(jQuery);

/*-------------------------------------------*/
/*	snsCount
/*-------------------------------------------*/
(function($){
var facebook = {
  init: function() {
    var url = '//graph.facebook.com/?id=' + encodeURIComponent(location.href);
    $.ajax({
      url: url,
      dataType: 'jsonp',
      success: function(json) {
        var count = json.shares ? json.shares : 0;
        $('.veu_socialSet').find('.veu_count_sns_fb').html(count);
      }
    });
  }
}
facebook.init();

window.twttr=(function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],t=window.twttr||{};if(d.getElementById(id))return t;js=d.createElement(s);js.id=id;js.src="https://platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);t._e=[];t.ready=function(f){t._e.push(f);};return t;}(document,"script","twitter-wjs"));

var hatena = {
  init: function() {
    var url = (location.protocol === 'https:'?'https://b.hatena.ne.jp':'http://api.b.st-hatena.com')
            + '/entry.count?url=' + encodeURIComponent(location.href);
    $.ajax({
      url: url,
      dataType: 'jsonp',
      success: function(json) {
        var count = json ? json : 0;
        $('.veu_socialSet').find('.veu_count_sns_hb').html(count);

        if(typeof(count) == 'undefined'){
          count = 0;
        }
      }
    });
  }
}
hatena.init();
})(jQuery);
