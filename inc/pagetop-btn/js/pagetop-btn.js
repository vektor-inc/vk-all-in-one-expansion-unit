
((window, document, cls) => {
    window.addEventListener('scroll', () => {
        console.log(window.pageYOffset);
        if(window.pageYOffset > 0){
            document.body.classList.add(cls)
        }else{
            document.body.classList.remove(cls)
        }
    })
})(window, document, 'scrolled');
