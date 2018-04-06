<?php
/*-------------------------------------------*/
/*	footer add pagetop btn
/*-------------------------------------------*/
add_action( 'wp_footer', 'ltg_add_pagetop' );
function ltg_add_pagetop() {
	echo '<button id="page_top" class="page_top_btn"><i class="fa fa-angle-up arrow" aria-hidden="true"></i></button>';
}
