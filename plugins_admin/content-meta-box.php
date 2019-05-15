<?php
/*
  add page custom field
/*-------------------------------------------*/

require_once( dirname( __FILE__ ) . '/class-veu-metabox.php' );

/**
 * Add Content meta box use for "Child Page List" , "Sitemap" , "Contact section" and more fields
 */
function veu_add_content_meta_box() {
	if ( apply_filters( 'veu_content_meta_box_activation', false ) ) {
		$meta_box_name = veu_get_name();

		$args       = array(
			'public' => true,
		);
		$post_types = get_post_types( $args );

		foreach ( $post_types as $key => $post_type ) {
			add_meta_box( 'veu_content_meta_box', $meta_box_name, 'veu_post_metabox_body', $post_type, 'normal', 'high' );
		}
	}
}
add_action( 'admin_menu', 'veu_add_content_meta_box' );

/**
 * Insert ExUnit Settings.
 */
function veu_post_metabox_body() {
	do_action( 'veu_post_metabox_body' );
}


function veu_metabox_section( $args ) {

	// Outer class
	$outer_class = '';
	if ( ! empty( $args['slug'] ) ) {
		$outer_class = ' ' . $args['slug'];
	}
	echo '<div class="veu_metabox_section' . $outer_class . '">';

	// Section title
	if ( ! empty( $args['title'] ) ) {
		echo '<h3 class="veu_metabox_section_title">' . $args['title'] . '</h3>';
	}

	// Section body
	if ( ! empty( $args['body'] ) ) {
		echo '<div class="veu_metabox_section_body">';
		echo $args['body'];
		echo '</div><!-- [ /.veu_metabox_section_body ] -->';
	}
	echo '</div><!-- [ /.veu_metabox_section ] -->';
}
