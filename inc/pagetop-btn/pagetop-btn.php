<?php
/*-------------------------------------------*/
/*	footer add pagetop btn
/*-------------------------------------------*/
add_action( 'wp_footer', 'veu_add_pagetop' );
function veu_add_pagetop() {
	echo '<a href="#top" id="page_top" class="page_top_btn">PAGE TOP</a>';
	?>
<script type="text/javascript">;(function(d,t){
d.addEventListener('scroll',function(){
	if(window.pageYOffset>0){d.body.classList.add(t)}else{d.body.classList.remove(t)}
},false);
})(document,'scrolled');</script>
	<?php
}