<?php

if ( is_admin() ) {
	global $wp_version;

	if ( version_compare( $wp_version, '5.3', '>=' ) ) {
		add_action(
			'admin_menu',
			function () {
				global $menu;
				$position   = 20;
				$menu_slug  = 'edit.php?post_type=wp_block';
				$menu_title = __( 'Patterns', 'vk-all-in-one-expansion-unit' );

				while ( isset( $menu[ $position ] ) ) {
					$position++;
				}

				$menu[ $position ] = array(
					$menu_title,
					'edit_posts',
					$menu_slug,
					'',
					'menu-top ',
					'',
					'dashicons-tagcloud',
				);
			},
			10
		);
	}
}
