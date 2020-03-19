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
                    elem.addEventListener('click', smooth_link)
                }
            }
        )

    })
})(window, document);
