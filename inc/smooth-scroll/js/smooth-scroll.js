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
            let y = 0
            let destination = document.getElementById(href.slice(1))
            if(destination){
                let scroll = window.pageYOffset || document.documentElement.scrollTop
                y = destination.getBoundingClientRect().top + scroll
            }
            window.scrollTo({
                top: y,
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
