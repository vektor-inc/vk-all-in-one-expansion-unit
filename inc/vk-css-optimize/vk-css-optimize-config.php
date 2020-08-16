<?php
/**
 * VK CSS Tree Shaking Config
 *
 * @package Lightning
 */

/**
 * Optimize CSS.
 */
function veu_optimize_css() {
	$options = get_option( 'vkExUnit_pagespeeding' );

	if ( ! isset( $options['css_optimize'] ) ) {
		$options['css_optimize'] = 'tree-shaking';
	}

	if ( ! empty( $options['css_optimize'] ) && ( 'optomize-all-css' === $options['css_optimize'] || 'tree-shaking' === $options['css_optimize'] ) ) {

		// 表示位置の配列.
		global $vk_css_tree_shaking_array;
		global $vkExUnit_version;

		if ( empty( $vk_css_tree_shaking_array ) ) {
			$vk_css_tree_shaking_array = array(
				array(
					'id'      => 'vkExUnit_common_style',
					'url'     => veu_get_directory_uri( '/assets/css/vkExUnit_style.css' ),
					'path'    => veu_get_directory( '/assets/css/vkExUnit_style.css' ),
					'version' => $vkExUnit_version,
				),
			);
		} else {
			$add_array = array(
				'id'      => 'vkExUnit_common_style',
				'url'     => veu_get_directory_uri( '/assets/css/vkExUnit_style.css' ),
				'path'    => veu_get_directory( '/assets/css/vkExUnit_style.css' ),
				'version' => $vkExUnit_version,
			);
			array_push( $vk_css_tree_shaking_array, $add_array );
		}

		$vk_css_tree_shaking_array = apply_filters( 'vk_css_tree_shaking_array', $vk_css_tree_shaking_array );
		if ( ! class_exists( 'VK_CSS_Optimize' ) ) {
			require_once dirname( __FILE__ ) . '/package/class-vk-css-optimize.php';
		}
	}
}
add_action( 'after_setup_theme', 'veu_optimize_css' );
