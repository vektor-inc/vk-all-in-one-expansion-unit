<?php
/*
  footer add pagetop btn
/*-------------------------------------------*/
add_action( 'wp_footer', 'veu_add_pagetop' );
function veu_add_pagetop() {
  $option = get_option( 'vkExUnit_pagetop' );
  if ( 
	  ! wp_is_mobile() ||  
	  ( wp_is_mobile() && empty( $option['hide_mobile'] ) ) 
	  ) {
	echo '<a href="#top" id="page_top" class="page_top_btn">PAGE TOP</a>';
  }
}

add_action( 'customize_register', 'veu_customize_register_pagetop' );
function veu_customize_register_pagetop( $wp_customize ) {

	/*
		Page Top setting
	/*-------------------------------------------*/
	$wp_customize->add_section(
		'veu_pagetop_setting',
		array(
			'title'    => __( 'Page Top Button', 'vk-all-in-one-expansion-unit' ),
			'priority' => 10000,
			'panel'    => 'veu_setting',
		)
	);

	$wp_customize->add_setting(
		'vkExUnit_pagetop[hide_mobile]',
		array(
			'default'           => false,
			'type'              => 'option',
			'capability'        => 'edit_theme_options',
			'sanitize_callback' => 'veu_sanitize_boolean',
		)
	);
	$wp_customize->add_control(
		'vkExUnit_pagetop[hide_mobile]',
		array(
    		'label'       => __( 'Do not display on touch screen devices', 'vk-all-in-one-expansion-unit' ),
			'section'     => 'veu_pagetop_setting',
			'settings'    => 'vkExUnit_pagetop[hide_mobile]',
			'type'        => 'checkbox',
		)
  );
  $wp_customize->selective_refresh->add_partial(
		'vkExUnit_pagetop[hide_mobile]', array(
			'selector'        => '.page_top_btn',
			'render_callback' => '',
			'supports' => [],
		)
	);

}


/**
 * ExUnitの機能管理パッケージに登録
 * @return [type] [description]
 */
function veu_pagetop_admin_register() {
	$tab_label         = __( 'Page Top Button', 'vk-all-in-one-expansion-unit' );
	$option_name       = 'vkExUnit_pagetop';
	$sanitize_callback = 'veu_pagetop_sanitize';
	$render_page       = 'veu_pagetop_admin';
	vkExUnit_register_setting( $tab_label, $option_name, $sanitize_callback, $render_page );
}
add_action( 'veu_package_init', 'veu_pagetop_admin_register' );

function veu_pagetop_admin() {
	$options = veu_pagetop_options();
?>
<div id="seoSetting" class="sectionBox">
<h3><?php _e( 'Page Top Button', 'vk-all-in-one-expansion-unit' ); ?></h3>
<table class="form-table">
<!-- Google Analytics -->
<tr>
<th><?php _e( 'Page Top Button', 'vk-all-in-one-expansion-unit' ); ?> </th>
<td><label>
<input type="checkbox" name="vkExUnit_pagetop[hide_mobile]" value="true"<?php if( ! empty( $options['hide_mobile'] ) ) echo ' checked'; ?> /> <?php _e( 'Do not display on touch screen devices', 'vk-all-in-one-expansion-unit' ); ?> </label>
</td>
</tr>
</table>
<?php submit_button(); ?>
</div>
<?php
}


function veu_pagetop_options() {
	$options = get_option( 'vkExUnit_pagetop', array() );
	$options = wp_parse_args( $options, veu_pagetop_default() );
	return $options;
}

function veu_pagetop_default() {
	$default_options = array(
		'hide_mobile' => false,
	);
	return apply_filters( 'veu_pagetop_default', $default_options );
}

function veu_pagetop_sanitize( $input ) {
	$output                = array();
	if ( isset($input['hide_mobile']) ){
		$output['hide_mobile'] = esc_attr( $input['hide_mobile'] );
	}
	return $output;
}