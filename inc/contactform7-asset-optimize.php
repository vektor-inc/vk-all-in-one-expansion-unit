<?php
/**
 * VkExUnit contactform7-asset-optimize.php
 *
 * @package  vektor-inc/vk-all-in-one-expansion-unit
 * @since    19/March/2020
 */


add_action(
	'wp_print_styles',
	function() {
		wp_dequeue_style( 'contact-form-7' );
	},
	0
);

add_action(
	'wp_print_scripts',
	function() {
		wp_dequeue_script( 'contact-form-7' );
	},
	0
);

function veu_reregister_contact_form_assets() {
	wp_enqueue_style( 'contact-form-7' );
	wp_enqueue_script( 'contact-form-7' );
}

add_filter(
	'do_shortcode_tag',
	function( $output, $tag, $attr, $m ) {
		if ( $tag == 'contact-form-7' ) {
			add_action( 'wp_footer', 'veu_reregister_contact_form_assets' );
			add_action( 'wp_footer', 'veu_add_recapcha_cf7' );
		}
		return $output;
	},
	10,
	4
);

/**
 * Contact Form 7 の reCAPTCHA v3 は全ページで出力されてしまうので、一旦削除
*
* @since 9.93.0.0
*/
function veu_deregister_recaptcha_js() {
	wp_deregister_script( 'google-recaptcha' );
}
add_action( 'wp_enqueue_scripts', 'veu_deregister_recaptcha_js', 21 );

/**
 * Contact Form 7 の reCAPTCHA v3 を出力
 *
 * @since 9.93.0.0
 */
function veu_add_recapcha_cf7() {
	if ( method_exists( 'WPCF7_RECAPTCHA', 'get_instance' ) ) {
		// reCAPTCHA のコードを再登録（ Contact Form 7 にかかれている reCAPTCHA v3 の登録スクリプトの複製 )
		$service = WPCF7_RECAPTCHA::get_instance();

		if ( ! $service->is_active() ) {
			return;
		}

		$url = 'https://www.google.com/recaptcha/api.js';

		if ( apply_filters( 'wpcf7_use_recaptcha_net', false ) ) {
			$url = 'https://www.recaptcha.net/recaptcha/api.js';
		}

		wp_register_script(
			'google-recaptcha',
			add_query_arg(
				array(
					'render' => $service->get_sitekey(),
				),
				$url
			),
			array(),
			'3.0',
			true
		);
	}
}

/**
 * reCAPTCHA の位置調整CSSを出力するかどうか
 *
 * @since 9.93.0.0
 * */
function veu_is_print_recapcha_position_adjustment_style() {
	$options = veu_get_common_options();
	if ( ! empty( $options['active_pagetop_button'] ) ) {
		$option = get_option( 'vkExUnit_pagetop' );
		if ( ! wp_is_mobile() ||
			( wp_is_mobile() && empty( $option['hide_mobile'] ) )
			) {
			return true;
		}
	}
	return false;
}

/**
 * reCAPTCHA の位置調整CSSを出力
 *
 * @since 9.93.0.0
 */
function veu_print_recapcha_position_adjustment_style() {
	if ( veu_is_print_recapcha_position_adjustment_style() ) {
		/* When the developer tools are open, the badge is displayed about 5mm higher. Please check when the developer tools are closed. */
		wp_add_inline_style( 'vkExUnit_common_style', '.grecaptcha-badge{bottom: 85px !important;}' );
	}
}
add_action( 'wp_enqueue_scripts', 'veu_print_recapcha_position_adjustment_style' );
