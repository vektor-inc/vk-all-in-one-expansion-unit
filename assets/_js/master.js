/*-------------------------------------------*/
/*	snsCount
/*-------------------------------------------*/
((window, document, parent_class) => {
	window.addEventListener('load', () => {
		if(!vkExOpt.entry_count) {
			return
		}
		let elements = document.getElementsByClassName('veu_count_sns_hb')
		if (elements.length == 0) {
			return
		}
		let param = (vkExOpt.entry_from_post)? {
				method: 'POST',
				headers: {
					"Content-Type": "application/json; charset=utf-8"
				},
				body: '{"linkurl": "'+ location.href +'"}'
			}: { method: 'GET' }

		// Avoiding Apache config "AllowEncodedSlashes" option issue
		let location_href_replaceslashes = location.href.replace(/\//g, '-#-');

		// hatena
		fetch(
			(vkExOpt.entry_from_post)? vkExOpt.hatena_entry : vkExOpt.hatena_entry + encodeURIComponent(location_href_replaceslashes),
			param
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

		// facebook
		let fb_elements = document.getElementsByClassName('veu_count_sns_fb');

		if(vkExOpt.facebook_count_enable) {
			fetch(
				(vkExOpt.entry_from_post)? vkExOpt.facebook_entry : vkExOpt.facebook_entry + encodeURIComponent(location_href_replaceslashes),
				param
			).then((r)=>{
				if (r.ok) {
					r.json().then((body)=>{
						if (body.count === undefined) {
							return
						}
						Array.prototype.forEach.call(
							fb_elements,
							(elm) => elm.innerHTML = body.count
						)
					})
				}
			})
			.catch((x)=>{})
		}

	}, false)

})(window, document, 'veu_socialSet');

const taxonomyWidgetAll = document.querySelectorAll('.veu_widget_taxonmomy');
if (taxonomyWidgetAll.length) {
	taxonomyWidgetAll.forEach((taxonomyWidget) => {
		taxonomyWidget.addEventListener('change', () => {

			taxName = taxonomyWidget.name;
			taxValue = taxonomyWidget.value;
			if (taxValue !== '') {
				document.location.href = vkExOpt.homeUrl + '/?' + taxName + '=' + taxValue;
			}
			
		});
	});		
};