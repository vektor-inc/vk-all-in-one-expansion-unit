<?php
/*-------------------------------------------*/
/*	footer add pagetop btn
/*-------------------------------------------*/
add_action( 'wp_footer', 'veu_add_pagetop' );
function veu_add_pagetop() {
	echo '<a href="#top" id="page_top" class="page_top_btn">PAGE TOP</a>';
}
