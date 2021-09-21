<?php
/**
 * Smooth scroll controll
 *
 * @package vk-all-in-one-expanaion-unit
 */

/**
 * Smooth scroll js の読み込み
 */
function veu_load_smooth_scroll_polyfill() {
	wp_enqueue_script(
		'smooth-scroll-js',
		plugin_dir_url( __FILE__ ) . 'js/smooth-scroll.min.js',
		array(),
		VEU_VERSION,
		true
	);
}

/**
 *  Smooth scroll css の読み込み
 *
 * @return void
 */
function veu_add_smooth_css() {
	$css = 'html{ scroll-behavior: smooth; }';
	wp_add_inline_style( 'vkExUnit_common_style', $css );
}

$options_smooth = veu_get_smooth_options();
if ( 'css' === $options_smooth['mode'] ) {
	add_action( 'wp_enqueue_scripts', 'veu_add_smooth_css' );
} else {
	add_action( 'wp_enqueue_scripts', 'veu_load_smooth_scroll_polyfill' );
}

/**
 * ExUnitの機能管理画面に登録
 *
 * @return void
 */
function veu_smooth_admin_register() {
	$tab_label         = __( 'Smooth scroll', 'vk-all-in-one-expansion-unit' );
	$option_name       = 'vkExUnit_smooth';
	$sanitize_callback = 'veu_smooth_sanitize';
	$render_page       = 'veu_smooth_admin';
	vkExUnit_register_setting( $tab_label, $option_name, $sanitize_callback, $render_page );
}
add_action( 'veu_package_init', 'veu_smooth_admin_register' );

/**
 * ExUnit Main Setting view
 *
 * @return void
 */
function veu_smooth_admin() {
	$options = veu_get_smooth_options();
	?>
<div id="seoSetting" class="sectionBox">
<h3><?php esc_html_e( 'Smooth scroll', 'vk-all-in-one-expansion-unit' ); ?></h3>
<table class="form-table">
<!-- Google Analytics -->
<tr>
<th><?php esc_html_e( 'Smooth scroll Type', 'vk-all-in-one-expansion-unit' ); ?> </th>
<td>
<ul class="no-style">
<li>
<label><input type="radio" name="vkExUnit_smooth[mode]" value="js" <?php checked( $options['mode'], 'js', true ); ?> /> <?php esc_html_e( 'JavaScript', 'vk-all-in-one-expansion-unit' ); ?> </label></li>
<li>
<label><input type="radio" name="vkExUnit_smooth[mode]" value="css" <?php checked( $options['mode'], 'css', true ); ?> /> <?php esc_html_e( 'CSS only ( Loading slightly light but do not work on Safari and so on. )', 'vk-all-in-one-expansion-unit' ); ?> </label></li>
</ul>
</td>
</tr>
</table>
	<?php submit_button(); ?>
</div>
	<?php
}

/**
 * Get smooth options
 *
 * @return array $options
 */
function veu_get_smooth_options() {
	$options = get_option( 'vkExUnit_smooth', array() );
	$options = wp_parse_args( $options, veu_get_smooth_options_default() );
	return $options;
}

/**
 * Get smooth options
 *
 * @return array $default_options
 */
function veu_get_smooth_options_default() {
	$default_options = array(
		'mode' => 'js',
	);
	return apply_filters( 'veu_get_smooth_options_default', $default_options );
}

/**
 * Sanitize
 *
 * @param array $input : input value.
 * @return array $options
 */
function veu_smooth_sanitize( $input ) {
	$output = array();
	if ( isset( $input['mode'] ) ) {
		$output['mode'] = esc_attr( $input['mode'] );
	}
	return $output;
}
