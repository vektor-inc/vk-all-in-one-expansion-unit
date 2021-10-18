/*-------------------------------------------*/
/*  ページ内するするスクロール
/*-------------------------------------------*/
((window, document) => {
    window.addEventListener('load', () =>{
        function smooth_link(e) {
			let path = e.path || (e.composedPath && e.composedPath());
            let i=0;
            for(i;i<path.length;i++){
                if(path[i].getAttribute('href')) break;
            }
            let href = path[i].getAttribute('href')
            if (!href) return;

            // role が tab の場合はスムーススクロールしない
            if (['tab'].indexOf(path[i].getAttribute('role')) > 0) return;
            // Lightning標準スライダーのスライド送り
            if (path[i].getAttribute('href') === '#top__fullcarousel') return;
        
            // role が button の場合で リンク先指定がない場合はスムーススクロールしない
            if (['button'].indexOf(path[i].getAttribute('role')) > 0 ) {
                let href = e.getAttribute('href')
                if( href.indexOf('#') == 0 && href === '#'){
                    return;
                }
            }

            if (path[i].getAttribute('data-toggle')) return;
            if (path[i].getAttribute('carousel-control')) return;

            let y = 0,
            destination = document.getElementById(href.slice(1))
            if(destination){
                let scroll = window.pageYOffset || document.documentElement.scrollTop
                y = destination.getBoundingClientRect().top + scroll
            }

            // G3 の場合用の補正
            // * header_scrolled の方が適切だが、クリック時に header_scrolled が消えて判定に使えないため
            if ( document.body.classList.contains('scrolled') == true ) {
                // ヘッダーを取得
                let siteHeader = document.getElementById('site-header');
                if (siteHeader){
                    // ヘッダーの高さを取得
                    let headerHeight = siteHeader.clientHeight;
                    if (headerHeight){
                        y = y - headerHeight;
                    }
                }
            }

            // Adminbar adjustment
            let adminbar = document.getElementById('wpadminbar');
            let adminbarHeight = 0;
            if (adminbar){
                adminbarHeight = adminbar.clientHeight;
            }
            
            window.scrollTo({
                top: y - adminbarHeight,
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
