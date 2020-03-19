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
		.catch((x)=>{})
	}, false)

})(window, document, 'veu_socialSet');

/*-------------------------------------------*/
/*  ページ内するするスクロール
/*-------------------------------------------*/
((window, document) => {
	if (!vkExOpt.enable_smooth_scroll) {
		return
	}
	window.addEventListener('load', () =>{
		function smooth_link(e) {
			let href = e.toElement.getAttribute('href')
			let y, destination = document.getElementById(href.slice(1));
			y = destination == null? 0: destination.getBoundingClientRect().top;
			window.scrollTo({
				top: y - window.pageYOffset,
				behavior: 'smooth'
			})
			e.preventDefault()
		}
		Array.prototype.forEach.call(
			document.getElementsByTagName('a'),
			(elem) => {
				let href = elem.getAttribute('href')
				if(href && href.indexOf('#') == 0){
					console.log(href);
					elem.addEventListener('click', smooth_link)
				}
			}
		)

	})
})(window, document);
