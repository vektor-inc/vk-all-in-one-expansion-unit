<?php
/**
 * Display IE Alert
 *
 * @package VK All in One Expansion Unit
 */

/**
 * Judgment IS IE
 */
function veu_is_ie() {
	$ua = isset( $_SERVER['HTTP_USER_AGENT'] ) ? mb_strtolower( $_SERVER['HTTP_USER_AGENT'] ) : '';  // すべて小文字にしてユーザーエージェントを取得.
	if ( strpos( $ua, 'msie' ) !== false || strpos( $ua, 'trident' ) !== false ) {
		return true;
	}
	return false;
}

/**
 * IE Alart HTML
 */
function veu_get_alert_html() {
	$title    = esc_html( __( 'The browser that you use is not safe.', 'vk-all-in-one-expansion-unit' ) );
	$message  = '';
	$message .= __( 'Internet Explorer, you are using, is old browser, Microsoft also reports that use is dangerous.', 'vk-all-in-one-expansion-unit' );
	$message .= __( 'This website is also not guaranteed to display on Internet Explorer.', 'vk-all-in-one-expansion-unit' );

	$html  = '<div class="ie_alert">';
	$html .= '<h1 class="ie_alert__title">';
	$html .= apply_filters( 'vk_ie_alert_title', $title );
	$html .= '</h1>';
	$html .= '<div class="ie_alert__body">';
	$html .= '<p>';
	$html .= apply_filters( 'vk_ie_alert_message', $message );
	$html .= '</p>';
	$html .= '<p>';
	$html .= __( 'Plese use The latest modern browser ( <a href="https://www.microsoft.com/ja-jp/edge" target="_blank" rel="noopener">Microsoft Edge</a>, <a href="https://www.google.co.jp/chrome/index.html" target="_blank" rel="noopener">Google Chrome</a> and so on ).', 'vk-all-in-one-expansion-unit' );
	$html .= '</p>';
	$html .= '<p>';
	$html .= __( 'If you are using Edge and you still get this message, make sure you are not in IE mode.', 'vk-all-in-one-expansion-unit' );
	$html .= '</p>';
	$html .= '</div>';
	$html .= '</div>';
	return apply_filters( 'vk_ie_alert_html', $html );
}

/**
 * Insert IE Alert.
 */
function veu_insert_alert() {
	if ( veu_is_ie() ) {
		?>
		<style type="text/css">
		.ie_alert {
			background-color:#c00;
			color:#fff;
			padding:10px;
			position: relative;
			z-index: 9999;
		}
		.ie_alert a {
			color:#fff;
			text-decoration:underline;
		}
		.ie_alert__title {
			font-size:16px;
			text-align:center;
		}
		.ie_alert__body p {
			margin:0 0 5px;
			padding:0;
			font-size:12px;
			text-align:center;
		}
		</style>
		<?php
		echo wp_kses_post( veu_get_alert_html() );
	}
}
add_action( 'wp_body_open', 'veu_insert_alert' );
