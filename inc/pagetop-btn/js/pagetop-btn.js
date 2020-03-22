
((window, document, cls) => {
    window.addEventListener('scroll', () => {
        if(window.pageYOffset > 0){
            document.body.classList.add(cls)
        }else{
            document.body.classList.remove(cls)
        }
    })
})(window, document, 'scrolled');
