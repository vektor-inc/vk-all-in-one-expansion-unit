<?php
/**
 * VkExUnit contactform7-asset-optimize.php
 *
 * @package  VkExUnit
 * @author   shoji imamura<imamura@vektor-inc.co.jp>
 * @since    19/March/2020
 */

add_action( 'wp_print_styles', function() {
    wp_dequeue_style( 'contact-form-7' );
}, 0 );

add_action( 'wp_print_scripts', function() {
    wp_dequeue_script( 'contact-form-7' );
}, 0 );

function veu_reregister_contact_form_assets() {
    wp_enqueue_style( 'contact-form-7' );
    wp_enqueue_script( 'contact-form-7' );
}

add_filter( 'do_shortcode_tag', function( $output, $tag, $attr, $m ){
    if ( $tag == 'contact-form-7' ) {
        add_action( 'wp_footer', 'veu_reregister_contact_form_assets' );
    }
    return $output;
}, 10, 4 );
