<?php
/*-------------------------------------------*/
/*	footer add pagetop btn
/*-------------------------------------------*/
add_action( 'wp_footer', 'ltg_add_pagetop' );
function ltg_add_pagetop() {
	echo '<a id="page_top" class="page_top_btn">to-top</a>';
}
