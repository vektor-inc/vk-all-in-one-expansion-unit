<?php
/**
 * VkExUnit contactform7-asset-optimize.php
 *
 * @package  vektor-inc/vk-all-in-one-expansion-unit
 * @since    19/March/2020
 */

 /**
  * Contact Form 7 の reCAPTCHA v3 は全ページで出力されてしまうので、一旦削除
  *
  * @since 9.93.0.0
  */
function veu_deregister_recaptcha_js() {
	wp_deregister_script( 'google-recaptcha' );
}
add_action( 'wp_enqueue_scripts', 'veu_deregister_recaptcha_js', 100 );


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
		return $output;
	},
	10,
	4
);
