
((window, document, cls) => {
    window.addEventListener('scroll', () => {
        if(window.pageYOffset > 0){
            document.body.classList.add(cls)
        }else{
            document.body.classList.remove(cls)
        }
    })
})(window, document, 'scrolled');

((window, document, cls) => {
	// check footer pagetop btn
	var exist_id = document.body.id;
	if(exist_id){
		//  use existing body ID
		if (exist_id !== 'top'){
			document.getElementById('page_top').href = '#' + exist_id;
		}
	} else{
		// add #top on body
		let newBodyId = 'top';

		// check double ID
		let i = 0;
		const allElements = document.querySelectorAll('*');		
		// 既存のHTML内に newBodyId の値と同じ id がある場合
		while (Array.from(allElements).some(element => element.id === newBodyId)) {
			newBodyId = `top`;
			if( 0 < i ){
				newBodyId += `-${i}`;
			}
			// page_top のリンク先を変更
			document.getElementById('page_top').href = '#' + newBodyId;
			i++;
		}

		document.body.id = newBodyId;
	}
})(window, document, 'ready');   