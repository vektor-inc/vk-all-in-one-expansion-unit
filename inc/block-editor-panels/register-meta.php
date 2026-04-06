<?php
/**
 * Register post meta for REST API access.
 *
 * @package vk-all-in-one-expansion-unit
 */
function veu_register_block_editor_meta() {
	$post_types = get_post_types( array( 'public' => true ) );

	// Checkbox-type meta keys used by child meta boxes.
	$checkbox_metas = array(
		'sns_share_botton_hide',
		'_vk_print_noindex',
		'sitemap_hide',
		'vkExUnit_EyeCatch_disable',
		'veu_display_promotion_alert',
	);

	foreach ( $post_types as $post_type ) {
		foreach ( $checkbox_metas as $meta_key ) {
			register_post_meta(
				$post_type,
				$meta_key,
				array(
					'type'              => 'string',
					'single'            => true,
					'sanitize_callback' => 'sanitize_text_field',
					'show_in_rest'      => true,
					'auth_callback'     => function () {
						return current_user_can( 'edit_posts' ); },
				)
			);
		}

		// Text-type meta keys.
		register_post_meta(
			$post_type,
			'vkExUnit_sns_title',
			array(
				'type'              => 'string',
				'single'            => true,
				'sanitize_callback' => 'sanitize_text_field',
				'show_in_rest'      => true,
				'auth_callback'     => function () {
					return current_user_can( 'edit_posts' ); },
			)
		);

		register_post_meta(
			$post_type,
			'veu_head_title',
			array(
				'type'              => 'string',
				'single'            => true,
				'sanitize_callback' => 'sanitize_text_field',
				'show_in_rest'      => true,
				'auth_callback'     => function () {
					return current_user_can( 'edit_posts' ); },
			)
		);

		// Custom CSS (textarea).
		register_post_meta(
			$post_type,
			'_veu_custom_css',
			array(
				'type'          => 'string',
				'single'        => true,
				'show_in_rest'  => true,
				'auth_callback' => function () {
					return current_user_can( 'edit_posts' ); },
			)
		);
	}

	// Page exclude (page only).
	register_post_meta(
		'page',
		'_exclude_from_list_pages',
		array(
			'type'              => 'string',
			'single'            => true,
			'sanitize_callback' => 'sanitize_text_field',
			'show_in_rest'      => true,
			'auth_callback'     => function () {
				return current_user_can( 'edit_posts' ); },
		)
	);
}
add_action( 'init', 'veu_register_block_editor_meta' );
