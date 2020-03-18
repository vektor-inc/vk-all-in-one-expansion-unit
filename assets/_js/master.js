/*-------------------------------------------*/
/*  jquery.flatheights.js
/*-------------------------------------------*/
/*  snsCount
/*-------------------------------------------*/

/*-------------------------------------------*/
/*	jquery.flatheights.js
/*-------------------------------------------*/
var a = null;
(function($) {
	$(function() {
		$('.prArea > .subSection-title').flatHeights();
		$('.prArea > .summary').flatHeights();
		// $('.childPage_list_title').flatHeights();
	});
	// window.onload は複数使うと一つしか動作しなくなるので使用しない
	window.addEventListener('DOMContentLoaded', function() {
		$('.childPage_list_text').flatHeights();
		// $('.childPage_list_box').flatHeights();
	})
})(jQuery);

/*-------------------------------------------*/
/*	snsCount
/*-------------------------------------------*/
((window, document, parent_class) => {
	window.addEventListener('load', () => {
		let elements = document.getElementsByClassName('veu_count_sns_hb')
		if (elements.length == 0) {
			return
		}
		let linkurl = encodeURIComponent(location.href);

		fetch(
			vkExOpt.hatena_entry + linkurl,
			{
				method: 'GET',
			}
		).then((r)=>{
			if (r.ok) {
				r.json().then((body)=>{
					if (body.count === undefined) {
						return
					}
					Array.prototype.forEach.call(
						elements,
						(elm) => elm.innerHTML = body.count
					)

				})
			}
		})
		// TODO: add error function
	}, false)

})(window, document, 'veu_socialSet');

(function($) {
	var socials = $('.veu_socialSet');
	if (typeof socials[0] === "undefined") return;
	// var linkurl = encodeURIComponent((typeof vkExOpt !== "undefined" && vkExOpt.sns_linkurl) || location.href);
	var linkurl = encodeURIComponent('https://vektor-inc.co.jp/');
	var facebook = {
		init: function() {
			var url = 'https://graph.facebook.com/?id=' + linkurl;
			$.ajax({
				url: url,
				dataType: 'jsonp',
				success: function(response) {
					if (!response.share || response.share.share_count === undefined) return;
					socials.find('.veu_count_sns_fb').html(response.share.share_count);
				}
			});
		}
	}
	facebook.init();
})(jQuery);
