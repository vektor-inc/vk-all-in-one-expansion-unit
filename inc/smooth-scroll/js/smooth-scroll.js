/*-------------------------------------------*/
/*  ページ内するするスクロール
/*-------------------------------------------*/
((window, document) => {
    if (!vkExOpt.enable_smooth_scroll) {
        return
    }
    window.addEventListener('load', () =>{
        function smooth_link(e) {
            let i=0;
            for(i;i<e.path.length;i++){
                if(e.path[i].getAttribute('href')) break;
            }
            let href = e.path[i].getAttribute('href')
            if (!href) return;

            // role が tab の場合はスムーススクロールしない
            if (['tab'].indexOf(e.path[i].getAttribute('role')) > 0) return;

            // role が button の場合で リンク先指定がない場合はスムーススクロールしない
            if (['button'].indexOf(e.path[i].getAttribute('role')) > 0 ) {
                let href = e.getAttribute('href')
                if( href.indexOf('#') == 0 && href === '#'){
                    return;
                }
            }

            if (e.path[i].getAttribute('data-toggle')) return;
            if (e.path[i].getAttribute('carousel-control')) return;

            let y = 0,
            destination = document.getElementById(href.slice(1))
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
                if(href && href.indexOf('#') == 0 && href !== '#'){
                    elem.addEventListener('click', smooth_link)
                }
            }
        )

    })
})(window, document);
