<?php

function vkblocks_add_animation_front_scripts( $block_content, $block ) {

	if ( $block['blockName'] === 'vk-blocks/animation'  ) {

		$attributes = $block['attrs'];
		$clientId = $block['attrs']["clientId"];

		$script ="<script>
		window.addEventListener('load', (event) => {
			let animationElm = document.querySelector('.vk_animation-" . esc_attr($clientId) . "');
			const observer = new IntersectionObserver((entries) => {
				if(entries[0].isIntersecting){
					animationElm.classList.add('vk_animation-active');
				}else{
					animationElm.classList.remove('vk_animation-active');
				}
			});
			observer.observe(animationElm);
		  }, false);
		</script>";

	return $block_content .  $script;
}
return $block_content;
}

add_filter( 'render_block', 'vkblocks_add_animation_front_scripts', 10, 2 );
