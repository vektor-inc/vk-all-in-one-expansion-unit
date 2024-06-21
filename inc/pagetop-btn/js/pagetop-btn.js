
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
	// add #top on body
	var add_id = '';
	var exist_id = document.body.id;
	if(exist_id){
		var ary_id = exist_id.split(' ');
		if (!ary_id.find(item => item === 'top')){
			add_id = exist_id + ' top';
		}
	}
	else{
		add_id = 'top';	
	}

	if(add_id){
		document.body.id = add_id;
	}
})(window, document, 'ready');   