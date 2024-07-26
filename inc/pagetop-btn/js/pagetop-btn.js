
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
		var body_id = exist_id.split(' ');
		if (!body_id.find(item => item === 'top')){
			document.getElementById('page_top').href = '#' + body_id[0];
		}
	}
	else{
		// add #top on body
		let new_id = 'top';

		// check double ID
		let i = 0;
		const allElements = document.querySelectorAll('*');		
		while (Array.from(allElements).some(element => element.id === new_id)) {
			new_id = `top`;
			if( 0 < i ){
				new_id += `-${i}`;
			}
			document.getElementById('page_top').href = '#' + new_id;
			i++;
		}		

		document.body.id = new_id;
	}
})(window, document, 'ready');   