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

    this.open();

    var url = 'http://graph.facebook.com/?id=' + encodeURIComponent(location.href);
    $.ajax({
      url: url,
      dataType: 'jsonp',
      success: function(json) {
        var count = json.shares ? json.shares : 0;
        $('.veu_socialSet').find('.vk_count_sns_fb').html(count);
      }
    });
  },
  open: function() {
    var $target = $('.veu_socialSet').find('.vk_count_sns_fb');
    $target.on('click', function(event) {
      event.preventDefault();
      window.open($(this).attr('href'), 'facebook', 'width=670, height=400, menubar=no, toolbar=no, scrollbars=yes');
    });
  }
}
facebook.init();

window.twttr=(function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],t=window.twttr||{};if(d.getElementById(id))return t;js=d.createElement(s);js.id=id;js.src="https://platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);t._e=[];t.ready=function(f){t._e.push(f);};return t;}(document,"script","twitter-wjs"));
var twitter = {
  init: function() {

    this.open();

    var url = 'http://urls.api.twitter.com/1/urls/count.json?url=' + encodeURIComponent(location.href);
    $.ajax({
      url: url,
      dataType: 'jsonp',
      success: function(json) {
        var count = json.count ? json.count : 0;
        $('.veu_socialSet').find('.vk_count_sns_tw').html(count);
      }
    });
  },
  open: function() {
    var $target = $('.veu_socialSet').find('.vk_count_sns_tw');
    $target.on('click', function(event) {
      event.preventDefault();
      window.open($(this).attr('href'), 'Twitter でリンクを共有する', 'width=550, height=400, menubar=no, toolbar=no, scrollbars=yes');
    });
  }
}
twitter.init();

var hatena = {
  init: function() {

    this.open();

    var url = 'http://api.b.st-hatena.com/entry.count?url=' + encodeURIComponent(location.href);
    $.ajax({
      url: url,
      dataType: 'jsonp',
      success: function(json) {
        var count = json ? json : 0;
        $('.veu_socialSet').find('.vk_count_sns_hb').html(count);

        if(typeof(count) == 'undefined'){
          count = 0;
        }
      }
    });
  },
  open: function() {
    var $target = $('.veu_socialSet').find('.vk_count_sns_hb');
    $target.on('click', function(event) {
      event.preventDefault();
      window.open($(this).attr('href'), 'はてなブックマークブックマークレット', 'width=550, height=420, menubar=no, toolbar=no, scrollbars=yes');
    });
  }
}
hatena.init();
})(jQuery);
